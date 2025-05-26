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
          <h1>Laporan Rekap Sisa Hutang Per Faktur</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Laporan Sisa Hutang</li>
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
            <h3 class="card-title">Data Rekap Sisa Hutang Per Faktur</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="hutang-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Invoice</th>
                    <th>Tanggal Transaksi</th>
                    <th>Total Hutang</th>
                    <th>Jatuh Tempo</th>
                    <th>Status Tempo</th>
                    <th>Sisa Hutang</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    // Query untuk mengambil semua data dari invoice_pembelian (tanpa supplier)
                    $query = "SELECT 
                              ip.invoice_pembelian_id,
                              ip.pembelian_invoice,
                              ip.invoice_tgl,
                              ip.invoice_total,
                              ip.invoice_hutang_jatuh_tempo,
                              ip.invoice_hutang,
                              CASE 
                                WHEN ip.invoice_hutang_jatuh_tempo < CURDATE() THEN 'Sudah Jatuh Tempo'
                                ELSE 'Belum Jatuh Tempo'
                              END as status_tempo
                            FROM invoice_pembelian ip
                            WHERE ip.invoice_pembelian_cabang = $sessionCabang
                            ORDER BY ip.invoice_tgl DESC";

                    $result = query($query);
                    $no = 1;
                    
                    // Cek apakah ada data
                    if (empty($result)) {
                      echo "<tr><td colspan='7' class='text-center'>Tidak ada data hutang.</td></tr>";
                    } else {
                      foreach ($result as $row) {
                        $status_class = ($row['status_tempo'] == 'Sudah Jatuh Tempo') ? 'badge badge-danger' : 'badge badge-success';
                  ?>
                  <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['pembelian_invoice']; ?></td>
                    <td><?= tanggal_indo($row['invoice_tgl']); ?></td>
                    <td>Rp. <?= number_format($row['invoice_total'], 0, ',', '.'); ?></td>
                    <td><?= tanggal_indo($row['invoice_hutang_jatuh_tempo']); ?></td>
                    <td><span class="<?= $status_class; ?>"><?= $row['status_tempo']; ?></span></td>
                    <td>Rp. <?= number_format($row['invoice_hutang'], 0, ',', '.'); ?></td>
                  </tr>
                  <?php 
                      }
                    } 
                  ?>
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
    // Periksa apakah tabel sudah diinisialisasi sebagai DataTable
    if (!$.fn.DataTable.isDataTable('#hutang-table')) {
      // Inisialisasi DataTable
      $("#hutang-table").DataTable({
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
      }).buttons().container().appendTo('#hutang-table_wrapper .col-md-6:eq(0)');
    }
  });
</script>
</body>
</html>