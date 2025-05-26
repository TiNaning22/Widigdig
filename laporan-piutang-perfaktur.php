<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
  include 'aksi/koneksi.php';
?>

<?php  
  if ( $levelLogin === "kurir") {
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
            <h1>Laporan Rekap Piutang <b>Per Nota</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Laporan Piutang</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Data Rekap Piutang Per Nota</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th style="width: 6%;">No.</th>
                  <th>Invoice</th>
                  <th>Tanggal Transaksi</th>
                  <th>Jatuh Tempo</th>
                  <th>Total Piutang</th>
                  <th>Sudah Dibayar</th>
                  <th>Sisa Piutang</th>
                  <th style="text-align: center;">Aksi</th>
                </tr>
                </thead>
                <tbody>
                  <?php 
                    $i = 1;
                    $sql = "SELECT 
                              a.invoice_id, 
                              a.penjualan_invoice,
                              a.invoice_date,
                              a.invoice_sub_total, 
                              a.invoice_cabang, 
                              a.invoice_kasir,
                              a.invoice_piutang,
                              a.invoice_piutang_jatuh_tempo
                            FROM invoice a
                            LEFT JOIN user c ON a.invoice_kasir = c.user_id
                            WHERE a.invoice_cabang = '$sessionCabang' && a.invoice_piutang > 0
                            ORDER BY a.invoice_id DESC";
                    $query = mysqli_query($conn, $sql);
                    while ($data = mysqli_fetch_array($query)) {
                      $invoiceId = $data['invoice_id'];
                      $invoiceNo = $data['penjualan_invoice'];
                      $invoiceDate = $data['invoice_date'];
                      $invoiceDueDate = $data['invoice_piutang_jatuh_tempo'];
                      $invoiceTotal = $data['invoice_sub_total'];
                      $invoiceRemaining = $data['invoice_piutang'];
                      $invoicePaid = $invoiceTotal - $invoiceRemaining;
                      
                      // Format date to Indonesian format (DD-MM-YYYY)
                      $formattedDate = date("d-m-Y", strtotime($invoiceDate));
                      $formattedDueDate = date("d-m-Y", strtotime($invoiceDueDate));
                      
                      // Encode invoice ID for URLs
                      $encodedId = base64_encode($invoiceId);
                  ?>
                    <tr>
                      <td><?= $i; ?></td>
                      <td><?= $invoiceNo; ?></td>
                      <td><?= $formattedDate; ?></td>
                      <td><?= $formattedDueDate; ?></td>
                      <td>Rp. <?= number_format($invoiceTotal, 0, ',', '.'); ?></td>
                      <td>Rp. <?= number_format($invoicePaid, 0, ',', '.'); ?></td>
                      <td>Rp. <?= number_format($invoiceRemaining, 0, ',', '.'); ?></td>
                      <td>
                        <center class="orderan-online-button">
                          <button class='btn btn-success tblCicilan' title='Riwayat Cicilan' onclick="window.location.href='piutang-cicilan?no=<?= $encodedId; ?>'">
                              <i class='fa fa-history'></i>
                          </button>&nbsp;
                        </center>
                      </td>
                    </tr>
                  <?php $i++; } ?>
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
  </div>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable('#example1')) {
      $("#example1").DataTable({
        "responsive": true,
        "autoWidth": false,
        "order": []
      });
    }
  });
</script>
</body>
</html>