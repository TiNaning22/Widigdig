<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
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
          <h1>Data Penjualan</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Laporan Pembelian</li>
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
            <h3 class="card-title">Laporan Pembelian <?= isset($_POST['submit']) ? 'Periode: ' . date('d/m/Y', strtotime($tanggal_awal)) . ' - ' . date('d/m/Y', strtotime($tanggal_akhir)) : ''; ?></h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="example1" class="table table-bordered table-striped" style="width: 100%">
                <thead>
                <tr>
                  <th style="width: 6%;">No.</th>
                  <th style="width: 12%;">Invoice</th>
                  <th style="width: 15%;">Tanggal Transaksi</th>
                  <th>Customer</th>
                  <th>Kasir</th>
                  <th>Sub Total</th>
                  <th style="text-align: center;">Aksi</th>
                </tr>
                </thead>
                <tbody>
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
</div>

<?php include '_footer.php'; ?>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(document).ready(function(){
    // Initialize Select2
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
    
    // Get filter parameters
    var tanggalAwal = "<?= $tanggal_awal; ?>";
    var tanggalAkhir = "<?= $tanggal_akhir; ?>";
    var customerId = "<?= $customer_id; ?>";
    var isFiltered = "<?= isset($_POST['submit']) ? 'true' : 'false'; ?>";
    
    // Destroy existing DataTable if it exists
    if ($.fn.dataTable.isDataTable('#example1')) {
      $('#example1').DataTable().destroy();
    }
    
    // Initialize DataTable
    var table = $('#example1').DataTable({ 
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url": "penjualan-data.php",
        "type": "GET",
        "data": function(d) {
          d.cabang = "<?= $sessionCabang; ?>";
          d.tanggal_awal = tanggalAwal;
          d.tanggal_akhir = tanggalAkhir;
          d.customer_id = customerId;
          d.filter = isFiltered;
        }
      },
      "columnDefs": [
        {
          "targets": 5,
          "render": $.fn.dataTable.render.number('.', '', '', 'Rp. ')
        },
        {
          "targets": -1,
          "data": null,
          "defaultContent": 
          `<center class="orderan-online-button">
              <button class='btn btn-warning tblZoom' title='Lihat Data'>
                  <i class='fa fa-eye'></i>
              </button>&nbsp;
          </center>` 
        }
      ]
    });

    // Add row numbers
    table.on('draw.dt', function () {
      var info = table.page.info();
      table.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
          cell.innerHTML = i + 1 + info.start;
      });
    });
    
    // Handle zoom button click
    $('#example1 tbody').on('click', '.tblZoom', function () {
      var data = table.row($(this).parents('tr')).data();
      var data0 = data[0];
      var data0 = btoa(data0);
      window.open('penjualan-zoom?no='+ data0, '_blank');
    });
  });
</script>
</body>
</html>