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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark text-white">

            <div class="modal-header border-secondary">
                <h5 class="modal-title">Cari Game atau Joki</h5>

                <button class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <input
                    type="text"
                    id="searchInput"
                    class="form-control"
                    placeholder="Ketik nama game...">

                <div id="searchResult" class="mt-3"></div>

            </div>

        </div>
    </div>
</div>

<body>
<nav class="navbar navbar-expand-lg sticky-top site-navbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="brand-mark">
                <video autoplay muted loop playsinline>
                    <source src="assets/logo/logo.mp4" type="video/mp4">
                </video>
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'topup' ? 'active' : '' ?>" href="TopUp.php">Top Up </a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'joki' ? 'active' : '' ?>" href="Jokigame.php">Joki Rank</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'akun' ? 'active' : '' ?>" href="beli-akun.php">Beli Akun</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'prices' ? 'active' : '' ?>" href="daftar-harga.php">Daftar Harga</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'contact' ? 'active' : '' ?>" href="kontak.php">Kontak</a></li>
                <li class="nav-item ms-lg-3">
<button class="search-navbar-btn"
        data-bs-toggle="modal"
        data-bs-target="#searchModal">
 Cari Game...
</button>
</li>
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
<div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content search-popup">

            <div class="modal-header">

                <h4> Cari Game </h4>

                <button class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <input
                    type="text"
                    id="searchInput"
                    class="form-control search-box"
                    placeholder="Ketik nama game...">

                <div id="searchResult" class="mt-4"></div>

            </div>

        </div>
    </div>
</div>
<div id="intro">
    <video autoplay muted playsinline id="introVideo">
        <source src="assets/logo/intro.mp4" type="video/mp4">
    </video>
</div>
<?php if ($flash = flash()): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>
