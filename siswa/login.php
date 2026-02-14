<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';

$error = '';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$page_signature = hash('sha256', $_SESSION['csrf_token'] . 'CBT-ESCOOL');
// Redirect jika sudah login
if (isset($_SESSION['siswa_logged_in']) && $_SESSION['siswa_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

// Tampilkan pesan jika login ganda (token tidak cocok)
if (isset($_GET['status']) && $_GET['status'] === 'multi') {
    $error = 'Login dibatalkan: akun sedang aktif di perangkat lain.';
}

// Proses form login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $expected_sign = hash('sha256', $_SESSION['csrf_token'] . 'CBT-ESCOOL');

    if (!isset($_POST['page_sign']) || $_POST['page_sign'] !== $expected_sign) {
        header("Location: ../error_page.php");
        exit;
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $captcha_input = $_POST['captcha'] ?? '';

    if (!isset($_SESSION['captcha']) || strtolower($captcha_input) !== strtolower($_SESSION['captcha'])) {
        $error = 'Captcha salah!';
    } else {
        if (authenticate_user($username, $password, 'siswa')) {
        unset($_SESSION['captcha']); // hapus captcha
        header("Location: dashboard.php");
        exit;
        } else {
            // Cek apakah login gagal karena sesi sudah aktif
            $settings = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT login_ganda FROM pengaturan WHERE id = 1"));
            $allow_multiple = ($settings['login_ganda'] == 'izinkan');

            if (!$allow_multiple) {
                $query = "SELECT session_token FROM siswa WHERE username = ?";
                $stmt = mysqli_prepare($koneksi, $query);
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);

                if ($user && !empty($user['session_token'])) {
                    $error = 'Akun sedang aktif di perangkat lain.';
                } else {
                    $error = 'Username atau password salah!';
                }
            } else {
                $error = 'Username atau password salah!';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Siswa Login</title>
    <?php include '../inc/css.php'; ?>
    <style>
    body {
        background: url('../assets/images/bglogin.webp') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(245, 245, 245, 0.47);
        /* lebih terang, jelas */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .glass-card {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        padding: 2.5rem;
        max-width: 100%;
        margin: auto;
        color: #333;
        transition: 0.3s ease;
    }

    .glass-card:hover {
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
    }

    label {
        color: #444;
        font-weight: 600;
        font-size: 14px;
    }

    .glass-card input {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 20px;
        padding: 10px;
        width: 100%;
        transition: border-color 0.3s ease;
        color: #333;
    }

    .glass-card input:focus {
        border-color: #0d6efd;
        outline: none;
        background-color: #fff;
    }

    .glass-card input::placeholder {
        color: #888;
    }

    button.btn {
        background-color: #0d6efd;
        border: none;
        color: #fff;
        padding: 10px 15px;
        border-radius: 20px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    button.btn:hover {
        background-color: #0b5ed7;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    @media (max-width: 576px) {
        .glass-card {
            padding: 1.5rem;
        }

        .glass-card input {
            font-size: 14px;
        }
    }

    .card-bottom-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        font-size: 13px;
    }

    #enc {
        opacity: .8;
    }

    .card-meta-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 18px;
        padding-top: 10px;
        font-size: 12px;
        color: #686868;
    }

    #enc {
        opacity: .7;
    }

    .admin-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #686868;
        text-decoration: none;
        font-weight: 500;
        letter-spacing: .3px;
        transition: .2s ease;
    }

    .admin-meta i {
        font-size: 13px;
    }

    .admin-meta:hover {
        color: #0d6efd;
        text-decoration: none;
    }
    </style>

</head>

<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="overlay d-flex align-items-center justify-content-center" style="height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="position-relative">
                        <div style="
                        position: absolute;
                        top: -12px;
                        left: -12px;
                        background-color:rgb(253, 129, 13);
                        color: white;
                        padding: 6px 12px;
                        font-weight: bold;
                        border-radius: 5px 0 5px 0;
                        font-size: 13px;
                        z-index: 10;
                        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                    ">
                            Login Siswa
                        </div>
                        <div class="card shadow p-4 glass-card">
                            <div class="head"
                                style="min-height:150px;display: flex;justify-content: center;align-items: center;">
                                <?php
                                        $q = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id = 1");
                                        $data = mysqli_fetch_assoc($q);
                                        ?>
                                <img src="../assets/images/<?php echo $data['logo_sekolah']; ?>" width="250"
                                    height="auto">
                            </div>
                            <?php if (!empty($error)): ?>
                            <div id="customAlert" class="text-danger text-center my-3" role="alert"
                                style="font-weight: bold;">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                            <?php endif; ?>
                            <form action="" method="POST" class="mt-3" id="loginForm">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="page_sign" id="page_sign">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Username" required autocomplete="off">
                                </div>
                                <div class="mb-3 position-relative">
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Password" required autocomplete="off">
                                    <span class="position-absolute top-50 end-0 translate-middle-y me-2"
                                        style="cursor:pointer;" onclick="togglePassword()">
                                        <i style="color:grey;" class="fa fa-eye" id="togglePasswordIcon"></i>
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="../inc/captcha.php?rand=<?= rand() ?>" alt="CAPTCHA Image"
                                            style="border-radius:20px; height: 40px;">
                                        <input type="text" class="form-control" id="captcha" name="captcha"
                                            placeholder="Ketik kode Captcha" required autocomplete="off">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="loginButton">Login <i
                                        class="fa fa-sign-in"></i></button>
                            </form>
                            <br>
                            <div class="card-meta-footer">
                                <div id="enc" data-sign="<?= $page_signature ?>"></div>
                                <a href="../admin" class="admin-meta">
                                    <i class="fa fa-user-shield"></i>
                                    <span>Admin Panel</span>
                                </a>
                            </div>
                            <div class="app-version">
                                <span class="version-line"></span>
                                <span class="version-text">
                                    v<?= htmlspecialchars($data['versi_aplikasi'] ?? '1.0.0'); ?>
                                </span>
                                <span class="version-line"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>
<script src="../assets/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

setTimeout(() => {
    const alert = document.getElementById('customAlert');
    if (alert) {
        alert.style.transition = "opacity 0.5s ease-out";
        alert.style.opacity = 0;
        setTimeout(() => alert.remove(), 500);
    }
}, 4000);

document.addEventListener("DOMContentLoaded", function() {
    var base64Text = "<?php echo $encryptedText; ?>";
    if (base64Text) {
        var decodedText = atob(base64Text);
        document.getElementById("enc").innerHTML = decodedText;
    }
});

document.addEventListener("DOMContentLoaded", function() {
    var enc = document.getElementById("enc");
    if (enc) {
        document.getElementById("page_sign").value = enc.dataset.sign;
    }
});
</script>

</html>