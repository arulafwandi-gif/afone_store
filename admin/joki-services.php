<?php
require_once __DIR__ . '/../includes/helpers.php';
$pageTitle = 'CRUD Joki - AFone Store';
$activeAdmin = 'joki';
$conn = db_connect();
$services = $conn ? get_joki_services(null, false) : [];
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div><h1 class="section-title mb-1">CRUD Joki</h1><p class="text-soft mb-0">Kelola daftar harga rank reguler dan express.</p></div>
    <a href="joki-service-form.php" class="btn btn-warning fw-bold">+ Tambah Harga Joki</a>
</div>
<div class="content-card">
<?php if (!$conn): ?>
    <div class="alert alert-warning mb-0">Database belum aktif. Jalankan <a href="../install.php" class="alert-link">install.php</a>.</div>
<?php elseif (!$services): ?>
    <div class="empty-state">Belum ada harga joki.</div>
<?php else: ?>
    <div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Tipe</th><th>Rank</th><th>Urutan</th><th>Harga</th><th>Status</th><th width="185">Aksi</th></tr></thead><tbody>
    <?php foreach ($services as $service): ?>
        <tr>
            <td><span class="badge bg-info text-dark"><?= e(strtoupper($service['service_type'])) ?></span></td>
            <td><strong><?= e($service['icon'] ?: '🏆') ?> <?= e($service['rank_name']) ?></strong></td>
            <td><?= (int)$service['rank_order'] ?></td>
            <td class="text-warning fw-bold"><?= rupiah($service['price']) ?></td>
            <td><?= (int)$service['is_active'] === 1 ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
            <td><div class="small-action"><a href="joki-service-form.php?id=<?= (int)$service['id'] ?>" class="btn btn-sm btn-outline-warning">Edit</a><form method="post" action="joki-service-delete.php" onsubmit="return confirm('Hapus harga joki ini?')"><input type="hidden" name="id" value="<?= (int)$service['id'] ?>"><button class="btn btn-sm btn-outline-danger">Hapus</button></form></div></td>
        </tr>
    <?php endforeach; ?>
    </tbody></table></div>
<?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
