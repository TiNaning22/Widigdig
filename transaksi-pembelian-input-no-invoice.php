<?php
ob_start();

include '_header.php';

ob_end_clean();

header('Content-Type: application/json');

try {
    $data = [
        'invoice_pembelian_number_input' => $_POST['invoice_pembelian_number_input'],
        'invoice_pembelian_number_parent' => $_POST['invoice_pembelian_number_parent'],
        'invoice_pembelian_number_user' => $_POST['invoice_pembelian_number_user'],
        'invoice_pembelian_cabang' => $_POST['invoice_pembelian_cabang'],
        'invoice_pembelian_number_delete' => $_POST['invoice_pembelian_number_parent']
    ];

    // Cek duplikat
    $cek = mysqli_query($conn, "SELECT * FROM invoice_pembelian_number 
                              WHERE invoice_pembelian_number_input = '".$data['invoice_pembelian_number_input']."' 
                              AND invoice_pembelian_cabang = '".$data['invoice_pembelian_cabang']."'");
    
    if(mysqli_num_rows($cek) > 0) {
        echo json_encode(['hasil' => 'duplikat']);
        exit;
    }

    // Insert invoice
    $query = "INSERT INTO invoice_pembelian_number SET 
              invoice_pembelian_number_input = '".mysqli_real_escape_string($conn, $data['invoice_pembelian_number_input'])."',
              invoice_pembelian_number_parent = '".mysqli_real_escape_string($conn, $data['invoice_pembelian_number_parent'])."',
              invoice_pembelian_number_user = '".mysqli_real_escape_string($conn, $data['invoice_pembelian_number_user'])."',
              invoice_pembelian_cabang = '".mysqli_real_escape_string($conn, $data['invoice_pembelian_cabang'])."',
              invoice_pembelian_number_delete = '".mysqli_real_escape_string($conn, $data['invoice_pembelian_number_delete'])."'";

    if(mysqli_query($conn, $query)) {
        echo json_encode(['hasil' => 'sukses']);
    } else {
        echo json_encode(['hasil' => 'gagal', 'error' => mysqli_error($conn)]);
    }
} catch(Exception $e) {
    echo json_encode(['hasil' => 'error', 'message' => $e->getMessage()]);
}
?>