<?php
require_once __DIR__ . '/../includes/helpers.php';
$pageTitle = 'CRUD Nominal Top Up - AFone Store';
$activeAdmin = 'packages';
$conn = db_connect();
$gameId = (int)($_GET['game_id'] ?? 0);
$games = $conn ? get_games(false) : [];
$packages = [];
if ($conn) {
    try {
        if ($gameId > 0) {
            $stmt = $conn->prepare('SELECT p.*, g.name AS game_name FROM topup_packages p JOIN games g ON g.id = p.game_id WHERE p.game_id = ? ORDER BY p.sort_order ASC, p.id DESC');
            $stmt->bind_param('i', $gameId);
            $stmt->execute();
            $packages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $packages = $conn->query('SELECT p.*, g.name AS game_name FROM topup_packages p JOIN games g ON g.id = p.game_id ORDER BY g.sort_order ASC, p.sort_order ASC, p.id DESC')->fetch_all(MYSQLI_ASSOC);
        }
    } catch (Throwable $e) { flash('Gagal membaca nominal top up.', 'danger'); }
}
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div><h1 class="section-title mb-1">CRUD Nominal Top Up</h1><p class="text-soft mb-0">Kelola jumlah diamond, UC, VP, Robux, coin, dan harga.</p></div>
    <a href="package-form.php" class="btn btn-warning fw-bold">+ Tambah Nominal</a>
</div>
<div class="content-card mb-4">
    <form class="row g-2 align-items-end" method="get">
        <div class="col-md-7"><label class="form-label">Filter Game</label><select name="game_id" class="form-select"><option value="0">Semua game</option><?php foreach ($games as $game): ?><option value="<?= (int)$game['id'] ?>" <?= $gameId === (int)$game['id'] ? 'selected' : '' ?>><?= e($game['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-5"><button class="btn btn-outline-warning">Terapkan</button><a href="packages.php" class="btn btn-outline-light">Reset</a></div>
    </form>
</div>
<div class="content-card">
<?php if (!$conn): ?>
    <div class="alert alert-warning mb-0">Database belum aktif. Jalankan <a href="../install.php" class="alert-link">install.php</a>.</div>
<?php elseif (!$packages): ?>
    <div class="empty-state">Belum ada nominal top up.</div>
<?php else: ?>
    <div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Game</th><th>Nominal</th><th>Harga</th><th>Badge</th><th>Status</th><th width="185">Aksi</th></tr></thead><tbody>
        <?php foreach ($packages as $package): ?>
        <tr>
            <td><strong><?= e($package['game_name']) ?></strong></td>
            <td><strong><?= e($package['name']) ?></strong><br><small class="text-soft"><?= e((string)$package['amount']) ?> <?= e($package['unit']) ?></small></td>
            <td><?= rupiah($package['price']) ?><?php if ((float)$package['original_price'] > 0): ?><br><small class="text-soft text-decoration-line-through"><?= rupiah($package['original_price']) ?></small><?php endif; ?></td>
            <td><?= $package['badge'] ? '<span class="badge bg-warning text-dark">'.e($package['badge']).'</span>' : '<span class="text-soft">-</span>' ?></td>
            <td><?= (int)$package['is_active'] === 1 ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
            <td><div class="small-action"><a href="package-form.php?id=<?= (int)$package['id'] ?>" class="btn btn-sm btn-outline-warning">Edit</a><form method="post" action="package-delete.php" onsubmit="return confirm('Hapus nominal ini?')"><input type="hidden" name="id" value="<?= (int)$package['id'] ?>"><button class="btn btn-sm btn-outline-danger">Hapus</button></form></div></td>
        </tr>
        <?php endforeach; ?>
    </tbody></table></div>
<?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
