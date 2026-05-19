<?php include "cek_login.php"; ?>
<?php
include "koneksi.php";

function rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

/* ===== SUMMARY MINGGUAN ===== */
$qMinggu = mysqli_query($koneksi,"
    SELECT COUNT(*) as total_trx, SUM(total_harga) as total_omzet, SUM(kembalian) as total_kembalian
    FROM transaksi
    WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
");
$minggu = mysqli_fetch_assoc($qMinggu);

/* ===== DETAIL PER HARI (7 hari) ===== */
$qHarian = mysqli_query($koneksi,"
    SELECT DATE(tanggal) as tgl, COUNT(*) as jumlah, SUM(total_harga) as omzet
    FROM transaksi
    WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(tanggal)
    ORDER BY tgl ASC
");
$dataHarian = [];
while($d = mysqli_fetch_assoc($qHarian)) $dataHarian[] = $d;

/* ===== SUMMARY BULANAN ===== */
$qBulan = mysqli_query($koneksi,"
    SELECT COUNT(*) as total_trx, SUM(total_harga) as total_omzet, SUM(kembalian) as total_kembalian
    FROM transaksi
    WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())
");
$bulan = mysqli_fetch_assoc($qBulan);

/* ===== DETAIL PER MINGGU (bulan ini) ===== */
$qPerMinggu = mysqli_query($koneksi,"
    SELECT WEEK(tanggal, 1) as minggu_ke,
           MIN(DATE(tanggal)) as awal,
           MAX(DATE(tanggal)) as akhir,
           COUNT(*) as jumlah,
           SUM(total_harga) as omzet
    FROM transaksi
    WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())
    GROUP BY WEEK(tanggal, 1)
    ORDER BY minggu_ke ASC
");
$dataPerMinggu = [];
$mingguNo = 1;
while($d = mysqli_fetch_assoc($qPerMinggu)){
    $d['no'] = $mingguNo++;
    $dataPerMinggu[] = $d;
}

/* ===== PRODUK TERLARIS MINGGU INI ===== */
$qTerlarisMinggu = mysqli_query($koneksi,"
    SELECT dt.nama_sepatu, SUM(dt.qty) as total_qty, SUM(dt.subtotal) as total_subtotal
    FROM detail_transaksi dt
    JOIN transaksi t ON t.id_transaksi = dt.id_transaksi
    WHERE t.tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY dt.nama_sepatu
    ORDER BY total_qty DESC
    LIMIT 5
");

/* ===== PRODUK TERLARIS BULAN INI ===== */
$qTerlarisBulan = mysqli_query($koneksi,"
    SELECT dt.nama_sepatu, SUM(dt.qty) as total_qty, SUM(dt.subtotal) as total_subtotal
    FROM detail_transaksi dt
    JOIN transaksi t ON t.id_transaksi = dt.id_transaksi
    WHERE MONTH(t.tanggal) = MONTH(CURDATE()) AND YEAR(t.tanggal) = YEAR(CURDATE())
    GROUP BY dt.nama_sepatu
    ORDER BY total_qty DESC
    LIMIT 5
");

/* ===== DATA GRAFIK MINGGUAN (label & omzet) ===== */
$labelMinggu = [];
$omzetMinggu = [];
foreach($dataHarian as $h){
    $labelMinggu[] = date('d M', strtotime($h['tgl']));
    $omzetMinggu[] = (int)$h['omzet'];
}

/* ===== DATA GRAFIK BULANAN (per minggu) ===== */
$labelBulan = [];
$omzetBulan = [];
foreach($dataPerMinggu as $w){
    $labelBulan[] = 'Minggu '.$w['no'];
    $omzetBulan[] = (int)$w['omzet'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Laporan — BaleSepatuMantan</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    /* TAB */
    .tab-bar { display: flex; gap: 4px; background: var(--white); border: 0.5px solid var(--border); border-radius: 10px; padding: 4px; width: fit-content; margin-bottom: 1.75rem; }
    .tab-btn { padding: 8px 22px; border-radius: 7px; font-size: 14px; font-weight: 500; border: none; background: none; cursor: pointer; color: var(--muted); font-family: 'Inter', sans-serif; transition: all 0.15s; }
    .tab-btn.active { background: var(--red); color: #fff; }
    .tab-btn:hover:not(.active) { color: var(--dark); }

    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* SUMMARY CARDS */
    .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 1.75rem; }
    .sum-card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; padding: 1.1rem 1.25rem; position: relative; overflow: hidden; }
    .sum-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--red); border-radius: 12px 0 0 12px; }
    .sum-icon { width: 34px; height: 34px; background: var(--red-soft); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 17px; margin-bottom: 10px; }
    .sum-label { font-size: 12px; color: var(--muted); margin-bottom: 4px; }
    .sum-value { font-size: 22px; font-weight: 500; color: var(--dark); }
    .sum-value.rupiah { font-size: 17px; }

    /* LAYOUT 2 KOLOM */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 1.75rem; }

    /* GRAFIK */
    .chart-card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; padding: 1.25rem; }
    .chart-title { font-weight: 500; font-size: 14px; color: var(--dark); margin-bottom: 1rem; display: flex; align-items: center; gap: 7px; }
    .chart-title i { color: var(--red); }
    canvas { max-height: 220px; }

    /* TABLE */
    .table-card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 1.75rem; }
    .table-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 0.5px solid var(--border); }
    .table-title { font-weight: 500; font-size: 14px; color: var(--dark); display: flex; align-items: center; gap: 8px; }
    .table-title i { color: var(--red); }
    .badge-count { font-size: 11px; color: var(--muted); background: var(--bg); border: 0.5px solid var(--border); border-radius: 20px; padding: 3px 10px; }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead { background: var(--bg); }
    th { text-align: left; padding: 10px 16px; font-weight: 500; color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 0.5px solid var(--border); }
    td { padding: 12px 16px; border-bottom: 0.5px solid var(--border); vertical-align: middle; color: var(--dark); }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #FAFAFA; }

    .rank-badge { width: 24px; height: 24px; border-radius: 6px; background: var(--red-soft); color: var(--red); font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; justify-content: center; }
    .rank-badge.gold   { background: #FEF9C3; color: #854D0E; }
    .rank-badge.silver { background: #F1F5F9; color: #475569; }
    .rank-badge.bronze { background: #FEF3C7; color: #92400E; }

    .qty-bar-wrap { display: flex; align-items: center; gap: 8px; }
    .qty-bar { height: 6px; background: var(--red); border-radius: 3px; min-width: 4px; }

    .empty-state { text-align: center; padding: 2.5rem 1rem; color: var(--muted); font-size: 13px; }
    .empty-state i { font-size: 32px; display: block; margin-bottom: 0.5rem; color: #ccc; }

    @media (max-width: 960px) { .two-col { grid-template-columns: 1fr; } .summary-grid { grid-template-columns: repeat(2, 1fr); } }
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
  <a href="transaksi.php" class="nav-item">
    <i class="ti ti-shopping-cart"></i> Transaksi
  </a>
  <a href="laporan.php" class="nav-item active">
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
      <div class="page-title">Laporan Penjualan</div>
      <div class="page-sub">Rekap mingguan &amp; bulanan BaleSepatuMantan</div>
    </div>
    <div style="font-size:13px;color:var(--muted);background:var(--white);border:0.5px solid var(--border);border-radius:8px;padding:8px 16px;display:flex;align-items:center;gap:7px;">
      <i class="ti ti-calendar" style="color:var(--red)"></i>
      <?= date('d F Y') ?>
    </div>
  </div>

  <!-- TAB -->
  <div class="tab-bar">
    <button class="tab-btn active" onclick="ganti('minggu', this)">
      <i class="ti ti-calendar-week" style="margin-right:5px"></i>Mingguan
    </button>
    <button class="tab-btn" onclick="ganti('bulan', this)">
      <i class="ti ti-calendar-month" style="margin-right:5px"></i>Bulanan
    </button>
  </div>

  <!-- ==================== TAB MINGGUAN ==================== -->
  <div class="tab-content active" id="tab-minggu">

    <div class="summary-grid">
      <div class="sum-card">
        <div class="sum-icon"><i class="ti ti-receipt"></i></div>
        <div class="sum-label">Total Transaksi (7 hari)</div>
        <div class="sum-value"><?= $minggu['total_trx'] ?? 0 ?></div>
      </div>
      <div class="sum-card">
        <div class="sum-icon"><i class="ti ti-coin"></i></div>
        <div class="sum-label">Total Omzet (7 hari)</div>
        <div class="sum-value rupiah"><?= rupiah($minggu['total_omzet'] ?? 0) ?></div>
      </div>
      <div class="sum-card">
        <div class="sum-icon"><i class="ti ti-arrow-back-up"></i></div>
        <div class="sum-label">Total Kembalian (7 hari)</div>
        <div class="sum-value rupiah"><?= rupiah($minggu['total_kembalian'] ?? 0) ?></div>
      </div>
    </div>

    <div class="two-col">
      <!-- GRAFIK OMZET -->
      <div class="chart-card">
        <div class="chart-title"><i class="ti ti-chart-line"></i> Grafik Omzet Harian</div>
        <canvas id="grafikMinggu"></canvas>
      </div>

      <!-- PRODUK TERLARIS -->
      <div class="chart-card">
        <div class="chart-title"><i class="ti ti-trophy"></i> Produk Terlaris Minggu Ini</div>
        <?php
          $terlarisMinggu = [];
          $maxQtyM = 1;
          while($r = mysqli_fetch_assoc($qTerlarisMinggu)) $terlarisMinggu[] = $r;
          if(!empty($terlarisMinggu)) $maxQtyM = $terlarisMinggu[0]['total_qty'];
        ?>
        <?php if(empty($terlarisMinggu)): ?>
          <div class="empty-state"><i class="ti ti-mood-empty"></i>Belum ada data</div>
        <?php else: ?>
          <table>
            <thead><tr><th>#</th><th>Nama Produk</th><th>Terjual</th><th>Omzet</th></tr></thead>
            <tbody>
            <?php foreach($terlarisMinggu as $i => $r):
              $rankClass = $i==0?'gold':($i==1?'silver':($i==2?'bronze':''));
              $barW = round(($r['total_qty']/$maxQtyM)*100);
            ?>
              <tr>
                <td><span class="rank-badge <?= $rankClass ?>"><?= $i+1 ?></span></td>
                <td style="font-weight:500"><?= $r['nama_sepatu'] ?></td>
                <td>
                  <div class="qty-bar-wrap">
                    <span><?= $r['total_qty'] ?> pcs</span>
                    <div class="qty-bar" style="width:<?= $barW ?>px"></div>
                  </div>
                </td>
                <td><?= rupiah($r['total_subtotal']) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <!-- TABEL DETAIL HARIAN -->
    <div class="table-card">
      <div class="table-header">
        <div class="table-title"><i class="ti ti-table"></i> Detail Per Hari</div>
        <span class="badge-count"><?= count($dataHarian) ?> hari</span>
      </div>
      <?php if(empty($dataHarian)): ?>
        <div class="empty-state"><i class="ti ti-receipt-off"></i>Belum ada transaksi minggu ini</div>
      <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Hari</th>
            <th>Jumlah Transaksi</th>
            <th>Total Omzet</th>
            <th>Rata-rata/Trx</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($dataHarian as $h):
          $rataRata = $h['jumlah'] > 0 ? $h['omzet'] / $h['jumlah'] : 0;
        ?>
          <tr>
            <td style="font-weight:500"><?= date('d M Y', strtotime($h['tgl'])) ?></td>
            <td style="color:var(--muted)"><?= date('l', strtotime($h['tgl'])) ?></td>
            <td><?= $h['jumlah'] ?> transaksi</td>
            <td style="font-weight:500;color:var(--red)"><?= rupiah($h['omzet']) ?></td>
            <td style="color:var(--muted)"><?= rupiah($rataRata) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

  </div><!-- end tab-minggu -->

  <!-- ==================== TAB BULANAN ==================== -->
  <div class="tab-content" id="tab-bulan">

    <div class="summary-grid">
      <div class="sum-card">
        <div class="sum-icon"><i class="ti ti-receipt"></i></div>
        <div class="sum-label">Total Transaksi (<?= date('F Y') ?>)</div>
        <div class="sum-value"><?= $bulan['total_trx'] ?? 0 ?></div>
      </div>
      <div class="sum-card">
        <div class="sum-icon"><i class="ti ti-coin"></i></div>
        <div class="sum-label">Total Omzet (<?= date('F Y') ?>)</div>
        <div class="sum-value rupiah"><?= rupiah($bulan['total_omzet'] ?? 0) ?></div>
      </div>
      <div class="sum-card">
        <div class="sum-icon"><i class="ti ti-arrow-back-up"></i></div>
        <div class="sum-label">Total Kembalian (<?= date('F Y') ?>)</div>
        <div class="sum-value rupiah"><?= rupiah($bulan['total_kembalian'] ?? 0) ?></div>
      </div>
    </div>

    <div class="two-col">
      <!-- GRAFIK PER MINGGU -->
      <div class="chart-card">
        <div class="chart-title"><i class="ti ti-chart-bar"></i> Grafik Omzet Per Minggu</div>
        <canvas id="grafikBulan"></canvas>
      </div>

      <!-- PRODUK TERLARIS BULAN -->
      <div class="chart-card">
        <div class="chart-title"><i class="ti ti-trophy"></i> Produk Terlaris Bulan Ini</div>
        <?php
          $terlarisBulan = [];
          $maxQtyB = 1;
          while($r = mysqli_fetch_assoc($qTerlarisBulan)) $terlarisBulan[] = $r;
          if(!empty($terlarisBulan)) $maxQtyB = $terlarisBulan[0]['total_qty'];
        ?>
        <?php if(empty($terlarisBulan)): ?>
          <div class="empty-state"><i class="ti ti-mood-empty"></i>Belum ada data</div>
        <?php else: ?>
          <table>
            <thead><tr><th>#</th><th>Nama Produk</th><th>Terjual</th><th>Omzet</th></tr></thead>
            <tbody>
            <?php foreach($terlarisBulan as $i => $r):
              $rankClass = $i==0?'gold':($i==1?'silver':($i==2?'bronze':''));
              $barW = round(($r['total_qty']/$maxQtyB)*100);
            ?>
              <tr>
                <td><span class="rank-badge <?= $rankClass ?>"><?= $i+1 ?></span></td>
                <td style="font-weight:500"><?= $r['nama_sepatu'] ?></td>
                <td>
                  <div class="qty-bar-wrap">
                    <span><?= $r['total_qty'] ?> pcs</span>
                    <div class="qty-bar" style="width:<?= $barW ?>px"></div>
                  </div>
                </td>
                <td><?= rupiah($r['total_subtotal']) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <!-- TABEL DETAIL PER MINGGU -->
    <div class="table-card">
      <div class="table-header">
        <div class="table-title"><i class="ti ti-table"></i> Detail Per Minggu</div>
        <span class="badge-count"><?= count($dataPerMinggu) ?> minggu</span>
      </div>
      <?php if(empty($dataPerMinggu)): ?>
        <div class="empty-state"><i class="ti ti-receipt-off"></i>Belum ada transaksi bulan ini</div>
      <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Minggu Ke</th>
            <th>Periode</th>
            <th>Jumlah Transaksi</th>
            <th>Total Omzet</th>
            <th>Rata-rata/Trx</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($dataPerMinggu as $w):
          $rataRata = $w['jumlah'] > 0 ? $w['omzet'] / $w['jumlah'] : 0;
        ?>
          <tr>
            <td><span class="rank-badge">W<?= $w['no'] ?></span></td>
            <td style="font-weight:500"><?= date('d M', strtotime($w['awal'])) ?> – <?= date('d M Y', strtotime($w['akhir'])) ?></td>
            <td><?= $w['jumlah'] ?> transaksi</td>
            <td style="font-weight:500;color:var(--red)"><?= rupiah($w['omzet']) ?></td>
            <td style="color:var(--muted)"><?= rupiah($rataRata) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

  </div><!-- end tab-bulan -->

</main>

<script>
/* ===== TAB SWITCH ===== */
function ganti(id, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + id).classList.add('active');
  btn.classList.add('active');
}

const RED       = '#C0392B';
const RED_SOFT  = 'rgba(192,57,43,0.12)';
const MUTED     = '#888';

/* ===== GRAFIK MINGGUAN ===== */
const labelMinggu = <?= json_encode($labelMinggu) ?>;
const omzetMinggu = <?= json_encode($omzetMinggu) ?>;

new Chart(document.getElementById('grafikMinggu'), {
  type: 'line',
  data: {
    labels: labelMinggu.length ? labelMinggu : ['Belum ada data'],
    datasets: [{
      label: 'Omzet (Rp)',
      data: omzetMinggu.length ? omzetMinggu : [0],
      borderColor: RED,
      backgroundColor: RED_SOFT,
      borderWidth: 2,
      pointBackgroundColor: RED,
      pointRadius: 4,
      fill: true,
      tension: 0.35
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID'), color: MUTED, font: { size: 11 } },
        grid: { color: '#F0F0F0' }
      },
      x: { ticks: { color: MUTED, font: { size: 11 } }, grid: { display: false } }
    }
  }
});

/* ===== GRAFIK BULANAN ===== */
const labelBulan = <?= json_encode($labelBulan) ?>;
const omzetBulan = <?= json_encode($omzetBulan) ?>;

new Chart(document.getElementById('grafikBulan'), {
  type: 'bar',
  data: {
    labels: labelBulan.length ? labelBulan : ['Belum ada data'],
    datasets: [{
      label: 'Omzet (Rp)',
      data: omzetBulan.length ? omzetBulan : [0],
      backgroundColor: RED_SOFT,
      borderColor: RED,
      borderWidth: 2,
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID'), color: MUTED, font: { size: 11 } },
        grid: { color: '#F0F0F0' }
      },
      x: { ticks: { color: MUTED, font: { size: 11 } }, grid: { display: false } }
    }
  }
});
</script>

</body>
</html>