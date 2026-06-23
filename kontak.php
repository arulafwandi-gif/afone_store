<?php
require_once __DIR__ . '/includes/helpers.php';
$pageTitle = 'Kontak - AFone Store';
$activePage = 'contact';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $phone === '' || $message === '') {
        flash('Nama, WhatsApp, dan pesan wajib diisi.', 'danger');
        redirect('kontak.php');
    }

    $conn = db_connect();
    if (!$conn) {
        flash('Database belum aktif. Jalankan install.php terlebih dahulu.', 'warning');
        redirect('kontak.php');
    }

    try {
        $stmt = $conn->prepare('INSERT INTO contact_messages (name, phone, message) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $phone, $message);
        $stmt->execute();
        flash('Pesan berhasil dikirim. Admin akan menghubungi kamu.', 'success');
        redirect('kontak.php');
    } catch (Throwable $e) {
        flash('Pesan gagal disimpan. Pastikan database sudah di-install.', 'danger');
        redirect('kontak.php');
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="page-hero-box">
            <h1 class="section-title mb-2">Hubungi Kami</h1>
            <p class="text-soft mb-0">Kirim pesan melalui form atau langsung chat WhatsApp admin.</p>
        </div>
    </div>
</section>
<section class="container my-4">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="content-card h-100">
                <h4 class="text-warning fw-bold">Kontak Admin</h4>
                <div class="mt-4">
                    <p class="mb-2"><strong>WhatsApp</strong><br><a class="footer-link" href="https://wa.me/6281949351883" target="_blank">0819-4935-1883</a></p>
                    <p class="mb-2"><strong>Alamat</strong><br><span class="text-soft">BTN Mavila Rengganis, Bajur, Labuapi, Lombok Barat, NTB</span></p>
                    <p class="mb-0"><strong>Jam Operasional</strong><br><span class="text-soft">08.00 - 23.59 setiap hari</span></p>
                </div>
                <a class="btn btn-warning mt-4 fw-bold" href="https://wa.me/6281949351883" target="_blank">Chat WhatsApp</a>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="form-card">
                <h4 class="fw-bold mb-3">Form Pesan</h4>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama kamu" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pesan</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Tulis pesan kamu" required></textarea>
                    </div>
                    <button class="btn btn-warning fw-bold" type="submit">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
