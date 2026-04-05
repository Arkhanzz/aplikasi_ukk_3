<?php
$et = null;
if(isset($_GET['edit_p'])){
    $resP = mysqli_query($conn,"SELECT t.*, k.plat_nomor, k.warna, k.pemilik FROM tb_transaksi t JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan WHERE t.id_parkir='$_GET[edit_p]'");
    $et = mysqli_fetch_assoc($resP);
}
$tarifOptions = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM tb_tarif ORDER BY jenis_kendaraan"), MYSQLI_ASSOC);
$areaOptions  = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM tb_area_parkir"), MYSQLI_ASSOC);
$transaksi = mysqli_fetch_all(mysqli_query($conn,"
SELECT t.waktu_masuk, tr.tarif_per_jam, t.*, 
       k.plat_nomor, k.pemilik, k.jenis_kendaraan, 
       a.nama_area
FROM tb_transaksi t
JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
JOIN tb_area_parkir a ON t.id_area = a.id_area
JOIN tb_tarif tr ON t.id_tarif = tr.id_tarif
WHERE t.status='masuk'
ORDER BY t.id_parkir DESC
"), MYSQLI_ASSOC);
?>

<div class="container-fluid mb-4">
    <div class="row align-items-center">
        <div class="col-md-8 d-flex align-items-center mb-3">
            <div class="bg-primary p-3 rounded-3 me-3"><i class="bi bi-car-front-fill text-white fs-2"></i></div>
            <div><h3 class="fw-bold mb-1">Manajemen Parkir</h3><p class="text-muted mb-0">Kelola masuk dan keluar kendaraan</p></div>
        </div>
        <div class="col-md-4 text-end d-flex gap-2 justify-content-end">
            <div class="bg-light p-2 rounded-3"><small class="text-muted">Total Masuk</small><div class="fw-bold fs-5"><?= count($transaksi) ?></div></div>
            <div class="bg-light p-2 rounded-3"><small class="text-muted">Kapasitas</small><div class="fw-bold fs-5"><?php $tk=0; $tt=0; foreach($areaOptions as $a){$tk+=$a['kapasitas'];$tt+=$a['terisi'];} echo "$tt/$tk"; ?></div></div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card border-danger mb-4 shadow-sm">
                <div class="card-header bg-danger text-white d-flex align-items-center"><i class="bi bi-box-arrow-right me-2"></i><h5 class="mb-0 fw-bold">Keluar Parkir Cepat</h5></div>
                <div class="card-body">
                    <form method="POST" action="process/aksi.php" id="formKeluar" class="row g-3 align-items-end">
                        <input type="hidden" name="redirect" value="<?= $_GET['page'] ?>">
                        <input type="hidden" name="aksi" value="konfirmasi_keluar">
                        <div class="col-md-8"><label class="form-label small text-muted">Scan QR / Masukkan Kode</label>
                            <div class="input-group input-group-lg"><span class="input-group-text bg-light"><i class="bi bi-upc-scan text-primary"></i></span>
                            <input type="text" name="kode_karcis" id="kodeInput" class="form-control form-control-lg" placeholder="KRC-XXXXXXXX" autofocus required></div>
                        </div>
                    </form>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Scanner QR Code</small>
                            <button class="btn btn-sm btn-outline-primary" id="toggleScannerBtn" onclick="toggleScanner()"><i class="bi bi-camera"></i> Toggle</button>
                        </div>
                        <div id="reader" style="width:100%; height:300px; background:#000; border-radius:8px; overflow:hidden; position:relative;">
                            <div id="scannerPlaceholder" class="text-white text-center py-5">
                                <div class="spinner-border text-primary mb-3"></div><p>Menyiapkan scanner...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-<?= $et?'warning':'success' ?> shadow-sm">
                <div class="card-header bg-<?= $et?'warning':'success' ?> text-white d-flex align-items-center"><i class="bi bi-<?= $et?'pencil-square':'plus-circle' ?> me-2"></i><h5 class="mb-0 fw-bold"><?= $et ? 'Edit Data' : 'Input Masuk' ?></h5></div>
                <div class="card-body">
                    <form method="POST" action="process/aksi.php" class="row g-3">
                        <input type="hidden" name="redirect" value="<?= $_GET['page'] ?>">
                        <input type="hidden" name="aksi" value="<?= $et?'edit_parkir':'masuk_parkir' ?>"><input type="hidden" name="page" value="transaksi">
                        <?php if($et): ?><input type="hidden" name="id_p" value="<?= $et['id_parkir'] ?>"><input type="hidden" name="id_k" value="<?= $et['id_kendaraan'] ?>"><?php endif; ?>
                        <div class="col-12"><h6 class="border-bottom pb-2 mb-3"><i class="bi bi-car-front me-2"></i>Informasi Kendaraan</h6></div>
                        <div class="col-md-4"><label class="form-label small fw-bold">Plat Nomor</label><div class="input-group"><span class="input-group-text"><i class="bi bi-tag"></i></span><input type="text" name="plat" class="form-control" value="<?= $et['plat_nomor']??'' ?>" required></div></div>
                        <div class="col-md-4"><label class="form-label small fw-bold">Warna</label><div class="input-group"><span class="input-group-text"><i class="bi bi-droplet"></i></span><input type="text" name="warna" class="form-control" value="<?= $et['warna']??'' ?>"></div></div>
                        <div class="col-md-4"><label class="form-label small fw-bold">Pemilik</label><div class="input-group"><span class="input-group-text"><i class="bi bi-person-badge"></i></span><input type="text" name="pemilik" class="form-control" value="<?= $et['pemilik']??'' ?>"></div></div>
                        <div class="col-12 mt-3"><h6 class="border-bottom pb-2 mb-3"><i class="bi bi-geo-alt me-2"></i>Detail Parkir</h6></div>
                        <div class="col-md-6"><label class="form-label small fw-bold">Jenis</label>
                            <input type="hidden" name="jenis_k" id="j_ken" value="<?= $et['jenis_kendaraan'] ?? '' ?>">
                            <select name="id_t" class="form-select" onchange="document.getElementById('j_ken').value=this.options[this.selectedIndex].text;" required>
                                <option value="">- Pilih -</option>
                                <?php foreach($tarifOptions as $rt): ?>
                                    <option value="<?= $rt['id_tarif'] ?>" <?= ($et && $et['id_tarif']==$rt['id_tarif'])?'selected':'' ?>><?= $rt['jenis_kendaraan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-bold">Area</label>
                            <select name="id_a" class="form-select" required>
                                <option value="">- Pilih -</option>
                                <?php foreach($areaOptions as $ra): 
                                    $sisa = ($ra['kapasitas'] - $ra['terisi']) + (($et && $et['id_area']==$ra['id_area'])?1:0); 
                                    $dis = $sisa <= 0 ? 'disabled' : ''; ?>
                                    <option value="<?= $ra['id_area'] ?>" <?= ($et && $et['id_area']==$ra['id_area'])?'selected':'' ?> <?= $dis ?>><?= $ra['nama_area'].($sisa <= 0 ? ' (PENUH)' : " ($sisa)") ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mt-4 d-flex gap-2">
                            <button class="btn <?= $et?'btn-warning':'btn-success' ?> flex-fill py-2"><i class="bi bi-<?= $et?'check-lg':'plus-lg' ?>"></i> <?= $et?'UPDATE':'SIMPAN' ?></button>
                            <?php if($et): ?><a href="?page=transaksi" class="btn btn-outline-secondary">Batal</a><?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"><div><i class="bi bi-list-ul me-2"></i><h5 class="mb-0 fw-bold">Daftar Parkir</h5></div><small class="opacity-75"><?= count($transaksi) ?> Aktif</small></div>
                <div class="card-body p-0"><div class="table-responsive" style="max-height: 680px;">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light sticky-top"><tr class="table-primary"><th class="ps-4">Waktu</th><th>Kendaraan</th><th>Jenis</th><th>Pemilik</th><th class="text-center">Area</th><th class="text-center">Durasi</th><th class="text-center">Biaya</th><th class="text-center pe-4">Aksi</th></tr></thead>
                        <tbody>
                            <?php foreach($transaksi as $r): 
                                // Hitung biaya real-time per kendaraan
                                $durasiJam = ceil((time() - strtotime($r['waktu_masuk'])) / 3600);
                                $biayaRealtime = $durasiJam * $r['tarif_per_jam'];
                            ?>
                            <tr class="align-middle" data-waktu-masuk="<?= strtotime($r['waktu_masuk']) ?>" data-tarif="<?= $r['tarif_per_jam'] ?>">
                                <td class="ps-4"><b><?= date('H:i',strtotime($r['waktu_masuk'])) ?></b><br><small><?= date('d/m',strtotime($r['waktu_masuk'])) ?></small></td>
                                <td><b><?= $r['plat_nomor'] ?></b><br><small class="text-muted"><?= $r['kode_karcis'] ?> <i class="bi bi-clipboard" onclick="copyKode('<?= $r['kode_karcis'] ?>')" style="cursor:pointer"></i></small></td>
                                <td><span class="badge bg-secondary"><?= $r['jenis_kendaraan'] ?></span></td> <td><div class="text-truncate" style="max-width: 100px;"><?= $r['pemilik'] ?></div></td>
                                <td class="text-center"><span class="badge bg-info text-info bg-opacity-10 border border-info"><?= $r['nama_area'] ?></span></td>
                                <td class="text-center"><small class="text-muted fw-bold"><?= $durasiJam ?> jam</small><br><small class="text-primary">Rp <?= number_format($r['tarif_per_jam'], 0, ',', '.') ?>/jam</small></td>
                                <td class="text-center text-success fw-bold">Rp <?= number_format($biayaRealtime, 0, ',', '.') ?></td>
                                <td class="text-center pe-4"><div class="btn-group btn-group-sm">
                                    
                                    <a href="?page=transaksi&edit_p=<?= $r['id_parkir'] ?>" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
                                    <a href="?page=struk&id=<?= $r['id_parkir'] ?>&from=page=transaksi" class="btn btn-info text-white"><i class="bi bi-printer"></i></a>
                                    <form method="POST" action="process/aksi.php" class="d-inline" onsubmit="return confirm('Hapus?')">
                                        <input type="hidden" name="redirect" value="<?= $_GET['page'] ?>">
                                        <input type="hidden" name="aksi" value="hapus_parkir"><input type="hidden" name="id_p" value="<?= $r['id_parkir'] ?>"><input type="hidden" name="id_k" value="<?= $r['id_kendaraan'] ?>"><input type="hidden" name="page" value="transaksi"><button class="btn btn-dark"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if(empty($transaksi)): ?><div class="text-center py-5"><i class="bi bi-car-front text-muted fs-1"></i><p class="text-muted">Kosong</p></div><?php endif; ?>
                </div></div>
            </div>
        </div>
    </div>
</div>

<div id="toast" class="toast position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:9999; display:none; min-width:300px;">
    <div class="toast-header bg-success text-white"><strong class="me-auto">Berhasil!</strong><button type="button" class="btn-close btn-close-white" onclick="hideToast()"></button></div>
    <div class="toast-body bg-light"></div>
</div>
<style>
    #reader {
    width: 100%;
    height: 300px;
    position: relative;
    margin: 0 auto;
    overflow: hidden;
    border-radius: 10px;
    background: #000;
}

#reader video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
}

#reader canvas {
    display: none;
}

/* Overlay untuk scanning area */
#reader::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 250px;
    height: 250px;
    border: 2px solid rgba(52, 152, 219, 0.8);
    border-radius: 10px;
    box-shadow: 0 0 0 2000px rgba(0, 0, 0, 0.7);
    pointer-events: none;
}

.scanner-animation {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 250px;
    height: 4px;
    background: linear-gradient(90deg, 
        transparent 0%, 
        #3498db 50%, 
        transparent 100%);
    animation: scan 2s linear infinite;
}

@keyframes scan {
    0% { top: 30%; }
    50% { top: 70%; }
    100% { top: 30%; }
}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
let html5QrCode = null, isScanning = false;

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initScanner, 1000);
    document.getElementById('kodeInput').focus();
});

function initScanner() {
    const ph = document.getElementById('scannerPlaceholder');
    try {
        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: 250, aspectRatio: 1.0 }, 
            (text) => {
                document.getElementById('kodeInput').value = text;
                showToast('QR Code berhasil di-scan!');
                html5QrCode.stop().then(() => { setTimeout(() => document.getElementById('formKeluar').submit(), 1000); });
            }, 
            () => {}
        ).then(() => {
            isScanning = true;
            document.getElementById('toggleScannerBtn').className = "btn btn-sm btn-danger";
            document.getElementById('toggleScannerBtn').innerHTML = '<i class="bi bi-camera-video-off"></i> Matikan';
            ph.style.display = 'none';
        }).catch(err => { ph.innerHTML = `<div class="alert alert-danger m-2 small">Gagal akses kamera</div>`; });
    } catch (e) { console.error(e); }
}

function toggleScanner() {
    if (isScanning) {
        html5QrCode.stop().then(() => {
            isScanning = false;
            document.getElementById('scannerPlaceholder').style.display = 'block';
            document.getElementById('scannerPlaceholder').innerHTML = `<div class="text-white text-center py-5"><i class="bi bi-camera-video-off fs-1 mb-3"></i><p>Scanner mati</p><button class="btn btn-sm btn-success" onclick="initScanner()">Nyalakan</button></div>`;
            document.getElementById('toggleScannerBtn').className = "btn btn-sm btn-outline-primary";
            document.getElementById('toggleScannerBtn').innerHTML = '<i class="bi bi-camera"></i> Toggle';
        });
    } else { initScanner(); }
}

function copyKode(k) { navigator.clipboard.writeText(k).then(() => showToast('Kode berhasil disalin!')); }

function showToast(m) {
    const t = document.getElementById('toast');
    t.querySelector('.toast-body').textContent = m;
    t.style.display = 'block';
    setTimeout(hideToast, 3000);
}

function hideToast() { document.getElementById('toast').style.display = 'none'; }

// Update biaya secara real-time setiap menit
function updateBiayaRealtime() {
    const rows = document.querySelectorAll('tbody tr[data-waktu-masuk]');
    rows.forEach(row => {
        const waktuMasukTimestamp = parseInt(row.getAttribute('data-waktu-masuk'));
        const tarifPerJam = parseInt(row.getAttribute('data-tarif'));
        
        // Hitung durasi dalam jam dari timestamp
        const sekarang = Math.floor(Date.now() / 1000); // current timestamp in seconds
        const durasiDetik = sekarang - waktuMasukTimestamp;
        const durasiJam = Math.max(1, Math.ceil(durasiDetik / 3600)); // minimal 1 jam
        
        // Hitung biaya
        const biaya = durasiJam * tarifPerJam;
        
        // Update durasi dan biaya di tabel
        const durasiCell = row.cells[5].querySelector('.text-muted');
        const biayaCell = row.cells[6];
        
        durasiCell.textContent = durasiJam + ' jam';
        biayaCell.innerHTML = '<span class="text-center text-success fw-bold">Rp ' + biaya.toLocaleString('id-ID') + '</span>';
    });
}

// Update setiap 60 detik
setInterval(updateBiayaRealtime, 60000);

// Update pertama kali saat load
updateBiayaRealtime();

document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); document.getElementById('kodeInput').focus(); }
    if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); toggleScanner(); }
});
</script>