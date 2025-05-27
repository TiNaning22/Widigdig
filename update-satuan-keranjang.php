<?php
session_start();
include 'config/koneksi.php'; // Sesuaikan dengan file koneksi database Anda

if ($_POST) {
    $keranjang_id = $_POST['keranjang_id'];
    $satuan_beli = intval($_POST['satuan_beli']);
    
    // Ambil data barang dari keranjang untuk mendapatkan barang_id
    $keranjangQuery = "SELECT barang_id FROM keranjang_pembelian WHERE keranjang_id = ?";
    $stmt = mysqli_prepare($conn, $keranjangQuery);
    mysqli_stmt_bind_param($stmt, "i", $keranjang_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $keranjangData = mysqli_fetch_assoc($result);
    $barang_id = $keranjangData['barang_id'];
    mysqli_stmt_close($stmt);
    
    // Ambil data barang untuk mendapatkan konversi
    $barangQuery = "SELECT b.*, s1.satuan_nama as satuan_1, s2.satuan_nama as satuan_2, s3.satuan_nama as satuan_3 
                    FROM barang b
                    LEFT JOIN satuan s1 ON b.satuan_id = s1.satuan_id
                    LEFT JOIN satuan s2 ON b.satuan_id_2 = s2.satuan_id
                    LEFT JOIN satuan s3 ON b.satuan_id_3 = s3.satuan_id
                    WHERE b.barang_id = ?";
    $stmt = mysqli_prepare($conn, $barangQuery);
    mysqli_stmt_bind_param($stmt, "i", $barang_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $barang = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    // Tentukan konversi berdasarkan satuan yang dipilih
    $konversi = 1;
    $satuan_nama = $barang['satuan_1'];
    
    switch ($satuan_beli) {
        case 2:
            $konversi = $barang['satuan_isi_1'];
            $satuan_nama = $barang['satuan_2'];
            break;
        case 3:
            $konversi = $barang['satuan_isi_2'];
            $satuan_nama = $barang['satuan_3'];
            break;
        default:
            $konversi = 1;
            $satuan_nama = $barang['satuan_1'];
    }
    
    // Update satuan beli dan konversi di tabel keranjang_pembelian
    $updateQuery = "UPDATE keranjang_pembelian SET keranjang_satuan_beli = ?, keranjang_konversi = ? WHERE keranjang_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "idi", $satuan_beli, $konversi, $keranjang_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode([
            'status' => 'success', 
            'konversi' => $konversi,
            'satuan_produk' => $barang['satuan_1'],
            'message' => 'Satuan berhasil diupdate'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal update satuan']);
    }
    
    mysqli_stmt_close($stmt);
}
?>