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
<div id="bannerSlider" class="carousel slide mb-4" data-bs-ride="carousel">

    <div class="carousel-indicators">
        <button type="button" data-bs-target="#bannerSlider" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#bannerSlider" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#bannerSlider" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">

        <div class="carousel-item active">
            <img src="assets/banner/banner1.jpg" class="d-block w-100 banner-img">
        </div>

        <div class="carousel-item">
            <img src="assets/banner/banner2.jpg" class="d-block w-100 banner-img">
        </div>

        <div class="carousel-item">
            <img src="assets/banner/banner3.jpg" class="d-block w-100 banner-img">
        </div>

    </div>

    <button class="carousel-control-prev" type="button"
        data-bs-target="#bannerSlider"
        data-bs-slide="prev">

        <span class="carousel-control-prev-icon"></span>

    </button>

    <button class="carousel-control-next" type="button"
        data-bs-target="#bannerSlider"
        data-bs-slide="next">

        <span class="carousel-control-next-icon"></span>

    </button>

</div>
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
                    <div class="game-logo">
                        <?php if (!empty($game['image_url'])): ?><img src="<?= e(image_src($game['image_url'])) ?>" alt="<?= e($game['name']) ?>"><?php else: ?><?= e($game['icon_emoji'] ?: '🎮') ?><?php endif; ?>
                    </div>
                    <div class="game-name"><?= e($game['name']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
