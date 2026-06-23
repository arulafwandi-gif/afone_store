<?php
require_once __DIR__ . '/../includes/helpers.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('accounts.php');
$id = (int)($_POST['id'] ?? 0);
$conn = db_connect();
if ($conn && $id > 0) { $stmt = $conn->prepare('DELETE FROM game_accounts WHERE id = ?'); $stmt->bind_param('i', $id); $stmt->execute(); flash('Akun berhasil dihapus.', 'success'); }
redirect('accounts.php');
