<?php
include 'aksi/koneksi.php';
$cabang = $_GET['cabang'];

// Get filter parameters
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$customerId = isset($_GET['customer_id']) && $_GET['customer_id'] !== '' ? $_GET['customer_id'] : null;
$kasirId = isset($_GET['kasir_id']) && $_GET['kasir_id'] !== '' ? $_GET['kasir_id'] : null;

// Format dates for display
$startDateFormatted = date('d-m-Y', strtotime($startDate));
$endDateFormatted = date('d-m-Y', strtotime($endDate));

// Build SQL query
$sql = "SELECT 
          a.invoice_id, 
          a.penjualan_invoice,
          a.invoice_tgl, 
          a.invoice_sub_total, 
          a.invoice_cabang,
          a.invoice_kasir, 
          a.invoice_customer,
          b.customer_nama,
          c.user_nama
        FROM invoice a
        LEFT JOIN user c ON a.invoice_kasir = c.user_id
        LEFT JOIN customer b ON a.invoice_customer = b.customer_id
        WHERE a.invoice_cabang = '$cabang' 
          AND a.invoice_piutang < 1 
          AND a.invoice_draft = 0
          AND DATE(a.invoice_tgl) >= '$startDate' 
          AND DATE(a.invoice_tgl) <= '$endDate'";

// Add customer filter if provided
if ($customerId !== null) {
    $sql .= " AND a.invoice_customer = $customerId";
}

// Add kasir filter if provided
if ($kasirId !== null) {
    $sql .= " AND a.invoice_kasir = $kasirId";
}

$sql .= " ORDER BY a.invoice_tgl DESC";

$data = query($sql);

// Get cabang name
$cabangQuery = query("SELECT nama_cabang FROM cabang WHERE id_cabang = '$cabang'");
$namaCabang = $cabangQuery[0]['nama_cabang'] ?? 'Semua Cabang';

// Get customer and kasir names for filters if selected
$customerName = "Semua Customer";
if ($customerId !== null) {
    $customerQuery = query("SELECT customer_nama FROM customer WHERE customer_id = '$customerId'");
    $customerName = $customerQuery[0]['customer_nama'] ?? 'Tidak Ditemukan';
}

$kasirName = "Semua Kasir";
if ($kasirId !== null) {
    $kasirQuery = query("SELECT user_nama FROM user WHERE user_id = '$kasirId'");
    $kasirName = $kasirQuery[0]['user_nama'] ?? 'Tidak Ditemukan';
}

// Calculate grand total
$grandTotal = 0;
foreach ($data as $row) {
    $grandTotal += $row['invoice_sub_total'];
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Laporan Penjualan Per Nota</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style type="text/css">
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
    }
    .container {
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
    }
    h1 {
        text-align: center;
        font-size: 18px;
        margin-bottom: 5px;
    }
    .subtitle {
        text-align: center;
        font-size: 14px;
        margin-top: 0;
        margin-bottom: 20px;
    }
    .info {
        margin-bottom: 20px;
    }
    .info-item {
        margin-bottom: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    table, th, td {
        border: 1px solid black;
    }
    th, td {
        padding: 5px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    .text-right {
        text-align: right;
    }
    .footer {
        margin-top: 30px;
        text-align: right;
    }
    .total-row {
        font-weight: bold;
    }
    @media print {
        @page {
            size: landscape;
        }
    }
  </style>
</head>
<body onload="window.print()">
    <div class="container">
        <h1>LAPORAN PENJUALAN PER NOTA</h1>
        <p class="subtitle"><?= $namaCabang ?></p>
        
        <div class="info">
            <div class="info-item">Periode: <?= $startDateFormatted ?> s/d <?= $endDateFormatted ?></div>
            <div class="info-item">Customer: <?= $customerName ?></div>
            <div class="info-item">Kasir: <?= $kasirName ?></div>
            <div class="info-item">Tanggal Cetak: <?= date('d-m-Y H:i:s') ?></div>
        </div>
        
        <?php if (empty($data)) : ?>
            <p>Tidak ada data penjualan pada periode yang dipilih.</p>
        <?php else : ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Kasir</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($data as $row) : 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['penjualan_invoice'] ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($row['invoice_tgl'])) ?></td>
                        <td><?= $row['customer_nama'] ?? 'Umum' ?></td>
                        <td><?= $row['user_nama'] ?></td>
                        <td class="text-right">Rp. <?= number_format($row['invoice_sub_total'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="5" class="text-right">Grand Total:</td>
                        <td class="text-right">Rp. <?= number_format($grandTotal, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
        
        <div class="footer">
            <p>Dicetak oleh: <?= isset($_SESSION['userLogin']) ? $_SESSION['userLogin'] : 'System' ?></p>
        </div>
    </div>
</body>
</html>