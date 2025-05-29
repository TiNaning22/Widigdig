<?php
// File: get-harga-satuan.php
include 'aksi/koneksi.php'; // Sesuaikan dengan file koneksi database Anda

if ($_POST) {
    $barang_id = $_POST['barang_id'];
    $satuan_pilihan = $_POST['satuan_pilihan'];
    $konversi = $_POST['konversi'];
    
    try {
        // Ambil data barang
        $query = "SELECT * FROM barang WHERE barang_id = '$barang_id'";
        $result = mysqli_query($conn, $query);
        $barang = mysqli_fetch_array($result);
        
        if (!$barang) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Barang tidak ditemukan'
            ]);
            exit;
        }
        
        // Hitung harga berdasarkan satuan yang dipilih
        $harga_beli_dasar = $barang['barang_harga_beli']; // Harga beli dalam satuan terkecil
        $harga_konversi = 0;
        
        switch($satuan_pilihan) {
            case '1': // Satuan terkecil
                $harga_konversi = $harga_beli_dasar;
                break;
            case '2': // Satuan kedua
                $harga_konversi = $harga_beli_dasar * $barang['satuan_isi_1'];
                break;
            case '3': // Satuan ketiga
                $harga_konversi = $harga_beli_dasar * $barang['satuan_isi_2'];
                break;
            default:
                $harga_konversi = $harga_beli_dasar;
        }
        
        echo json_encode([
            'status' => 'success',
            'harga_konversi' => $harga_konversi,
            'harga_beli_dasar' => $harga_beli_dasar,
            'konversi_value' => $konversi,
            'satuan_pilihan' => $satuan_pilihan
        ]);
        
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