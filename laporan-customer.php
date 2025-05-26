<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Laporan Customer</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="bo">Home</a></li>
                        <li class="breadcrumb-item active">Laporan Customer</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Filter Data Berdasarkan Tanggal</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
                    </div>
                </div>
                <!-- /.card-header -->
                <form role="form" action="" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tanggal_awal">Tanggal Awal</label>
                                    <input type="date" name="tanggal_awal" class="form-control" id="tanggal_awal" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tanggal_akhir">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="form-control" id="tanggal_akhir" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customer_id">Nama Customer</label>
                                    <select class="form-control select2bs4" required="" name="customer_id">
                                        <option selected="selected" value="-1">-- Semua Customer --</option>
                                        <option value="0">Umum</option>
                                        <?php  
                                          $customer = query("SELECT * FROM customer WHERE customer_cabang = $sessionCabang && customer_status = '1' ORDER BY customer_id DESC ");
                                          foreach ($customer as $ctr) :
                                              if ($ctr['customer_id'] > 1 && $ctr['customer_nama'] != "Customer Umum") { 
                                        ?>
                                        <option value="<?= $ctr['customer_id'] ?>"><?= $ctr['customer_nama'] ?></option>
                                        <?php } endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
    </section>

    <?php if (isset($_POST["submit"])) { ?>
        <?php  
            $tanggal_awal  = $_POST['tanggal_awal'];
            $tanggal_akhir = $_POST['tanggal_akhir'];
            $customer_id   = $_POST['customer_id'];

            $queryInvoice = "SELECT invoice.invoice_id, invoice.penjualan_invoice, invoice.invoice_tgl, 
                                    customer.customer_id, customer.customer_nama, 
                                    invoice.invoice_total, invoice.invoice_sub_total
                             FROM invoice 
                             JOIN customer ON invoice.invoice_customer = customer.customer_id
                             WHERE invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' 
                             AND invoice_piutang < 1 
                             AND invoice_cabang = $sessionCabang";

            if ($customer_id != '-1') {
                $queryInvoice .= " AND customer.customer_id = '$customer_id'";
            }

            $queryInvoice .= " ORDER BY invoice_id DESC";
            $resultInvoice = $conn->query($queryInvoice);
        ?>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Laporan Customer</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-auto">
                                <table id="laporan-customer" class="table table-bordered table-striped table-laporan">
                                    <thead>
                                        <tr>
                                            <th style="width: 6%;">No.</th>
                                            <th style="width: 13%;">Invoice</th>
                                            <th>Tanggal</th>
                                            <th>Customer</th>
                                            <th>Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                        $i = 1; 
                                        $total = 0;
                                        while ($rowProduct = mysqli_fetch_array($resultInvoice)) {
                                            $total += $rowProduct['invoice_sub_total'];
                                    ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $rowProduct['penjualan_invoice']; ?></td>
                                        <td><?= $rowProduct['invoice_tgl']; ?></td>
                                        <td>
                                            <?php  
                                                $customer = $rowProduct['customer_nama'];   
                                                if ($customer === 'Umum') {
                                                    echo "<b style='color: red;'>Umum</b>";
                                                } else {
                                                    echo($customer);
                                                }
                                            ?> 
                                        </td>
                                        <td>Rp. <?= number_format($rowProduct['invoice_sub_total'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php $i++; } ?>
                                    <tr>
                                        <td colspan="4"><b>Total</b></td>
                                        <td>Rp. <?= number_format($total, 0, ',', '.'); ?></td>
                                    </tr>
                                    <tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>
</div>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $("#laporan-customer").DataTable();
  });

  $(function () {
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  });
</script>
</body>
</html>
