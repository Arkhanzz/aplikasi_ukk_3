<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

include __DIR__ . '/koneksi.php';

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
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'] ?? null;
$uid  = $_SESSION['id_user'] ?? 0;
$page = $_GET['page'] ?? 'dashboard';
