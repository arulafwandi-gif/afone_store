<?php
require_once __DIR__ . '/includes/helpers.php';
$pageTitle = 'Beli Akun Game - AFone Store';
$activePage = 'akun';
$gameId = (int)($_GET['game_id'] ?? 0);
$games = get_games(true);
$accounts = get_game_accounts(true, $gameId > 0 ? $gameId : null);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="page-hero-box text-center">
            <div class="section-kicker mb-2">Beli Akun</div>
            <h1 class="section-title mb-2">Koleksi akun game AFone Store</h1>
            <p class="text-soft mb-0">Pilih akun game siap pakai. Data akun bisa ditambah, diedit, dan dihapus dari dashboard admin.</p>
        </div>
    </div>
</section>

<section class="container my-4">
    <div class="account-filter-grid mb-4">
        <a class="account-category-card <?= $gameId === 0 ? 'active' : '' ?>" href="beli-akun.php">
            <div class="account-category-thumb">🎮</div>
            <strong>Semua Game</strong>
        </a>
        <?php foreach (array_slice($games, 0, 9) as $game): ?>
            <a class="account-category-card <?= $gameId === (int)$game['id'] ? 'active' : '' ?>" href="beli-akun.php?game_id=<?= (int)$game['id'] ?>">
                <div class="account-category-thumb">
                    <?php if (!empty($game['image_url'])): ?><img src="<?= e(image_src($game['image_url'])) ?>" alt="<?= e($game['name']) ?>"><?php else: ?><span><?= e($game['icon_emoji'] ?: '🎮') ?></span><?php endif; ?>
                </div>
                <strong><?= e($game['name']) ?></strong>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-4">
        <div>
            <div class="section-kicker mb-2">Akun Tersedia</div>
            <h2 class="section-title mb-1">Pilih akun yang ingin dibeli</h2>
            <p class="text-soft mb-0">Klik tombol beli untuk menghubungi admin melalui WhatsApp.</p>
        </div>
    </div>

    <?php if (!$accounts): ?>
        <div class="empty-state content-card">Belum ada akun pada kategori ini.</div>
    <?php else: ?>
        <div class="akun-grid">
            <?php foreach ($accounts as $account): ?>
                <article class="akun-card">
                    <div class="akun-card-image">
                        <?php if (!empty($account['image_url'])): ?><img src="<?= e(image_src($account['image_url'])) ?>" alt="<?= e($account['title']) ?>"><?php elseif (!empty($account['game_image'])): ?><img src="<?= e(image_src($account['game_image'])) ?>" alt="<?= e($account['game_name']) ?>"><?php else: ?><span><?= e($account['game_icon'] ?? '🎮') ?></span><?php endif; ?>
                        <em><?= e(strtoupper($account['status'])) ?></em>
                    </div>
                    <div class="akun-card-body">
                        <small><?= e($account['game_name'] ?? 'Game') ?></small>
                        <h3><?= e($account['title']) ?></h3>
                        <p><?= e($account['description']) ?></p>
                        <div class="akun-specs"><?= e($account['specs'] ?? '-') ?></div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <strong class="akun-price"><?= rupiah($account['price']) ?></strong>
                            <a class="btn btn-sm btn-warning fw-bold" target="_blank" href="https://wa.me/6281949351883?text=Halo%20admin,%20saya%20mau%20beli%20<?= rawurlencode($account['title']) ?>">Beli</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
