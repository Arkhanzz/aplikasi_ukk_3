<?php
include 'config/session.php';
include 'includes/head.php';
$page = $_GET['page'] ?? 'dashboard';
?>

<?php if (!$role): ?>
    <?php include 'pages/login.php'; ?>
<?php else: ?>
    <?php include 'includes/navbar.php'; ?>
    <div class="container py-4">
        <?php
        $file = "pages/$page.php";
        if (file_exists($file)) {
            include $file;
        } else {
            echo "<div class='alert alert-danger'>Halaman tidak ditemukan</div>";
        }
        ?>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
