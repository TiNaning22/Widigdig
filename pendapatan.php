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
          <h1>Grafik Pertumbuhan Pendapatan</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Pertumbuhan Pendapatan</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <!-- Chart Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Grafik Pertumbuhan Pendapatan</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <!-- Meningkatkan tinggi chart container -->
            <div style="height: 500px; position: relative;">
              <canvas id="revenueChart"></canvas>
            </div>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
        
        <!-- Data Table Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Pertumbuhan Pendapatan</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="laporan-pertumbuhan-pendapatan" class="table table-bordered table-striped table-laporan">
                <thead>
                <tr>
                  <th style="width: 6%;">No.</th>
                  <th>Bulan</th>
                  <th>Tahun</th>
                  <th>Total Pendapatan</th>
                  <th>Pertumbuhan (%)</th>
                  <th>Jumlah Transaksi</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                      $i = 1;
                      // Modified query to use the properly formatted date column
                      $queryRevenue = $conn->query("SELECT 
                                                  MONTH(STR_TO_DATE(invoice_date, '%Y-%m-%d')) as bulan,
                                                  YEAR(STR_TO_DATE(invoice_date, '%Y-%m-%d')) as tahun,
                                                  SUM(invoice_total) as total_pendapatan,
                                                  COUNT(invoice_id) as jumlah_transaksi
                                                FROM invoice 
                                                WHERE invoice_cabang = '".$sessionCabang."' 
                                                GROUP BY YEAR(STR_TO_DATE(invoice_date, '%Y-%m-%d')), 
                                                         MONTH(STR_TO_DATE(invoice_date, '%Y-%m-%d'))
                                                ORDER BY tahun DESC, bulan DESC
                                                LIMIT 12");
                      
                      // Check if there are any results
                      if ($queryRevenue->num_rows > 0) {
                        $dataRevenue = array();
                        while ($rowRevenue = mysqli_fetch_array($queryRevenue)) {
                          $dataRevenue[] = $rowRevenue;
                        }
                        
                        // Calculate growth percentage
                        for ($j = 0; $j < count($dataRevenue); $j++) {
                          $currentRevenue = $dataRevenue[$j]['total_pendapatan'];
                          $growth = 0;
                          
                          if ($j < count($dataRevenue) - 1) {
                            $previousRevenue = $dataRevenue[$j + 1]['total_pendapatan'];
                            if ($previousRevenue > 0) {
                              $growth = (($currentRevenue - $previousRevenue) / $previousRevenue) * 100;
                            }
                          }
                          
                          $dataRevenue[$j]['growth'] = $growth;
                        }
                        
                        // Convert month number to name
                        function getMonthName($monthNum) {
                          $months = array(
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                          );
                          return isset($months[$monthNum]) ? $months[$monthNum] : 'Unknown';
                        }
                        
                        foreach ($dataRevenue as $row) {
                          // Make sure bulan is a valid numeric value
                          $bulan = intval($row['bulan']);
                          $monthName = getMonthName($bulan);
                    ?>
                    <tr>
                      <td><?= $i; ?></td>
                      <td><?= $monthName; ?></td>
                      <td><?= $row['tahun']; ?></td>
                      <td>Rp. <?= number_format($row['total_pendapatan'], 0, ',', '.'); ?></td>
                      <td><?= number_format($row['growth'], 2, ',', '.'); ?>%</td>
                      <td><?= $row['jumlah_transaksi']; ?></td>
                    </tr>
                    <?php 
                          $i++; 
                        }
                      } else {
                        // Display a message if no data found
                        echo '<tr><td colspan="6" class="text-center">Tidak ada data yang ditemukan</td></tr>';
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
<?php include '_footer.php'; ?>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<script>
  $(function () {
    // Initialize DataTable
    $("#laporan-pertumbuhan-pendapatan").DataTable();
    
    // Setup Chart
    var ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Prepare data for chart
    var months = [];
    var revenues = [];
    var growthData = [];
    
    <?php
    // Reverse the array to show oldest to newest in chart
    $chartData = array_reverse($dataRevenue);
    foreach ($chartData as $row) {
      echo "months.push('".getMonthName($row['bulan'])." ".$row['tahun']."');\n";
      echo "revenues.push(".$row['total_pendapatan'].");\n";
      echo "growthData.push(".$row['growth'].");\n";
    }
    ?>
    
    var revenueChart = new Chart(ctx, {
      type: 'bar', // Changed from 'line' to 'bar'
      data: {
        labels: months,
        datasets: [
          {
            label: 'Total Pendapatan (Rp)',
            data: revenues,
            backgroundColor: 'rgba(60, 141, 188, 0.7)', // Made more opaque for bar chart
            borderColor: 'rgba(60, 141, 188, 1)',
            borderWidth: 1,
            yAxisID: 'y-axis-1'
          },
          {
            label: 'Pertumbuhan (%)',
            data: growthData,
            backgroundColor: 'rgba(210, 214, 222, 0.7)', // Made more opaque for bar chart
            borderColor: 'rgba(210, 214, 222, 1)',
            borderWidth: 1,
            yAxisID: 'y-axis-2'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false,
        },
        stacked: false,
        scales: {
          yAxes: [
            {
              type: 'linear',
              display: true,
              position: 'left',
              id: 'y-axis-1',
              ticks: {
                beginAtZero: true,
                callback: function(value) {
                  return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
              },
              gridLines: {
                drawOnChartArea: true
              }
            },
            {
              type: 'linear',
              display: true,
              position: 'right',
              id: 'y-axis-2',
              ticks: {
                beginAtZero: true,
                callback: function(value) {
                  return value + '%';
                }
              },
              gridLines: {
                drawOnChartArea: false
              }
            }
          ],
          xAxes: [{
            gridLines: {
              drawOnChartArea: false
            },
            ticks: {
              maxRotation: 45,
              minRotation: 45
            },
            // Added barPercentage for better bar spacing
            barPercentage: 0.8,
            categoryPercentage: 0.9
          }]
        },
        tooltips: {
          callbacks: {
            label: function(tooltipItem, data) {
              var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
              if (tooltipItem.datasetIndex === 0) {
                return datasetLabel + ': Rp ' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
              } else {
                return datasetLabel + ': ' + tooltipItem.yLabel.toFixed(2) + '%';
              }
            }
          }
        }
      }
    });
  });
</script>
</body>
</html>