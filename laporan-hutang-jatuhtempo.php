<?php  
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
  include 'aksi/koneksi.php';
?>

<?php  
  if ($levelLogin === "kurir") {
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
          <h1>Laporan Rekap Hutang <b>Jatuh Tempo</b></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Laporan Hutang</li>
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
            <h3 class="card-title">Laporan Rekap Hutang Jatuh Tempo</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 13%;">Invoice</th>
                    <th>Tanggal Transaksi</th>
                    <th>Jatuh Tempo</th>
                    <th>Total Hutang</th>
                    <th>Status</th>
                    <th style="text-align: center; width: 16%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    // Query untuk mengambil data hutang jatuh tempo
                    $sql = "SELECT 
                              a.invoice_pembelian_id, 
                              a.pembelian_invoice, 
                              a.invoice_date, 
                              a.invoice_hutang,
                              a.invoice_hutang_jatuh_tempo
                            FROM invoice_pembelian a
                            WHERE a.invoice_pembelian_cabang = $sessionCabang 
                            AND a.invoice_hutang > 0 
                            AND a.invoice_hutang_jatuh_tempo IS NOT NULL
                            ORDER BY a.invoice_hutang_jatuh_tempo ASC";
                    
                    $result = mysqli_query($conn, $sql);
                    $no = 1;
                    
                    function getStatusBadge($dueDate) {
                      $today = new DateTime();
                      $dueDateTime = new DateTime($dueDate);
                      
                      if ($today > $dueDateTime) {
                        return '<span class="badge badge-danger">Terlambat</span>';
                      } else {
                        $diff = $today->diff($dueDateTime);
                        $diffDays = $diff->days;
                        
                        if ($diffDays <= 7) {
                          return '<span class="badge badge-warning">Mendekati Jatuh Tempo</span>';
                        } else {
                          return '<span class="badge badge-success">Masih Aman</span>';
                        }
                      }
                    }
                    
                    if (mysqli_num_rows($result) > 0) {
                      while ($row = mysqli_fetch_assoc($result)) {
                        $id = $row['invoice_pembelian_id'];
                        $invoice = $row['pembelian_invoice'];
                        $tanggal = date('d-m-Y', strtotime($row['invoice_date']));
                        $jatuhTempo = date('d-m-Y', strtotime($row['invoice_hutang_jatuh_tempo']));
                        $totalHutang = $row['invoice_hutang'];
                        $status = getStatusBadge($row['invoice_hutang_jatuh_tempo']);
                        $idEncoded = base64_encode($id);
                  ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <td><?= $invoice; ?></td>
                      <td><?= $tanggal; ?></td>
                      <td><?= $jatuhTempo; ?></td>
                      <td>Rp. <?= number_format($totalHutang, 0, ',', '.'); ?></td>
                      <td><?= $status; ?></td>
                      <td>
                        <center class="orderan-online-button">
                          <a href="pembelian-zoom?no=<?= $idEncoded; ?>" target="_blank" class="btn btn-info" title="Lihat Data">
                            <i class="fa fa-eye"></i>
                          </a>&nbsp;
                          
                          <a href="hutang-cicilan?no=<?= $idEncoded; ?>" class="btn btn-success" title="Cicilan">
                            <i class="fa fa-money"></i>
                          </a>&nbsp;
                          
                          <a href="nota-cetak-hutang?no=<?= $id; ?>" target="_blank" class="btn btn-warning" title="Cetak Nota">
                            <i class="fa fa-print"></i>
                          </a>&nbsp;
                          
                          <?php if ($levelLogin === "super admin") { ?>
                          <?php } ?>
                        </center>
                      </td>
                    </tr>
                  <?php
                      }
                    } else {
                  ?>
                    <tr>
                      <td colspan="7" class="text-center">Tidak ada data hutang jatuh tempo</td>
                    </tr>
                  <?php
                    }
                    
                    // Close connection
                    mysqli_close($conn);
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

<script>
  function kirimNotifikasi(id, invoice) {
    var link = confirm('Kirim notifikasi pengingat hutang jatuh tempo?');
    if (link === true) {
      // Kirim data menggunakan form submission untuk menghindari AJAX
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = 'hutang-kirim-notifikasi.php';
      
      var inputId = document.createElement('input');
      inputId.type = 'hidden';
      inputId.name = 'id';
      inputId.value = id;
      form.appendChild(inputId);
      
      var inputInvoice = document.createElement('input');
      inputInvoice.type = 'hidden';
      inputInvoice.name = 'invoice';
      inputInvoice.value = invoice;
      form.appendChild(inputInvoice);
      
      // Tambahkan parameter redirect_back
      var inputRedirect = document.createElement('input');
      inputRedirect.type = 'hidden';
      inputRedirect.name = 'redirect_back';
      inputRedirect.value = window.location.href;
      form.appendChild(inputRedirect);
      
      document.body.appendChild(form);
      form.submit();
    }
  }
</script>

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
    if (!$.fn.DataTable.isDataTable('#example1')) {
      // Inisialisasi DataTable dengan tombol ekspor
      $("#example1").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    }
  });
</script>
</body>
</html>