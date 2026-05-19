<?php
include "koneksi.php";
/* ================= HAPUS TRANSAKSI ================= */
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];

    // hapus detail transaksi dulu
    mysqli_query($koneksi,"DELETE FROM detail_transaksi WHERE id_transaksi='$id'");

    // hapus transaksi utama
    mysqli_query($koneksi,"DELETE FROM transaksi WHERE id_transaksi='$id'");

    echo "<script>
    alert('Transaksi berhasil dihapus');
    location='transaksi.php';
    </script>";
}

function rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

$id = $_GET['id'];

$data = mysqli_query($koneksi,"SELECT * FROM detail_transaksi WHERE id_transaksi='$id'");
?>


<h2>🧾 Detail Transaksi</h2>

<table border="1">
<tr>
<th>Nama Sepatu</th>
<th>Qty</th>
<th>Harga</th>
<th>Subtotal</th>
</tr>

<?php while($d=mysqli_fetch_array($data)){ ?>
<tr>
<td><?= $d['nama_sepatu'] ?></td>
<td><?= $d['qty'] ?></td>
<td><?= rupiah($d['harga']) ?></td>
<td><?= rupiah($d['subtotal']) ?></td>
</tr>
<?php } ?>

</table>

</body>
</html>