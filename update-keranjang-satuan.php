<?php
// File: update-keranjang-satuan.php
include 'aksi/koneksi.php'; // Sesuaikan dengan file koneksi database Anda

if ($_POST) {
    $keranjang_id = $_POST['keranjang_id'];
    $satuan_pilihan = $_POST['satuan_pilihan'];
    $harga_konversi = $_POST['harga_konversi'];
    $konversi_value = $_POST['konversi_value'];
    
    try {
        // Update keranjang dengan harga dan satuan baru
        $query = "UPDATE keranjang_pembelian SET 
                    keranjang_harga = '$harga_konversi',
                    keranjang_satuan_pilihan = '$satuan_pilihan',
                    keranjang_konversi_value = '$konversi_value'
                  WHERE keranjang_id = '$keranjang_id'";
        
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Keranjang berhasil diupdate'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal update keranjang: ' . mysqli_error($conn)
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}
?>