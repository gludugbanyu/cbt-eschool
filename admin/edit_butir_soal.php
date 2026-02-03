<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

if (!isset($_GET['id_soal']) || !isset($_GET['kode_soal'])) {
    header('Location: soal.php');
    exit();
}

$id_soal = $_GET['id_soal'];
$kode_soal = $_GET['kode_soal'];
only_pemilik_soal_by_kode($kode_soal);

// Deteksi jumlah opsi dari URL, default 4 jika tidak ada
$jumlah_opsi_url = (isset($_GET['opsi']) && $_GET['opsi'] == '5') ? 5 : 4;

// Ambil data soal utama
$query_soal = mysqli_query($koneksi, "SELECT * FROM soal WHERE kode_soal='$kode_soal'");
$data_soal = mysqli_fetch_assoc($query_soal);

if ($data_soal['status'] == 'Aktif') {
   $swal = "soal_aktif";
}

// Ambil data butir soal yang akan diedit
$query_butir = mysqli_query($koneksi, "
    SELECT b.*
    FROM butir_soal b
    JOIN soal s ON b.kode_soal = s.kode_soal
    WHERE b.id_soal='$id_soal'
    AND s.kode_soal='$kode_soal'
");
if(mysqli_num_rows($query_butir) == 0){
    $_SESSION['error'] = "Butir soal tidak valid.";
    header("Location: soal.php");
    exit;
}
$butir_soal = mysqli_fetch_assoc($query_butir);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // VALIDASI WAJIB
    if (empty($_POST['pertanyaan']) || empty($_POST['tipe_soal']) || empty($_POST['nomor_soal'])) {
        $swal = "form_kosong";
    }

    $pertanyaan = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pertanyaan']));
    $tipe_soal  = mysqli_real_escape_string($koneksi, $_POST['tipe_soal']);
    $nomor_soal = mysqli_real_escape_string($koneksi, $_POST['nomor_soal']);

    // CEK DUPLIKAT
    $cek = mysqli_query($koneksi, "SELECT 1 FROM butir_soal 
        WHERE nomer_soal='$nomor_soal' 
        AND kode_soal='$kode_soal' 
        AND id_soal!='$id_soal'");

    if (mysqli_num_rows($cek) > 0) {
        $swal = "nomor_duplikat";
    }

    // KHUSUS PG WAJIB PILIH JAWABAN
    if (($tipe_soal == 'Pilihan Ganda' || $tipe_soal == 'Pilihan Ganda Kompleks')
        && (!isset($_POST['jawaban_benar']) || count($_POST['jawaban_benar']) == 0)) {
        $swal = "jawaban_kosong";
    }

    // ❗ STOP JIKA ADA ERROR
    if (!isset($swal)) {

        if ($tipe_soal == 'Pilihan Ganda' || $tipe_soal == 'Pilihan Ganda Kompleks') {

            $jawaban_benar = implode(",", $_POST['jawaban_benar']);

            $query = "UPDATE butir_soal SET
                pertanyaan='$pertanyaan',
                tipe_soal='$tipe_soal',
                nomer_soal='$nomor_soal',
                pilihan_1='".mysqli_real_escape_string($koneksi, $_POST['pilihan_1'])."',
                pilihan_2='".mysqli_real_escape_string($koneksi, $_POST['pilihan_2'])."',
                pilihan_3='".mysqli_real_escape_string($koneksi, $_POST['pilihan_3'])."',
                pilihan_4='".mysqli_real_escape_string($koneksi, $_POST['pilihan_4'])."',
                pilihan_5='".mysqli_real_escape_string($koneksi, $_POST['pilihan_5'])."',
                jawaban_benar='$jawaban_benar'
                WHERE id_soal='$id_soal' AND kode_soal='$kode_soal'
";

        } elseif ($tipe_soal == 'Benar/Salah') {

            $jawaban_benar = implode("|", $_POST['jawaban_benar'] ?? []);

            $query = "UPDATE butir_soal SET
                pertanyaan='$pertanyaan',
                tipe_soal='$tipe_soal',
                nomer_soal='$nomor_soal',
                pilihan_1='".mysqli_real_escape_string($koneksi, $_POST['pilihan_1'])."',
                pilihan_2='".mysqli_real_escape_string($koneksi, $_POST['pilihan_2'])."',
                pilihan_3='".mysqli_real_escape_string($koneksi, $_POST['pilihan_3'])."',
                pilihan_4='".mysqli_real_escape_string($koneksi, $_POST['pilihan_4'])."',
                pilihan_5='".mysqli_real_escape_string($koneksi, $_POST['pilihan_5'])."',
                jawaban_benar='$jawaban_benar'
                WHERE id_soal='$id_soal' AND kode_soal='$kode_soal'
";

        } elseif ($tipe_soal == 'Menjodohkan') {

            $pairs = [];
            foreach ($_POST['pasangan_soal'] as $i => $s) {
                $j = $_POST['pasangan_jawaban'][$i];
                if ($s && $j) $pairs[] = "$s:$j";
            }
            $jawaban_benar = implode("|", $pairs);

            $query = "UPDATE butir_soal SET
                pertanyaan='$pertanyaan',
                tipe_soal='$tipe_soal',
                nomer_soal='$nomor_soal',
                jawaban_benar='$jawaban_benar'
                WHERE id_soal='$id_soal' AND kode_soal='$kode_soal'
";

        } else { // Uraian

            $jawaban_benar = mysqli_real_escape_string($koneksi, $_POST['jawaban_benar']);

            $query = "UPDATE butir_soal SET
                pertanyaan='$pertanyaan',
                tipe_soal='$tipe_soal',
                nomer_soal='$nomor_soal',
                jawaban_benar='$jawaban_benar'
                WHERE id_soal='$id_soal' AND kode_soal='$kode_soal'
";
        }

        if (mysqli_query($koneksi, $query)) {
            header("Location: daftar_butir_soal.php?kode_soal=$kode_soal&success=1");
            exit();
        } else {
            $swal = "gagal_simpan";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Butir Soal</title>
    <?php include '../inc/css.php'; ?>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <link href="../assets/summernote/summernote-bs5.css" rel="stylesheet">
    <style>
        .note-editable img { max-width: 400px !important; height: auto; }
        .no-click { pointer-events: none; background-color: #e9ecef; }
        .border-box { border: 1px solid #ced4da; padding: 15px; border-radius: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main">
            <?php include 'navbar.php'; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Form Edit Butir Soal</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-2 mb-3">
                                        <label for="nomor_soal" class="form-label">Nomor Soal</label>
                                        <input type="number" class="form-control" name="nomor_soal" value="<?= $butir_soal['nomer_soal'] ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="tipe_soal" class="form-label">Tipe Soal</label>
                                        <select class="form-control no-click" id="tipe_soal" name="tipe_soal" required>
                                            <option value="Pilihan Ganda" <?= $butir_soal['tipe_soal'] == 'Pilihan Ganda' ? 'selected' : '' ?>>Pilihan Ganda</option>
                                            <option value="Pilihan Ganda Kompleks" <?= $butir_soal['tipe_soal'] == 'Pilihan Ganda Kompleks' ? 'selected' : '' ?>>Pilihan Ganda Kompleks</option>
                                            <option value="Benar/Salah" <?= $butir_soal['tipe_soal'] == 'Benar/Salah' ? 'selected' : '' ?>>Benar/Salah</option>
                                            <option value="Menjodohkan" <?= $butir_soal['tipe_soal'] == 'Menjodohkan' ? 'selected' : '' ?>>Menjodohkan</option>
                                            <option value="Uraian" <?= $butir_soal['tipe_soal'] == 'Uraian' ? 'selected' : '' ?>>Uraian</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Pertanyaan</label>
                                    <textarea class="form-control" id="pertanyaan" name="pertanyaan" required><?= $butir_soal['pertanyaan'] ?></textarea>
                                </div>
                                <hr>

                                <div id="pg-fields" class="d-none">
                                    <?php 
                                    $jawaban_arr = explode(',', $butir_soal['jawaban_benar']);
                                    for ($i = 1; $i <= $jumlah_opsi_url; $i++) : 
                                    ?>
                                        <div class="border-box">
                                            <label class="form-label">Pilihan <?= $i ?></label>
                                            <textarea class="form-control editor-opsi" name="pilihan_<?= $i ?>"><?= $butir_soal['pilihan_'.$i] ?></textarea>
                                            <div class="mt-2">
                                                <input type="checkbox" name="jawaban_benar[]" value="pilihan_<?= $i ?>" class="pg-check" <?= in_array("pilihan_$i", $jawaban_arr) ? 'checked' : '' ?>> Jawaban Benar
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>

                                <div id="bs-fields" class="d-none">
                                    <div id="bs-container">
                                        <?php 
                                        $jawaban_bs = explode('|', $butir_soal['jawaban_benar']);
                                        for ($i = 1; $i <= 5; $i++): 
                                            $val_pilihan = $butir_soal['pilihan_'.$i];
                                            if ($i > 1 && empty($val_pilihan)) continue;
                                        ?>
                                        <div class="border-box bs-row position-relative" id="bs_row_<?= $i ?>">

                                            <label class="form-label">Pernyataan <?= $i ?></label>
                                            <textarea class="form-control editor-simple" name="pilihan_<?= $i ?>"><?= $val_pilihan ?></textarea>
                                            <div class="mt-2">
                                                <label><input type="radio" name="jawaban_benar[<?= $i-1 ?>]" value="Benar" <?= ($jawaban_bs[$i-1] ?? '') == 'Benar' ? 'checked' : '' ?>> Benar</label>
                                                <label class="ms-3"><input type="radio" name="jawaban_benar[<?= $i-1 ?>]" value="Salah" <?= ($jawaban_bs[$i-1] ?? '') == 'Salah' ? 'checked' : '' ?>> Salah</label>
                                                <?php if($i > 1): ?>
<button type="button"
        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
        onclick="removeBS(<?= $i ?>)">
    <i class="fas fa-trash"></i>
</button>
<?php endif; ?>

                                            </div>
                                        </div>
                                        <?php endfor; ?>
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm mb-3" onclick="addBS()"><i class="fas fa-plus"></i> Tambah Pernyataan</button>
                                </div>

                                <div id="match-fields" class="d-none">
                                    <div id="match-container">
                                        <?php 
                                        $pairs = explode('|', $butir_soal['jawaban_benar']);
                                        foreach($pairs as $p): 
                                            $item = explode(':', $p);
                                        ?>
                                        <div class="row mb-2 match-row">
                                            <div class="col-md-5"><textarea class="form-control" name="pasangan_soal[]"><?= $item[0] ?? '' ?></textarea></div>
                                            <div class="col-md-5"><textarea class="form-control" name="pasangan_jawaban[]"><?= $item[1] ?? '' ?></textarea></div>
                                            <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Hapus</button></div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm mb-3" onclick="addMatch()"><i class="fas fa-plus"></i> Tambah Pasangan</button>
                                </div>

                                <div id="uraian-fields" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label">Kunci Jawaban</label>
                                        <textarea class="form-control" name="jawaban_benar" rows="3"><?= $butir_soal['jawaban_benar'] ?></textarea>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                                    <a href="daftar_butir_soal.php?kode_soal=<?= $kode_soal ?>" class="btn btn-danger">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../inc/js.php'; ?>
    <script src="../assets/summernote/summernote-bs5.js"></script>
    <script>
        function makeEditor(el, height=100) {
    $(el).summernote({
        height: height,
        toolbar: [['insert', ['picture']], ['view', ['codeview']]],
        callbacks: {
            onImageUpload: function(files) {
                sendFile(files[0], $(this));
            }
        }
    });
}

function sendFile(file, editor) {
    let data = new FormData();
    data.append("file", file);

    $.ajax({
        url: "uploadeditor.php",
        type: "POST",
        data: data,
        contentType: false,
        processData: false,
        success: function(res) {
            let hasil = JSON.parse(res);
            if (hasil.url) {
                editor.summernote('focus');
                editor.summernote('editor.insertImage', hasil.url);
            }
        }
    });
}

$(document).ready(function() {

    makeEditor('#pertanyaan', 250);

    $('.editor-opsi').each(function() {
        makeEditor(this, 100);
    });

    $('.editor-simple').each(function() {
        makeEditor(this, 100);
    });

    showFields($('#tipe_soal').val());

    $(document).on('click', '.pg-check', function() {
        if ($('#tipe_soal').val() === 'Pilihan Ganda') {
            $('.pg-check').not(this).prop('checked', false);
        }
    });

});


function showFields(tipe) {
    $("#pg-fields, #bs-fields, #match-fields, #uraian-fields")
        .addClass('d-none')
        .find('input, textarea').prop('disabled', true);

    if (tipe === 'Pilihan Ganda' || tipe === 'Pilihan Ganda Kompleks') {
        $("#pg-fields").removeClass('d-none').find('input, textarea').prop('disabled', false);
    } else if (tipe === 'Benar/Salah') {
        $("#bs-fields").removeClass('d-none').find('input, textarea').prop('disabled', false);
    } else if (tipe === 'Menjodohkan') {
        $("#match-fields").removeClass('d-none').find('input, textarea').prop('disabled', false);
    } else if (tipe === 'Uraian') {
        $("#uraian-fields").removeClass('d-none').find('textarea').prop('disabled', false);
    }
}

/* ================= BENAR SALAH ================= */

function addBS() {
    let count = $('.bs-row').length + 1;

    if (count > 5) {
        Swal.fire({
            icon: 'warning',
            title: 'Maksimal 5 Pernyataan',
            text: 'Benar / Salah hanya boleh sampai 5 pernyataan.',
            confirmButtonColor: '#3085d6'
        });
        return;
    }

    let html = `
    <div class="border-box bs-row position-relative" id="bs_row_${count}">
        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
            onclick="removeBS(${count})">
            <i class="fas fa-trash"></i>
        </button>

        <label class="form-label">Pernyataan ${count}</label>
        <textarea class="form-control editor-simple" name="pilihan_${count}"></textarea>
        <div class="mt-2">
            <label><input type="radio" name="jawaban_benar[${count-1}]" value="Benar"> Benar</label>
            <label class="ms-3"><input type="radio" name="jawaban_benar[${count-1}]" value="Salah"> Salah</label>
        </div>
    </div>`;

    $('#bs-container').append(html);
    makeEditor($(`[name="pilihan_${count}"]`), 100);

}

function removeBS(id) {
    Swal.fire({
        icon: 'question',
        title: 'Hapus pernyataan ini?',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $(`#bs_row_${id}`).remove();
        }
    });
}


/* ================= MENJODOHKAN ================= */

function addMatch() {
    let html = `
    <div class="row mb-2 match-row border-box position-relative">
        <div class="col-md-5">
            <textarea class="form-control" name="pasangan_soal[]" placeholder="Pilihan"></textarea>
        </div>
        <div class="col-md-5">
            <textarea class="form-control" name="pasangan_jawaban[]" placeholder="Pasangan"></textarea>
        </div>
        <div class="col-md-2 d-flex align-items-center">
            <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeRow(this)">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>`;

    $('#match-container').append(html);
}

function removeRow(btn) {
    Swal.fire({
        icon: 'question',
        title: 'Hapus pasangan ini?',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $(btn).closest('.match-row').remove();
        }
    });
}
</script>
<?php if(isset($swal) && $swal == "soal_aktif"): ?>
<script src="../assets/js/sweetalert.js"></script>
<script>
Swal.fire({
    icon: "warning",
    title: "Tidak Bisa Diedit!",
    text: "Soal ini sudah aktif dan tidak bisa diedit!",
    showConfirmButton: false,
    timer: 2000
}).then(() => {
    window.location.href = "soal.php";
});
</script>
<?php endif; ?>
<?php if(isset($swal) && $swal == "jawaban_kosong"): ?>
<script src="../assets/js/sweetalert.js"></script>
<script>
Swal.fire({
    icon: "warning",
    title: "Jawaban Belum Dipilih",
    text: "Harap pilih minimal satu jawaban benar!"
}).then(() => {
    window.history.back();
});
</script>
<?php endif; ?>
<?php if(isset($swal) && $swal == "gagal_simpan"): ?>
<script src="../assets/js/sweetalert.js"></script>
<script>
Swal.fire({
    icon: "error",
    title: "Gagal Menyimpan",
    text: "Terjadi kesalahan saat update data!"
});
</script>
<?php endif; ?>
<?php if(isset($swal) && $swal == "form_kosong"): ?>
<script src="../assets/js/sweetalert.js"></script>
<script>
Swal.fire({
    icon: "warning",
    title: "Form Belum Lengkap",
    text: "Harap isi semua field!"
});
</script>
<?php endif; ?>
<?php if(isset($swal) && $swal == "nomor_duplikat"): ?>
<script src="../assets/js/sweetalert.js"></script>
<script>
Swal.fire({
    icon: "error",
    title: "Nomor Soal Sudah Ada!",
    text: "Gunakan nomor soal yang lain."
});
</script>
<?php endif; ?>

</body>
</html>