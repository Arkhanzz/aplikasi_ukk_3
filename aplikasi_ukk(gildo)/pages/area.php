<?php
// Prevent SQL Injection via GET parameter
if(isset($_GET['edit'])){
    $edit_id = (int)$_GET['edit'];
    $e = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_area_parkir WHERE id_area=$edit_id"));
} else {
    $e = null;
}
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary">Manajemen Area Parkir</h4>
            <p class="text-muted mb-0">Kelola area parkir dan kapasitas kendaraan</p>
        </div>
        <div>
            <span class="badge bg-light text-dark fs-6 p-2">
                <i class="bi bi-p-square me-2"></i>
                <?php 
                $count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_area_parkir"));
                echo $count . " Area";
                ?>
            </span>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-lg mb-5" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-gradient-primary text-white py-4">
            <div class="d-flex align-items-center">
                <div class="icon-circle bg-white text-primary p-3 me-3 rounded-circle">
                    <i class="bi bi-plus-square-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold"><?= $e?'Edit Area':'Tambah Area Baru' ?></h5>
                    <p class="mb-0 opacity-75"><?= $e?'Perbarui data area':'Tambah area parkir baru' ?></p>
                </div>
            </div>
        </div>
        
        <div class="card-body p-4">
            <form method="POST" action="process/aksi.php" class="row g-4">
                <input type="hidden" name="redirect" value="<?= $_GET['page'] ?>">
                <input type="hidden" name="aksi" value="<?= $e?'edit_area':'tambah_area' ?>">
                <input type="hidden" name="id" value="<?= $e['id_area']??'' ?>">

                <!-- Nama Area -->
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-muted small">Nama Area</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-geo-alt text-primary"></i>
                        </span>
                        <input class="form-control border-start-0 ps-2" name="nama_a" 
                               placeholder="Contoh: Area A, Basement, Lantai 1, dll." 
                               value="<?= $e['nama_area']??'' ?>" required
                               style="height: 50px;">
                    </div>
                </div>

                <!-- Kapasitas -->
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-muted small">Kapasitas Kendaraan</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-car-front-fill text-primary"></i>
                        </span>
                        <input type="number" class="form-control border-start-0 ps-2" name="kapasitas" 
                               placeholder="Jumlah maksimal kendaraan" 
                               value="<?= $e['kapasitas']??'' ?>" required min="1" max="10000"
                               style="height: 50px;">
                        <span class="input-group-text bg-light">slot</span>
                    </div>
                    <div class="form-text text-muted ms-2">
                        <small>Masukkan jumlah slot parkir yang tersedia</small>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-lg btn-gradient-primary w-100 fw-bold py-3 shadow-sm" 
                            style="border-radius: 12px;">
                        <i class="bi bi-<?= $e?'check-circle':'save' ?> me-2"></i>
                        <?= $e?'Update':'Simpan' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-white border-0 py-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Daftar Area Parkir</h5>
                <div class="d-flex">
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari area...">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase text-muted small fw-semibold">Nama Area</th>
                        <th class="py-3 text-uppercase text-muted small fw-semibold">Kapasitas</th>
                        <th class="py-3 text-uppercase text-muted small fw-semibold text-center">Status</th>
                        <th class="py-3 text-uppercase text-muted small fw-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $q = mysqli_query($conn, "SELECT * FROM tb_area_parkir ORDER BY nama_area"); 
                    while($r = mysqli_fetch_assoc($q)): 
                        $terisi = $r['terisi'] ?? 0;
                        $kapasitas = $r['kapasitas'];
                        $persenTerisi = $kapasitas > 0 ? round(($terisi / $kapasitas) * 100) : 0;
                        
                        // Tentukan warna berdasarkan persentase terisi
                        $statusClass = 'bg-success';
                        $statusIcon = 'bi-check-circle';
                        $statusText = 'Tersedia';
                        
                        if ($persenTerisi >= 100) {
                            $statusClass = 'bg-danger';
                            $statusIcon = 'bi-x-circle';
                            $statusText = 'Penuh';
                        } elseif ($persenTerisi >= 80) {
                            $statusClass = 'bg-warning';
                            $statusIcon = 'bi-exclamation-circle';
                            $statusText = 'Hampir Penuh';
                        }
                    ?>
                    <tr class="border-bottom">
                        <td class="ps-4 py-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-p-square fs-5"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold"><?= $r['nama_area'] ?></h6>
                                    <small class="text-muted">
                                        Kode: A<?= str_pad($r['id_area'], 3, '0', STR_PAD_LEFT) ?>
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="py-4">
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Terisi: <?= $terisi ?> / <?= $kapasitas ?></small>
                                    <small class="fw-semibold <?= $persenTerisi >= 100 ? 'text-danger' : 'text-success' ?>">
                                        <?= $persenTerisi ?>%
                                    </small>
                                </div>
                                <div class="progress" style="height: 8px; width: 200px;">
                                    <div class="progress-bar <?= $statusClass ?>" 
                                         role="progressbar" 
                                         style="width: <?= min($persenTerisi, 100) ?>%"
                                         aria-valuenow="<?= $persenTerisi ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 text-center">
                            <span class="badge <?= $statusClass ?> px-3 py-2 fw-semibold">
                                <i class="bi <?= $statusIcon ?> me-1"></i>
                                <?= $statusText ?>
                            </span>
                            <div class="mt-1 small text-muted">
                                <?= ($kapasitas - $terisi) ?> slot tersedia
                            </div>
                        </td>
                        <td class="py-4 text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <!-- Detail Button -->
                                <a href="?page=area_detail&id=<?= $r['id_area'] ?>" 
                                   class="btn btn-info btn-lg px-3 py-2 d-flex align-items-center" 
                                   style="border-radius: 10px;" 
                                   title="Lihat Detail">
                                    <i class="bi bi-eye fs-6"></i>
                                    <span class="ms-1 d-none d-md-inline">Detail</span>
                                </a>
                                
                                <!-- Edit Button -->
                                <a href="?page=area&edit=<?= $r['id_area'] ?>" 
                                   class="btn btn-warning btn-lg px-3 py-2 d-flex align-items-center" 
                                   style="border-radius: 10px;" 
                                   title="Edit Area">
                                    <i class="bi bi-pencil-square fs-6"></i>
                                    <span class="ms-1 d-none d-md-inline">Edit</span>
                                </a>
                                
                                <!-- Delete Button -->
                                <form method="POST" action="process/aksi.php" 
                                      class="d-inline" 
                                      onsubmit="return confirm('Yakin hapus area <?= $r['nama_area'] ?>?')">
                                      <input type="hidden" name="redirect" value="<?= $_GET['page'] ?>">
                                    <input type="hidden" name="aksi" value="hapus_area">
                                    <input type="hidden" name="id" value="<?= $r['id_area'] ?>">
                                    <button class="btn btn-danger btn-lg px-3 py-2 d-flex align-items-center" 
                                            style="border-radius: 10px;" 
                                            title="Hapus Area">
                                        <i class="bi bi-trash3 fs-6"></i>
                                        <span class="ms-1 d-none d-md-inline">Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-white border-0 py-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-info mb-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle-fill fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="alert-heading mb-1">Penggunaan Area</h6>
                                <p class="mb-0 small">
                                    • <span class="badge bg-success">Hijau</span>: Tersedia (>80% slot kosong)<br>
                                    • <span class="badge bg-warning">Kuning</span>: Hampir penuh (80-99% terisi)<br>
                                    • <span class="badge bg-danger">Merah</span>: Penuh (100% terisi)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light rounded p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Area:</span>
                            <span class="fw-bold"><?= $count ?> area</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Kapasitas:</span>
                            <span class="fw-bold text-primary">
                                <?php
                                $totalQuery = mysqli_query($conn, "SELECT SUM(kapasitas) as total FROM tb_area_parkir");
                                $totalData = mysqli_fetch_assoc($totalQuery);
                                echo number_format($totalData['total'] ?? 0, 0, ',', '.') . " slot";
                                ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Terpakai:</span>
                            <span class="fw-bold">
                                <?php
                                $terisiQuery = mysqli_query($conn, "SELECT SUM(terisi) as total FROM tb_area_parkir");
                                $terisiData = mysqli_fetch_assoc($terisiQuery);
                                $totalTerisi = $terisiData['total'] ?? 0;
                                echo number_format($totalTerisi, 0, ',', '.') . " slot";
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .btn-gradient-primary {
        background: linear-gradient(135deg, #40425c 0%, #0e0846 100%);
        border: none;
        color: white;
    }
    
    .btn-gradient-primary:hover {
        background: linear-gradient(135deg, #10548b 0%, #b8bcf1 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .table th {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    
    .avatar-circle {
        flex-shrink: 0;
    }
    
    .icon-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #40425c 0%, #0e0846 100%);
    }
    
    input:focus, select:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25) !important;
    }
    
    .alert {
        border-radius: 12px;
        border: none;
        background-color: rgba(13, 202, 240, 0.1);
    }
    
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        border-radius: 10px;
        transition: width 0.3s ease;
    }
</style>