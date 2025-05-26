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
  $format_laporan = $_POST['format_laporan'];
  $tipe_pembayaran = $_POST['tipe_pembayaran'];
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
    $whereClause = "invoice_cabang = '".$sessionCabang."' && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && invoice_draft = 0";
    
    if ($tipe_pembayaran != 'semua') {
      if ($tipe_pembayaran == 'tunai') {
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
    $totalPenjualan += $rowProduct['invoice_sub_total'];
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
    $totalHpp += $rowProduct['invoice_total_beli'];
  ?>
<?php } ?>
<!-- End Total HPP -->

<!-- Get operational data -->
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
                      Laporan LABA RUGI Periode <?= tanggal_indo($tanggal_awal); ?> - <?= tanggal_indo($tanggal_akhir); ?>
                      <?php if ($tipe_pembayaran != 'semua') echo " - Pembayaran " . ucfirst($tipe_pembayaran); ?>
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
                        <td>Rp <?= number_format($totalPenjualan, 0, ',', '.'); ?></td>
                      </tr>
                      <?php if ($format_laporan == 'detail') : ?>
                      <tr>
                        <td>b. Pendapatan Lain</td>
                        <td>Rp <?= number_format($lb_pendapatan_lain, 0, ',', '.'); ?></td>
                      </tr>
                      <?php endif; ?>
                      <tr>
                        <td><b>Total Pendapatan</b></td>
                        <td>
                            <?php  
                              $totalPendapatan = $totalPenjualan + $lb_pendapatan_lain;
                              echo "<b>Rp ".number_format($totalPendapatan, 0, ',', '.')."</b>";
                            ?> 
                        </td>
                      </tr>

                      <tr>
                        <th colspan="2">2. Harga Pokok Penjualan</th>
                      </tr>
                      <tr>
                        <td>a. HPP (Harga Pokok Penjualan)</td>
                        <td>Rp <?= number_format($totalHpp, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td><b>Laba Kotor</b></td>
                        <td>
                            <?php  
                              $labaKotor = $totalPendapatan - $totalHpp;
                              echo "<b>Rp ".number_format($labaKotor, 0, ',', '.')."</b>";
                            ?>
                        </td>
                      </tr>

                      <?php if ($format_laporan == 'detail') : ?>
                      <tr>
                        <th colspan="2">3. Biaya Operasional</th>
                      </tr>
                      <tr>
                        <td>a. Gaji Pegawai</td>
                        <td>Rp <?= number_format($lb_pengeluaran_gaji, 0, ',', '.'); ?></td>
                      </tr>
                      <tr>
                        <td>b. Biaya Listrik</td>
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
                        <td><b>Total Biaya Operasional</b></td>
                        <td>
                            <?php  
                              $totalBiayaOperasional = $lb_pengeluaran_gaji + $lb_pengeluaran_listrik + $lb_pengeluaran_tlpn_internet + $lb_pengeluaran_perlengkapan_toko + $lb_pengeluaran_biaya_penyusutan + $lb_pengeluaran_bensin + $lb_pengeluaran_tak_terduga + $lb_pengeluaran_lain;
                              echo "<b>Rp ".number_format($totalBiayaOperasional, 0, ',', '.' )."</b>";
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
                              echo "<b>Rp ".number_format($totalBiayaOperasional, 0, ',', '.' )."</b>";
                            ?>
                        </td>
                      </tr>
                      <?php endif; ?>
                      
                      <tr>
                        <th>Laba Bersih</th>
                        <th>
                            <?php  
                                $labaBersih = $labaKotor - $totalBiayaOperasional;
                                echo "Rp ".number_format($labaBersih, 0, ',', '.');
                            ?>
                        </th>
                      </tr>
                      <tr>
                        <th>Rasio Laba Bersih (%)</th>
                        <th>
                            <?php  
                                $rasioLabaBersih = ($totalPendapatan > 0) ? ($labaBersih / $totalPendapatan) * 100 : 0;
                                echo number_format($rasioLabaBersih, 2, ',', '.') . "%";
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