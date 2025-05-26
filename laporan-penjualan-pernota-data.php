<?php 
include 'aksi/koneksi.php';

// Ambil parameter
$cabang = $_GET['cabang'];
$filter_active = isset($_GET['filter']) ? $_GET['filter'] : 'false';

// Buat filter dasar
$filter = "invoice_cabang = $cabang && invoice_piutang < 1 && invoice_draft = 0";

// Tambahkan filter tanggal dan customer jika aktif
if ($filter_active == 'true') {
    $tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-d');
    $tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
    
    $filter .= " && DATE(invoice_tgl) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
    
    if (isset($_GET['customer_id']) && !empty($_GET['customer_id'])) {
        $customer_id = $_GET['customer_id'];
        $filter .= " && invoice_customer = '$customer_id'";
    }
}

// Database connection info 
$dbDetails = array( 
    'host' => $servername, 
    'user' => $username, 
    'pass' => $password, 
    'db'   => $db
); 
 
// DB table to use 
$table = <<<EOT
 (
    SELECT 
      a.invoice_id, 
      a.penjualan_invoice,
      a.invoice_tgl, 
      a.invoice_sub_total, 
      a.invoice_cabang, 
      a.invoice_kasir, 
      a.invoice_customer,
      a.invoice_piutang,
      a.invoice_draft,
      b.customer_id,
      b.customer_nama,
      c.user_id,
      c.user_nama
    FROM invoice a
    LEFT JOIN user c ON a.invoice_kasir = c.user_id
    LEFT JOIN customer b ON a.invoice_customer = b.customer_id
 ) temp
EOT;
 
// Table's primary key 
$primaryKey = 'invoice_id'; 
 
// Array of database columns which should be read and sent back to DataTables. 
// The `db` parameter represents the column name in the database.  
// The `dt` parameter represents the DataTables column identifier. 
$columns = array( 
    array( 'db' => 'invoice_id', 'dt'         => 0 ),
    array( 'db' => 'penjualan_invoice', 'dt'  => 1 ), 
    array( 'db' => 'invoice_tgl',  'dt'       => 2 ), 
    array( 'db' => 'customer_nama',      'dt' => 3 ),
    array( 'db' => 'user_nama',      'dt'     => 4 ), 
    array( 'db' => 'invoice_sub_total',  'dt' => 5 )
); 

// Include SQL query processing class 
require 'aksi/ssp.php'; 

// Output data as json format 
echo json_encode( 
    SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns, null, $filter )
);
?>