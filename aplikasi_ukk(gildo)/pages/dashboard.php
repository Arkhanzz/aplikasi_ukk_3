<?php
// Query Statistik Utama
$parkirAktif = mysqli_num_rows(mysqli_query($conn, "SELECT 1 FROM tb_transaksi WHERE status='masuk'"));
$totalTransaksi = mysqli_num_rows(mysqli_query($conn, "SELECT 1 FROM tb_transaksi WHERE status='keluar'"));

$tp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(biaya_total) as t FROM tb_transaksi WHERE DATE(waktu_keluar)=CURDATE()"));
$pendapatanHariIni = $tp['t'] ?? 0;

$bulanIni = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(biaya_total) as t FROM tb_transaksi WHERE MONTH(waktu_keluar)=MONTH(CURDATE()) AND YEAR(waktu_keluar)=YEAR(CURDATE())"));
$pendapatanBulanIni = $bulanIni['t'] ?? 0;

// Statistik Kendaraan
$qKnd = "SELECT COUNT(*) as c FROM tb_transaksi t JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan WHERE k.jenis_kendaraan LIKE ";
$totalMobil = mysqli_fetch_assoc(mysqli_query($conn, $qKnd . "'%mobil%'"))['c'];
$totalLainnya = mysqli_fetch_assoc(mysqli_query($conn, $qKnd . "'%lainnya%'"))['c'];
$totalMotor = mysqli_fetch_assoc(mysqli_query($conn, $qKnd . "'%motor%'"))['c'];

// Data Area & Transaksi
$areaParkir = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM tb_area_parkir ORDER BY nama_area"), MYSQLI_ASSOC);
$transaksiTerakhir = mysqli_fetch_all(mysqli_query($conn, "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area FROM tb_transaksi t JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan JOIN tb_area_parkir a ON t.id_area = a.id_area ORDER BY t.waktu_masuk DESC LIMIT 5"), MYSQLI_ASSOC);

// Chart Data (7 Hari)
$pendapatan7Hari = mysqli_fetch_all(mysqli_query($conn, "SELECT DATE(waktu_keluar) as tgl, SUM(biaya_total) as total FROM tb_transaksi WHERE waktu_keluar >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(waktu_keluar) ORDER BY tgl"), MYSQLI_ASSOC);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-0">Dashboard</h4><p class="text-muted small">Ringkasan sistem manajemen parkir</p></div>
        <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i> <?= date('d F Y, H:i') ?></div>
    </div>

    <div class="row g-3 mb-4">
        <?php 
        $cards = [
            ['Parkir Aktif', $parkirAktif, 'bi-car-front-fill', 'bg-gradient-primary', 'Kendaraan masuk'],
            ['Total Transaksi', $totalTransaksi, 'bi-receipt', 'bg-gradient-success', 'Semua waktu'],
            ['Hari Ini', 'Rp '.number_format($pendapatanHariIni,0,',','.'), 'bi-cash-coin', 'bg-gradient-warning', date('d M Y')],
            ['Bulan Ini', 'Rp '.number_format($pendapatanBulanIni,0,',','.'), 'bi-graph-up', 'bg-gradient-info', date('F Y')]
        ];
        foreach($cards as $c): ?>
        <div class="col-md-3">
            <div class="card <?= $c[3] ?> text-white border-0 shadow-sm" style="border-radius:15px;">
                <div class="card-body p-4 d-flex align-items-center">
                    <i class="<?= $c[2] ?> fs-1 opacity-50"></i>
                    <div class="ms-3">
                        <h6 class="mb-0 small opacity-75"><?= $c[0] ?></h6>
                        <h3 class="mb-0 fw-bold"><?= $c[1] ?></h3>
                        <small class="opacity-75"><?= $c[4] ?></small>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-3" style="border-radius:15px;">
                <h6 class="fw-bold mb-3"><i class="bi bi-graph-up me-2"></i>Pendapatan 7 Hari Terakhir</h6>
                <canvas id="revenueChart" height="250"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius:15px; height:100%;">
                <h6 class="fw-bold mb-3"><i class="bi bi-p-square me-2"></i>Status Area Parkir</h6>
                <?php foreach($areaParkir as $area): 
                    $p = $area['kapasitas'] > 0 ? round(($area['terisi']/$area['kapasitas'])*100) : 0;
                    $clr = $p >= 100 ? 'bg-danger' : ($p >= 80 ? 'bg-warning' : 'bg-success');
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span><?= $area['nama_area'] ?></span>
                        <span class="fw-bold"><?= $area['terisi'] ?>/<?= $area['kapasitas'] ?> (<?= $p ?>%)</span>
                    </div>
                    <div class="progress" style="height:8px;"><div class="progress-bar <?= $clr ?>" style="width:<?= min($p,100) ?>%"></div></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-3" style="border-radius:15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Transaksi Terakhir</h6>
                    <a href="?page=riwayat" class="btn btn-sm btn-light">Semua</a>
                </div>
                <div class="table-responsive"><table class="table table-sm align-middle small">
                    <thead><tr class="text-muted"><th>PLAT</th><th>JENIS</th><th>AREA</th><th>WAKTU</th></tr></thead>
                    <tbody>
                        <?php foreach($transaksiTerakhir as $t): ?>
                        <tr>
                            <td><b><?= $t['plat_nomor'] ?></b></td>
                            <td><span class="badge bg-secondary opacity-75"><?= $t['jenis_kendaraan'] ?></span></td>
                            <td><?= $t['nama_area'] ?></td>
                            <td class="text-muted"><?= date('H:i', strtotime($t['waktu_masuk'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center" style="border-radius:15px; height:100%;">
                <h6 class="fw-bold text-start mb-3">Kendaraan</h6>
                <div class="position-relative mx-auto mb-3" style="width:120px; height:120px;">
                    <canvas id="vehicleChart"></canvas>
                    <div class="position-absolute top-50 start-50 translate-middle">
                        <h5 class="mb-0 fw-bold"><?= $totalMobil+$totalMotor+$totalLainnya ?></h5><small class="text-muted">Total</small>
                    </div>
                </div>
                <div class="d-flex justify-content-around small">
                    <span><i class="bi bi-circle-fill text-primary me-1"></i>Mobil: <?= $totalMobil ?></span>
                    <span><i class="bi bi-circle-fill text-info me-1"></i>Motor: <?= $totalMotor ?></span>
                    <span><i class="bi bi-circle-fill text-info me-1"></i>Lainnya: <?= $totalLainnya ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-grid gap-2">
                 <?php if($role=='admin' || $role=='petugas'): ?>
                <a href="?page=transaksi" class="btn btn-gradient-primary py-3 border-0 text-white shadow-sm" style="border-radius:12px;"><i class="bi bi-plus-circle me-2"></i>Kelola Parkir</a>
              
                <?php endif; ?>
                <a href="?page=rekap" class="btn btn-white py-3 shadow-sm" style="border-radius:12px; border:1px solid #eee;"><i class="bi bi-bar-chart me-2"></i>Laporan</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: [<?php foreach($pendapatan7Hari as $d) echo '"'.date('d M', strtotime($d['tgl'])).'",'; ?>],
            datasets: [{
                data: [<?php foreach($pendapatan7Hari as $d) echo $d['total'].','; ?>],
                borderColor: '#667eea', backgroundColor: 'rgba(102, 126, 234, 0.1)', fill: true, tension: 0.3
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // Vehicle Chart
    new Chart(document.getElementById('vehicleChart'), {
        type: 'doughnut',
        data: {
            labels: ['Mobil', 'Motor'],
            datasets: [{ data: [<?= $totalMobil ?>, <?= $totalMotor ?>], backgroundColor: ['#667eea', '#17a2b8'], borderWidth: 0 }]
        },
        options: { cutout: '75%', plugins: { legend: { display: false } } }
    });
});
</script>

<style>
    .bg-gradient-primary { background: linear-gradient(135deg, #0048b4, #1e273a); }
    .bg-gradient-success { background: linear-gradient(135deg, #1e273a, #3335a1); } 
    .bg-gradient-warning { background: linear-gradient(135deg, #3335a1, #1f2944); }
    .bg-gradient-info { background: linear-gradient(135deg, #1f2944, #0048b4); }
    .btn-gradient-primary { background: linear-gradient(135deg, #667eea, #1f2944); }
    .card { transition: transform .3s; }
    .card:hover { transform: translateY(-3px); }
    .progress { border-radius: 10px; background: #eee; }
    .progress-bar { border-radius: 10px; }
</style>

