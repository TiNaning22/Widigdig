<?php
session_start();
include 'config/koneksi.php'; // Sesuaikan dengan file koneksi database Anda

if ($_POST) {
    $keranjang_id = $_POST['keranjang_id'];
    $discount_persen = floatval($_POST['discount_persen']);
    
    // Validasi input
    if ($discount_persen < 0 || $discount_persen > 100) {
        echo json_encode(['status' => 'error', 'message' => 'Discount harus antara 0-100%']);
        exit;
    }
    
    // Update discount di tabel keranjang_pembelian
    $updateQuery = "UPDATE keranjang_pembelian SET keranjang_discount = ? WHERE keranjang_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "di", $discount_persen, $keranjang_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Discount berhasil diupdate']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal update discount']);
    }
    
    mysqli_stmt_close($stmt);
}
?>