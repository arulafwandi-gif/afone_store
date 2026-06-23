<?php
require_once __DIR__ . '/../includes/helpers.php';
require_login();
$conn = db_connect();
if (!$conn) { flash('Database belum aktif. Jalankan install.php terlebih dahulu.', 'warning'); redirect('../install.php'); }
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$isEdit = $id > 0;
$game = ['name'=>'','slug'=>'','category'=>'moba','publisher'=>'','description'=>'','instruction'=>'','image_url'=>'','icon_emoji'=>'🎮','is_popular'=>0,'is_active'=>1,'sort_order'=>0];
if ($isEdit) {
    $stmt = $conn->prepare('SELECT * FROM games WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $game = $stmt->get_result()->fetch_assoc();
    if (!$game) { flash('Game tidak ditemukan.', 'danger'); redirect('games.php'); }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = slugify($_POST['slug'] ?: $name);
    $category = $_POST['category'] ?? 'moba';
    $publisher = trim($_POST['publisher'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $instruction = trim($_POST['instruction'] ?? '');
    $imageUrl = trim($_POST['image_url'] ?? '');
    $iconEmoji = trim($_POST['icon_emoji'] ?? '🎮');
    $isPopular = isset($_POST['is_popular']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $valid = ['moba','battle-royale','rpg','sports','lainnya'];
    if ($name === '' || !in_array($category, $valid, true)) { flash('Nama game dan kategori wajib valid.', 'danger'); redirect($isEdit ? 'game-form.php?id='.$id : 'game-form.php'); }
    try {
        $uploaded = upload_image_file('image_file');
        if ($uploaded) $imageUrl = $uploaded;
        if ($isEdit) {
            $stmt = $conn->prepare('UPDATE games SET name=?, slug=?, category=?, publisher=?, description=?, instruction=?, image_url=?, icon_emoji=?, is_popular=?, is_active=?, sort_order=? WHERE id=?');
            $stmt->bind_param('ssssssssiiii', $name, $slug, $category, $publisher, $description, $instruction, $imageUrl, $iconEmoji, $isPopular, $isActive, $sortOrder, $id);
            $stmt->execute();
            flash('Game berhasil diperbarui.', 'success');
        } else {
            $stmt = $conn->prepare('INSERT INTO games (name, slug, category, publisher, description, instruction, image_url, icon_emoji, is_popular, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('ssssssssiii', $name, $slug, $category, $publisher, $description, $instruction, $imageUrl, $iconEmoji, $isPopular, $isActive, $sortOrder);
            $stmt->execute();
            flash('Game berhasil ditambahkan.', 'success');
        }
        redirect('games.php');
    } catch (Throwable $e) {
        flash($e->getMessage(), 'danger');
        redirect($isEdit ? 'game-form.php?id='.$id : 'game-form.php');
    }
}
$pageTitle = ($isEdit ? 'Edit Game' : 'Tambah Game') . ' - AFone Store';
$activeAdmin = 'games';
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4"><div><h1 class="section-title mb-1"><?= $isEdit ? 'Edit Game' : 'Tambah Game' ?></h1><p class="text-soft mb-0">Game akan tampil sebagai menu utama top up.</p></div><a href="games.php" class="btn btn-outline-light">Kembali</a></div>
<div class="form-card">
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= (int)$id ?>">
<div class="row g-3">
    <div class="col-md-7"><label class="form-label">Nama Game</label><input type="text" name="name" class="form-control" value="<?= e($game['name']) ?>" required></div>
    <div class="col-md-5"><label class="form-label">Slug URL</label><input type="text" name="slug" class="form-control" value="<?= e($game['slug']) ?>" placeholder="mobile-legends"></div>
    <div class="col-md-4"><label class="form-label">Kategori</label><select name="category" class="form-select"><option value="moba" <?= $game['category']==='moba'?'selected':'' ?>>MOBA</option><option value="battle-royale" <?= $game['category']==='battle-royale'?'selected':'' ?>>Battle Royale</option><option value="rpg" <?= $game['category']==='rpg'?'selected':'' ?>>RPG</option><option value="sports" <?= $game['category']==='sports'?'selected':'' ?>>Sports</option><option value="lainnya" <?= $game['category']==='lainnya'?'selected':'' ?>>Lainnya</option></select></div>
    <div class="col-md-4"><label class="form-label">Publisher</label><input type="text" name="publisher" class="form-control" value="<?= e($game['publisher']) ?>" placeholder="Moonton / Garena"></div>
    <div class="col-md-2"><label class="form-label">Emoji</label><input type="text" name="icon_emoji" class="form-control" value="<?= e($game['icon_emoji']) ?>"></div>
    <div class="col-md-2"><label class="form-label">Urutan</label><input type="number" name="sort_order" class="form-control" value="<?= e((string)$game['sort_order']) ?>"></div>
    <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3"><?= e($game['description']) ?></textarea></div>
    <div class="col-12"><label class="form-label">Petunjuk ID Game</label><textarea name="instruction" class="form-control" rows="3" placeholder="Contoh: Masukkan User ID dan Zone ID."><?= e($game['instruction']) ?></textarea></div>
    <div class="col-md-7"><label class="form-label">URL Gambar</label><input type="text" name="image_url" class="form-control" value="<?= e($game['image_url']) ?>" placeholder="Opsional"></div>
    <div class="col-md-5"><label class="form-label">Upload Gambar</label><input type="file" name="image_file" class="form-control" accept="image/*"><small class="text-soft">Opsional, maksimal 2 MB.</small></div>
    <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_popular" id="is_popular" <?= (int)$game['is_popular']===1?'checked':'' ?>><label class="form-check-label" for="is_popular">Tandai sebagai game populer</label></div></div>
    <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= (int)$game['is_active']===1?'checked':'' ?>><label class="form-check-label" for="is_active">Aktifkan di website</label></div></div>
</div>
<div class="mt-4"><button class="btn btn-warning fw-bold" type="submit">Simpan Game</button><a href="games.php" class="btn btn-outline-light">Batal</a></div>
</form>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
