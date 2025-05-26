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
          <h1>Laporan Pengeluaran</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Laporan Pengeluaran</li>
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
            <h3 class="card-title">Data Pengeluaran</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <!-- Debug information - Remove in production -->
            <?php
              $debug = false; // Set to true to see debug info
              if ($debug) {
                echo "<div class='alert alert-info'>Debug mode enabled</div>";
              }
            ?>
            
            <div class="table-auto">
              <table id="laporan-pengeluaran" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th style="width: 5%;">No.</th>
                  <th style="width: 12%;">Invoice</th>
                  <th style="width: 10%;">Tanggal</th>
                  <th>Nama Barang</th>
                  <th style="width: 7%;">Qty</th>
                  <th style="width: 15%;">Harga Beli</th>
                  <th style="width: 15%;">Total</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                  $i = 1; 
                  $total_pengeluaran = 0;
                  
                  // Corrected query based on actual database schema
                  $query = "SELECT 
                            p.pembelian_invoice,
                            p.pembelian_date,
                            b.barang_nama,
                            p.barang_qty AS qty_dibeli,
                            p.barang_harga_beli AS harga_beli
                          FROM pembelian p
                          JOIN barang b ON p.barang_id = b.barang_id";
                  
                  if (isset($sessionCabang) && !empty($sessionCabang)) {
                    $query .= " WHERE p.pembelian_cabang = '".$sessionCabang."'";
                  }
                  
                  $query .= " ORDER BY p.pembelian_date DESC";
                  
                  if ($debug) {
                    echo "<div class='alert alert-info'>Query: $query</div>";
                  }
                  
                  $result = $conn->query($query);
                  
                  if (!$result) {
                    echo "<tr><td colspan='7'>Query Error: " . $conn->error . "</td></tr>";
                    if ($debug) {
                      echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
                    }
                  } else if (mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='7'>Tidak ada data pengeluaran.</td></tr>";
                    if ($debug) {
                      echo "<div class='alert alert-warning'>No data found.</div>";
                    }
                  } else {
                    if ($debug) {
                      echo "<div class='alert alert-success'>Found " . mysqli_num_rows($result) . " records</div>";
                    }
                    
                    while ($row = mysqli_fetch_array($result)) {
                      $subtotal = $row['qty_dibeli'] * $row['harga_beli'];
                      $total_pengeluaran += $subtotal;
                ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $row['pembelian_invoice']; ?></td>
                  <td><?= date('d-m-Y', strtotime($row['pembelian_date'])); ?></td>
                  <td><?= $row['barang_nama']; ?></td>
                  <td><?= $row['qty_dibeli']; ?></td>
                  <td>Rp. <?= number_format($row['harga_beli'], 0, ',', '.'); ?></td>
                  <td>Rp. <?= number_format($subtotal, 0, ',', '.'); ?></td>
                </tr>
                <?php 
                      $i++; 
                    }
                  }
                ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="6" class="text-right">Total Pengeluaran:</th>
                    <th>Rp. <?= number_format($total_pengeluaran, 0, ',', '.'); ?></th>
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
  $(function () {
    // Handle any DataTables extension errors gracefully
    try {
      $("#laporan-pengeluaran").DataTable({
        "responsive": true, 
        "lengthChange": true, 
        "autoWidth": false,
        "pageLength": 25,
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
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
        },
        "order": [[2, 'desc']] // Sort by date (column index 2) in descending order
      }).buttons().container().appendTo('#laporan-pengeluaran_wrapper .col-md-6:eq(0)');
    } catch (e) {
      // Fallback to basic DataTable if extensions fail
      console.error("DataTables error:", e);
      $("#laporan-pengeluaran").DataTable({
        "responsive": true,
        "lengthChange": true,
        "pageLength": 25,
        "order": [[2, 'desc']]
      });
    }
  });
</script>
</body>
</html>