<?php
require_once __DIR__ . '/../includes/helpers.php';
require_login();
$conn = db_connect();
if (!$conn) { flash('Database belum aktif. Jalankan install.php terlebih dahulu.', 'warning'); redirect('../install.php'); }
$games = get_games(false);
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$isEdit = $id > 0;
$service = ['game_id'=>1,'service_type'=>'reguler','rank_name'=>'','rank_order'=>0,'price'=>'','icon'=>'🏆','is_active'=>1];
if ($isEdit) {
    $stmt = $conn->prepare('SELECT * FROM joki_services WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();
    if (!$service) { flash('Harga joki tidak ditemukan.', 'danger'); redirect('joki-services.php'); }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gameId = (int)($_POST['game_id'] ?? 0);
    $serviceType = $_POST['service_type'] ?? 'reguler';
    $rankName = trim($_POST['rank_name'] ?? '');
    $rankOrder = (int)($_POST['rank_order'] ?? 0);
    $price = (float)str_replace(['.', ','], ['', '.'], $_POST['price'] ?? '0');
    $icon = trim($_POST['icon'] ?? '🏆');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    if (!in_array($serviceType, ['reguler','express'], true) || $rankName === '') { flash('Tipe layanan dan rank wajib diisi.', 'danger'); redirect($isEdit ? 'joki-service-form.php?id='.$id : 'joki-service-form.php'); }
    try {
        if ($isEdit) {
            $stmt = $conn->prepare('UPDATE joki_services SET game_id=?, service_type=?, rank_name=?, rank_order=?, price=?, icon=?, is_active=? WHERE id=?');
            $stmt->bind_param('issidsii', $gameId, $serviceType, $rankName, $rankOrder, $price, $icon, $isActive, $id);
            $stmt->execute();
            flash('Harga joki berhasil diperbarui.', 'success');
        } else {
            $stmt = $conn->prepare('INSERT INTO joki_services (game_id, service_type, rank_name, rank_order, price, icon, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('issidsi', $gameId, $serviceType, $rankName, $rankOrder, $price, $icon, $isActive);
            $stmt->execute();
            flash('Harga joki berhasil ditambahkan.', 'success');
        }
        redirect('joki-services.php');
    } catch (Throwable $e) { flash($e->getMessage(), 'danger'); redirect($isEdit ? 'joki-service-form.php?id='.$id : 'joki-service-form.php'); }
}
$pageTitle = ($isEdit ? 'Edit Harga Joki' : 'Tambah Harga Joki') . ' - AFone Store';
$activeAdmin = 'joki';
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4"><div><h1 class="section-title mb-1"><?= $isEdit ? 'Edit Harga Joki' : 'Tambah Harga Joki' ?></h1><p class="text-soft mb-0">Harga ini muncul di halaman Joki.</p></div><a href="joki-services.php" class="btn btn-outline-light">Kembali</a></div>
<div class="form-card"><form method="post"><input type="hidden" name="id" value="<?= (int)$id ?>"><div class="row g-3">
    <div class="col-md-6"><label class="form-label">Game</label><select name="game_id" class="form-select"><?php foreach ($games as $game): ?><option value="<?= (int)$game['id'] ?>" <?= (int)$service['game_id']===(int)$game['id']?'selected':'' ?>><?= e($game['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-6"><label class="form-label">Tipe Layanan</label><select name="service_type" class="form-select"><option value="reguler" <?= $service['service_type']==='reguler'?'selected':'' ?>>Reguler</option><option value="express" <?= $service['service_type']==='express'?'selected':'' ?>>Express</option></select></div>
    <div class="col-md-5"><label class="form-label">Nama Rank</label><input type="text" name="rank_name" class="form-control" value="<?= e($service['rank_name']) ?>" placeholder="Epic / Mythic" required></div>
    <div class="col-md-2"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="<?= e($service['icon']) ?>"></div>
    <div class="col-md-2"><label class="form-label">Urutan</label><input type="number" name="rank_order" class="form-control" value="<?= (int)$service['rank_order'] ?>"></div>
    <div class="col-md-3"><label class="form-label">Harga</label><input type="number" name="price" class="form-control" value="<?= e((string)$service['price']) ?>" min="0" step="100" required></div>
    <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= (int)$service['is_active']===1?'checked':'' ?>><label class="form-check-label" for="is_active">Aktif di website</label></div></div>
</div><div class="mt-4"><button class="btn btn-warning fw-bold" type="submit">Simpan</button><a href="joki-services.php" class="btn btn-outline-light">Batal</a></div></form></div>
<?php require __DIR__ . '/_footer.php'; ?>
