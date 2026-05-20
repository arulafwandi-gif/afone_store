<?php
include "cek_login.php";
include "koneksi.php";

function rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM detail_transaksi WHERE id_transaksi='$id'");
    mysqli_query($koneksi,"DELETE FROM transaksi WHERE id_transaksi='$id'");
    header("Location: riwayat.php"); exit;
}

$id = $_GET['id'];

/* Data transaksi utama */
$qTrx = mysqli_query($koneksi,"SELECT * FROM transaksi WHERE id_transaksi='$id'");
$trx  = mysqli_fetch_assoc($qTrx);

/* Detail item */
$qDetail = mysqli_query($koneksi,"SELECT * FROM detail_transaksi WHERE id_transaksi='$id'");
$items = [];
while($d = mysqli_fetch_assoc($qDetail)) $items[] = $d;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Transaksi — BaleSepatuMantan</title>
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
    .sidebar {
      position: fixed; top: 0; left: 0;
      width: 240px; height: 100vh;
      background: var(--dark);
      display: flex; flex-direction: column;
      padding: 1.5rem 1rem 1rem;
      z-index: 10;
    }
    .brand { display: flex; align-items: center; gap: 12px; padding: 0.5rem 0.5rem 1.75rem; border-bottom: 0.5px solid #333; margin-bottom: 1.5rem; }
    .brand-logo { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--red); flex-shrink: 0; }
    .brand-name { font-family: 'Playfair Display', serif; font-size: 14px; color: #fff; line-height: 1.3; }
    .brand-sub { font-size: 11px; color: #666; margin-top: 2px; }
    .nav-label { font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.08em; color: #555; padding: 0 0.75rem; margin-bottom: 0.5rem; }
    .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; font-size: 14px; color: #aaa; text-decoration: none; transition: background 0.12s, color 0.12s; margin-bottom: 2px; }
    .nav-item i { font-size: 18px; }
    .nav-item:hover { background: #2a2a2a; color: #fff; }
    .nav-item.active { background: var(--red); color: #fff; }

    /* SPACER dorong logout + footer ke bawah */
    .nav-spacer { flex: 1; }

    .nav-logout { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; font-size: 14px; color: #e74c3c; text-decoration: none; margin-bottom: 8px; transition: background 0.12s; }
    .nav-logout i { font-size: 18px; }
    .nav-logout:hover { background: rgba(231,76,60,0.1); color: #ff6b6b; }

    .sidebar-footer {
      padding: 10px 0.5rem 0;
      font-size: 11px;
      color: #444;
      border-top: 0.5px solid #2a2a2a;
    }

    /* MAIN */
    .main { margin-left: 240px; padding: 2rem 2.5rem; min-height: 100vh; }
    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .page-title { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); }
    .page-sub { font-size: 13px; color: var(--muted); margin-top: 3px; }

    .top-actions { display: flex; gap: 10px; }
    .action-btn { display: inline-flex; align-items: center; gap: 7px; background: var(--white); border: 0.5px solid var(--border); border-radius: 8px; padding: 8px 16px; font-size: 13px; color: var(--mid); text-decoration: none; transition: border-color 0.15s, color 0.15s; }
    .action-btn:hover { border-color: var(--red); color: var(--red); }
    .action-btn.red { background: var(--red-soft); border-color: var(--red-light); color: var(--red); }
    .action-btn.red:hover { background: var(--red-light); }
    .action-btn.primary { background: var(--red); color: #fff; border-color: var(--red); }
    .action-btn.primary:hover { background: var(--red-dark); }

    /* INFO CARDS */
    .info-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 1.75rem; }
    .info-card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; padding: 1rem 1.25rem; position: relative; overflow: hidden; }
    .info-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--red); border-radius: 12px 0 0 12px; }
    .info-label { font-size: 11px; color: var(--muted); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.04em; }
    .info-value { font-size: 16px; font-weight: 500; color: var(--dark); }

    /* TABLE */
    .table-card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 1.5rem; }
    .table-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 0.5px solid var(--border); }
    .table-title { font-weight: 500; font-size: 15px; color: var(--dark); display: flex; align-items: center; gap: 8px; }
    .table-title i { color: var(--red); }
    .badge-count { font-size: 11px; color: var(--muted); background: var(--bg); border: 0.5px solid var(--border); border-radius: 20px; padding: 3px 10px; }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead { background: var(--bg); }
    th { text-align: left; padding: 10px 16px; font-weight: 500; color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 0.5px solid var(--border); }
    td { padding: 13px 16px; border-bottom: 0.5px solid var(--border); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #FAFAFA; }

    /* TOTAL BOX */
    .total-box { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; padding: 1.25rem 1.5rem; }
    .total-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 0.5px solid var(--border); font-size: 14px; }
    .total-row:last-child { border-bottom: none; }
    .total-row .label { color: var(--muted); }
    .total-row .val { font-weight: 500; color: var(--dark); }
    .total-row.kembalian .val { color: #16a34a; }

    /* MODAL */
    .modal-bg { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 100; align-items: center; justify-content: center; }
    .modal-bg.open { display: flex; }
    .modal-box { background: var(--white); border-radius: 14px; padding: 2rem; width: 100%; max-width: 360px; text-align: center; }
    .modal-icon { width: 52px; height: 52px; background: var(--red-soft); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
    .modal-icon i { font-size: 26px; color: var(--red); }
    .modal-title { font-weight: 500; font-size: 16px; color: var(--dark); margin-bottom: 8px; }
    .modal-desc { font-size: 13px; color: var(--muted); margin-bottom: 1.5rem; line-height: 1.6; }
    .modal-actions { display: flex; gap: 10px; justify-content: center; }
    .modal-cancel { background: none; border: 0.5px solid var(--border); border-radius: 8px; padding: 9px 20px; font-size: 14px; color: var(--muted); cursor: pointer; font-family: 'Inter', sans-serif; }
    .modal-confirm { background: var(--red); color: #fff; border: none; border-radius: 8px; padding: 9px 20px; font-size: 14px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-block; }
    .modal-confirm:hover { background: var(--red-dark); }

    @media (max-width: 900px) { .info-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .sidebar { display: none; } .main { margin-left: 0; padding: 1rem; } }
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

  <!-- spacer dorong logout & footer ke paling bawah -->
  <div class="nav-spacer"></div>

  <a href="logout.php" class="nav-logout"><i class="ti ti-logout"></i> Logout</a>

  <div class="sidebar-footer">&copy; <?= date('Y') ?> BaleSepatuMantan</div>
</aside>

<!-- MODAL HAPUS -->
<div class="modal-bg" id="modalHapus">
  <div class="modal-box">
    <div class="modal-icon"><i class="ti ti-trash"></i></div>
    <div class="modal-title">Hapus Transaksi?</div>
    <div class="modal-desc">Data transaksi ini akan dihapus permanen dan tidak bisa dikembalikan.</div>
    <div class="modal-actions">
      <button class="modal-cancel" onclick="document.getElementById('modalHapus').classList.remove('open')">Batal</button>
      <a href="?hapus=<?= $id ?>" class="modal-confirm">Ya, Hapus</a>
    </div>
  </div>
</div>

<!-- MAIN -->
<main class="main">

  <div class="topbar">
    <div>
      <div class="page-title">Detail Transaksi</div>
      <div class="page-sub">ID Transaksi #<?= str_pad($id, 5, '0', STR_PAD_LEFT) ?></div>
    </div>
    <div class="top-actions">
      <a href="riwayat.php" class="action-btn"><i class="ti ti-arrow-left"></i> Kembali</a>
      <a href="struk.php?id=<?= $id ?>" class="action-btn primary"><i class="ti ti-printer"></i> Cetak Struk</a>
      <a href="#" class="action-btn red" onclick="document.getElementById('modalHapus').classList.add('open'); return false;">
        <i class="ti ti-trash"></i> Hapus
      </a>
    </div>
  </div>

  <!-- INFO CARDS -->
  <div class="info-grid">
    <div class="info-card">
      <div class="info-label">ID Transaksi</div>
      <div class="info-value">#<?= str_pad($id, 5, '0', STR_PAD_LEFT) ?></div>
    </div>
    <div class="info-card">
      <div class="info-label">Tanggal</div>
      <div class="info-value"><?= date('d M Y', strtotime($trx['tanggal'])) ?></div>
    </div>
    <div class="info-card">
      <div class="info-label">Waktu</div>
      <div class="info-value"><?= date('H:i', strtotime($trx['tanggal'])) ?> WITA</div>
    </div>
    <div class="info-card">
      <div class="info-label">Jumlah Item</div>
      <div class="info-value"><?= count($items) ?> produk</div>
    </div>
  </div>

  <!-- TABEL ITEM -->
  <div class="table-card">
    <div class="table-header">
      <div class="table-title"><i class="ti ti-receipt"></i> Item Pembelian</div>
      <span class="badge-count"><?= count($items) ?> item</span>
    </div>
    <table>
      <thead>
        <tr>
          <th style="width:40px">No</th>
          <th>Nama Sepatu</th>
          <th style="width:80px">Qty</th>
          <th>Harga Satuan</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($items as $i => $d): ?>
        <tr>
          <td style="color:var(--muted)"><?= $i+1 ?></td>
          <td style="font-weight:500"><?= $d['nama_sepatu'] ?></td>
          <td><?= $d['qty'] ?> pcs</td>
          <td><?= rupiah($d['harga']) ?></td>
          <td style="font-weight:500;color:var(--red)"><?= rupiah($d['subtotal']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- TOTAL -->
  <div style="max-width:380px;margin-left:auto;">
    <div class="total-box">
      <div class="total-row">
        <span class="label">Total Belanja</span>
        <span class="val"><?= rupiah($trx['total_harga']) ?></span>
      </div>
      <div class="total-row">
        <span class="label">Uang Diterima</span>
        <span class="val"><?= rupiah($trx['bayar']) ?></span>
      </div>
      <div class="total-row kembalian">
        <span class="label">Kembalian</span>
        <span class="val"><?= rupiah($trx['kembalian']) ?></span>
      </div>
    </div>
  </div>

</main>
</body>
</html>