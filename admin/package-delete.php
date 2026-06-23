<?php
require_once __DIR__ . '/../includes/helpers.php';
require_login();
$id = (int)($_POST['id'] ?? 0);
$conn = db_connect();
if (!$conn) { flash('Database belum aktif.', 'danger'); redirect('packages.php'); }
try {
    $stmt = $conn->prepare('DELETE FROM topup_packages WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    flash('Nominal berhasil dihapus.', 'success');
} catch (Throwable $e) { flash('Nominal gagal dihapus.', 'danger'); }
redirect('packages.php');
