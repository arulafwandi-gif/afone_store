CARA PAKAI AFONE STORE - VERSI TOP UP / JOKI / BELI AKUN

1. Extract ZIP.
2. Pindahkan folder top-up-master ke:
   C:\xampp\htdocs\
3. Jalankan XAMPP:
   - Apache: Start
   - MySQL: Start
4. Buka installer:
   http://localhost/top-up-master/install.php
5. Buka website:
   http://localhost/top-up-master/index.php

LOGIN ADMIN
Username: admin
Password: admin123

MENU ADMIN
Dashboard:
http://localhost/top-up-master/admin/index.php

CRUD Game:
http://localhost/top-up-master/admin/games.php

CRUD Nominal Top Up:
http://localhost/top-up-master/admin/packages.php

CRUD Joki:
http://localhost/top-up-master/admin/joki-services.php

CRUD Beli Akun:
http://localhost/top-up-master/admin/accounts.php

Data Order:
http://localhost/top-up-master/admin/orders.php

CARA MASUKKAN FOTO GAME
1. Login admin.
2. Buka CRUD Game.
3. Klik Edit pada game.
4. Upload gambar di bagian Upload Gambar.
5. Simpan.

CARA MASUKKAN FOTO AKUN
1. Login admin.
2. Buka CRUD Beli Akun.
3. Tambah/Edit akun.
4. Upload gambar akun.
5. Simpan.

JIKA MYSQL BEDA PORT
Buka config/database.php, lalu ubah:
define('DB_PORT', 3306);
menjadi port yang dipakai, contoh:
define('DB_PORT', 3307);

CATATAN
- File install.php otomatis membuat database afone_store.
- File afone_store.sql juga tersedia jika ingin import manual lewat phpMyAdmin.
- UI dibuat mengikuti alur website top up umum: Top Up, Joki, Beli Akun, admin CRUD.
