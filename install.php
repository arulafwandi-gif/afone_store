<?php
require_once __DIR__ . '/includes/helpers.php';
$errors = [];
$success = [];

function install_column_exists(mysqli $conn, string $table, string $column): bool
{
    $stmt = $conn->prepare('SELECT COUNT(*) AS total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $db = DB_NAME;
    $stmt->bind_param('sss', $db, $table, $column);
    $stmt->execute();
    return (int)($stmt->get_result()->fetch_assoc()['total'] ?? 0) > 0;
}

function install_add_column(mysqli $conn, string $table, string $column, string $definition, array &$success): void
{
    if (!install_column_exists($conn, $table, $column)) {
        $conn->query("ALTER TABLE `$table` ADD COLUMN $definition");
        $success[] = "Kolom `$table.$column` berhasil ditambahkan.";
    }
}

$conn = db_connect(false);
if (!$conn) {
    $errors[] = 'Gagal konek ke MySQL. Cek config/database.php: host, user, password, dan port MySQL.';
} else {
    try {
        $conn->query('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $conn->select_db(DB_NAME);
        $success[] = 'Database `' . DB_NAME . '` siap digunakan.';

        $conn->query("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            username VARCHAR(60) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(30) NOT NULL DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS games (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(140) NOT NULL,
            slug VARCHAR(160) NOT NULL UNIQUE,
            category ENUM('moba','battle-royale','rpg','sports','lainnya') NOT NULL DEFAULT 'lainnya',
            publisher VARCHAR(120) NULL,
            description TEXT NULL,
            instruction TEXT NULL,
            image_url TEXT NULL,
            icon_emoji VARCHAR(20) NULL,
            is_popular TINYINT(1) NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS topup_packages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            game_id INT NOT NULL,
            name VARCHAR(160) NOT NULL,
            amount INT NOT NULL DEFAULT 0,
            unit VARCHAR(60) NOT NULL DEFAULT 'Diamonds',
            price DECIMAL(12,2) NOT NULL DEFAULT 0,
            original_price DECIMAL(12,2) NOT NULL DEFAULT 0,
            badge VARCHAR(50) NULL,
            description TEXT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX (game_id),
            CONSTRAINT fk_topup_packages_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS topup_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            game_id INT NULL,
            package_id INT NULL,
            game_name VARCHAR(140) NOT NULL,
            package_name VARCHAR(160) NOT NULL,
            package_price DECIMAL(12,2) NOT NULL DEFAULT 0,
            promo_code VARCHAR(60) NULL,
            promo_discount DECIMAL(12,2) NOT NULL DEFAULT 0,
            payment_fee DECIMAL(12,2) NOT NULL DEFAULT 0,
            total_price DECIMAL(12,2) NOT NULL DEFAULT 0,
            customer_name VARCHAR(120) NOT NULL,
            whatsapp VARCHAR(40) NOT NULL,
            email VARCHAR(150) NULL,
            user_id VARCHAR(120) NOT NULL,
            zone_id VARCHAR(120) NULL,
            nickname VARCHAR(120) NULL,
            payment_method VARCHAR(60) NOT NULL DEFAULT 'QRIS',
            refund_account_type VARCHAR(80) NULL,
            refund_account_number VARCHAR(120) NULL,
            notes TEXT NULL,
            status ENUM('baru','menunggu pembayaran','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'baru',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX (game_id),
            INDEX (package_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        install_add_column($conn, 'topup_orders', 'promo_code', 'promo_code VARCHAR(60) NULL AFTER package_price', $success);
        install_add_column($conn, 'topup_orders', 'promo_discount', 'promo_discount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER promo_code', $success);
        install_add_column($conn, 'topup_orders', 'payment_fee', 'payment_fee DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER promo_discount', $success);
        install_add_column($conn, 'topup_orders', 'total_price', 'total_price DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER payment_fee', $success);
        install_add_column($conn, 'topup_orders', 'email', 'email VARCHAR(150) NULL AFTER whatsapp', $success);
        install_add_column($conn, 'topup_orders', 'refund_account_type', 'refund_account_type VARCHAR(80) NULL AFTER payment_method', $success);
        install_add_column($conn, 'topup_orders', 'refund_account_number', 'refund_account_number VARCHAR(120) NULL AFTER refund_account_type', $success);
        $conn->query('UPDATE topup_orders SET total_price = package_price WHERE total_price = 0');


        $conn->query("CREATE TABLE IF NOT EXISTS joki_services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            game_id INT NULL,
            service_type ENUM('reguler','express') NOT NULL DEFAULT 'reguler',
            rank_name VARCHAR(120) NOT NULL,
            rank_order INT NOT NULL DEFAULT 0,
            price DECIMAL(12,2) NOT NULL DEFAULT 0,
            icon VARCHAR(30) NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX (game_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS joki_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            current_rank VARCHAR(120) NOT NULL,
            current_star INT NOT NULL DEFAULT 0,
            target_rank VARCHAR(120) NOT NULL,
            target_star INT NOT NULL DEFAULT 0,
            service_type ENUM('reguler','express') NOT NULL DEFAULT 'reguler',
            whatsapp VARCHAR(40) NOT NULL,
            account_info VARCHAR(180) NULL,
            estimated_price DECIMAL(12,2) NOT NULL DEFAULT 0,
            notes TEXT NULL,
            status ENUM('baru','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'baru',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS game_accounts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            game_id INT NULL,
            title VARCHAR(180) NOT NULL,
            slug VARCHAR(200) NOT NULL UNIQUE,
            description TEXT NULL,
            specs TEXT NULL,
            image_url TEXT NULL,
            price DECIMAL(12,2) NOT NULL DEFAULT 0,
            status ENUM('tersedia','terjual','booking') NOT NULL DEFAULT 'tersedia',
            sort_order INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX (game_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            phone VARCHAR(40) NOT NULL,
            message TEXT NOT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $success[] = 'Tabel utama berhasil dibuat/diperbarui.';

        $stmt = $conn->prepare('SELECT COUNT(*) AS total FROM users WHERE username = ?');
        $adminUsername = 'admin';
        $stmt->bind_param('s', $adminUsername);
        $stmt->execute();
        $userCount = (int)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
        if ($userCount === 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $name = 'Administrator';
            $role = 'admin';
            $stmt = $conn->prepare('INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $name, $adminUsername, $hash, $role);
            $stmt->execute();
            $success[] = 'Akun admin dibuat: admin / admin123.';
        } else {
            $success[] = 'Akun admin sudah tersedia.';
        }

        $countGames = (int)($conn->query('SELECT COUNT(*) AS total FROM games')->fetch_assoc()['total'] ?? 0);
        if ($countGames === 0) {
            $stmt = $conn->prepare('INSERT INTO games (name, slug, category, publisher, description, instruction, image_url, icon_emoji, is_popular, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            foreach (fallback_games() as $game) {
                $name = $game['name'];
                $slug = $game['slug'];
                $category = $game['category'];
                $publisher = $game['publisher'];
                $description = $game['description'];
                $instruction = $game['instruction'];
                $imageUrl = $game['image_url'];
                $iconEmoji = $game['icon_emoji'];
                $isPopular = (int)$game['is_popular'];
                $isActive = (int)$game['is_active'];
                $sortOrder = (int)$game['sort_order'];
                $stmt->bind_param('ssssssssiii', $name, $slug, $category, $publisher, $description, $instruction, $imageUrl, $iconEmoji, $isPopular, $isActive, $sortOrder);
                $stmt->execute();
            }
            $success[] = 'Data contoh game populer berhasil ditambahkan.';
        } else {
            $success[] = 'Data game sudah ada, seed game tidak ditambahkan ulang.';
        }

        $countPackages = (int)($conn->query('SELECT COUNT(*) AS total FROM topup_packages')->fetch_assoc()['total'] ?? 0);
        if ($countPackages === 0) {
            $map = [];
            $result = $conn->query('SELECT id, slug FROM games');
            while ($row = $result->fetch_assoc()) $map[$row['slug']] = (int)$row['id'];
            $gameByOldId = [];
            foreach (fallback_games() as $g) if (isset($map[$g['slug']])) $gameByOldId[(int)$g['id']] = $map[$g['slug']];
            $stmt = $conn->prepare('INSERT INTO topup_packages (game_id, name, amount, unit, price, original_price, badge, description, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            foreach (fallback_packages() as $p) {
                $realGameId = $gameByOldId[(int)$p['game_id']] ?? null;
                if (!$realGameId) continue;
                $packageName = $p['name'];
                $amount = (int)$p['amount'];
                $unit = $p['unit'];
                $price = (float)$p['price'];
                $originalPrice = (float)$p['original_price'];
                $badge = $p['badge'];
                $description = $p['description'];
                $sortOrder = (int)$p['sort_order'];
                $isActive = (int)$p['is_active'];
                $stmt->bind_param('isisddssii', $realGameId, $packageName, $amount, $unit, $price, $originalPrice, $badge, $description, $sortOrder, $isActive);
                $stmt->execute();
            }
            $success[] = 'Data contoh nominal top up berhasil ditambahkan.';
        } else {
            $success[] = 'Data nominal sudah ada, seed nominal tidak ditambahkan ulang.';
        }


        $countJoki = (int)($conn->query('SELECT COUNT(*) AS total FROM joki_services')->fetch_assoc()['total'] ?? 0);
        if ($countJoki === 0) {
            $map = [];
            $result = $conn->query('SELECT id, slug FROM games');
            while ($row = $result->fetch_assoc()) $map[$row['slug']] = (int)$row['id'];
            $mlId = $map['mobile-legends'] ?? null;
            $stmt = $conn->prepare('INSERT INTO joki_services (game_id, service_type, rank_name, rank_order, price, icon, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)');
            foreach (fallback_joki_services() as $s) {
                $gid = $mlId ?: null;
                $serviceType = $s['service_type'];
                $rankName = $s['rank_name'];
                $rankOrder = (int)$s['rank_order'];
                $price = (float)$s['price'];
                $icon = $s['icon'];
                $active = (int)$s['is_active'];
                $stmt->bind_param('issidsi', $gid, $serviceType, $rankName, $rankOrder, $price, $icon, $active);
                $stmt->execute();
            }
            $success[] = 'Data contoh harga joki berhasil ditambahkan.';
        } else {
            $success[] = 'Data joki sudah ada, seed joki tidak ditambahkan ulang.';
        }

        $countAccounts = (int)($conn->query('SELECT COUNT(*) AS total FROM game_accounts')->fetch_assoc()['total'] ?? 0);
        if ($countAccounts === 0) {
            $map = [];
            $result = $conn->query('SELECT id, slug FROM games');
            while ($row = $result->fetch_assoc()) $map[$row['slug']] = (int)$row['id'];
            $stmt = $conn->prepare('INSERT INTO game_accounts (game_id, title, slug, description, specs, image_url, price, status, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            foreach (fallback_accounts() as $idx => $a) {
                $gameSlug = match ((int)$a['game_id']) { 1 => 'mobile-legends', 2 => 'free-fire', 8 => 'efootball', default => 'mobile-legends' };
                $gid = $map[$gameSlug] ?? null;
                $title = $a['title'];
                $slug = $a['slug'];
                $description = $a['description'];
                $specs = $a['specs'];
                $imageUrl = $a['image_url'];
                $price = (float)$a['price'];
                $status = $a['status'];
                $sortOrder = (int)$a['sort_order'];
                $active = (int)$a['is_active'];
                $stmt->bind_param('isssssdsii', $gid, $title, $slug, $description, $specs, $imageUrl, $price, $status, $sortOrder, $active);
                $stmt->execute();
            }
            $success[] = 'Data contoh akun game berhasil ditambahkan.';
        } else {
            $success[] = 'Data akun game sudah ada, seed akun tidak ditambahkan ulang.';
        }
    } catch (Throwable $e) {
        $errors[] = 'Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Database - AFone Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<section class="container my-5">
    <div class="form-card">
        <div class="section-kicker mb-2">Installer</div>
        <h1 class="section-title mb-3">Install Database AFone Store</h1>
        <?php foreach ($success as $msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endforeach; ?>
        <?php foreach ($errors as $msg): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endforeach; ?>
        <div class="d-flex gap-2 flex-wrap mt-4">
            <a href="index.php" class="btn btn-warning fw-bold">Buka Website</a>
            <a href="TopUp.php" class="btn btn-outline-warning">Lihat Top Up</a>
            <a href="daftar-harga.php" class="btn btn-outline-warning">Daftar Harga</a>
            <a href="login.php" class="btn btn-outline-light">Login Admin</a>
            <a href="admin/games.php" class="btn btn-outline-light">CRUD Game</a>
        </div>
        <div class="alert alert-dark border-warning text-soft mt-4 mb-0">
            Jika MySQL memakai port berbeda, buka <strong>config/database.php</strong>, lalu ubah <strong>DB_PORT</strong> dari 3306 ke port MySQL kamu.
        </div>
    </div>
</section>
</body>
</html>
