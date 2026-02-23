<?php
session_start();
include '../inc/functions.php';

// 🔐 Cek login admin khusus API
check_login_api('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    // 👉 Dibuka langsung via browser
    if (!is_ajax_request()) {
        header("Location: ../admin/dashboard.php?notallowed=1");
        exit;
    }

    // 👉 Dipanggil AJAX (Summernote dll)
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
// 🔐 Wajib POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error'=>'Method not allowed']));
}

// 🔐 Cek CSRF Token
if (!isset($_POST['token']) || $_POST['token'] !== ($_SESSION['upload_token'] ?? '')) {
    http_response_code(403);
    exit(json_encode(['error'=>'Invalid CSRF Token']));
}
if ($_FILES['file']['name']) {

    $fileTmpName = $_FILES['file']['tmp_name'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $fileTmpName);
    finfo_close($finfo);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif'
    ];

    if (!isset($allowed[$mime])) {
        exit(json_encode(['error'=>'Invalid image']));
    }

    switch ($mime) {
        case 'image/jpeg':
            $img = imagecreatefromjpeg($fileTmpName);
            break;
        case 'image/png':
            $img = imagecreatefrompng($fileTmpName);
            break;
        case 'image/gif':
            $img = imagecreatefromgif($fileTmpName);
            break;
        default:
            exit(json_encode(['error'=>'Invalid image data']));
    }

    if (!$img) {
        exit(json_encode(['error'=>'Invalid image']));
    }

    $uploadDir = '../gambar/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newFileName = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $filePath = $uploadDir . $newFileName;

    // 🚨 REWRITE IMAGE (hapus webshell polyglot)
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($img, $filePath, 90);
            break;
        case 'image/png':
            imagepng($img, $filePath, 6);
            break;
        case 'image/gif':
            imagegif($img, $filePath);
            break;
    }

    imagedestroy($img);

    $relativePath = '../gambar/' . $newFileName;
    echo json_encode(['url'=>$relativePath]);
} else {
    echo json_encode(['error' => 'No file uploaded']);
}
?>