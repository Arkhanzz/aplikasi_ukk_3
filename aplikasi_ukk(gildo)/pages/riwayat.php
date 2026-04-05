<?php
if (!isset($conn)) include 'config/koneksi.php';
$riwayat = mysqli_fetch_all(mysqli_query($conn, "SELECT t.*, k.plat_nomor, k.pemilik, k.jenis_kendaraan, a.nama_area FROM tb_transaksi t JOIN tb_kendaraan k ON t.id_kendaraan=k.id_kendaraan JOIN tb_area_parkir a ON t.id_area=a.id_area WHERE t.status='keluar' ORDER BY t.waktu_keluar DESC"), MYSQLI_ASSOC);
$totalTransaksi = count($riwayat); $totalPendapatan = $mobilCount = $motorCount = 0;
foreach($riwayat as $r) {
    $totalPendapatan += $r['biaya_total'];
    if (stripos($r['jenis_kendaraan'], 'mobil') !== false) $mobilCount++;
    if (stripos($r['jenis_kendaraan'], 'motor') !== false) $motorCount++;
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Riwayat Transaksi</h4><p class="text-muted mb-0 small">Data kendaraan yang sudah keluar</p></div>
        <span class="badge bg-light text-dark p-2 border"><i class="bi bi-clock-history me-1"></i><?= $totalTransaksi ?> Data</span>
    </div>

    <div class="row mb-3">
        <?php 
        $stats = [['Mobil', $mobilCount, 'bi-car-front', 'bg-gradient-primary'], ['Motor', $motorCount, 'bi-bicycle', 'bg-gradient-success'], ['Transaksi', $totalTransaksi, 'bi-receipt', 'bg-gradient-warning'], ['Pendapatan', 'Rp '.number_format($totalPendapatan,0,',','.'), 'bi-cash-coin', 'bg-gradient-info']];
        foreach($stats as $s): ?>
        <div class="col-md-3 mb-3"><div class="card <?= $s[3] ?> text-white border-0 shadow-sm" style="border-radius:12px;"><div class="card-body py-3 d-flex align-items-center"><i class="<?= $s[2] ?> fs-2 opacity-75"></i><div class="ms-3"><small class="opacity-75 d-block"><?= $s[0] ?></small><h4 class="mb-0 fw-bold"><?= $s[1] ?></h4></div></div></div></div>
        <?php endforeach; ?>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;"><div class="card-body p-3"><div class="row g-2">
        <div class="col-md-3"><input type="date" class="form-control" id="filterTanggal"></div>
        <div class="col-md-4"><input type="text" class="form-control" id="searchInput" placeholder="Cari plat atau nama pemilik..."></div>
        <div class="col-md-3"><select class="form-select" id="filterJenis"><option value="">Semua Jenis</option><option value="mobil">Mobil</option><option value="motor">Motor</option></select></div>
        <div class="col-md-2"><button class="btn btn-dark w-100 fw-bold" onclick="resetFilters()">Reset</button></div>
    </div></div></div>

    <div class="card border-0 shadow-sm" style="border-radius:15px; overflow:hidden;">
        <div class="table-responsive"><table class="table table-hover align-middle mb-0" id="riwayatTable">
            <thead class="bg-light text-muted small uppercase"><tr><th class="ps-4">Waktu Keluar</th><th>Kendaraan & Pemilik</th><th>Jenis</th><th>Area</th><th>Durasi</th><th>Total</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
                <?php foreach($riwayat as $r): 
                    $int = (new DateTime($r['waktu_masuk']))->diff(new DateTime($r['waktu_keluar']));
                    // Format durasi: D days H:i format
                    $durasi_display = $int->format('%d hari %h jam %i menit');
                    $jns = strtolower($r['jenis_kendaraan']);
                ?>
                <tr data-tanggal="<?= date('Y-m-d',strtotime($r['waktu_keluar'])) ?>" data-jenis="<?= $jns ?>" data-plat="<?= strtolower($r['plat_nomor']) ?>" data-pemilik="<?= strtolower($r['pemilik']) ?>">
                    <td class="ps-4"><strong><?= date('H:i',strtotime($r['waktu_keluar'])) ?></strong><br><small class="text-muted"><?= date('d M Y',strtotime($r['waktu_keluar'])) ?></small></td>
                    <td><span class="fw-bold text-primary"><?= $r['plat_nomor'] ?></span><br><span class="text-dark"><i class="bi bi-person me-1"></i><?= $r['pemilik'] ?: '-' ?></span></td>
                    <td><span class="badge <?= strpos($jns,'motor')!==false?'bg-info':'bg-primary' ?>"><?= $r['jenis_kendaraan'] ?></span></td>
                    <td><small class="text-muted"><i class="bi bi-geo-alt"></i></small> <?= $r['nama_area'] ?></td>
                    <td><small class="badge bg-dark"><?= $durasi_display ?></small></td>
                    <td class="fw-bold text-success">Rp <?= number_format($r['biaya_total'],0,',','.') ?></td>
                    <td class="text-center"><a href="?page=struk&id=<?= $r['id_parkir'] ?>&from=page=riwayat" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer"></i></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</div>

<style> 
    .bg-gradient-primary{background:linear-gradient(135deg, #0048b4 0%, #1e273a 100%)} 
    .bg-gradient-success{background:linear-gradient(135deg, #1e273a 0%, #3335a1 100%)}
    .bg-gradient-warning{background:linear-gradient(135deg, #3335a1 0%, #1e273a 100%)}
    .bg-gradient-info{background:linear-gradient(135deg, #1e273a 0%, #0048b4 100%)}
    .table thead th{font-size:11px; letter-spacing:0.5px}
</style>

<script>
function resetFilters(){document.getElementById('filterTanggal').value=''; document.getElementById('searchInput').value=''; document.getElementById('filterJenis').value=''; applyFilters()}
function applyFilters(){
    const t=document.getElementById('filterTanggal').value, s=document.getElementById('searchInput').value.toLowerCase(), j=document.getElementById('filterJenis').value;
    document.querySelectorAll('#riwayatTable tbody tr').forEach(r=>{
        const matchT = !t || r.getAttribute('data-tanggal')===t;
        const matchJ = !j || r.getAttribute('data-jenis').includes(j);
        const matchS = !s || r.getAttribute('data-plat').includes(s) || r.getAttribute('data-pemilik').includes(s);
        r.style.display = (matchT && matchJ && matchS) ? '' : 'none';
    });
}
document.getElementById('filterTanggal').onchange=applyFilters;
document.getElementById('searchInput').oninput=applyFilters;
document.getElementById('filterJenis').onchange=applyFilters;
</script>