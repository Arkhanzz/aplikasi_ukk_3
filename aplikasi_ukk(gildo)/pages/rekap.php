<?php 
$filter = $_GET['filter'] ?? 'hari';
$f_jenis = $_GET['f_jenis'] ?? 'semua';
$tgl_hari = $_GET['tgl_hari'] ?? date('Y-m-d'); 
$tgl_mulai = $_GET['tgl_mulai'] ?? date('Y-m-d'); 
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d'); 

// Hitung filter tanggal
if ($filter == 'rentang') {
    $start = $tgl_mulai; 
    $end = $tgl_selesai;
    $label_periode = "Rentang: ".date('d F Y', strtotime($start))." - ".date('d F Y', strtotime($end));
} else { 
    $start = $end = $tgl_hari;
    $label_periode = "Tanggal: ".date('d F Y', strtotime($start));
}

// Filter jenis kendaraan
$where_jenis = '';
if ($f_jenis == 'mobil') $where_jenis = " AND k.jenis_kendaraan LIKE '%mobil%'";
elseif ($f_jenis == 'motor') $where_jenis = " AND k.jenis_kendaraan LIKE '%motor%'";

// Ambil data rekap sesuai filter
$data_rekap = mysqli_fetch_all(mysqli_query($conn, "
SELECT 
    t.*,
    tr.tarif_per_jam,
    k.plat_nomor,
    k.pemilik,
    k.jenis_kendaraan,
    a.nama_area
FROM tb_transaksi t
JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
LEFT JOIN tb_tarif tr ON t.id_tarif = tr.id_tarif
LEFT JOIN tb_area_parkir a ON t.id_area = a.id_area
WHERE t.status='keluar' AND DATE(t.waktu_keluar) BETWEEN '$start' AND '$end'
$where_jenis
ORDER BY t.id_parkir DESC
"), MYSQLI_ASSOC);

// Statistik
$total = $jml_mobil = $jml_motor = 0;
foreach ($data_rekap as $r) {
    $total += $r['biaya_total'] ?? 0;
    if (stripos($r['jenis_kendaraan'], 'mobil') !== false) $jml_mobil++;
    if (stripos($r['jenis_kendaraan'], 'motor') !== false) $jml_motor++;
}

?>

<style>
    @media print { .no-print { display: none !important; } .row { display: flex !important; flex-wrap: nowrap !important; } .col-6 { width: 50% !important; } .card { border: 1px solid #ddd !important; break-inside: avoid; } }
    .card { transition: all 0.3s ease; border-radius: 15px; }
    .card:hover { transform: translateY(-2px); }
    .btn-gradient-primary, .bg-gradient-primary { background: linear-gradient(135deg, #0048b4 0%, #1e273a 100%); color: white; border: none; }
    .bg-gradient-success { background: linear-gradient(135deg, #1e273a 0%, #3335a1 100%); color: white; }
    .bg-gradient-warning { background: linear-gradient(135deg, #3335a1 0%, #1e273a 100%); color: white; }
    .bg-gradient-info { background: linear-gradient(135deg, #1e273a 0%, #0048b4 100%); color: white; }
    .table-hover tbody tr:hover { background-color: rgba(102, 126, 234, 0.05); }
</style> 


<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<div class="container-fluid py-4 " id="rekap-container">
    <div class="d-flex justify-content-between mb-4">
        <div><h4 class="fw-bold mb-0">Laporan Rekapitulasi Parkir</h4><p class="text-muted">Analisis statistik transaksi</p></div>
        <span class="badge bg-light text-dark fs-6 p-2"><i class="bi bi-bar-chart me-2"></i>Report</span>
    </div>

    <div class="card shadow-lg mb-4 no-print" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-gradient-primary py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-filter-circle me-2"></i>Filter Laporan</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="rekap">
                <div class="col-md-3">
                    <label class="small fw-bold">Periode</label>
                    <select name="filter" id="filterSelect" class="form-select" onchange="toggleInputs()">
                        <option value="hari" <?= $filter == 'hari' ? 'selected' : '' ?>>Per Hari</option>
                        <option value="rentang" <?= $filter == 'rentang' ? 'selected' : '' ?>>Rentang Tanggal</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Jenis</label>
                    <select name="f_jenis" class="form-select">
                        <option value="semua" <?= $f_jenis == 'semua' ? 'selected' : '' ?>>Semua</option>
                        <option value="mobil" <?= $f_jenis == 'mobil' ? 'selected' : '' ?>>Mobil</option>
                        <option value="motor" <?= $f_jenis == 'motor' ? 'selected' : '' ?>>Motor</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold" id="label_dinamis">Tanggal</label>
                    <div id="div_hari"><input type="date" name="tgl_hari" value="<?= $tgl_hari ?>" class="form-control"></div>
                    <div id="div_rentang" class="d-none input-group">
                        <input type="date" name="tgl_mulai" value="<?= $tgl_mulai ?>" class="form-control">
                        <span class="input-group-text">s/d</span>
                        <input type="date" name="tgl_selesai" value="<?= $tgl_selesai ?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-2"><button class="btn btn-gradient-primary w-100 fw-bold">Terapkan</button></div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <?php 
        $stats = [
            ['bg-gradient-primary', 'bi-car-front-fill', 'Mobil', 'res_mobil', $jml_mobil],
            ['bg-gradient-success', 'bi-bicycle', 'Motor', 'res_motor', $jml_motor],
            ['bg-gradient-warning', 'bi-car-front', 'Total', 'res_total', $jml_mobil+$jml_motor],
            ['bg-gradient-info', 'bi-cash-coin', 'Pendapatan', '', 'Rp '.number_format($total, 0, ',', '.')]
        ];
        foreach($stats as $s): ?>
        <div class="col-md-3">
            <div class="card <?= $s[0] ?> border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <i class="<?= $s[1] ?> fs-1 opacity-75"></i>
                    <div class="ms-3"><h6 class="mb-0 opacity-75"><?= $s[2] ?></h6><h2 class="mb-0 fw-bold" id="<?= $s[3] ?>"><?= $s[4] ?></h2></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
        <div class="p-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="fw-bold mb-0" id="title_rekap"><?= $label_periode ?></h5>
            <div class="btn-group no-print">
                <button onclick="window.print()" class="btn btn-sm btn-outline-primary">Cetak</button>
                <button onclick="exportExcel()" class="btn btn-sm btn-outline-success">Excel</button>
                <button onclick="exportPDF()" class="btn btn-sm btn-outline-danger">PDF</button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="tableRekap" class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr><th class="ps-4">Masuk</th><th>Keluar</th><th>Durasi</th><th>Plat</th><th>Jenis</th><th class="text-end pe-4">Biaya</th></tr>
                </thead>
                <tbody>
<?php foreach ($data_rekap as $r): ?>

<?php
$masuk = strtotime($r['waktu_masuk']);

if (!empty($r['waktu_keluar'])) {
    $selisih = strtotime($r['waktu_keluar']) - $masuk;
} else {
    $selisih = time() - $masuk;
}

/* DURASI */
$jam   = floor($selisih / 3600);
$menit = floor(($selisih % 3600) / 60);
$durasi = $jam > 0 ? $jam.' jam '.$menit.' menit' : $menit.' menit';

/* BIAYA REALTIME MINIMAL 1 JAM */
$jamTagih = ceil($selisih / 3600);
if ($jamTagih < 1) $jamTagih = 1;
$tarifPerJam = $r['tarif_per_jam'] ?? 0;
$biayaRealtime = $jamTagih * $tarifPerJam;

/* SUDAH KELUAR = AMBIL DB | BELUM = REALTIME */
$biaya = !empty($r['waktu_keluar']) ? $r['biaya_total'] : $biayaRealtime;
?>

<tr>
    <?php 
    $masuk_dt = strtotime($r['waktu_masuk']);
    $masuk_jam = date('H:i', $masuk_dt);
    $masuk_tgl = date('d M Y', $masuk_dt);

    if (!empty($r['waktu_keluar'])) {
        $keluar_dt = strtotime($r['waktu_keluar']);
        $keluar_jam = date('H:i', $keluar_dt);
        $keluar_tgl = date('d M Y', $keluar_dt);
    } else {
        $keluar_jam = '-';
        $keluar_tgl = '';
    }
    ?>
    
    <td>
        <div style="font-weight:bold;"><?= $masuk_jam ?></div>
        <div style="font-size:0.8rem; color:gray;"><?= $masuk_tgl ?></div>
    </td>

    <td>
        <div style="font-weight:bold;"><?= $keluar_jam ?></div>
        <div style="font-size:0.8rem; color:gray;"><?= $keluar_tgl ?></div>
    </td>
    <td><span class="badge bg-primary"><?= $durasi ?></span></td>
    <td><?= $r['plat_nomor'] ?></td>
    <td><?= $r['jenis_kendaraan'] ?></td>
    <td class="text-end fw-bold text-success">Rp <?= number_format($biaya,0,',','.') ?></td>
</tr>

<?php endforeach; ?>
</tbody>
                <tfoot class="bg-light fw-bold">
                    <tr><td colspan="5" class="text-end">TOTAL PENDAPATAN</td><td class="text-end pe-4 text-success"><h4>Rp <?= number_format($total, 0, ',', '.') ?></h4></td></tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print, .btn-group, .badge.bg-light, .card-header, 
        .navbar, .sidebar, footer, .breadcrumb,
        h4.fw-bold.mb-0, p.text-muted { 
            display: none !important; 
        }

        body { 
            background: white !important; 
            font-family: "Times New Roman", serif; 
            color: black !important;
        }
        
        .container-fluid { padding: 0 !important; margin: 0 !important; }

       #rekap-container::before {
        content: "LAPORAN REKAPITULASI TRANSAKSI PARKIR";
        display: block;
        text-align: center;
        font-size: 16pt;
        font-weight: bold;
        margin-bottom: 20px;
        text-transform: uppercase;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }


        .row.g-3 {
            display: flex !important;
            flex-direction: row !important;
            border: 1px solid #000 !important;
            margin-bottom: 20px !important;
            padding: 10px 0 !important;
        }
        
        .col-md-3 { 
            flex: 1 !important; 
            width: 25% !important; 
            text-align: center !important;
            border-right: 1px solid #000 !important;
        }
        .col-md-3:last-child { border-right: none !important; }

        .card { background: none !important; box-shadow: none !important; border: none !important; }
        .card i { display: none !important; }
.card h6 { 
    font-size: 10pt; 
    text-transform: uppercase; 
    margin-bottom: 5px !important; 
    color: #000 !important;
    font-weight: bold !important;
}
.card h2 { 
    font-size: 12pt; 
    font-weight: bold; 
    color: #000 !important;
}

        #title_rekap { text-align: left; font-size: 12pt; font-weight: bold; margin-bottom: 10px; }
        
        .table { width: 100% !important; border: 1px solid #000 !important; }
        .table th { 
            background-color: #eee !important; 
            border: 1px solid #000 !important; 
            text-align: center !important;
            padding: 5px !important;
            font-size: 10pt;
        }
        .table td { 
            border: 1px solid #000 !important; 
            padding: 5px !important;
            font-size: 10pt;
        }
        
        .badge { background: none !important; color: black !important; padding: 0 !important; }
        .text-success { color: black !important; }

                #rekap-container::after {
            content: "Dicetak pada: <?= date('d/m/Y H:i') ?>";
            display: block;
            text-align: right;
            font-size: 9pt;
            margin-top: 15px;
        }

    }
</style>

<script>
function toggleInputs() {
    const f = document.getElementById('filterSelect').value;
    document.getElementById('div_hari').classList.toggle('d-none', f !== 'hari');
    document.getElementById('div_rentang').classList.toggle('d-none', f !== 'rentang');
    document.getElementById('label_dinamis').textContent = f === 'hari' ? 'Pilih Tanggal' : 'Pilih Rentang Tanggal';
}
function exportExcel() {
    const wb = XLSX.utils.book_new();

    // Header + ringkasan
    const data = [
        ['LAPORAN REKAPITULASI TRANSAKSI PARKIR'],
        ['<?= $label_periode ?>'],
        [],
        ['Total Mobil', <?= $jml_mobil ?>],
        ['Total Motor', <?= $jml_motor ?>],
        ['Total Pendapatan', 'Rp <?= number_format($total,0,',','.') ?>'],
        [],
        ['Waktu Masuk','Waktu Keluar','Durasi','Plat Nomor','Jenis Kendaraan','Biaya']
    ];

    <?php if(!empty($data_rekap)): foreach($data_rekap as $r):
        $masuk = strtotime($r['waktu_masuk']);
        if(!empty($r['waktu_keluar'])){
            $keluar = strtotime($r['waktu_keluar']);
            $waktu_keluar = date('d/m/Y H:i', $keluar);
            $biaya = $r['biaya_total'];
        } else {
            $waktu_keluar = '-';
            $selisih = time() - $masuk;
            $tarifPerJam = $r['tarif_per_jam'] ?? 0;
            $biaya = ceil($selisih / 3600) * $tarifPerJam;
            if($biaya < $tarifPerJam) $biaya = $tarifPerJam;
        }

        $durasi = (new DateTime($r['waktu_masuk']))->diff(
            !empty($r['waktu_keluar']) ? new DateTime($r['waktu_keluar']) : new DateTime()
        )->format('%h jam %i menit');
    ?>
    data.push([
        '<?= date('d/m/Y H:i', $masuk) ?>',
        '<?= $waktu_keluar ?>',
        '<?= $durasi ?>',
        '<?= $r['plat_nomor'] ?>',
        '<?= $r['jenis_kendaraan'] ?>',
        'Rp <?= number_format($biaya,0,',','.') ?>'
    ]);
    <?php endforeach; endif; ?>

    const ws = XLSX.utils.aoa_to_sheet(data);
    ws['!cols'] = [{wch:18},{wch:18},{wch:15},{wch:15},{wch:20},{wch:18}];
    ws['!merges'] = [
        {s:{r:0,c:0}, e:{r:0,c:5}},
        {s:{r:1,c:0}, e:{r:1,c:5}}
    ];

    XLSX.utils.book_append_sheet(wb, ws, 'Laporan Parkir');
    XLSX.writeFile(wb, `Laporan_Parkir_${new Date().toISOString().slice(0,10)}.xlsx`);
}
function exportPDF() {
    const { jsPDF } = window.jspdf; const doc = new jsPDF('landscape');
    doc.text("Laporan Parkir - <?= $label_periode ?>", 14, 15);
    doc.autoTable({ html: '#tableRekap', startY: 25, theme: 'grid' });
    doc.save("Laporan_Parkir.pdf");
}
document.addEventListener('DOMContentLoaded', () => {
    toggleInputs();
});
</script>