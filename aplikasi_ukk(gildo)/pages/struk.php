<?php
if (!isset($conn)) {
    include 'config/koneksi.php'; 
}
$back = $_GET['from'] ?? 'page=transaksi';

$id = $_GET['id'] ?? 0;

if ($id == 0) {
    echo "<div class='alert alert-danger'>ID parkir tidak valid</div>";
    return;
}

$sql = "
SELECT 
    t.*, 
    k.plat_nomor, k.jenis_kendaraan, k.warna,
    tr.tarif_per_jam,
    u.nama_lengkap AS petugas
FROM tb_transaksi t
JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
LEFT JOIN tb_tarif tr ON t.id_tarif = tr.id_tarif
LEFT JOIN tb_user u ON t.id_user = u.id_user
WHERE t.id_parkir = '$id'
LIMIT 1
";

$q  = mysqli_query($conn, $sql);
$ds = mysqli_fetch_assoc($q);

if (!$ds) {
    echo "<div class='alert alert-danger'>Data struk tidak ditemukan</div>";
    return;
}

/* ================= HITUNG BIAYA HANYA JIKA KELUAR ================= */
$jam = 0;
$total_bayar = 0;

if ($ds['status'] == 'keluar') {
    $masuk  = new DateTime($ds['waktu_masuk']);
    $keluar = new DateTime($ds['waktu_keluar'] ?? 'now');
    $diff   = $masuk->diff($keluar);

    $jam = ($diff->days * 24) + $diff->h;
    if ($diff->i > 0 || $diff->s > 0) $jam++;
    if ($jam < 1) $jam = 1;

    $total_bayar = $jam * $ds['tarif_per_jam'];
}
?>

<style>
.struk-modal-backdrop{
    position:fixed; inset:0; background:rgba(0,0,0,.6);
    display:flex; align-items:center; justify-content:center; z-index:9999;
}
.struk-container{
    width:350px; background:#fff; padding:20px;
    font-family:Courier New, monospace; text-align:center;
}
.plat-nomor{ font-size:26px; font-weight:bold; margin:8px 0; }
.info-detail{ font-size:11px; text-align:left; }
.info-detail div{ display:flex; justify-content:space-between; margin-bottom:2px; }

@media print{
    @page{ size:58mm auto; margin:0; }
    body *{ visibility:hidden; }
    .struk-modal-backdrop, .struk-container, .struk-container *{ visibility:visible; }
    .struk-modal-backdrop{ position:static; background:none; }
    .struk-container{
        position:absolute; top:0; left:50%; transform:translateX(-50%);
        width:58mm; padding:2mm; font-size:10px;
    }
    .no-print{ display:none !important; }
}
</style>

<div class="struk-modal-backdrop">
<div class="struk-container">

    <h4 style="margin:0;">STRUK PARKIR</h4>
    <div style="font-size:11px;">Aplikasi Parkir Digital</div>
    <hr>

    <div class="plat-nomor"><?= strtoupper($ds['plat_nomor']) ?></div>
    <div style="font-size:12px;font-weight:bold;">
        <?= $ds['jenis_kendaraan'] ?> | <?= $ds['warna'] ?>
    </div>

    <hr>

    <div class="info-detail">
        <div><span>Masuk</span><span><?= date('d/m/y H:i', strtotime($ds['waktu_masuk'])) ?></span></div>
        
        <?php if ($ds['status'] == 'keluar'): ?>
            
            <div style="font-weight:bold;">
                <span>TOTAL</span><span>Rp <?= number_format($total_bayar) ?></span>
            </div>
        <?php endif; ?>

        <div style="font-size:10px;">
            <span>Petugas</span><span><?= $ds['petugas'] ?? 'Admin' ?></span>
        </div>
    </div>

    <hr>

    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($ds['kode_karcis']) ?>" style="width:80px">
    <div style="font-weight:bold;"><?= $ds['kode_karcis'] ?></div>

    <hr>
    <div style="font-size:9px;">Simpan karcis ini</div>

    <div class="no-print" style="margin-top:10px;">
        <button onclick="window.print()" style="width:100%;padding:8px;">CETAK</button>
    </div>
     <div class="no-print">
      <a href="index.php?<?= $back ?>" style="
            display:block;margin-top:6px;
            padding:10px;background:#6c757d;
            color:#fff;text-decoration:none;text-align:center;">
            KEMBALI
        </a>
    </div>

</div>
</div>