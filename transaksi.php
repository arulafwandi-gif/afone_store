<?php include "cek_login.php"; ?>
<?php
include "koneksi.php";

function rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}


if(!isset($_SESSION['keranjang'])){
    $_SESSION['keranjang'] = [];
}

if(isset($_GET['tambah'])){
    $kode = $_GET['tambah'];
    $ambil = mysqli_query($koneksi,"SELECT * FROM `data barang` WHERE kode_sepatu='$kode'");
    $data = mysqli_fetch_assoc($ambil);
    $_SESSION['keranjang'][$kode] = [
        "nama"  => $data['nama_sepatu'],
        "harga" => $data['harga_jual'],
        "qty"   => 1
    ];
    header("Location: transaksi.php"); exit;
}



if(isset($_POST['bayar'])){
    $bayar = $_POST['uang'];
    $total = 0;
    foreach($_SESSION['keranjang'] as $item){
        $total += $item['harga'] * $item['qty'];
    }
    $kembalian = $bayar - $total;
    mysqli_query($koneksi,"INSERT INTO transaksi(total_harga,bayar,kembalian) VALUES('$total','$bayar','$kembalian')");
    $id_transaksi = mysqli_insert_id($koneksi);
    foreach($_SESSION['keranjang'] as $kode => $item){
        $subtotal = $item['harga'] * $item['qty'];
        mysqli_query($koneksi,"INSERT INTO detail_transaksi(id_transaksi,kode_sepatu,nama_sepatu,qty,harga,subtotal) VALUES('$id_transaksi','$kode','$item[nama]','$item[qty]','$item[harga]','$subtotal')");
        mysqli_query($koneksi,"UPDATE `data barang` SET stok = stok - $item[qty] WHERE kode_sepatu='$kode'");
    }
    $_SESSION['keranjang'] = [];
    header("Location: struk.php?id=$id_transaksi"); exit;
}

$total = 0;
foreach($_SESSION['keranjang'] as $item){
    $total += $item['harga'] * $item['qty'];
}
$jumlahKeranjang = count($_SESSION['keranjang']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Transaksi — BaleSepatuMantan</title>
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

    /* MAIN — 2 KOLOM */
    .main { margin-left: 240px; padding: 2rem 2.5rem; min-height: 100vh; }
    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .page-title { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); }
    .page-sub { font-size: 13px; color: var(--muted); margin-top: 3px; }
    .riwayat-btn { display: flex; align-items: center; gap: 7px; background: var(--white); border: 0.5px solid var(--border); border-radius: 8px; padding: 8px 16px; font-size: 13px; color: var(--mid); text-decoration: none; transition: border-color 0.15s; }
    .riwayat-btn:hover { border-color: var(--red); color: var(--red); }

    .layout { display: grid; grid-template-columns: 1fr 360px; gap: 20px; align-items: start; }

    /* PRODUK */
    .card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; overflow: hidden; }
    .card-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 0.5px solid var(--border); }
    .card-title { font-weight: 500; font-size: 15px; color: var(--dark); display: flex; align-items: center; gap: 8px; }
    .card-title i { color: var(--red); }
    .badge-count { font-size: 11px; color: var(--muted); background: var(--bg); border: 0.5px solid var(--border); border-radius: 20px; padding: 3px 10px; }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead { background: var(--bg); }
    th { text-align: left; padding: 10px 16px; font-weight: 500; color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 0.5px solid var(--border); }
    td { padding: 12px 16px; border-bottom: 0.5px solid var(--border); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #FAFAFA; }

    .kode-badge { font-family: monospace; font-size: 11px; background: var(--red-soft); color: var(--red); padding: 2px 7px; border-radius: 4px; }
    .stok-ok    { color: #16a34a; font-weight: 500; }
    .stok-low   { color: #d97706; font-weight: 500; }
    .stok-empty { color: var(--red); font-weight: 500; }

    .btn-tambah {
      display: inline-flex; align-items: center; gap: 5px;
      background: var(--red); color: #fff;
      border: none; border-radius: 6px;
      padding: 6px 12px; font-size: 12px; font-weight: 500;
      text-decoration: none; transition: background 0.15s;
    }
    .btn-tambah:hover { background: var(--red-dark); }
    .btn-tambah.disabled { background: #ccc; pointer-events: none; }

    /* KERANJANG */
    .keranjang-wrap { display: flex; flex-direction: column; gap: 16px; }

    .keranjang-list { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; overflow: hidden; }
    .keranjang-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-bottom: 0.5px solid var(--border); }
    .keranjang-item:last-child { border-bottom: none; }
    .item-nama { font-weight: 500; font-size: 14px; color: var(--dark); }
    .item-sub  { font-size: 12px; color: var(--muted); margin-top: 2px; }
    .item-price { font-weight: 500; font-size: 14px; color: var(--dark); text-align: right; }
    .btn-hapus { background: none; border: 0.5px solid var(--border); border-radius: 6px; padding: 4px 8px; font-size: 13px; color: var(--muted); text-decoration: none; transition: all 0.12s; display: inline-flex; align-items: center; }
    .btn-hapus:hover { border-color: var(--red); color: var(--red); background: var(--red-soft); }

    .keranjang-empty { padding: 2rem 1rem; text-align: center; color: var(--muted); font-size: 13px; }
    .keranjang-empty i { font-size: 32px; display: block; margin-bottom: 0.5rem; color: #ccc; }

    /* TOTAL + BAYAR */
    .bayar-card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; padding: 1.25rem; }
    .total-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 0.5px solid var(--border); }
    .total-label { font-size: 13px; color: var(--muted); }
    .total-value { font-family: 'Playfair Display', serif; font-size: 22px; color: var(--red); }

    .form-label { font-size: 12px; color: var(--muted); font-weight: 500; margin-bottom: 5px; display: block; }
    .form-input { width: 100%; padding: 10px 12px; font-size: 14px; border: 0.5px solid var(--border); border-radius: 8px; background: var(--bg); color: var(--dark); font-family: 'Inter', sans-serif; margin-bottom: 1rem; transition: border-color 0.15s; }
    .form-input:focus { outline: none; border-color: var(--red); background: var(--white); }

    .btn-bayar { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: var(--red); color: #fff; border: none; border-radius: 8px; padding: 12px; font-size: 15px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; transition: background 0.15s; }
    .btn-bayar:hover { background: var(--red-dark); }
    .btn-bayar:disabled { background: #ccc; cursor: not-allowed; }

    @media (max-width: 960px) { .layout { grid-template-columns: 1fr; } }
    @media (max-width: 640px) { .sidebar { display: none; } .main { margin-left: 0; padding: 1rem; } }
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
  <a href="laporan.php" class="nav-item">
    <i class="ti ti-chart-bar"></i> Laporan
  </a>

 <div class="nav-spacer"></div>

<a href="logout.php" class="nav-logout"><i class="ti ti-logout"></i> Logout</a>

<div class="sidebar-footer">&copy; <?= date('Y') ?> BaleSepatuMantan</div>
</aside>

<!-- MAIN -->
<main class="main">

  <div class="topbar">
    <div>
      <div class="page-title">Transaksi</div>
      <div class="page-sub">Tambah produk ke keranjang lalu proses pembayaran</div>
    </div>
    <a href="riwayat.php" class="riwayat-btn">
      <i class="ti ti-history"></i> Riwayat Transaksi
    </a>
  </div>

  <div class="layout">

    <!-- KIRI: DAFTAR PRODUK -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="ti ti-shoe"></i> Daftar Produk</div>
        <?php
          $cq = mysqli_query($koneksi,"SELECT COUNT(*) as t FROM `data barang`");
          $cd = mysqli_fetch_assoc($cq);
        ?>
        <span class="badge-count"><?= $cd['t'] ?> produk</span>
      </div>
      <table>
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Sepatu</th>
            <th>Harga Jual</th>
            <th>Stok</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $produk = mysqli_query($koneksi,"SELECT * FROM `data barang` ORDER BY nama_sepatu ASC");
          while($d = mysqli_fetch_array($produk)):
            if($d['stok'] == 0) $sc = 'stok-empty';
            elseif($d['stok'] < 5) $sc = 'stok-low';
            else $sc = 'stok-ok';
            $sudahDiKeranjang = isset($_SESSION['keranjang'][$d['kode_sepatu']]);
        ?>
          <tr>
            <td><span class="kode-badge"><?= $d['kode_sepatu'] ?></span></td>
            <td style="font-weight:500"><?= $d['nama_sepatu'] ?></td>
            <td><?= rupiah($d['harga_jual']) ?></td>
            <td class="<?= $sc ?>"><?= $d['stok'] ?></td>
            <td>
              <?php if($d['stok'] > 0 && !$sudahDiKeranjang): ?>
                <a href="?tambah=<?= $d['kode_sepatu'] ?>" class="btn-tambah">
                  <i class="ti ti-plus"></i> Tambah
                </a>
              <?php elseif($sudahDiKeranjang): ?>
                <span style="font-size:12px;color:#16a34a;display:flex;align-items:center;gap:4px">
                  <i class="ti ti-check"></i> Ditambahkan
                </span>
              <?php else: ?>
                <span style="font-size:12px;color:#ccc">Stok habis</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- KANAN: KERANJANG + BAYAR -->
    <div class="keranjang-wrap">

      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="ti ti-shopping-cart"></i> Keranjang</div>
          <span class="badge-count"><?= $jumlahKeranjang ?> item</span>
        </div>

        <?php if($jumlahKeranjang > 0): ?>
          <div class="keranjang-list">
          <?php foreach($_SESSION['keranjang'] as $kode => $item):
            $sub = $item['harga'] * $item['qty'];
          ?>
            <div class="keranjang-item">
              <div>
                <div class="item-nama"><?= $item['nama'] ?></div>
                <div class="item-sub"><?= rupiah($item['harga']) ?> × <?= $item['qty'] ?></div>
              </div>
              <div style="display:flex;align-items:center;gap:10px">
                <div class="item-price"><?= rupiah($sub) ?></div>
                <a href="?hapus=<?= $kode ?>" class="btn-hapus" title="Hapus"><i class="ti ti-x"></i></a>
              </div>
            </div>
          <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="keranjang-empty">
            <i class="ti ti-shopping-cart-off"></i>
            Keranjang masih kosong.<br>Tambahkan produk dari daftar.
          </div>
        <?php endif; ?>
      </div>

      <!-- BAYAR -->
      <div class="bayar-card">
        <div class="total-row">
          <span class="total-label">Total Pembayaran</span>
          <span class="total-value"><?= rupiah($total) ?></span>
        </div>
        <form method="POST">
          <label class="form-label">Uang Diterima (Rp)</label>
          <input class="form-input" type="number" name="uang" placeholder="Masukkan nominal uang..." required min="<?= $total ?>"/>
          <button class="btn-bayar" name="bayar" <?= $jumlahKeranjang == 0 ? 'disabled' : '' ?>>
            <i class="ti ti-receipt"></i>
            Proses Transaksi
          </button>
        </form>
      </div>

    </div>
  </div>

</main>
</body>
</html>