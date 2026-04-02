<div class="container-fluid px-0">
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary shadow-sm sticky-top">
        <div class="container-fluid px-4">
            <!-- Logo & Brand -->
            <a class="navbar-brand d-flex align-items-center fw-bold" href="?page=dashboard">
                <div class="brand-logo me-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px;">
                        <i class="bi bi-p-square-fill fs-5"></i>
                    </div>
                </div>
                <div>
                    <span class="fs-4">UKK PARKIR</span>
                    <small class="d-block text-white-50" style="font-size: 0.7rem; margin-top: -2px;">
                        Parking Management System
                    </small>
                </div>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Menu Items -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- Dashboard -->
                    <li class="nav-item mx-1">
                        <a class="nav-link <?= $page=='dashboard'?'active':'' ?>" href="?page=dashboard">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Dashboard
                        </a>
                    </li>

                    <!-- Admin Menu -->
                    <?php if($role=='admin'): ?>
                        <li class="nav-item dropdown mx-1">
                            <a class="nav-link dropdown-toggle <?= in_array($page, ['user','tarif','area','log'])?'active':'' ?>" 
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-gear me-1"></i>
                                Admin
                            </a>
                            <ul class="dropdown-menu dropdown-menu-start">
                                <li>
                                    <a class="dropdown-item <?= $page=='user'?'active':'' ?>" href="?page=user">
                                        <i class="bi bi-people me-2"></i>
                                        User Management
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= $page=='tarif'?'active':'' ?>" href="?page=tarif">
                                        <i class="bi bi-cash-coin me-2"></i>
                                        Tarif Parkir
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= $page=='area'?'active':'' ?>" href="?page=area">
                                        <i class="bi bi-p-square me-2"></i>
                                        Area Parkir
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item <?= $page=='log'?'active':'' ?>" href="?page=log">
                                        <i class="bi bi-activity me-2"></i>
                                        Log Aktivitas
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- Transaksi (Non-Owner) -->
                    <?php if($role!='owner'): ?>
                        <li class="nav-item mx-1">
                            <a class="nav-link <?= $page=='transaksi'?'active':'' ?>" href="?page=transaksi">
                                <i class="bi bi-car-front me-1"></i>
                                Transaksi
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Riwayat -->
                    <li class="nav-item mx-1">
                        <a class="nav-link <?= $page=='riwayat'?'active':'' ?>" href="?page=riwayat">
                            <i class="bi bi-clock-history me-1"></i>
                            Riwayat
                        </a>
                    </li>

                    <!-- Laporan (Owner & Admin) -->
                    <?php if($role=='owner' || $role=='admin' || $role=='petugas'): ?>
                        <li class="nav-item mx-1">
                            <a class="nav-link <?= $page=='rekap'?'active':'' ?>" href="?page=rekap">
                                <i class="bi bi-bar-chart me-1"></i>
                                Laporan
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- User Info & Actions -->
                <div class="d-flex align-items-center">
                    <!-- User Profile -->
                    <div class="dropdown me-3">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none" 
                           data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">
                                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 35px; height: 35px; font-size: 14px; font-weight: bold;">
                                    <?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?>
                                </div>
                            </div>
                            <div class="d-none d-lg-block">
                                <span class="fw-semibold"><?= $_SESSION['nama'] ?></span>
                                <small class="d-block text-white-50" style="font-size: 0.7rem;">
                                    <?= ucfirst($role) ?>
                                </small>
                            </div>
                        </a>
                        
                    </div>

                    <!-- Logout Button -->
                    <a class="btn btn-outline-light btn-sm fw-bold" href="?logout=1">
                        <i class="bi bi-box-arrow-right me-1"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb (Optional) -->
    <div class="container-fluid px-4 py-2 bg-light border-bottom">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="?page=dashboard" class="text-decoration-none">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                </li>
                <?php if($page != 'dashboard'): ?>
                    <li class="breadcrumb-item active text-capitalize" aria-current="page">
                        <?= str_replace('_', ' ', $page) ?>
                    </li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</div>

<style>
    /* Navbar Styling */
    .navbar {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        background: linear-gradient(135deg, #1f257c 0%, #070614 100%) !important;
    }

    .nav-link {
        border-radius: 8px;
        padding: 0.5rem 1rem !important;
        transition: all 0.2s ease;
        color: rgba(255, 255, 255, 0.85) !important;
        font-weight: 500;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.15);
        color: white !important;
        transform: translateY(-1px);
    }

    .nav-link.active {
        background-color: white !important;
        color: #667eea !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Dropdown Styling */
    .dropdown-menu {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        padding: 0.5rem 0;
        margin-top: 0.5rem;
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        margin: 0.125rem 0.5rem;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .dropdown-item:hover {
        background-color: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .dropdown-item.active {
        background-color: rgba(102, 126, 234, 0.15);
        color: #667eea;
    }

    /* User Avatar */
    .user-avatar {
        flex-shrink: 0;
    }

    .user-avatar div {
        transition: transform 0.2s ease;
    }

    .user-avatar:hover div {
        transform: scale(1.1);
    }

    /* Brand Logo */
    .brand-logo {
        flex-shrink: 0;
    }

    /* Breadcrumb */
    .breadcrumb {
        margin-bottom: 0;
        padding: 0.5rem 1rem;
        background-color: transparent;
        border-radius: 8px;
    }

    .breadcrumb-item a {
        color: #667eea;
        font-weight: 500;
    }

    .breadcrumb-item.active {
        color: #6c757d;
        font-weight: 500;
    }

    /* Mobile Responsive */
    @media (max-width: 992px) {
        .navbar-nav {
            padding-top: 1rem;
        }
        
        .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .dropdown-menu {
            border: 1px solid rgba(0, 0, 0, 0.1);
            margin-left: 1rem;
        }
    }

    /* Button Styling */
    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
    }

    /* Badge in Dropdown */
    .badge {
        font-size: 0.6rem;
        padding: 0.2rem 0.4rem;
    }
</style>

<script>
// Add active class to parent dropdown when child is active
document.addEventListener('DOMContentLoaded', function() {
    const activeLinks = document.querySelectorAll('.nav-link.active');
    activeLinks.forEach(link => {
        // If active link is in a dropdown, also highlight the parent dropdown
        const parentDropdown = link.closest('.dropdown');
        if (parentDropdown) {
            const dropdownToggle = parentDropdown.querySelector('.dropdown-toggle');
            if (dropdownToggle) {
                dropdownToggle.classList.add('active');
            }
        }
    });
});
</script>