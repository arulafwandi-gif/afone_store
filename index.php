<?php
require_once __DIR__ . '/includes/helpers.php';
$pageTitle = 'AFone Store - Top Up Game, Joki, dan Beli Akun';
$activePage = 'home';
$popularGames = get_popular_games(8);
$allGames = get_games(true);
$gamesForHome = $popularGames ?: array_slice($allGames, 0, 8);
$accounts = get_game_accounts(true);
$regularJoki = get_joki_services('reguler', true);
$topPackages = array_slice(fallback_packages(), 0, 6);
require __DIR__ . '/includes/header.php';
?>
<section class="home-ticker">
    <div class="container">
        <div class="ticker-track">
            <span>🔥 Promo top up hari ini</span>
            <span>💎 Diamond MLBB & FF tersedia</span>
            <span>⚡ Order cepat diproses admin</span>
            <span>🛡️ Data akun aman dan bisa dicek</span>
            <span>🎮 Joki rank reguler dan express</span>
        </div>
    </div>
</section>

<section class="container home-block">
    <div class="container">
        <div class="home-promo-main">
            <div class="promo-copy">
                <span class="section-kicker">AFone Flash Deal</span>
                <h1>Beli kebutuhan game favoritmu lebih cepat.</h1>
                <p>Top up diamond, UC, Robux, beli akun, dan order joki rank dalam satu website.</p>
                <div class="promo-actions">
                    <a href="TopUp.php" class="btn btn-warning btn-lg fw-bold">Mulai Top Up</a>
                    <a href="Jokigame.php" class="btn btn-outline-light btn-lg">Cek Joki</a>
                </div>
            </div>
           
        </div>
    </div>
</section>

<section class="container home-block">
    <div class="home-section-head text-center">
        <span class="section-kicker">Top Up</span>
        <h2>Game populer di AFone Store</h2>
        <p>Cari game, pilih nominal, isi ID, lalu order langsung masuk ke dashboard admin.</p>
    </div>

    <form action="TopUp.php" method="get" class="home-search-wrap">
        <input type="text" name="q" class="form-control" placeholder="Cari game favoritmu, misalnya Mobile Legends atau Free Fire...">
        <button class="btn btn-warning fw-bold" type="submit">Cari</button>
    </form>

    <div class="home-popular-grid">
        <?php foreach (array_slice($gamesForHome, 0, 6) as $game): ?>
            <a class="home-game-tile" href="game.php?slug=<?= e($game['slug']) ?>">
                <div class="home-game-thumb">
                    <?php if (!empty($game['image_url'])): ?>
                        <img src="<?= e(image_src($game['image_url'])) ?>" alt="<?= e($game['name']) ?>">
                    <?php else: ?>
                        <span><?= e($game['icon_emoji'] ?: '🎮') ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <strong><?= e($game['name']) ?></strong>
                    <small><?= count(get_packages_by_game((int)$game['id'])) ?> nominal tersedia</small>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="container home-block">
    <div class="dual-shop-card">
        <div>
            <span class="section-kicker">Jual Beli Akun</span>
            <h2>Stok akun game siap dipilih</h2>

                <a href="beli-akun.php?game_id=1">Mobile Legends</a>
                <a href="beli-akun.php?game_id=2">Free Fire</a>
            </div>
        </div>
        <a href="beli-akun.php" class="btn btn-warning fw-bold">Lihat Beli Akun</a>
    </div>

    <div class="home-account-row">
        <?php foreach (array_slice($accounts, 0, 3) as $account): ?>
            <article class="home-account-card">
                <div class="home-account-image">
                    <?php if (!empty($account['image_url'])): ?>
                        <img src="<?= e(image_src($account['image_url'])) ?>" alt="<?= e($account['title']) ?>">
                    <?php elseif (!empty($account['game_image'])): ?>
                        <img src="<?= e(image_src($account['game_image'])) ?>" alt="<?= e($account['game_name']) ?>">
                    <?php else: ?>
                        <span><?= e($account['game_icon'] ?? '🎮') ?></span>
                    <?php endif; ?>
                    <em><?= e(strtoupper($account['status'] ?? 'TERSEDIA')) ?></em>
                </div>
                <div class="home-account-body">
                    <small><?= e($account['game_name'] ?? 'Game') ?></small>
                    <h3><?= e($account['title']) ?></h3>
                    <p><?= e($account['specs'] ?? $account['description']) ?></p>
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <strong><?= rupiah($account['price']) ?></strong>
                        <a href="beli-akun.php" class="btn btn-sm btn-outline-warning">Detail</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="home-why-section">
    <div class="container">
        <div class="home-section-head text-center">
            <span class="section-kicker">Kenapa AFone?</span>
            <h2>Layanan game dibuat lebih praktis</h2>
        </div>
        <div class="home-why-grid">
            <div class="home-why-card"><div>⚡</div><h4>Proses Kilat</h4><p>Order tersimpan otomatis agar admin bisa langsung cek dan update status.</p></div>
            <div class="home-why-card"><div>⭐</div><h4>Harga Tertata</h4><p>Nominal top up dan harga joki bisa diedit dari CRUD admin.</p></div>
            <div class="home-why-card"><div>🛡️</div><h4>Aman Bergaransi</h4><p>Data order, WhatsApp, dan detail game tercatat lebih rapi di database.</p></div>
        </div>
    </div>
</section>

<section class="container home-block">
    <div class="home-section-head text-center">
        <span class="section-kicker">Joki & Top Up</span>
        <h2>Layanan cepat untuk banyak game</h2>
        <p>Pakai baris kartu horizontal supaya halaman utama terlihat hidup tanpa meniru desain referensi secara mentah.</p>
    </div>
    <div class="home-scroll-row">
        <?php foreach (array_slice($allGames, 0, 10) as $game): ?>
            <a class="home-scroll-chip" href="game.php?slug=<?= e($game['slug']) ?>">
                <span><?php if (!empty($game['image_url'])): ?><img src="<?= e(image_src($game['image_url'])) ?>" alt="<?= e($game['name']) ?>"><?php else: ?><?= e($game['icon_emoji'] ?: '🎮') ?><?php endif; ?></span>
                <b><?= e($game['name']) ?></b>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="container home-block">
    <div class="home-joki-price-card">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-3">
            <div>
                <span class="section-kicker">Joki Rank</span>
                <h2 class="mb-1">Daftar harga joki reguler</h2>
                <p class="text-soft mb-0">Tampilan ringkas di halaman utama, detail lengkap tetap ada di menu Joki.</p>
            </div>
            <a href="Jokigame.php" class="btn btn-warning fw-bold">Buka Kalkulator</a>
        </div>
        <div class="home-joki-mini-grid">
            <?php foreach (array_slice($regularJoki, 0, 6) as $service): ?>
                <div class="home-joki-mini">
                    <span><?= e($service['icon'] ?? '🏆') ?></span>
                    <div><strong><?= e($service['rank_name']) ?></strong><small><?= rupiah($service['price']) ?></small></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="container home-block">
    <div class="home-section-head text-center">
        <span class="section-kicker">Info Terbaru</span>
        <h2>Update seputar game dan promo</h2>
    </div>
    <div class="home-info-grid">
        <article class="home-info-card"><div>💎</div><h4>Promo diamond MLBB</h4><p>Cek nominal favorit seperti Weekly Pass, 86 Diamonds, dan 172 Diamonds.</p><a href="TopUp.php">Cek Top Up</a></article>
        <article class="home-info-card"><div>🔥</div><h4>Bundle Free Fire</h4><p>Siapkan diamond FF untuk event, bundle, dan membership mingguan.</p><a href="game.php?slug=free-fire">Lihat FF</a></article>
        <article class="home-info-card"><div>👑</div><h4>Joki push rank</h4><p>Pakai kalkulator joki untuk menghitung target rank dan layanan express.</p><a href="Jokigame.php">Hitung Joki</a></article>
    </div>
</section>

<section class="container home-block">
    <div class="home-faq-card">
        <div class="home-section-head text-center">
            <span class="section-kicker">FAQ</span>
            <h2>Pertanyaan yang sering ditanyakan</h2>
        </div>
        <div class="accordion accordion-flush" id="homeFaq">
            <div class="accordion-item home-faq-item">
                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">Apa itu AFone Store?</button></h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#homeFaq"><div class="accordion-body">AFone Store adalah website PHP MySQL untuk layanan top up game, joki rank, beli akun, dan pengelolaan data melalui admin panel.</div></div>
            </div>
            <div class="accordion-item home-faq-item">
                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">Apakah nominal diamond bisa diubah?</button></h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#homeFaq"><div class="accordion-body">Bisa. Masuk ke dashboard admin, lalu buka menu CRUD Nominal untuk tambah, edit, hapus, atau menonaktifkan paket.</div></div>
            </div>
            <div class="accordion-item home-faq-item">
                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">Bagaimana menambahkan foto game?</button></h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#homeFaq"><div class="accordion-body">Masuk ke admin, buka CRUD Game, klik tambah atau edit game, lalu gunakan tombol upload gambar.</div></div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
