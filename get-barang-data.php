<?php
include 'aksi/koneksi.php';

$cabang = isset($_GET['cabang']) ? intval($_GET['cabang']) : 0;

$query = "SELECT 
            a.barang_id, 
            a.barang_kode,
            a.barang_nama,
            a.barang_harga,
            a.barang_stock,
            b.kategori_nama
          FROM barang a
          LEFT JOIN kategori b ON a.barang_kategori_id = b.kategori_id
          WHERE a.barang_cabang = $cabang
          ORDER BY a.barang_nama ASC";

$result = mysqli_query($conn, $query);
$data = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>