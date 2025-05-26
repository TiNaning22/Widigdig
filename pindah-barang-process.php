<?php
require_once 'config/config.php';
require_once 'config/function.php';
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    $response = array(
        'status' => 'error',
        'message' => 'Anda harus login terlebih dahulu!'
    );
    echo json_encode($response);
    exit;
}

// Debug: Log request method and data
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . print_r($_POST, true));

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = array(
        'status' => 'error',
        'message' => 'Method tidak diizinkan!'
    );
    echo json_encode($response);
    exit;
}

// Validasi input
$required_fields = ['barang_id', 'cabang_asal', 'cabang_tujuan', 'jumlah'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $response = array(
            'status' => 'error',
            'message' => 'Parameter ' . $field . ' tidak boleh kosong!'
        );
        echo json_encode($response);
        exit;
    }
}

$barangId = intval($_POST['barang_id']);
$cabangAsal = intval($_POST['cabang_asal']);
$cabangTujuan = intval($_POST['cabang_tujuan']);
$jumlah = intval($_POST['jumlah']);
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';
$username = $_SESSION['username'];
$tanggal = date('Y-m-d H:i:s');

// Debug: Log the processed data
error_log("Processed data: barangId=$barangId, cabangAsal=$cabangAsal, cabangTujuan=$cabangTujuan, jumlah=$jumlah");

try {
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    // Generate nomor referensi
    $prefix = "PB";
    $date = date('Ymd');
    $query = "SELECT MAX(SUBSTRING(no_referensi, 10)) as max_number FROM perpindahan_barang WHERE no_referensi LIKE '$prefix$date%'";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Error executing query: " . mysqli_error($conn));
    }
    
    $row = mysqli_fetch_assoc($result);
    $next_number = isset($row['max_number']) && !empty($row['max_number']) ? intval($row['max_number']) + 1 : 1;
    $no_referensi = $prefix . $date . sprintf("%04d", $next_number);
    
    // Debug: Log the reference number
    error_log("Generated reference number: $no_referensi");
    
    // Cek stok di cabang asal
    $query = "SELECT barang_kode, barang_nama, barang_stock FROM barang_internal WHERE barang_id = ? AND barang_cabang = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $barangId, $cabangAsal);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $barang = mysqli_fetch_assoc($result);
    
    if (!$barang) {
        throw new Exception("Barang tidak ditemukan di cabang asal!");
    }
    
    // Debug: Log the stock info
    error_log("Current stock: " . $barang['barang_stock'] . ", Requested: $jumlah");
    
    if ($barang['barang_stock'] < $jumlah) {
        throw new Exception("Stok barang tidak mencukupi! Stok tersedia: " . $barang['barang_stock']);
    }
    
    // Cek apakah barang sudah ada di cabang tujuan
    $query = "SELECT barang_id FROM barang_internal WHERE barang_kode = ? AND barang_cabang = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "si", $barang['barang_kode'], $cabangTujuan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $barangTujuan = mysqli_fetch_assoc($result);
    
    // Dapatkan nama cabang untuk log
    $query = "SELECT toko_nama FROM toko WHERE toko_cabang = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $cabangAsal);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $tokoAsal = mysqli_fetch_assoc($result);
    $namaCabangAsal = $tokoAsal ? $tokoAsal['toko_nama'] : 'Cabang '.$cabangAsal;
    
    $query = "SELECT toko_nama FROM toko WHERE toko_cabang = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $cabangTujuan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $tokoTujuan = mysqli_fetch_assoc($result);
    $namaCabangTujuan = $tokoTujuan ? $tokoTujuan['toko_nama'] : 'Cabang '.$cabangTujuan;
    
    // Debug: Log the branch names
    error_log("Branch names: Source=$namaCabangAsal, Destination=$namaCabangTujuan");
    
    // Insert ke tabel perpindahan_barang
    $query = "INSERT INTO perpindahan_barang (no_referensi, tanggal, barang_id, jumlah, cabang_asal, cabang_tujuan, keterangan, created_by, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "ssiiissss", $no_referensi, $tanggal, $barangId, $jumlah, $cabangAsal, $cabangTujuan, $keterangan, $username, $tanggal);
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        throw new Exception("Error executing statement: " . mysqli_stmt_error($stmt));
    }
    
    // Kurangi stok di cabang asal
    $query = "UPDATE barang_internal SET barang_stock = barang_stock - ? WHERE barang_id = ? AND barang_cabang = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "iii", $jumlah, $barangId, $cabangAsal);
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        throw new Exception("Error executing statement: " . mysqli_stmt_error($stmt));
    }
    
    // Catat log stok keluar
    $logKeterangan = "Perpindahan barang ke $namaCabangTujuan (Ref: $no_referensi)";
    $query = "INSERT INTO log_stock (barang_id, jumlah, jenis, keterangan, created_by, created_at) 
              VALUES (?, ?, 'keluar', ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "iisss", $barangId, $jumlah, $logKeterangan, $username, $tanggal);
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        throw new Exception("Error executing statement: " . mysqli_stmt_error($stmt));
    }
    
    // Jika barang sudah ada di cabang tujuan, tambah stok
    if ($barangTujuan) {
        $query = "UPDATE barang_internal SET barang_stock = barang_stock + ? WHERE barang_kode = ? AND barang_cabang = ?";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "isi", $jumlah, $barang['barang_kode'], $cabangTujuan);
        $result = mysqli_stmt_execute($stmt);
        if (!$result) {
            throw new Exception("Error executing statement: " . mysqli_stmt_error($stmt));
        }
        
        // Ambil barang_id di cabang tujuan untuk log
        $barangIdTujuan = $barangTujuan['barang_id'];
    } else {
        // Jika barang belum ada di cabang tujuan, buat baru
        $query = "SELECT * FROM barang_internal WHERE barang_id = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "i", $barangId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $barangData = mysqli_fetch_assoc($result);
        
        $query = "INSERT INTO barang_internal (barang_kode, barang_nama, barang_deskripsi, barang_harga, barang_stock, 
                  barang_kategori_id, barang_satuan_id, barang_cabang, created_by, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "sssdiissss", 
            $barangData['barang_kode'], 
            $barangData['barang_nama'], 
            $barangData['barang_deskripsi'], 
            $barangData['barang_harga'], 
            $jumlah, 
            $barangData['barang_kategori_id'], 
            $barangData['barang_satuan_id'], 
            $cabangTujuan, 
            $username, 
            $tanggal
        );
        $result = mysqli_stmt_execute($stmt);
        if (!$result) {
            throw new Exception("Error executing statement: " . mysqli_stmt_error($stmt));
        }
        
        // Ambil barang_id yang baru dibuat untuk log
        $barangIdTujuan = mysqli_insert_id($conn);
    }
    
    // Catat log stok masuk di cabang tujuan
    $logKeterangan = "Penerimaan barang dari $namaCabangAsal (Ref: $no_referensi)";
    $query = "INSERT INTO log_stock (barang_id, jumlah, jenis, keterangan, created_by, created_at) 
              VALUES (?, ?, 'masuk', ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iisss", $barangIdTujuan, $jumlah, $logKeterangan, $username, $tanggal);
    mysqli_stmt_execute($stmt);
    
    // Commit transaction
    mysqli_commit($conn);
    
    $response = array(
        'status' => 'success',
        'message' => 'Perpindahan barang berhasil dilakukan dengan nomor referensi: ' . $no_referensi,
        'no_referensi' => $no_referensi
    );
    
} catch (Exception $e) {
    // Rollback transaction jika terjadi error
    mysqli_rollback($conn);
    
    $response = array(
        'status' => 'error',
        'message' => $e->getMessage()
    );
}

echo json_encode($response);
exit;
?>