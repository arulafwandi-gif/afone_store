<?php
include "koneksi.php";

function rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

$id = $_GET['id'];

$qTrx = mysqli_query($koneksi,"SELECT * FROM transaksi WHERE id_transaksi='$id'");
$trx  = mysqli_fetch_assoc($qTrx);

$qDetail = mysqli_query($koneksi,"SELECT * FROM detail_transaksi WHERE id_transaksi='$id'");
$items = [];
while($d = mysqli_fetch_assoc($qDetail)) $items[] = $d;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Struk #<?= str_pad($id, 5, '0', STR_PAD_LEFT) ?> — BaleSepatuMantan</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Playfair+Display:wght@600&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --red:    #C0392B;
      --dark:   #1A1A1A;
      --muted:  #888;
      --border: #E5E5E5;
      --bg:     #F7F7F7;
      --white:  #FFFFFF;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      padding: 2rem 1rem;
    }

    /* TOMBOL AKSI - tidak ikut print */
    .action-bar {
      display: flex;
      gap: 10px;
      margin-bottom: 1.5rem;
      width: 100%;
      max-width: 400px;
    }

    .btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      padding: 10px 16px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 500;
      font-family: 'Inter', sans-serif;
      cursor: pointer;
      text-decoration: none;
      border: none;
      transition: all 0.15s;
    }

    .btn-back {
      background: var(--white);
      border: 0.5px solid var(--border);
      color: var(--dark);
    }
    .btn-back:hover { border-color: #aaa; }

    .btn-print {
      background: var(--red);
      color: #fff;
    }
    .btn-print:hover { background: #922B21; }

    /* STRUK */
    .struk {
      background: var(--white);
      width: 100%;
      max-width: 400px;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    }

    /* HEADER STRUK */
    .struk-header {
      background: var(--dark);
      padding: 1.75rem 1.5rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .struk-header::before {
      content: '';
      position: absolute;
      width: 200px; height: 200px;
      border-radius: 50%;
      border: 30px solid rgba(192,57,43,0.15);
      top: -80px; left: -60px;
    }
    .struk-header::after {
      content: '';
      position: absolute;
      width: 150px; height: 150px;
      border-radius: 50%;
      border: 20px solid rgba(192,57,43,0.1);
      bottom: -60px; right: -40px;
    }

    .struk-logo {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      border: 2px solid var(--red);
      object-fit: cover;
      margin: 0 auto 0.75rem;
      display: block;
      position: relative;
      z-index: 1;
    }

    .struk-toko {
      font-family: 'Playfair Display', serif;
      font-size: 20px;
      color: #fff;
      position: relative;
      z-index: 1;
    }

    .struk-handle {
      font-size: 11px;
      color: #555;
      margin-top: 2px;
      position: relative;
      z-index: 1;
    }

    .struk-divider-red {
      width: 36px;
      height: 2px;
      background: var(--red);
      margin: 10px auto;
      border-radius: 2px;
      position: relative;
      z-index: 1;
    }

    .struk-id {
      font-family: 'DM Mono', monospace;
      font-size: 12px;
      color: #555;
      position: relative;
      z-index: 1;
    }

    /* ZIGZAG */
    .zigzag {
      width: 100%;
      height: 16px;
      background:
        radial-gradient(circle at 8px 0, var(--bg) 8px, var(--white) 8px) repeat-x;
      background-size: 16px 16px;
    }

    /* BODY STRUK */
    .struk-body { padding: 1.25rem 1.5rem; }

    .struk-meta {
      display: flex;
      justify-content: space-between;
      font-size: 12px;
      color: var(--muted);
      margin-bottom: 1.25rem;
      padding-bottom: 1rem;
      border-bottom: 1px dashed var(--border);
    }

    .struk-meta span:last-child { text-align: right; }

    /* ITEMS */
    .item-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding: 8px 0;
      border-bottom: 0.5px solid #F5F5F5;
      font-size: 13px;
    }

    .item-row:last-child { border-bottom: none; }

    .item-nama { font-weight: 500; color: var(--dark); margin-bottom: 2px; }
    .item-qty  { font-size: 11px; color: var(--muted); font-family: 'DM Mono', monospace; }
    .item-sub  { font-weight: 500; color: var(--dark); white-space: nowrap; padding-left: 12px; }

    /* TOTAL SECTION */
    .struk-total {
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px dashed var(--border);
    }

    .total-line {
      display: flex;
      justify-content: space-between;
      font-size: 13px;
      padding: 5px 0;
      color: var(--muted);
    }

    .total-line .val { color: var(--dark); font-weight: 500; }

    .total-line.grand {
      margin-top: 8px;
      padding-top: 10px;
      border-top: 1px solid var(--border);
      font-size: 15px;
    }

    .total-line.grand .label { font-weight: 500; color: var(--dark); }
    .total-line.grand .val   { color: var(--red); font-family: 'Playfair Display', serif; font-size: 20px; }

    .total-line.kembalian .val { color: #16a34a; }

    /* FOOTER STRUK */
    .zigzag-bottom {
      width: 100%;
      height: 16px;
      background:
        radial-gradient(circle at 8px 100%, var(--bg) 8px, var(--white) 8px) repeat-x bottom;
      background-size: 16px 16px;
    }

    .struk-footer {
      padding: 1.25rem 1.5rem;
      text-align: center;
      border-top: 0.5px solid var(--border);
    }

    .struk-thanks {
      font-family: 'Playfair Display', serif;
      font-size: 16px;
      color: var(--dark);
      margin-bottom: 4px;
    }

    .struk-note {
      font-size: 11px;
      color: var(--muted);
      line-height: 1.6;
    }

    .struk-barcode {
      font-family: 'DM Mono', monospace;
      font-size: 10px;
      color: #ccc;
      letter-spacing: 0.3em;
      margin-top: 10px;
    }

    /* PRINT */
    @media print {
      body { background: #fff; padding: 0; }
      .action-bar { display: none; }
      .struk { box-shadow: none; border-radius: 0; max-width: 100%; }
    }
  </style>
</head>
<body>

<!-- TOMBOL AKSI -->
<div class="action-bar">
  <a href="detail_transaksi.php?id=<?= $id ?>" class="btn btn-back">
    ← Kembali
  </a>
  <button class="btn btn-print" onclick="window.print()">
    🖨 Cetak Struk
  </button>
</div>

<!-- STRUK -->
<div class="struk">

  <!-- HEADER -->
  <div class="struk-header">
    <img src="508672227_17903328033191189_6852045551131810257_n.jpg" alt="Logo" class="struk-logo"/>
    <div class="struk-toko">BaleSepatuMantan</div>
    <div class="struk-handle">@balesepatumantan</div>
    <div class="struk-divider-red"></div>
    <div class="struk-id">Struk #<?= str_pad($id, 5, '0', STR_PAD_LEFT) ?></div>
  </div>

  <!-- ZIGZAG ATAS -->
  <div class="zigzag"></div>

  <!-- BODY -->
  <div class="struk-body">

    <!-- META INFO -->
    <div class="struk-meta">
      <span>
        📅 <?= date('d M Y', strtotime($trx['tanggal'])) ?><br>
        🕐 <?= date('H:i', strtotime($trx['tanggal'])) ?> WITA
      </span>
      <span>
        Kasir<br>
        <strong style="color:var(--dark)"><?= $_SESSION['nama'] ?? 'Admin' ?></strong>
      </span>
    </div>

    <!-- ITEM LIST -->
    <?php foreach($items as $d): ?>
    <div class="item-row">
      <div>
        <div class="item-nama"><?= $d['nama_sepatu'] ?></div>
        <div class="item-qty"><?= $d['qty'] ?> pcs × <?= rupiah($d['harga']) ?></div>
      </div>
      <div class="item-sub"><?= rupiah($d['subtotal']) ?></div>
    </div>
    <?php endforeach; ?>

    <!-- TOTAL -->
    <div class="struk-total">
      <div class="total-line">
        <span class="label">Subtotal</span>
        <span class="val"><?= rupiah($trx['total_harga']) ?></span>
      </div>
      <div class="total-line">
        <span class="label">Uang Diterima</span>
        <span class="val"><?= rupiah($trx['bayar']) ?></span>
      </div>
      <div class="total-line grand">
        <span class="label">Total</span>
        <span class="val"><?= rupiah($trx['total_harga']) ?></span>
      </div>
      <div class="total-line kembalian">
        <span class="label">Kembalian</span>
        <span class="val"><?= rupiah($trx['kembalian']) ?></span>
      </div>
    </div>

  </div>

  <!-- ZIGZAG BAWAH -->
  <div class="zigzag-bottom"></div>

  <!-- FOOTER -->
  <div class="struk-footer">
    <div class="struk-thanks">Terima Kasih! 🙏</div>
    <div class="struk-note">
      Barang yang sudah dibeli<br>tidak dapat dikembalikan
    </div>
    <div class="struk-barcode">
      ||| <?= str_pad($id, 5, '0', STR_PAD_LEFT) ?> ||| <?= date('dmY', strtotime($trx['tanggal'])) ?> |||
    </div>
  </div>

</div>

</body>
</html>