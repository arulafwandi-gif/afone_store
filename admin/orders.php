<?php
require_once __DIR__ . '/../includes/helpers.php';
$pageTitle = 'Data Order - AFone Store';
$activeAdmin = 'orders';
$conn = db_connect();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn) {
    $id = (int)($_POST['id'] ?? 0);
    $type = $_POST['type'] ?? 'topup';
    $status = $_POST['status'] ?? 'baru';
    $validTopup = ['baru','menunggu pembayaran','diproses','selesai','dibatalkan'];
    $validJoki = ['baru','diproses','selesai','dibatalkan'];
    try {
        if ($type === 'joki' && $id > 0 && in_array($status, $validJoki, true)) {
            $stmt = $conn->prepare('UPDATE joki_orders SET status = ? WHERE id = ?');
            $stmt->bind_param('si', $status, $id);
            $stmt->execute();
            flash('Status order joki berhasil diperbarui.', 'success');
        } elseif ($id > 0 && in_array($status, $validTopup, true)) {
            $stmt = $conn->prepare('UPDATE topup_orders SET status = ? WHERE id = ?');
            $stmt->bind_param('si', $status, $id);
            $stmt->execute();
            flash('Status order top up berhasil diperbarui.', 'success');
        }
    } catch (Throwable $e) { flash('Gagal memperbarui status order.', 'danger'); }
    redirect('orders.php');
}
$orders = [];
$jokiOrders = [];
if ($conn) {
    try { $orders = $conn->query('SELECT * FROM topup_orders ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC); } catch (Throwable $e) { flash('Gagal membaca order top up. Jalankan install.php dulu.', 'danger'); }
    try { $jokiOrders = $conn->query('SELECT * FROM joki_orders ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC); } catch (Throwable $e) { $jokiOrders = []; }
}
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4"><div><h1 class="section-title mb-1">Data Order</h1><p class="text-soft mb-0">Pesanan top up dan joki dari website.</p></div><div class="d-flex gap-2"><a href="../TopUp.php" class="btn btn-outline-warning">Top Up</a><a href="../Jokigame.php" class="btn btn-outline-warning">Joki</a></div></div>
<div class="content-card mb-4">
<h4 class="fw-bold mb-3">Order Top Up</h4>
<?php if (!$conn): ?>
    <div class="alert alert-warning mb-0">Database belum aktif. Jalankan <a href="../install.php" class="alert-link">install.php</a>.</div>
<?php elseif (!$orders): ?>
    <div class="empty-state">Belum ada order top up masuk.</div>
<?php else: ?>
    <div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Order</th><th>Game & Nominal</th><th>Data Akun</th><th>Kontak & Refund</th><th>Pembayaran</th><th>Status</th></tr></thead><tbody>
        <?php foreach ($orders as $order): ?>
        <?php $total = (float)($order['total_price'] ?? 0); if ($total <= 0) $total = (float)$order['package_price']; ?>
        <tr>
            <td><span class="order-code">#<?= (int)$order['id'] ?></span><br><small class="text-soft"><?= e($order['created_at']) ?></small></td>
            <td><strong><?= e($order['game_name']) ?></strong><br><span class="text-warning fw-bold"><?= e($order['package_name']) ?></span><br><small class="text-soft">Harga: <?= rupiah($order['package_price']) ?></small></td>
            <td><strong><?= e($order['user_id']) ?><?= $order['zone_id'] ? ' (' . e($order['zone_id']) . ')' : '' ?></strong><br><small class="text-soft">Nick: <?= e($order['nickname'] ?: '-') ?></small><?php if ($order['notes']): ?><br><small class="text-soft">Catatan: <?= nl2br(e($order['notes'])) ?></small><?php endif; ?></td>
            <td><strong><?= e($order['customer_name']) ?></strong><br><a class="text-warning" target="_blank" href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $order['whatsapp'])) ?>"><?= e($order['whatsapp']) ?></a><?php if (!empty($order['email'])): ?><br><small class="text-soft"><?= e($order['email']) ?></small><?php endif; ?><?php if (!empty($order['refund_account_type']) || !empty($order['refund_account_number'])): ?><br><small class="text-soft">Refund: <?= e(($order['refund_account_type'] ?? '-') . ' ' . ($order['refund_account_number'] ?? '')) ?></small><?php endif; ?></td>
            <td><strong><?= e($order['payment_method']) ?></strong><br><small class="text-soft">Promo: <?= e($order['promo_code'] ?: '-') ?> <?= ((float)($order['promo_discount'] ?? 0) > 0) ? '(-'.rupiah($order['promo_discount']).')' : '' ?></small><br><span class="text-warning fw-bold">Total: <?= rupiah($total) ?></span></td>
            <td><form method="post" class="d-flex gap-2 flex-wrap"><input type="hidden" name="type" value="topup"><input type="hidden" name="id" value="<?= (int)$order['id'] ?>"><select name="status" class="form-select form-select-sm" style="min-width:180px"><option value="baru" <?= $order['status']==='baru'?'selected':'' ?>>Baru</option><option value="menunggu pembayaran" <?= $order['status']==='menunggu pembayaran'?'selected':'' ?>>Menunggu pembayaran</option><option value="diproses" <?= $order['status']==='diproses'?'selected':'' ?>>Diproses</option><option value="selesai" <?= $order['status']==='selesai'?'selected':'' ?>>Selesai</option><option value="dibatalkan" <?= $order['status']==='dibatalkan'?'selected':'' ?>>Dibatalkan</option></select><button class="btn btn-sm btn-outline-warning">Update</button></form><span class="badge <?= e(order_status_badge_class($order['status'])) ?> mt-2"><?= e($order['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody></table></div>
<?php endif; ?>
</div>
<div class="content-card">
<h4 class="fw-bold mb-3">Order Joki</h4>
<?php if (!$conn): ?>
    <div class="alert alert-warning mb-0">Database belum aktif.</div>
<?php elseif (!$jokiOrders): ?>
    <div class="empty-state">Belum ada order joki masuk.</div>
<?php else: ?>
    <div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Order</th><th>Rank</th><th>Kontak</th><th>Estimasi</th><th>Status</th></tr></thead><tbody>
    <?php foreach ($jokiOrders as $order): ?>
        <tr>
            <td><span class="order-code">#J<?= (int)$order['id'] ?></span><br><small class="text-soft"><?= e($order['created_at']) ?></small></td>
            <td><strong><?= e(ucfirst($order['service_type'])) ?></strong><br><small class="text-soft"><?= e($order['current_rank']) ?> <?= (int)$order['current_star'] ?>★ → <?= e($order['target_rank']) ?> <?= (int)$order['target_star'] ?>★</small><?php if ($order['notes']): ?><br><small class="text-soft">Catatan: <?= nl2br(e($order['notes'])) ?></small><?php endif; ?></td>
            <td><a class="text-warning" target="_blank" href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $order['whatsapp'])) ?>"><?= e($order['whatsapp']) ?></a><br><small class="text-soft"><?= e($order['account_info'] ?: '-') ?></small></td>
            <td><span class="text-warning fw-bold"><?= rupiah($order['estimated_price']) ?></span></td>
            <td><form method="post" class="d-flex gap-2 flex-wrap"><input type="hidden" name="type" value="joki"><input type="hidden" name="id" value="<?= (int)$order['id'] ?>"><select name="status" class="form-select form-select-sm" style="min-width:160px"><option value="baru" <?= $order['status']==='baru'?'selected':'' ?>>Baru</option><option value="diproses" <?= $order['status']==='diproses'?'selected':'' ?>>Diproses</option><option value="selesai" <?= $order['status']==='selesai'?'selected':'' ?>>Selesai</option><option value="dibatalkan" <?= $order['status']==='dibatalkan'?'selected':'' ?>>Dibatalkan</option></select><button class="btn btn-sm btn-outline-warning">Update</button></form><span class="badge bg-info text-dark mt-2"><?= e($order['status']) ?></span></td>
        </tr>
    <?php endforeach; ?>
    </tbody></table></div>
<?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
