CREATE DATABASE IF NOT EXISTS afone_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE afone_store;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  username VARCHAR(60) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(30) NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS games (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS topup_packages (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS topup_orders (
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
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS joki_services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  game_id INT NULL,
  service_type ENUM('reguler','express') NOT NULL DEFAULT 'reguler',
  rank_name VARCHAR(120) NOT NULL,
  rank_order INT NOT NULL DEFAULT 0,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  icon VARCHAR(30) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS joki_orders (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS game_accounts (
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
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO users (id, name, username, password, role) VALUES
(1, 'Administrator', 'admin', '$2y$12$CKWiHNP2sizrPpYPGL36cO/g11tecE1.sGAyl17w4WVIlArGE1HgS', 'admin');

INSERT IGNORE INTO games (id, name, slug, category, publisher, description, instruction, image_url, icon_emoji, is_popular, is_active, sort_order) VALUES
(1,'Mobile Legends','mobile-legends','moba','Moonton','Top up diamond MLBB cepat.','Masukkan User ID dan Zone ID. Contoh: 12345678 (1234).','','💎',1,1,1),
(2,'Free Fire','free-fire','battle-royale','Garena','Top up diamond FF.','Masukkan ID pemain Free Fire dengan benar.','','🔥',1,1,2),
(3,'Roblox','roblox','lainnya','Roblox Corporation','Isi Robux untuk avatar dan item.','Masukkan username Roblox.','','🧱',1,1,3),
(4,'PUBG Mobile','pubg-mobile','battle-royale','Tencent','Top up UC PUBG Mobile.','Masukkan Character ID PUBG Mobile.','','🎯',1,1,4),
(5,'eFootball','efootball','sports','Konami','Top up coins eFootball.','Masukkan User ID eFootball.','','⚽',1,1,5),
(6,'Valorant','valorant','lainnya','Riot Games','Top up Valorant Points.','Masukkan Riot ID dan tagline.','','🛡️',0,1,6);

INSERT IGNORE INTO topup_packages (id, game_id, name, amount, unit, price, original_price, badge, description, sort_order, is_active) VALUES
(1,1,'10 Diamonds',10,'Diamonds',2990,3500,'PROMO','Paket kecil MLBB.',1,1),
(2,1,'86 Diamonds',86,'Diamonds',23000,0,'LARIS','Paket MLBB populer.',2,1),
(3,1,'172 Diamonds',172,'Diamonds',45000,0,'BEST','Nominal sedang MLBB.',3,1),
(4,1,'Weekly Diamond Pass',0,'Pass',27599,31000,'WDP','Paket mingguan MLBB.',4,1),
(5,2,'50 Diamonds',50,'Diamonds',8000,0,'LARIS','Paket FF ringan.',1,1),
(6,2,'140 Diamonds',140,'Diamonds',20000,0,'BEST','Paket FF populer.',2,1),
(7,2,'Membership Mingguan',0,'Membership',28000,0,'PASS','Membership FF.',3,1),
(8,3,'80 Robux',80,'Robux',16000,0,'','Robux kecil.',1,1),
(9,3,'400 Robux',400,'Robux',76000,0,'LARIS','Robux populer.',2,1),
(10,4,'325 UC',325,'UC',70000,0,'LARIS','UC PUBG populer.',1,1),
(11,5,'550 Coins',550,'Coins',65000,0,'LARIS','Coins eFootball.',1,1),
(12,6,'700 VP',700,'VP',80000,0,'LARIS','Valorant Points.',1,1);

INSERT IGNORE INTO joki_services (id, game_id, service_type, rank_name, rank_order, price, icon, is_active) VALUES
(1,1,'reguler','Master',1,5000,'🏅',1),(2,1,'reguler','Grand Master',2,5500,'🥈',1),(3,1,'reguler','Epic',3,7000,'🛡️',1),(4,1,'reguler','Legend',4,8000,'👑',1),(5,1,'reguler','Mythic Grading',5,230000,'🔰',1),(6,1,'reguler','Mythic Romawi',6,19000,'💠',1),(7,1,'reguler','Mythical Honor',7,24000,'⚜️',1),(8,1,'reguler','Mythical Glory',8,29000,'🏆',1),(9,1,'reguler','Mythical Immortal',9,34000,'💫',1),
(10,1,'express','Master',1,7000,'🏅',1),(11,1,'express','Grand Master',2,8000,'🥈',1),(12,1,'express','Epic',3,10000,'🛡️',1),(13,1,'express','Legend',4,12000,'👑',1),(14,1,'express','Mythic Romawi',6,27000,'💠',1),(15,1,'express','Mythical Honor',7,33000,'⚜️',1),(16,1,'express','Mythical Glory',8,40000,'🏆',1),(17,1,'express','Mythical Immortal',9,48000,'💫',1);

INSERT IGNORE INTO game_accounts (id, game_id, title, slug, description, specs, image_url, price, status, sort_order, is_active) VALUES
(1,2,'Akun FF Sultan Bundle Rare','akun-ff-sultan-bundle-rare','Rank Heroic, banyak bundle event, cocok untuk koleksi dan push rank.','Rank Heroic • 25+ bundle • Login aman','',350000,'tersedia',1,1),
(2,1,'Akun ML Epic Skin Collector','akun-ml-epic-skin-collector','Hero lengkap, beberapa skin epic dan collector, siap main ranked.','90+ hero • Skin epic • Rank Mythic','',550000,'tersedia',2,1),
(3,5,'Akun eFootball Banyak Pemain Legenda','akun-efootball-legenda','Akun siap main dengan beberapa pemain unggulan dan GP banyak.','Pemain legenda • GP banyak • Tim siap pakai','',275000,'tersedia',3,1);
