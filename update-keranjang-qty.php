<?php
// update-keranjang-qty.php
include 'aksi/koneksi.php'; // Sesuaikan dengan file koneksi database Anda

if ($_POST) {
    $keranjang_id = intval($_POST['keranjang_id']);
    $qty = floatval($_POST['qty']);
    $qty_stock = floatval($_POST['qty_stock']);
    $konversi = floatval($_POST['konversi']);
    
    // Update quantity di tabel keranjang_pembelian
    $query = "UPDATE keranjang_pembelian SET 
              keranjang_qty = ?,
              keranjang_qty_stock = ?,
              keranjang_konversi = ?
              WHERE keranjang_id = ?";
    
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("dddi", $qty, $qty_stock, $konversi, $keranjang_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Quantity berhasil diupdate',
            'keranjang_id' => $keranjang_id,
            'qty' => $qty,
            'qty_stock' => $qty_stock
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal update quantity: ' . $stmt->error
        ]);
    }
    
    $stmt->close();
}
?>