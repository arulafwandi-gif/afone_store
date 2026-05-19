<?php
include "koneksi.php";

/* ============================
   FUNCTION AUTO KODE SPT001
============================ */
function generateKodeSepatu($koneksi){
    $query = mysqli_query($koneksi,"SELECT MAX(kode_sepatu) as kodeTerbesar FROM `data barang`");
    $data  = mysqli_fetch_assoc($query);

    if($data['kodeTerbesar']){
        $urutan = (int) substr($data['kodeTerbesar'], 3, 3);
        $urutan++;
    }else{
        $urutan = 1;
    }

    return "SPT".str_pad($urutan,3,"0",STR_PAD_LEFT);
}

/* ============================
   TAMBAH DATA
============================ */
if(isset($_POST['tambah'])){
    $kode   = generateKodeSepatu($koneksi);
    $nama   = $_POST['nama_sepatu'];
    $size   = $_POST['size'];
    $stok   = $_POST['stok'];
    $modal  = $_POST['harga_modal'];
    $jual   = $_POST['harga_jual'];

    mysqli_query($koneksi,"INSERT INTO `data barang`
    (kode_sepatu,nama_sepatu,size,stok,harga_modal,harga_jual)
    VALUES('$kode','$nama','$size','$stok','$modal','$jual')");

    header("Location: data_sepatu.php"); 
    exit;
}

/* ============================
   HAPUS DATA
============================ */
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM `data barang` WHERE id_sepatu='$id'");
    header("Location: data_sepatu.php");
    exit;
}

/* ============================
   AMBIL DATA EDIT
============================ */
$editData = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $queryEdit = mysqli_query($koneksi,"SELECT * FROM `data barang` WHERE id_sepatu='$id'");
    $editData = mysqli_fetch_assoc($queryEdit);
}

/* ============================
   UPDATE DATA
============================ */
if(isset($_POST['update'])){
    $id     = $_POST['id_sepatu'];
    $nama   = $_POST['nama_sepatu'];
    $size   = $_POST['size'];
    $stok   = $_POST['stok'];
    $modal  = $_POST['harga_modal'];
    $jual   = $_POST['harga_jual'];

    mysqli_query($koneksi,"UPDATE `data barang` SET
        nama_sepatu='$nama',
        size='$size',
        stok='$stok',
        harga_modal='$modal',
        harga_jual='$jual'
        WHERE id_sepatu='$id'");

    header("Location: data_sepatu.php");
    exit;
}
?>

<style>
body{background:#0f1f17;color:white;font-family:Segoe UI;padding:30px}
h2{text-align:center;color:#4ade80}
form{background:#1b2f26;padding:25px;border-radius:14px;width:360px;margin:auto;box-shadow:0 0 25px rgba(74,222,128,.2)}
input{width:100%;padding:10px;margin-top:5px;margin-bottom:15px;border-radius:8px;background:#0b1712;color:white;border:1px solid #355e4a}
button{background:#4ade80;color:#052e16;padding:12px;border:none;border-radius:10px;width:100%;cursor:pointer;font-weight:bold}
button:hover{background:#22c55e}
table{margin:auto;border-collapse:collapse;width:90%;background:#1b2f26;border-radius:12px;overflow:hidden;margin-top:40px}
th{background:#4ade80;color:#052e16;padding:14px}
td{padding:12px;text-align:center}
tr:nth-child(even){background:#0f1f17}
tr:hover{background:#1f3a2f}
a{color:#86efac;text-decoration:none;font-weight:bold}
</style>

<h2>👟 Data Sepatu</h2>

<form method="POST">
    <input type="hidden" name="id_sepatu" value="<?= $editData['id_sepatu'] ?? '' ?>">

    Nama Sepatu :
    <input type="text" name="nama_sepatu" required value="<?= $editData['nama_sepatu'] ?? '' ?>">

    Size :
    <input type="text" name="size" required value="<?= $editData['size'] ?? '' ?>">

    Stok :
    <input type="number" name="stok" required value="<?= $editData['stok'] ?? '' ?>">

    Harga Modal :
    <input type="number" name="harga_modal" required value="<?= $editData['harga_modal'] ?? '' ?>">

    Harga Jual :
    <input type="number" name="harga_jual" required value="<?= $editData['harga_jual'] ?? '' ?>">

    <?php if($editData): ?>
        <button type="submit" name="update">Update</button>
    <?php else: ?>
        <button type="submit" name="tambah">Simpan</button>
    <?php endif; ?>
</form>

<table>
<tr>
    <th>ID</th>
    <th>KODE</th>
    <th>Nama</th>
    <th>Size</th>
    <th>Stok</th>
    <th>Modal</th>
    <th>Jual</th>
    <th>Aksi</th>
</tr>

<?php
$data = mysqli_query($koneksi,"SELECT * FROM `data barang` ORDER BY id_sepatu DESC");
while($d = mysqli_fetch_array($data)){
?>
<tr>
    <td><?= $d['id_sepatu'] ?></td>
    <td><b style="color:#22c55e"><?= $d['kode_sepatu'] ?></b></td>
    <td><?= $d['nama_sepatu'] ?></td>
    <td><?= $d['size'] ?></td>
    <td><?= $d['stok'] ?></td>
    <td><?= $d['harga_modal'] ?></td>
    <td><?= $d['harga_jual'] ?></td>
    <td>
        <a href="?edit=<?= $d['id_sepatu'] ?>">Edit</a> |
        <a href="?hapus=<?= $d['id_sepatu'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
    </td>
</tr>
<?php } ?>
</table>