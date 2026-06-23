<?php
require_once __DIR__ . '/includes/helpers.php';
$pageTitle = 'Daftar Harga Top Up - AFone Store';
$activePage = 'prices';
$games = get_games(true);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="page-hero-box">
            <div class="section-kicker mb-2">Daftar Harga</div>
            <h1 class="section-title mb-2">Daftar harga top up game</h1>
            <p class="text-soft mb-0">Halaman ringkasan harga untuk memudahkan pelanggan melihat nominal yang tersedia sebelum masuk ke halaman order.</p>
        </div>
    </div>
</section>
<section class="container my-4">
    <?php if (!$games): ?>
        <div class="empty-state content-card">Belum ada game aktif.</div>
    <?php else: ?>
        <?php foreach ($games as $game): ?>
            <?php $packages = get_packages_by_game((int)$game['id']); ?>
            <div class="price-table-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="admin-emoji"><?= e($game['icon_emoji'] ?: '🎮') ?></div>
                        <div><h4 class="mb-0"><?= e($game['name']) ?></h4><small class="text-soft"><?= count($packages) ?> nominal tersedia</small></div>
                    </div>
                    <a href="game.php?slug=<?= e($game['slug']) ?>" class="btn btn-outline-warning btn-sm">Top Up Sekarang</a>
                </div>
                <?php if (!$packages): ?>
                    <div class="text-soft">Belum ada nominal.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark-custom align-middle mb-0">
                            <thead><tr><th>Nominal</th><th>Keterangan</th><th>Harga</th></tr></thead>
                            <tbody>
                            <?php foreach ($packages as $package): ?>
                                <tr>
                                    <td><strong><?= e($package['name']) ?></strong><?php if ($package['badge']): ?> <span class="badge badge-soft"><?= e($package['badge']) ?></span><?php endif; ?></td>
                                    <td class="text-soft"><?= e($package['description']) ?></td>
                                    <td class="text-warning fw-bold"><?= rupiah($package['price']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
