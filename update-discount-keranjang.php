<?php
include '_header-artibut.php';

$keranjang_id = $_POST['keranjang_id'] ?? 0;
$diskon = $_POST['diskon'] ?? 0;

// Validasi input
$keranjang_id = (int)$keranjang_id;
$diskon = (float)$diskon;

// Pastikan diskon antara 0-100
$diskon = max(0, min(100, $diskon));

// Update database
$query = "UPDATE keranjang_pembelian SET keranjang_diskon = ? WHERE keranjang_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("di", $diskon, $keranjang_id);
$stmt->execute();

echo json_encode(['status' => 'success']);
?>