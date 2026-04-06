<?php
$e=null;
if(isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $e = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_tarif WHERE id_tarif=$edit_id"));
}
$count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_tarif"));
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1 text-primary">Manajemen Tarif Parkir</h4><p class="text-muted mb-0 small">Kelola tarif parkir berdasarkan jenis kendaraan</p></div>
        <span class="badge bg-light text-dark fs-6 p-2"><i class="bi bi-currency-dollar me-2"></i><?= $count ?> Tarif</span>
    </div>

    <div class="card border-0 shadow-lg mb-5" style="border-radius:20px; overflow:hidden;">
        <div class="card-header bg-gradient-success text-white py-4"><div class="d-flex align-items-center">
            <div class="icon-circle bg-white text-success p-3 me-3 rounded-circle"><i class="bi bi-cash-stack fs-4"></i></div>
            <div><h5 class="mb-0 fw-bold"><?= $e?'Edit Tarif':'Tambah Tarif Baru' ?></h5><p class="mb-0 opacity-75 small"><?= $e?'Perbarui data tarif':'Tambah jenis kendaraan dan tarif baru' ?></p></div>
        </div></div>
        <div class="card-body p-4"><form method="POST" action="process/aksi.php" class="row g-4">
            <input type="hidden" name="aksi" value="<?= $e?'edit_tarif':'tambah_tarif' ?>"><input type="hidden" name="id" value="<?= $e['id_tarif']??'' ?>"><input type="hidden" name="page" value="transaksi"><input type="hidden" name="redirect" value="<?= $_GET['page'] ?>">
            <div class="col-md-5"><label class="form-label fw-semibold text-muted small">Jenis Kendaraan</label><div class="input-group input-group-lg"><span class="input-group-text bg-light border-end-0"><i class="bi bi-car-front text-success"></i></span><select class="form-select border-start-0 ps-2" name="jenis" required style="height:50px;">
                        <?php $jenisOptions = ['mobil' => 'Mobil', 'motor' => 'Motor', 'lainnya' => 'Lainnya']; ?>
                        <option value="" disabled <?= empty($e['jenis_kendaraan']) ? 'selected' : '' ?>>Pilih jenis kendaraan</option>
                        <?php foreach($jenisOptions as $value => $label): ?>
                            <option value="<?= $value ?>" <?= isset($e['jenis_kendaraan']) && $e['jenis_kendaraan'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select></div></div>
            <div class="col-md-5"><label class="form-label fw-semibold text-muted small">Tarif per Jam</label><div class="input-group input-group-lg"><span class="input-group-text bg-light border-end-0"><i class="bi bi-clock text-success"></i></span><input type="number" class="form-control border-start-0 ps-2" name="tarif" placeholder="Masukkan angka" value="<?= $e['tarif_per_jam']??'' ?>" required min="1000" max="1000000" step="500" style="height:50px;"><span class="input-group-text bg-light">/ jam</span></div></div>
            <div class="col-md-2 d-flex align-items-end"><button class="btn btn-lg btn-gradient-success w-100 fw-bold py-3 shadow-sm" style="border-radius:12px;"><i class="bi bi-<?= $e?'check-circle':'plus-circle' ?> me-2"></i><?= $e?'Update':'Tambah' ?></button></div>
        </form></div>
    </div>

    <div class="card border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
        <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Daftar Tarif Parkir</h5>
            <div class="input-group" style="width:250px;"><span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span><input type="text" class="form-control border-start-0" placeholder="Cari tarif..."></div>
        </div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="bg-light"><tr><th class="ps-4">Jenis Kendaraan</th><th>Tarif per Jam</th><th class="text-end pe-4">Aksi</th></tr></thead>
            <tbody>
                <?php $q = mysqli_query($conn, "SELECT * FROM tb_tarif ORDER BY tarif_per_jam ASC"); while($r = mysqli_fetch_assoc($q)): 
                    $jns = strtolower($r['jenis_kendaraan']);
                    $icon = strpos($jns,'motor')!==false?'bi-bicycle':(strpos($jns,'bus')!==false||strpos($jns,'truk')!==false?'bi-truck':'bi-car-front');
                    $color = strpos($jns,'motor')!==false?'bg-info':(strpos($jns,'bus')!==false||strpos($jns,'truk')!==false?'bg-warning':'bg-success');
                ?>
                <tr class="border-bottom">
                    <td class="ps-4 py-4"><div class="d-flex align-items-center"><div class="<?= $color ?> text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px; height:40px;"><i class="bi <?= $icon ?> fs-5"></i></div>
                        <div><h6 class="mb-0 fw-semibold"><?= $r['jenis_kendaraan'] ?></h6><small class="text-muted">T<?= str_pad($r['id_tarif'], 3, '0', STR_PAD_LEFT) ?></small></div></div></td>
                    <td><div class="d-flex align-items-center"><div class="bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 me-3 small fw-bold">Per Jam</div>
                        <div><h5 class="mb-0 fw-bold text-success">Rp <?= number_format($r['tarif_per_jam'],0,',','.') ?></h5><small class="text-muted">Rp <?= number_format($r['tarif_per_jam']*24,0,',','.') ?> / hari</small></div></div></td>
                    <td class="text-end pe-4"><div class="d-flex justify-content-end gap-2">
                        <a href="?page=tarif&edit=<?= $r['id_tarif'] ?>" class="btn btn-warning btn-lg px-3 py-2" style="border-radius:10px;"><i class="bi bi-pencil-square"></i></a>
                        <form method="POST" action="process/aksi.php" class="d-inline" onsubmit="return confirm('Hapus tarif <?= $r['jenis_kendaraan'] ?>?')">
                            <input type="hidden" name="redirect" value="<?= $_GET['page'] ?>"><input type="hidden" name="aksi" value="hapus_tarif"><input type="hidden" name="id" value="<?= $r['id_tarif'] ?>">
                            <button class="btn btn-danger btn-lg px-3 py-2" style="border-radius:10px;"><i class="bi bi-trash3"></i></button>
                        </form>
                    </div></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table></div>
        <div class="card-footer bg-white border-0 py-4"><div class="row align-items-center">
            <div class="col-md-8"><div class="alert alert-info mb-0 d-flex small"><i class="bi bi-info-circle-fill fs-5 me-3"></i><div><b>Informasi Tarif:</b> Tarif otomatis digunakan saat transaksi parkir.</div></div></div>
            <div class="col-md-4 text-end small">
                <div class="text-muted">Total: <b><?= $count ?> jenis</b></div>
                <div class="text-success fw-bold">Rata-rata: Rp <?= number_format(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(tarif_per_jam) as a FROM tb_tarif"))['a'],0,',','.') ?>/jam</div>
            </div>
        </div></div>
    </div>
</div>

<style>
    .card{transition:transform .3s ease,box-shadow .3s ease}.card:hover{transform:translateY(-2px)} 
    .btn-gradient-success,.bg-gradient-success{background:linear-gradient(135deg, #2a3d61 0%, #1a1b4e 100%);border:none;color:#fff}
    .btn-gradient-success:hover{background:linear-gradient(135deg,  #10548b 0%, #b8bcf1 100%);transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.15)}
    .table th{font-weight:600;letter-spacing:.5px}.table tbody tr:hover{background-color:rgba(25,135,84,.05)}
    .icon-circle{display:flex;align-items:center;justify-content:center;width:60px;height:60px}
    input:focus{border-color:#00b09b!important;box-shadow:0 0 0 .25rem rgba(0,176,155,.25)!important}
    .alert{border-radius:12px;background:rgba(13,202,240,.1);border:none}
</style>