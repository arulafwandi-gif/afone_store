<?php include "cek_login.php"; ?>
<?php
include "koneksi.php";

function rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

$today = date("Y-m-d");

$q1 = mysqli_query($koneksi,"
    SELECT COUNT(*) as total_transaksi,
           SUM(total_harga) as total_penjualan
    FROM transaksi
    WHERE DATE(tanggal)='$today'
");
$todayData = mysqli_fetch_assoc($q1);

$q2 = mysqli_query($koneksi,"SELECT COUNT(*) as total_produk FROM `data barang`");
$totalProduk = mysqli_fetch_assoc($q2);

$q3 = mysqli_query($koneksi,"SELECT COUNT(*) as stok_tipis FROM `data barang` WHERE stok < 5");
$stokTipis = mysqli_fetch_assoc($q3);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Bale Sepatu Mantan — Kasir</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --red:       #C0392B;
      --red-dark:  #922B21;
      --red-light: #FADBD8;
      --red-soft:  #FDEDEC;
      --dark:      #1A1A1A;
      --mid:       #3D3D3D;
      --muted:     #888;
      --border:    #E5E5E5;
      --bg:        #F7F7F7;
      --white:     #FFFFFF;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      min-height: 100vh;
      color: var(--dark);
    }

    /* ── SIDEBAR ── */
    .sidebar {
      position: fixed;
      top: 0; left: 0;
      width: 240px;
      height: 100vh;
      background: var(--dark);
      display: flex;
      flex-direction: column;
      padding: 1.5rem 1rem;
      z-index: 10;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 0.5rem 0.5rem 1.75rem;
      border-bottom: 0.5px solid #333;
      margin-bottom: 1.5rem;
    }

    .brand-logo {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--red);
      flex-shrink: 0;
    }

    .brand-name {
      font-family: 'Playfair Display', serif;
      font-size: 14px;
      color: #fff;
      line-height: 1.3;
    }

    .brand-sub {
      font-size: 11px;
      color: #666;
      margin-top: 2px;
    }

    .nav-label {
      font-size: 10px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #555;
      padding: 0 0.75rem;
      margin-bottom: 0.5rem;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 12px;
      border-radius: 8px;
      font-size: 14px;
      color: #aaa;
      text-decoration: none;
      transition: background 0.12s, color 0.12s;
      margin-bottom: 2px;
    }

    .nav-item i { font-size: 18px; }

    .nav-item:hover {
      background: #2a2a2a;
      color: #fff;
    }

    .nav-item.active {
      background: var(--red);
      color: #fff;
    }

    .nav-spacer { flex: 1; }

.nav-logout {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 14px;
  color: #e74c3c;
  text-decoration: none;
  margin-bottom: 8px;
  transition: background 0.12s;
}
.nav-logout i { font-size: 18px; }
.nav-logout:hover { background: rgba(231,76,60,0.1); color: #ff6b6b; }

.sidebar-footer {
  padding: 10px 0.5rem 0;
  font-size: 11px;
  color: #444;
  border-top: 0.5px solid #2a2a2a;
}

    /* ── MAIN ── */
    .main {
      margin-left: 240px;
      padding: 2rem 2.5rem;
      min-height: 100vh;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 2rem;
    }

    .page-title {
      font-family: 'Playfair Display', serif;
      font-size: 26px;
      color: var(--dark);
    }

    .page-sub {
      font-size: 13px;
      color: var(--muted);
      margin-top: 3px;
    }

    .date-badge {
      display: flex;
      align-items: center;
      gap: 7px;
      background: var(--white);
      border: 0.5px solid var(--border);
      border-radius: 8px;
      padding: 8px 16px;
      font-size: 13px;
      color: var(--mid);
    }

    .date-badge i { color: var(--red); }

    /* ── STATS ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px;
      margin-bottom: 1.75rem;
    }

    .stat-card {
      background: var(--white);
      border: 0.5px solid var(--border);
      border-radius: 12px;
      padding: 1.25rem;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 4px;
      height: 100%;
      background: var(--red);
      border-radius: 12px 0 0 12px;
    }

    .stat-icon {
      width: 38px;
      height: 38px;
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 19px;
      margin-bottom: 0.75rem;
      background: var(--red-soft);
      color: var(--red);
    }

    .stat-label {
      font-size: 12px;
      color: var(--muted);
      margin-bottom: 4px;
    }

    .stat-value {
      font-size: 26px;
      font-weight: 500;
      color: var(--dark);
    }

    .stat-value.rupiah { font-size: 18px; }

    /* ── ALERT ── */
    .alert {
      display: flex;
      align-items: center;
      gap: 12px;
      background: var(--red-soft);
      border: 0.5px solid var(--red-light);
      border-radius: 10px;
      padding: 1rem 1.25rem;
      margin-bottom: 2rem;
      font-size: 14px;
      color: var(--red-dark);
    }

    .alert i { font-size: 20px; flex-shrink: 0; }
    .alert strong { font-weight: 500; }

    /* ── MENU ── */
    .section-title {
      font-size: 12px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.07em;
      color: var(--muted);
      margin-bottom: 1rem;
    }

    .menu-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
    }

    .menu-card {
      background: var(--white);
      border: 0.5px solid var(--border);
      border-radius: 12px;
      padding: 1.25rem 1.5rem;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 16px;
      transition: border-color 0.15s, box-shadow 0.15s;
    }

    .menu-card:hover {
      border-color: var(--red);
      box-shadow: 0 0 0 3px var(--red-soft);
    }

    .menu-card-icon {
      width: 46px;
      height: 46px;
      border-radius: 11px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      flex-shrink: 0;
      background: var(--red-soft);
      color: var(--red);
    }

    .menu-card-label {
      font-weight: 500;
      font-size: 15px;
      color: var(--dark);
    }

    .menu-card-desc {
      font-size: 12px;
      color: var(--muted);
      margin-top: 3px;
    }

    .menu-card-arrow {
      margin-left: auto;
      color: #ccc;
      font-size: 18px;
    }

    .menu-card:hover .menu-card-arrow { color: var(--red); }

    @media (max-width: 900px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 640px) {
      .sidebar { display: none; }
      .main { margin-left: 0; padding: 1rem; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .menu-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<aside class="sidebar">
  <div class="brand">
    <!-- Ganti "logo.png" dengan nama file logo kamu -->
    <img src="508672227_17903328033191189_6852045551131810257_n.jpg" alt="BaleSepatuMantan" class="brand-logo"/>
    <div>
      <div class="brand-name">Bale Sepatu Mantan</div>
      <div class="brand-sub">Sistem Kasir</div>
    </div>
  </div>

  <p class="nav-label">Menu</p>
  <a href="index.php" class="nav-item active">
    <i class="ti ti-layout-dashboard"></i> Dashboard
  </a>
  <a href="data_sepatu.php" class="nav-item">
    <i class="ti ti-shoe"></i> Data Barang
  </a>
  <a href="transaksi.php" class="nav-item">
    <i class="ti ti-shopping-cart"></i> Transaksi
  </a>
  <a href="laporan.php" class="nav-item">
    <i class="ti ti-chart-bar"></i> Laporan
  </a>

  <div class="nav-spacer"></div>

<a href="logout.php" class="nav-logout"><i class="ti ti-logout"></i> Logout</a>

<div class="sidebar-footer">&copy; <?= date('Y') ?> BaleSepatuMantan</div>
</aside>



<main class="main">

  <div class="topbar">
    <div>
      <div class="page-title">Dashboard</div>
      <div class="page-sub">Selamat datang di Sistem Kasir</div>
    </div>
    <div class="date-badge">
      <i class="ti ti-calendar"></i>
      <?= date('l, d F Y') ?>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon"><i class="ti ti-receipt"></i></div>
      <div class="stat-label">Transaksi Hari Ini</div>
      <div class="stat-value"><?= $todayData['total_transaksi'] ?? 0 ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="ti ti-coin"></i></div>
      <div class="stat-label">Penjualan Hari Ini</div>
      <div class="stat-value rupiah"><?= rupiah($todayData['total_penjualan'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="ti ti-box"></i></div>
      <div class="stat-label">Total Produk</div>
      <div class="stat-value"><?= $totalProduk['total_produk'] ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="ti ti-alert-triangle"></i></div>
      <div class="stat-label">Stok Menipis (&lt;5)</div>
      <div class="stat-value"><?= $stokTipis['stok_tipis'] ?></div>
    </div>
  </div>

  <?php if (($stokTipis['stok_tipis'] ?? 0) > 0): ?>
  <div class="alert">
    <i class="ti ti-alert-triangle"></i>
    <span>Terdapat <strong><?= $stokTipis['stok_tipis'] ?> produk</strong> dengan stok di bawah 5 unit. Segera lakukan restok.</span>
  </div>
  <?php endif; ?>

  <p class="section-title">Menu Utama</p>
  <div class="menu-grid">
    <a href="data_sepatu.php" class="menu-card">
      <div class="menu-card-icon"><i class="ti ti-shoe"></i></div>
      <div>
        <div class="menu-card-label">Data Barang</div>
        <div class="menu-card-desc">Kelola produk &amp; stok sepatu</div>
      </div>
      <i class="ti ti-chevron-right menu-card-arrow"></i>
    </a>
    <a href="transaksi.php" class="menu-card">
      <div class="menu-card-icon"><i class="ti ti-shopping-cart"></i></div>
      <div>
        <div class="menu-card-label">Transaksi</div>
        <div class="menu-card-desc">Catat penjualan baru</div>
      </div>
      <i class="ti ti-chevron-right menu-card-arrow"></i>
    </a>
    <a href="laporan.php" class="menu-card">
      <div class="menu-card-icon"><i class="ti ti-chart-bar"></i></div>
      <div>
        <div class="menu-card-label">Laporan</div>
        <div class="menu-card-desc">Riwayat &amp; grafik penjualan</div>
      </div>
      <i class="ti ti-chevron-right menu-card-arrow"></i>
    </a>
    <a href="profil.php" class="menu-card">
      <div class="menu-card-icon"><i class="ti ti-user-circle"></i></div>
      <div>
        <div class="menu-card-label">Profil Toko</div>
        <div class="menu-card-desc">Kelola informasi toko</div>
      </div>
      <i class="ti ti-chevron-right menu-card-arrow"></i>
    </a>
  </div>

</main>
</body>
</html>