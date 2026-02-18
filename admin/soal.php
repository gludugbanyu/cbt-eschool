<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';
$id_admin = $_SESSION['admin_id'];
$role     = $_SESSION['role'];

$where = "";
if ($role != 'admin') {
    $where = "WHERE FIND_IN_SET('$id_admin', s.id_pembuat)";
}

$query = "
    SELECT 
        s.id_soal, s.kode_soal, s.nama_soal, s.mapel, s.kelas, 
        s.tampilan_soal, s.status, s.tanggal, s.waktu_ujian, 
        s.token, s.jumlah_opsi,
        s.id_pembuat,
        s.tampil_tombol_selesai,
        COUNT(b.id_soal) AS jumlah_butir
    FROM soal s
    LEFT JOIN butir_soal b ON s.kode_soal = b.kode_soal
    $where
    GROUP BY 
        s.id_soal, s.kode_soal, s.nama_soal, s.mapel, s.kelas, 
        s.tampilan_soal, s.status, s.tanggal, s.waktu_ujian, 
        s.token, s.jumlah_opsi,
        s.id_pembuat
";
$result = mysqli_query($koneksi, $query);

// Check if the query was successful
if (!$result) {
    // If there's an error with the query, display the error message
    die('Error with the query: ' . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Soal</title>
    <?php include '../inc/css.php'; ?>
    <style>
    .table-wrapper {
        overflow-x: auto;
        /* Enable horizontal scrolling */
        -webkit-overflow-scrolling: touch;
        /* Smooth scrolling for mobile */
    }

    table th,
    table td {
        text-align: left !important;
    }

    .row-alarm {
        border-left: 8px solid #dc3545 !important;
        background-color: #efefef !important;
    }

    #soalTable td:last-child .btn {
        margin-right: 4px;
        margin-bottom: 4px;
    }
    </style>
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
                                    <h5 class="card-title mb-0">Daftar Soal</h5>
                                </div>
                                <div class="card-body table-wrapper">
                                    <a href="tambah_soal.php" class="btn btn-primary mb-3"><i class="fas fa-plus"></i>
                                        Tambah Soal Baru</a>
                                    <table id="soalTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode Soal</th>
                                                <th>Pembuat / korektor</th>
                                                <th>Mapel</th>
                                                <th>Kls</th>
                                                <th>Jmlh</th>
                                                <th>Opsi</th>
                                                <th>Durasi (menit)</th>
                                                <th>Tombol Selesai</th>
                                                <th>Tgl Ujian</th>
                                                <th>Tampilan</th>
                                                <th>Token</th>
                                                <th>On/Off</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                                            <?php $alarm = ($row['status'] == 'Aktif'); ?>
                                            <tr class="<?= $alarm ? 'row-alarm' : '' ?>">
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo $row['kode_soal']; ?><br>

                                                    <!-- Status -->
                                                    <?php if ($row['status'] == 'Aktif') { ?>
                                                    <span class="badge bg-success me-1">Aktif</span>
                                                    <?php } else { ?>
                                                    <span class="badge bg-danger me-1">Nonaktif</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php
                                                $ids = $row['id_pembuat'];
                                                $users = mysqli_query($koneksi, "SELECT id, nama_admin FROM admins WHERE FIND_IN_SET(id, '$ids')");
                                                $nama_list = [];
                                                while($u = mysqli_fetch_assoc($users)){
                                                    $nama_list[] = $u['nama_admin'];
                                                }
                                                ?>
                                                    <?php foreach($nama_list as $nama): ?>
                                                    <span class="badge bg-dark me-1 mb-1">
                                                        <i class="fa fa-user"></i> <?= $nama; ?>
                                                    </span>
                                                    <?php endforeach; ?>
                                                    <?php if($_SESSION['role']=='admin'): ?>
                                                    <br>
                                                    <button class="btn btn-sm btn-outline-primary mt-1 btn-akses"
                                                        data-id="<?= $row['id_soal']; ?>" data-current="<?= $ids; ?>">
                                                        <i class="fa fa-users"></i> Atur Akses
                                                    </button>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $row['mapel']; ?></td>
                                                <td style="min-width:180px">
                                                    <?php
                                                $kelas_list = array_map('trim', explode(',', $row['kelas']));

                                                $romawi = [
                                                    'I'=>1,'II'=>2,'III'=>3,'IV'=>4,'V'=>5,'VI'=>6,
                                                    'VII'=>7,'VIII'=>8,'IX'=>9,'X'=>10,'XI'=>11,'XII'=>12
                                                ];

                                                $getAngkaKelas = function($text) use ($romawi){
                                                    preg_match('/^([A-ZIVXLC0-9]+)([A-Z]+)/', $text, $m);
                                                    $kelas = $m[1] ?? '';

                                                    if (is_numeric($kelas)) return (int)$kelas;
                                                    return $romawi[$kelas] ?? 0;
                                                };

                                                // URUTKAN
                                                usort($kelas_list, function($a, $b) use ($getAngkaKelas){
                                                    $na = $getAngkaKelas($a);
                                                    $nb = $getAngkaKelas($b);

                                                    if ($na == $nb) return strcmp($a, $b);
                                                    return $na - $nb;
                                                });

                                                // WARNA SIKLUS 3
                                                foreach($kelas_list as $kr){
                                                    $angka = $getAngkaKelas($kr);
                                                    $mod = $angka % 3;

                                                    if ($mod == 1) {
                                                        $warna = 'bg-success';   // Grup A → 1,4,7,10
                                                    } elseif ($mod == 2) {
                                                        $warna = 'bg-primary';   // Grup B → 2,5,8,11
                                                    } else {
                                                        $warna = 'bg-danger';    // Grup C → 3,6,9,12
                                                    }

                                                    echo '<span class="badge '.$warna.' me-1 mb-1">'.$kr.'</span>';
                                                }
                                                ?>
                                                    <?php if($_SESSION['role']=='admin'): ?>
                                                    <br>
                                                    <?php
                                                $list_kelas = array_filter($kelas_list); // hasil setelah di-trim & diurut
                                                $total_kelas = count($list_kelas);
                                                ?>

                                                    <?php if($_SESSION['role']=='admin'): ?>
                                                    <br>
                                                    <button class="btn btn-sm btn-outline-secondary mt-1 btn-kelas"
                                                        data-id="<?= $row['id_soal']; ?>"
                                                        data-current="<?= htmlspecialchars($row['kelas']); ?>">
                                                        <i class="fa fa-layer-group"></i>
                                                        Atur Kelas (<?= $total_kelas ?>)
                                                    </button>
                                                    <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $row['jumlah_butir']; ?></td>
                                                <td>
                                                    <?php if ($row['jumlah_opsi'] == 5): ?>
                                                    <span class="badge bg-info">A-E</span>
                                                    <?php else: ?>
                                                    <span class="badge bg-secondary">A-D</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><i class="fa fa-clock" aria-hidden="true"></i>
                                                    <?php echo $row['waktu_ujian']; ?></td>
                                                <td>
                                                    <?php
                                                    $ts = (int)$row['tampil_tombol_selesai'];

                                                    if ($ts === 0) {
                                                        echo '<span class="badge bg-success">
                                                                Selalu Muncul
                                                            </span>';
                                                    } else {
                                                        echo '<span class="badge bg-warning text-white">
                                                                ' . $ts . ' menit terakhir
                                                            </span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><span class="badge bg-primary">
                                                        <i class="fa fa-calendar-alt me-1"></i>
                                                        <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                                    </span></td>
                                                <td><?php echo $row['tampilan_soal']; ?></td>
                                                <td style="white-space:nowrap;">
                                                    <!-- Token -->
                                                    <?= $row['token']; ?>
                                                    <!-- Generate Token -->
                                                    <?php if ($row['status'] == 'Aktif') { ?>
                                                    <a href="generate_token.php?id_soal=<?= $row['id_soal']; ?>"
                                                        class="btn btn-outline-secondary btn-sm p-0 rounded-circle d-inline-flex align-items-center justify-content-center"
                                                        style="width:26px;height:26px;" title="Generate Token">
                                                        <i class="fa fa-history" style="font-size:10px;"></i>
                                                    </a>
                                                    <?php } ?>

                                                </td>
                                                <td style="white-space:nowrap;">
                                                    <?php if ($row['status'] == 'Aktif') { ?>
                                                    <a href="ubah_status_soal.php?id_soal=<?= $row['id_soal']; ?>&aksi=nonaktif"
                                                        class="text-decoration-none">

                                                        <span
                                                            class="btn btn-sm p-0 rounded-circle d-inline-flex align-items-center justify-content-center btn-danger align-middle"
                                                            style="width:28px;height:28px;">
                                                            <i class="fa fa-toggle-off" style="font-size:13px;"></i>
                                                        </span>

                                                        <span class="ms-1 text-danger align-middle"
                                                            style="font-size:12px;">
                                                            Nonaktifkan
                                                        </span>
                                                    </a>
                                                    <?php } else { ?>
                                                    <a href="ubah_status_soal.php?id_soal=<?= $row['id_soal']; ?>&aksi=aktif"
                                                        class="text-decoration-none">

                                                        <span
                                                            class="btn btn-sm p-0 rounded-circle d-inline-flex align-items-center justify-content-center btn-success align-middle"
                                                            style="width:28px;height:28px;">
                                                            <i class="fa fa-toggle-on" style="font-size:13px;"></i>
                                                        </span>

                                                        <span class="ms-1 text-success align-middle"
                                                            style="font-size:12px;">
                                                            Aktifkan
                                                        </span>
                                                    </a>
                                                    <?php } ?>
                                                </td>
                                                <td style="white-space:nowrap;">
                                                    <!-- Preview -->
                                                    <a href="preview_soal.php?kode_soal=<?= $row['kode_soal']; ?>&opsi=<?= $row['jumlah_opsi']; ?>"
                                                        class="btn btn-light btn-sm rounded-circle me-1"
                                                        style="width:30px;height:30px;border:1px solid #ccc;"
                                                        title="Preview Soal">
                                                        <i class="fa fa-eye"></i>
                                                    </a>

                                                    <!-- Edit -->
                                                    <a href="<?= $alarm ? '#' : 'edit_soal.php?id_soal='.$row['id_soal']; ?>"
                                                        class="btn btn-primary btn-sm rounded-circle me-1 <?= $alarm ? 'disabled opacity-50' : '' ?>"
                                                        style="width:30px;height:30px;" title="Edit Soal">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <!-- Duplikat -->
                                                    <a href="#"
                                                        class="btn btn-info btn-sm rounded-circle me-1 btn-duplicate <?= $alarm ? 'disabled opacity-50' : '' ?>"
                                                        data-kode="<?= $row['kode_soal']; ?>"
                                                        style="width:30px;height:30px;" title="Duplikat Soal">
                                                        <i class="fa fa-copy"></i>
                                                    </a>

                                                    <!-- Input Butir -->
                                                    <a href="<?= $alarm ? '#' : 'daftar_butir_soal.php?kode_soal='.$row['kode_soal']; ?>"
                                                        class="btn btn-success btn-sm rounded-circle me-1 <?= $alarm ? 'disabled opacity-50' : '' ?>"
                                                        style="width:30px;height:30px;" title="Input Butir Soal">
                                                        <i class="fa fa-plus"></i>
                                                    </a>

                                                    <!-- Hapus -->
                                                    <button
                                                        class="btn btn-danger btn-sm rounded-circle btn-hapus <?= $alarm ? 'disabled opacity-50' : '' ?>"
                                                        data-kode="<?= $row['kode_soal']; ?>"
                                                        style="width:30px;height:30px;" title="Hapus Soal">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php } ?>
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
    <script>
    // Tambahkan di bagian script yang sudah ada
    document.querySelectorAll('.btn-duplicate').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const oldKode = this.getAttribute('data-kode');

            Swal.fire({
                title: 'Duplikasi Soal',
                input: 'text',
                inputLabel: 'Masukkan Kode Soal Baru',
                inputPlaceholder: 'Kode unik untuk soal duplikat',
                showCancelButton: true,
                confirmButtonText: 'Duplikat',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Kode soal baru harus diisi!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const newKode = result.value;

                    // Kirim permintaan AJAX
                    fetch('duplicate_soal.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `old_kode=${encodeURIComponent(oldKode)}&new_kode=${encodeURIComponent(newKode)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error',
                                'Terjadi kesalahan saat memproses permintaan.', 'error');
                        });
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTables
        $(document).ready(function() {
            $('#soalTable').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true
            });
        });
        document.querySelectorAll('.btn-hapus').forEach(function(button) {
            button.addEventListener('click', function() {
                const kodeSoal = this.getAttribute('data-kode');

                fetch('cek_nilai_soal.php?kode_soal=' + kodeSoal)
                    .then(res => res.json())
                    .then(data => {

                        let warningText = '';

                        if (data.jumlah > 0) {
                            warningText = `
                ⚠️ <b>PERINGATAN KERAS</b><br><br>
                Soal ini sudah dikerjakan oleh <b>${data.jumlah} siswa</b>.<br>
                Jika dihapus:<br>
                • Semua nilai siswa akan ikut terhapus<br>
                • Analisa soal akan hilang<br><br>
                Ketik <b>HAPUS SEMUA</b> untuk melanjutkan.
                `;
                        } else {
                            warningText = `
                Soal ini belum pernah dikerjakan siswa.<br>
                Ketik <b>HAPUS</b> untuk menghapus.
                `;
                        }

                        Swal.fire({
                            title: 'Konfirmasi Hapus Soal',
                            html: warningText,
                            input: 'text',
                            inputPlaceholder: 'Ketik sesuai perintah di atas',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Hapus',
                            confirmButtonColor: '#d33',
                            preConfirm: (val) => {
                                if (data.jumlah > 0 && val !== 'HAPUS SEMUA') {
                                    Swal.showValidationMessage(
                                        'Harus ketik: HAPUS SEMUA');
                                }
                                if (data.jumlah == 0 && val !== 'HAPUS') {
                                    Swal.showValidationMessage(
                                        'Harus ketik: HAPUS');
                                }
                            }
                        }).then((r) => {
                            if (r.isConfirmed) {
                                window.location.href = 'hapus_soal.php?kode_soal=' +
                                    kodeSoal;
                            }
                        });

                    });
            });
        });



    });

    document.querySelectorAll('.btn-akses').forEach(btn => {
        btn.addEventListener('click', function() {

            const idSoal = this.dataset.id;
            const current = this.dataset.current;

            // Modal langsung muncul dulu
            Swal.fire({
                title: 'Atur Akses User',
                html: '<div style="text-align:center;padding:20px;">Memuat data...</div>',
                width: 600,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                didOpen: () => {

                    fetch('get_admin_list.php?current=' + current)
                        .then(res => res.text())
                        .then(html => {

                            // Ganti isi modal setelah data datang
                            Swal.update({
                                html: html
                            });

                            const popup = Swal.getHtmlContainer();

                            const search = popup.querySelector('#searchAdmin');
                            if (search) {
                                search.addEventListener('keyup', function() {
                                    let val = this.value.toLowerCase();
                                    popup.querySelectorAll('.admin-item')
                                        .forEach(el => {
                                            el.style.display =
                                                el.innerText.toLowerCase()
                                                .includes(val) ? '' :
                                                'none';
                                        });
                                });
                            }

                        });
                },
                preConfirm: () => {
                    const popup = Swal.getHtmlContainer();
                    let selected = [];
                    popup.querySelectorAll('.chk-admin:checked')
                        .forEach(c => selected.push(c.value));
                    return selected.join(',');
                }
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('update_akses_soal.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `id_soal=${idSoal}&ids=${result.value}`
                        })
                        .then(res => res.json())
                        .then(r => {
                            if (r.status == 'ok') {
                                Swal.fire('Berhasil', 'Akses diperbarui', 'success')
                                    .then(() => location.reload());
                            }
                        });
                }
            });

        });
    });

    document.querySelectorAll('.btn-kelas').forEach(btn => {
        btn.addEventListener('click', function() {

            const idSoal = this.dataset.id;
            const current = this.dataset.current;

            // Modal langsung muncul dulu
            Swal.fire({
                title: 'Atur Kelas & Rombel',
                html: '<div style="text-align:center;padding:20px;">Memuat data...</div>',
                width: 700,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                didOpen: () => {

                    fetch('get_kelas_list.php?current=' + encodeURIComponent(current))
                        .then(res => res.text())
                        .then(html => {

                            // Ganti isi modal setelah data datang
                            Swal.update({
                                html: html
                            });

                            const popup = Swal.getHtmlContainer();

                            // SEARCH (aman)
                            const search = popup.querySelector('#searchKelas');
                            if (search) {
                                search.addEventListener('keyup', function() {
                                    let val = this.value.toLowerCase();
                                    popup.querySelectorAll('.kelas-item')
                                        .forEach(el => {
                                            el.style.display =
                                                el.innerText.toLowerCase()
                                                .includes(val) ? '' :
                                                'none';
                                        });
                                });
                            }

                            // SELECT ALL
                            const selectAll = popup.querySelector('#selectAll');
                            if (selectAll) {
                                selectAll.addEventListener('click', () => {
                                    popup.querySelectorAll('.chk-kelas')
                                        .forEach(c => c.checked = true);
                                });
                            }

                            // UNSELECT ALL
                            const unselectAll = popup.querySelector('#unselectAll');
                            if (unselectAll) {
                                unselectAll.addEventListener('click', () => {
                                    popup.querySelectorAll('.chk-kelas')
                                        .forEach(c => c.checked = false);
                                });
                            }

                        })
                        .catch(() => {
                            Swal.update({
                                html: '<div style="color:red;text-align:center;padding:20px;">Gagal memuat data</div>'
                            });
                        });

                },
                preConfirm: () => {

                    const popup = Swal.getHtmlContainer();
                    let selected = [];

                    popup.querySelectorAll('.chk-kelas:checked')
                        .forEach(c => selected.push(c.value));

                    if (selected.length === 0) {
                        Swal.showValidationMessage('Pilih minimal 1 kelas / rombel');
                        return false;
                    }

                    return selected.join(',');
                }
            }).then(result => {
                if (result.isConfirmed) {

                    const fd = new FormData();
                    fd.append('id_soal', idSoal);
                    fd.append('kelas', result.value);

                    fetch('update_kelas_soal.php', {
                            method: 'POST',
                            body: fd
                        })
                        .then(res => res.json())
                        .then(r => {
                            if (r.status == 'ok') {
                                Swal.fire('Berhasil', 'Kelas diperbarui', 'success')
                                    .then(() => location.reload());
                            }
                        });
                }
            });

        });
    });
    </script>
    <?php if (isset($_SESSION['success'])): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '<?php echo addslashes($_SESSION['success']); ?>',
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
        text: '<?php echo addslashes($_SESSION['error']); ?>',
        showConfirmButton: false,
        timer: 2000
    });
    </script>
    <?php unset($_SESSION['error']); endif; ?>
    <?php if (isset($_SESSION['success_message'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Berhasil!',
            text: '<?php echo $_SESSION['success_message']; ?>',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    });
    </script>
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if(isset($_GET['akses'])): ?>
    <script src="../assets/swal/sweetalert2.all.min.js"></script>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Akses Ditolak!',
        text: 'Halaman tersebut hanya bisa diakses oleh Pemilik.',
        confirmButtonColor: '#d33'
    });
    </script>
    <?php endif; ?>
    <?php if (isset($_SESSION['warning_message'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Bisa Diedit!',
            text: '<?php echo $_SESSION['warning_message']; ?>',
            showConfirmButton: false,
            timer: 2000
        });
    });
    </script>
    <?php unset($_SESSION['warning_message']); ?>
    <?php endif; ?>

</body>

</html>