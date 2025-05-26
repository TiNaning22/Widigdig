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
  header("Content-Disposition: attachment; filename=Laporan Rekap Penjualan Per Cabang ".$tanggal_awal." - ".$tanggal_akhir.".xls");
}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Laporan Rekap Penjualan Per Cabang</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Laporan Penjualan</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Filter Section -->
    <section class="content">
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">Filter Data</h3>
        </div>
        <form action="" method="POST">
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="tanggal_awal">Tanggal Awal:</label>
                  <input type="date" name="tanggal_awal" class="form-control" id="tanggal_awal" value="<?= $tanggal_awal; ?>">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="tanggal_akhir">Tanggal Akhir:</label>
                  <input type="date" name="tanggal_akhir" class="form-control" id="tanggal_akhir" value="<?= $tanggal_akhir; ?>">
                </div>
              </div>
              <?php if ( $levelLogin === "super admin" ) { ?>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="cabang">Cabang:</label>
                  <select name="cabang" class="form-control" id="cabang">
                    <option value="0">-- Semua Cabang --</option>
                    <?php 
                      $queryCabang = "SELECT * FROM toko WHERE toko_status > 0 ORDER BY toko_nama ASC";
                      $cabangData = query($queryCabang);
                      foreach($cabangData as $cabang) :
                        $selected = (isset($_POST['cabang']) && $_POST['cabang'] == $cabang['toko_cabang']) ? 'selected' : '';
                    ?>
                    <option value="<?= $cabang['toko_cabang']; ?>" <?= $selected; ?>><?= $cabang['toko_nama']; ?> - <?= $cabang['toko_kota']; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <?php } ?>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" name="submit" class="btn btn-primary">Filter</button>
            <button type="submit" name="reset" class="btn btn-default">Reset</button>
            <?php if ( $tanggal_awal && $tanggal_akhir ) { ?>
            <button type="submit" name="excel" class="btn btn-success">Export Excel</button>
            <?php } ?>
          </div>
        </form>
      </div>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Data Rekap Penjualan Per Cabang</h3>
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
                    <th>Total Penjualan</th>
                    <th>Laba</th>
                    <th>Kasir</th>
                    <th style="text-align: center;">Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $i = 1; 
                    $queryFilter = "";
                    
                    if ( $tanggal_awal && $tanggal_akhir ) {
                      $queryFilter = " AND invoice_tgl BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
                    }

                    // Cabang Filter
                    $cabangFilter = "";
                    if(isset($_POST['cabang']) && $_POST['cabang'] != "" && $_POST['cabang'] != "0"){
                      $cabang = $_POST['cabang'];
                      $cabangFilter = " AND invoice_cabang = '$cabang'";
                    }

                    // Super Admin melihat semua cabang
                    if ( $levelLogin === "super admin" ) {
                      $cabangQuery = "SELECT toko.toko_nama, toko.toko_kota, toko.toko_cabang,
                                    COUNT(invoice.invoice_id) as total_transaksi,
                                    SUM(invoice.invoice_total) as total_penjualan,
                                    SUM(invoice.invoice_total - invoice.invoice_total_beli) as total_laba
                                    FROM toko 
                                    LEFT JOIN invoice ON toko.toko_cabang = invoice.invoice_cabang
                                    WHERE toko.toko_status > 0 $queryFilter $cabangFilter
                                    GROUP BY toko.toko_cabang
                                    ORDER BY total_penjualan DESC";
                      
                      $dataRekap = query($cabangQuery);

                    } else {
                      // Admin & Kasir hanya melihat cabang mereka
                      $cabangQuery = "SELECT toko.toko_nama, toko.toko_kota, toko.toko_cabang,
                                    COUNT(invoice.invoice_id) as total_transaksi,
                                    SUM(invoice.invoice_total) as total_penjualan,
                                    SUM(invoice.invoice_total - invoice.invoice_total_beli) as total_laba
                                    FROM toko 
                                    LEFT JOIN invoice ON toko.toko_cabang = invoice.invoice_cabang
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
                    <td>Rp. <?= number_format($row['total_penjualan']); ?></td>
                    <td>Rp. <?= number_format($row['total_laba']); ?></td>
                    <td>
                      <?php
                        // Get top 3 kasir berdasarkan jumlah transaksi
                        $cabangId = $row['toko_cabang'];
                        $kasirQuery = "SELECT user.user_nama, COUNT(invoice.invoice_id) as transaksi_count
                                      FROM invoice
                                      LEFT JOIN user ON invoice.invoice_kasir = user.user_id
                                      WHERE invoice.invoice_cabang = '$cabangId' $queryFilter
                                      GROUP BY invoice.invoice_kasir
                                      ORDER BY transaksi_count DESC
                                      LIMIT 3";
                        $kasirData = query($kasirQuery);
                        
                        foreach ($kasirData as $kasir) {
                          echo $kasir['user_nama'] . ' (' . number_format($kasir['transaksi_count']) . ')<br>';
                        }
                      ?>
                    </td>
                    <td class="text-center">
                      <?php if ($tanggal_awal && $tanggal_akhir) { ?>
                        <a href="terlaris" class="btn btn-success btn-sm">
                          <i class="fa fa-chart-bar"></i> Produk Terlaris
                        </a>
                      <?php } else { ?>
                        <a href="terlaris" class="btn btn-success btn-sm">
                          <i class="fa fa-chart-bar"></i> Produk Terlaris
                        </a>
                      <?php } ?>
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
                      $totalInvoice = 0;
                      $totalPenjualan = 0;
                      $totalLaba = 0;
                      
                      foreach($dataRekap as $row) {
                        $totalInvoice += $row['total_transaksi'];
                        $totalPenjualan += $row['total_penjualan'];
                        $totalLaba += $row['total_laba'];
                      }
                    ?>
                    <th><?= number_format($totalInvoice); ?></th>
                    <th>Rp. <?= number_format($totalPenjualan); ?></th>
                    <th>Rp. <?= number_format($totalLaba); ?></th>
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
              <h3 class="card-title">Grafik Penjualan Per Cabang</h3>
            </div>
            <div class="card-body">
              <canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Tren Penjualan Periode <?= date('d/m/Y', strtotime($tanggal_awal)); ?> - <?= date('d/m/Y', strtotime($tanggal_akhir)); ?></h3>
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
              <?= $row['total_penjualan']; ?>,
            <?php endforeach; ?>
          ],
          backgroundColor: [
            '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de',
            '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#6c757d', '#343a40'
          ],
        }
      ]
    }

    // Get Line Chart Data - Daily sales for date range
    <?php
      // Create date range array
      $begin = new DateTime($tanggal_awal);
      $end = new DateTime($tanggal_akhir);
      $end = $end->modify('+1 day');
      $interval = new DateInterval('P1D');
      $daterange = new DatePeriod($begin, $interval, $end);
      
      $dates = [];
      $dailySales = [];
      
      foreach ($daterange as $date) {
        $currentDate = $date->format("Y-m-d");
        $dates[] = $date->format("d/m/Y");
        
        // Get sales for all cabang on this date
        if ($levelLogin === "super admin" && isset($_POST['cabang']) && $_POST['cabang'] != "" && $_POST['cabang'] != "0") {
          $cabang = $_POST['cabang'];
          $dailySalesQuery = "SELECT SUM(invoice_total) as daily_total FROM invoice 
                              WHERE invoice_tgl = '$currentDate' AND invoice_cabang = '$cabang'";
        } else if ($levelLogin === "super admin") {
          $dailySalesQuery = "SELECT SUM(invoice_total) as daily_total FROM invoice 
                              WHERE invoice_tgl = '$currentDate'";
        } else {
          $dailySalesQuery = "SELECT SUM(invoice_total) as daily_total FROM invoice 
                              WHERE invoice_tgl = '$currentDate' AND invoice_cabang = '$sessionCabang'";
        }
        
        $dailySalesData = query($dailySalesQuery);
        $dailySales[] = $dailySalesData[0]['daily_total'] ? $dailySalesData[0]['daily_total'] : 0;
      }
    ?>
    
    var lineData = {
      labels: [<?= "'" . implode("','", $dates) . "'"; ?>],
      datasets: [
        {
          label: 'Total Penjualan',
          backgroundColor: 'rgba(60,141,188,0.9)',
          borderColor: 'rgba(60,141,188,0.8)',
          pointRadius: true,
          pointColor: '#3b8bba',
          pointStrokeColor: 'rgba(60,141,188,1)',
          pointHighlightFill: '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data: [<?= implode(",", $dailySales); ?>]
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