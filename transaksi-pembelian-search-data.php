<?php
include '_header.php';

$requestData = $_REQUEST;
$columns = array( 
    0 => 'barang_kode',
    1 => 'barang_nama',
    2 => 'satuan_1',
    3 => 'barang_harga',
    4 => 'barang_stock'
);

// Query dasar
$query = "SELECT b.*, s1.satuan_nama as satuan_1 
          FROM barang b
          LEFT JOIN satuan s1 ON b.satuan_id = s1.satuan_id
          WHERE b.barang_cabang = '".$sessionCabang."'";

// Total data tanpa filter
$totalData = mysqli_num_rows(mysqli_query($conn, $query));
$totalFiltered = $totalData;

// Pencarian
if (!empty($requestData['search']['value'])) {
    $query .= " AND (b.barang_kode LIKE '%".$requestData['search']['value']."%' ";
    $query .= " OR b.barang_nama LIKE '%".$requestData['search']['value']."%')";
    $totalFiltered = mysqli_num_rows(mysqli_query($conn, $query));
}

// Pengurutan
$query .= " ORDER BY ".$columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." 
            LIMIT ".$requestData['start']." ,".$requestData['length']." ";

$result = mysqli_query($conn, $query);
$data = array();

while ($row = mysqli_fetch_array($result)) {
    $nestedData = array(); 
    $nestedData[] = $row["barang_kode"];
    $nestedData[] = $row["barang_nama"];
    $nestedData[] = $row["satuan_1"];
    $nestedData[] = $row["barang_harga"];
    $nestedData[] = $row["barang_stock"];
    $nestedData[] = ""; // Kolom aksi
    
    $data[] = $nestedData;
}

$json_data = array(
    "draw"            => intval($requestData['draw']),  
    "recordsTotal"    => intval($totalData),  
    "recordsFiltered" => intval($totalFiltered), 
    "data"            => $data   
);

echo json_encode($json_data);
?>