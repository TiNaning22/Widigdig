<?php
include '_header.php';


// Debug: Log received data (PHP 7.0 compatible)
error_log("POST data: " . var_export($_POST, true));

if (!isset($_POST['id']) || empty($_POST['id'])) {
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'error', 'message' => 'ID invoice tidak ditemukan'));
    exit;
}

$invoiceId = $_POST['id'];

// Debug ID yang diterima
error_log("Received ID: " . $invoiceId);
error_log("Session Cabang: " . $sessionCabang);

// Jika ID berupa encoded string, decode dulu
if (!is_numeric($invoiceId)) {
    // Coba decode base64 jika diperlukan
    $decodedId = base64_decode($invoiceId);
    if (is_numeric($decodedId)) {
        $invoiceId = $decodedId;
    }
}

// Sanitize ID
$invoiceId = intval($invoiceId);

if ($invoiceId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'error', 'message' => 'ID invoice tidak valid: ' . $_POST['id']));
    exit;
}

// Query dengan escaping manual (PHP 7.0 style)
$invoiceId_escaped = mysqli_real_escape_string($conn, $invoiceId);
$sessionCabang_escaped = mysqli_real_escape_string($conn, $sessionCabang);

$query = "SELECT * FROM invoice_pembelian_number 
          WHERE invoice_pembelian_number_id = '$invoiceId_escaped' 
          AND invoice_pembelian_cabang = '$sessionCabang_escaped'";

error_log("Query: " . $query);

$result = mysqli_query($conn, $query);

if (!$result) {
    error_log("MySQL Error: " . mysqli_error($conn));
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'error', 'message' => 'Database error'));
    exit;
}

$data = mysqli_fetch_assoc($result);

if($data) {
    error_log("Data found: " . var_export($data, true));
    echo '
    <div class="form-group">
        <label>No. Invoice</label>
        <input type="text" class="form-control" name="invoice_number" 
               value="'.htmlspecialchars($data['invoice_pembelian_number_input'], ENT_QUOTES, 'UTF-8').'" required>
        <input type="hidden" name="invoice_id" value="'.$invoiceId.'">
    </div>
    <div class="form-group">
        <label>Tanggal</label>
        <input type="datetime-local" class="form-control" name="invoice_date" 
               value="'.date('Y-m-d\TH:i').'" readonly>
    </div>';
} else {
    error_log("No data found for ID: " . $invoiceId . " and Cabang: " . $sessionCabang);
    echo '<div class="alert alert-danger">Data invoice tidak ditemukan untuk ID: '.$invoiceId.' di cabang: '.$sessionCabang.'</div>';
}
?>