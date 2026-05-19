<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "bale_sepatu_mantan";

$koneksi = mysqli_connect($host, $username, $password, $dbname);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
} else {
    echo "Koneksi berhasil!";
}
?>