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
            <h1>Laporan Laba Rugi</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Laba Rugi</li>
            </ol>
          </div>
        </div><br>
        <div class="callout callout-info">
          <h5><i class="fas fa-info"></i> Note:</h5>
          Laporan ini menampilkan ringkasan kinerja keuangan perusahaan dalam periode tertentu. Pastikan data transaksi penjualan dan pembelian sudah lengkap dan akurat.
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Filter Data Berdasarkan Tanggal</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
            </div>
          </div>
          <!-- /.card-header -->
          <form role="form" action="laporan-labarugi-detail" method="POST" target="_blank">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="form-control" id="tanggal_awal" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" id="tanggal_akhir" required>
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="format_laporan">Format Laporan</label>
                    <select name="format_laporan" class="form-control" id="format_laporan" required>
                      <option value="detail">Detail</option>
                      <option value="ringkas">Ringkas</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="tipe_pembayaran">Tipe Pembayaran</label>
                    <select name="tipe_pembayaran" class="form-control" id="tipe_pembayaran">
                      <option value="semua">Semua</option>
                      <option value="tunai">Tunai</option>
                      <option value="kredit">Kredit</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="card-footer text-right">
                <button type="submit" name="submit" class="btn btn-primary">
                  <i class="fa fa-filter"></i> Filter
                </button>
                <a href="pendapatan-lababersih" class="btn btn-secondary">
                  <i class="fa fa-chart-bar"></i> Dashboard Laporan
                </a>
              </div>
            </div>
          </form>
        </div>

        <!-- Recent Transactions Summary Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Ringkasan Transaksi Terbaru (7 Hari Terakhir)</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <?php
              // Set default timezone
              date_default_timezone_set('Asia/Jakarta');
              
              // Calculate date for 7 days ago
              $tanggal_akhir = date('Y-m-d');
              $tanggal_awal = date('Y-m-d', strtotime('-7 days'));
              
              // Query to get total sales
              $totalPenjualan = 0;
              $queryPenjualan = $conn->query("SELECT SUM(invoice_sub_total) as total_penjualan 
                FROM invoice 
                WHERE invoice_cabang = '".$sessionCabang."' 
                AND invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' 
                AND invoice_draft = 0");
              $rowPenjualan = mysqli_fetch_assoc($queryPenjualan);
              $totalPenjualan = $rowPenjualan['total_penjualan'] ?: 0;
              
              // Query to get total purchases
              $totalPembelian = 0;
              $queryPembelian = $conn->query("SELECT SUM(invoice_total) as total_pembelian 
                FROM invoice_pembelian 
                WHERE invoice_pembelian_cabang = '".$sessionCabang."' 
                AND invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'");
              $rowPembelian = mysqli_fetch_assoc($queryPembelian);
              $totalPembelian = $rowPembelian['total_pembelian'] ?: 0;
              
              // Query to get total HPP
              $totalHpp = 0;
              $queryHpp = $conn->query("SELECT SUM(invoice_total_beli) as total_hpp 
                FROM invoice 
                WHERE invoice_cabang = '".$sessionCabang."' 
                AND invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'");
              $rowHpp = mysqli_fetch_assoc($queryHpp);
              $totalHpp = $rowHpp['total_hpp'] ?: 0;
              
              // Calculate gross profit
              $labaKotor = $totalPenjualan - $totalHpp;
            ?>
            
            <div class="row">
              <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                  <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Total Penjualan</span>
                    <span class="info-box-number">Rp <?= number_format($totalPenjualan, 0, ',', '.'); ?></span>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                  <span class="info-box-icon bg-success"><i class="fas fa-chart-line"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Laba Kotor</span>
                    <span class="info-box-number">Rp <?= number_format($labaKotor, 0, ',', '.'); ?></span>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                  <span class="info-box-icon bg-warning"><i class="fas fa-tags"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">HPP</span>
                    <span class="info-box-number">Rp <?= number_format($totalHpp, 0, ',', '.'); ?></span>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                  <span class="info-box-icon bg-danger"><i class="fas fa-truck"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Total Pembelian</span>
                    <span class="info-box-number">Rp <?= number_format($totalPembelian, 0, ',', '.'); ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Create the detail page handler -->
  <?php 
    // Create new file for report detail view if it doesn't exist
    $detailFile = 'laporan-labarugi-detail.php';
    if (!file_exists($detailFile)) {
      $detailContent = '<?php 
  include \'_header.php\'; 
?>
<?php  
  if ( $levelLogin !== "super admin") {
    echo "
      <script>
        document.location.href = \'bo\';
      </script>
    ";
  }  
?>

<?php  
  $tanggal_awal = $_POST[\'tanggal_awal\'];
  $tanggal_akhir = $_POST[\'tanggal_akhir\'];
  $format_laporan = $_POST[\'format_laporan\'];
  $tipe_pembayaran = $_POST[\'tipe_pembayaran\'];
?>

<?php  
    $toko = query("SELECT * FROM toko WHERE toko_cabang = $sessionCabang");
?>
<?php foreach ( $toko as $row ) : ?>
    <?php 
      $toko_nama = $row[\'toko_nama\'];
      $toko_kota = $row[\'toko_kota\'];
      $toko_tlpn = $row[\'toko_tlpn\'];
      $toko_wa   = $row[\'toko_wa\'];
      $toko_print= $row[\'toko_print\']; 
    ?>
<?php endforeach; ?>

<!-- Total penjualan -->
<?php  
    $totalPenjualan = 0;
    $whereClause = "invoice_cabang = \'".$sessionCabang."\' && invoice_date BETWEEN \'".$tanggal_awal."\' AND \'".$tanggal_akhir."\' && invoice_draft = 0";
    
    if ($tipe_pembayaran != \'semua\') {
      if ($tipe_pembayaran == \'tunai\') {
        $whereClause .= " && invoice_piutang = 0";
      } else {
        $whereClause .= " && invoice_piutang = 1";
      }
    }
    
    $queryInvoice = $conn->query("SELECT invoice.invoice_id, invoice.invoice_date, invoice.invoice_cabang, invoice.invoice_total_beli, invoice.invoice_sub_total, invoice.penjualan_invoice
        FROM invoice 
        WHERE ".$whereClause."
      ");
    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
    $totalPenjualan += $rowProduct[\'invoice_sub_total\'];
  ?>
<?php } ?>
<!-- End Total penjualan  -->

<!-- Total HPP -->
<?php  
    $totalHpp = 0;
    $queryInvoice = $conn->query("SELECT invoice.invoice_id, invoice.invoice_date, invoice.invoice_cabang, invoice.invoice_total_beli, invoice.invoice_sub_total, invoice.penjualan_invoice
        FROM invoice 
        WHERE ".$whereClause."
      ");
    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
    $totalHpp += $rowProduct[\'invoice_total_beli\'];
  ?>
<?php } ?>
<!-- End Total HPP -->

<!-- Get operational data -->
<?php  
  $labaBersih = query("SELECT * FROM laba_bersih WHERE lb_cabang = $sessionCabang");
?>
<?php foreach ( $labaBersih as $row ) : ?>
    <?php 
      $lb_pendapatan_lain                 = $row[\'lb_pendapatan_lain\'];
      $lb_pengeluaran_gaji                = $row[\'lb_pengeluaran_gaji\'];
      $lb_pengeluaran_listrik             = $row[\'lb_pengeluaran_listrik\'];
      $lb_pengeluaran_tlpn_internet       = $row[\'lb_pengeluaran_tlpn_internet\'];
      $lb_pengeluaran_perlengkapan_toko   = $row[\'lb_pengeluaran_perlengkapan_toko\']; 
      $lb_pengeluaran_biaya_penyusutan    = $row[\'lb_pengeluaran_biaya_penyusutan\'];
      $lb_pengeluaran_bensin              = $row[\'lb_pengeluaran_bensin\'];
      $lb_pengeluaran_tak_terduga         = $row[\'lb_pengeluaran_tak_terduga\'];
      $lb_pengeluaran_lain                = $row[\'lb_pengeluaran_lain\']; 
    ?>
<?php endforeach; ?>

    <section class="laporan-laba-bersih">
        <div class="container">
            <div class="llb-header">
                  <div class="llb-header-parent">
                    <?= $toko_nama; ?>
                  </div>
                  <div class="llb-header-address">
                    <?= $toko_kota; ?>
                  </div>
                  <div class="llb-header-contact">
                    <ul>
                        <li><b>No.tlpn:</b> <?= $toko_tlpn; ?></li>&nbsp;&nbsp;
                        <li><b>Wa:</b> <?= $toko_wa; ?></li>
                    </ul>
                  </div>
              </div>

              <div class="laporan-laba-bersih-detail">
                  <div class="llbd-title">
                      Laporan LABA RUGI Periode <?= tanggal_indo($tanggal_awal); ?> - <?= tanggal_indo($tanggal_akhir); ?>
                      <?php if ($tipe_pembayaran != \'semua\') echo " - Pembayaran " . ucfirst($tipe_pembayaran); ?>
                  </div>
                  <table class="table">
                    <thead>
                      <tr>
                        <th colspan="2">1. Pendapatan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>a. Penjualan</td>
                        <td>Rp <?= number_format($totalPenjualan, 0, \',\', \'.\'); ?></td>
                      </tr>
                      <?php if ($format_laporan == \'detail\') : ?>
                      <tr>
                        <td>b. Pendapatan Lain</td>
                        <td>Rp <?= number_format($lb_pendapatan_lain, 0, \',\', \'.\'); ?></td>
                      </tr>
                      <?php endif; ?>
                      <tr>
                        <td><b>Total Pendapatan</b></td>
                        <td>
                            <?php  
                              $totalPendapatan = $totalPenjualan + $lb_pendapatan_lain;
                              echo "<b>Rp ".number_format($totalPendapatan, 0, \',\', \'.\')."</b>";
                            ?> 
                        </td>
                      </tr>

                      <tr>
                        <th colspan="2">2. Harga Pokok Penjualan</th>
                      </tr>
                      <tr>
                        <td>a. HPP (Harga Pokok Penjualan)</td>
                        <td>Rp <?= number_format($totalHpp, 0, \',\', \'.\'); ?></td>
                      </tr>
                      <tr>
                        <td><b>Laba Kotor</b></td>
                        <td>
                            <?php  
                              $labaKotor = $totalPendapatan - $totalHpp;
                              echo "<b>Rp ".number_format($labaKotor, 0, \',\', \'.\')."</b>";
                            ?>
                        </td>
                      </tr>

                      <?php if ($format_laporan == \'detail\') : ?>
                      <tr>
                        <th colspan="2">3. Biaya Operasional</th>
                      </tr>
                      <tr>
                        <td>a. Gaji Pegawai</td>
                        <td>Rp <?= number_format($lb_pengeluaran_gaji, 0, \',\', \'.\'); ?></td>
                      </tr>
                      <tr>
                        <td>b. Biaya Listrik</td>
                        <td>Rp <?= number_format($lb_pengeluaran_listrik, 0, \',\', \'.\'); ?></td>
                      </tr>
                      <tr>
                        <td>c. Telepon & Internet</td>
                        <td>Rp <?= number_format($lb_pengeluaran_tlpn_internet, 0, \',\', \'.\'); ?></td>
                      </tr>
                      <tr>
                        <td>d. Perlengkapan Toko</td>
                        <td>Rp <?= number_format($lb_pengeluaran_perlengkapan_toko, 0, \',\', \'.\'); ?></td>
                      </tr>
                      <tr>
                        <td>e. Biaya Penyusutan</td>
                        <td>Rp <?= number_format($lb_pengeluaran_biaya_penyusutan, 0, \',\', \'.\'); ?></td>
                      </tr>
                      <tr>
                        <td>f. Transportasi & Bensin</td>
                        <td>Rp <?= number_format($lb_pengeluaran_bensin, 0, \',\', \'.\'); ?></td>
                      </tr>
                      <tr>
                        <td>g. Biaya Tak Terduga</td>
                        <td>Rp <?= number_format($lb_pengeluaran_tak_terduga, 0, \',\', \'.\'); ?></td>
                      </tr>
                      <tr>
                        <td>h. Pengeluaran Lain</td>
                        <td>Rp <?= number_format($lb_pengeluaran_lain, 0, \',\', \'.\'); ?></td>
                      </tr>
                      <tr>
                        <td><b>Total Biaya Operasional</b></td>
                        <td>
                            <?php  
                              $totalBiayaOperasional = $lb_pengeluaran_gaji + $lb_pengeluaran_listrik + $lb_pengeluaran_tlpn_internet + $lb_pengeluaran_perlengkapan_toko + $lb_pengeluaran_biaya_penyusutan + $lb_pengeluaran_bensin + $lb_pengeluaran_tak_terduga + $lb_pengeluaran_lain;
                              echo "<b>Rp ".number_format($totalBiayaOperasional, 0, \',\', \'.\' )."</b>";
                            ?>
                        </td>
                      </tr>
                      <?php else: ?>
                      <tr>
                        <th colspan="2">3. Biaya Operasional</th>
                      </tr>
                      <tr>
                        <td><b>Total Biaya Operasional</b></td>
                        <td>
                            <?php  
                              $totalBiayaOperasional = $lb_pengeluaran_gaji + $lb_pengeluaran_listrik + $lb_pengeluaran_tlpn_internet + $lb_pengeluaran_perlengkapan_toko + $lb_pengeluaran_biaya_penyusutan + $lb_pengeluaran_bensin + $lb_pengeluaran_tak_terduga + $lb_pengeluaran_lain;
                              echo "<b>Rp ".number_format($totalBiayaOperasional, 0, \',\', \'.\' )."</b>";
                            ?>
                        </td>
                      </tr>
                      <?php endif; ?>
                      
                      <tr>
                        <th>Laba Bersih</th>
                        <th>
                            <?php  
                                $labaBersih = $labaKotor - $totalBiayaOperasional;
                                echo "Rp ".number_format($labaBersih, 0, \',\', \'.\');
                            ?>
                        </th>
                      </tr>
                      <tr>
                        <th>Rasio Laba Bersih (%)</th>
                        <th>
                            <?php  
                                $rasioLabaBersih = ($totalPendapatan > 0) ? ($labaBersih / $totalPendapatan) * 100 : 0;
                                echo number_format($rasioLabaBersih, 2, \',\', \'.\') . "%";
                            ?>
                        </th>
                      </tr>
                    </tbody>
                  </table>
              </div>

              <div class="text-center">
                © <?= date("Y"); ?> Copyright www.senimankoding.com All rights reserved.
              </div>
        </div>
    </section>

</body>
</html>
<script>
  window.print();
</script>';
      
      file_put_contents($detailFile, $detailContent);
    }
  ?>

<?php include '_footer.php'; ?>

<!-- Page-specific scripts -->
<script>
$(function () {
  // Set default dates
  var today = new Date();
  var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
  
  // Format dates as YYYY-MM-DD
  var formatDate = function(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
  };
  
  // Set default values for date inputs
  $('#tanggal_awal').val(formatDate(firstDay));
  $('#tanggal_akhir').val(formatDate(today));
  
  // Initialize datepicker if available
  if ($.fn.datepicker) {
    $('#tanggal_awal, #tanggal_akhir').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true
    });
  }
});
</script>
</body>
</html>