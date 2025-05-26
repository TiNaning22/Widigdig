<?php
// Database connection
include 'aksi/koneksi.php';

// Initialize variables for DataTables server-side processing
$draw = isset($_POST['draw']) ? $_POST['draw'] : 1;
$row = isset($_POST['start']) ? $_POST['start'] : 0;
$rowperpage = isset($_POST['length']) ? $_POST['length'] : 10;
$columnIndex = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
$columnName = isset($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : 'id';
$columnSortOrder = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'desc';
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

$cabang = $_GET['cabang'];

// Total number of records without filtering
$sql = mysqli_query($conn, "SELECT COUNT(*) as total FROM arus_stock WHERE cabang = '$cabang'");
$records = mysqli_fetch_assoc($sql);
$totalRecords = $records['total'];

// Total number of records with filtering
$whereCondition = $searchValue != '' ? "AND (nama_kas LIKE '%$searchValue%' OR jenis_kas LIKE '%$searchValue%' OR keterangan LIKE '%$searchValue%')" : '';
$sql = mysqli_query($conn, "SELECT COUNT(*) as total FROM arus_stock WHERE cabang = '$cabang' $whereCondition");
$records = mysqli_fetch_assoc($sql);
$totalRecordwithFilter = $records['total'];

// Fetch records with sorting and filtering
$query = "SELECT * FROM arus_stock 
          WHERE cabang = '$cabang' 
          $whereCondition
          ORDER BY tanggal $columnSortOrder 
          LIMIT $row, $rowperpage";
$sql = mysqli_query($conn, $query);
$data = array();

while ($row = mysqli_fetch_assoc($sql)) {
    $subdata = array();
    $subdata[] = $row['id'];
    $subdata[] = date('d-m-Y', strtotime($row['tanggal']));
    $subdata[] = $row['nama_kas'];
    $subdata[] = ucfirst($row['jenis_kas']);
    $subdata[] = $row['nominal'];
    $subdata[] = $row['keterangan'];
    $subdata[] = '';
    $data[] = $subdata;
}

// Response
$response = array(
    "draw" => intval($draw),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordwithFilter,
    "data" => $data
);

echo json_encode($response);
?>