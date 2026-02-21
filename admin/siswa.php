<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();
include '../inc/dataadmin.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Siswa</title>
    <?php include '../inc/css.php'; ?>
    <style>
    .table-wrapper {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }

    table th,
    table td {
        text-align: left !important;
    }

    #deleteSelected:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    #selectedCount {
        font-size: 11px;
        padding: 4px 6px;
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
                                    <h5 class="card-title mb-0">Daftar Siswa</h5>
                                </div>
                                <div class="card-body">

                                    <div class="d-flex justify-content align-items-center mb-3 flex-wrap gap-2">

                                        <div style="width:180px;">
                                            <select id="filterKelas" class="form-select">
                                                <option value="">Semua Kelas</option>
                                                <?php
            $kelas = mysqli_query($koneksi,"
                SELECT DISTINCT CONCAT(kelas,rombel) as kls 
                FROM siswa 
                ORDER BY kelas,rombel
            ");
            while($k = mysqli_fetch_assoc($kelas)){
                echo '<option value="'.$k['kls'].'">'.$k['kls'].'</option>';
            }
            ?>
                                            </select>
                                        </div>

                                        <div class="btn-group">
                                            <a href="tambah_siswa.php" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Tambah Siswa
                                            </a>

                                            <button id="deleteSelected" class="btn btn-danger" disabled>
                                                <i class="fa fa-trash"></i> Hapus Terpilih
                                                <span id="selectedCount" class="badge bg-light text-dark ms-2">0</span>
                                            </button>

                                            <a href="import_siswa.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-file-import"></i> Import Siswa
                                            </a>

                                            <button id="exportExcel" class="btn btn-outline-secondary">
                                                <i class="fas fa-file-excel"></i> Export Excel
                                            </button>
                                        </div>

                                    </div>

                                    <div class=" table-wrapper">
                                        <table id="siswaTable" class="table table-striped nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="width:30px;">
                                                        <input type="checkbox" id="selectAll">
                                                    </th>
                                                    <th style="display:none;">ID</th>
                                                    <th>No</th>
                                                    <th>Nama Siswa</th>
                                                    <th>Kelas</th>
                                                    <th>Username</th>
                                                    <th>Password</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                      $no = 1;
                      $query = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY id_siswa DESC");
                      while ($data = mysqli_fetch_assoc($query)) {
                        include '../inc/encrypt.php';
                        $encoded = $data['password'];
                        $decoded = base64_decode($encoded);
                        $iv_length = openssl_cipher_iv_length($method);
                        $iv2 = substr($decoded, 0, $iv_length);
                        $encrypted_data = substr($decoded, $iv_length);
                        $decrypted = openssl_decrypt($encrypted_data, $method, $rahasia, 0, $iv2);

                        echo "<tr>";
                        echo "<td>
                                <input type='checkbox' class='row-check' value='{$data['id_siswa']}'>
                              </td>";
                        echo "<td style='display:none;'>{$data['id_siswa']}</td>";
                        echo "<td class='row-number'></td>";
                        echo "<td>{$data['nama_siswa']}</td>";
                        echo "<td>{$data['kelas']}{$data['rombel']}</td>";
                        echo "<td>{$data['username']}</td>";
                        echo "<td>{$decrypted}</td>";
                        echo '<td>
                                <a href="edit_siswa.php?id=' . $data['id_siswa'] . '" class="btn btn-sm btn-success">
                                  <i class="fas fa-edit"></i> Edit Siswa
                                </a>
                                <form method="POST" action="hapus_siswa.php" class="d-inline delete-form" style="display:inline;">
                                  <input type="hidden" name="id" value="' . $data['id_siswa'] . '">
                                  <button type="submit" class="btn btn-danger btn-sm btn-delete">
                                    <i class="fa fa-close"></i> Hapus
                                  </button>
                                </form>
                              </td>';
                        echo "</tr>";
                        $no++;
                      }
                      ?>
                                            </tbody>
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
    <div class="modal fade" id="progressModal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">

                <h6 class="mb-3">Menghapus Data Siswa...</h6>

                <div class="progress mb-2" style="height:20px;">
                    <div id="deleteProgressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                        role="progressbar" style="width:0%">0%
                    </div>
                </div>

                <small id="progressText">Memulai...</small>

            </div>
        </div>
    </div>
    <?php include '../inc/js.php'; ?>
    <script src="../assets/datatables/jszip.min.js"></script>
    <script src="../assets/datatables/buttons.html5.min.js"></script>
    <script>
    const table = $('#siswaTable').DataTable({
      
        dom:
            // Baris 1: Export buttons + Search box
            '<"row mb-3"' +
            '<"col-md-6 d-flex align-items-center">' +
            '<"col-md-6 d-flex justify-content-end"f>' +
            '>' +
            // Baris 2: Length dropdown + pagination
            '<"row mb-3"' +
            '<"col-md-6 d-flex align-items-center"l>' +
            '<"col-md-6 d-flex justify-content-end"p>' +
            '>' +
            // Table
            't' +
            // Baris 3: Info + pagination bawah
            '<"row mt-3"' +
            '<"col-md-6 d-flex align-items-center"i>' +
            '<"col-md-6 d-flex justify-content-end"p>' +
            '>',
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: true,
        responsive: true,
        order: [
            [1, 'desc']
        ],
        columnDefs: [{
                targets: 1,
                visible: false
            },
            {
                orderable: false,
                targets: 0
            }

        ],
        buttons: [{
            extend: 'excelHtml5',
            title: 'Daftar Siswa',
            exportOptions: {
                columns: [2, 3, 4, 5, 6]
            }
        }]
    });

    table.on('draw.dt', function () {

    let PageInfo = table.page.info();

    table.column(2, { page: 'current' }).nodes().each(function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
    });

}).draw();
    
    let selectedIds = new Set();
    $('#filterKelas').on('change', function() {

        let val = $(this).val();

        table
            .column(4) // kolom kelas
            .search(val)
            .draw();

    });
    // Trigger export dari tombol luar
    $('#exportExcel').on('click', function() {
        table.button('.buttons-excel').trigger();
    });

    // Konfirmasi Hapus
    $(document).on('submit', '.delete-form', function(e) {

        e.preventDefault();
        e.stopImmediatePropagation();

        let form = this;
        let id = $(this).find('input[name="id"]').val();

        $(form).find('button').prop('disabled', true);

        $.post('cek_relasi_satu.php', {
            id: id
        }, function(res) {

            let nama = res.nama;
            let status = res.status;

            let warningText = 'Siswa "' + nama + '" akan dihapus!';

            if (status === 'ada') {
                warningText =
                    nama + ' memiliki data ujian.\nNilainya akan ikut terhapus!';
            }

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: warningText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed) {
                    form.submit();
                } else {
                    $(form).find('button').prop('disabled', false);
                }

            });

        }, 'json');

    });

    function updateSelectedCount() {

        let count = selectedIds.size;

        $('#selectedCount').text(count);

        if (count > 0) {
            $('#deleteSelected').prop('disabled', false);
        } else {
            $('#deleteSelected').prop('disabled', true);
        }

    }


    $('#selectAll').on('click', function() {

        let rows = table.rows({
            search: 'applied'
        }).nodes();

        $('input.row-check', rows).each(function() {

            let id = $(this).val();

            if ($('#selectAll').is(':checked')) {
                $(this).prop('checked', true);
                selectedIds.add(id);
            } else {
                $(this).prop('checked', false);
                selectedIds.delete(id);
            }

        });

        updateSelectedCount();

    });


    $('#siswaTable').on('change', '.row-check', function() {

        let id = $(this).val();

        if ($(this).is(':checked')) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }

        // 🔥 sync header checkbox
        let total = table.rows({
            search: 'applied'
        }).nodes().length;
        let checked = 0;

        table.rows({
            search: 'applied'
        }).nodes().each(function(row) {
            if ($(row).find('.row-check').is(':checked')) {
                checked++;
            }
        });

        $('#selectAll').prop('checked', total === checked);

        updateSelectedCount();

    });

    table.on('draw', function() {

        table.$('.row-check').each(function() {

            let id = $(this).val();

            if (selectedIds.has(id)) {
                $(this).prop('checked', true);
            }

        });

    });


    $('#deleteSelected').on('click', function() {

        let ids = Array.from(selectedIds);

        if (ids.length == 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih siswa dulu!'
            });
            return;
        }

        // 🔥 CEK RELASI DULU
        $.post('cek_relasi_siswa.php', {
            ids: ids
        }, function(relasiCount) {

            relasiCount = parseInt(relasiCount);

            let warningText = ids.length + " siswa akan dihapus!";

            if (relasiCount > 0) {

                warningText =
                    relasiCount + " dari " + ids.length +
                    " siswa memiliki data ujian.\nNilainya akan ikut terhapus!";

            } else {

                warningText =
                    ids.length + " siswa akan dihapus!";
            }

            Swal.fire({
                title: 'Hapus siswa?',
                text: warningText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33'
            }).then((result) => {

                if (result.isConfirmed) {

                    let total = ids.length;
                    let done = 0;

                    let modal = new bootstrap.Modal(document.getElementById('progressModal'));
                    modal.show();

                    function deleteNext() {

                        if (ids.length === 0) {

                            modal.hide();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'siswa berhasil dihapus'
                            }).then(() => {
                                location.reload();
                            });

                            return;
                        }

                        let id = ids.shift();

                        $.post('hapus_siswa_multi.php', {
                            id: id
                        }, function() {

                            done++;

                            let percent = Math.round((done / total) * 100);

                            $('#deleteProgressBar')
                                .css('width', percent + '%')
                                .text(percent + '%');

                            $('#progressText')
                                .text(done + ' dari ' + total + ' siswa dihapus');

                            deleteNext();

                        });

                    }

                    deleteNext();

                }

            });

        });

    });
    </script>
    <?php if (isset($_SESSION['success'])): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= $_SESSION['success']; ?>',
        confirmButtonColor: '#28a745'
    });
    </script>
    <?php unset($_SESSION['success']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?= $_SESSION['error']; ?>',
        confirmButtonColor: '#dc3545'
    });
    </script>
    <?php unset($_SESSION['error']); endif; ?>
    <?php if (isset($_SESSION['alert'])): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?= $_SESSION['error']; ?>',
        confirmButtonColor: '#dc3545'
    });
    </script>
    <?php unset($_SESSION['error']); endif; ?>
</body>

</html>