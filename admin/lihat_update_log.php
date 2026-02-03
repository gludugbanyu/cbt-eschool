<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();

$logFile = __DIR__ . '/update_log.txt';
if (file_exists($logFile)) {
    echo htmlspecialchars(file_get_contents($logFile));
} else {
    echo "Belum ada log update.";
}
