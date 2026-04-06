<?php
include '../config/session.php';

// Role-based Access Control
if(!isset($role) || ($role != 'admin' && $role != 'petugas')) {
    exit("<script>alert('Akses ditolak - Anda tidak memiliki izin');history.back();</script>");
}

$aksi = $_POST['aksi'] ?? '';

/* ================= UTIL ================= */

function areaPenuh($conn,$id){
    $a = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT kapasitas,terisi FROM tb_area_parkir WHERE id_area='$id'"
    ));
    return $a['terisi'] >= $a['kapasitas'];
}

function updateArea($conn,$id,$val){
    // Prevent negative capacity
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT terisi FROM tb_area_parkir WHERE id_area='$id'"));
    $new_terisi = $cek['terisi'] + $val;
    if($new_terisi < 0) {
        error_log("Area capacity error: terisi would be negative for id_area=$id");
        return false;
    }
    mysqli_query($conn, "UPDATE tb_area_parkir SET terisi = $new_terisi WHERE id_area='$id'");
    return true;
}

function hitungJamTagih($waktu_masuk, $waktu_keluar=null){
    if($waktu_keluar === null) $waktu_keluar = date('Y-m-d H:i:s');
    $diff = (strtotime($waktu_keluar) - strtotime($waktu_masuk)) / 3600;
    return max(1, ceil($diff)); // Minimal 1 jam
}

/* ================= USER ================= */

if($aksi=='tambah_user'){
    // Validation
    if(empty($_POST['nama']) || empty($_POST['user']) || empty($_POST['pass'])) 
        exit("<script>alert('Data tidak lengkap');history.back();</script>");
    
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $user = mysqli_real_escape_string($conn, trim($_POST['user']));
    $pass = mysqli_real_escape_string($conn, trim($_POST['pass']));
    $role_u = in_array($_POST['role_u'], ['admin', 'petugas', 'owner']) ? $_POST['role_u'] : 'petugas';
    
    mysqli_query($conn,"INSERT INTO tb_user 
    (nama_lengkap,username,password,role,status_aktif)
    VALUES ('$nama','$user','$pass','$role_u',1)");
}

elseif($aksi=='edit_user'){
    if(empty($_POST['id']) || empty($_POST['nama'])) 
        exit("<script>alert('Data tidak lengkap');history.back();</script>");
    
    $id = (int)$_POST['id'];
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $user = mysqli_real_escape_string($conn, trim($_POST['user']));
    $pass = mysqli_real_escape_string($conn, trim($_POST['pass']));
    $role_u = in_array($_POST['role_u'], ['admin', 'petugas', 'owner']) ? $_POST['role_u'] : 'petugas';
    
    mysqli_query($conn,"UPDATE tb_user SET
        nama_lengkap='$nama',
        username='$user',
        password='$pass',
        role='$role_u'
        WHERE id_user=$id");
}

elseif($aksi=='hapus_user'){
    if(empty($_POST['id'])) exit("<script>alert('ID tidak valid');history.back();</script>");
    
    $id = (int)$_POST['id'];
    
    // Check FK: transaksi & kendaraan yang dibuat user ini
    $chk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tb_transaksi WHERE id_user=$id"));
    if($chk['cnt'] > 0) 
        exit("<script>alert('Tidak bisa hapus user - masih ada ".intval($chk['cnt'])." transaksi');history.back();</script>");
    
    mysqli_query($conn,"DELETE FROM tb_user WHERE id_user=$id");
}

/* ================= TARIF ================= */

elseif($aksi=='tambah_tarif'){
    if(empty($_POST['jenis']) || empty($_POST['tarif'])) 
        exit("<script>alert('Data tidak lengkap');history.back();</script>");
    
    $tarif = (float)$_POST['tarif'];
    if($tarif <= 0) 
        exit("<script>alert('Tarif harus lebih dari 0');history.back();</script>");
    
    $jenis = mysqli_real_escape_string($conn, trim($_POST['jenis']));
    mysqli_query($conn,"INSERT INTO tb_tarif (jenis_kendaraan,tarif_per_jam)
    VALUES ('$jenis',$tarif)");
}

elseif($aksi=='edit_tarif'){
    if(empty($_POST['id'])) exit("<script>alert('ID tidak valid');history.back();</script>");
    
    $id = (int)$_POST['id'];
    $tarif = (float)$_POST['tarif'];
    if($tarif <= 0) 
        exit("<script>alert('Tarif harus lebih dari 0');history.back();</script>");
    
    $jenis = mysqli_real_escape_string($conn, trim($_POST['jenis']));
    mysqli_query($conn,"UPDATE tb_tarif SET
        jenis_kendaraan='$jenis',
        tarif_per_jam=$tarif
        WHERE id_tarif=$id");
}

elseif($aksi=='hapus_tarif'){
    if(empty($_POST['id'])) exit("<script>alert('ID tidak valid');history.back();</script>");
    
    $id = (int)$_POST['id'];
    
    // Check FK: transaksi yang menggunakan tarif ini
    $chk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tb_transaksi WHERE id_tarif=$id AND status='masuk'"));
    if($chk['cnt'] > 0) 
        exit("<script>alert('Tidak bisa hapus tarif - masih ".intval($chk['cnt'])." kendaraan aktif menggunakannya');history.back();</script>");
    
    mysqli_query($conn,"DELETE FROM tb_tarif WHERE id_tarif=$id");
}

/* ================= AREA ================= */

elseif($aksi=='tambah_area'){
    if(empty($_POST['nama_a']) || empty($_POST['kapasitas'])) 
        exit("<script>alert('Data tidak lengkap');history.back();</script>");
    
    $kapasitas = (int)$_POST['kapasitas'];
    if($kapasitas <= 0) 
        exit("<script>alert('Kapasitas harus lebih dari 0');history.back();</script>");
    
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_a']));
    mysqli_query($conn,"INSERT INTO tb_area_parkir (nama_area,kapasitas,terisi)
    VALUES ('$nama',$kapasitas,0)");
}

elseif($aksi=='edit_area'){
    if(empty($_POST['id'])) exit("<script>alert('ID tidak valid');history.back();</script>");
    
    $id = (int)$_POST['id'];
    $kapasitas = (int)$_POST['kapasitas'];
    if($kapasitas <= 0) 
        exit("<script>alert('Kapasitas harus lebih dari 0');history.back();</script>");
    
    // Check if new capacity >= current terisi
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT terisi FROM tb_area_parkir WHERE id_area=$id"));
    if($kapasitas < $cek['terisi']) 
        exit("<script>alert('Kapasitas harus >= kendaraan yang terisi (".intval($cek['terisi']).")');history.back();</script>");
    
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_a']));
    mysqli_query($conn,"UPDATE tb_area_parkir SET
        nama_area='$nama',
        kapasitas=$kapasitas
        WHERE id_area=$id");
}

elseif($aksi=='hapus_area'){
    if(empty($_POST['id'])) exit("<script>alert('ID tidak valid');history.back();</script>");
    
    $id = (int)$_POST['id'];
    
    // Check if area still has active vehicles
    $area = mysqli_fetch_assoc(mysqli_query($conn, "SELECT terisi FROM tb_area_parkir WHERE id_area=$id"));
    if($area['terisi'] > 0) 
        exit("<script>alert('Tidak bisa hapus area - masih ada ".intval($area['terisi'])." kendaraan parkir');history.back();</script>");
    
    // Check FK: transaksi aktif menggunakan area ini
    $chk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tb_transaksi WHERE id_area=$id AND status='masuk'"));
    if($chk['cnt'] > 0) 
        exit("<script>alert('Tidak bisa hapus area - masih ".intval($chk['cnt'])." kendaraan parkir aktif');history.back();</script>");
    
    // Set id_area to NULL for all transactions (completed ones) to avoid foreign key constraint
    mysqli_query($conn,"UPDATE tb_transaksi SET id_area=NULL WHERE id_area=$id");
    
    mysqli_query($conn,"DELETE FROM tb_area_parkir WHERE id_area=$id");
}

/* ================= MASUK PARKIR ================= */

elseif($aksi=='masuk_parkir'){
    // Validation
    if(empty($_POST['id_a']) || empty($_POST['plat']) || empty($_POST['id_t']))
        exit("<script>alert('Data tidak lengkap');history.back();</script>");
    
    $area = (int)$_POST['id_a'];
    $plat = mysqli_real_escape_string($conn, strtoupper(trim($_POST['plat'])));
    
    // Validate plat format (basic)
    if(strlen($plat) < 3 || strlen($plat) > 20)
        exit("<script>alert('Format plat nomor tidak valid');history.back();</script>");

    if(areaPenuh($conn,$area))
        exit("<script>alert('Area penuh');history.back();</script>");
    
    $jenis_k = mysqli_real_escape_string($conn, $_POST['jenis_k']);
    $warna = mysqli_real_escape_string($conn, $_POST['warna'] ?? '');
    $pemilik = mysqli_real_escape_string($conn, $_POST['pemilik'] ?? '');
    $id_t = (int)$_POST['id_t'];

    mysqli_query($conn,"
        INSERT INTO tb_kendaraan
        (plat_nomor,jenis_kendaraan,warna,pemilik,id_user)
        VALUES
        ('$plat','$jenis_k','$warna','$pemilik','$uid')
    ");

    $id_k = mysqli_insert_id($conn);
    $kode = 'KRC-'.date('YmdHis').'-'.rand(100,999);

   mysqli_query($conn,"
    INSERT INTO tb_transaksi
    (id_kendaraan,waktu_masuk,id_tarif,status,id_user,id_area,biaya_total,durasi_jam,kode_karcis)
    VALUES
    ('$id_k',NOW(),'$id_t','masuk','$uid','$area',0,0,'$kode')
");

$id_parkir = mysqli_insert_id($conn);

updateArea($conn,$area,1);

header("Location: ../index.php?page=struk&id=$id_parkir");
exit();

}

/* ================= EDIT PARKIR ================= */

elseif($aksi=='edit_parkir'){
    // Validation
    if(empty($_POST['id_p']) || empty($_POST['id_a']) || empty($_POST['id_t']))
        exit("<script>alert('Data tidak lengkap');history.back();</script>");

    $id_p = (int)$_POST['id_p'];
    $new  = (int)$_POST['id_a'];
    $id_t = (int)$_POST['id_t'];
    $id_k = (int)($_POST['id_k'] ?? 0);

    // ambil jenis kendaraan sesuai id_tarif
    $rt = mysqli_fetch_assoc(mysqli_query($conn,"SELECT jenis_kendaraan FROM tb_tarif WHERE id_tarif=$id_t"));
    if(!$rt) exit("<script>alert('Tarif tidak ditemukan');history.back();</script>");

    $old = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT id_area,status FROM tb_transaksi WHERE id_parkir=$id_p
    "));
    
    if(!$old) exit("<script>alert('Parkir tidak ditemukan');history.back();</script>");
    if($old['status']!='masuk') exit("<script>alert('Status harus masuk untuk edit');history.back();</script>");

    if($old['id_area']!=$new && areaPenuh($conn,$new))
        exit("<script>alert('Area penuh');history.back();</script>");

    $plat = mysqli_real_escape_string($conn, strtoupper(trim($_POST['plat'] ?? '')));
    $warna = mysqli_real_escape_string($conn, $_POST['warna'] ?? '');
    $pemilik = mysqli_real_escape_string($conn, $_POST['pemilik'] ?? '');

    if($id_k > 0) {
        mysqli_query($conn," 
            UPDATE tb_kendaraan SET
            plat_nomor='$plat',
            warna='$warna',
            pemilik='$pemilik',
            jenis_kendaraan='{$rt['jenis_kendaraan']}'
            WHERE id_kendaraan=$id_k
        ");
    }

    mysqli_query($conn,"
        UPDATE tb_transaksi SET
        id_tarif=$id_t,
        id_area=$new
        WHERE id_parkir=$id_p
    ");

    if($old['id_area']!=$new){
        updateArea($conn,$old['id_area'],-1);
        updateArea($conn,$new,1);
    }
}


/* ================= HAPUS PARKIR ================= */

elseif($aksi=='hapus_parkir'){
    // Validation
    if(empty($_POST['id_p']) || empty($_POST['id_k']))
        exit("<script>alert('Data tidak valid');history.back();</script>");
    
    $id_p = (int)$_POST['id_p'];
    $id_k = (int)$_POST['id_k'];

    $old = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT id_area,status FROM tb_transaksi WHERE id_parkir=$id_p
    "));

    if(!$old) exit("<script>alert('Parkir tidak ditemukan');history.back();</script>");

    if($id_k > 0)
        mysqli_query($conn,"DELETE FROM tb_kendaraan WHERE id_kendaraan=$id_k");
}

/* ================= KELUAR PARKIR ================= */

elseif($aksi=='konfirmasi_keluar'){
    // Validation
    if(empty($_POST['kode_karcis']))
        exit("<script>alert('Kode karcis tidak valid');history.back();</script>");
    
    $kode = mysqli_real_escape_string($conn, $_POST['kode_karcis']);

    // ambil transaksi berdasarkan kode karcis yang masih "masuk"
    $transaksi = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT * FROM tb_transaksi WHERE kode_karcis='$kode' AND status='masuk' LIMIT 1
    "));

    if(!$transaksi){
        exit("<script>alert('Data tidak ditemukan atau sudah keluar');history.back();</script>");
    }

    // hitung durasi jam dengan helper function - STANDARDIZED
    $durasi_jam = hitungJamTagih($transaksi['waktu_masuk'], date('Y-m-d H:i:s'));

    // hitung total biaya
    $tarif = mysqli_fetch_assoc(mysqli_query($conn,"SELECT tarif_per_jam FROM tb_tarif WHERE id_tarif=".$transaksi['id_tarif']));
    if(!$tarif) 
        exit("<script>alert('Tarif tidak ditemukan');history.back();</script>");
    
    $total = $durasi_jam * $tarif['tarif_per_jam'];

    // update transaksi
    mysqli_query($conn,"
        UPDATE tb_transaksi SET
        waktu_keluar=NOW(),
        durasi_jam='$durasi_jam',
        biaya_total='$total',
        status='keluar'
        WHERE id_parkir='{$transaksi['id_parkir']}'
    ");

    updateArea($conn, $transaksi['id_area'], -1);

    header("Location: ../index.php?page=struk&id={$transaksi['id_parkir']}");
    exit();
}



/* ================= REDIRECT ================= */

// Whitelist redirect pages - Prevent Open Redirect
$allowed_redirect = ['dashboard', 'transaksi', 'riwayat', 'rekap', 'user', 'tarif', 'area', 'log'];
$to = $_POST['redirect'] ?? 'dashboard';
if(!in_array($to, $allowed_redirect)) {
    $to = 'dashboard';
}
header("Location: ../index.php?page=".$to);
exit();

