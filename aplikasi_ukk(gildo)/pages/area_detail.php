<?php
if (!isset($conn)) {
    include 'config/koneksi.php';
}

$id_area = $_GET['id'] ?? 0;

$area = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT * FROM tb_area_parkir
    WHERE id_area='$id_area'
"));

if (!$area) {
    echo "<div class='alert alert-danger'>Area tidak ditemukan</div>";
    return;
}
?>

<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">AREA <?= htmlspecialchars($area['nama_area']) ?></h5>
            <small class="text-muted">
                Terisi <?= $area['terisi'] ?> / <?= $area['kapasitas'] ?>
            </small>
        </div>
        <a href="?page=area" class="btn btn-secondary btn-sm">
            ← Kembali
        </a>
    </div>
</div>

<div class="card p-3">
<table class="table table-bordered table-hover bg-white align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th>No</th>
            <th>Plat / Kode</th>
            <th>Jenis</th>
            <th>Warna</th>
            <th>Masuk</th>
            <th width="180">Aksi</th>
        </tr>
    </thead>
    <tbody>

<?php
$no = 1;
$q = mysqli_query($conn,"
    SELECT
        t.id_parkir,
        k.id_kendaraan,
        k.plat_nomor,
        k.jenis_kendaraan,
        k.warna,
        t.waktu_masuk,
        t.status,
        t.kode_karcis
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    WHERE t.id_area='$id_area'
    AND t.status='masuk'
    ORDER BY t.waktu_masuk ASC
");

if (mysqli_num_rows($q) == 0):
?>
    <tr>
        <td colspan="6" class="text-center text-muted">
            Tidak ada kendaraan di area ini
        </td>
    </tr>
<?php
else:
while ($r = mysqli_fetch_assoc($q)):
?>
<tr>
    <td><?= $no++ ?></td>
    <td>
        <strong><?= htmlspecialchars($r['plat_nomor']) ?></strong><br>
        <small class="text-muted d-flex align-items-center">
            <?= htmlspecialchars($r['kode_karcis']) ?>
            <button class="btn btn-sm btn-outline-secondary ms-2"
                onclick="copyKode('<?= $r['kode_karcis'] ?>')">
                📋
            </button>
        </small>
    </td>
    <td><?= htmlspecialchars($r['jenis_kendaraan']) ?></td>
    <td><?= htmlspecialchars($r['warna']) ?></td>
    <td><?= $r['waktu_masuk'] ?></td>
    <td>
        <!-- EDIT -->
        <a href="?page=transaksi&edit_p=<?= $r['id_parkir'] ?>"
           class="btn btn-warning btn-sm">
           ✏️
        </a>

        <!-- STRUK -->
     <a href="?page=struk&id=<?= $r['id_parkir'] ?>&from=<?= urlencode($_SERVER['QUERY_STRING']) ?>"

           class="btn btn-info btn-sm text-white">
           🖨
        </a>

        <!-- HAPUS -->
        <form method="POST"
              action="process/aksi.php"
              class="d-inline"
              onsubmit="return confirm('Hapus data parkir ini?')">
            <input type="hidden" name="aksi" value="hapus_parkir">
            <input type="hidden" name="id_p" value="<?= $r['id_parkir'] ?>">
            <input type="hidden" name="id_k" value="<?= $r['id_kendaraan'] ?>">
            <button class="btn btn-danger btn-sm">🗑</button>
        </form>
    </td>
</tr>
<?php
endwhile;
endif;
?>
    </tbody>
</table>
</div>

<!-- ================= TOAST NOTIF COPY KODE ================= -->
<div id="toast" class="position-fixed top-0 start-50 translate-middle-x mt-3"
     style="z-index:1050; display:none;">
    <div class="toast align-items-center text-bg-success border-0 show">
        <div class="d-flex">
            <div class="toast-body">
                Kode berhasil disalin!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                    onclick="document.getElementById('toast').style.display='none'"></button>
        </div>
    </div>
</div>

<script>
function copyKode(kode) {
    navigator.clipboard.writeText(kode).then(() => {
        const toast = document.getElementById('toast');
        toast.style.display = 'block';
        setTimeout(() => toast.style.display = 'none', 2000); // hilang otomatis 2 detik
    });
}
</script>
