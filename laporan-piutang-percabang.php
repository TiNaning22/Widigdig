<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "kurir" ) {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }   
?>

<?php
// Query untuk filter tanggal
$tanggal_awal  = "";
$tanggal_akhir = "";

if(isset($_POST['submit'])){
  $tanggal_awal  = mysqli_real_escape_string($conn, $_POST['tanggal_awal']);
  $tanggal_akhir = mysqli_real_escape_string($conn, $_POST['tanggal_akhir']);
}

// Reset Filter
if(isset($_POST['reset'])){
  $tanggal_awal  = "";
  $tanggal_akhir = "";
}

// Export ke Excel
if(isset($_POST['excel'])){
  header("Content-type: application/vnd-ms-excel");
  header("Content-Disposition: attachment; filename=Laporan Rekap Pelunasan Piutang Per Cabang ".$tanggal_awal." - ".$tanggal_akhir.".xls");
}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Laporan Rekap Piutang Per Cabang</h1>
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
      <div class="container-fluid">
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Filter Data</h3>

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
                    <input type="date" name="tanggal_awal" class="form-control" id="tanggal_awal" required value="<?= $tanggal_awal; ?>">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="tanggal_akhir">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control" id="tanggal_akhir" required value="<?= $tanggal_akhir; ?>">
                  </div>
                </div>
                <?php if ( $levelLogin === "super admin" ) : ?>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="cabang">Lokasi Cabang</label>
                    <select class="form-control select2bs4" name="cabang" required>
                      <option selected="selected" value="">-- Pilih Cabang --</option>
                      <option value="0">Semua Cabang</option>
                      <?php  
                        $tokoselect = query("SELECT * FROM toko WHERE toko_status > 0 ORDER BY toko_id ASC");
                      ?>
                      <?php foreach ( $tokoselect as $row ) : ?>
                        <option value="<?= $row['toko_cabang'] ?>">
                          <?= $row['toko_nama'] ?> - <?= $row['toko_kota'] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <?php endif; ?>
              </div>
              <div class="card-footer text-right">
                <button type="submit" name="reset" class="btn btn-secondary">Reset</button>
                <button type="submit" name="submit" class="btn btn-primary">Filter Data</button>
                <?php if(isset($_POST['submit'])) { ?>
                <button type="submit" name="excel" class="btn btn-success">
                  <i class="fa fa-file-excel-o"></i> Export Excel
                </button>
                <?php } ?>
              </div>
            </div>
          </form>
      </div>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Data Rekap Piutang Per Cabang</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No.</th>
                  <th>Nama Cabang</th>
                  <th>Jumlah Transaksi</th>
                  <th>Total Piutang</th>
                  <th>Total Pelunasan</th>
                  <th>Sisa Piutang</th>
                  <th>Kasir</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                  $i = 1; 
                  $queryFilter = "";
                  
                  if ( $tanggal_awal && $tanggal_akhir ) {
                    $queryFilter = " AND piutang.piutang_date_time BETWEEN '$tanggal_awal' AND '$tanggal_akhir 23:59:59'";
                  }

                  // Cabang Filter
                  $cabangFilter = "";
                  if(isset($_POST['cabang']) && $_POST['cabang'] != "" && $_POST['cabang'] != "0"){
                    $cabang = $_POST['cabang'];
                    $cabangFilter = " AND piutang.piutang_cabang = '$cabang'";
                  }

                  // Super Admin melihat semua cabang
                  if ( $levelLogin === "super admin" ) {
                    $cabangQuery = "SELECT 
                                    toko.toko_nama, 
                                    toko.toko_kota, 
                                    toko.toko_cabang,
                                    COUNT(DISTINCT piutang.piutang_invoice) as total_transaksi,
                                    SUM(invoice.invoice_sub_total) as total_piutang,
                                    SUM(piutang.piutang_nominal) as total_pelunasan,
                                    SUM(invoice.invoice_sub_total) - SUM(piutang.piutang_nominal) as sisa_piutang
                                  FROM toko 
                                  LEFT JOIN piutang ON toko.toko_cabang = piutang.piutang_cabang
                                  LEFT JOIN invoice ON piutang.piutang_invoice = invoice.penjualan_invoice
                                  WHERE toko.toko_status > 0 $queryFilter $cabangFilter
                                  GROUP BY toko.toko_cabang
                                  ORDER BY total_piutang DESC";
                    
                    $dataRekap = query($cabangQuery);

                  } else {
                    // Admin & Kasir hanya melihat cabang mereka
                    $cabangQuery = "SELECT 
                                    toko.toko_nama, 
                                    toko.toko_kota, 
                                    toko.toko_cabang,
                                    COUNT(DISTINCT piutang.piutang_invoice) as total_transaksi,
                                    SUM(invoice.invoice_sub_total) as total_piutang,
                                    SUM(piutang.piutang_nominal) as total_pelunasan,
                                    SUM(invoice.invoice_sub_total) - SUM(piutang.piutang_nominal) as sisa_piutang
                                  FROM toko 
                                  LEFT JOIN piutang ON toko.toko_cabang = piutang.piutang_cabang
                                  LEFT JOIN invoice ON piutang.piutang_invoice = invoice.penjualan_invoice
                                  WHERE toko.toko_cabang = '$sessionCabang' $queryFilter
                                  GROUP BY toko.toko_cabang";
                    
                    $dataRekap = query($cabangQuery);
                  }
                ?>
                <?php foreach($dataRekap as $row) : ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $row['toko_nama']; ?> - <?= $row['toko_kota']; ?></td>
                  <td><?= number_format($row['total_transaksi']); ?></td>
                  <td>Rp. <?= number_format($row['total_piutang']); ?></td>
                  <td>Rp. <?= number_format($row['total_pelunasan']); ?></td>
                  <td>Rp. <?= number_format($row['sisa_piutang']); ?></td>
                  <td>
                    <?php
                      // Get top 3 kasir berdasarkan jumlah transaksi
                      $cabangId = $row['toko_cabang'];
                      $kasirQuery = "SELECT user.user_nama, COUNT(piutang.piutang_id) as transaksi_count
                                    FROM piutang
                                    LEFT JOIN user ON piutang.piutang_kasir = user.user_id
                                    WHERE piutang.piutang_cabang = '$cabangId' $queryFilter
                                    GROUP BY piutang.piutang_kasir
                                    ORDER BY transaksi_count DESC
                                    LIMIT 3";
                      $kasirData = query($kasirQuery);
                      
                      foreach ($kasirData as $kasir) {
                        echo $kasir['user_nama'] . ' (' . number_format($kasir['transaksi_count']) . ')<br>';
                      }
                    ?>
                  </td>
                  
                </tr>
                <?php $i++; ?>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                  <th colspan="2">TOTAL</th>
                  <?php
                    // Calculate grand totals
                    $totalTransaksi = 0;
                    $totalPiutang = 0;
                    $totalPelunasan = 0;
                    $totalSisa = 0;
                    
                    foreach($dataRekap as $row) {
                      $totalTransaksi += $row['total_transaksi'];
                      $totalPiutang += $row['total_piutang'];
                      $totalPelunasan += $row['total_pelunasan'];
                      $totalSisa += $row['sisa_piutang'];
                    }
                  ?>
                  <th><?= number_format($totalTransaksi); ?></th>
                  <th>Rp. <?= number_format($totalPiutang); ?></th>
                  <th>Rp. <?= number_format($totalPelunasan); ?></th>
                  <th>Rp. <?= number_format($totalSisa); ?></th>
                  <th colspan="2"></th>
                </tr>
                </tfoot>
              </table>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    
    <!-- Statistik Section -->
    <?php if ($tanggal_awal && $tanggal_akhir && count($dataRekap) > 0) : ?>
    <section class="content">
      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Grafik Piutang Per Cabang</h3>
            </div>
            <div class="card-body">
              <canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Tren Piutang Periode <?= date('d/m/Y', strtotime($tanggal_awal)); ?> - <?= date('d/m/Y', strtotime($tanggal_akhir)); ?></h3>
            </div>
            <div class="card-body">
              <canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>
    
  </div>
</div>

<!-- Bootstrap Modal for Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Piutang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-3" id="loadingIndicator">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
          </div>
          <p>Memuat data...</p>
        </div>
        <div id="detailContent"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" id="printDetail" style="display: none;">
          <i class="fa fa-print"></i> Cetak
        </button>
      </div>
    </div>
  </div>
</div>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>

<script>
  $(function () {
    // DataTable
    $("#example1").DataTable();
    
    // Popup for detail functionality
    $('.table-auto').on('click', '.btn-detail', function(e) {
      e.preventDefault();
      var detailUrl = $(this).attr('href');
      var cabangName = $(this).data('cabang');
      
      // Update modal title
      $('#detailModalLabel').text('Detail Piutang - ' + cabangName);
      
      // Show modal with loading indicator
      $('#detailModal').modal('show');
      $('#loadingIndicator').show();
      $('#detailContent').html('');
      $('#printDetail').hide();
      
      // Fetch detail content via AJAX
      $.ajax({
        url: detailUrl + '&popup=1',
        type: 'GET',
        success: function(response) {
          // Hide loading indicator
          $('#loadingIndicator').hide();
          
          // Replace content area with response
          $('#detailContent').html(response);
          
          // Initialize any DataTables in the fetched content
          if ($.fn.DataTable.isDataTable('#detailTable')) {
            $('#detailTable').DataTable().destroy();
          }
          $('#detailTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
          });
          
          // Show print button
          $('#printDetail').show();
        },
        error: function() {
          $('#loadingIndicator').hide();
          $('#detailContent').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>');
        }
      });
    });
    
    // Print functionality for detail popup
    $('#printDetail').on('click', function() {
      var printContents = document.getElementById('detailContent').innerHTML;
      var originalContents = document.body.innerHTML;
      
      // Add print styles
      var printStyles = '<style>' +
        '@media print {' +
        '  .modal-footer, .dataTables_filter, .dataTables_info, .dataTables_paginate, .dataTables_length {display: none !important;}' +
        '  table {width: 100%;}' +
        '  th, td {padding: 8px; border: 1px solid #ddd;}' +
        '  th {background-color: #f2f2f2;}' +
        '}' +
        '</style>';
      
      // Create a print window
      document.body.innerHTML = '<div class="container">' + 
                                '<h3>' + $('#detailModalLabel').text() + '</h3>' +
                                printStyles + 
                                printContents + 
                                '</div>';
      
      window.print();
      
      // Restore original content
      document.body.innerHTML = originalContents;
      
      // Reinitialize event handlers after restoring
      $(function() {
        // Reinitialize the detail button click handler
        $('.table-auto').on('click', '.btn-detail', function(e) {
          // Event handler code here (same as above)
        });
      });
    });
    
    <?php if ($tanggal_awal && $tanggal_akhir && count($dataRekap) > 0) : ?>
    // Pie Chart Data
    var pieData = {
      labels: [
        <?php foreach($dataRekap as $row) : ?>
          '<?= $row['toko_nama']; ?> - <?= $row['toko_kota']; ?>',
        <?php endforeach; ?>
      ],
      datasets: [
        {
          data: [
            <?php foreach($dataRekap as $row) : ?>
              <?= $row['total_pelunasan']; ?>,
            <?php endforeach; ?>
          ],
          backgroundColor: [
            '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de',
            '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#6c757d', '#343a40'
          ],
        }
      ]
    }

    // Get Line Chart Data - Daily pelunasan for date range
    <?php
      // Create date range array
      $begin = new DateTime($tanggal_awal);
      $end = new DateTime($tanggal_akhir);
      $end = $end->modify('+1 day');
      $interval = new DateInterval('P1D');
      $daterange = new DatePeriod($begin, $interval, $end);
      
      $dates = [];
      $dailyPelunasan = [];
      
      foreach ($daterange as $date) {
        $currentDate = $date->format("Y-m-d");
        $dates[] = $date->format("d/m/Y");
        
        // Get pelunasan for all cabang on this date
        if ($levelLogin === "super admin" && isset($_POST['cabang']) && $_POST['cabang'] != "" && $_POST['cabang'] != "0") {
          $cabang = $_POST['cabang'];
          $dailyPelunasanQuery = "SELECT SUM(piutang_nominal) as daily_total FROM piutang 
                                  WHERE DATE(piutang_date_time) = '$currentDate' AND piutang_cabang = '$cabang'";
        } else if ($levelLogin === "super admin") {
          $dailyPelunasanQuery = "SELECT SUM(piutang_nominal) as daily_total FROM piutang 
                                  WHERE DATE(piutang_date_time) = '$currentDate'";
        } else {
          $dailyPelunasanQuery = "SELECT SUM(piutang_nominal) as daily_total FROM piutang 
                                  WHERE DATE(piutang_date_time) = '$currentDate' AND piutang_cabang = '$sessionCabang'";
        }
        
        $dailyPelunasanData = query($dailyPelunasanQuery);
        $dailyPelunasan[] = $dailyPelunasanData[0]['daily_total'] ? $dailyPelunasanData[0]['daily_total'] : 0;
      }
    ?>
    
    var lineData = {
      labels: [<?= "'" . implode("','", $dates) . "'"; ?>],
      datasets: [
        {
          label: 'Total Pelunasan',
          backgroundColor: 'rgba(60,141,188,0.9)',
          borderColor: 'rgba(60,141,188,0.8)',
          pointRadius: true,
          pointColor: '#3b8bba',
          pointStrokeColor: 'rgba(60,141,188,1)',
          pointHighlightFill: '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data: [<?= implode(",", $dailyPelunasan); ?>]
        }
      ]
    }

    //-------------
    //- PIE CHART -
    //-------------
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
    var pieOptions = {
      maintainAspectRatio: false,
      responsive: true,
    }
    new Chart(pieChartCanvas, {
      type: 'pie',
      data: pieData,
      options: pieOptions
    })

    //-------------
    //- LINE CHART -
    //-------------
    var lineChartCanvas = $('#lineChart').get(0).getContext('2d')
    var lineOptions = {
      maintainAspectRatio: false,
      responsive: true,
      scales: {
        xAxes: [{
          gridLines: {
            display: false,
          }
        }],
        yAxes: [{
          gridLines: {
            display: true,
          }
        }]
      }
    }
    new Chart(lineChartCanvas, {
      type: 'line',
      data: lineData,
      options: lineOptions
    })
    <?php endif; ?>
  });
</script>
</body>
</html>