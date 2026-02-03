<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

$role = $_SESSION['role'];
$id_admin = $_SESSION['admin_id'];

$files = [];

if ($role == 'admin') {
    $directory = "../gambar/";
    $files = array_diff(scandir($directory), ['..', '.']);
} else {
    $q = mysqli_query($koneksi, "
        SELECT pertanyaan, pilihan_1, pilihan_2, pilihan_3, pilihan_4, pilihan_5
        FROM butir_soal b
        JOIN soal s ON b.kode_soal = s.kode_soal
        WHERE FIND_IN_SET('$id_admin', s.id_pembuat)
    ");

    while ($r = mysqli_fetch_assoc($q)) {
        foreach ($r as $kolom) {
            preg_match_all('/gambar\/([^"]+)/', $kolom, $matches);
            foreach ($matches[1] as $img) {
                $files[] = $img;
            }
        }
    }

    $files = array_unique($files);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gambar Soal</title>
    <?php include '../inc/css.php'; ?>
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
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Direktori Gambar</h5>
                                </div>
                                <div class="card-body">
                                    <form id="formUpload" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="gambar" class="form-label">Upload Gambar (Max 10 gambar sekaligus)</label>
                                            <input class="form-control" type="file" name="gambar[]" id="gambar" multiple required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Upload</button>
                                    </form>

                                    <div class="progress mt-3" style="height: 20px; display:none;" id="uploadProgress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" id="progressBar">0%</div>
                                    </div>

                                    <hr>
                                    <h5 class="card-title">Daftar Gambar</h5>
                                    <div class="table-responsive">
                                        <table id="gambarTable" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" id="selectAll"></th>
                                                    <th>Nama File</th>
                                                    <th>Preview</th>
                                                    <th>Path</th>
                                                    <th>Ukuran</th>
                                                    <th>Tanggal Upload</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
<?php
$directory = "../gambar/";
$validExtensions = ['jpg','jpeg','png','gif','webp'];

$images = array_filter($files, function($file) use ($validExtensions, $directory) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return in_array($ext, $validExtensions) && file_exists($directory.$file);
});

usort($images, function($a, $b) use ($directory) {
    return filemtime($directory . $b) - filemtime($directory . $a);
});

if (!empty($images)) {
    foreach ($images as $image) {

        $timestamp = filemtime($directory . $image);
        $uploadDate = date("H:i, d F Y", $timestamp);
        $fileSize = filesize($directory . $image);

        if ($fileSize >= 1048576) {
            $fileSizeFormatted = round($fileSize / 1048576, 2) . ' MB';
        } elseif ($fileSize >= 1024) {
            $fileSizeFormatted = round($fileSize / 1024, 2) . ' KB';
        } else {
            $fileSizeFormatted = $fileSize . ' B';
        }
?>
<tr>
    <td><input type="checkbox" class="checkbox-delete" value="<?= $image ?>"></td>
    <td><?= $image ?></td>
    <td>
        <a href="../gambar/<?= $image ?>" target="_blank">
            <img src="../gambar/<?= $image ?>" width="100">
        </a>
    </td>
    <td>
        <button class="btn btn-outline-secondary copy-btn" data-target="imgTag<?= md5($image) ?>">
            Copy <i class="fa fa-copy"></i>
        </button>
        <code id="imgTag<?= md5($image) ?>" style="display:none;">
            &lt;img src="../gambar/<?= $image ?>"&gt;
        </code>
    </td>
    <td><?= $fileSizeFormatted ?></td>
    <td><?= $uploadDate ?></td>
    <td>
        <button type="button" class="btn btn-danger delete-single" data-file="<?= $image ?>">
            Hapus
        </button>
    </td>
</tr>
<?php
    }
} else {
    echo '<tr>
<td></td>
<td>Tidak ada gambar</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>';

}
?>
</tbody>

                                            </tbody>
                                        </table>
                                    </div>

                                    <form id="deleteImagesForm">
                                        <button type="submit" class="btn btn-danger">Hapus Gambar Terpilih</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>

        </div>
    </div>

<?php include '../inc/js.php'; ?>
<script>
$(document).ready(function() {
    var table = $('#gambarTable').DataTable({
        "paging": true,
        "lengthChange": false,
        "pageLength": 10,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "columnDefs": [
            { "orderable": false, "targets": 0 }
        ]
    });

    $('#selectAll').on('click', function() {
        $('.checkbox-delete').prop('checked', this.checked);
    });

    $('#deleteImagesForm').on('submit', function(e) {
        e.preventDefault();
        let selectedFiles = [];
        $('.checkbox-delete:checked').each(function() {
            selectedFiles.push($(this).val());
        });

        if (selectedFiles.length > 0) {
            Swal.fire({
                title: 'Yakin ingin menghapus gambar?',
                text: "Data gambar yang dipilih akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('hapus_gambar.php', {files: selectedFiles}, function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', 'Gambar berhasil dihapus.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', 'Beberapa gambar gagal dihapus.', 'error');
                        }
                    }, 'json');
                }
            });
        } else {
            Swal.fire('Peringatan', 'Silakan pilih gambar yang akan dihapus', 'warning');
        }
    });

    $('#gambarTable').on('click', '.delete-single', function() {
        const file = $(this).data('file');
        Swal.fire({
            title: 'Yakin ingin menghapus gambar ini?',
            text: "Gambar ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('hapus_gambar.php', {files: [file]}, function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', 'Gambar berhasil dihapus.', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', 'Gambar gagal dihapus.', 'error');
                    }
                }, 'json');
            }
        });
    });
});

document.getElementById('formUpload').addEventListener('submit', function (e) {
    e.preventDefault();

    const fileInput = document.getElementById('gambar');
    const files = fileInput.files;

    if (files.length > 10) {
        Swal.fire('Peringatan', 'Maksimal upload 10 gambar sekaligus.', 'warning');
        return;
    }

    const form = e.target;
    const formData = new FormData(form);
    const xhr = new XMLHttpRequest();

    const progressContainer = document.getElementById("uploadProgress");
    const progressBar = document.getElementById("progressBar");

    xhr.open("POST", "proses_upload_gambar.php", true);

    xhr.upload.addEventListener("progress", function (e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressContainer.style.display = "block";
            progressBar.style.width = percent + "%";
            progressBar.innerText = percent + "%";
        }
    });

    xhr.onload = function () {
        progressContainer.style.display = "none";
        if (xhr.status === 200) {
            const results = JSON.parse(xhr.responseText);
            let html = '<ul style="text-align: left;">';

            results.forEach(res => {
                const icon = res.status === 'success' ? '✅' : '❌';
                html += `<li>${icon} <strong>${res.file}</strong>: ${res.message}</li>`;
            });

            html += '</ul>';

            Swal.fire({
                title: 'Hasil Upload',
                html: html,
                icon: 'info'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Gagal', 'Terjadi kesalahan saat upload.', 'error');
        }
    };

    xhr.send(formData);
});

document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const targetId = this.getAttribute('data-target');
        const textToCopy = document.getElementById(targetId).textContent;

        navigator.clipboard.writeText(textToCopy).then(() => {
            Swal.fire('Tersalin!', 'Path gambar berhasil disalin ke clipboard.', 'success');
        }).catch(() => {
            Swal.fire('Gagal', 'Tidak dapat menyalin teks.', 'error');
        });
    });
});
</script>
</body>
</html>
