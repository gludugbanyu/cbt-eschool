<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Login Siswa</title>
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
                                    <h5 class="card-title mb-0">Pilih Siswa untuk Reset</h5>
                                </div>
                                <div class="card-body">
                                <?php
$qset = mysqli_query($koneksi, "SELECT izinkan_lanjut_ujian FROM pengaturan WHERE id=1");
$dset = mysqli_fetch_assoc($qset);
$izinkan_lanjut = $dset['izinkan_lanjut_ujian'] ?? 'tidak';
?>

<?php if ($izinkan_lanjut === 'ya'): ?>
<div class="alert alert-danger mb-3">
    <i class="fas fa-info-circle"></i>
    <b>Fitur Lanjut Ujian Aktif</b><br>
    Siswa yang keluar dari ujian dapat masuk kembali dengan mengisi token tanpa perlu reset login dari admin.
    </div>
<?php else: ?>
<div class="alert alert-info mb-3">
    <i class="fas fa-exclamation-triangle"></i>
    <b>Fitur Lanjut Ujian Tidak Aktif</b><br>
    Jika siswa keluar dari ujian, mereka tidak dapat masuk kembali sebelum admin melakukan reset login di halaman ini.
</div>
<?php endif; ?>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <input type="text" id="customSearch" class="form-control form-control-lg"
                                                placeholder="🔍 Cari nama, kelas, rombel, atau kode soal...">
                                        </div>
                                        <div class="col-md-3">
                                            <?php
                                                // Ambil semua kelas unik dari database
                                                $kelasResult = mysqli_query($koneksi, "SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC");
                                                ?>
                                            <select id="filterKelas" class="form-select form-select-lg">
                                                <option value="">📚 Semua Kelas</option>
                                                <?php while ($k = mysqli_fetch_assoc($kelasResult)): ?>
                                                <option value="<?= htmlspecialchars($k['kelas']) ?>">
                                                    <?= htmlspecialchars($k['kelas']) ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="filterStatus" class="form-select form-select-lg">
                                                <option value="">🎯 Semua Status</option>
                                                <option value="Aktif">Aktif</option>
                                                <option value="Non-Aktif">Non-Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button id="resetTerpilih" class="btn btn-danger mb-3" disabled>
    <i class="fas fa-undo"></i> Reset Login Terpilih
</button>
                                    <div class="table-wrapper">
                                        <table id="tabelReset" class="table table-striped table-bordered">
                                        <thead>
    <tr>
        <th width="40">
            <input type="checkbox" id="checkAll">
        </th>
        <th>No</th>
        <th>Nama Siswa</th>
        <th>Kelas</th>
        <th>Rombel</th>
        <th>Kode Soal</th>
        <th>Waktu Mulai Ujian</th>
        <th>Status Ujian</th>
        <th>Aksi</th>
    </tr>
</thead>

                                        </table>
                                    </div>
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
// Check all
$('#checkAll').on('change', function() {
    $('.row-check').prop('checked', this.checked);
    toggleResetButton();
});

// Check per row
$(document).on('change', '.row-check', function() {
    toggleResetButton();
});

// Aktifkan tombol jika ada yang dicentang
function toggleResetButton() {
    let ada = $('.row-check:checked').length > 0;
    $('#resetTerpilih').prop('disabled', !ada);
}


        var table = $('#tabelReset').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'reset_data.php',
                data: function(d) {
                    d.search.value = $('#customSearch').val();
                    d.filterKelas = $('#filterKelas').val();
                    d.filterStatus = $('#filterStatus').val();
                }
            },
            columns: [
{
    data: null,
    orderable: false,
    render: function(data, type, row) {
        if (row.status_ujian === "Aktif") {
            return `
                <input type="checkbox" 
                       class="row-check" 
                       data-id="${row.id_siswa}" 
                       data-kode="${row.kode_soal}">
            `;
        }
        return '';
    }
},
{
    data: null,
    render: function(data, type, row, meta) {
        return meta.row + meta.settings._iDisplayStart + 1;
    }
},
{ data: 'nama_siswa' },
{ data: 'kelas' },
{ data: 'rombel' },
{ data: 'kode_soal' },
{ data: 'waktu_dijawab' },
{
    data: 'status_ujian',
    render: function(data) {
        return data === "Aktif"
            ? '<span class="badge bg-success">Aktif</span>'
            : '<span class="badge bg-danger">Non-Aktif</span>';
    }
},
{
    data: null,
    orderable: false,
    render: function(data, type, row) {
        if (row.status_ujian === "Aktif") {
            return `
                <button 
                    class="btn btn-outline-danger btn-sm reset-btn" 
                    data-id="${row.id_siswa}" 
                    data-nama="${row.nama_siswa}"
                    data-kode-soal="${row.kode_soal}">
                    <i class="fas fa-undo"></i> Reset
                </button>
            `;
        } else {
            return '<button class="btn btn-outline-secondary btn-sm" disabled>Reset</button>';
        }
    }
}
],

            initComplete: function() {
                $('#tabelReset_filter').hide(); // sembunyikan search default
            }
        });

        $('#customSearch, #filterKelas, #filterStatus').on('keyup change', function() {
            table.draw();
        });

        $(document).on('click', '.reset-btn', function(e) {
            e.preventDefault();
            var id_siswa = $(this).data('id');
            var nama = $(this).data('nama');
            var kode_soal = $(this).data('kode-soal');

            Swal.fire({
                title: 'Reset Login?',
                text: 'Yakin ingin reset login untuk ' + nama + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': 'reset_login_aksi.php'
                    });
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'id_siswa',
                        'value': id_siswa
                    }));
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'kode_soal',
                        'value': kode_soal
                    }));
                    $('body').append(form);
                    form.submit();
                }
            });
        });

    });

    // RESET TERPILIH
$('#resetTerpilih').on('click', function(e) {
    e.preventDefault();

    let ids = [];
    let kodes = [];

    $('.row-check:checked').each(function() {
        ids.push($(this).data('id'));
        kodes.push($(this).data('kode'));
    });

    if (ids.length === 0) return;

    Swal.fire({
        title: 'Reset Login Terpilih?',
        text: ids.length + ' siswa akan direset.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, reset semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            let form = $('<form>', {
                method: 'POST',
                action: 'reset_login_aksi.php'
            });

            for (let i = 0; i < ids.length; i++) {
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'id_siswa[]',
                    value: ids[i]
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'kode_soal[]',
                    value: kodes[i]
                }));
            }

            $('body').append(form);
            form.submit();
        }
    });
});

    </script>

    <?php if (isset($_SESSION['success'])): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Sukses',
        text: '<?php echo $_SESSION['success']; ?>',
        showConfirmButton: false,
        timer: 2000
    });
    </script>
    <?php unset($_SESSION['success']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '<?php echo $_SESSION['error']; ?>',
        showConfirmButton: false,
        timer: 2000
    });
    </script>
    <?php unset($_SESSION['error']); endif; ?>

</body>

</html>