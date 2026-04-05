<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Log Aktivitas Sistem</h4>
            <p class="text-muted mb-0">Rekaman semua aktivitas yang dilakukan di sistem</p>
        </div>
        <div>
            <div class="d-flex gap-3">
                <div class="text-center">
                    <div class="badge bg-light text-dark fs-6 p-2">
                        <i class="bi bi-clock-history me-2"></i>
                        <?php 
                        $totalLogs = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_log_aktivitas"));
                        echo number_format($totalLogs) . " Log";
                        ?>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Semua Aktivitas</a></li>
                        <li><a class="dropdown-item" href="#">Hari Ini</a></li>
                        <li><a class="dropdown-item" href="#">Minggu Ini</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Hanya Admin</a></li>
                        <li><a class="dropdown-item" href="#">Hanya Petugas</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Card -->
    <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-white border-0 py-4 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-activity me-2 text-primary"></i>
                        Riwayat Aktivitas Terbaru
                    </h5>
                    <p class="text-muted mb-0 small">50 aktivitas terakhir</p>
                </div>
                <div class="d-flex">
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari aktivitas...">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase text-muted small fw-semibold" style="width: 20%;">
                            <i class="bi bi-clock me-1"></i> Waktu
                        </th>
                        <th class="py-3 text-uppercase text-muted small fw-semibold" style="width: 25%;">
                            <i class="bi bi-person me-1"></i> User
                        </th>
                        <th class="py-3 text-uppercase text-muted small fw-semibold" style="width: 55%;">
                            <i class="bi bi-activity me-1"></i> Aktivitas
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Mengambil data log dan join dengan tabel user untuk mendapatkan nama lengkap
                    $q = mysqli_query($conn, "SELECT l.*, u.nama_lengkap, u.role FROM tb_log_aktivitas l 
                                             JOIN tb_user u ON l.id_user = u.id_user 
                                             ORDER BY l.id_log DESC LIMIT 50");
                    
                    while($r = mysqli_fetch_assoc($q)):
                        // Memformat waktu
                        $waktu = date('d M Y', strtotime($r['waktu_aktivitas']));
                        $jam = date('H:i', strtotime($r['waktu_aktivitas']));
                        
                        // Tentukan icon berdasarkan aktivitas
                        $activityIcon = 'bi-activity';
                        $activityColor = 'text-primary';
                        
                        // Deteksi jenis aktivitas untuk icon yang sesuai
                        $activity = strtolower($r['aktivitas']);
                        if (strpos($activity, 'login') !== false) {
                            $activityIcon = 'bi-box-arrow-in-right';
                            $activityColor = 'text-success';
                        } elseif (strpos($activity, 'logout') !== false) {
                            $activityIcon = 'bi-box-arrow-right';
                            $activityColor = 'text-danger';
                        } elseif (strpos($activity, 'tambah') !== false) {
                            $activityIcon = 'bi-plus-circle';
                            $activityColor = 'text-success';
                        } elseif (strpos($activity, 'edit') !== false || strpos($activity, 'update') !== false) {
                            $activityIcon = 'bi-pencil-square';
                            $activityColor = 'text-warning';
                        } elseif (strpos($activity, 'hapus') !== false || strpos($activity, 'delete') !== false) {
                            $activityIcon = 'bi-trash';
                            $activityColor = 'text-danger';
                        } elseif (strpos($activity, 'transaksi') !== false) {
                            $activityIcon = 'bi-cash';
                            $activityColor = 'text-info';
                        } elseif (strpos($activity, 'parkir') !== false) {
                            $activityIcon = 'bi-car-front';
                            $activityColor = 'text-primary';
                        }
                        
                        // Badge role user
                        $roleClass = 'bg-secondary';
                        if ($r['role'] == 'admin') {
                            $roleClass = 'bg-danger';
                        } elseif ($r['role'] == 'petugas') {
                            $roleClass = 'bg-info';
                        } elseif ($r['role'] == 'owner') {
                            $roleClass = 'bg-success';
                        }
                    ?>
                    <tr class="border-bottom">
                        <td class="ps-4 py-4">
                            <div class="d-flex align-items-center">
                                <div class="time-circle me-3">
                                    <div class="bg-light text-dark rounded-circle d-flex align-item-center justify-content-center" 
                                         style="width: 40px; height: 40px; border: 2px solid #e9ecef;">
                                        <i class="bi bi-calendar3 text-muted"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold"><?= $waktu ?></h6>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i> <?= $jam ?>
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="py-4">
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                        <?= strtoupper(substr($r['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold"><?= $r['nama_lengkap'] ?></h6>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge <?= $roleClass ?> px-2 py-1 small">
                                            <?= ucfirst($r['role']) ?>
                                        </span>
                                        <small class="text-muted">
                                            ID: U<?= str_pad($r['id_user'], 3, '0', STR_PAD_LEFT) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4">
                            <div class="d-flex align-items-center">
                                <div class="activity-icon me-3">
                                    <div class="<?= $activityColor ?> rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; background-color: rgba(var(--bs-primary-rgb), 0.1);">
                                        <i class="bi <?= $activityIcon ?> fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-medium" style="color: #333; line-height: 1.5;">
                                        <?= $r['aktivitas'] ?>
                                    </p>
                                    <small class="text-muted">
                                        <?php
                                        // Tampilkan waktu relatif (berapa lama yang lalu)
                                        $timeAgo = time() - strtotime($r['waktu_aktivitas']);
                                        if ($timeAgo < 60) {
                                            echo 'Baru saja';
                                        } elseif ($timeAgo < 3600) {
                                            echo floor($timeAgo / 60) . ' menit yang lalu';
                                        } elseif ($timeAgo < 86400) {
                                            echo floor($timeAgo / 3600) . ' jam yang lalu';
                                        } else {
                                            echo floor($timeAgo / 86400) . ' hari yang lalu';
                                        }
                                        ?>
                                    </small>
                                </div>
                                <div class="ms-3">
                                    <span class="badge bg-light text-muted border px-3 py-2">
                                        <i class="bi bi-hash me-1"></i>
                                        <?= str_pad($r['id_log'], 5, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-white border-0 py-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="alert alert-info mb-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle-fill fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="alert-heading mb-1">Informasi Log</h6>
                                <p class="mb-0 small">
                                    Sistem secara otomatis mencatat semua aktivitas yang dilakukan pengguna. 
                                    Data log digunakan untuk audit dan monitoring penggunaan sistem.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light rounded p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Aktivitas Hari Ini:</span>
                            <span class="fw-bold text-primary">
                                <?php
                                $today = date('Y-m-d');
                                $todayQuery = mysqli_query($conn, 
                                    "SELECT COUNT(*) as total FROM tb_log_aktivitas 
                                     WHERE DATE(waktu_aktivitas) = '$today'");
                                $todayData = mysqli_fetch_assoc($todayQuery);
                                echo number_format($todayData['total'] ?? 0);
                                ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Aktivitas Kemarin:</span>
                            <span class="fw-bold">
                                <?php
                                $yesterday = date('Y-m-d', strtotime('-1 day'));
                                $yesterdayQuery = mysqli_query($conn, 
                                    "SELECT COUNT(*) as total FROM tb_log_aktivitas 
                                     WHERE DATE(waktu_aktivitas) = '$yesterday'");
                                $yesterdayData = mysqli_fetch_assoc($yesterdayQuery);
                                echo number_format($yesterdayData['total'] ?? 0);
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
    
    .table th {
        font-weight: 600;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    
    .time-circle, .user-avatar, .activity-icon {
        flex-shrink: 0;
    }
    
    input:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25) !important;
    }
    
    .alert {
        border-radius: 12px;
        border: none;
        background-color: rgba(13, 202, 240, 0.1);
    }
    
    .badge {
        font-weight: 500;
    }
    
    .dropdown-toggle {
        border-radius: 10px;
        padding: 0.375rem 0.75rem;
    }
    
    .text-success { color: #28a745 !important; }
    .text-danger { color: #dc3545 !important; }
    .text-warning { color: #ffc107 !important; }
    .text-info { color: #17a2b8 !important; }
    .text-primary { color: #667eea !important; }
</style>