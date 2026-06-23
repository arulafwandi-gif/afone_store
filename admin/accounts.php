<?php
require_once __DIR__ . '/../includes/helpers.php';
$pageTitle = 'CRUD Beli Akun - AFone Store';
$activeAdmin = 'accounts';
$conn = db_connect();
$accounts = $conn ? get_game_accounts(false) : [];
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div><h1 class="section-title mb-1">CRUD Beli Akun</h1><p class="text-soft mb-0">Kelola akun game yang dijual di halaman Beli Akun.</p></div>
    <a href="account-form.php" class="btn btn-warning fw-bold">+ Tambah Akun</a>
</div>
<div class="content-card">
<?php if (!$conn): ?>
    <div class="alert alert-warning mb-0">Database belum aktif. Jalankan <a href="../install.php" class="alert-link">install.php</a>.</div>
<?php elseif (!$accounts): ?>
    <div class="empty-state">Belum ada akun game.</div>
<?php else: ?>
    <div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Foto</th><th>Akun</th><th>Game</th><th>Harga</th><th>Status</th><th width="185">Aksi</th></tr></thead><tbody>
    <?php foreach ($accounts as $account): ?>
        <tr>
            <td><?php if (!empty($account['image_url'])): ?><img src="../<?= e(image_src($account['image_url'])) ?>" class="admin-thumb" alt=""><?php else: ?><div class="admin-emoji"><?= e($account['game_icon'] ?? '🎮') ?></div><?php endif; ?></td>
            <td><strong><?= e($account['title']) ?></strong><br><small class="text-soft"><?= e($account['specs'] ?? '-') ?></small></td>
            <td><?= e($account['game_name'] ?? '-') ?></td>
            <td class="text-warning fw-bold"><?= rupiah($account['price']) ?></td>
            <td><span class="badge bg-info text-dark"><?= e(strtoupper($account['status'])) ?></span> <?= (int)$account['is_active'] === 1 ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
            <td><div class="small-action"><a href="account-form.php?id=<?= (int)$account['id'] ?>" class="btn btn-sm btn-outline-warning">Edit</a><form method="post" action="account-delete.php" onsubmit="return confirm('Hapus akun ini?')"><input type="hidden" name="id" value="<?= (int)$account['id'] ?>"><button class="btn btn-sm btn-outline-danger">Hapus</button></form></div></td>
        </tr>
    <?php endforeach; ?>
    </tbody></table></div>
<?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
