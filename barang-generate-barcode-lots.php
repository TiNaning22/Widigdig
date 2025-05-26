<?php 
  include '_header.php';
?>
<?php  
  if ( $levelLogin === "kasir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }

  $input_barcode  = htmlspecialchars($_POST['input_barcode']);
  $input_kode     = htmlspecialchars($_POST['input_kode']);

  $barang = mysqli_query( $conn, "select barang_nama, barang_harga from barang where barang_kode = '".$input_kode."'");
  $ns = mysqli_fetch_array($barang); 
  $barang_nama  = $ns["barang_nama"];
  $barang_harga = number_format($ns["barang_harga"], 0, ',', '.');
 
?>


  <section class="detail-barcode">
      <div class="container">
          <br><br><br>
          <div class="text-center">
              <h3>Barcode Produk <b><?= $barang_nama; ?></b> Kode <b><?= $input_kode; ?></b></h3>
          </div>
          <br><br>

          <div class="row" style="margin-left: 20px;">
              <?php  
                for ( $i = 1; $i <= $input_barcode; $i++ ) {
                  echo '
                      <div class="col-6">
                          <div class="detail-barcode-box" id="detail-barcode-box">
                            <b class="title-barcode-box">'.$barang_nama.'</b><br>
                            <img src="vendor/barcode/img/'.$input_kode.'-produk-'.$barang_nama.'-cabang-'.$sessionCabang.'.png" alt="" class="img-fluid">
                            
                            <div class="row">
                              <div class="col-3">
                                <b>IDR</b>
                              </div>

                              <div class="col-9">
                                <b style="float: right;">'.$barang_harga.'</b>
                              </div>
                            </div>
                            
                          </div><br>
                      </div>
                  ';
                }
              ?>
          </div>
      </div>
  </section>
  <script>
    window.print();
  </script>