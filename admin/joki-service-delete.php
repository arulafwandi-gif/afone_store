<?php
require_once __DIR__ . '/../includes/helpers.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('joki-services.php');
$id = (int)($_POST['id'] ?? 0);
$conn = db_connect();
if ($conn && $id > 0) { $stmt = $conn->prepare('DELETE FROM joki_services WHERE id = ?'); $stmt->bind_param('i', $id); $stmt->execute(); flash('Harga joki berhasil dihapus.', 'success'); }
redirect('joki-services.php');
