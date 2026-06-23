<?php
require_once __DIR__ . '/../includes/helpers.php';
$pageTitle = 'CRUD Game - AFone Store';
$activeAdmin = 'games';
$conn = db_connect();
$games = [];
if ($conn) {
    try {
        $games = $conn->query('SELECT g.*, COUNT(p.id) AS total_packages FROM games g LEFT JOIN topup_packages p ON p.game_id = g.id GROUP BY g.id ORDER BY g.sort_order ASC, g.id DESC')->fetch_all(MYSQLI_ASSOC);
    } catch (Throwable $e) {
        flash('Gagal membaca data game. Jalankan install.php dulu.', 'danger');
    }
}
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div><h1 class="section-title mb-1">CRUD Game</h1><p class="text-soft mb-0">Kelola daftar game yang muncul di halaman Top Up.</p></div>
    <a href="game-form.php" class="btn btn-warning fw-bold">+ Tambah Game</a>
</div>
<div class="content-card">
<?php if (!$conn): ?>
    <div class="alert alert-warning mb-0">Database belum aktif. Jalankan <a href="../install.php" class="alert-link">install.php</a>.</div>
<?php elseif (!$games): ?>
    <div class="empty-state">Belum ada game.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-dark-custom align-middle mb-0">
            <thead><tr><th>Logo</th><th>Game</th><th>Kategori</th><th>Nominal</th><th>Status</th><th width="185">Aksi</th></tr></thead>
            <tbody>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td>
                            <?php if (!empty($game['image_url'])): ?><img class="admin-thumb" src="<?= e(image_src($game['image_url'], '../')) ?>" alt=""><?php else: ?><div class="admin-emoji"><?= e($game['icon_emoji'] ?: '🎮') ?></div><?php endif; ?>
                        </td>
                        <td><strong><?= e($game['name']) ?></strong><br><small class="text-soft"><?= e($game['publisher']) ?> • /<?= e($game['slug']) ?></small></td>
                        <td><?= e(category_label($game['category'])) ?><?= (int)$game['is_popular'] === 1 ? ' <span class="badge bg-warning text-dark ms-1">Populer</span>' : '' ?></td>
                        <td><span class="badge badge-soft"><?= (int)$game['total_packages'] ?> nominal</span></td>
                        <td><?= (int)$game['is_active'] === 1 ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
                        <td><div class="small-action"><a href="game-form.php?id=<?= (int)$game['id'] ?>" class="btn btn-sm btn-outline-warning">Edit</a><form method="post" action="game-delete.php" onsubmit="return confirm('Hapus game ini? Semua nominal game ini juga terhapus.')"><input type="hidden" name="id" value="<?= (int)$game['id'] ?>"><button class="btn btn-sm btn-outline-danger">Hapus</button></form></div></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
