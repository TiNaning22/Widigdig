<?php
session_start();
include 'aksi/koneksi.php';

header('Content-Type: application/json');

if ($_POST) {
    $keranjang_id = mysqli_real_escape_string($conn, $_POST['keranjang_id']);
    $satuan_pilihan = mysqli_real_escape_string($conn, $_POST['satuan_pilihan']);
    $harga_satuan = mysqli_real_escape_string($conn, $_POST['harga_satuan']);
    $konversi = mysqli_real_escape_string($conn, $_POST['konversi']);
    
    // Update keranjang dengan harga satuan yang sudah benar
    $query = "UPDATE keranjang_pembelian SET 
              keranjang_harga = '$harga_satuan',
              keranjang_satuan_pilihan = '$satuan_pilihan'
              WHERE keranjang_id = '$keranjang_id' 
              AND keranjang_id_kasir = '".$_SESSION['user_id']."'";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Satuan berhasil diupdate',
            'harga_baru' => $harga_satuan
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Gagal update satuan: ' . mysqli_error($conn)
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
