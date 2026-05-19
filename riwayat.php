<?php include "cek_login.php"; 
include "koneksi.php"; ?>
<?php

function rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Riwayat Transaksi — Bale Sepatu Mantan</title>
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
    .sidebar { position: fixed; top: 0; left: 0; width: 240px; height: 100vh; background: var(--dark); display: flex; flex-direction: column; padding: 1.5rem 1rem; z-index: 10; }
    .brand { display: flex; align-items: center; gap: 12px; padding: 0.5rem 0.5rem 1.75rem; border-bottom: 0.5px solid #333; margin-bottom: 1.5rem; }
    .brand-logo { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--red); flex-shrink: 0; }
    .brand-name { font-family: 'Playfair Display', serif; font-size: 14px; color: #fff; line-height: 1.3; }
    .brand-sub { font-size: 11px; color: #666; margin-top: 2px; }
    .nav-label { font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.08em; color: #555; padding: 0 0.75rem; margin-bottom: 0.5rem; }
    .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; font-size: 14px; color: #aaa; text-decoration: none; transition: background 0.12s, color 0.12s; margin-bottom: 2px; }
    .nav-item i { font-size: 18px; }
    .nav-item:hover { background: #2a2a2a; color: #fff; }
    .nav-item.active { background: var(--red); color: #fff; }
    .sidebar-footer { margin-top: auto; padding: 0 0.5rem; font-size: 11px; color: #444; }

    /* MAIN */
    .main { margin-left: 240px; padding: 2rem 2.5rem; min-height: 100vh; }

    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .page-title { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); }
    .page-sub { font-size: 13px; color: var(--muted); margin-top: 3px; }

    .top-actions { display: flex; gap: 10px; }
    .action-btn { display: flex; align-items: center; gap: 7px; background: var(--white); border: 0.5px solid var(--border); border-radius: 8px; padding: 8px 16px; font-size: 13px; color: var(--mid); text-decoration: none; transition: border-color 0.15s, color 0.15s; }
    .action-btn:hover { border-color: var(--red); color: var(--red); }
    .action-btn.primary { background: var(--red); color: #fff; border-color: var(--red); }
    .action-btn.primary:hover { background: var(--red-dark); }

    /* SUMMARY CARDS */
    .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 1.75rem; }
    .sum-card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; padding: 1.1rem 1.25rem; position: relative; overflow: hidden; }
    .sum-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--red); border-radius: 12px 0 0 12px; }
    .sum-label { font-size: 12px; color: var(--muted); margin-bottom: 4px; }
    .sum-value { font-size: 20px; font-weight: 500; color: var(--dark); }
    .sum-value.rupiah { font-size: 16px; }

    /* TABLE */
    .table-card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; overflow: hidden; }
    .table-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 0.5px solid var(--border); }
    .table-title { font-weight: 500; font-size: 15px; color: var(--dark); display: flex; align-items: center; gap: 8px; }
    .table-title i { color: var(--red); }
    .badge-count { font-size: 11px; color: var(--muted); background: var(--bg); border: 0.5px solid var(--border); border-radius: 20px; padding: 3px 10px; }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead { background: var(--bg); }
    th { text-align: left; padding: 10px 16px; font-weight: 500; color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 0.5px solid var(--border); }
    td { padding: 13px 16px; border-bottom: 0.5px solid var(--border); vertical-align: middle; color: var(--dark); }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #FAFAFA; }

    .no-badge { width: 26px; height: 26px; background: var(--bg); border: 0.5px solid var(--border); border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; color: var(--muted); }
    .tanggal-main { font-weight: 500; font-size: 13px; color: var(--dark); }
    .tanggal-sub  { font-size: 11px; color: var(--muted); margin-top: 1px; }
    .kembalian-val { color: #16a34a; font-weight: 500; }

    .btn-detail { display: inline-flex; align-items: center; gap: 5px; background: none; border: 0.5px solid var(--border); border-radius: 6px; padding: 5px 10px; font-size: 12px; color: var(--mid); text-decoration: none; transition: all 0.12s; }
    .btn-detail:hover { border-color: var(--red); color: var(--red); }

    .btn-print { display: inline-flex; align-items: center; gap: 5px; background: var(--red-soft); border: 0.5px solid var(--red-light); border-radius: 6px; padding: 5px 10px; font-size: 12px; color: var(--red); text-decoration: none; transition: all 0.12s; }
    .btn-print:hover { background: var(--red-light); }

    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--muted); font-size: 14px; }
    .empty-state i { font-size: 36px; display: block; margin-bottom: 0.5rem; color: #ccc; }

    @media (max-width: 900px) { .summary-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .sidebar { display: none; } .main { margin-left: 0; padding: 1rem; } .summary-grid { grid-template-columns: 1fr 1fr; } }
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
  <a href="index.php" class="nav-item ">
    <i class="ti ti-layout-dashboard"></i> Dashboard
  </a>
  <a href="data_sepatu.php" class="nav-item">
    <i class="ti ti-shoe"></i> Data Barang
  </a>
  <a href="transaksi.php" class="nav-item active">
    <i class="ti ti-shopping-cart"></i> Transaksi
  </a>
  <a href="laporan.php" class="nav-item ">
    <i class="ti ti-chart-bar"></i> Laporan
  </a>

  <a href="logout.php" class="nav-item" style="color:#ff6b6b; margin-bottom: 8px;">
  <i class="ti ti-logout"></i> Logout
</a>

  <div class="sidebar-footer">
    &copy; <?= date('Y') ?> BaleSepatuMantan
  </div>
  
</aside>

<!-- MAIN -->
<main class="main">

  <div class="topbar">
    <div>
      <div class="page-title">Riwayat Transaksi</div>
      <div class="page-sub">Semua catatan penjualan BaleSepatuMantan</div>
    </div>
    <div class="top-actions">
      <a href="transaksi.php" class="action-btn primary">
        <i class="ti ti-shopping-cart"></i> Transaksi Baru
      </a>
    </div>
  </div>

  <!-- SUMMARY -->
  <?php
    $sq = mysqli_query($koneksi,"SELECT COUNT(*) as total_trx, SUM(total_harga) as total_omzet, SUM(kembalian) as total_kembalian FROM transaksi");
    $sd = mysqli_fetch_assoc($sq);
  ?>
  <div class="summary-grid">
    <div class="sum-card">
      <div class="sum-label">Total Transaksi</div>
      <div class="sum-value"><?= $sd['total_trx'] ?></div>
    </div>
    <div class="sum-card">
      <div class="sum-label">Total Omzet</div>
      <div class="sum-value rupiah"><?= rupiah($sd['total_omzet'] ?? 0) ?></div>
    </div>
    <div class="sum-card">
      <div class="sum-label">Total Kembalian</div>
      <div class="sum-value rupiah"><?= rupiah($sd['total_kembalian'] ?? 0) ?></div>
    </div>
  </div>

  <!-- TABEL RIWAYAT -->
  <div class="table-card">
    <div class="table-header">
      <div class="table-title"><i class="ti ti-history"></i> Daftar Transaksi</div>
      <span class="badge-count"><?= $sd['total_trx'] ?> transaksi</span>
    </div>
    <table>
      <thead>
        <tr>
          <th style="width:50px">No</th>
          <th>Tanggal &amp; Waktu</th>
          <th>Total</th>
          <th>Bayar</th>
          <th>Kembalian</th>
          <th style="width:140px">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $no = 1;
        $data = mysqli_query($koneksi,"SELECT * FROM transaksi ORDER BY id_transaksi DESC");
        $hasData = false;
        while($d = mysqli_fetch_array($data)):
          $hasData = true;
          $tgl = date('d M Y', strtotime($d['tanggal']));
          $jam = date('H:i', strtotime($d['tanggal']));
      ?>
        <tr>
          <td><span class="no-badge"><?= $no++ ?></span></td>
          <td>
            <div class="tanggal-main"><?= $tgl ?></div>
            <div class="tanggal-sub"><?= $jam ?> WIB</div>
          </td>
          <td style="font-weight:500"><?= rupiah($d['total_harga']) ?></td>
          <td><?= rupiah($d['bayar']) ?></td>
          <td class="kembalian-val"><?= rupiah($d['kembalian']) ?></td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="detail_transaksi.php?id=<?= $d['id_transaksi'] ?>" class="btn-detail">
                <i class="ti ti-eye"></i> Detail
              </a>
              <a href="struk.php?id=<?= $d['id_transaksi'] ?>" class="btn-print">
                <i class="ti ti-printer"></i> Struk
              </a>
              
            </div>
          </td>
        </tr>
      <?php endwhile; ?>
      <?php if(!$hasData): ?>
        <tr><td colspan="6">
          <div class="empty-state">
            <i class="ti ti-receipt-off"></i>
            Belum ada transaksi. Mulai transaksi pertama kamu!
          </div>
        </td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

</main>
</body>
</html>