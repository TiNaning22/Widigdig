<?php
include 'aksi/koneksi.php';

$keranjangId = $_POST['keranjang_id'];
$diskon = $_POST['diskon'];

// Update diskon di keranjang
$updateQuery = "UPDATE keranjang SET 
                keranjang_diskon_persen = '$diskon'
                WHERE keranjang_id = '$keranjangId'";
mysqli_query($conn, $updateQuery);

// Kembalikan response JSON
echo json_encode(['success' => true]);
?>