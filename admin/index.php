<?php
require_once __DIR__ . '/../includes/helpers.php';
$pageTitle = 'Dashboard Admin - AFone Store';
$activeAdmin = 'dashboard';
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="section-title mb-1">Dashboard Admin</h1>
        <p class="text-soft mb-0">Kelola website top up, joki, dan jual beli akun.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="game-form.php" class="btn btn-warning fw-bold">+ Game</a>
        <a href="package-form.php" class="btn btn-outline-warning">+ Nominal</a>
        <a href="account-form.php" class="btn btn-outline-warning">+ Akun</a>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6"><div class="stat-card"><h3><?= count_table('games') ?></h3><p class="text-soft mb-0">Game</p></div></div>
    <div class="col-md-3 col-6"><div class="stat-card"><h3><?= count_table('topup_packages') ?></h3><p class="text-soft mb-0">Nominal</p></div></div>
    <div class="col-md-3 col-6"><div class="stat-card"><h3><?= count_table('joki_services') ?></h3><p class="text-soft mb-0">Harga Joki</p></div></div>
    <div class="col-md-3 col-6"><div class="stat-card"><h3><?= count_table('game_accounts') ?></h3><p class="text-soft mb-0">Akun Dijual</p></div></div>
</div>
<div class="content-card">
    <h4 class="fw-bold mb-3">Menu yang sudah tersedia</h4>
    <div class="row g-3">
        <div class="col-md-6"><div class="feature-card"><div class="feature-icon">🎮</div><h5 class="mt-3">Top Up Game</h5><p class="text-soft mb-0">Halaman pilih game → pilih nominal → isi data → order.</p></div></div>
        <div class="col-md-6"><div class="feature-card"><div class="feature-icon">💎</div><h5 class="mt-3">CRUD Nominal</h5><p class="text-soft mb-0">Atur diamond, UC, Robux, VP, harga, badge, dan status aktif.</p></div></div>
        <div class="col-md-6"><div class="feature-card"><div class="feature-icon">⚡</div><h5 class="mt-3">Joki + Kalkulator</h5><p class="text-soft mb-0">Daftar harga reguler/express dan order joki.</p></div></div>
        <div class="col-md-6"><div class="feature-card"><div class="feature-icon">▣</div><h5 class="mt-3">Beli Akun</h5><p class="text-soft mb-0">Tambah akun game, foto, spesifikasi, harga, dan status.</p></div></div>
    </div>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
