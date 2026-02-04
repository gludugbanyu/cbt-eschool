<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Akses Tidak Valid</title>
  <link rel="icon" type="image/png" href="assets/images/icon.png" />
  <link href="../assets/bootstrap-5.3.6/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/fontawesome/css/all.min.css" rel="stylesheet">

  <style>
    body{
        min-height:100vh;
        background: url('../assets/images/bglogin.webp') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .overlay{
        position:fixed;
        inset:0;
        background: rgba(0,0,0,.65);
        backdrop-filter: blur(5px);
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .error-card{
        background:#fff;
        border-radius:22px;
        padding:45px 35px;
        max-width:560px;
        width:100%;
        text-align:center;
        box-shadow:0 25px 70px rgba(0,0,0,.35);
        animation:fadeIn .4s ease;
    }

    @keyframes fadeIn{
        from{opacity:0; transform:translateY(10px)}
        to{opacity:1; transform:translateY(0)}
    }

    .logo img{
        width:170px;
        margin-bottom:25px;
    }

    .icon{
        font-size:52px;
        color:#dc3545;
        margin-bottom:15px;
    }

    .title{
        font-size:24px;
        font-weight:700;
        margin-bottom:10px;
    }

    .desc{
        color:#6c757d;
        font-size:15px;
        line-height:1.7;
        margin-bottom:30px;
    }

    .btn-back{
        border-radius:30px;
        padding:8px 26px;
        font-weight:600;
    }

    .footer-note{
        margin-top:25px;
        font-size:13px;
        color:#adb5bd;
    }
  </style>
</head>
<body>

<div class="overlay">
    <div class="error-card">

        <div class="logo">
            <img src="assets/images/codelite.png" alt="Codelite Logo">
        </div>

        <div class="icon">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <div class="title">
            Akses Diblokir Sistem
        </div>

        <div class="desc">
            Sistem keamanan mendeteksi adanya perubahan pada halaman login.<br>
            Untuk menjaga integritas aplikasi CBT, proses dihentikan secara otomatis.
        </div>

        <a href="javascript:history.back()" class="btn btn-primary btn-back">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>

        <div class="footer-note">
            CBT E-School Protection System
        </div>

    </div>
</div>

<script src="../assets/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
</body>
</html>
