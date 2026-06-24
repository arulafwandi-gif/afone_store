<?php
require_once __DIR__ . '/includes/helpers.php';
$pageTitle = 'Top Up Game - AFone Store';
$activePage = 'topup';
$category = $_GET['category'] ?? '';
$query = trim($_GET['q'] ?? '');
$validCategories = ['moba','battle-royale','rpg','sports','lainnya'];
$games = in_array($category, $validCategories, true) ? get_games(true, $category) : get_games(true);
if ($query !== '') {
    $games = array_values(array_filter($games, function($game) use ($query) {
        return stripos($game['name'], $query) !== false
            || stripos($game['publisher'] ?? '', $query) !== false
            || stripos(category_label($game['category']), $query) !== false;
    }));
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="page-hero-box">
            <div class="section-kicker mb-2">Top Up Game</div>
            <h1 class="section-title mb-2">Pilih game, lalu pilih jumlah diamond/coin sesuai kebutuhan.</h1>
            <div class="promo-strip">
                <div class="promo-box">
                    <div class="promo-emoji">💎</div>
                    <span>Promo Top Up</span>
                    <strong>Diamond, UC, Robux, VP, dan coin game.</strong>
                    <p>Harga bersaing, lebih murah dari harga resmi.</p>
                </div>
                <div class="promo-box small">
                    <div class="promo-emoji">⚡</div>
                    <span>Proses Cepat</span>
                    <strong>Order masuk ke admin.</strong>
                    <p>Top up langsung diproses, tidak perlu menunggu lama. Bisa juga lewat auto top up.</p>
                </div>
                <div class="promo-box small">
                    <div class="promo-emoji">🎮</div>
                    <span>Game Populer</span>
                    <strong>ML, FF, PUBG, Roblox.</strong>
                    <p>Game populer tersedia, dan akan terus ditambahkan game baru.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container my-4">
    <form method="get" class="content-card mb-4 search-card">
        <input type="text" name="q" value="<?= e($query) ?>" class="form-control search-pill" placeholder="Cari game favoritmu, misalnya Mobile Legends atau Free Fire...">
        <?php if ($category): ?><input type="hidden" name="category" value="<?= e($category) ?>"><?php endif; ?>
        <button class="btn btn-warning fw-bold" type="submit">Cari Game</button>
    </form>

    <div class="category-tabs mb-4">
        <a href="TopUp.php<?= $query ? '?q=' . urlencode($query) : '' ?>" class="<?= $category === '' ? 'active' : '' ?>">Semua</a>
        <a href="TopUp.php?category=moba<?= $query ? '&q=' . urlencode($query) : '' ?>" class="<?= $category === 'moba' ? 'active' : '' ?>">MOBA</a>
        <a href="TopUp.php?category=battle-royale<?= $query ? '&q=' . urlencode($query) : '' ?>" class="<?= $category === 'battle-royale' ? 'active' : '' ?>">Battle Royale</a>
        <a href="TopUp.php?category=rpg<?= $query ? '&q=' . urlencode($query) : '' ?>" class="<?= $category === 'rpg' ? 'active' : '' ?>">RPG</a>
        <a href="TopUp.php?category=sports<?= $query ? '&q=' . urlencode($query) : '' ?>" class="<?= $category === 'sports' ? 'active' : '' ?>">Sports</a>
        <a href="TopUp.php?category=lainnya<?= $query ? '&q=' . urlencode($query) : '' ?>" class="<?= $category === 'lainnya' ? 'active' : '' ?>">Lainnya</a>
    </div>

    <?php if (!$games): ?>
        <div class="empty-state content-card">Game tidak ditemukan. Tambahkan game lewat dashboard admin atau ubah kata kunci.</div>
    <?php else: ?>
        <div class="game-grid">
            <?php foreach ($games as $game): ?>
                <a class="game-card" href="game.php?slug=<?= e($game['slug']) ?>">
                    <?php if ((int)$game['is_popular'] === 1): ?><span class="game-badge">POPULER</span><?php endif; ?>
                    <div class="game-logo">
                        <?php if (!empty($game['image_url'])): ?><img src="<?= e(image_src($game['image_url'])) ?>" alt="<?= e($game['name']) ?>"><?php else: ?><?= e($game['icon_emoji'] ?: '🎮') ?><?php endif; ?>
                    </div>
                    <div class="game-name"><?= e($game['name']) ?></div>
                    <div class="game-meta"><?= e(category_label($game['category'])) ?> • <?= e($game['publisher']) ?></div>
                    <div class="mt-3 small text-warning fw-bold"><?= count(get_packages_by_game((int)$game['id'])) ?> nominal tersedia</div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
