<?php
require_once __DIR__ . '/includes/helpers.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('Jokigame.php');
$currentRank = trim($_POST['current_rank'] ?? '');
$targetRank = trim($_POST['target_rank'] ?? '');
$currentStar = (int)($_POST['current_star'] ?? 0);
$targetStar = (int)($_POST['target_star'] ?? 0);
$serviceType = trim($_POST['service_type'] ?? 'reguler');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$accountInfo = trim($_POST['account_info'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$estimatedPrice = max(0, (float)($_POST['estimated_price'] ?? 0));
if ($currentRank === '' || $targetRank === '' || $whatsapp === '' || !in_array($serviceType, ['reguler','express'], true)) {
    flash('Rank, tipe layanan, dan WhatsApp wajib diisi.', 'danger');
    redirect('Jokigame.php');
}
$conn = db_connect();
if (!$conn) { flash('Database belum aktif. Jalankan install.php terlebih dahulu.', 'warning'); redirect('Jokigame.php'); }
try {
    $status = 'baru';
    $stmt = $conn->prepare('INSERT INTO joki_orders (current_rank, current_star, target_rank, target_star, service_type, whatsapp, account_info, estimated_price, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sisisssdss', $currentRank, $currentStar, $targetRank, $targetStar, $serviceType, $whatsapp, $accountInfo, $estimatedPrice, $notes, $status);
    $stmt->execute();
    $orderId = $stmt->insert_id;
} catch (Throwable $e) {
    flash('Order joki gagal disimpan. Jalankan ulang install.php.', 'danger');
    redirect('Jokigame.php');
}
$pageTitle = 'Order Joki Berhasil - AFone Store';
$activePage = 'joki';
require __DIR__ . '/includes/header.php';
?>
<section class="container my-5">
    <div class="row justify-content-center"><div class="col-lg-8"><div class="form-card text-center">
        <div class="feature-icon mx-auto">✅</div>
        <h1 class="section-title mt-3">Order joki berhasil dibuat</h1>
        <p class="text-soft">Kirim kode order ke admin agar pesanan bisa dikonfirmasi.</p>
        <div class="content-card text-start mt-4">
            <div class="summary-line"><span class="text-soft">Kode Order</span><strong>#J<?= (int)$orderId ?></strong></div>
            <div class="summary-line"><span class="text-soft">Layanan</span><strong><?= e(ucfirst($serviceType)) ?></strong></div>
            <div class="summary-line"><span class="text-soft">Dari</span><strong><?= e($currentRank) ?> <?= (int)$currentStar ?>★</strong></div>
            <div class="summary-line"><span class="text-soft">Tujuan</span><strong><?= e($targetRank) ?> <?= (int)$targetStar ?>★</strong></div>
            <div class="summary-line"><span class="text-soft">Estimasi</span><strong class="summary-price"><?= rupiah($estimatedPrice) ?></strong></div>
        </div>
        <div class="d-flex gap-2 justify-content-center flex-wrap mt-4">
            <a href="Jokigame.php" class="btn btn-warning fw-bold">Order Lagi</a>
            <a href="https://wa.me/6281949351883?text=Halo%20admin,%20saya%20buat%20order%20joki%20%23J<?= (int)$orderId ?>" target="_blank" class="btn btn-outline-warning">Chat Admin</a>
        </div>
    </div></div></div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
