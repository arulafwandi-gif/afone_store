<?php
require_once __DIR__ . '/../includes/helpers.php';
require_login();
$id = (int)($_POST['id'] ?? 0);
$conn = db_connect();
if (!$conn) { flash('Database belum aktif.', 'danger'); redirect('games.php'); }
try {
    $stmt = $conn->prepare('DELETE FROM games WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    flash('Game berhasil dihapus.', 'success');
} catch (Throwable $e) {
    flash('Game gagal dihapus.', 'danger');
}
redirect('games.php');
