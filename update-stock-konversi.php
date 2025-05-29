<?php
// File: update-stock-konversi.php
// Untuk mengupdate stock barang berdasarkan konversi satuan

function updateStockDenganKonversi($conn, $barang_id, $qty_pembelian, $satuan_pilihan, $cabang) {
    try {
        // Ambil data barang
        $query = "SELECT * FROM barang WHERE barang_id = '$barang_id'";
        $result = mysqli_query($conn, $query);
        $barang = mysqli_fetch_array($result);
        
        if (!$barang) {
            return ['status' => 'error', 'message' => 'Barang tidak ditemukan'];
        }
        
        // Hitung konversi qty ke satuan terkecil
        $qty_stock = 0;
        
        switch($satuan_pilihan) {
            case '1': // Satuan terkecil
                $qty_stock = $qty_pembelian;
                break;
            case '2': // Satuan kedua
                $qty_stock = $qty_pembelian * $barang['satuan_isi_1'];
                break;
            case '3': // Satuan ketiga  
                $qty_stock = $qty_pembelian * $barang['satuan_isi_2'];
                break;
            default:
                $qty_stock = $qty_pembelian;
        }
        
        // Update stock barang
        $current_stock = $barang['barang_stock'];
        $new_stock = $current_stock + $qty_stock;
        
        $update_query = "UPDATE barang SET barang_stock = '$new_stock' WHERE barang_id = '$barang_id'";
        $update_result = mysqli_query($conn, $update_query);
        
        if ($update_result) {
            // Log stock movement
            $log_query = "INSERT INTO stock_movement 
                         (barang_id, movement_type, qty_before, qty_change, qty_after, 
                          satuan_pembelian, konversi_qty, cabang, created_date) 
                         VALUES 
                         ('$barang_id', 'PEMBELIAN', '$current_stock', '$qty_stock', '$new_stock',
                          '$satuan_pilihan', '$qty_pembelian', '$cabang', NOW())";
            mysqli_query($conn, $log_query);
            
            return [
                'status' => 'success', 
                'message' => 'Stock berhasil diupdate',
                'qty_konversi' => $qty_stock,
                'stock_before' => $current_stock,
                'stock_after' => $new_stock
            ];
        } else {
            return ['status' => 'error', 'message' => 'Gagal update stock: ' . mysqli_error($conn)];
        }
        
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Contoh penggunaan dalam proses pembelian
if ($_POST && isset($_POST['updateStock'])) {
    // Proses setiap item dalam keranjang
    $barang_ids = $_POST['barang_ids'];
    $keranjang_qtys = $_POST['keranjang_qty'];
    
    for ($i = 0; $i < count($barang_ids); $i++) {
        $barang_id = $barang_ids[$i];
        $qty = $keranjang_qtys[$i];
        
        // Ambil data satuan pilihan dari keranjang
        $keranjang_query = "SELECT keranjang_satuan_pilihan FROM keranjang_pembelian 
                           WHERE barang_id = '$barang_id' AND keranjang_id_kasir = '{$_SESSION['user_id']}'";
        $keranjang_result = mysqli_query($conn, $keranjang_query);
        $keranjang_data = mysqli_fetch_array($keranjang_result);
        
        $satuan_pilihan = $keranjang_data['keranjang_satuan_pilihan'] ?? '1';
        
        // Update stock dengan konversi
        $result = updateStockDenganKonversi($conn, $barang_id, $qty, $satuan_pilihan, $sessionCabang);
        
        if ($result['status'] !== 'success') {
            // Handle error
            echo "Error updating stock for item $barang_id: " . $result['message'];
        }
    }
}
?>