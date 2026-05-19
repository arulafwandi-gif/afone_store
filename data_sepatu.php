<?php include "cek_login.php"; ?>
<?php
include "koneksi.php";

function generateKodeSepatu($koneksi){
    $query = mysqli_query($koneksi,"SELECT MAX(kode_sepatu) as kodeTerbesar FROM `data barang`");
    $data  = mysqli_fetch_assoc($query);
    if($data['kodeTerbesar']){
        $urutan = (int) substr($data['kodeTerbesar'], 3, 3);
        $urutan++;
    } else {
        $urutan = 1;
    }
    return "SPT".str_pad($urutan,3,"0",STR_PAD_LEFT);
}

function rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

if(isset($_POST['tambah'])){
    $kode  = generateKodeSepatu($koneksi);
    $nama  = $_POST['nama_sepatu'];
    $size  = $_POST['size'];
    $stok  = $_POST['stok'];
    $modal = $_POST['harga_modal'];
    $jual  = $_POST['harga_jual'];
    mysqli_query($koneksi,"INSERT INTO `data barang` (kode_sepatu,nama_sepatu,size,stok,harga_modal,harga_jual) VALUES('$kode','$nama','$size','$stok','$modal','$jual')");
    header("Location: data_sepatu.php"); exit;
}

if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM `data barang` WHERE id_sepatu='$id'");
    header("Location: data_sepatu.php"); exit;
}

$editData = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $queryEdit = mysqli_query($koneksi,"SELECT * FROM `data barang` WHERE id_sepatu='$id'");
    $editData = mysqli_fetch_assoc($queryEdit);
}

if(isset($_POST['update'])){
    $id    = $_POST['id_sepatu'];
    $nama  = $_POST['nama_sepatu'];
    $size  = $_POST['size'];
    $stok  = $_POST['stok'];
    $modal = $_POST['harga_modal'];
    $jual  = $_POST['harga_jual'];
    mysqli_query($koneksi,"UPDATE `data barang` SET nama_sepatu='$nama',size='$size',stok='$stok',harga_modal='$modal',harga_jual='$jual' WHERE id_sepatu='$id'");
    header("Location: data_sepatu.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Data Barang — BaleSepatuMantan</title>
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
      padding: 1.5rem 1rem; z-index: 10;
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
    .sidebar-footer { margin-top: auto; padding: 0 0.5rem; font-size: 11px; color: #444; }

    /* MAIN */
    .main { margin-left: 240px; padding: 2rem 2.5rem; min-height: 100vh; }

    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .page-title { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); }
    .page-sub { font-size: 13px; color: var(--muted); margin-top: 3px; }

    /* FORM CARD */
    .form-card {
      background: var(--white);
      border: 0.5px solid var(--border);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.75rem;
    }
    .form-card-title {
      font-weight: 500;
      font-size: 15px;
      color: var(--dark);
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .form-card-title i { color: var(--red); font-size: 18px; }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-bottom: 1rem;
    }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-label { font-size: 12px; color: var(--muted); font-weight: 500; }
    .form-input {
      padding: 9px 12px;
      font-size: 14px;
      border: 0.5px solid var(--border);
      border-radius: 8px;
      background: var(--bg);
      color: var(--dark);
      font-family: 'Inter', sans-serif;
      transition: border-color 0.15s;
    }
    .form-input:focus { outline: none; border-color: var(--red); background: var(--white); }

    .form-actions { display: flex; gap: 10px; justify-content: flex-end; }
    .btn-save {
      display: flex; align-items: center; gap: 7px;
      background: var(--red); color: #fff;
      border: none; border-radius: 8px;
      padding: 9px 20px; font-size: 14px; font-weight: 500;
      cursor: pointer; font-family: 'Inter', sans-serif;
      transition: background 0.15s;
    }
    .btn-save:hover { background: var(--red-dark); }
    .btn-cancel {
      display: flex; align-items: center; gap: 7px;
      background: none; color: var(--muted);
      border: 0.5px solid var(--border); border-radius: 8px;
      padding: 9px 18px; font-size: 14px;
      cursor: pointer; font-family: 'Inter', sans-serif;
      text-decoration: none;
      transition: border-color 0.15s, color 0.15s;
    }
    .btn-cancel:hover { border-color: #aaa; color: var(--dark); }

    /* EDIT BANNER */
    .edit-banner {
      background: #FFF8E1;
      border: 0.5px solid #FFE082;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 13px;
      color: #795548;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* TABLE */
    .table-card {
      background: var(--white);
      border: 0.5px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
    }
    .table-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 1.25rem;
      border-bottom: 0.5px solid var(--border);
    }
    .table-title { font-weight: 500; font-size: 15px; color: var(--dark); }
    .table-count { font-size: 12px; color: var(--muted); background: var(--bg); border: 0.5px solid var(--border); border-radius: 20px; padding: 3px 12px; }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead { background: var(--bg); }
    th { text-align: left; padding: 11px 16px; font-weight: 500; color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 0.5px solid var(--border); }
    td { padding: 13px 16px; color: var(--dark); border-bottom: 0.5px solid var(--border); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #FAFAFA; }

    .kode-badge { font-family: monospace; font-size: 12px; background: var(--red-soft); color: var(--red); padding: 3px 8px; border-radius: 5px; font-weight: 500; }

    .stok-ok   { color: #16a34a; font-weight: 500; }
    .stok-low  { color: #d97706; font-weight: 500; }
    .stok-empty{ color: var(--red); font-weight: 500; }

    .btn-edit {
      display: inline-flex; align-items: center; gap: 5px;
      background: none; border: 0.5px solid var(--border);
      border-radius: 6px; padding: 5px 10px;
      font-size: 12px; color: var(--mid);
      text-decoration: none; transition: all 0.12s;
    }
    .btn-edit:hover { border-color: var(--red); color: var(--red); }

    .btn-del {
      display: inline-flex; align-items: center; gap: 5px;
      background: none; border: 0.5px solid var(--border);
      border-radius: 6px; padding: 5px 10px;
      font-size: 12px; color: var(--muted);
      text-decoration: none; transition: all 0.12s;
    }
    .btn-del:hover { border-color: var(--red); color: var(--red); background: var(--red-soft); }

    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--muted); font-size: 14px; }
    .empty-state i { font-size: 36px; display: block; margin-bottom: 0.5rem; color: #ccc; }

    @media (max-width: 900px) { .form-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) {
      .sidebar { display: none; }
      .main { margin-left: 0; padding: 1rem; }
      .form-grid { grid-template-columns: 1fr; }
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
  <a href="index.php" class="nav-item ">
    <i class="ti ti-layout-dashboard"></i> Dashboard
  </a>
  <a href="data_sepatu.php" class="nav-item active">
    <i class="ti ti-shoe"></i> Data Barang
  </a>
  <a href="transaksi.php" class="nav-item">
    <i class="ti ti-shopping-cart"></i> Transaksi
  </a>
  <a href="laporan.php" class="nav-item">
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
      <div class="page-title">Data Barang</div>
      <div class="page-sub">Kelola stok &amp; produk sepatu</div>
    </div>
  </div>

  <!-- FORM TAMBAH / EDIT -->
  <div class="form-card">
    <?php if($editData): ?>
      <div class="edit-banner">
        <i class="ti ti-pencil"></i>
        Mode edit — kamu sedang mengubah data: <strong><?= $editData['nama_sepatu'] ?></strong>
      </div>
    <?php endif; ?>

    <div class="form-card-title">
      <i class="ti ti-<?= $editData ? 'pencil' : 'plus' ?>"></i>
      <?= $editData ? 'Edit Data Barang' : 'Tambah Barang Baru' ?>
    </div>

    <form method="POST">
      <input type="hidden" name="id_sepatu" value="<?= $editData['id_sepatu'] ?? '' ?>">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Nama Sepatu</label>
          <input class="form-input" type="text" name="nama_sepatu" placeholder="Contoh: Nike Air Max" required value="<?= $editData['nama_sepatu'] ?? '' ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label">Size</label>
          <input class="form-input" type="text" name="size" placeholder="Contoh: 40" required value="<?= $editData['size'] ?? '' ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label">Stok</label>
          <input class="form-input" type="number" name="stok" placeholder="0" required value="<?= $editData['stok'] ?? '' ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label">Harga Modal (Rp)</label>
          <input class="form-input" type="number" name="harga_modal" placeholder="0" required value="<?= $editData['harga_modal'] ?? '' ?>"/>
        </div>
        <div class="form-group">
          <label class="form-label">Harga Jual (Rp)</label>
          <input class="form-input" type="number" name="harga_jual" placeholder="0" required value="<?= $editData['harga_jual'] ?? '' ?>"/>
        </div>
      </div>
      <div class="form-actions">
        <?php if($editData): ?>
          <a href="data_sepatu.php" class="btn-cancel"><i class="ti ti-x"></i> Batal</a>
          <button type="submit" name="update" class="btn-save"><i class="ti ti-check"></i> Update</button>
        <?php else: ?>
          <button type="submit" name="tambah" class="btn-save"><i class="ti ti-plus"></i> Simpan Barang</button>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- TABEL -->
  <div class="table-card">
    <div class="table-header">
      <span class="table-title">Daftar Barang</span>
      <?php
        $countQ = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM `data barang`");
        $countD = mysqli_fetch_assoc($countQ);
      ?>
      <span class="table-count"><?= $countD['total'] ?> produk</span>
    </div>

    <table>
      <thead>
        <tr>
          <th>Kode</th>
          <th>Nama Sepatu</th>
          <th>Size</th>
          <th>Stok</th>
          <th>Harga Modal</th>
          <th>Harga Jual</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $data = mysqli_query($koneksi,"SELECT * FROM `data barang` ORDER BY id_sepatu DESC");
        $hasData = false;
        while($d = mysqli_fetch_array($data)):
          $hasData = true;
          if($d['stok'] == 0) $stokClass = 'stok-empty';
          elseif($d['stok'] < 5) $stokClass = 'stok-low';
          else $stokClass = 'stok-ok';
      ?>
        <tr>
          <td><span class="kode-badge"><?= $d['kode_sepatu'] ?></span></td>
          <td style="font-weight:500"><?= $d['nama_sepatu'] ?></td>
          <td><?= $d['size'] ?></td>
          <td class="<?= $stokClass ?>"><?= $d['stok'] ?></td>
          <td><?= rupiah($d['harga_modal']) ?></td>
          <td><?= rupiah($d['harga_jual']) ?></td>
          <td style="display:flex;gap:6px;align-items:center">
            <a href="?edit=<?= $d['id_sepatu'] ?>" class="btn-edit"><i class="ti ti-pencil"></i> Edit</a>
            <a href="?hapus=<?= $d['id_sepatu'] ?>" class="btn-del" onclick="return confirm('Yakin hapus <?= $d['nama_sepatu'] ?>?')"><i class="ti ti-trash"></i> Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
      <?php if(!$hasData): ?>
        <tr><td colspan="7">
          <div class="empty-state">
            <i class="ti ti-shoe"></i>
            Belum ada data barang. Tambah barang pertama kamu!
          </div>
        </td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

</main>
</body>
</html>