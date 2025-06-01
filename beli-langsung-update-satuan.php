<?php
include '_config.php';
include 'aksi/koneksi';

$keranjangId = $_POST['keranjang_id'];
$barangId = $_POST['barang_id'];
$satuanId = $_POST['satuan_id'];

// Ambil data konversi satuan dari database
$query = "SELECT * FROM barang_satuan WHERE barang_id = '$barangId' AND satuan_id = '$satuanId'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Update keranjang
$updateQuery = "UPDATE keranjang SET 
                keranjang_satuan = '$satuanId',
                keranjang_qty = '".$data['konversi_jumlah']."',
                keranjang_qty_view = '1',
                keranjang_konversi_isi = '".$data['konversi_jumlah']."'
                WHERE keranjang_id = '$keranjangId'";
mysqli_query($conn, $updateQuery);

echo "success";
?>