<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: daftar_butir_soal.php?kode_soal=' . ($_GET['kode_soal'] ?? ''));
    exit();
}

$kode_soal_post = trim($_POST['kode_soal']);
$file_tmp = $_FILES['file_docx']['tmp_name'];
only_pemilik_soal_by_kode($kode_soal_post);

// --- 1. AMBIL DATA JUMLAH OPSI DARI DATABASE ---
$query_db_soal = $koneksi->query("SELECT jumlah_opsi FROM soal WHERE kode_soal = '$kode_soal_post'");
$data_db_soal = $query_db_soal->fetch_assoc();
$jumlah_opsi_allowed = (int)$data_db_soal['jumlah_opsi'];

// --- FUNGSI EKSTRAKSI DOCX (TEKS + BASE64 GAMBAR) ---
function extract_docx_to_base64($file_path) {
    $zip = new ZipArchive;
    $content = '';
    if ($zip->open($file_path) === TRUE) {
        $rels_xml = $zip->getFromName('word/_rels/document.xml.rels');
        $image_map = [];
        if ($rels_xml) {
            $rels_obj = simplexml_load_string($rels_xml);
            foreach ($rels_obj->Relationship as $rel) {
                if (strpos($rel['Type'], 'image') !== false) {
                    $image_map[(string)$rel['Id']] = basename((string)$rel['Target']);
                }
            }
        }

        $doc_xml = $zip->getFromName('word/document.xml');
        if ($doc_xml) {
            $dom = new DOMDocument();
            @$dom->loadXML($doc_xml);
            $blips = $dom->getElementsByTagNameNS('http://schemas.openxmlformats.org/drawingml/2006/main', 'blip');
            
            for ($i = $blips->length - 1; $i >= 0; $i--) {
                $blip = $blips->item($i);
                $embedId = $blip->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'embed');
                
                if (isset($image_map[$embedId])) {
                    $image_name = $image_map[$embedId];
                    $image_data = $zip->getFromName('word/media/' . $image_name);
                    $ext = pathinfo($image_name, PATHINFO_EXTENSION);
                    $base64 = 'data:image/' . $ext . ';base64,' . base64_encode($image_data);
                    
                    $text_node = $dom->createTextNode("\n[IMG_BASE64:$base64]\n");
                    $parent = $blip->parentNode;
                    while ($parent && $parent->nodeName !== 'w:drawing') { $parent = $parent->parentNode; }
                    if ($parent && $parent->parentNode) { 
                        $parent->parentNode->replaceChild($text_node, $parent); 
                    }
                }
            }
            $content = $dom->saveXML();
            $content = str_replace(['</w:p>', '<w:br/>'], "\n", $content);
            $content = strip_tags($content);
        }
        $zip->close();
    }
    return $content;
}

$text_raw = extract_docx_to_base64($file_tmp);

// --- VALIDASI KESESUAIAN KODE SOAL ---
$kode_soal_file = "";
$kode_cocok = true;
if (preg_match('/Kode Soal:\s*(.*)\n/i', $text_raw, $match_kode)) {
    $kode_soal_file = trim($match_kode[1]);
    if (strtoupper($kode_soal_file) !== strtoupper($kode_soal_post)) {
        $kode_cocok = false;
    }
}

$text = preg_replace('/Kode Soal:.*?\n[=\-]+/si', '', $text_raw); 
$text = trim($text);

$blocks = preg_split('/\-{5,}/', $text);
$soal_list = [];
$ada_duplikat = false;
$ada_error_opsi = false;

foreach ($blocks as $block) {
    $block = trim($block);
    if (empty($block)) continue;
    if (!preg_match('/^(\d+)\.\s*(.*)/s', $block, $m)) continue;
    
    $nomor = intval($m[1]);
    $lines = explode("\n", $m[2]);
    
    $soal = [
        'nomer_soal' => $nomor,
        'pertanyaan' => '',
        'pilihan' => [], 
        'opsi_angka' => [], 
        'tipe_soal' => 'PG',
        'jawaban_benar' => '',
        'gambar_base64' => '', 
        'error_opsi' => false,
        'sudah_ada' => false
    ];

    $current_mode = 'pertanyaan'; 
    $mode_jawaban = false;
    $last_option_key = '';

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;

        if (preg_match('/\[IMG_BASE64:(.*?)\]/', $line, $img_match)) {
            $img_data = $img_match[1];
            if ($current_mode == 'pertanyaan') {
                $soal['gambar_base64'] = $img_data;
            } elseif ($current_mode == 'pilihan' && $last_option_key != '') {
                $soal['pilihan'][$last_option_key] .= " [IMG_BASE64:$img_data]";
            }
            continue; 
        }

        if (preg_match('/^Tipe:\s*(.+)/i', $line, $mt)) { $soal['tipe_soal'] = strtoupper(trim($mt[1])); continue; }
        if (preg_match('/^Kunci:\s*(.+)/i', $line, $mk)) { $soal['jawaban_benar'] = strtoupper(trim($mk[1])); continue; }
        if (preg_match('/^Jawaban:/i', $line)) { $mode_jawaban = true; continue; }

        if ($mode_jawaban) {
            $soal['jawaban_benar'] .= ($soal['jawaban_benar'] == '' ? '' : "\n") . $line;
        } 
        elseif (preg_match('/^([A-E])\.\s*(.*)/i', $line, $mo)) {
            $current_mode = 'pilihan';
            $last_option_key = strtoupper($mo[1]);
            $soal['pilihan'][$last_option_key] = $mo[2];
        } 
        elseif (preg_match('/^(\d+)\.\s*(.*)/', $line, $mn)) {
            $current_mode = 'pilihan';
            $last_option_key = intval($mn[1]);
            if ($soal['tipe_soal'] === 'MJD') {
                $soal['opsi_angka'][$last_option_key] = $mn[2];
            } else {
                $soal['pilihan'][$last_option_key] = $mn[2];
            }
        } 
        else {
            if ($current_mode == 'pertanyaan') {
                $soal['pertanyaan'] .= $line . " ";
            } elseif ($last_option_key != '') {
                $soal['pilihan'][$last_option_key] .= " " . $line;
            }
        }
    }

    if (in_array($soal['tipe_soal'], ['PG', 'PGX'])) {
        if (count($soal['pilihan']) > $jumlah_opsi_allowed) {
            $soal['error_opsi'] = true;
            $ada_error_opsi = true;
        }
    }

    $res_cek = $koneksi->query("SELECT id_soal FROM butir_soal WHERE kode_soal='$kode_soal_post' AND nomer_soal='$nomor'");
    if ($res_cek && $res_cek->num_rows > 0) {
        $soal['sudah_ada'] = true;
        $ada_duplikat = true;
    }
    $soal_list[] = $soal;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Preview Import - <?= htmlspecialchars($kode_soal_post) ?></title>
    <?php include '../inc/css.php'; ?>
    <style>
        body { background-color: #f4f7f6; }
        .card-preview { border: none; border-radius: 15px; margin-bottom: 30px; border-left: 6px solid #3b7ddd; transition: 0.3s; }
        .card-danger { border-left-color: #dc3545 !important; background-color: #fff8f8; }
        .img-soal { max-width: 100%; max-height: 350px; border: 1px solid #ddd; border-radius: 10px; margin: 15px 0; display: block; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        .img-opsi { max-width: 200px; display: block; margin-top: 8px; border: 1px solid #eee; border-radius: 5px; }
        .badge-tipe { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; padding: 5px 12px; }
        .opsi-container { background: #fff; padding: 12px; border-radius: 8px; margin-bottom: 8px; border: 1px solid #edf2f7; }
        .opsi-error { border: 1px dashed #dc3545; background-color: #fff5f5; }
        .opsi-error-text { color: #dc3545; font-size: 0.85rem; font-weight: bold; }
        .kunci-box { background: #e6fffa; border: 1px solid #b2f5ea; color: #2c7a7b; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark"><i class="fas fa-file-contract me-2 text-primary"></i>Preview Import Soal</h2>
            <p class="text-muted mb-0">
                Kode Sistem: <span class="badge bg-dark"><?= $kode_soal_post ?></span> | 
                Maksimal: <span class="badge bg-primary"><?= $jumlah_opsi_allowed ?> Opsi</span>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="daftar_butir_soal.php?kode_soal=<?= $kode_soal_post ?>" class="btn btn-outline-danger px-4">
                <i class="fas fa-times me-1"></i> Batalkan
            </a>
        </div>
    </div>

    <?php if (!$kode_cocok || $ada_duplikat || $ada_error_opsi): ?>
        <div class="alert alert-custom alert-danger shadow-sm border-0 mb-4" style="border-radius:12px;">
            <div class="d-flex">
                <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                <div>
                    <h5 class="fw-bold mb-1">Ditemukan Masalah Berat!</h5>
                    <ul class="mb-0 small">
                        <?php if(!$kode_cocok): ?>
                            <li class="fw-bold">KODE SOAL TIDAK COCOK! File Word berisi "<?= htmlspecialchars($kode_soal_file) ?>", sedangkan sistem meminta "<?= $kode_soal_post ?>".</li>
                        <?php endif; ?>
                        <?php if($ada_duplikat): ?><li>Beberapa nomor soal sudah terdaftar di database (Duplikat).</li><?php endif; ?>
                        <?php if($ada_error_opsi): ?><li>Terdapat soal PG yang opsinya melebihi batas sistem (<?= $jumlah_opsi_allowed ?>).</li><?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($soal_list as $s): 
        $has_error = ($s['sudah_ada'] || $s['error_opsi']);
    ?>
    <div class="card card-preview shadow-sm <?= $has_error ? 'card-danger' : '' ?>">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
            <div>
                <span class="fs-5 fw-bold text-primary">Soal No. <?= $s['nomer_soal'] ?></span>
                <span class="badge badge-tipe ms-2 text-uppercase"><?= $s['tipe_soal'] ?></span>
            </div>
            <div>
                <?php if($s['sudah_ada']): ?><span class="badge bg-danger rounded-pill px-3">SUDAH ADA</span><?php endif; ?>
                <?php if($s['error_opsi']): ?><span class="badge bg-warning text-dark rounded-pill px-3">OPSI BERLEBIH</span><?php endif; ?>
            </div>
        </div>
        <div class="card-body pt-0 px-4 pb-4">
            <div class="pertanyaan-area mb-3">
                <p class="fs-6 text-dark" style="line-height: 1.6;"><?= nl2br(htmlspecialchars(trim($s['pertanyaan']))) ?></p>
                <?php if ($s['gambar_base64']): ?>
                    <img src="<?= $s['gambar_base64'] ?>" class="img-soal">
                <?php endif; ?>
            </div>

            <div class="opsi-area ps-3 border-start border-3 border-light">
                <?php if (in_array($s['tipe_soal'], ['PG', 'PGX'])): ?>
                    <?php 
                    $count = 0;
                    foreach ($s['pilihan'] as $h => $t): 
                        $count++;
                        $is_invalid = ($count > $jumlah_opsi_allowed);
                        $teks_opsi = $t;
                        $gambar_opsi = '';
                        if (preg_match('/\[IMG_BASE64:(.*?)\]/', $t, $match_opsi)) {
                            $teks_opsi = trim(str_replace($match_opsi[0], '', $t));
                            $gambar_opsi = $match_opsi[1];
                        }
                    ?>
                        <div class="opsi-container shadow-xs <?= $is_invalid ? 'opsi-error' : '' ?>">
                            <div class="d-flex align-items-start">
                                <strong class="me-2 text-primary"><?= $h ?>.</strong>
                                <div class="flex-grow-1">
                                    <span><?= htmlspecialchars($teks_opsi) ?></span>
                                    <?php if($gambar_opsi): ?>
                                        <img src="<?= $gambar_opsi ?>" class="img-opsi">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if($is_invalid): ?>
                                <div class="opsi-error-text mt-1"><i class="fas fa-exclamation-triangle me-1"></i> Melebihi batas opsi sistem</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                <?php elseif ($s['tipe_soal'] == 'MJD'): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border">
                                <small class="text-muted d-block mb-2 fw-bold">KOLOM KIRI (A, B...)</small>
                                <?php foreach($s['pilihan'] as $h=>$t) echo "<div class='mb-1'><strong>$h.</strong> $t</div>"; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border">
                                <small class="text-muted d-block mb-2 fw-bold">KOLOM KANAN (1, 2...)</small>
                                <?php foreach($s['opsi_angka'] as $n=>$t) echo "<div class='mb-1'><strong>$n.</strong> $t</div>"; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mt-4 p-3 kunci-box rounded-3 d-flex align-items-center">
                <i class="fas fa-key me-2"></i>
                <strong>Kunci Jawaban:</strong> 
                <span class="ms-2 fw-bold fs-5"><?= nl2br(htmlspecialchars($s['jawaban_benar'] ?: '-')) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <form action="proses_import_save.php" method="POST" class="mt-5 pb-5">
        <input type="hidden" name="kode_soal" value="<?= $kode_soal_post ?>">
        <textarea name="data_json" hidden><?= htmlspecialchars(json_encode($soal_list)) ?></textarea>
        
        <?php if (!$kode_cocok || $ada_duplikat || $ada_error_opsi): ?>
            <div class="card border-warning bg-light shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    <h5 class="text-danger fw-bold"><i class="fas fa-lock me-2"></i>Penyimpanan Dinonaktifkan</h5>
                    <p class="mb-0 small">Sistem mendeteksi kesalahan fatal. Periksa Kode Soal atau nomor soal duplikat pada file Anda.</p>
                </div>
            </div>
            <a href="daftar_butir_soal.php?kode_soal=<?= $kode_soal_post ?>" class="btn btn-secondary w-100 py-3 btn-lg shadow">
                <i class="fas fa-sync-alt me-2"></i>Ulangi Proses Upload
            </a>
        <?php else: ?>
            <button type="button" id="btnSimpan" class="btn btn-primary btn-lg w-100 py-3 shadow-lg">
                <i class="fas fa-save me-2"></i> Konfirmasi & Simpan ke Database
            </button>
        <?php endif; ?>
    </form>
</div>
<?php include '../inc/js.php'; ?>
<script>
document.getElementById('btnSimpan')?.addEventListener('click', function(e) {
    const form = this.closest('form');
    
    Swal.fire({
        title: 'Konfirmasi Simpan',
        text: "Apakah data sudah benar? Data akan disimpan permanen ke database.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b7ddd',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fas fa-check"></i> Ya, Simpan!',
        cancelButtonText: 'Periksa Kembali',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sedang Memproses...',
                text: 'Mohon tunggu sebentar, sistem sedang menyimpan data dan gambar.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            form.submit();
        }
    });
});
</script>
</body>
</html>