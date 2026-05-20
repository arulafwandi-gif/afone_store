<?php
include "cek_login.php";
include "koneksi.php";

function rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

$sukses = '';
$error  = '';

/* ============================
   UPDATE PROFIL TOKO
============================ */
if(isset($_POST['simpan_profil'])){
    $nama     = $_POST['nama_toko'];
    $alamat   = $_POST['alamat'];
    $hp       = $_POST['no_hp'];
    $ig       = $_POST['instagram'];
    $catatan  = $_POST['catatan_struk'];

    mysqli_query($koneksi,"UPDATE pengaturan SET
        nama_toko='$nama',
        alamat='$alamat',
        no_hp='$hp',
        instagram='$ig',
        catatan_struk='$catatan'
        WHERE id=1");

    $sukses = "Profil toko berhasil disimpan!";
}

/* ============================
   GANTI PASSWORD
============================ */
if(isset($_POST['ganti_password'])){
    $user        = $_SESSION['nama'] ?? '';
    $pass_lama   = $_POST['pass_lama'];
    $pass_baru   = $_POST['pass_baru'];
    $pass_ulang  = $_POST['pass_ulang'];

    /* Cek password lama */
    $cek = mysqli_query($koneksi,"SELECT * FROM user WHERE nama='$user' AND password='$pass_lama'");

    if(mysqli_num_rows($cek) == 0){
        $error = "Password lama salah!";
    } elseif($pass_baru !== $pass_ulang){
        $error = "Konfirmasi password baru tidak cocok!";
    } elseif(strlen($pass_baru) < 6){
        $error = "Password baru minimal 6 karakter!";
    } else {
        mysqli_query($koneksi,"UPDATE user SET password='$pass_baru' WHERE nama='$user'");
        $sukses = "Password berhasil diubah!";
    }
}

/* Ambil data pengaturan */
$qSet = mysqli_query($koneksi,"SELECT * FROM pengaturan WHERE id=1");
$set  = mysqli_fetch_assoc($qSet);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pengaturan — BaleSepatuMantan</title>
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

    /* ALERT */
    .alert-sukses {
      display: flex; align-items: center; gap: 10px;
      background: #ECFDF5; border: 0.5px solid #A7F3D0;
      border-radius: 10px; padding: 12px 16px;
      font-size: 13px; color: #065F46;
      margin-bottom: 1.5rem;
    }
    .alert-error {
      display: flex; align-items: center; gap: 10px;
      background: var(--red-soft); border: 0.5px solid var(--red-light);
      border-radius: 10px; padding: 12px 16px;
      font-size: 13px; color: var(--red-dark);
      margin-bottom: 1.5rem;
    }

    /* LAYOUT 2 KOLOM */
    .layout { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start; }

    /* FORM CARD */
    .form-card { background: var(--white); border: 0.5px solid var(--border); border-radius: 12px; overflow: hidden; }
    .form-card-header { padding: 1rem 1.25rem; border-bottom: 0.5px solid var(--border); display: flex; align-items: center; gap: 10px; }
    .form-card-icon { width: 32px; height: 32px; background: var(--red-soft); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 16px; }
    .form-card-title { font-weight: 500; font-size: 15px; color: var(--dark); }
    .form-card-body { padding: 1.25rem; }

    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; font-size: 12px; font-weight: 500; color: var(--muted); margin-bottom: 5px; }
    .input-wrap { position: relative; }
    .input-wrap i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #ccc; pointer-events: none; }
    .form-input {
      width: 100%; padding: 9px 12px 9px 36px;
      font-size: 14px; border: 0.5px solid var(--border);
      border-radius: 8px; background: var(--bg);
      color: var(--dark); font-family: 'Inter', sans-serif;
      transition: border-color 0.15s, background 0.15s;
    }
    .form-input.no-icon { padding-left: 12px; }
    .form-input:focus { outline: none; border-color: var(--red); background: var(--white); }
    textarea.form-input { resize: vertical; min-height: 80px; padding-top: 9px; }

    .form-input-pass { padding-right: 38px; }
    .toggle-pass { position: absolute; right: 11px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #ccc; cursor: pointer; transition: color 0.12s; }
    .toggle-pass:hover { color: var(--muted); }

    .btn-save { display: flex; align-items: center; gap: 7px; background: var(--red); color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-size: 14px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; transition: background 0.15s; margin-top: 0.25rem; }
    .btn-save:hover { background: var(--red-dark); }

    /* INFO USER */
    .user-info { background: var(--bg); border-radius: 8px; padding: 12px 14px; margin-bottom: 1rem; display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--muted); }
    .user-info strong { color: var(--dark); }
    .user-avatar { width: 32px; height: 32px; background: var(--red-soft); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 16px; flex-shrink: 0; }

    @media (max-width: 900px) { .layout { grid-template-columns: 1fr; } }
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
  <a href="riwayat.php" class="nav-item"><i class="ti ti-history"></i> Riwayat</a>
  <a href="laporan.php" class="nav-item"><i class="ti ti-chart-bar"></i> Laporan</a>
  <a href="pengaturan.php" class="nav-item active"><i class="ti ti-settings"></i> Pengaturan</a>
  <div class="nav-spacer"></div>
  <a href="logout.php" class="nav-logout"><i class="ti ti-logout"></i> Logout</a>
  <div class="sidebar-footer">&copy; <?= date('Y') ?> BaleSepatuMantan</div>
</aside>

<!-- MAIN -->
<main class="main">

  <div class="topbar">
    <div>
      <div class="page-title">Pengaturan</div>
      <div class="page-sub">Kelola profil toko dan akun kasir</div>
    </div>
  </div>

  <?php if($sukses): ?>
  <div class="alert-sukses">
    <i class="ti ti-circle-check" style="font-size:18px"></i>
    <?= $sukses ?>
  </div>
  <?php endif; ?>

  <?php if($error): ?>
  <div class="alert-error">
    <i class="ti ti-alert-circle" style="font-size:18px"></i>
    <?= $error ?>
  </div>
  <?php endif; ?>

  <div class="layout">

    <!-- PROFIL TOKO -->
    <div class="form-card">
      <div class="form-card-header">
        <div class="form-card-icon"><i class="ti ti-building-store"></i></div>
        <div class="form-card-title">Profil Toko</div>
      </div>
      <div class="form-card-body">
        <form method="POST">
          <div class="form-group">
            <label class="form-label">Nama Toko</label>
            <div class="input-wrap">
              <i class="ti ti-building-store"></i>
              <input class="form-input" type="text" name="nama_toko" value="<?= htmlspecialchars($set['nama_toko'] ?? '') ?>" required/>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Alamat</label>
            <textarea class="form-input no-icon" name="alamat"><?= htmlspecialchars($set['alamat'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">No. HP / WhatsApp</label>
            <div class="input-wrap">
              <i class="ti ti-phone"></i>
              <input class="form-input" type="text" name="no_hp" value="<?= htmlspecialchars($set['no_hp'] ?? '') ?>"/>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Instagram</label>
            <div class="input-wrap">
              <i class="ti ti-brand-instagram"></i>
              <input class="form-input" type="text" name="instagram" value="<?= htmlspecialchars($set['instagram'] ?? '') ?>"/>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Catatan Struk</label>
            <textarea class="form-input no-icon" name="catatan_struk" placeholder="Contoh: Terima kasih! Barang tidak dapat dikembalikan."><?= htmlspecialchars($set['catatan_struk'] ?? '') ?></textarea>
          </div>
          <button type="submit" name="simpan_profil" class="btn-save">
            <i class="ti ti-device-floppy"></i> Simpan Profil
          </button>
        </form>
      </div>
    </div>

    <!-- GANTI PASSWORD -->
    <div class="form-card">
      <div class="form-card-header">
        <div class="form-card-icon"><i class="ti ti-lock"></i></div>
        <div class="form-card-title">Ganti Password</div>
      </div>
      <div class="form-card-body">

        <!-- Info user login -->
        <div class="user-info">
          <div class="user-avatar"><i class="ti ti-user"></i></div>
          <span>Login sebagai <strong><?= $_SESSION['nama'] ?? 'Admin' ?></strong></span>
        </div>

        <form method="POST">
          <div class="form-group">
            <label class="form-label">Password Lama</label>
            <div class="input-wrap">
              <i class="ti ti-lock"></i>
              <input class="form-input form-input-pass" type="password" name="pass_lama" id="passLama" placeholder="Masukkan password lama" required/>
              <i class="ti ti-eye toggle-pass" onclick="togglePass('passLama', this)"></i>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Password Baru</label>
            <div class="input-wrap">
              <i class="ti ti-lock-open"></i>
              <input class="form-input form-input-pass" type="password" name="pass_baru" id="passBaru" placeholder="Minimal 6 karakter" required/>
              <i class="ti ti-eye toggle-pass" onclick="togglePass('passBaru', this)"></i>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Konfirmasi Password Baru</label>
            <div class="input-wrap">
              <i class="ti ti-lock-check"></i>
              <input class="form-input form-input-pass" type="password" name="pass_ulang" id="passUlang" placeholder="Ulangi password baru" required/>
              <i class="ti ti-eye toggle-pass" onclick="togglePass('passUlang', this)"></i>
            </div>
          </div>
          <button type="submit" name="ganti_password" class="btn-save">
            <i class="ti ti-key"></i> Ubah Password
          </button>
        </form>
      </div>
    </div>

  </div>

</main>

<script>
function togglePass(id, icon) {
  const input = document.getElementById(id);
  if(input.type === 'password'){
    input.type = 'text';
    icon.classList.replace('ti-eye', 'ti-eye-off');
  } else {
    input.type = 'password';
    icon.classList.replace('ti-eye-off', 'ti-eye');
  }
}
</script>

</body>
</html>