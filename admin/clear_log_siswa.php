<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';

check_login('admin');
only_admin();

// Batas offline (menit)
$minutes = 5;

// Query aman
$sql = "
    UPDATE siswa 
    SET 
        last_activity = NULL,
        page_url = NULL,
        session_token = NULL
    WHERE 
        session_token IS NOT NULL
        AND last_activity IS NOT NULL
        AND last_activity < (NOW() - INTERVAL ? MINUTE)
";

// Prepare statement
$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    http_response_code(500);
    exit("Query preparation failed.");
}

mysqli_stmt_bind_param($stmt, "i", $minutes);
mysqli_stmt_execute($stmt);

$affected = mysqli_stmt_affected_rows($stmt);

mysqli_stmt_close($stmt);

echo "$affected siswa offline berhasil dibersihkan.";
?>