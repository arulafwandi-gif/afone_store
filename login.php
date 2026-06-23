<?php
require_once __DIR__ . '/includes/helpers.php';

if (is_logged_in()) {
    redirect('admin/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $conn = db_connect();
    if (!$conn) {
        flash('Database belum tersedia. Buka install.php terlebih dahulu.', 'warning');
        redirect('login.php');
    }

    try {
        $stmt = $conn->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'username' => $user['username'],
                'role' => $user['role'],
            ];
            flash('Login berhasil. Selamat datang di dashboard admin.', 'success');
            redirect('admin/index.php');
        }

        flash('Username atau password salah.', 'danger');
        redirect('login.php');
    } catch (Throwable $e) {
        flash('Login gagal. Pastikan database sudah di-install.', 'danger');
        redirect('login.php');
    }
}

$pageTitle = 'Login Admin - AFone Store';
$activePage = 'login';
require __DIR__ . '/includes/header.php';
?>
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="form-card">
                <h2 class="section-title mb-2">Login Admin</h2>
                <p class="text-soft">Masuk untuk mengelola produk, order, dan pesan pelanggan.</p>
                <form method="post" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="admin" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="admin123" required>
                    </div>
                    <button class="btn btn-warning w-100 fw-bold" type="submit">Login</button>
                </form>
                <div class="alert alert-dark border-warning text-soft mt-4 mb-0">
                    Default setelah install: <strong class="text-warning">admin</strong> / <strong class="text-warning">admin123</strong>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
