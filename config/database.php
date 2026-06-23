<?php
// Pengaturan database untuk XAMPP/Laragon.
// Jika MySQL kamu memakai port berbeda, ubah DB_PORT menjadi 3307 atau port yang sesuai.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'afone_store');
define('DB_PORT', 3306);

function db_connect(bool $useDatabase = true)
{
    if (!function_exists('mysqli_report') || !class_exists('mysqli')) {
        return null;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $database = $useDatabase ? DB_NAME : '';
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, $database, DB_PORT);
        $conn->set_charset('utf8mb4');
        return $conn;
    } catch (Throwable $e) {
        return null;
    }
}
