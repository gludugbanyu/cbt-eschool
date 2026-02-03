<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();

include '../inc/dataadmin.php';

// Tambah FAQ
if (isset($_POST['tambah'])) {
    function clean_html($text) {
    // Hapus tag <p><br></p>
    $text = str_ireplace('<p><br></p>', '', $text);
    // Hapus tag <br>, <br/>, <br />
    $text = preg_replace('#<br\s*/?>#i', '', $text);
    // Hapus tag pembuka dan penutup <p>...</p>
    $text = preg_replace('#<p>(.*?)</p>#i', '$1', $text);
    return trim($text);
}

// Ambil data dari POST dan bersihkan
$q_raw = $_POST['question'] ?? '';
$a_raw = $_POST['answer'] ?? '';

$q_clean = clean_html($q_raw);
$a_clean = clean_html($a_raw);

// Escape sebelum query SQL
$q = mysqli_real_escape_string($koneksi, $q_clean);
$a = mysqli_real_escape_string($koneksi, $a_clean);

    if(mysqli_query($koneksi, "INSERT INTO faq (question, answer) VALUES ('$q', '$a')")) {
        header("Location: ".$_SERVER['PHP_SELF']."?alert=success&msg=FAQ%20berhasil%20ditambahkan");
    } else {
        header("Location: ".$_SERVER['PHP_SELF']."?alert=error&msg=Gagal%20menambahkan%20FAQ");
    }
    exit();
}

// Edit FAQ
if (isset($_POST['edit'])) {
    $id = (int) $_POST['id'];
    function clean_html($text) {
    // Hapus tag <p><br></p>
    $text = str_ireplace('<p><br></p>', '', $text);
    // Hapus tag <br>, <br/>, <br />
    $text = preg_replace('#<br\s*/?>#i', '', $text);
    // Hapus tag pembuka dan penutup <p>...</p>
    $text = preg_replace('#<p>(.*?)</p>#i', '$1', $text);
    return trim($text);
}

// Ambil data dari POST dan bersihkan
$q_raw = $_POST['question'] ?? '';
$a_raw = $_POST['answer'] ?? '';

$q_clean = clean_html($q_raw);
$a_clean = clean_html($a_raw);

// Escape sebelum query SQL
$q = mysqli_real_escape_string($koneksi, $q_clean);
$a = mysqli_real_escape_string($koneksi, $a_clean);

    if(mysqli_query($koneksi, "UPDATE faq SET question='$q', answer='$a' WHERE id=$id")) {
        header("Location: ".$_SERVER['PHP_SELF']."?alert=success&msg=FAQ%20berhasil%20diperbarui");
    } else {
        header("Location: ".$_SERVER['PHP_SELF']."?alert=error&msg=Gagal%20memperbarui%20FAQ");
    }
    exit();
}

// Hapus FAQ
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    if(mysqli_query($koneksi, "DELETE FROM faq WHERE id=$id")) {
        header("Location: ".$_SERVER['PHP_SELF']."?alert=success&msg=FAQ%20berhasil%20dihapus");
    } else {
        header("Location: ".$_SERVER['PHP_SELF']."?alert=error&msg=Gagal%20menghapus%20FAQ");
    }
    exit();
}

// Ambil semua data FAQ
$faq = mysqli_query($koneksi, "SELECT * FROM faq ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ | Asisten BOT</title>
    <?php include '../inc/css.php'; ?>
    <link href="../assets/summernote/summernote-bs5.css" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'navbar.php'; ?>
        <main class="content">
            <div class="container-fluid p-0">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">FAQ | Asisten BOT</h5>
                            </div>
                            <div class="card-body">
                                <!-- Form Tambah -->
                                <div class="card mb-4">
                                    <div class="card-header bg-secondary text-white">
                                        <i class="fa fa-plus-circle"></i> Tambah FAQ
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label>Pertanyaan</label>
                                                        <textarea name="question" id="question_add" class="form-control" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label>Jawaban</label>
                                                        <textarea name="answer" id="answer_add" class="form-control" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" name="tambah" class="btn btn-primary">
                                                <i class="fa fa-plus"></i> Tambah
                                            </button>
                                        </form>

                                <table id="faqTable" class="table table-bordered table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="30%">Pertanyaan</th>
                                            <th width="50%">Jawaban</th>
                                            <th width="20%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_assoc($faq)): ?>
                                            <tr>
                                                <form method="post">
                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                    <td>
                                                        <div contenteditable="true" class="editable-cell" data-name="question"><?= htmlspecialchars_decode($row['question']); ?></div>
                                                        <input type="hidden" name="question" value="<?= htmlspecialchars_decode($row['question']); ?>">
                                                    </td>
                                                    <td>
                                                        <div contenteditable="true" class="editable-cell" data-name="answer"><?= htmlspecialchars_decode($row['answer']); ?></div>
                                                        <input type="hidden" name="answer" value="<?= htmlspecialchars_decode($row['answer']); ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="submit" name="edit" class="btn btn-success btn-sm">
                                                            <i class="fa fa-save"></i> Simpan
                                                        </button>
                                                        <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm delete-btn" data-id="<?= $row['id']; ?>">
                                                            <i class="fa fa-trash"></i> Hapus
                                                        </a>
                                                    </td>
                                                </form>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../inc/js.php'; ?>
<script src="../assets/summernote/summernote-bs5.js"></script>
<!-- Tambahkan ini di akhir sebelum </body> -->
<script>
$(document).ready(function () {
    // Konfigurasi Summernote

    // SweetAlert dari URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const alertType = urlParams.get('alert');
    const alertMsg = urlParams.get('msg');

    if (alertType && alertMsg) {
        Swal.fire({
            icon: alertType,
            title: alertType === 'success' ? 'Sukses!' : 'Error!',
            text: decodeURIComponent(alertMsg),
            timer: 3000,
            showConfirmButton: false
        });

        // Bersihkan parameter dari URL
        if (window.history.replaceState) {
            const cleanUrl = window.location.origin + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }

    // SweetAlert konfirmasi hapus
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak dapat mengembalikan data ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });

    // Hapus <p>, </p>, dan <br> saat submit form
    $('form').on('submit', function (e) {
        $('.summernote').each(function () {
            const editor = $(this);
            let content = editor.summernote('code');

            content = content
                .replace(/<p><br><\/p>/gi, '')       // hilangkan <p><br></p>
                .replace(/<p>(.*?)<\/p>/gi, '$1')    // hilangkan <p>...</p>
                .replace(/<br\s*\/?>/gi, '');        // hilangkan <br>

            // Set ulang isi editor
            editor.summernote('code', content);

            // Jika ada textarea hidden
            const textarea = editor.next('textarea');
            if (textarea.length) {
                textarea.val(content);
            }
        });
    });
});

$(document).ready(function() {
    $('#faqTable').DataTable({
        responsive: true
    });
});

document.querySelectorAll('.editable-cell').forEach(cell => {
    cell.addEventListener('input', function () {
        const input = this.closest('td').querySelector('input[name="' + this.dataset.name + '"]');
        input.value = this.innerHTML;
    });
});

</script>
</body>
</html>
