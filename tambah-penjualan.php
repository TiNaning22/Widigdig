<?php
include '_header.php';

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Get form data for main penjualan_sales (sales) record
    $penjualan_date = htmlspecialchars($_POST['penjualan_date']);
    $penjualan_invoice = htmlspecialchars($_POST['penjualan_invoice']);
    $penjualan_pelanggan = htmlspecialchars($_POST['penjualan_pelanggan']);
    $penjualan_sales_id = !empty($_POST['penjualan_sales_id']) ? htmlspecialchars($_POST['penjualan_sales_id']) : NULL;
    $penjualan_tipe_pembayaran = htmlspecialchars($_POST['penjualan_tipe_pembayaran']);
    $penjualan_keterangan = htmlspecialchars($_POST['penjualan_keterangan']);
    $penjualan_total = htmlspecialchars($_POST['penjualan_total']);
    $penjualan_bayar = htmlspecialchars($_POST['penjualan_bayar']);
    $penjualan_kembali = htmlspecialchars($_POST['penjualan_kembali']);
    $penjualan_status = 1;
    $penjualan_cabang = $sessionCabang;
    $penjualan_user = $user_id;
    $penjualan_created = date('Y-m-d H:i:s');

    $penjualan_status_lunas = ($penjualan_tipe_pembayaran == "kredit") ? 0 : 1;

    if ($penjualan_tipe_pembayaran != "kredit" && $penjualan_bayar < $penjualan_total) {
        echo "<script>alert('Pembayaran tidak mencukupi!'); window.history.back();</script>";
        exit;
    }

    mysqli_autocommit($conn, false);
    $transaction_status = true;

    try {
        // Insert into penjualan_sales (sales) table
        $query_penjualan = "INSERT INTO penjualan_sales 
                           (penjualan_invoice, 
                            penjualan_date, 
                            penjualan_pelanggan, 
                            penjualan_sales_id, 
                            penjualan_tipe_pembayaran, 
                            penjualan_keterangan, 
                            penjualan_total, 
                            penjualan_bayar, 
                            penjualan_kembali, 
                            penjualan_status, 
                            penjualan_status_lunas, 
                            penjualan_cabang, 
                            penjualan_user, 
                            penjualan_created) 
                           VALUES 
                           ('$penjualan_invoice', 
                            '$penjualan_date', 
                            '$penjualan_pelanggan', 
                            ".($penjualan_sales_id ? "'$penjualan_sales_id'" : "NULL").", 
                            '$penjualan_tipe_pembayaran', 
                            '$penjualan_keterangan', 
                            '$penjualan_total', 
                            '$penjualan_bayar', 
                            '$penjualan_kembali', 
                            '$penjualan_status', 
                            '$penjualan_status_lunas', 
                            '$penjualan_cabang', 
                            '$penjualan_user', 
                            '$penjualan_created')";
        
        $result_penjualan = mysqli_query($conn, $query_penjualan);

        if ($result_penjualan) {
            mysqli_commit($conn);
            echo "<script>alert('Data penjualan berhasil ditambahkan!'); document.location.href = 'penjualan-baru';</script>";
        } else {
            mysqli_rollback($conn);
            echo "<script>alert('Data penjualan gagal ditambahkan!'); window.history.back();</script>";
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
    }

    mysqli_autocommit($conn, true);
} else {
    echo "<script>document.location.href = 'penjualan-sales-tambah';</script>";
}
?>
