<?php
include "cek_login.php";
include "koneksi.php";

function rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

/* Ambil data pengaturan toko */
$qSet = mysqli_query($koneksi,"SELECT * FROM pengaturan WHERE id=1");
$set  = mysqli_fetch_assoc($qSet);

/* Statistik toko */
$qTrxTotal = mysqli_query($koneksi,"SELECT COUNT(*) as total, SUM(total_harga) as omzet FROM transaksi");
$trxTotal  = mysqli_fetch_assoc($qTrxTotal);

$qProduk = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM `data barang`");
$produk  = mysqli_fetch_assoc($qProduk);

$qBulan = mysqli_query($koneksi,"SELECT COUNT(*) as total, SUM(total_harga) as omzet FROM transaksi WHERE MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())");
$bulan  = mysqli_fetch_assoc($qBulan);

$qHari = mysqli_query($koneksi,"SELECT COUNT(*) as total, SUM(total_harga) as omzet FROM transaksi WHERE DATE(tanggal)=CURDATE()");
$hari  = mysqli_fetch_assoc($qHari);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profil Toko — BaleSepatuMantan</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --red:      #C0392B;
      --red-dark: #922B21;
      --red-light:#FADBD8;
      --red-soft: #FDEDEC;
      --dark:     #1A1A1A;
      --mid:      #3D3D3D;
      --muted:    #888;
      --border:   #E5E5E5;
      --bg:       #F7F7F7;
      --white:    #FFFFFF;
    }
    body { font-family: 'Inter', sans-serif; background: var(--bg); min-height: 100vh; color: var(--dark); }

    /* SIDEBAR */
    .sidebar { position: fixed; top: 0; left: 0; width: 240px; height: 100vh; background: var(--dark); display: flex; flex-direction: column; padding: 1.5rem 1rem 1rem; z-index: 10; }
    .brand { display: flex; align-items: center; gap: 12px; padding: 0.5rem 0.5rem 1.75rem; border-bottom: 0.5px solid #333; margin-bottom: 1.5rem; }
    .brand-logo { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--red); flex-shrink: 0; }
    .brand-name { font-family: 'Playfair Display', serif; font-size: 14px; color: #fff; line-height: 1.3; }
    .brand-sub { font-size: 11px; color: #666; margin-top: 2px; }
    .nav-label { font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.08em; color: #555; padding: 0 0.75rem; margin-bottom: 0.5rem; }
    .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; font-size: 14px; color: #aaa; text-decoration: none; transition: background 0.12s, color 0.12s; margin-bottom: 2px; }
    .nav-item i { font-size: 18px; }
    .nav-item:hover { background: #2a2a2a; color: #fff; }
    .nav-item.active { background: var(--red); color: #fff; }
    .nav-spacer { flex: 1; }
    .nav-logout { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; font-size: 14px; color: #e74c3c; text-decoration: none; margin-bottom: 8px; transition: background 0.12s; }
    .nav-logout i { font-size: 18px; }
    .nav-logout:hover { background: rgba(231,76,60,0.1); color: #ff6b6b; }
    .sidebar-footer { padding: 10px 0.5rem 0; font-size: 11px; color: #444; border-top: 0.5px solid #2a2a2a; }

    /* MAIN */
    .main { margin-left: 240px; padding: 2rem 2.5rem; min-height: 100vh; }
    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .page-title { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); }
    .page-sub { font-size: 13px; color: var(--muted); margin-top: 3px; }
    .edit-btn { display: inline-flex; align-items: center; gap: 7px; background: var(--red); color: #fff; border: none; border-radius: 8px; padding: 9px 18px; font-size: 13px; font-weight: 500; text-decoration: none; transition: background 0.15s; }
    .edit-btn:hover { background: var(--red-dark); }

    /* HERO CARD */
    .hero-card {
      background: var(--dark);
      border-radius: 16px;
      padding: 2.5rem;
      margin-bottom: 1.75rem;
      display: flex;
      align-items: center;
      gap: 2rem;
      position: relative;
      overflow: hidden;
    }
    .hero-card::before {
      content: '';
      position: absolute;
      width: 300px; height: 300px;
      border-radius: 50%;
      border: 50px solid rgba(192,57,43,0.1);
      top: -100px; right: -80px;
    }
    .hero-card::after {
      content: '';
      position: absolute;
      width: 200px; height: 200px;
      border-radius: 50%;
      border: 30px solid rgba(192,57,43,0.07);
      bottom: -80px; left: 200px;
    }

    .hero-logo {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--red);
      box-shadow: 0 0 0 8px rgba(192,57,43,0.12);
      flex-shrink: 0;
      position: relative;
      z-index: 1;
    }

    .hero-info { position: relative; z-index: 1; }

    .hero-name {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      color: #fff;
      margin-bottom: 4px;
    }

    .hero-handle {
      font-size: 13px;
      color: #555;
      margin-bottom: 1rem;
    }

    .hero-tags { display: flex; gap: 8px; flex-wrap: wrap; }

    .hero-tag {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      background: rgba(255,255,255,0.05);
      border: 0.5px solid #333;
      border-radius: 20px;
      padding: 4px 12px;
      font-size: 12px;
      color: #aaa;
    }
    .hero-tag i { font-size: 14px; color: var(--red); }

    /* STATS ROW */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 1.75rem; }
    .stat-card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; padding: 1.25rem; position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--red); border-radius: 12px 0 0 12px; }
    .stat-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 0.75rem; background: var(--red-soft); color: var(--red); }
    .stat-label { font-size: 12px; color: var(--muted); margin-bottom: 4px; }
    .stat-value { font-size: 22px; font-weight: 500; color: var(--dark); }
    .stat-value.sm { font-size: 16px; }

    /* LAYOUT 2 KOLOM */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

    /* INFO CARD */
    .info-card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; overflow: hidden; }
    .info-card-header { padding: 1rem 1.25rem; border-bottom: 0.5px solid var(--border); display: flex; align-items: center; gap: 10px; }
    .info-card-icon { width: 32px; height: 32px; background: var(--red-soft); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 16px; }
    .info-card-title { font-weight: 500; font-size: 15px; color: var(--dark); }
    .info-card-body { padding: 0; }

    .info-row { display: flex; align-items: flex-start; gap: 12px; padding: 14px 1.25rem; border-bottom: 0.5px solid var(--border); }
    .info-row:last-child { border-bottom: none; }
    .info-row-icon { width: 32px; height: 32px; background: var(--bg); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 16px; flex-shrink: 0; }
    .info-row-label { font-size: 11px; color: var(--muted); margin-bottom: 2px; }
    .info-row-value { font-size: 14px; font-weight: 500; color: var(--dark); }

    /* KASIR CARD */
    .kasir-wrap { display: flex; align-items: center; gap: 14px; padding: 1rem 1.25rem; border-bottom: 0.5px solid var(--border); }
    .kasir-wrap:last-child { border-bottom: none; }
    .kasir-avatar { width: 40px; height: 40px; background: var(--red-soft); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 20px; flex-shrink: 0; }
    .kasir-nama { font-weight: 500; font-size: 14px; color: var(--dark); }
    .kasir-role { font-size: 12px; color: var(--muted); margin-top: 1px; }
    .kasir-badge { margin-left: auto; font-size: 11px; background: #ECFDF5; color: #065F46; border: 0.5px solid #A7F3D0; border-radius: 20px; padding: 3px 10px; }

    @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2,1fr); } .two-col { grid-template-columns: 1fr; } }
    @media (max-width: 640px) { .sidebar { display: none; } .main { margin-left: 0; padding: 1rem; } .hero-card { flex-direction: column; text-align: center; } }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="brand">
    <img src="508672227_17903328033191189_6852045551131810257_n.jpg" alt="BaleSepatuMantan" class="brand-logo"/>
    <div>
      <div class="brand-name">Bale Sepatu Mantan</div>
      <div class="brand-sub">Sistem Kasir</div>
    </div>
  </div>
  <p class="nav-label">Menu</p>
  <a href="index.php" class="nav-item"><i class="ti ti-layout-dashboard"></i> Dashboard</a>
  <a href="data_sepatu.php" class="nav-item"><i class="ti ti-shoe"></i> Data Barang</a>
  <a href="transaksi.php" class="nav-item"><i class="ti ti-shopping-cart"></i> Transaksi</a>
  <a href="laporan.php" class="nav-item"><i class="ti ti-chart-bar"></i> Laporan</a>
  <a href="profil.php" class="nav-item active"><i class="ti ti-user-circle"></i> Profil</a>
  <div class="nav-spacer"></div>
  <a href="logout.php" class="nav-logout"><i class="ti ti-logout"></i> Logout</a>
  <div class="sidebar-footer">&copy; <?= date('Y') ?> BaleSepatuMantan</div>
</aside>

<!-- MAIN -->
<main class="main">

  <div class="topbar">
    <div>
      <div class="page-title">Profil Toko</div>
      <div class="page-sub">Informasi lengkap BaleSepatuMantan</div>
    </div>
    <a href="pengaturan.php" class="edit-btn">
      <i class="ti ti-pencil"></i> Edit Profil
    </a>
  </div>

  <!-- HERO -->
  <div class="hero-card">
    <img src="508672227_17903328033191189_6852045551131810257_n.jpg" alt="Logo" class="hero-logo"/>
    <div class="hero-info">
      <div class="hero-name"><?= htmlspecialchars($set['nama_toko'] ?? 'BaleSepatuMantan') ?></div>
      <div class="hero-handle"><?= htmlspecialchars($set['instagram'] ?? '@balesepatumantan') ?></div>
      <div class="hero-tags">
        <?php if($set['no_hp']): ?>
        <span class="hero-tag"><i class="ti ti-phone"></i> <?= htmlspecialchars($set['no_hp']) ?></span>
        <?php endif; ?>
        <?php if($set['alamat']): ?>
        <span class="hero-tag"><i class="ti ti-map-pin"></i> <?= htmlspecialchars($set['alamat']) ?></span>
        <?php endif; ?>
        <span class="hero-tag"><i class="ti ti-shoe"></i> Toko Sepatu</span>
      </div>
    </div>
  </div>

  <!-- STATISTIK -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon"><i class="ti ti-receipt"></i></div>
      <div class="stat-label">Total Transaksi</div>
      <div class="stat-value"><?= $trxTotal['total'] ?? 0 ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="ti ti-coin"></i></div>
      <div class="stat-label">Total Omzet</div>
      <div class="stat-value sm"><?= rupiah($trxTotal['omzet'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="ti ti-shopping-cart"></i></div>
      <div class="stat-label">Transaksi Bulan Ini</div>
      <div class="stat-value"><?= $bulan['total'] ?? 0 ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="ti ti-box"></i></div>
      <div class="stat-label">Total Produk</div>
      <div class="stat-value"><?= $produk['total'] ?? 0 ?></div>
    </div>
  </div>

  <!-- INFO + KASIR -->
  <div class="two-col">

    <!-- INFO TOKO -->
    <div class="info-card">
      <div class="info-card-header">
        <div class="info-card-icon"><i class="ti ti-building-store"></i></div>
        <div class="info-card-title">Informasi Toko</div>
      </div>
      <div class="info-card-body">
        <div class="info-row">
          <div class="info-row-icon"><i class="ti ti-building-store"></i></div>
          <div>
            <div class="info-row-label">Nama Toko</div>
            <div class="info-row-value"><?= htmlspecialchars($set['nama_toko'] ?? '-') ?></div>
          </div>
        </div>
        <div class="info-row">
          <div class="info-row-icon"><i class="ti ti-map-pin"></i></div>
          <div>
            <div class="info-row-label">Alamat</div>
            <div class="info-row-value"><?= htmlspecialchars($set['alamat'] ?? '-') ?></div>
          </div>
        </div>
        <div class="info-row">
          <div class="info-row-icon"><i class="ti ti-phone"></i></div>
          <div>
            <div class="info-row-label">No. HP / WhatsApp</div>
            <div class="info-row-value"><?= htmlspecialchars($set['no_hp'] ?? '-') ?></div>
          </div>
        </div>
        <div class="info-row">
          <div class="info-row-icon"><i class="ti ti-brand-instagram"></i></div>
          <div>
            <div class="info-row-label">Instagram</div>
            <div class="info-row-value"><?= htmlspecialchars($set['instagram'] ?? '-') ?></div>
          </div>
        </div>
        <div class="info-row">
          <div class="info-row-icon"><i class="ti ti-notes"></i></div>
          <div>
            <div class="info-row-label">Catatan Struk</div>
            <div class="info-row-value"><?= htmlspecialchars($set['catatan_struk'] ?? '-') ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- DAFTAR KASIR -->
    <div class="info-card">
      <div class="info-card-header">
        <div class="info-card-icon"><i class="ti ti-users"></i></div>
        <div class="info-card-title">Daftar Kasir</div>
      </div>
      <div class="info-card-body">
        <?php
          $qUser = mysqli_query($koneksi,"SELECT * FROM user ORDER BY id ASC");
          while($u = mysqli_fetch_assoc($qUser)):
            $isLogin = ($u['nama'] === ($_SESSION['nama'] ?? ''));
        ?>
        <div class="kasir-wrap">
          <div class="kasir-avatar"><i class="ti ti-user"></i></div>
          <div>
            <div class="kasir-nama"><?= htmlspecialchars($u['nama']) ?></div>
            <div class="kasir-role">@<?= htmlspecialchars($u['username']) ?></div>
          </div>
          <?php if($isLogin): ?>
          <span class="kasir-badge">● Login sekarang</span>
          <?php endif; ?>
        </div>
        <?php endwhile; ?>
      </div>
    </div>

  </div>

</main>
</body>
</html>