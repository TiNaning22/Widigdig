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
            <h1>Data Laporan Supplier</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Laporan Supplier</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>


    <section class="content">
      <div class="container-fluid">
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Filter Data Berdasrkan Tanggal</h3>

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
                        <label for="tanggal_akhir">Nama Supplier</label>
                        <select class="form-control select2bs4" required="" name="supplier_id">
                            <option selected="selected" value="">-- Pilih Supplier --</option>
                            <?php  
                              $supplier = query("SELECT * FROM supplier WHERE supplier_status = '1' ORDER BY supplier_id DESC ");
                            ?>
                            <?php foreach ( $supplier as $ctr ) : ?>
                              <?php if ( $ctr['supplier_id'] != 0 ) { ?>
                              <option value="<?= $ctr['supplier_id']; ?>">
                                <?= $ctr['supplier_nama']; ?> - <?= $ctr['supplier_company'] ?>
                              </option>
                              <?php } ?>
                            <?php endforeach; ?>
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


    <?php if( isset($_POST["submit"]) ){ ?>
        <?php  
          $tanggal_awal  = $_POST['tanggal_awal'];
          $tanggal_akhir = $_POST['tanggal_akhir'];
          $supplier_id   = $_POST['supplier_id'];
        ?>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Laporan Data Supplier</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="laporan-data-supplier" class="table table-bordered table-striped table-laporan">
                  <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 13%;">Invoice</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Total</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $i = 1; 
                    $total = 0;
                    $queryInvoice = $conn->query("SELECT invoice_pembelian.invoice_pembelian_id ,invoice_pembelian.pembelian_invoice, invoice_pembelian.invoice_tgl, supplier.supplier_id, supplier.supplier_nama, supplier.supplier_company, invoice_pembelian.invoice_total, invoice_pembelian.invoice_pembelian_cabang
                               FROM invoice_pembelian 
                               JOIN supplier ON invoice_pembelian.invoice_supplier = supplier.supplier_id
                               WHERE invoice_pembelian_cabang = '".$sessionCabang."' && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
                               ORDER BY invoice_pembelian_id DESC
                               ");
                    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                   
                    if ( $rowProduct['supplier_id'] === $supplier_id ) {
                       $total += $rowProduct['invoice_total'];
                  ?>
                  <tr>
                      <td><?= $i; ?></td>
                      <td><?= $rowProduct['pembelian_invoice']; ?></td>
                      <td><?= $rowProduct['invoice_tgl']; ?></td>
                      <td>
                          <?= $rowProduct['supplier_nama']; ?> - 
                          <?= $rowProduct['supplier_company']; ?>
                      </td>
                      <td>Rp. <?= number_format($rowProduct['invoice_total'], 0, ',', '.'); ?></td>
                  </tr>
                  <?php $i++; ?>
                  <?php } ?>
                  <?php } ?>
                  <tr>
                      <td colspan="4">
                        <b>Total</b>
                      </td>
                      <td>
                        Rp. <?php echo number_format($total, 0, ',', '.'); ?>
                      </td>
                  </tr>
                  <tbody>
                </table>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
    <?php  } ?>
  </div>
</div>



<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $("#laporan-data-supplier").DataTable();
  });
</script>
<script>
  $(function () {

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  });
</script>
</body>
</html>