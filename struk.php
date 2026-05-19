<?php
include "koneksi.php";

function rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

$id = $_GET['id'];

// ambil data transaksi
$t = mysqli_query($koneksi,"SELECT * FROM transaksi WHERE id_transaksi='$id'");
$transaksi = mysqli_fetch_assoc($t);

// ambil detail belanja
$detail = mysqli_query($koneksi,"SELECT * FROM detail_transaksi WHERE id_transaksi='$id'");
?>

<!DOCTYPE html>
<html>
<head>
<title>Struk Belanja</title>
<style>
body{
    background:#111;
    font-family:monospace;
    color:white;
    text-align:center;
}
.struk{
    width:320px;
    margin:auto;
    background:#fff;
    color:#000;
    padding:20px;
    border-radius:10px;
}
hr{border:1px dashed #000}
button{
    padding:10px;
    margin-top:20px;
    background:#3b82f6;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}
</style>
</head>
<body>

<div class="struk">
    <h3>BALE SEPATU MANTAN</h3>
    <small><?= date("d/m/Y H:i") ?></small>
    <hr>

    <?php while($d = mysqli_fetch_array($detail)){ ?>
        <?= $d['nama_sepatu'] ?><br>
        <?= $d['qty'] ?> x <?= rupiah($d['harga']) ?>  
        = <?= rupiah($d['subtotal']) ?><br><br>
    <?php } ?>

    <hr>
    <b>Total : <?= rupiah($transaksi['total_harga']) ?></b><br>
    Bayar : <?= rupiah($transaksi['bayar']) ?><br>
    Kembali : <?= rupiah($transaksi['kembalian']) ?><br>
    <hr>
    <b>TERIMA KASIH 🙏</b>
</div>

<button onclick="window.print()">🖨 Print</button>

</body>
</html>