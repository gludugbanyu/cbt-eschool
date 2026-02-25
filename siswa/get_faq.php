<?php
session_start();
include '../inc/functions.php';

check_login_api('siswa'); // 🔥 ini yang handle redirect

$query = mysqli_query($koneksi, "SELECT question, answer FROM faq");
$faq = [];

while ($row = mysqli_fetch_assoc($query)) {
    $faq[$row['question']] = $row['answer'];
}

header('Content-Type: application/json');
echo json_encode($faq);