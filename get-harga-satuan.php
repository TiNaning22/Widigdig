<?php
// get-harga-satuan.php
include 'aksi/koneksi.php'; // Sesuaikan dengan file koneksi database Anda

// Cek apakah koneksi berhasil
if (!isset($conn) || $conn === null) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi database gagal',
        'harga_konversi' => 0
    ]);
    exit;
}

if ($_POST) {
    $barang_id = intval($_POST['barang_id']);
    $satuan_pilihan = $_POST['satuan_pilihan'];
    $konversi = floatval($_POST['konversi']);
    
    try {
        // Query untuk mendapatkan harga barang
        $query = "SELECT barang_harga_beli FROM barang WHERE barang_id = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $koneksi->error);
        }
        
        $stmt->bind_param("i", $barang_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $harga_dasar = floatval($row['barang_harga_beli']) ?: 0; // Default 0 jika null
            
            // Hitung harga berdasarkan konversi satuan
            // Jika konversi > 1, maka harga per satuan kecil
            // Jika konversi = 1, maka harga tetap
            $harga_konversi = ($konversi > 0) ? $harga_dasar / $konversi : $harga_dasar;
            
            echo json_encode([
                'status' => 'success',
                'harga_dasar' => $harga_dasar,
                'harga_konversi' => $harga_konversi,
                'konversi' => $konversi,
                'satuan_pilihan' => $satuan_pilihan,
                'barang_id' => $barang_id
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Barang tidak ditemukan',
                'harga_konversi' => 0
            ]);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage(),
            'harga_konversi' => 0
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Tidak ada data yang dikirim',
        'harga_konversi' => 0
    ]);
}

// Tutup koneksi jika masih terbuka
if (isset($koneksi) && $koneksi !== null) {
    $koneksi->close();
}
?>