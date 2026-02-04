<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

if (empty($_GET['kode_soal']) || empty($_GET['nomer_baru'])) {
    header("Location: soal.php");
    exit();
}

$kode_soal = $_GET['kode_soal'];
$nomer_baru = $_GET['nomer_baru'];
only_pemilik_soal_by_kode($kode_soal);



// Mendeteksi jumlah opsi dari URL, default 4 jika tidak diset
$jumlah_opsi_url = (isset($_GET['opsi']) && $_GET['opsi'] == '5') ? 5 : 4;

// Ambil data soal
$query_soal = mysqli_query($koneksi, "SELECT * FROM soal WHERE kode_soal='$kode_soal'");
$data_soal = mysqli_fetch_assoc($query_soal);

if ($data_soal['status'] == 'Aktif') {
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="../assets/js/sweetalert.js"></script>
</head>
<body>
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
</body>
</html>';
exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (empty($_POST['pertanyaan']) || empty($_POST['tipe_soal']) || empty($_POST['nomer_soal'])) {
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="../assets/js/sweetalert.js"></script>
</head>
<body>
<script>
Swal.fire({
    icon: "warning",
    title: "Form Belum Lengkap",
    text: "Harap isi semua field wajib!",
    confirmButtonText: "Kembali"
}).then(() => { window.history.back(); });
</script>
</body>
</html>';
exit;
}



    $pertanyaan = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pertanyaan']));

    $tipe_soal = mysqli_real_escape_string($koneksi, $_POST['tipe_soal']);
    $nomer_soal = mysqli_real_escape_string($koneksi, $_POST['nomer_soal']);

    // Check nomor ganda
    $query_check = "SELECT * FROM butir_soal WHERE kode_soal = '$kode_soal' AND nomer_soal = '$nomer_soal'";
    $result_check = mysqli_query($koneksi, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo '<!DOCTYPE html><html><head><script src="../assets/js/sweetalert.js"></script></head><body><script>Swal.fire({icon: "error", title: "Nomor Soal Sudah Ada!", confirmButtonText: "OK"}).then(() => { window.history.back(); });</script></body></html>';
        exit;
    }

    $query = "";

    if ($tipe_soal == 'Pilihan Ganda' || $tipe_soal == 'Pilihan Ganda Kompleks') {
        $p1 = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pilihan_1'] ?? ''));
$p2 = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pilihan_2'] ?? ''));
$p3 = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pilihan_3'] ?? ''));
$p4 = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pilihan_4'] ?? ''));
$p5 = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pilihan_5'] ?? ''));


// Ganti baris pengecekan jawaban_benar dengan ini:
if (!isset($_POST['jawaban_benar']) || !is_array($_POST['jawaban_benar']) || count($_POST['jawaban_benar']) == 0) {
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <script src="../assets/js/sweetalert.js"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: "warning",
                title: "Jawaban Belum Dipilih",
                text: "Harap pilih minimal satu jawaban benar!",
                confirmButtonText: "Kembali"
            }).then(() => {
                window.history.back();
            });
        </script>
    </body>
    </html>
    ';
    exit;
}

        $jawaban_benar = implode(",", $_POST['jawaban_benar']);

        $query = "INSERT INTO butir_soal (kode_soal, nomer_soal, pertanyaan, tipe_soal, pilihan_1, pilihan_2, pilihan_3, pilihan_4, pilihan_5, jawaban_benar, status_soal)
                  VALUES ('$kode_soal', '$nomer_soal', '$pertanyaan', '$tipe_soal', '$p1', '$p2', '$p3', '$p4', '$p5', '$jawaban_benar', 'Aktif')";

    } elseif ($tipe_soal == 'Benar/Salah') {
        $p1 = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pilihan_1'] ?? ''));
$p2 = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pilihan_2'] ?? ''));
$p3 = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pilihan_3'] ?? ''));
$p4 = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pilihan_4'] ?? ''));
$p5 = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['pilihan_5'] ?? ''));
        $jawaban_benar = implode("|", $_POST['jawaban_benar'] ?? []);

        $query = "INSERT INTO butir_soal (kode_soal, nomer_soal, pertanyaan, tipe_soal, pilihan_1, pilihan_2, pilihan_3, pilihan_4, pilihan_5, jawaban_benar, status_soal)
                  VALUES ('$kode_soal', '$nomer_soal', '$pertanyaan', '$tipe_soal', '$p1', '$p2', '$p3', '$p4', '$p5', '$jawaban_benar', 'Aktif')";

    } elseif ($tipe_soal == 'Menjodohkan') {
        $pasangan_data = [];
        foreach ($_POST['pasangan_soal'] as $i => $soal) {
            $jawaban = $_POST['pasangan_jawaban'][$i];
            if (!empty($soal) && !empty($jawaban)) {
                $pasangan_data[] =
                mysqli_real_escape_string($koneksi, bersihkan_html(trim($soal))) . ":" .
                mysqli_real_escape_string($koneksi, bersihkan_html(trim($jawaban)));
            
            }
        }
        $jawaban_benar = implode("|", $pasangan_data);
        $query = "INSERT INTO butir_soal (kode_soal, nomer_soal, pertanyaan, tipe_soal, jawaban_benar, status_soal)
                  VALUES ('$kode_soal', '$nomer_soal', '$pertanyaan', '$tipe_soal', '$jawaban_benar', 'Aktif')";

    } elseif ($tipe_soal == 'Uraian') {
        $jawaban_benar = mysqli_real_escape_string($koneksi, bersihkan_html($_POST['jawaban_benar']));

        $query = "INSERT INTO butir_soal (kode_soal, nomer_soal, pertanyaan, tipe_soal, jawaban_benar, status_soal)
                  VALUES ('$kode_soal', '$nomer_soal', '$pertanyaan', '$tipe_soal', '$jawaban_benar', 'Aktif')";
    }

    if (!empty($query)) {
        if (mysqli_query($koneksi, $query)) {
            header("Location: daftar_butir_soal.php?kode_soal=$kode_soal&success=1");
            exit();
        } else {
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="../assets/js/sweetalert.js"></script>
</head>
<body>
<script>
Swal.fire({
    icon: "error",
    title: "Gagal Menyimpan",
    text: "Terjadi kesalahan saat menyimpan data!",
    confirmButtonText: "Kembali"
}).then(() => { window.history.back(); });
</script>
</body>
</html>';
    exit;
}

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Butir Soal</title>
    <?php include '../inc/css.php'; ?>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <link href="../assets/summernote/summernote-bs5.css" rel="stylesheet">
    <style>
        .note-editable img { max-width: 400px !important; height: auto; }
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
                            <h5 class="card-title mb-0">Form Tambah Butir Soal (Opsi: <?= $jumlah_opsi_url ?>)</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="formSoal">
                                <div class="row">
                                    <div class="col-md-2 mb-3">
                                        <label for="nomer_soal" class="form-label">Nomor Soal</label>
                                        <input type="number" class="form-control" name="nomer_soal" value="<?= $nomer_baru ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="tipe_soal" class="form-label">Tipe Soal</label>
                                        <select class="form-control" id="tipe_soal" name="tipe_soal" onchange="showFields(this.value)" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="Pilihan Ganda">Pilihan Ganda</option>
                                            <option value="Pilihan Ganda Kompleks">Pilihan Ganda Kompleks</option>
                                            <option value="Benar/Salah">Benar/Salah</option>
                                            <option value="Menjodohkan">Menjodohkan</option>
                                            <option value="Uraian">Uraian</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 d-none" id="box-pertanyaan">
                                    <label class="form-label">Pertanyaan</label>
                                    <textarea class="form-control" id="pertanyaan" name="pertanyaan" required></textarea>
                                </div>
                                <hr>

                                <div id="pg-fields" class="d-none">
                                    <?php for ($i = 1; $i <= $jumlah_opsi_url; $i++) : ?>
                                        <div class="border-box">
                                            <label class="form-label">Pilihan <?= $i ?></label>
                                            <textarea class="form-control editor-opsi" name="pilihan_<?= $i ?>"></textarea>
                                            <div class="mt-2">
                                                <input type="checkbox" name="jawaban_benar[]" value="pilihan_<?= $i ?>" class="pg-check"> Jawaban Benar
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>

                                <div id="bs-fields" class="d-none">
                                    <div id="bs-container">
                                        <div class="border-box bs-row" id="bs_row_1">
                                            <label class="form-label">Pernyataan 1</label>
                                            <textarea class="form-control editor-simple" name="pilihan_1"></textarea>
                                            <div class="mt-2">
                                                <label><input type="radio" name="jawaban_benar[0]" value="Benar"> Benar</label>
                                                <label class="ms-3"><input type="radio" name="jawaban_benar[0]" value="Salah"> Salah</label>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm mb-3" onclick="addBS()"><i class="fas fa-plus"></i> Tambah Pernyataan</button>
                                </div>

                                <div id="match-fields" class="d-none">
                                    <div id="match-container">
                                        <div class="row mb-2 match-row">
                                            <div class="col-md-5"><textarea class="form-control" name="pasangan_soal[]" placeholder="Pilihan"></textarea></div>
                                            <div class="col-md-5"><textarea class="form-control" name="pasangan_jawaban[]" placeholder="Pasangan"></textarea></div>
                                            <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Hapus</button></div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm mb-3" onclick="addMatch()"><i class="fas fa-plus"></i> Tambah Pasangan</button>
                                </div>

                                <div id="uraian-fields" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label">Kunci Jawaban</label>
                                        <textarea class="form-control" name="jawaban_benar" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="mt-3 d-none" id="box-simpan">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
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

$(document).ready(function() {

    makeEditor('#pertanyaan', 250);

    $('.editor-opsi').each(function() {
        makeEditor(this, 100);
    });

    $('.editor-simple').each(function() {
        makeEditor(this, 100);
    });

    // Pilihan ganda hanya 1 jika PG biasa
    $(document).on('click', '.pg-check', function() {
        let tipe = $('#tipe_soal').val();
        if (tipe === 'Pilihan Ganda') {
            $('.pg-check').not(this).prop('checked', false);
        }
    });

});



function showFields(tipe) {
    $("#pg-fields, #bs-fields, #match-fields, #uraian-fields")
        .addClass('d-none')
        .find('input, textarea, select').prop('disabled', true);

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

let bsCount = 1;

function addBS() {
    if ($('.bs-row').length >= 5) {
        Swal.fire({
            icon: 'warning',
            title: 'Maksimal 5 Pernyataan!',
            text: 'Benar / Salah hanya boleh sampai 5 pernyataan.',
            confirmButtonColor: '#3085d6'
        });
        return;
    }

    bsCount++;

    let html = `
    <div class="border-box bs-row position-relative" id="bs_row_${bsCount}">
        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
            onclick="removeBS(${bsCount})">
            <i class="fas fa-trash"></i>
        </button>

        <label class="form-label">Pernyataan ${bsCount}</label>
        <textarea class="form-control editor-simple" name="pilihan_${bsCount}"></textarea>
        <div class="mt-2">
            <label><input type="radio" name="jawaban_benar[${bsCount-1}]" value="Benar"> Benar</label>
            <label class="ms-3"><input type="radio" name="jawaban_benar[${bsCount-1}]" value="Salah"> Salah</label>
        </div>
    </div>`;

    $('#bs-container').append(html);
    makeEditor($(`[name="pilihan_${bsCount}"]`), 100);


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
    <div class="row mb-2 match-row position-relative border-box">
        <div class="col-md-5">
            <textarea class="form-control" name="pasangan_soal[]" placeholder="Soal / Kiri"></textarea>
        </div>
        <div class="col-md-5">
            <textarea class="form-control" name="pasangan_jawaban[]" placeholder="Jawaban / Kanan"></textarea>
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


</script>
<script>
document.addEventListener('DOMContentLoaded', function(){

    const tipeSelect   = document.getElementById('tipe_soal');
    const boxPertanyaan = document.getElementById('box-pertanyaan');
    const boxSimpan     = document.getElementById('box-simpan');

    function toggleAwal(){
        if(!tipeSelect.value){
            boxPertanyaan.classList.add('d-none');
            boxSimpan.classList.add('d-none');
        }else{
            boxPertanyaan.classList.remove('d-none');
            boxSimpan.classList.remove('d-none');
        }
    }

    // pertama kali load
    toggleAwal();

    // saat pilih tipe
    tipeSelect.addEventListener('change', function(){
        toggleAwal();

        // penting: refresh summernote setelah muncul
        setTimeout(function(){
            $('#pertanyaan').summernote('reset');
        }, 200);
    });

});
</script>

</body>
</html>