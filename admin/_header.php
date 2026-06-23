<?php
require_once __DIR__ . '/../includes/helpers.php';
require_login();
$pageTitle = $pageTitle ?? 'Dashboard Admin - AFone Store';
$activeAdmin = $activeAdmin ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top site-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
            <span class="brand-mark">A</span>
            <span>AFone Store Admin</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="../index.php">Lihat Website</a></li>
                <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<?php if ($flash = flash()): ?>
    <div class="container mt-3"><div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert"><?= e($flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
<?php endif; ?>
<div class="container my-4">
    <div class="row g-4">
        <div class="col-lg-3">
            <aside class="admin-sidebar">
                <div class="mb-3">
                    <strong class="text-warning"><?= e(current_user()['name'] ?? 'Admin') ?></strong>
                    <div class="text-soft small">Administrator</div>
                </div>
                <a class="<?= $activeAdmin === 'dashboard' ? 'active' : '' ?>" href="index.php">Dashboard</a>
                <a class="<?= $activeAdmin === 'games' ? 'active' : '' ?>" href="games.php">CRUD Game</a>
                <a class="<?= $activeAdmin === 'packages' ? 'active' : '' ?>" href="packages.php">CRUD Nominal</a>
                <a class="<?= $activeAdmin === 'joki' ? 'active' : '' ?>" href="joki-services.php">CRUD Joki</a>
                <a class="<?= $activeAdmin === 'accounts' ? 'active' : '' ?>" href="accounts.php">CRUD Beli Akun</a>
                <a class="<?= $activeAdmin === 'orders' ? 'active' : '' ?>" href="orders.php">Data Order Top Up</a>
                <a class="<?= $activeAdmin === 'messages' ? 'active' : '' ?>" href="messages.php">Pesan Kontak</a>
                <a href="../install.php">Install Database</a>
            </aside>
        </div>
        <div class="col-lg-9">
