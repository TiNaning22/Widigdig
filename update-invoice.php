<?php
// Pastikan tidak ada output sebelum ini
ob_start();

include '_header.php';

ob_end_clean();

header('Content-Type: application/json');

// Response default
$response = [
    'status' => 'error',
    'message' => 'Invalid request'
];

try {
    if(isset($_POST['invoice_id'], $_POST['invoice_number'])) {
        $invoiceId = $_POST['invoice_id'];
        $invoiceNumber = trim($_POST['invoice_number']);
        
        // Validasi input
        if(empty($invoiceNumber)) {
            $response['message'] = 'Nomor invoice tidak boleh kosong';
            echo json_encode($response);
            exit;
        }
        
        // Cek duplikat
        $check = mysqli_query($conn, "SELECT * FROM invoice_pembelian_number 
                                    WHERE invoice_pembelian_number_input = '".mysqli_real_escape_string($conn, $invoiceNumber)."' 
                                    AND invoice_pembelian_number_id != '".mysqli_real_escape_string($conn, $invoiceId)."'
                                    AND invoice_pembelian_cabang = '".mysqli_real_escape_string($conn, $sessionCabang)."'");
        
        if(mysqli_num_rows($check) > 0) {
            $response['message'] = 'Nomor invoice sudah digunakan';
            echo json_encode($response);
            exit;
        }
        
        // Update invoice
        $query = "UPDATE invoice_pembelian_number SET 
                  invoice_pembelian_number_input = '".mysqli_real_escape_string($conn, $invoiceNumber)."'";
        
        $query .= " WHERE invoice_pembelian_number_id = '".mysqli_real_escape_string($conn, $invoiceId)."' 
                  AND invoice_pembelian_cabang = '".mysqli_real_escape_string($conn, $sessionCabang)."'";
        
        if(mysqli_query($conn, $query)) {
            $response['status'] = 'success';
            $response['message'] = 'Invoice berhasil diperbarui';
        } else {
            $response['message'] = 'Gagal memperbarui invoice: ' . mysqli_error($conn);
        }
    }
} catch(Exception $e) {
    $response['message'] = 'Exception: ' . $e->getMessage();
}

echo json_encode($response);
?>