<?php
require_once __DIR__ . '/helpers.php';
$pageTitle = $pageTitle ?? 'AFone Store';
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top site-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <span class="brand-mark">sul</span>
            <span>AFone Store</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'topup' ? 'active' : '' ?>" href="TopUp.php">Top Up </a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'joki' ? 'active' : '' ?>" href="Jokigame.php">Joki Rank</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'akun' ? 'active' : '' ?>" href="beli-akun.php">Beli Akun</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'prices' ? 'active' : '' ?>" href="daftar-harga.php">Daftar Harga</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'contact' ? 'active' : '' ?>" href="kontak.php">Kontak</a></li>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item"><a class="btn btn-warning btn-sm fw-bold ms-lg-2" href="admin/index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-1" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-warning btn-sm fw-bold ms-lg-2" href="login.php">Login Admin</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php if ($flash = flash()): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>
