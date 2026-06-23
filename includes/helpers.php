<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rupiah($number): string
{
    return 'Rp ' . number_format((float) $number, 0, ',', '.');
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ../login.php');
        exit;
    }
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash(?string $message = null, string $type = 'success'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }

    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    return null;
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'game';
}

function fallback_games(): array
{
    return [
        ['id'=>1,'name'=>'Mobile Legends','slug'=>'mobile-legends','category'=>'moba','publisher'=>'Moonton','description'=>'Top up diamond MLBB cepat untuk skin, hero, dan kebutuhan event.','instruction'=>'Masukkan User ID dan Zone ID. Contoh: 12345678 (1234).','image_url'=>'','icon_emoji'=>'💎','is_popular'=>1,'is_active'=>1,'sort_order'=>1],
        ['id'=>2,'name'=>'Free Fire','slug'=>'free-fire','category'=>'battle-royale','publisher'=>'Garena','description'=>'Top up diamond FF untuk bundle, elite pass, dan item favorit.','instruction'=>'Masukkan ID pemain Free Fire dengan benar.','image_url'=>'','icon_emoji'=>'🔥','is_popular'=>1,'is_active'=>1,'sort_order'=>2],
        ['id'=>3,'name'=>'PUBG Mobile','slug'=>'pubg-mobile','category'=>'battle-royale','publisher'=>'Tencent','description'=>'Top up UC PUBG Mobile untuk Royale Pass dan item eksklusif.','instruction'=>'Masukkan Character ID PUBG Mobile.','image_url'=>'','icon_emoji'=>'🎯','is_popular'=>1,'is_active'=>1,'sort_order'=>3],
        ['id'=>4,'name'=>'Genshin Impact','slug'=>'genshin-impact','category'=>'rpg','publisher'=>'HoYoverse','description'=>'Top up Genesis Crystal dan Blessing of the Welkin Moon.','instruction'=>'Masukkan UID dan pilih server akun.','image_url'=>'','icon_emoji'=>'⭐','is_popular'=>1,'is_active'=>1,'sort_order'=>4],
        ['id'=>5,'name'=>'Roblox','slug'=>'roblox','category'=>'lainnya','publisher'=>'Roblox Corporation','description'=>'Isi Robux untuk avatar, item, dan pengalaman bermain.','instruction'=>'Masukkan username Roblox.','image_url'=>'','icon_emoji'=>'🧱','is_popular'=>0,'is_active'=>1,'sort_order'=>5],
        ['id'=>6,'name'=>'Valorant','slug'=>'valorant','category'=>'lainnya','publisher'=>'Riot Games','description'=>'Top up Valorant Points untuk skin dan bundle.','instruction'=>'Masukkan Riot ID dan tagline. Contoh: nama#1234.','image_url'=>'','icon_emoji'=>'🛡️','is_popular'=>0,'is_active'=>1,'sort_order'=>6],
        ['id'=>7,'name'=>'Call of Duty Mobile','slug'=>'call-of-duty-mobile','category'=>'battle-royale','publisher'=>'Activision','description'=>'Top up CP CODM untuk battle pass dan crate.','instruction'=>'Masukkan OpenID/Player ID CODM.','image_url'=>'','icon_emoji'=>'⚔️','is_popular'=>0,'is_active'=>1,'sort_order'=>7],
        ['id'=>8,'name'=>'eFootball','slug'=>'efootball','category'=>'sports','publisher'=>'Konami','description'=>'Top up coins eFootball untuk pack dan event.','instruction'=>'Masukkan User ID eFootball.','image_url'=>'','icon_emoji'=>'⚽','is_popular'=>0,'is_active'=>1,'sort_order'=>8],
    ];
}

function fallback_packages(?int $gameId = null): array
{
    $rows = [
        ['id'=>1,'game_id'=>1,'name'=>'5 Diamonds','amount'=>5,'unit'=>'Diamonds','price'=>1500,'original_price'=>0,'badge'=>'HEMAT','description'=>'Paket kecil untuk kebutuhan cepat.','sort_order'=>1,'is_active'=>1],
        ['id'=>2,'game_id'=>1,'name'=>'12 Diamonds','amount'=>12,'unit'=>'Diamonds','price'=>3600,'original_price'=>0,'badge'=>'','description'=>'Cocok untuk isi saldo ringan.','sort_order'=>2,'is_active'=>1],
        ['id'=>3,'game_id'=>1,'name'=>'28 Diamonds','amount'=>28,'unit'=>'Diamonds','price'=>8000,'original_price'=>0,'badge'=>'LARIS','description'=>'Paket populer pemain MLBB.','sort_order'=>3,'is_active'=>1],
        ['id'=>4,'game_id'=>1,'name'=>'86 Diamonds','amount'=>86,'unit'=>'Diamonds','price'=>23000,'original_price'=>0,'badge'=>'BEST','description'=>'Pilihan aman untuk event dan skin.','sort_order'=>4,'is_active'=>1],
        ['id'=>5,'game_id'=>1,'name'=>'172 Diamonds','amount'=>172,'unit'=>'Diamonds','price'=>45000,'original_price'=>0,'badge'=>'PROMO','description'=>'Nominal sedang paling sering dipilih.','sort_order'=>5,'is_active'=>1],
        ['id'=>6,'game_id'=>1,'name'=>'Weekly Diamond Pass','amount'=>0,'unit'=>'Pass','price'=>28500,'original_price'=>0,'badge'=>'PASS','description'=>'Paket mingguan MLBB.','sort_order'=>6,'is_active'=>1],
        ['id'=>7,'game_id'=>2,'name'=>'5 Diamonds','amount'=>5,'unit'=>'Diamonds','price'=>1000,'original_price'=>0,'badge'=>'','description'=>'Diamond FF nominal kecil.','sort_order'=>1,'is_active'=>1],
        ['id'=>8,'game_id'=>2,'name'=>'50 Diamonds','amount'=>50,'unit'=>'Diamonds','price'=>8000,'original_price'=>0,'badge'=>'LARIS','description'=>'Cocok untuk top up harian.','sort_order'=>2,'is_active'=>1],
        ['id'=>9,'game_id'=>2,'name'=>'140 Diamonds','amount'=>140,'unit'=>'Diamonds','price'=>20000,'original_price'=>0,'badge'=>'BEST','description'=>'Paket menengah Free Fire.','sort_order'=>3,'is_active'=>1],
        ['id'=>10,'game_id'=>2,'name'=>'355 Diamonds','amount'=>355,'unit'=>'Diamonds','price'=>50000,'original_price'=>0,'badge'=>'PROMO','description'=>'Untuk event dan bundle.','sort_order'=>4,'is_active'=>1],
        ['id'=>11,'game_id'=>2,'name'=>'720 Diamonds','amount'=>720,'unit'=>'Diamonds','price'=>98000,'original_price'=>0,'badge'=>'SULTAN','description'=>'Paket besar FF.','sort_order'=>5,'is_active'=>1],
        ['id'=>12,'game_id'=>2,'name'=>'Membership Mingguan','amount'=>0,'unit'=>'Membership','price'=>28000,'original_price'=>0,'badge'=>'PASS','description'=>'Membership mingguan Free Fire.','sort_order'=>6,'is_active'=>1],
        ['id'=>13,'game_id'=>3,'name'=>'60 UC','amount'=>60,'unit'=>'UC','price'=>14000,'original_price'=>0,'badge'=>'','description'=>'UC PUBG nominal awal.','sort_order'=>1,'is_active'=>1],
        ['id'=>14,'game_id'=>3,'name'=>'325 UC','amount'=>325,'unit'=>'UC','price'=>70000,'original_price'=>0,'badge'=>'LARIS','description'=>'Paket PUBG paling umum.','sort_order'=>2,'is_active'=>1],
        ['id'=>15,'game_id'=>3,'name'=>'660 UC','amount'=>660,'unit'=>'UC','price'=>140000,'original_price'=>0,'badge'=>'BEST','description'=>'Cocok untuk Royale Pass.','sort_order'=>3,'is_active'=>1],
        ['id'=>16,'game_id'=>3,'name'=>'1800 UC','amount'=>1800,'unit'=>'UC','price'=>350000,'original_price'=>0,'badge'=>'PRO','description'=>'Paket besar UC PUBG.','sort_order'=>4,'is_active'=>1],
        ['id'=>17,'game_id'=>4,'name'=>'60 Genesis Crystals','amount'=>60,'unit'=>'Genesis Crystals','price'=>15000,'original_price'=>0,'badge'=>'','description'=>'Top up kecil Genshin.','sort_order'=>1,'is_active'=>1],
        ['id'=>18,'game_id'=>4,'name'=>'330 Genesis Crystals','amount'=>330,'unit'=>'Genesis Crystals','price'=>75000,'original_price'=>0,'badge'=>'LARIS','description'=>'Paket menengah Genshin.','sort_order'=>2,'is_active'=>1],
        ['id'=>19,'game_id'=>4,'name'=>'Blessing of the Welkin Moon','amount'=>0,'unit'=>'Blessing','price'=>75000,'original_price'=>0,'badge'=>'WELKIN','description'=>'Paket bulanan favorit pemain.','sort_order'=>3,'is_active'=>1],
        ['id'=>20,'game_id'=>5,'name'=>'80 Robux','amount'=>80,'unit'=>'Robux','price'=>16000,'original_price'=>0,'badge'=>'','description'=>'Robux nominal kecil.','sort_order'=>1,'is_active'=>1],
        ['id'=>21,'game_id'=>5,'name'=>'400 Robux','amount'=>400,'unit'=>'Robux','price'=>76000,'original_price'=>0,'badge'=>'LARIS','description'=>'Paket Roblox populer.','sort_order'=>2,'is_active'=>1],
        ['id'=>22,'game_id'=>5,'name'=>'800 Robux','amount'=>800,'unit'=>'Robux','price'=>150000,'original_price'=>0,'badge'=>'BEST','description'=>'Untuk item dan avatar premium.','sort_order'=>3,'is_active'=>1],
        ['id'=>23,'game_id'=>6,'name'=>'420 VP','amount'=>420,'unit'=>'VP','price'=>50000,'original_price'=>0,'badge'=>'','description'=>'Valorant Points nominal awal.','sort_order'=>1,'is_active'=>1],
        ['id'=>24,'game_id'=>6,'name'=>'700 VP','amount'=>700,'unit'=>'VP','price'=>80000,'original_price'=>0,'badge'=>'LARIS','description'=>'Paket Valorant populer.','sort_order'=>2,'is_active'=>1],
        ['id'=>25,'game_id'=>6,'name'=>'1375 VP','amount'=>1375,'unit'=>'VP','price'=>150000,'original_price'=>0,'badge'=>'BEST','description'=>'Cocok untuk skin pilihan.','sort_order'=>3,'is_active'=>1],
        ['id'=>26,'game_id'=>7,'name'=>'63 CP','amount'=>63,'unit'=>'CP','price'=>15000,'original_price'=>0,'badge'=>'','description'=>'CP CODM nominal awal.','sort_order'=>1,'is_active'=>1],
        ['id'=>27,'game_id'=>7,'name'=>'321 CP','amount'=>321,'unit'=>'CP','price'=>75000,'original_price'=>0,'badge'=>'LARIS','description'=>'Paket CODM populer.','sort_order'=>2,'is_active'=>1],
        ['id'=>28,'game_id'=>7,'name'=>'800 CP','amount'=>800,'unit'=>'CP','price'=>180000,'original_price'=>0,'badge'=>'BEST','description'=>'Cocok untuk battle pass.','sort_order'=>3,'is_active'=>1],
        ['id'=>29,'game_id'=>8,'name'=>'130 Coins','amount'=>130,'unit'=>'Coins','price'=>15000,'original_price'=>0,'badge'=>'','description'=>'Coins eFootball kecil.','sort_order'=>1,'is_active'=>1],
        ['id'=>30,'game_id'=>8,'name'=>'550 Coins','amount'=>550,'unit'=>'Coins','price'=>65000,'original_price'=>0,'badge'=>'LARIS','description'=>'Paket eFootball populer.','sort_order'=>2,'is_active'=>1],
        ['id'=>31,'game_id'=>8,'name'=>'1040 Coins','amount'=>1040,'unit'=>'Coins','price'=>125000,'original_price'=>0,'badge'=>'BEST','description'=>'Untuk pack pemain.','sort_order'=>3,'is_active'=>1],
    ];
    if ($gameId !== null) {
        return array_values(array_filter($rows, fn($row) => (int)$row['game_id'] === (int)$gameId));
    }
    return $rows;
}

function get_games(bool $onlyActive = true, ?string $category = null): array
{
    $conn = db_connect();
    if (!$conn) {
        $items = fallback_games();
        if ($onlyActive) $items = array_values(array_filter($items, fn($i) => (int)$i['is_active'] === 1));
        if ($category) $items = array_values(array_filter($items, fn($i) => $i['category'] === $category));
        usort($items, fn($a,$b) => ($a['sort_order'] <=> $b['sort_order']));
        return $items;
    }

    try {
        $where = [];
        $params = [];
        $types = '';
        if ($onlyActive) $where[] = 'is_active = 1';
        if ($category) {
            $where[] = 'category = ?';
            $params[] = $category;
            $types .= 's';
        }
        $sql = 'SELECT * FROM games';
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY sort_order ASC, name ASC';
        $stmt = $conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (Throwable $e) {
        return fallback_games();
    }
}

function get_popular_games(int $limit = 8): array
{
    $conn = db_connect();
    if (!$conn) {
        $items = array_values(array_filter(fallback_games(), fn($i) => (int)$i['is_popular'] === 1));
        return array_slice($items, 0, $limit);
    }
    try {
        $stmt = $conn->prepare('SELECT * FROM games WHERE is_active = 1 AND is_popular = 1 ORDER BY sort_order ASC, name ASC LIMIT ?');
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (Throwable $e) {
        return array_slice(fallback_games(), 0, $limit);
    }
}

function get_game_by_slug(string $slug): ?array
{
    $conn = db_connect();
    if (!$conn) {
        foreach (fallback_games() as $game) if ($game['slug'] === $slug) return $game;
        return null;
    }
    try {
        $stmt = $conn->prepare('SELECT * FROM games WHERE slug = ? LIMIT 1');
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

function get_game(int $id): ?array
{
    $conn = db_connect();
    if (!$conn) {
        foreach (fallback_games() as $game) if ((int)$game['id'] === $id) return $game;
        return null;
    }
    try {
        $stmt = $conn->prepare('SELECT * FROM games WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

function get_packages_by_game(int $gameId, bool $onlyActive = true): array
{
    $conn = db_connect();
    if (!$conn) {
        $items = fallback_packages($gameId);
        if ($onlyActive) $items = array_values(array_filter($items, fn($i) => (int)$i['is_active'] === 1));
        return $items;
    }
    try {
        $sql = 'SELECT * FROM topup_packages WHERE game_id = ?';
        if ($onlyActive) $sql .= ' AND is_active = 1';
        $sql .= ' ORDER BY sort_order ASC, price ASC, id ASC';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $gameId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (Throwable $e) {
        return fallback_packages($gameId);
    }
}

function get_package(int $id): ?array
{
    $conn = db_connect();
    if (!$conn) {
        foreach (fallback_packages() as $p) if ((int)$p['id'] === $id) return $p;
        return null;
    }
    try {
        $stmt = $conn->prepare('SELECT p.*, g.name AS game_name, g.slug AS game_slug FROM topup_packages p JOIN games g ON g.id = p.game_id WHERE p.id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

function category_label(string $category): string
{
    $labels = [
        'populer' => 'Populer',
        'moba' => 'MOBA',
        'battle-royale' => 'Battle Royale',
        'rpg' => 'RPG',
        'sports' => 'Sports',
        'lainnya' => 'Lainnya',
    ];
    return $labels[$category] ?? ucfirst($category);
}

function count_table(string $table): int
{
    $allowed = ['games', 'topup_packages', 'topup_orders', 'joki_services', 'joki_orders', 'game_accounts', 'contact_messages', 'users'];
    if (!in_array($table, $allowed, true)) return 0;
    $conn = db_connect();
    if (!$conn) return 0;
    try {
        $result = $conn->query("SELECT COUNT(*) AS total FROM {$table}");
        return (int)($result->fetch_assoc()['total'] ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

function image_src(?string $path, string $prefix = ''): string
{
    $path = trim((string)$path);
    if ($path === '') return '';
    if (preg_match('/^(https?:)?\/\//i', $path) || substr($path, 0, 1) === '/' || substr($path, 0, 5) === 'data:') return $path;
    return $prefix . $path;
}

function order_status_badge_class(string $status): string
{
    return match ($status) {
        'baru' => 'bg-info text-dark',
        'menunggu pembayaran' => 'bg-secondary',
        'diproses' => 'bg-warning text-dark',
        'selesai' => 'bg-success',
        'dibatalkan' => 'bg-danger',
        default => 'bg-secondary',
    };
}

function payment_methods(): array
{
    return array_column(payment_channels(), 'name');
}

function payment_channels(): array
{
    return [
        ['group' => 'Pembayaran Melalui QRIS', 'name' => 'QRIS', 'label' => 'QRIS', 'fee_type' => 'flat', 'fee' => 0, 'note' => 'Scan semua e-wallet dan mobile banking.'],
        ['group' => 'E-Wallet', 'name' => 'Dana', 'label' => 'DANA', 'fee_type' => 'flat', 'fee' => 0, 'note' => 'Transfer ke nomor DANA admin.'],
        ['group' => 'E-Wallet', 'name' => 'OVO', 'label' => 'OVO', 'fee_type' => 'flat', 'fee' => 0, 'note' => 'Transfer ke nomor OVO admin.'],
        ['group' => 'E-Wallet', 'name' => 'GoPay', 'label' => 'GoPay', 'fee_type' => 'flat', 'fee' => 0, 'note' => 'Transfer ke GoPay admin.'],
        ['group' => 'E-Wallet', 'name' => 'ShopeePay', 'label' => 'ShopeePay', 'fee_type' => 'flat', 'fee' => 0, 'note' => 'Transfer ke ShopeePay admin.'],
        ['group' => 'Transfer Bank', 'name' => 'BCA', 'label' => 'BCA', 'fee_type' => 'flat', 'fee' => 0, 'note' => 'Transfer bank BCA.'],
        ['group' => 'Transfer Bank', 'name' => 'BRI', 'label' => 'BRI', 'fee_type' => 'flat', 'fee' => 0, 'note' => 'Transfer bank BRI.'],
        ['group' => 'Transfer Bank', 'name' => 'BNI', 'label' => 'BNI', 'fee_type' => 'flat', 'fee' => 0, 'note' => 'Transfer bank BNI.'],
        ['group' => 'Transfer Bank', 'name' => 'Mandiri', 'label' => 'Mandiri', 'fee_type' => 'flat', 'fee' => 0, 'note' => 'Transfer bank Mandiri.'],
    ];
}

function payment_channel(string $method): ?array
{
    foreach (payment_channels() as $channel) {
        if (strcasecmp($channel['name'], $method) === 0) return $channel;
    }
    return null;
}

function payment_fee(string $method, float $subtotal): float
{
    $channel = payment_channel($method);
    if (!$channel) return 0;
    if (($channel['fee_type'] ?? 'flat') === 'percent') {
        return max(0, round($subtotal * ((float)$channel['fee'] / 100)));
    }
    return max(0, (float)($channel['fee'] ?? 0));
}

function available_promos(): array
{
    return [
        'HEMAT5' => ['label' => 'Diskon 5%', 'type' => 'percent', 'value' => 5],
        'NEWUSER' => ['label' => 'Diskon Rp 2.000', 'type' => 'flat', 'value' => 2000],
        'AFONE10' => ['label' => 'Diskon 10%', 'type' => 'percent', 'value' => 10],
    ];
}

function promo_discount(?string $code, float $subtotal): float
{
    $code = strtoupper(trim((string)$code));
    if ($code === '') return 0;
    $promos = available_promos();
    if (!isset($promos[$code])) return 0;
    $promo = $promos[$code];
    $discount = ($promo['type'] === 'percent') ? round($subtotal * ((float)$promo['value'] / 100)) : (float)$promo['value'];
    return min($subtotal, max(0, $discount));
}

function grouped_payment_channels(): array
{
    $groups = [];
    foreach (payment_channels() as $channel) {
        $groups[$channel['group']][] = $channel;
    }
    return $groups;
}

function upload_image_file(string $fieldName = 'image_file'): ?string
{
    if (empty($_FILES[$fieldName]['name'])) return null;

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime = mime_content_type($_FILES[$fieldName]['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Format gambar harus JPG, PNG, WEBP, atau GIF.');
    }
    if ($_FILES[$fieldName]['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Ukuran gambar maksimal 2 MB.');
    }

    $filename = 'game_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $destination = __DIR__ . '/../uploads/' . $filename;
    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destination)) {
        throw new RuntimeException('Upload gambar gagal.');
    }
    return 'uploads/' . $filename;
}

function fallback_joki_services(?string $type = null): array
{
    $rows = [
        ['id'=>1,'game_id'=>1,'service_type'=>'reguler','rank_name'=>'Master','rank_order'=>1,'price'=>5000,'icon'=>'🏅','is_active'=>1],
        ['id'=>2,'game_id'=>1,'service_type'=>'reguler','rank_name'=>'Grand Master','rank_order'=>2,'price'=>5500,'icon'=>'🥈','is_active'=>1],
        ['id'=>3,'game_id'=>1,'service_type'=>'reguler','rank_name'=>'Epic','rank_order'=>3,'price'=>7000,'icon'=>'🛡️','is_active'=>1],
        ['id'=>4,'game_id'=>1,'service_type'=>'reguler','rank_name'=>'Legend','rank_order'=>4,'price'=>8000,'icon'=>'👑','is_active'=>1],
        ['id'=>5,'game_id'=>1,'service_type'=>'reguler','rank_name'=>'Mythic Grading','rank_order'=>5,'price'=>230000,'icon'=>'🔰','is_active'=>1],
        ['id'=>6,'game_id'=>1,'service_type'=>'reguler','rank_name'=>'Mythic Romawi','rank_order'=>6,'price'=>19000,'icon'=>'💠','is_active'=>1],
        ['id'=>7,'game_id'=>1,'service_type'=>'reguler','rank_name'=>'Mythical Honor','rank_order'=>7,'price'=>24000,'icon'=>'⚜️','is_active'=>1],
        ['id'=>8,'game_id'=>1,'service_type'=>'reguler','rank_name'=>'Mythical Glory','rank_order'=>8,'price'=>29000,'icon'=>'🏆','is_active'=>1],
        ['id'=>9,'game_id'=>1,'service_type'=>'reguler','rank_name'=>'Mythical Immortal','rank_order'=>9,'price'=>34000,'icon'=>'💫','is_active'=>1],
        ['id'=>10,'game_id'=>1,'service_type'=>'express','rank_name'=>'Master','rank_order'=>1,'price'=>7000,'icon'=>'🏅','is_active'=>1],
        ['id'=>11,'game_id'=>1,'service_type'=>'express','rank_name'=>'Grand Master','rank_order'=>2,'price'=>8000,'icon'=>'🥈','is_active'=>1],
        ['id'=>12,'game_id'=>1,'service_type'=>'express','rank_name'=>'Epic','rank_order'=>3,'price'=>10000,'icon'=>'🛡️','is_active'=>1],
        ['id'=>13,'game_id'=>1,'service_type'=>'express','rank_name'=>'Legend','rank_order'=>4,'price'=>12000,'icon'=>'👑','is_active'=>1],
        ['id'=>14,'game_id'=>1,'service_type'=>'express','rank_name'=>'Mythic Romawi','rank_order'=>6,'price'=>27000,'icon'=>'💠','is_active'=>1],
        ['id'=>15,'game_id'=>1,'service_type'=>'express','rank_name'=>'Mythical Honor','rank_order'=>7,'price'=>33000,'icon'=>'⚜️','is_active'=>1],
        ['id'=>16,'game_id'=>1,'service_type'=>'express','rank_name'=>'Mythical Glory','rank_order'=>8,'price'=>40000,'icon'=>'🏆','is_active'=>1],
        ['id'=>17,'game_id'=>1,'service_type'=>'express','rank_name'=>'Mythical Immortal','rank_order'=>9,'price'=>48000,'icon'=>'💫','is_active'=>1],
    ];
    if ($type !== null) return array_values(array_filter($rows, fn($row) => $row['service_type'] === $type));
    return $rows;
}

function fallback_accounts(): array
{
    return [
        ['id'=>1,'game_id'=>2,'game_name'=>'Free Fire','title'=>'Akun FF Sultan Bundle Rare','slug'=>'akun-ff-sultan-bundle-rare','description'=>'Rank Heroic, banyak bundle event, cocok untuk koleksi dan push rank.','image_url'=>'','price'=>350000,'status'=>'tersedia','specs'=>'Rank Heroic • 25+ bundle • Login aman','sort_order'=>1,'is_active'=>1],
        ['id'=>2,'game_id'=>1,'game_name'=>'Mobile Legends','title'=>'Akun ML Epic Skin Collector','slug'=>'akun-ml-epic-skin-collector','description'=>'Hero lengkap, beberapa skin epic dan collector, siap main ranked.','image_url'=>'','price'=>550000,'status'=>'tersedia','specs'=>'90+ hero • Skin epic • Rank Mythic','sort_order'=>2,'is_active'=>1],
        ['id'=>3,'game_id'=>8,'game_name'=>'eFootball','title'=>'Akun eFootball Banyak Pemain Legenda','slug'=>'akun-efootball-legenda','description'=>'Akun siap main dengan beberapa pemain unggulan dan GP banyak.','image_url'=>'','price'=>275000,'status'=>'tersedia','specs'=>'Pemain legenda • GP banyak • Tim siap pakai','sort_order'=>3,'is_active'=>1],
    ];
}

function get_joki_services(?string $type = null, bool $onlyActive = true): array
{
    $conn = db_connect();
    if (!$conn) {
        $items = fallback_joki_services($type);
        if ($onlyActive) $items = array_values(array_filter($items, fn($i) => (int)$i['is_active'] === 1));
        return $items;
    }
    try {
        $where = [];
        $params = [];
        $types = '';
        if ($type !== null) { $where[] = 'js.service_type = ?'; $params[] = $type; $types .= 's'; }
        if ($onlyActive) $where[] = 'js.is_active = 1';
        $sql = 'SELECT js.*, g.name AS game_name FROM joki_services js LEFT JOIN games g ON g.id = js.game_id';
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY js.service_type ASC, js.rank_order ASC, js.price ASC';
        $stmt = $conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (Throwable $e) {
        return fallback_joki_services($type);
    }
}

function get_joki_service(int $id): ?array
{
    $conn = db_connect();
    if (!$conn) { foreach (fallback_joki_services() as $s) if ((int)$s['id'] === $id) return $s; return null; }
    try {
        $stmt = $conn->prepare('SELECT * FROM joki_services WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    } catch (Throwable $e) { return null; }
}

function get_game_accounts(bool $onlyActive = true, ?int $gameId = null): array
{
    $conn = db_connect();
    if (!$conn) {
        $items = fallback_accounts();
        if ($onlyActive) $items = array_values(array_filter($items, fn($i) => (int)$i['is_active'] === 1));
        if ($gameId !== null && $gameId > 0) $items = array_values(array_filter($items, fn($i) => (int)$i['game_id'] === $gameId));
        return $items;
    }
    try {
        $where = [];
        $params = [];
        $types = '';
        if ($onlyActive) $where[] = 'ga.is_active = 1';
        if ($gameId !== null && $gameId > 0) { $where[] = 'ga.game_id = ?'; $params[] = $gameId; $types .= 'i'; }
        $sql = 'SELECT ga.*, g.name AS game_name, g.image_url AS game_image, g.icon_emoji AS game_icon FROM game_accounts ga LEFT JOIN games g ON g.id = ga.game_id';
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY ga.sort_order ASC, ga.id DESC';
        $stmt = $conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (Throwable $e) { return fallback_accounts(); }
}

function get_game_account(int $id): ?array
{
    $conn = db_connect();
    if (!$conn) { foreach (fallback_accounts() as $a) if ((int)$a['id'] === $id) return $a; return null; }
    try {
        $stmt = $conn->prepare('SELECT * FROM game_accounts WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    } catch (Throwable $e) { return null; }
}

function joki_price_map(string $serviceType = 'reguler'): array
{
    $map = [];
    foreach (get_joki_services($serviceType, true) as $service) {
        $map[$service['rank_name']] = (float)$service['price'];
    }
    return $map;
}
