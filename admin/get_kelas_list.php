<?php
include '../koneksi/koneksi.php';

$current = $_GET['current'] ?? '';
$current_arr = array_map('trim', explode(',', $current));

$q = mysqli_query($koneksi, "
    SELECT DISTINCT CONCAT(kelas, rombel) as kr
    FROM siswa
");

$data = [];
while($d = mysqli_fetch_assoc($q)){
    $data[] = $d['kr'];
}

// SORT angka & romawi
$romawi = [
    'I'=>1,'II'=>2,'III'=>3,'IV'=>4,'V'=>5,'VI'=>6,
    'VII'=>7,'VIII'=>8,'IX'=>9,'X'=>10,'XI'=>11,'XII'=>12
];

$getAngka = function($text) use ($romawi){
    preg_match('/^([A-ZIVXLC0-9]+)([A-Z]+)/', $text, $m);
    $kelas = $m[1] ?? '';
    if(is_numeric($kelas)) return (int)$kelas;
    return $romawi[$kelas] ?? 0;
};

usort($data, function($a,$b) use($getAngka){
    $na = $getAngka($a);
    $nb = $getAngka($b);
    if($na == $nb) return strcmp($a,$b);
    return $na - $nb;
});
?>

<div style="text-align:left">
    <input type="text" id="searchKelas" class="form-control mb-2" placeholder="Cari kelas / rombel...">

    <div class="mb-2">
        <button type="button" class="btn btn-sm btn-success" id="selectAll">Select All</button>
        <button type="button" class="btn btn-sm btn-danger" id="unselectAll">Unselect All</button>
    </div>

    <div style="max-height:350px;overflow:auto;border:1px solid #ddd;padding:10px;border-radius:6px;">
        <?php foreach($data as $kr): 
            $checked = in_array($kr, $current_arr) ? 'checked' : '';
        ?>
        <div class="form-check kelas-item">
            <input class="form-check-input chk-kelas" type="checkbox" value="<?= $kr ?>" <?= $checked ?>>
            <label class="form-check-label"><?= $kr ?></label>
        </div>
        <?php endforeach; ?>
    </div>
</div>