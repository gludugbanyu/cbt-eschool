<?php
// Ambil file yang pertama kali dipanggil browser
$entryFile = realpath($_SERVER['SCRIPT_FILENAME']);

// Ambil file yang sedang dijalankan sekarang
$currentFile = realpath(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1)[0]['file']);

// Kalau file ini diakses langsung → redirect ke login
if ($entryFile === $currentFile) {

    // ganti sesuai login kamu
    header("Location: ../error.php");
    exit;
}