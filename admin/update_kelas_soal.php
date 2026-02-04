<?php
session_start();
include '../koneksi/koneksi.php';

header('Content-Type: application/json');

$id    = $_POST['id_soal'] ?? '';
$kelas = $_POST['kelas'] ?? '';

if(empty($id) || empty($kelas)){
    echo json_encode(['status'=>'error','msg'=>'Data kosong']);
    exit;
}

$stmt = $koneksi->prepare("UPDATE soal SET kelas=? WHERE id_soal=?");
$stmt->bind_param("si", $kelas, $id);

if($stmt->execute()){
    echo json_encode(['status'=>'ok']);
}else{
    echo json_encode(['status'=>'error','msg'=>$stmt->error]);
}