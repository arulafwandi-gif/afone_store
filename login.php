<?php
session_start();
include "koneksi.php";

if(isset($_POST['login'])){
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $cek = mysqli_query($koneksi,"SELECT * FROM user 
           WHERE username='$user' AND password='$pass'");

    if(mysqli_num_rows($cek) > 0){
        $data = mysqli_fetch_assoc($cek);
        $_SESSION['login'] = true;
        $_SESSION['nama']  = $data['nama'];
        header("Location: index.php");
        exit;
    }else{
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login — BaleSepatuMantan</title>
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
      --card:     #222222;
      --muted:    #888;
      --border:   #333;
      --input-bg: #2A2A2A;
      --white:    #FFFFFF;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--dark);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      position: relative;
      overflow: hidden;
    }

    /* Dekorasi lingkaran background */
    body::before {
      content: '';
      position: fixed;
      width: 500px; height: 500px;
      border-radius: 50%;
      border: 70px solid rgba(192,57,43,0.07);
      top: -160px; left: -160px;
      pointer-events: none;
    }
    body::after {
      content: '';
      position: fixed;
      width: 400px; height: 400px;
      border-radius: 50%;
      border: 50px solid rgba(192,57,43,0.05);
      bottom: -120px; right: -100px;
      pointer-events: none;
    }

    /* CARD */
    .login-card {
      background: var(--card);
      border: 0.5px solid #2E2E2E;
      border-radius: 20px;
      padding: 2.5rem 2.25rem;
      width: 100%;
      max-width: 420px;
      position: relative;
      z-index: 1;
    }

    /* LOGO */
    .logo-area {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 2rem;
    }

    .logo-wrap {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      border: 2.5px solid var(--red);
      overflow: hidden;
      margin-bottom: 1rem;
      box-shadow: 0 0 0 6px rgba(192,57,43,0.1);
    }

    .logo-wrap img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .brand-name {
      font-family: 'Playfair Display', serif;
      font-size: 22px;
      color: #fff;
      margin-bottom: 3px;
    }

    .brand-handle {
      font-size: 12px;
      color: #555;
    }

    /* DIVIDER */
    .divider {
      height: 0.5px;
      background: #2E2E2E;
      margin: 0 0 1.75rem;
    }

    /* TITLE */
    .login-title {
      font-family: 'Playfair Display', serif;
      font-size: 22px;
      color: #fff;
      margin-bottom: 4px;
    }

    .login-sub {
      font-size: 13px;
      color: var(--muted);
      margin-bottom: 1.5rem;
    }

    /* ERROR */
    .error-box {
      display: flex;
      align-items: center;
      gap: 10px;
      background: rgba(192,57,43,0.12);
      border: 0.5px solid rgba(192,57,43,0.3);
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 13px;
      color: #F1948A;
      margin-bottom: 1.25rem;
    }
    .error-box i { font-size: 16px; flex-shrink: 0; }

    /* FORM */
    .form-group { margin-bottom: 1rem; }

    .form-label {
      display: block;
      font-size: 12px;
      font-weight: 500;
      color: #666;
      margin-bottom: 6px;
    }

    .input-wrap { position: relative; }

    .input-wrap .icon-left {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 17px;
      color: #444;
      pointer-events: none;
    }

    .form-input {
      width: 100%;
      padding: 11px 12px 11px 38px;
      font-size: 14px;
      border: 0.5px solid var(--border);
      border-radius: 9px;
      background: var(--input-bg);
      color: #fff;
      font-family: 'Inter', sans-serif;
      transition: border-color 0.15s, background 0.15s;
    }

    .form-input:focus {
      outline: none;
      border-color: var(--red);
      background: #2F2F2F;
    }

    .form-input::placeholder { color: #444; }

    .toggle-pass {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 17px;
      color: #444;
      cursor: pointer;
      transition: color 0.12s;
    }
    .toggle-pass:hover { color: #888; }

    /* BUTTON */
    .btn-login {
      width: 100%;
      padding: 12px;
      background: var(--red);
      color: #fff;
      border: none;
      border-radius: 9px;
      font-size: 15px;
      font-weight: 500;
      font-family: 'Inter', sans-serif;
      cursor: pointer;
      margin-top: 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background 0.15s, transform 0.1s;
    }
    .btn-login:hover  { background: var(--red-dark); }
    .btn-login:active { transform: scale(0.98); }

    .footer-note {
      text-align: center;
      font-size: 11px;
      color: #333;
      margin-top: 1.75rem;
    }
  </style>
</head>
<body>

<div class="login-card">

  <!-- LOGO -->
  <div class="logo-area">
    <div class="logo-wrap">
      <img src="508672227_17903328033191189_6852045551131810257_n.jpg" alt="BaleSepatuMantan"/>
    </div>
    <div class="brand-name">Bale Sepatu Mantan</div>
    <div class="brand-handle">SISTEM KASIR</div>
  </div>

  <div class="divider"></div>

  <div class="login-title">Selamat datang</div>
  <div class="login-sub">Masuk untuk melanjutkan ke sistem kasir</div>

  <?php if(isset($error)): ?>
  <div class="error-box">
    <i class="ti ti-alert-circle"></i>
    <?= $error ?>
  </div>
  <?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label class="form-label">Username</label>
      <div class="input-wrap">
        <i class="ti ti-user icon-left"></i>
        <input
          class="form-input"
          type="text"
          name="username"
          placeholder="Masukkan username"
          value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
          required
          autocomplete="username"
        />
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Password</label>
      <div class="input-wrap">
        <i class="ti ti-lock icon-left"></i>
        <input
          class="form-input"
          type="password"
          name="password"
          id="inputPass"
          placeholder="Masukkan password"
          required
          autocomplete="current-password"
        />
        <i class="ti ti-eye toggle-pass" id="togglePass" onclick="togglePassword()"></i>
      </div>
    </div>

    <button type="submit" name="login" class="btn-login">
      <i class="ti ti-login"></i> Masuk
    </button>
  </form>

  <div class="footer-note">
    &copy; <?= date('Y') ?> BaleSepatuMantan &mdash; Sistem Kasir
  </div>

</div>

<script>
  function togglePassword() {
    const input = document.getElementById('inputPass');
    const icon  = document.getElementById('togglePass');
    if (input.type === 'password') {
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