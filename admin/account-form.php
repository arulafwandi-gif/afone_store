<?php
require_once __DIR__ . '/../includes/helpers.php';
require_login();
$conn = db_connect();
if (!$conn) { flash('Database belum aktif. Jalankan install.php terlebih dahulu.', 'warning'); redirect('../install.php'); }
$games = get_games(false);
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$isEdit = $id > 0;
$account = ['game_id'=>($games[0]['id'] ?? 1),'title'=>'','slug'=>'','description'=>'','specs'=>'','image_url'=>'','price'=>'','status'=>'tersedia','sort_order'=>0,'is_active'=>1];
if ($isEdit) {
    $stmt = $conn->prepare('SELECT * FROM game_accounts WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $account = $stmt->get_result()->fetch_assoc();
    if (!$account) { flash('Akun tidak ditemukan.', 'danger'); redirect('accounts.php'); }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gameId = (int)($_POST['game_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
    $description = trim($_POST['description'] ?? '');
    $specs = trim($_POST['specs'] ?? '');
    $imageUrl = trim($_POST['image_url'] ?? '');
    $price = (float)str_replace(['.', ','], ['', '.'], $_POST['price'] ?? '0');
    $status = $_POST['status'] ?? 'tersedia';
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    if ($title === '' || !in_array($status, ['tersedia','terjual','booking'], true)) { flash('Judul akun dan status wajib valid.', 'danger'); redirect($isEdit ? 'account-form.php?id='.$id : 'account-form.php'); }
    try {
        $uploaded = upload_image_file('image_file');
        if ($uploaded) $imageUrl = $uploaded;
        if ($isEdit) {
            $stmt = $conn->prepare('UPDATE game_accounts SET game_id=?, title=?, slug=?, description=?, specs=?, image_url=?, price=?, status=?, sort_order=?, is_active=? WHERE id=?');
            $stmt->bind_param('isssssdsiii', $gameId, $title, $slug, $description, $specs, $imageUrl, $price, $status, $sortOrder, $isActive, $id);
            $stmt->execute();
            flash('Akun berhasil diperbarui.', 'success');
        } else {
            $stmt = $conn->prepare('INSERT INTO game_accounts (game_id, title, slug, description, specs, image_url, price, status, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('isssssdsii', $gameId, $title, $slug, $description, $specs, $imageUrl, $price, $status, $sortOrder, $isActive);
            $stmt->execute();
            flash('Akun berhasil ditambahkan.', 'success');
        }
        redirect('accounts.php');
    } catch (Throwable $e) { flash($e->getMessage(), 'danger'); redirect($isEdit ? 'account-form.php?id='.$id : 'account-form.php'); }
}
$pageTitle = ($isEdit ? 'Edit Akun' : 'Tambah Akun') . ' - AFone Store';
$activeAdmin = 'accounts';
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4"><div><h1 class="section-title mb-1"><?= $isEdit ? 'Edit Akun' : 'Tambah Akun' ?></h1><p class="text-soft mb-0">Data ini muncul di halaman Beli Akun.</p></div><a href="accounts.php" class="btn btn-outline-light">Kembali</a></div>
<div class="form-card"><form method="post" enctype="multipart/form-data"><input type="hidden" name="id" value="<?= (int)$id ?>"><div class="row g-3">
    <div class="col-md-6"><label class="form-label">Game</label><select name="game_id" class="form-select"><?php foreach ($games as $game): ?><option value="<?= (int)$game['id'] ?>" <?= (int)$account['game_id']===(int)$game['id']?'selected':'' ?>><?= e($game['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-6"><label class="form-label">Judul Akun</label><input type="text" name="title" class="form-control" value="<?= e($account['title']) ?>" placeholder="Akun ML Skin Epic" required></div>
    <div class="col-md-6"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="<?= e($account['slug']) ?>" placeholder="otomatis jika dikosongkan"></div>
    <div class="col-md-3"><label class="form-label">Harga</label><input type="number" name="price" class="form-control" value="<?= e((string)$account['price']) ?>" min="0" step="100" required></div>
    <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="tersedia" <?= $account['status']==='tersedia'?'selected':'' ?>>Tersedia</option><option value="booking" <?= $account['status']==='booking'?'selected':'' ?>>Booking</option><option value="terjual" <?= $account['status']==='terjual'?'selected':'' ?>>Terjual</option></select></div>
    <div class="col-md-6"><label class="form-label">URL Gambar</label><input type="text" name="image_url" class="form-control" value="<?= e($account['image_url']) ?>" placeholder="uploads/nama-file.jpg atau URL"></div>
    <div class="col-md-6"><label class="form-label">Upload Gambar</label><input type="file" name="image_file" class="form-control" accept="image/*"><small class="text-soft">Maksimal 2 MB. JPG/PNG/WEBP/GIF.</small></div>
    <div class="col-md-4"><label class="form-label">Urutan</label><input type="number" name="sort_order" class="form-control" value="<?= (int)$account['sort_order'] ?>"></div>
    <div class="col-md-8 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= (int)$account['is_active']===1?'checked':'' ?>><label class="form-check-label" for="is_active">Aktif di website</label></div></div>
    <div class="col-12"><label class="form-label">Spesifikasi Singkat</label><input type="text" name="specs" class="form-control" value="<?= e($account['specs']) ?>" placeholder="Rank Mythic • 90+ hero • Skin epic"></div>
    <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="description" rows="4" class="form-control"><?= e($account['description']) ?></textarea></div>
</div><div class="mt-4"><button class="btn btn-warning fw-bold" type="submit">Simpan Akun</button><a href="accounts.php" class="btn btn-outline-light">Batal</a></div></form></div>
<?php require __DIR__ . '/_footer.php'; ?>
