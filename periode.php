<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "kasir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }
    
?>

	<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Data Per Periode</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Per Periode</li>
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
                        <label for="tanggal_akhir">Pembayaran</label>
                        <select class="form-control select2bs4" required="" name="type_transaksi">
                            <option selected="selected" value="">-- Pilih --</option>
                            <option value="0">Semua Pembayaran</option>
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
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
          $type_transaksi= $_POST['type_transaksi'];
        ?>

      <!-- Main content -->
      <?php if ( $type_transaksi === "0" ) : ?>
      <section class="content">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Laporan Penjulan Periode</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="table-auto">
                  <table id="laporan-penjulan-periode" class="table table-bordered table-striped table-laporan">
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
                      $queryInvoice = $conn->query("SELECT invoice.invoice_id ,invoice.penjualan_invoice, invoice.invoice_tgl, customer.customer_id, customer.customer_nama, invoice.invoice_total, invoice.invoice_sub_total, invoice.invoice_cabang
                                 FROM invoice 
                                 JOIN customer ON invoice.invoice_customer = customer.customer_id
                                 WHERE invoice_cabang = '".$sessionCabang."' && invoice_piutang < 1 && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && invoice_draft = 0
                                 ORDER BY invoice_id DESC
                                 ");
                      while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                      $total += $rowProduct['invoice_sub_total'];
                    ?>
                    <tr>
                      	<td><?= $i; ?></td>
                        <td><?= $rowProduct['penjualan_invoice']; ?></td>
                        <td><?= $rowProduct['invoice_tgl']; ?></td>
                        <td>
                            <?php  
                              $customer = $rowProduct['customer_nama'];   
                              if  ( $customer === 'Umum' ) {
                                echo "<b style='color: red;'>Umum</b>";
                              } else {
                                echo($customer);
                              }
                            ?> 
                        </td>
                        <td>Rp. <?= number_format($rowProduct['invoice_sub_total'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php $i++; ?>
                    <?php } ?>
                    <tr>
                        <td colspan="4">
                          <b>Total</b>
                        </td>
                        <td>
                          Rp. <?php echo number_format($total, 0, ',', '.'); ?>
                        </td>
                    </tr>
                  </tbody>
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

      <?php elseif ( $type_transaksi === "cash" ) : ?>
      <section class="content">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Laporan Penjulan Periode</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="table-auto">
                  <table id="laporan-penjulan-periode-tipe-cash" class="table table-bordered table-striped table-laporan">
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
                      $queryInvoice = $conn->query("SELECT invoice.invoice_id ,invoice.penjualan_invoice, invoice.invoice_tgl, customer.customer_id, customer.customer_nama, invoice.invoice_total, invoice.invoice_sub_total, invoice.invoice_cabang, invoice.invoice_tipe_transaksi
                                 FROM invoice 
                                 JOIN customer ON invoice.invoice_customer = customer.customer_id
                                 WHERE invoice_cabang = '".$sessionCabang."' && invoice_piutang < 1 && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && invoice_tipe_transaksi = 0 && invoice_draft = 0
                                 ORDER BY invoice_id DESC
                                 ");
                      while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                      $total += $rowProduct['invoice_sub_total'];
                    ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><?= $rowProduct['penjualan_invoice']; ?></td>
                        <td><?= $rowProduct['invoice_tgl']; ?></td>
                        <td>
                            <?php  
                              $customer = $rowProduct['customer_nama'];   
                              if  ( $customer === 'Umum' ) {
                                echo "<b style='color: red;'>Umum</b>";
                              } else {
                                echo($customer);
                              }
                            ?> 
                        </td>
                        <td>Rp. <?= number_format($rowProduct['invoice_sub_total'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php $i++; ?>
                    <?php } ?>
                    <tr>
                        <td colspan="4">
                          <b>Total</b>
                        </td>
                        <td>
                          Rp. <?php echo number_format($total, 0, ',', '.'); ?>
                        </td>
                    </tr>
                  </tbody>
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

      <?php elseif ( $type_transaksi === "transfer" ) : ?>
      <section class="content">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Laporan Penjulan Periode</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="table-auto">
                  <table id="laporan-penjulan-periode-tipe-transfer" class="table table-bordered table-striped table-laporan">
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
                      $queryInvoice = $conn->query("SELECT invoice.invoice_id ,invoice.penjualan_invoice, invoice.invoice_tgl, customer.customer_id, customer.customer_nama, invoice.invoice_total, invoice.invoice_sub_total, invoice.invoice_cabang, invoice.invoice_tipe_transaksi
                                 FROM invoice 
                                 JOIN customer ON invoice.invoice_customer = customer.customer_id
                                 WHERE invoice_cabang = '".$sessionCabang."' && invoice_piutang < 1 && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && invoice_tipe_transaksi = 1 && invoice_draft = 0
                                 ORDER BY invoice_id DESC
                                 ");
                      while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                      $total += $rowProduct['invoice_sub_total'];
                    ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><?= $rowProduct['penjualan_invoice']; ?></td>
                        <td><?= $rowProduct['invoice_tgl']; ?></td>
                        <td>
                            <?php  
                              $customer = $rowProduct['customer_nama'];   
                              if  ( $customer === 'Umum' ) {
                                echo "<b style='color: red;'>Umum</b>";
                              } else {
                                echo($customer);
                              }
                            ?> 
                        </td>
                        <td>Rp. <?= number_format($rowProduct['invoice_sub_total'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php $i++; ?>
                    <?php } ?>
                    <tr>
                        <td colspan="4">
                          <b>Total</b>
                        </td>
                        <td>
                          Rp. <?php echo number_format($total, 0, ',', '.'); ?>
                        </td>
                    </tr>
                  </tbody>
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
      <?php endif; ?>
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
    $("#laporan-penjulan-periode").DataTable();
  });
</script>
</body>
</html>