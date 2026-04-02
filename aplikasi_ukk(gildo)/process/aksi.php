<?php
include '../config/session.php';



$aksi = $_POST['aksi'];

/* ================= UTIL ================= */

function areaPenuh($conn,$id){
    $a = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT kapasitas,terisi FROM tb_area_parkir WHERE id_area='$id'"
    ));
    return $a['terisi'] >= $a['kapasitas'];
}

function updateArea($conn,$id,$val){
    mysqli_query($conn,
        "UPDATE tb_area_parkir SET terisi = terisi + ($val) WHERE id_area='$id'"
    );
}

/* ================= USER ================= */

if($aksi=='tambah_user'){
    mysqli_query($conn,"INSERT INTO tb_user 
    (nama_lengkap,username,password,role,status_aktif)
    VALUES ('$_POST[nama]','$_POST[user]','$_POST[pass]','$_POST[role_u]',1)");
}

elseif($aksi=='edit_user'){
    mysqli_query($conn,"UPDATE tb_user SET
        nama_lengkap='$_POST[nama]',
        username='$_POST[user]',
        password='$_POST[pass]',
        role='$_POST[role_u]'
        WHERE id_user='$_POST[id]'");
}

elseif($aksi=='hapus_user'){
    mysqli_query($conn,"DELETE FROM tb_user WHERE id_user='$_POST[id]'");
}

/* ================= TARIF ================= */

elseif($aksi=='tambah_tarif'){
    mysqli_query($conn,"INSERT INTO tb_tarif (jenis_kendaraan,tarif_per_jam)
    VALUES ('$_POST[jenis]','$_POST[tarif]')");
}

elseif($aksi=='edit_tarif'){
    mysqli_query($conn,"UPDATE tb_tarif SET
        jenis_kendaraan='$_POST[jenis]',
        tarif_per_jam='$_POST[tarif]'
        WHERE id_tarif='$_POST[id]'");
}

elseif($aksi=='hapus_tarif'){
    mysqli_query($conn,"DELETE FROM tb_tarif WHERE id_tarif='$_POST[id]'");
}

/* ================= AREA ================= */

elseif($aksi=='tambah_area'){
    mysqli_query($conn,"INSERT INTO tb_area_parkir (nama_area,kapasitas,terisi)
    VALUES ('$_POST[nama_a]','$_POST[kapasitas]',0)");
}

elseif($aksi=='edit_area'){
    mysqli_query($conn,"UPDATE tb_area_parkir SET
        nama_area='$_POST[nama_a]',
        kapasitas='$_POST[kapasitas]'
        WHERE id_area='$_POST[id]'");
}

elseif($aksi=='hapus_area'){
    mysqli_query($conn,"DELETE FROM tb_area_parkir WHERE id_area='$_POST[id]'");
}

/* ================= MASUK PARKIR ================= */

elseif($aksi=='masuk_parkir'){

    $area = $_POST['id_a'];

    if(areaPenuh($conn,$area))
        exit("<script>alert('Area penuh');history.back();</script>");

    mysqli_query($conn,"
        INSERT INTO tb_kendaraan
        (plat_nomor,jenis_kendaraan,warna,pemilik,id_user)
        VALUES
        ('$_POST[plat]','$_POST[jenis_k]','$_POST[warna]','$_POST[pemilik]','$uid')
    ");

    $id_k = mysqli_insert_id($conn);
    $kode = 'KRC-'.date('YmdHis').'-'.rand(100,999);

   mysqli_query($conn,"
    INSERT INTO tb_transaksi
    (id_kendaraan,waktu_masuk,id_tarif,status,id_user,id_area,biaya_total,durasi_jam,kode_karcis)
    VALUES
    ('$id_k',NOW(),'$_POST[id_t]','masuk','$uid','$area',0,0,'$kode')
");

$id_parkir = mysqli_insert_id($conn);

updateArea($conn,$area,1);

header("Location: ../index.php?page=struk&id=$id_parkir");
exit();

}

/* ================= EDIT PARKIR ================= */

elseif($aksi=='edit_parkir'){

    $id_p = $_POST['id_p'];
    $new  = $_POST['id_a'];
    $id_t = $_POST['id_t']; // <== lo lupa ini

    // ambil jenis kendaraan sesuai id_tarif
    $rt = mysqli_fetch_assoc(mysqli_query($conn,"SELECT jenis_kendaraan FROM tb_tarif WHERE id_tarif='$id_t'"));

    $old = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT id_area,status FROM tb_transaksi WHERE id_parkir='$id_p'
    "));

    if($old['status']!='masuk') exit();

    if($old['id_area']!=$new && areaPenuh($conn,$new))
        exit("<script>alert('Area penuh');history.back();</script>");

    mysqli_query($conn," 
        UPDATE tb_kendaraan SET
        plat_nomor='$_POST[plat]',
        warna='$_POST[warna]',
        pemilik='$_POST[pemilik]',
        jenis_kendaraan='{$rt['jenis_kendaraan']}'
        WHERE id_kendaraan='$_POST[id_k]'
    ");

    mysqli_query($conn,"
        UPDATE tb_transaksi SET
        id_tarif='$id_t',
        id_area='$new'
        WHERE id_parkir='$id_p'
    ");

    if($old['id_area']!=$new){
        updateArea($conn,$old['id_area'],-1);
        updateArea($conn,$new,1);
    }
}


/* ================= HAPUS PARKIR ================= */

elseif($aksi=='hapus_parkir'){

    $old = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT id_area,status FROM tb_transaksi WHERE id_parkir='$_POST[id_p]'
    "));

    if($old && $old['status']=='masuk')
        updateArea($conn,$old['id_area'],-1);

    mysqli_query($conn,"DELETE FROM tb_transaksi WHERE id_parkir='$_POST[id_p]'");
    mysqli_query($conn,"DELETE FROM tb_kendaraan WHERE id_kendaraan='$_POST[id_k]'");
}

/* ================= KELUAR PARKIR ================= */

elseif($aksi=='konfirmasi_keluar'){
    $kode = $_POST['kode_karcis'];

    // ambil transaksi berdasarkan kode karcis yang masih "masuk"
    $transaksi = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT * FROM tb_transaksi WHERE kode_karcis='$kode' AND status='masuk'
    "));

    if(!$transaksi){
        exit("<script>alert('Data tidak ditemukan');history.back();</script>");
    }

    // hitung durasi jam
    $w_masuk = strtotime($transaksi['waktu_masuk']);
    $w_keluar = time(); // sekarang
    $durasi_jam = ceil(($w_keluar - $w_masuk) / 3600); // minimal 1 jam

    // hitung total biaya
    $total = $durasi_jam * $transaksi['biaya_total']; // atau ambil tarif_per_jam dari tb_tarif
    $tarif = mysqli_fetch_assoc(mysqli_query($conn,"SELECT tarif_per_jam FROM tb_tarif WHERE id_tarif='{$transaksi['id_tarif']}'"));
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

$to = $_POST['redirect'] ?? 'dashboard';
header("Location: ../index.php?page=".$to);
exit();

