<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin !== "super admin") {
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
          <h1>Grafik Pertumbuhan Laba Bersih</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Pertumbuhan Laba Bersih</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <!-- Date Range Filter Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Filter Data</h3>
          </div>
          <div class="card-body">
            <form action="" method="POST">
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label for="tanggal_awal">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <label for="tanggal_akhir">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group" style="margin-top: 32px;">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
        
        <?php
        // Process data if form is submitted
        if(isset($_POST['tanggal_awal']) && isset($_POST['tanggal_akhir'])) {
          $tanggal_awal = $_POST['tanggal_awal'];
          $tanggal_akhir = $_POST['tanggal_akhir'];
          
          // Get monthly data between the selected dates
          $monthly_data = array();
          $current_date = new DateTime($tanggal_awal);
          $end_date = new DateTime($tanggal_akhir);
          $end_date->modify('+1 month'); // Include the end month
          
          while($current_date < $end_date) {
            $year_month = $current_date->format('Y-m');
            $month_start = $year_month . '-01';
            $month_end = $current_date->format('Y-m-t');
            
            // For each month, calculate laba bersih
            // Total penjualan for the month
            $totalPenjualan = 0;
            $queryInvoice = $conn->query("SELECT SUM(invoice_sub_total) as total
              FROM invoice 
              WHERE invoice_cabang = '".$sessionCabang."' && invoice_piutang = 0 && invoice_piutang_lunas = 0 
              && invoice_date BETWEEN '".$month_start."' AND '".$month_end."' && invoice_draft = 0
            ");
            $row = mysqli_fetch_assoc($queryInvoice);
            $totalPenjualan = $row['total'] ? $row['total'] : 0;
            
            // Total HPP for the month
            $totalHpp = 0;
            $queryInvoice = $conn->query("SELECT SUM(invoice_total_beli) as total
              FROM invoice 
              WHERE invoice_cabang = '".$sessionCabang."' && invoice_piutang = 0 
              && invoice_date BETWEEN '".$month_start."' AND '".$month_end."'
            ");
            $row = mysqli_fetch_assoc($queryInvoice);
            $totalHpp = $row['total'] ? $row['total'] : 0;
            
            // Total Piutang Cicilan for the month
            $totalPiutang = 0;
            $queryInvoice = $conn->query("SELECT SUM(piutang_nominal) as total
              FROM piutang 
              WHERE piutang_cabang = '".$sessionCabang."' 
              && piutang_date BETWEEN '".$month_start."' AND '".$month_end."'
            ");
            $row = mysqli_fetch_assoc($queryInvoice);
            $totalPiutang = $row['total'] ? $row['total'] : 0;
            
            // Total Piutang Kembalian for the month
            $totalPiutangKembalian = 0;
            $queryInvoice = $conn->query("SELECT SUM(pl_nominal) as total
              FROM piutang_kembalian 
              WHERE pl_cabang = '".$sessionCabang."' 
              && pl_date BETWEEN '".$month_start."' AND '".$month_end."'
            ");
            $row = mysqli_fetch_assoc($queryInvoice);
            $totalPiutangKembalian = $row['total'] ? $row['total'] : 0;
            
            // Piutang = Total Piutang - Total Piutang Kembalian
            $piutang = $totalPiutang - $totalPiutangKembalian;
            
            // Total DP Piutang for the month
            $totalDp = 0;
            $queryInvoice = $conn->query("SELECT SUM(invoice_piutang_dp) as total
              FROM invoice 
              WHERE invoice_cabang = '".$sessionCabang."' 
              && invoice_date BETWEEN '".$month_start."' AND '".$month_end."'
            ");
            $row = mysqli_fetch_assoc($queryInvoice);
            $totalDp = $row['total'] ? $row['total'] : 0;
            
            // Total Hutang Cicilan for the month
            $totalHutang = 0;
            $queryInvoice = $conn->query("SELECT SUM(hutang_nominal) as total
              FROM hutang 
              WHERE hutang_cabang = '".$sessionCabang."' 
              && hutang_date BETWEEN '".$month_start."' AND '".$month_end."'
            ");
            $row = mysqli_fetch_assoc($queryInvoice);
            $totalHutang = $row['total'] ? $row['total'] : 0;
            
            // Total Hutang Kembalian for the month
            $totalHutangKembalian = 0;
            $queryInvoice = $conn->query("SELECT SUM(hl_nominal) as total
              FROM hutang_kembalian 
              WHERE hl_cabang = '".$sessionCabang."' 
              && hl_date BETWEEN '".$month_start."' AND '".$month_end."'
            ");
            $row = mysqli_fetch_assoc($queryInvoice);
            $totalHutangKembalian = $row['total'] ? $row['total'] : 0;
            
            // Hutang = Total Hutang - Total Hutang Kembalian
            $hutang = $totalHutang - $totalHutangKembalian;
            
            // Total DP Hutang for the month
            $totalDpHutang = 0;
            $queryInvoice = $conn->query("SELECT SUM(invoice_hutang_dp) as total
              FROM invoice_pembelian 
              WHERE invoice_pembelian_cabang = '".$sessionCabang."' 
              && invoice_date BETWEEN '".$month_start."' AND '".$month_end."'
            ");
            $row = mysqli_fetch_assoc($queryInvoice);
            $totalDpHutang = $row['total'] ? $row['total'] : 0;
            
            // Total Pembelian for the month
            $totalPembelian = 0;
            $queryInvoice = $conn->query("SELECT SUM(invoice_total) as total
              FROM invoice_pembelian 
              WHERE invoice_pembelian_cabang = '".$sessionCabang."' && invoice_hutang = 0 && invoice_hutang_lunas = 0 
              && invoice_date BETWEEN '".$month_start."' AND '".$month_end."'
            ");
            $row = mysqli_fetch_assoc($queryInvoice);
            $totalPembelian = $row['total'] ? $row['total'] : 0;
            
            // Get laba bersih data
            $labaBersih = query("SELECT * FROM laba_bersih WHERE lb_cabang = $sessionCabang");
            $lb_pendapatan_lain = $labaBersih[0]['lb_pendapatan_lain'];
            $lb_pengeluaran_gaji = $labaBersih[0]['lb_pengeluaran_gaji'];
            $lb_pengeluaran_listrik = $labaBersih[0]['lb_pengeluaran_listrik'];
            $lb_pengeluaran_tlpn_internet = $labaBersih[0]['lb_pengeluaran_tlpn_internet'];
            $lb_pengeluaran_perlengkapan_toko = $labaBersih[0]['lb_pengeluaran_perlengkapan_toko'];
            $lb_pengeluaran_biaya_penyusutan = $labaBersih[0]['lb_pengeluaran_biaya_penyusutan'];
            $lb_pengeluaran_bensin = $labaBersih[0]['lb_pengeluaran_bensin'];
            $lb_pengeluaran_tak_terduga = $labaBersih[0]['lb_pengeluaran_tak_terduga'];
            $lb_pengeluaran_lain = $labaBersih[0]['lb_pengeluaran_lain'];
            
            // Calculate total pendapatan
            $totalPendapatan = $totalPenjualan + $piutang + $totalDp + $lb_pendapatan_lain;
            
            // Calculate laba rugi kotor
            $labaRugiKotor = $totalPendapatan - $totalHpp;
            
            // Calculate total biaya pengeluaran
            $totalBiayaPengeluaran = $lb_pengeluaran_gaji + $lb_pengeluaran_listrik + $lb_pengeluaran_tlpn_internet + 
                                    $lb_pengeluaran_perlengkapan_toko + $lb_pengeluaran_biaya_penyusutan + 
                                    $lb_pengeluaran_bensin + $lb_pengeluaran_tak_terduga + $lb_pengeluaran_lain + 
                                    $hutang + $totalDpHutang + $totalPembelian;
            
            // Calculate laba bersih
            $laba_bersih = $labaRugiKotor - $totalBiayaPengeluaran;
            
            // Store data for the month
            $monthly_data[] = array(
              'month' => $current_date->format('M Y'),
              'month_num' => $current_date->format('n'),
              'year' => $current_date->format('Y'),
              'laba_bersih' => $laba_bersih,
              'pendapatan' => $totalPendapatan,
              'hpp' => $totalHpp,
              'pengeluaran' => $totalBiayaPengeluaran
            );
            
            // Move to next month
            $current_date->modify('+1 month');
          }
          
          // Calculate growth rate
          for ($i = 0; $i < count($monthly_data); $i++) {
            if ($i > 0) {
              $current = $monthly_data[$i]['laba_bersih'];
              $previous = $monthly_data[$i-1]['laba_bersih'];
              $growth = 0;
              
              if ($previous != 0) {
                $growth = (($current - $previous) / abs($previous)) * 100;
              }
              
              $monthly_data[$i]['growth'] = $growth;
            } else {
              $monthly_data[$i]['growth'] = 0; // First month has no growth rate
            }
          }
        ?>
        
        <!-- Chart Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Grafik Pertumbuhan Laba Bersih</h3>
          </div>
          <div class="card-body">
            <div style="height: 500px; position: relative;">
              <canvas id="labaBersihChart"></canvas>
            </div>
          </div>
        </div>
        
        <!-- Data Table Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Laba Bersih</h3>
          </div>
          <div class="card-body">
            <div class="table-auto">
              <table id="tabel-laba-bersih" class="table table-bordered table-striped table-laporan">
                <thead>
                <tr>
                  <th style="width: 6%;">No.</th>
                  <th>Bulan</th>
                  <th>Pendapatan</th>
                  <th>HPP</th>
                  <th>Biaya Pengeluaran</th>
                  <th>Laba Bersih</th>
                  <th>Pertumbuhan (%)</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                  $i = 1;
                  foreach ($monthly_data as $row) {
                ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $row['month']; ?></td>
                  <td>Rp. <?= number_format($row['pendapatan'], 0, ',', '.'); ?></td>
                  <td>Rp. <?= number_format($row['hpp'], 0, ',', '.'); ?></td>
                  <td>Rp. <?= number_format($row['pengeluaran'], 0, ',', '.'); ?></td>
                  <td>Rp. <?= number_format($row['laba_bersih'], 0, ',', '.'); ?></td>
                  <td><?= $i > 1 ? number_format($row['growth'], 2, ',', '.') . '%' : '-'; ?></td>
                </tr>
                <?php $i++; ?>
                <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <script src="plugins/chart.js/Chart.min.js"></script>
        <script>
          $(function () {
            // Initialize DataTable
            $("#tabel-laba-bersih").DataTable();
            
            // Setup Chart
            var ctx = document.getElementById('labaBersihChart').getContext('2d');
            
            // Prepare data for chart
            var months = [];
            var labaBersih = [];
            var pendapatan = [];
            var pengeluaran = [];
            var growthData = [];
            
            <?php
            foreach ($monthly_data as $row) {
              echo "months.push('".$row['month']."');\n";
              echo "labaBersih.push(".$row['laba_bersih'].");\n";
              echo "pendapatan.push(".$row['pendapatan'].");\n";
              echo "pengeluaran.push(".$row['pengeluaran'].");\n";
              echo "growthData.push(".$row['growth'].");\n";
            }
            ?>
            
            var labaBersihChart = new Chart(ctx, {
              type: 'bar',
              data: {
                labels: months,
                datasets: [
                  {
                    label: 'Laba Bersih (Rp)',
                    data: labaBersih,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    yAxisID: 'y-axis-1'
                  },
                  {
                    label: 'Pertumbuhan (%)',
                    data: growthData,
                    type: 'line',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    fill: false,
                    yAxisID: 'y-axis-2'
                  },
                  {
                    label: 'Pendapatan (Rp)',
                    data: pendapatan,
                    backgroundColor: 'rgba(54, 162, 235, 0.3)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    hidden: true,
                    yAxisID: 'y-axis-1'
                  },
                  {
                    label: 'Pengeluaran (Rp)',
                    data: pengeluaran,
                    backgroundColor: 'rgba(255, 99, 132, 0.3)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    hidden: true,
                    yAxisID: 'y-axis-1'
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
                        beginAtZero: false,
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
                        beginAtZero: false,
                        callback: function(value) {
                          return value.toFixed(1) + '%';
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
                    }
                  }]
                },
                tooltips: {
                  callbacks: {
                    label: function(tooltipItem, data) {
                      var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                      if (tooltipItem.datasetIndex === 1) {
                        return datasetLabel + ': ' + tooltipItem.yLabel.toFixed(2) + '%';
                      } else {
                        return datasetLabel + ': Rp ' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                      }
                    }
                  }
                },
                legend: {
                  position: 'top',
                  onClick: function(e, legendItem) {
                    var index = legendItem.datasetIndex;
                    var ci = this.chart;
                    var meta = ci.getDatasetMeta(index);
                    
                    // We allow hiding the growth line but keep laba bersih always visible
                    if (index === 0) {
                      return;
                    }
                    
                    meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;
                    ci.update();
                  }
                }
              }
            });
          });
        </script>
        <?php } // End of if(isset($_POST... ?>
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
</body>
</html>