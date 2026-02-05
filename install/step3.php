<?php
session_start();

// Cek jika sudah terinstal
if (file_exists(__DIR__ . '/../koneksi/koneksi.php')) {
    header('Location: error.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama_admin']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $host = $_SESSION['db_host'];
    $user = $_SESSION['db_user'];
    $pass = $_SESSION['db_pass'];
    $db   = $_SESSION['db_name'];

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Simpan akun admin pertama (wajib admin)
$stmt = $conn->prepare("
    INSERT INTO admins (username, nama_admin, password, created_at, role) 
    VALUES (?, ?, ?, NOW(), 'admin')
");
$stmt->execute([$username, $nama, $password]);

        // Generate file koneksi.php dari config_example.php
        $template = file_get_contents('config_example.php');
        $finalConfig = str_replace(
            ['{DB_HOST}', '{DB_USER}', '{DB_PASS}', '{DB_NAME}'],
            [$host, $user, $pass, $db],
            $template
        );
        file_put_contents(__DIR__ . '/../koneksi/koneksi.php', $finalConfig);

        // Hancurkan session lama (WAJIB di installer)
$_SESSION = [];
session_unset();
session_destroy();

header('Location: selesai.php');
exit;

    } catch (PDOException $e) {
        $error = "Gagal menyimpan data admin: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Langkah 3 - Buat Akun Admin</title>
  <link href="../assets/bootstrap-5.3.6/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="../assets/images/icon.png" />
  <style>
    body {
      background-color: #f2f4f7;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 500px;
      margin-top: 60px;
      padding: 30px;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }
    .logo {
      display: block;
      margin: 0 auto 20px;
      width: 70px;
    }

  </style>
</head>
<body>
  <div class="container">
    <img src="../assets/images/codelite.png" class="logo" alt="Logo CBT eSchool">
    <h4 class="text-center mb-3">Langkah 3: Buat Admin</h4>

<!-- Progress bar -->
<div class="progress mb-4" style="height: 22px; border-radius: 12px; overflow: hidden;">
  <div class="progress-bar text-white" id="animatedBar"
       style="
         width: 0%;
         background: linear-gradient(90deg, #6a11cb, #2575fc); 
         font-weight: 600;
         font-size: 14px;
         display: flex;
         align-items: center;
         justify-content: center;
         transition: width 1.5s ease;
       ">
    85%
  </div>
</div>

    <?php if (!empty($error)) : ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label for="username" class="form-label">Username Admin</label>
        <input type="text" class="form-control" id="username" name="username" required>
        <small id="usernameWarning" class="text-danger d-none">
          Hindari menggunakan username <strong>admin</strong> demi keamanan.
        </small>
      </div>
      <div class="mb-3">
        <label for="nama_admin" class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" name="nama_admin" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password Admin</label>
        <input type="password" class="form-control" name="password" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-outline-secondary" id="submitBtn">Simpan</button>
      </div>
    </form>
  </div>

  <script>
    const usernameInput = document.getElementById('username');
    const warning = document.getElementById('usernameWarning');
    const submitBtn = document.getElementById('submitBtn');

    usernameInput.addEventListener('input', function() {
      if (this.value.trim().toLowerCase() === 'admin') {
        warning.classList.remove('d-none');
        submitBtn.disabled = true;
      } else {
        warning.classList.add('d-none');
        submitBtn.disabled = false;
      }
    });
  </script>
  <script>
  window.addEventListener('DOMContentLoaded', () => {
    const bar = document.getElementById('animatedBar');
    setTimeout(() => {
      bar.style.width = '85%';
    }, 300); // delay untuk animasi
  });
</script>
</body>
</html>
