<?php 
  include '_header.php'; 
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

<?php  
  $tanggal_awal = $_POST['tanggal_awal'];
  $tanggal_akhir = $_POST['tanggal_akhir'];
?>

<?php  
    $toko = query("SELECT * FROM toko WHERE toko_cabang = $sessionCabang");
?>
<?php foreach ( $toko as $row ) : ?>
    <?php 
      $toko_nama = $row['toko_nama'];
      $toko_kota = $row['toko_kota'];
      $toko_tlpn = $row['toko_tlpn'];
      $toko_wa   = $row['toko_wa'];
      $toko_print= $row['toko_print']; 
    ?>
<?php endforeach; ?>


<!-- Total penjualan -->
<?php  
    $totalPenjualan = 0;
      $queryInvoice = $conn->query("SELECT invoice.invoice_id, invoice.invoice_date, invoice.invoice_cabang, invoice.invoice_total_beli, invoice.invoice_sub_total, invoice.penjualan_invoice
        FROM invoice 
        WHERE invoice_cabang = '".$sessionCabang."' && invoice_piutang = 0 && invoice_piutang_lunas = 0 && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && invoice_draft = 0
      ");
    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
    $totalPenjualan += $rowProduct['invoice_sub_total'];
  ?>
<?php } ?>
<!-- End Total penjualan  -->


<!-- Total HPP -->
<?php  
    $totalHpp       = 0;
      $queryInvoice = $conn->query("SELECT invoice.invoice_id, invoice.invoice_date, invoice.invoice_cabang, invoice.invoice_total_beli, invoice.invoice_sub_total, invoice.penjualan_invoice
        FROM invoice 
        WHERE invoice_cabang = '".$sessionCabang."' && invoice_piutang = 0 && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
      ");
    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
    $totalHpp += $rowProduct['invoice_total_beli'];
  ?>
<?php } ?>
<!-- End Total HPP -->



<!-- Total Piutang Cicilan -->
<?php  
    $totalPiutang = 0;
      $queryInvoice = $conn->query("SELECT piutang.piutang_id, piutang.piutang_date, piutang.piutang_nominal, piutang.piutang_cabang
        FROM piutang 
        WHERE piutang_cabang = '".$sessionCabang."' && piutang_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
      ");
    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
    $totalPiutang += $rowProduct['piutang_nominal'];
  ?>
<?php } ?>
<!-- End Total Piutang Cicilan -->

<!-- Total Piutang Kembalian -->
<?php  
    $totalPiutangKembalian = 0;
    $queryInvoice = $conn->query("SELECT piutang_kembalian.pl_id, piutang_kembalian.pl_date, piutang_kembalian.pl_nominal, piutang_kembalian.pl_cabang
        FROM piutang_kembalian 
        WHERE pl_cabang = '".$sessionCabang."' && pl_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
      ");
    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
    $totalPiutangKembalian += $rowProduct['pl_nominal'];
  ?>
<?php } ?>
<!-- End Total Piutang Kembalian -->

<!-- Piutang = Total Piutang - Total Piutang Kembalian -->
<?php  
  $piutang = $totalPiutang - $totalPiutangKembalian;
?>
<!-- End Piutang = Total Piutang - Total Piutang Kembalian -->

<!-- Total DP Piutang-->
<?php  
    $totalDp = 0;
      $queryInvoice = $conn->query("SELECT invoice.invoice_id, invoice.invoice_date, invoice.invoice_cabang, invoice.invoice_piutang_dp, invoice.penjualan_invoice
        FROM invoice 
        WHERE invoice_cabang = '".$sessionCabang."' && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
      ");
    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
    $totalDp += $rowProduct['invoice_piutang_dp'];
  ?>
<?php } ?>
<!-- End Total DP Piutang -->



<!-- Total Hutang Cicilan -->
<?php  
    $totalHutang = 0;
      $queryInvoice = $conn->query("SELECT hutang.hutang_id, hutang.hutang_date, hutang.hutang_nominal, hutang.hutang_cabang
        FROM hutang 
        WHERE hutang_cabang = '".$sessionCabang."' && hutang_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
      ");
    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
    $totalHutang += $rowProduct['hutang_nominal'];
  ?>
<?php } ?>
<!-- End Total Hutang Cicilan -->

<!-- Total Hutang Kembalian -->
<?php  
    $totalHutangKembalian = 0;
    $queryInvoice = $conn->query("SELECT hutang_kembalian.hl_id, hutang_kembalian.hl_date, hutang_kembalian.hl_nominal, hutang_kembalian.hl_cabang
        FROM hutang_kembalian 
        WHERE hl_cabang = '".$sessionCabang."' && hl_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
      ");
    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
    $totalHutangKembalian += $rowProduct['hl_nominal'];
  ?>
<?php } ?>
<!-- End Total Hutang Kembalian -->

<!-- Hutang = Total Hutang - Total Hutang Kembalian -->
<?php  
  $hutang = $totalHutang - $totalHutangKembalian;
?>
<!-- End Hutang = Total Hutang - Total Hutang Kembalian -->

<!-- Total DP Hutang-->
<?php  
    $totalDpHutang = 0;
      $queryInvoice = $conn->query("SELECT invoice_pembelian.invoice_pembelian_id, invoice_pembelian.invoice_date, invoice_pembelian.invoice_pembelian_cabang, invoice_pembelian.invoice_hutang_dp, invoice_pembelian.pembelian_invoice_parent
        FROM invoice_pembelian 
        WHERE invoice_pembelian_cabang = '".$sessionCabang."' && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
      ");
    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
    $totalDpHutang += $rowProduct['invoice_hutang_dp'];
  ?>
<?php } ?>
<!-- End Total DP Hutang -->

<!-- Total Pembelian -->
<?php  
    $totalPembelian = 0;
      $queryInvoice = $conn->query("SELECT invoice_pembelian.invoice_pembelian_id, invoice_pembelian.invoice_date, invoice_pembelian.invoice_pembelian_cabang, invoice_pembelian.invoice_total, invoice_pembelian.pembelian_invoice_parent
        FROM invoice_pembelian 
        WHERE invoice_pembelian_cabang = '".$sessionCabang."' && invoice_hutang = 0 && invoice_hutang_lunas = 0 && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
      ");
    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
    $totalPembelian += $rowProduct['invoice_total'];
  ?>
<?php } ?>
<!-- End Total Pembelian  -->


<?php  
  $labaBersih = query("SELECT * FROM laba_bersih WHERE lb_cabang = $sessionCabang");
?>
<?php foreach ( $labaBersih as $row ) : ?>
    <?php 
      $lb_pendapatan_lain                 = $row['lb_pendapatan_lain'];
      $lb_pengeluaran_gaji                = $row['lb_pengeluaran_gaji'];
      $lb_pengeluaran_listrik             = $row['lb_pengeluaran_listrik'];
      $lb_pengeluaran_tlpn_internet       = $row['lb_pengeluaran_tlpn_internet'];
      $lb_pengeluaran_perlengkapan_toko   = $row['lb_pengeluaran_perlengkapan_toko']; 
      $lb_pengeluaran_biaya_penyusutan    = $row['lb_pengeluaran_biaya_penyusutan'];
      $lb_pengeluaran_bensin              = $row['lb_pengeluaran_bensin'];
      $lb_pengeluaran_tak_terduga         = $row['lb_pengeluaran_tak_terduga'];
      $lb_pengeluaran_lain                = $row['lb_pengeluaran_lain']; 
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
                      Laporan LABA BERSIH Periode <?= tanggal_indo($tanggal_awal); ?> - <?= tanggal_indo($tanggal_akhir); ?>
                  </div>
                  <table class="table">
                    <thead>
                      <tr>
                        <th colspan="2">1. Pendapatan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>a. Sub Total Penjualan</td>
                        <td>Rp <?= number_format($totalPenjualan, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>b. DP Piutang</td>
                        <td>Rp <?= number_format($totalDp, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>c. Piutang (Cicilan)</td>
                        <td>Rp <?= number_format($piutang, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>d. Pendapatan Lain</td>
                        <td>Rp <?= number_format($lb_pendapatan_lain, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td><b>Total Pendapatan</b></td>
                        <td>
                            <?php  
                              $totalPendapatan = $totalPenjualan + $piutang + $totalDp + $lb_pendapatan_lain;
                              echo "<b>Rp ".number_format($totalPendapatan, 0, ',', '.')."</b>";
                            ?> 
                        </td>
                      </tr>

                      <tr>
                        <th colspan="2">2. HPP</th>
                      </tr>
                      <tr>
                        <td>a. HPP (Harga Pokok Penjualan)</td>
                        <td>Rp <?= number_format($totalHpp, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td><b>Laba / Rugi Kotor</b></td>
                        <td>
                            <?php  
                              $labaRugiKotor = $totalPendapatan - $totalHpp;
                              echo "<b>Rp ".number_format($labaRugiKotor, 0, ',', '.')."</b>";
                            ?>
                        </td>
                      </tr>

                      <tr>
                        <th colspan="2">3. Biaya Pengeluaran</th>
                      </tr>
                      <tr>
                        <td>a. Total Gaji Pegawai</td>
                        <td>Rp <?= number_format($lb_pengeluaran_gaji, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>b. Biaya Listrik 1 Bulan</td>
                        <td>Rp <?= number_format($lb_pengeluaran_listrik, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>c. Telepon & Internet</td>
                        <td>Rp <?= number_format($lb_pengeluaran_tlpn_internet, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>d. Perlengkapan Toko</td>
                        <td>Rp <?= number_format($lb_pengeluaran_perlengkapan_toko, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>e. Biaya Penyusutan</td>
                        <td>Rp <?= number_format($lb_pengeluaran_biaya_penyusutan, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>f. Transportasi & Bensin</td>
                        <td>Rp <?= number_format($lb_pengeluaran_bensin, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>g. Biaya Tak Terduga</td>
                        <td>Rp <?= number_format($lb_pengeluaran_tak_terduga, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>h. Pengeluaran Lain</td>
                        <td>Rp <?= number_format($lb_pengeluaran_lain, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>i. Total Pembelian ke Supplier</td>
                        <td>Rp <?= number_format($totalPembelian, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>j. DP Hutang</td>
                        <td>Rp <?= number_format($totalDpHutang, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>k. Hutang (Cicilan)</td>
                        <td>Rp <?= number_format($hutang, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td><b>Total Biaya Pengeluaran</b></td>
                        <td>
                            <?php  
                              $totalBiayaPengeluaran = $lb_pengeluaran_gaji + $lb_pengeluaran_listrik + $lb_pengeluaran_tlpn_internet + $lb_pengeluaran_perlengkapan_toko + $lb_pengeluaran_biaya_penyusutan + $lb_pengeluaran_bensin + $lb_pengeluaran_tak_terduga + $lb_pengeluaran_lain + $hutang + $totalDpHutang + $totalPembelian;
                              echo "<b>Rp ".number_format($totalBiayaPengeluaran, 0, ',', '.' )."</b>";
                            ?>
                        </td>
                      </tr>
                      <tr>
                        <th>Laba Bersih</th>
                        <th>
                            <?php  
                                $labaBersih = $labaRugiKotor - $totalBiayaPengeluaran;
                                echo "Rp ".number_format($labaBersih, 0, ',', '.');
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
</script>