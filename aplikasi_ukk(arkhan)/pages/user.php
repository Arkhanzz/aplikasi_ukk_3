<?php
$e=null;
if(isset($_GET['edit'])) $e=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_user WHERE id_user='$_GET[edit]'"));
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1 text-dark">Manajemen Pengguna</h4><p class="text-muted mb-0">Kelola data pengguna sistem</p></div>
        <span class="badge bg-light text-dark fs-6 p-2"><i class="bi bi-people-fill me-2"></i><?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_user")) ?> Pengguna</span>
    </div>

    <div class="card border-0 shadow-lg mb-5" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-gradient-primary text-white py-4"><div class="d-flex align-items-center">
            <div class="icon-circle bg-white text-primary p-3 me-3 rounded-circle"><i class="bi bi-person-plus-fill fs-4"></i></div>
            <div><h5 class="mb-0 fw-bold"><?= $e?'Edit Pengguna':'Tambah Pengguna Baru' ?></h5><p class="mb-0 opacity-75"><?= $e?'Perbarui data pengguna':'Tambahkan pengguna baru ke sistem' ?></p></div>
        </div></div>
        <div class="card-body p-4">
            <form method="POST" action="process/aksi.php" class="row g-4">
                <input type="hidden" name="redirect" value="<?= $_GET['page'] ?>">
                <input type="hidden" name="aksi" value="<?= $e?'edit_user':'tambah_user' ?>"><input type="hidden" name="id" value="<?= $e['id_user']??'' ?>">
                <div class="col-md-3"><label class="form-label fw-semibold text-muted small">Nama Lengkap</label><div class="input-group input-group-lg"><span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-primary"></i></span><input class="form-control border-start-0 ps-2" name="nama" placeholder="Nama lengkap" value="<?= $e['nama_lengkap']??'' ?>" required style="height:50px;"></div></div>
                <div class="col-md-3"><label class="form-label fw-semibold text-muted small">Username</label><div class="input-group input-group-lg"><span class="input-group-text bg-light border-end-0"><i class="bi bi-at text-primary"></i></span><input class="form-control border-start-0 ps-2" name="user" placeholder="Username" value="<?= $e['username']??'' ?>" required style="height:50px;"></div></div>
                <div class="col-md-2"><label class="form-label fw-semibold text-muted small">Password</label><div class="input-group input-group-lg"><span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-primary"></i></span><input class="form-control border-start-0 ps-2" name="pass" type="password" value="<?= $e['password']??'' ?>" required style="height:50px;"></div></div>
                <div class="col-md-2"><label class="form-label fw-semibold text-muted small">Role</label><div class="input-group input-group-lg"><span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check text-primary"></i></span>
                    <select class="form-select border-start-0 ps-2" name="role_u" style="height: 50px;">
                        <?php foreach(['admin','petugas','owner'] as $r): ?>
                        <option value="<?= $r ?>" <?= ($e && $e['role'] == $r) ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div></div>
                <div class="col-md-2 d-flex align-items-end"><button class="btn btn-lg btn-gradient-primary w-100 fw-bold py-3 shadow-sm" style="border-radius:12px;"><i class="bi bi-<?= $e?'check-circle':'save' ?> me-2"></i><?= $e?'Update':'Simpan' ?></button></div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Daftar Pengguna</h5>
            <div class="input-group" style="width: 250px;"><span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span><input type="text" class="form-control border-start-0" placeholder="Cari..."></div>
        </div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="bg-light"><tr><th class="ps-4">Nama Lengkap</th><th>Username</th><th>Role</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
                <?php $q=mysqli_query($conn,"SELECT * FROM tb_user"); while($r=mysqli_fetch_assoc($q)): 
                    $roleSet = ['admin' => ['danger', 'shield-check'], 'petugas' => ['info', 'person-workspace'], 'owner' => ['success', 'person-badge']];
                    $set = $roleSet[$r['role']] ?? ['secondary', 'person'];
                ?>
                <tr class="border-bottom">
                    <td class="ps-4 py-4"><div class="d-flex align-items-center"><div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px; height:40px; font-weight:bold;"><?= strtoupper(substr($r['nama_lengkap'], 0, 1)) ?></div><h6 class="mb-0 fw-semibold"><?= $r['nama_lengkap'] ?></h6></div></td>
                    <td><i class="bi bi-person-circle me-2 text-muted"></i><span class="fw-medium"><?= $r['username'] ?></span></td>
                    <td><span class="badge bg-<?= $set[0] ?> bg-gradient px-3 py-2 fw-semibold text-capitalize"><i class="bi bi-<?= $set[1] ?> me-1"></i><?= $r['role'] ?></span></td>
                    <td class="text-center"><div class="d-flex justify-content-center gap-2">
                        <a href="?page=user&edit=<?= $r['id_user'] ?>" class="btn btn-warning btn-lg px-3 py-2" style="border-radius:10px;"><i class="bi bi-pencil-square"></i></a>
                        <form method="POST" action="process/aksi.php" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                            <input type="hidden" name="aksi" value="hapus_user"><input type="hidden" name="id" value="<?= $r['id_user'] ?>">
                            <button class="btn btn-danger btn-lg px-3 py-2" style="border-radius:10px;"><i class="bi bi-trash3"></i></button>
                        </form>
                    </div></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table></div>
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center small text-muted">
            <span><i class="bi bi-info-circle me-1"></i>Kelola data pengguna via tombol aksi</span>
            <span>Update: <?= date('d M Y H:i') ?></span>
        </div>
    </div>
</div>

<style>
    .card{transition:transform .3s ease,box-shadow .3s ease}.card:hover{transform:translateY(-2px)}
    .btn-gradient-primary,.bg-gradient-primary{background:linear-gradient(135deg,#fff00f 0%,#dab45c 100%);border:none;color:#fff}
    .btn-gradient-primary:hover{background:linear-gradient(135deg,#fff00f 0%,#dab45c 100%);transform:translateY(-1px);box-shadow:0 4px 12px rgba(239, 249, 46, 0.67)}
    .table th{font-weight:600;letter-spacing:.5px}.table tbody tr:hover{background-color:rgba(234, 208, 102, 0.63)}
    .icon-circle{display:flex;align-items:center;justify-content:center;width:60px;height:60px}
    input:focus,select:focus{border-color:#fff00f!important;box-shadow:0 0 0 .25rem rgba(251, 201, 74, 0.77)!important}
</style>