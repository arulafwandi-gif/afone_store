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
    <link rel="icon" type="image/png" href="assets/logo/logo.png">
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
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content bg-dark text-white">

            <div class="modal-header border-secondary">

                <h5 class="modal-title">
                    Login User
                </h5>

                <button
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <form action="login-user.php" method="POST">

                    <div class="mb-3">
                        <label>Email</label>

                        <input
                            type="email"
                            class="form-control"
                            name="email"
                            required>
                    </div>

                    <div class="mb-3">

                        <label>Password</label>

                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            required>

                    </div>

                    <button class="btn btn-warning w-100">
                        Login
                    </button>

                </form>

                <hr>

                <p class="text-center mb-0">

                    Belum punya akun?

                    <a href="register.php">
                        Daftar Sekarang
                    </a>

                </p>

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
                <li class="nav-item"><a class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>" href="index.php">BERANDA</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'topup' ? 'active' : '' ?>" href="TopUp.php">TOP UP </a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'akun' ? 'active' : '' ?>" href="beli-akun.php">BELI AKUN</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'prices' ? 'active' : '' ?>" href="daftar-harga.php">DAFTAR HARGA</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'contact' ? 'active' : '' ?>" href="kontak.php">KONTAK</a></li>
                <li class="nav-item ms-lg-3">
<button class="search-navbar-btn"
        data-bs-toggle="modal"
        data-bs-target="#searchModal">
 Cari Game...
</button>

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
