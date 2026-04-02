<?php
if (!isset($conn)) include 'config/koneksi.php';

$kode = mysqli_real_escape_string($conn, $_POST['kode_karcis'] ?? '');

$q = mysqli_query($conn,"
  SELECT t.*, tr.tarif_per_jam
  FROM tb_transaksi t
  JOIN tb_tarif tr ON t.id_tarif=tr.id_tarif
  WHERE t.kode_karcis='$kode'
  AND t.status='masuk'
  LIMIT 1
");

if(mysqli_num_rows($q)==0){
  echo "<div class='alert alert-danger'>Karcis tidak valid</div>";
  return;
}

$d = mysqli_fetch_assoc($q);

// hitung durasi (ASLI DARI KODE LU)
$masuk = new DateTime($d['waktu_masuk']);
$keluar = new DateTime();
$diff = $masuk->diff($keluar);

$durasi = ($diff->days*24) + $diff->h;
if($diff->i>0) $durasi++;
if($durasi<1) $durasi=1;

$total = $durasi * $d['tarif_per_jam'];
?>

<div class="container text-center py-4">
  <h4 class="fw-bold text-danger">PEMBAYARAN PARKIR</h4>

  <p>Durasi: <b><?= $durasi ?> jam</b></p>
  <h3>Rp <?= number_format($total) ?></h3>

  <!-- QRIS STATIS -->
  <img src="https://www.devel.pa-gunungsitoli.go.id/images/Gambar/Qris.png" width="250">

  <form method="POST" action="process/aksi.php">
    <input type="hidden" name="aksi" value="konfirmasi_keluar">
    <input type="hidden" name="id_parkir" value="<?= $d['id_parkir'] ?>">
    <input type="hidden" name="durasi" value="<?= $durasi ?>">
    <input type="hidden" name="total" value="<?= $total ?>">

    <button class="btn btn-danger w-100 fw-bold mt-3">
      KONFIRMASI BAYAR
    </button>
  </form>
</div>
