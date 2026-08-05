<?php
require_once __DIR__ . '/includes/helpers.php';

// Set judul halaman dan halaman aktif untuk navigasi
$pageTitle = 'AFone Store - Top Up Game, Joki, dan Beli Akun';
$activePage = 'home';

// Ambil data yang diperlukan untuk halaman utama
$popularGames = get_popular_games(8);
$allGames = get_games(true);
$gamesForHome = $popularGames ?: array_slice($allGames, 0, 8);
$accounts = get_game_accounts(true);
$regularJoki = get_joki_services('reguler', true);
$topPackages = array_slice(fallback_packages(), 0, 6);

// Sertakan header
require __DIR__ . '/includes/header.php';
?>
<section class="hero-banner">

    <div class="container">

        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">

            <div class="carousel-inner">

                <div class="carousel-item active">
                    <img src="assets/banner/banner.jpg" alt="Banner 1">
                </div>

                <div class="carousel-item">
                    <img src="c:\Users\Hype G12\Downloads\ChatGPT Image 30 Jun 2026, 01.27.08.png" alt="Banner 2">
                </div>

                <div class="carousel-item">
                    <img src="assets/banner/banner3.jpg" alt="Banner 3">
                </div>

            </div>

            <button class="carousel-control-prev"
                    type="button"
                    data-bs-target="#heroCarousel"
                    data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next"
                    type="button"
                    data-bs-target="#heroCarousel"
                    data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

        </div>

    </div>

</section>
<!-- Promo Ticker -->
<section class="home-ticker">
    <div class="container">
        <div class="ticker-wrap">
            <div class="ticker-track">
                <span>🔥 Promo top up hari ini</span>
                <span>💎 Diamond MLBB & FF tersedia</span>
                <span>⚡ Order cepat diproses admin</span>
                <span>🛡️ Data akun aman dan bisa dicek</span>
                <span>🔥 Promo top up hari ini</span>
                <span>💎 Diamond MLBB & FF tersedia</span>
                <span>⚡ Order cepat diproses admin</span>
                <span>🛡️ Data akun aman dan bisa dicek</span>
            </div>
        </div>
    </div>
</section>



<!-- Joki & Top Up -->
<section class="container home-block">
    <div class="home-section-head text-center">

    </div>
   <div class="game-marquee marquee-top">
    <div class="game-track">
        <?php foreach ($allGames as $game): ?>
            <a class="home-scroll-chip" href="game.php?slug=<?= e($game['slug']) ?>">
                <span>
                    <img src="<?= e(image_src($game['image_url'])) ?>">
                </span>
                <b><?= e($game['name']) ?></b>
            </a>
        <?php endforeach; ?>

        <!-- ulangi lagi supaya tidak putus -->
        <?php foreach ($allGames as $game): ?>
            <a class="home-scroll-chip" href="game.php?slug=<?= e($game['slug']) ?>">
                <span>
                    <img src="<?= e(image_src($game['image_url'])) ?>">
                </span>
                <b><?= e($game['name']) ?></b>
            </a>
        <?php endforeach; ?>
    </div>
</div>


</section>
</section>

<!-- Jual Beli Akun -->
<section class="container home-block">
    <div class="dual-shop-card">
        <div>
            <span class="section-kicker">Jual Beli Akun</span>
            <h2>Temukan Akun Game Berkualitas.</h2>
            <a href="beli-akun.php?game_id=1">Mobile Legends</a>
            <a href="beli-akun.php?game_id=2">Free Fire</a>
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




<!-- Info Terbaru -->
<section class="container home-block">
    <div class="home-section-head text-center">
        <span class="section-kicker">Info Terbaru</span>
        <h2>Update seputar game dan promo</h2>
    </div>
    <div class="home-info-grid">
        <article class="home-info-card">
            <div>💎</div>
            <h4>Promo diamond MLBB</h4>
            <p>Cek nominal favorit seperti Weekly Pass, 86 Diamonds, dan 172 Diamonds.</p>
            <a href="TopUp.php">Cek Top Up</a>
        </article>
        <article class="home-info-card">
            <div>🔥</div>
            <h4>Bundle Free Fire</h4>
            <p>Siapkan diamond FF untuk event, bundle, dan membership mingguan.</p>
            <a href="game.php?slug=free-fire">Lihat FF</a>
        </article>
        <article class="home-info-card">
            <div>👑</div>
            <h4>Joki push rank</h4>
            <p>Pakai kalkulator joki untuk menghitung target rank dan layanan express.</p>
            <a href="Jokigame.php">Hitung Joki</a>
        </article>
    </div>
</section>

<!-- FAQ -->
<section class="container home-block">
    <div class="home-faq-card">
        <div class="home-section-head text-center">
            <span class="section-kicker">FAQ</span>
            <h2>Pertanyaan yang sering ditanyakan</h2>
        </div>
        <div class="accordion accordion-flush" id="homeFaq">
            <div class="accordion-item home-faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Apa itu AFone Store?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#homeFaq">
                    <div class="accordion-body">
                        AFone Store adalah website PHP MySQL untuk layanan top up game, joki rank, beli akun, dan pengelolaan data melalui admin panel.
                    </div>
                </div>
            </div>
            <div class="accordion-item home-faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        Apakah nominal diamond bisa diubah?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#homeFaq">
                    <div class="accordion-body">
                        Bisa. Masuk ke dashboard admin, lalu buka menu CRUD Nominal untuk tambah, edit, hapus, atau menonaktifkan paket.
                    </div>
                </div>
            </div>
            <div class="accordion-item home-faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        Bagaimana menambahkan foto game?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#homeFaq">
                    <div class="accordion-body">
                        Masuk ke admin, buka CRUD Game, klik tambah atau edit game, lalu gunakan tombol upload gambar.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Sertakan footer
require __DIR__ . '/includes/footer.php';
?>
