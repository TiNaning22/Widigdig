<?php
session_start();
include 'config.php';
include 'function.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_nama'])) {
    header("location: index.php");
    exit();
}

$sessionCabang = isset($_GET['cabang']) ? $_GET['cabang'] : '';

// Sanitize inputs
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$supplierId = isset($_GET['supplier_id']) ? $_GET['supplier_id'] : '';
$userId = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// DataTables parameters
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

// Order parameters
$orderColumn = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 2; // Default to date column
$orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'desc'; // Default to latest first

// Column names for ordering
$columns = [
    0 => 'row_number', // Not a real column
    1 => 'pembelian_faktur',
    2 => 'pembelian_tanggal',
    3 => 'supplier_nama',
    4 => 'user_nama',
    5 => 'pembelian_total'
];

// Get the column name for ordering
$orderColumnName = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'pembelian_tanggal';

// Base query
$sqlBase = "FROM pembelian 
            INNER JOIN supplier ON pembelian.pembelian_supplier = supplier.supplier_id
            INNER JOIN user ON pembelian.pembelian_user = user.user_id
            WHERE pembelian.pembelian_cabang = '$sessionCabang'
            AND DATE(pembelian.pembelian_tanggal) BETWEEN '$startDate' AND '$endDate'";

// Add filters if provided
if (!empty($supplierId)) {
    $sqlBase .= " AND pembelian.pembelian_supplier = '$supplierId'";
}

if (!empty($userId)) {
    $sqlBase .= " AND pembelian.pembelian_user = '$userId'";
}

// Add search if provided
if (!empty($search)) {
    $sqlBase .= " AND (
        pembelian.pembelian_faktur LIKE '%$search%' OR
        supplier.supplier_nama LIKE '%$search%' OR
        user.user_nama LIKE '%$search%'
    )";
}

// Count total records
$sqlCount = "SELECT COUNT(*) as total " . $sqlBase;
$totalResult = mysqli_query($conn, $sqlCount);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];

// Count filtered records (same as total if no search)
$filteredRecords = $totalRecords;

// Fetch data with ordering and pagination
$sqlData = "SELECT 
            pembelian.pembelian_id,
            pembelian.pembelian_faktur,
            DATE_FORMAT(pembelian.pembelian_tanggal, '%d/%m/%Y %H:%i') as pembelian_tanggal,
            supplier.supplier_nama,
            user.user_nama,
            pembelian.pembelian_total
            " . $sqlBase . "
            ORDER BY " . $orderColumnName . " " . $orderDir . "
            LIMIT " . $start . ", " . $length;

$dataResult = mysqli_query($conn, $sqlData);

// Format data for DataTables
$data = [];
while ($row = mysqli_fetch_assoc($dataResult)) {
    $data[] = [
        "", // Row number will be added by JavaScript
        $row['pembelian_faktur'],
        $row['pembelian_tanggal'],
        $row['supplier_nama'],
        $row['user_nama'],
        $row['pembelian_total'],
        "" // Action buttons will be added by JavaScript
    ];
}

// Prepare response for DataTables
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
];

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>