<?php 
  include '_header.php'; 
?>

<?php  
  // ambil data di URL
  $id = base64_decode($_GET['no']);

  // query data mahasiswa berdasarkan id
  $transfer = query("SELECT * FROM transfer WHERE transfer_ref = $id && transfer_cabang = $sessionCabang")[0];

  if ( $transfer == null ) {
    header("location: transfer-stock-cabang-keluar");
  }
?>

	<div class="">

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">


            <!-- Main content -->
            <div class="invoice p-3 mb-3">

              <!-- title row -->
              <!-- info row -->
              <?php  
                  $tokoPengirim = $transfer['transfer_pengirim_cabang'];
                  $toko = query("SELECT * FROM toko WHERE toko_cabang = $tokoPengirim");
              ?>
              <?php foreach ( $toko as $row ) : ?>
                  <?php 
                    $toko_nama   = $row['toko_nama'];
                    $toko_kota   = $row['toko_kota'];
                    $toko_tlpn   = $row['toko_tlpn'];
                    $toko_wa     = $row['toko_wa']; 
                    $toko_email  = $row['toko_email'];
                    $toko_alamat = $row['toko_alamat'];
                  ?>
              <?php endforeach; ?>
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  <h4><b>Dari Pengirim</b></h4>
                  <address>
                    <strong><?= $toko_nama; ?></strong><br>
                    <?= $toko_alamat; ?><br>
                    Tlpn/wa: <?= $toko_tlpn; ?> / <?= $toko_wa; ?><br>
                    Email: <?= $toko_email; ?><br>

                    <?php  
                    	$kasir = $transfer['transfer_user'];
                    	$dataKasir = query("SELECT * FROM user WHERE user_id = $kasir");
                    ?>
                    <?php foreach ( $dataKasir as $ksr ) : ?>
                    	<?php $ksrDetail = $ksr['user_nama']; ?>
                    <?php endforeach; ?>

                    <?php  
                        $tokoPengirimUser = $transfer['transfer_cabang'];
                        $toko = query("SELECT * FROM toko WHERE toko_cabang = $tokoPengirimUser");
                    ?>
                    <?php foreach ( $toko as $row ) : ?>
                        <?php 
                          $toko_nama_user   = $row['toko_nama'];
                          $toko_kota_user   = $row['toko_kota'];
                          $toko_tlpn_user   = $row['toko_tlpn'];
                          $toko_wa_user     = $row['toko_wa']; 
                          $toko_email_user  = $row['toko_email'];
                          $toko_alamat_user = $row['toko_alamat'];
                        ?>
                    <?php endforeach; ?>

                    <b>Kasir: </b><?= $ksrDetail; ?> dari <?= $toko_nama_user; ?> Kota <?= $toko_kota_user; ?>
                  </address>
                </div>
                <!-- /.col -->

                <div class="col-sm-4 invoice-col">
                  <h4><b>Penerima</b></h4>
                  <address>
                  	<?php  
                        $tokoPenerima = $transfer['transfer_penerima_cabang'];
                        $toko = query("SELECT * FROM toko WHERE toko_cabang = $tokoPenerima");
                    ?>
                    <?php foreach ( $toko as $row ) : ?>
                        <?php 
                          $toko_nama_penerima   = $row['toko_nama'];
                          $toko_kota_penerima   = $row['toko_kota'];
                          $toko_tlpn_penerima   = $row['toko_tlpn'];
                          $toko_wa_penerima     = $row['toko_wa']; 
                          $toko_email_penerima  = $row['toko_email'];
                          $toko_alamat_penerima = $row['toko_alamat'];
                        ?>
                    <?php endforeach; ?>

                    <strong><?= $toko_nama_penerima; ?></strong><br>
                    <?= $toko_alamat_penerima; ?><br>
                    Tlpn/wa: <?= $toko_tlpn_penerima; ?> / <?= $toko_wa_penerima; ?><br>
                    Email: <?= $toko_email_penerima; ?><br>
                  </address>
                </div>

                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <h4>
                    <i class="fas fa-globe"></i> N0. Ref: <?= $id; ?><br>
                    <small>Tanggal: <?= tanggal_indo($transfer['transfer_date']); ?></small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <div class="table-auto">
                    <table class="table table-striped">
                      <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php 
                        $transfer1 = $id;
  	                  $i = 1; 
  	                  $queryProduct = $conn->query("SELECT transfer_produk_keluar.tpk_id, 
                        transfer_produk_keluar.tpk_qty, 
                        transfer_produk_keluar.tpk_ref, 
                        transfer_produk_keluar.tpk_barang_option_sn, 
                        transfer_produk_keluar.tpk_barang_sn_desc,  
                        transfer_produk_keluar.tpk_cabang, 
                        barang.barang_id, 
                        barang.barang_nama
  	                             FROM transfer_produk_keluar 
  	                             JOIN barang ON transfer_produk_keluar.tpk_barang_id = barang.barang_id
  	                             WHERE tpk_ref = $transfer1 && tpk_cabang = '".$sessionCabang."'
  	                             ORDER BY tpk_id DESC
  	                             ");
  	                  while ($rowProduct = mysqli_fetch_array($queryProduct)) {
  	                ?>
  	                
                      <tr>
                        <td><?= $i; ?></td>
                        <td>
                            <?= $rowProduct['barang_nama']; ?><br>
                            <?php if ( $rowProduct['tpk_barang_option_sn'] > 0 ) { ?>  
                            <small>No. SN: <?= $rowProduct['tpk_barang_sn_desc']; ?></small>
                            <?php } ?>
                        </td>
                        <td><?= $rowProduct['tpk_qty']; ?></td>
                      </tr>
                      <?php $i++; ?>
                  	<?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <div class="row">
                <!-- accepted payments column -->
                <div class="col-12"></div>

                <!-- /.col -->
                <div class="col-12">
                  <div class="invoice-table">
                      <?php  
                        $note = $transfer['transfer_note'];
                        if ( $note == null ) {
                          $noteTeks = "-";
                        } else {
                          $noteTeks = $note;
                        }
                      ?>
                      <div class="form-group">
                          <label for="transfer_note">Catatan Pengiriman</label><br>
                          <?= $noteTeks; ?>
                      </div>
                    </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->


              <!-- this row will not appear when printing -->
              
            </div>
            <!-- /.invoice -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <div class="text-center">
      © <?= date("Y"); ?> Copyright www.senimankoding.com All rights reserved.
    </div>
    <!-- /.content -->
  </div>

<script>
  window.print();
</script>
