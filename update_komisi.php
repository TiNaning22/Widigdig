<?php
include 'koneksi.php'; // Sesuaikan dengan file koneksi Anda

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sales_id = $_POST['sales_id'];
    $komisi = $_POST['komisi'];

    // Query untuk update komisi
    $query = "UPDATE sales SET komisi = ? WHERE sales_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $komisi, $sales_id);

    if ($stmt->execute()) {
        header("Location: data_sales.php?status=success");
    } else {
        header("Location: data_sales.php?status=error");
    }

    $stmt->close();
    $conn->close();
}
?>