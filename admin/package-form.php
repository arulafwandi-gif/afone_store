<?php
require_once __DIR__ . '/../includes/helpers.php';
require_login();
$conn = db_connect();
if (!$conn) { flash('Database belum aktif. Jalankan install.php terlebih dahulu.', 'warning'); redirect('../install.php'); }
$games = get_games(false);
if (!$games) { flash('Tambahkan game terlebih dahulu sebelum membuat nominal.', 'warning'); redirect('game-form.php'); }
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$isEdit = $id > 0;
$package = ['game_id'=>(int)$games[0]['id'],'name'=>'','amount'=>0,'unit'=>'Diamonds','price'=>'','original_price'=>'','badge'=>'','description'=>'','sort_order'=>0,'is_active'=>1];
if ($isEdit) {
    $stmt = $conn->prepare('SELECT * FROM topup_packages WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $package = $stmt->get_result()->fetch_assoc();
    if (!$package) { flash('Nominal tidak ditemukan.', 'danger'); redirect('packages.php'); }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gameId = (int)($_POST['game_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $amount = (int)($_POST['amount'] ?? 0);
    $unit = trim($_POST['unit'] ?? 'Diamonds');
    $price = (float)str_replace(['.', ','], ['', '.'], $_POST['price'] ?? '0');
    $originalPrice = (float)str_replace(['.', ','], ['', '.'], $_POST['original_price'] ?? '0');
    $badge = trim($_POST['badge'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    if ($gameId <= 0 || $name === '' || $price < 0) { flash('Game, nama nominal, dan harga wajib valid.', 'danger'); redirect($isEdit ? 'package-form.php?id='.$id : 'package-form.php'); }
    try {
        if ($isEdit) {
            $stmt = $conn->prepare('UPDATE topup_packages SET game_id=?, name=?, amount=?, unit=?, price=?, original_price=?, badge=?, description=?, sort_order=?, is_active=? WHERE id=?');
            $stmt->bind_param('isisddssiii', $gameId, $name, $amount, $unit, $price, $originalPrice, $badge, $description, $sortOrder, $isActive, $id);
            $stmt->execute();
            flash('Nominal berhasil diperbarui.', 'success');
        } else {
            $stmt = $conn->prepare('INSERT INTO topup_packages (game_id, name, amount, unit, price, original_price, badge, description, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('isisddssii', $gameId, $name, $amount, $unit, $price, $originalPrice, $badge, $description, $sortOrder, $isActive);
            $stmt->execute();
            flash('Nominal berhasil ditambahkan.', 'success');
        }
        redirect('packages.php');
    } catch (Throwable $e) { flash($e->getMessage(), 'danger'); redirect($isEdit ? 'package-form.php?id='.$id : 'package-form.php'); }
}
$pageTitle = ($isEdit ? 'Edit Nominal' : 'Tambah Nominal') . ' - AFone Store';
$activeAdmin = 'packages';
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4"><div><h1 class="section-title mb-1"><?= $isEdit ? 'Edit Nominal' : 'Tambah Nominal' ?></h1><p class="text-soft mb-0">Nominal akan muncul di halaman detail game.</p></div><a href="packages.php" class="btn btn-outline-light">Kembali</a></div>
<div class="form-card"><form method="post"><input type="hidden" name="id" value="<?= (int)$id ?>"><div class="row g-3">
    <div class="col-md-6"><label class="form-label">Game</label><select name="game_id" class="form-select" required><?php foreach ($games as $game): ?><option value="<?= (int)$game['id'] ?>" <?= (int)$package['game_id']===(int)$game['id']?'selected':'' ?>><?= e($game['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-6"><label class="form-label">Nama Nominal</label><input type="text" name="name" class="form-control" value="<?= e($package['name']) ?>" placeholder="86 Diamonds / 325 UC" required></div>
    <div class="col-md-3"><label class="form-label">Jumlah</label><input type="number" name="amount" class="form-control" value="<?= e((string)$package['amount']) ?>" min="0"></div>
    <div class="col-md-3"><label class="form-label">Satuan</label><input type="text" name="unit" class="form-control" value="<?= e($package['unit']) ?>" placeholder="Diamonds / UC / VP"></div>
    <div class="col-md-3"><label class="form-label">Harga</label><input type="number" name="price" class="form-control" value="<?= e((string)$package['price']) ?>" min="0" step="100" required></div>
    <div class="col-md-3"><label class="form-label">Harga Coret</label><input type="number" name="original_price" class="form-control" value="<?= e((string)$package['original_price']) ?>" min="0" step="100"></div>
    <div class="col-md-4"><label class="form-label">Badge</label><input type="text" name="badge" class="form-control" value="<?= e($package['badge']) ?>" placeholder="PROMO / LARIS / BEST"></div>
    <div class="col-md-4"><label class="form-label">Urutan</label><input type="number" name="sort_order" class="form-control" value="<?= e((string)$package['sort_order']) ?>"></div>
    <div class="col-md-4 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= (int)$package['is_active']===1?'checked':'' ?>><label class="form-check-label" for="is_active">Aktif di website</label></div></div>
    <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="description" rows="3" class="form-control"><?= e($package['description']) ?></textarea></div>
</div><div class="mt-4"><button class="btn btn-warning fw-bold" type="submit">Simpan Nominal</button><a href="packages.php" class="btn btn-outline-light">Batal</a></div></form></div>
<?php require __DIR__ . '/_footer.php'; ?>
