<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

include __DIR__ . '/koneksi.php';

// Fungsi untuk log aktivitas
function logAktivitas($conn, $id_user, $aktivitas) {
    $aktivitas = mysqli_real_escape_string($conn, $aktivitas);
    mysqli_query($conn, "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) 
                        VALUES ($id_user, '$aktivitas', NOW())");
}

if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = mysqli_real_escape_string($conn, $_POST['password']);

    $q = mysqli_query($conn, "SELECT * FROM tb_user 
        WHERE username='$u' AND password='$p' AND status_aktif=1");

    if (mysqli_num_rows($q) > 0) {
        $d = mysqli_fetch_assoc($q);
        $_SESSION['role'] = $d['role'];
        $_SESSION['nama'] = $d['nama_lengkap'];
        $_SESSION['id_user'] = $d['id_user'];
        
        // Log login
        logAktivitas($conn, $d['id_user'], "Login ke sistem");
        
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}

if (isset($_GET['logout'])) {
    // Log logout sebelum destroy session
    if(isset($_SESSION['id_user'])) {
        logAktivitas($conn, $_SESSION['id_user'], "Logout dari sistem");
    }
    
    session_destroy();
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'] ?? null;
$uid  = $_SESSION['id_user'] ?? 0;
$page = $_GET['page'] ?? 'dashboard';
