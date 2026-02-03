<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();

$key = 'cbteschool@#12345';

function encrypt_data($data, $key) {
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function backup_database($koneksi) {
    $backup = "";
    $tables = [];
    $res = mysqli_query($koneksi, "SHOW TABLES");
    while ($row = mysqli_fetch_row($res)) {
        $tables[] = $row[0];
    }

    foreach ($tables as $table) {
        $res1 = mysqli_query($koneksi, "SHOW CREATE TABLE `$table`");
        $row1 = mysqli_fetch_row($res1);
        $backup .= "DROP TABLE IF EXISTS `$table`;\n";
        $backup .= $row1[1] . ";\n\n";

        $res2 = mysqli_query($koneksi, "SELECT * FROM `$table`");
        while ($row2 = mysqli_fetch_assoc($res2)) {
            $cols = array_map(fn($col) => "`$col`", array_keys($row2));
            $vals = array_map(fn($val) => "'" . mysqli_real_escape_string($koneksi, $val) . "'", array_values($row2));
            $backup .= "INSERT INTO `$table` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ");\n";
        }
        $backup .= "\n\n";
    }

    return $backup;
}

// Generate backup & encrypt
$sql = backup_database($koneksi);
$encrypted = encrypt_data($sql, $key);

$fileName = 'backup-' . date('Ymd_His') . '.dbk';

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($encrypted));

echo $encrypted;
exit;
