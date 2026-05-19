<?php
include "koneksi.php";

function rupiah($angka){
    return "Rp " . number_format($angka,0,',','.');
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Laporan Mingguan</title>
<style>
body{background:#111;color:white;font-family:Segoe UI;padding:30px}
table{width:90%;margin:auto;border-collapse:collapse;background:#1a1a1a}
th,td{padding:12px;text-align:center}
th{background:#333}
h2{text-align:center}
</style>
</head>
<body>

<h2>LAPORAN PENJUALAN 7 HARI TERAKHIR</h2>

<?php
/* ================= TOTAL TRANSAKSI MINGGU INI ================= */
$q1 = mysqli_query($koneksi,"
SELECT COUNT(*) as total_transaksi,
SUM(total_harga) as total_omzet
FROM transaksi
WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
");

$d1 = mysqli_fetch_assoc($q1);
?>

<h3 align="center">
Total Transaksi : <?= $d1['total_transaksi'] ?> <br>
Total Omzet : <?= rupiah($d1['total_omzet'] ?? 0) ?>
</h3>

<br>

<h3 align="center">Detail Per Hari</h3>

<table border="1">
<tr>
<th>Tanggal</th>
<th>Jumlah Transaksi</th>
<th>Omzet</th>
</tr>

<?php
$q2 = mysqli_query($koneksi,"
SELECT DATE(tanggal) as tgl,
COUNT(*) as jumlah,
SUM(total_harga) as omzet
FROM transaksi
WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY DATE(tanggal)
ORDER BY tgl DESC
");

while($d = mysqli_fetch_assoc($q2)){
?>
<tr>
<td><?= $d['tgl'] ?></td>
<td><?= $d['jumlah'] ?></td>
<td><?= rupiah($d['omzet']) ?></td>
</tr>
<?php } ?>

</table>

</body>
</html>