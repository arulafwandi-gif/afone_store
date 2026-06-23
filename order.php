<?php
require_once __DIR__ . '/includes/helpers.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('TopUp.php');
}

$gameId = (int)($_POST['game_id'] ?? 0);
$packageId = (int)($_POST['package_id'] ?? 0);
$game = get_game($gameId);
$package = get_package($packageId);

if (!$game || !$package || (int)$package['game_id'] !== $gameId) {
    flash('Game atau nominal top up tidak valid.', 'danger');
    redirect('TopUp.php');
}

$userId = trim($_POST['user_id'] ?? '');
$zoneId = trim($_POST['zone_id'] ?? '');
$nickname = trim($_POST['nickname'] ?? '');
$accountNote = trim($_POST['account_note'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$email = trim($_POST['email'] ?? '');
$paymentMethod = trim($_POST['payment_method'] ?? 'QRIS');
$promoCode = strtoupper(trim($_POST['promo_code'] ?? ''));
$refundType = trim($_POST['refund_account_type'] ?? '');
$refundNumber = trim($_POST['refund_account_number'] ?? '');
$notes = trim($_POST['notes'] ?? '');

if ($userId === '' || $whatsapp === '') {
    flash('User ID dan WhatsApp wajib diisi.', 'danger');
    redirect('game.php?slug=' . urlencode($game['slug']));
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('Format email belum benar.', 'danger');
    redirect('game.php?slug=' . urlencode($game['slug']));
}

if (!payment_channel($paymentMethod)) {
    flash('Metode pembayaran tidak valid.', 'danger');
    redirect('game.php?slug=' . urlencode($game['slug']));
}

$subtotal = (float)$package['price'];
$discount = promo_discount($promoCode, $subtotal);
$fee = payment_fee($paymentMethod, max(0, $subtotal - $discount));
$total = max(0, $subtotal - $discount + $fee);

$conn = db_connect();
if (!$conn) {
    flash('Database belum aktif. Jalankan install.php terlebih dahulu.', 'warning');
    redirect('game.php?slug=' . urlencode($game['slug']));
}

try {
    $status = 'baru';
    $customerName = $nickname ?: 'Pelanggan';
    $finalNotes = trim($notes . ($accountNote !== '' ? "\nCatatan akun: " . $accountNote : ''));

    $stmt = $conn->prepare('INSERT INTO topup_orders (game_id, package_id, game_name, package_name, package_price, promo_code, promo_discount, payment_fee, total_price, customer_name, whatsapp, email, user_id, zone_id, nickname, payment_method, refund_account_type, refund_account_number, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('iissdsdddsssssssssss', $gameId, $packageId, $game['name'], $package['name'], $subtotal, $promoCode, $discount, $fee, $total, $customerName, $whatsapp, $email, $userId, $zoneId, $nickname, $paymentMethod, $refundType, $refundNumber, $finalNotes, $status);
    $stmt->execute();
    $orderId = $stmt->insert_id;
} catch (Throwable $e) {
    flash('Order gagal disimpan. Jalankan ulang install.php agar kolom order terbaru dibuat.', 'danger');
    redirect('game.php?slug=' . urlencode($game['slug']));
}

$pageTitle = 'Order Berhasil - AFone Store';
$activePage = 'topup';
require __DIR__ . '/includes/header.php';
?>
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card text-center">
                <div class="feature-icon mx-auto">✅</div>
                <h1 class="section-title mt-3 mb-2">Order berhasil dibuat</h1>
                <p class="text-soft">Simpan detail order ini. Admin akan memproses pesanan melalui WhatsApp.</p>
                <div class="content-card text-start mt-4">
                    <div class="summary-line"><span class="text-soft">Kode Order</span><strong>#<?= (int)$orderId ?></strong></div>
                    <div class="summary-line"><span class="text-soft">Game</span><strong><?= e($game['name']) ?></strong></div>
                    <div class="summary-line"><span class="text-soft">Nominal</span><strong><?= e($package['name']) ?></strong></div>
                    <div class="summary-line"><span class="text-soft">User ID</span><strong><?= e($userId) ?><?= $zoneId ? ' (' . e($zoneId) . ')' : '' ?></strong></div>
                    <div class="summary-line"><span class="text-soft">Metode Bayar</span><strong><?= e($paymentMethod) ?></strong></div>
                    <?php if ($promoCode !== ''): ?><div class="summary-line"><span class="text-soft">Promo</span><strong><?= e($promoCode) ?> (-<?= rupiah($discount) ?>)</strong></div><?php endif; ?>
                    <div class="summary-line"><span class="text-soft">Total Bayar</span><strong class="summary-price"><?= rupiah($total) ?></strong></div>
                </div>
                <div class="alert alert-dark border-warning text-soft text-start mt-4">
                    Pembayaran belum otomatis. Setelah transfer, hubungi admin dan kirim kode order <strong>#<?= (int)$orderId ?></strong> agar pesanan lebih cepat diproses.
                </div>
                <div class="d-flex gap-2 justify-content-center flex-wrap mt-4">
                    <a href="game.php?slug=<?= e($game['slug']) ?>" class="btn btn-warning fw-bold">Order Lagi</a>
                    <a href="TopUp.php" class="btn btn-outline-light">Kembali ke Daftar Game</a>
                    <a href="https://wa.me/6281949351883?text=Halo%20admin,%20saya%20sudah%20order%20%23<?= (int)$orderId ?>%20<?= rawurlencode($game['name'] . ' - ' . $package['name']) ?>" target="_blank" class="btn btn-outline-warning">Chat Admin</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
