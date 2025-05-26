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
          <h1>Laporan Per Faktur</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Laporan Per Faktur</li>
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
            <h3 class="card-title">Data Faktur Pembelian</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          
          <!-- Table -->
          <div class="card-body">
            <div class="table-responsive">
              <table id="faktur-table" class="table table-bordered table-striped display">
                <thead>
                <tr>
                  <th>No.</th>
                  <th>No. Faktur</th>
                  <th>Total Hutang</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                  // Build the SQL query with the provided statement
                  $query = "SELECT 
                    ip.invoice_pembelian_id,
                    ip.pembelian_invoice,
                    ip.invoice_total AS total_hutang
                  FROM invoice_pembelian ip
                  WHERE ip.invoice_pembelian_cabang = $sessionCabang
                  ORDER BY ip.invoice_tgl DESC
                  LIMIT 25";
                  
                  $fakturs = query($query);
                  
                  $i = 1;
                  $total_hutang_keseluruhan = 0;
                  
                  // Cek apakah ada data
                  if (empty($fakturs)) {
                    echo "<tr><td colspan='3' class='text-center'>Tidak ada data faktur pembelian.</td></tr>";
                  } else {
                    foreach($fakturs as $row) :
                      $total_hutang_keseluruhan += $row['total_hutang'];
                ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $row['pembelian_invoice']; ?></td>
                  <td class="text-right"><?= number_format($row['total_hutang'], 0, ',', '.'); ?></td>
                </tr>
                <?php 
                    $i++; 
                    endforeach;
                  } 
                ?>
                </tbody>
                <tfoot>
                  <tr class="bg-light">
                    <th colspan="2" class="text-right">Total:</th>
                    <th class="text-right"><?= number_format($total_hutang_keseluruhan, 0, ',', '.'); ?></th>
                  </tr>
                </tfoot>
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
  $(document).ready(function() {
    // Initialize DataTable
    $("#faktur-table").DataTable({
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
    }).buttons().container().appendTo('#faktur-table_wrapper .col-md-6:eq(0)');
  });
</script>
</body>
</html>