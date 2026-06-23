<?php
require_once __DIR__ . '/../includes/helpers.php';
$pageTitle = 'Pesan Kontak - AFone Store';
$activeAdmin = 'messages';
$conn = db_connect();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn) {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    try {
        if ($action === 'delete' && $id > 0) {
            $stmt = $conn->prepare('DELETE FROM contact_messages WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            flash('Pesan berhasil dihapus.', 'success');
        } elseif ($action === 'read' && $id > 0) {
            $stmt = $conn->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            flash('Pesan ditandai sudah dibaca.', 'success');
        }
    } catch (Throwable $e) { flash('Aksi pesan gagal.', 'danger'); }
    redirect('messages.php');
}
$messages = [];
if ($conn) {
    try { $messages = $conn->query('SELECT * FROM contact_messages ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC); }
    catch (Throwable $e) { flash('Gagal membaca pesan.', 'danger'); }
}
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4"><div><h1 class="section-title mb-1">Pesan Kontak</h1><p class="text-soft mb-0">Pesan yang dikirim pelanggan dari halaman kontak.</p></div><a href="../kontak.php" class="btn btn-outline-warning">Lihat Form Kontak</a></div>
<div class="content-card">
<?php if (!$conn): ?>
    <div class="alert alert-warning mb-0">Database belum aktif. Jalankan <a href="../install.php" class="alert-link">install.php</a>.</div>
<?php elseif (!$messages): ?>
    <div class="empty-state">Belum ada pesan.</div>
<?php else: ?>
    <div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Pengirim</th><th>Pesan</th><th>Tanggal</th><th>Status</th><th width="160">Aksi</th></tr></thead><tbody>
    <?php foreach ($messages as $msg): ?>
        <tr><td><strong><?= e($msg['name']) ?></strong><br><a class="text-warning" target="_blank" href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $msg['phone'])) ?>"><?= e($msg['phone']) ?></a></td><td><?= nl2br(e($msg['message'])) ?></td><td><small class="text-soft"><?= e($msg['created_at']) ?></small></td><td><?= (int)$msg['is_read'] === 1 ? '<span class="badge bg-success">Dibaca</span>' : '<span class="badge bg-warning text-dark">Baru</span>' ?></td><td><div class="small-action"><form method="post"><input type="hidden" name="id" value="<?= (int)$msg['id'] ?>"><input type="hidden" name="action" value="read"><button class="btn btn-sm btn-outline-warning">Baca</button></form><form method="post" onsubmit="return confirm('Hapus pesan ini?')"><input type="hidden" name="id" value="<?= (int)$msg['id'] ?>"><input type="hidden" name="action" value="delete"><button class="btn btn-sm btn-outline-danger">Hapus</button></form></div></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
<?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
