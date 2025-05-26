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
          <h1>Data Invoice</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Data Invoice</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Invoice</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="invoice-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No.</th>
                  <th>Invoice ID</th>
                  <th>Invoice Count</th>
                  <th>Tanggal</th>
                  <th>Customer</th>
                  <th>Kategori Customer</th>
                  <th>Kurir</th>
                  <th>Status Kurir</th>
                  <th>Tipe Transaksi</th>
                  <th>Total Beli</th>
                  <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                  $i = 1; 
                  $queryInvoice = $conn->query("SELECT 
                      invoice_id, 
                      penjualan_invoice, 
                      penjualan_invoice_count, 
                      invoice_tgl, 
                      invoice_customer, 
                      invoice_customer_category, 
                      invoice_kurir, 
                      invoice_status_kurir, 
                      invoice_tipe_transaksi, 
                      invoice_total_beli, 
                      invoice_total
                    FROM invoice 
                    WHERE invoice_cabang = '".$sessionCabang."' 
                    ORDER BY invoice_id DESC");
                  
                  while ($row = mysqli_fetch_array($queryInvoice)) {
                    // Get customer name
                    $customerQuery = $conn->query("SELECT customer_nama FROM customer WHERE customer_id = '".$row['invoice_customer']."'");
                    $customerData = mysqli_fetch_array($customerQuery);
                    $customerName = $customerData ? $customerData['customer_nama'] : 'Umum';
                ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $row['penjualan_invoice']; ?></td>
                  <td><?= $row['penjualan_invoice_count']; ?></td>
                  <td><?= date('d-m-Y', strtotime($row['invoice_tgl'])); ?></td>
                  <td>
                    <?php  
                      if ($customerName === 'Umum') {
                        echo "<b style='color: red;'>Umum</b>";
                      } else {
                        echo $customerName;
                      }
                    ?>
                  </td>
                  <td><?= $row['invoice_customer_category']; ?></td>
                  <td><?= $row['invoice_kurir']; ?></td>
                  <td><?= $row['invoice_status_kurir']; ?></td>
                  <td><?= $row['invoice_tipe_transaksi']; ?></td>
                  <td>Rp. <?= number_format($row['invoice_total_beli'], 0, ',', '.'); ?></td>
                  <td>Rp. <?= number_format($row['invoice_total'], 0, ',', '.'); ?></td>
                  
                </tr>
                
                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $row['invoice_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $row['invoice_id']; ?>" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel<?= $row['invoice_id']; ?>">Detail Invoice</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
                          <label>Invoice ID</label>
                          <input type="text" class="form-control" value="<?= $row['penjualan_invoice']; ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                          <label>Tanggal</label>
                          <input type="text" class="form-control" value="<?= date('d-m-Y', strtotime($row['invoice_tgl'])); ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                          <label>Customer</label>
                          <input type="text" class="form-control" value="<?= $customerName; ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                          <label>Kategori Customer</label>
                          <input type="text" class="form-control" value="<?= $row['invoice_customer_category']; ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                          <label>Tipe Transaksi</label>
                          <input type="text" class="form-control" value="<?= $row['invoice_tipe_transaksi']; ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                          <label>Total</label>
                          <input type="text" class="form-control" value="Rp. <?= number_format($row['invoice_total'], 0, ',', '.'); ?>" readonly>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <a href="invoice-detail?no=<?= $row['invoice_id']; ?>-invoice-<?= $row['penjualan_invoice']; ?>" class="btn btn-primary">Lihat Detail Lengkap</a>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- End Modal Edit -->
                
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
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- DataTables Buttons extension -->
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script>
  $(function () {
    // Initialize DataTable
    $("#invoice-table").DataTable({
      "responsive": true, 
      "lengthChange": true, 
      "autoWidth": false,
      "pageLength": 25,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
      "language": {
        "search": "Cari:",
        "lengthMenu": "Tampilkan _MENU_ data per halaman",
        "zeroRecords": "Tidak ditemukan data yang sesuai",
        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
        "infoFiltered": "(disaring dari _MAX_ data keseluruhan)",
        "paginate": {
          "first": "Pertama",
          "last": "Terakhir",
          "next": "Selanjutnya",
          "previous": "Sebelumnya"
        }
      }
    }).buttons().container().appendTo('#invoice-table_wrapper .col-md-6:eq(0)');
  });
</script>
</body>
</html>