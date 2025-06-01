<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
  $userId = $_SESSION['user_id'];
  $tipeHarga = base64_decode($_GET['customer']);
  if ( $tipeHarga == 1 ) {
      $nameTipeHarga = "Grosir 1";
  } elseif ( $tipeHarga == 2 ) {
      $nameTipeHarga = "Grosir 2";
  } else {
      $nameTipeHarga = "Umum";
  }

  if ( $levelLogin === "kurir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }  


if ( $dataTokoLogin['toko_status'] < 1 ) {
  echo "
      <script>
        alert('Status Toko Tidak Aktif Jadi Anda Tidak Bisa melakukan Transaksi !!');
        document.location.href = 'bo';
      </script>
    ";
}



// Insert Ke keranjang Scan Barcode
if( isset($_POST["inputbarcode"]) ){
  // var_dump($_POST);

  // cek apakah data berhasil di tambahkan atau tidak
  if( tambahKeranjangBarcode($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = '';
      </script>
    ";
  }  
}
?>



<?php 
error_reporting(0);
// Insert Ke keranjang
$inv = $_POST["penjualan_invoice2"];
if( isset($_POST["updateStock"]) ){
  // var_dump($_POST);
  $sql = mysqli_query($conn, "SELECT * FROM invoice WHERE penjualan_invoice='$inv' && invoice_cabang = '$sessionCabang' ") or die (mysqli_error($conn));

  $hasilquery = mysqli_num_rows($sql);

  if( $hasilquery == 0){
      // cek apakah data berhasil di tambahkan atau tidak
      if( updateStock($_POST) > 0 ) {
        echo "
          <script>
            document.location.href = 'invoice?no=".$inv."';
          </script>
        ";
      } else {
        echo "
          <script>
            alert('Transaksi Gagal !!');
          </script>
        ";
      }
  }else {
    echo "
        <script>
          document.location.href = 'invoice?no=".$inv."';
        </script>
      ";
  }
}
?>

<?php 
if( isset($_POST["updateStockDraft"]) ){
  // var_dump($_POST);
  $sql = mysqli_query($conn, "SELECT * FROM invoice WHERE penjualan_invoice='$inv' && invoice_cabang = '$sessionCabang' ") or die (mysqli_error($conn));

  $hasilquery = mysqli_num_rows($sql);

  if( $hasilquery == 0){
      // cek apakah data berhasil di tambahkan atau tidak
      if( updateStockDraft($_POST) > 0 ) {
        echo "
          <script>
            document.location.href = '';
            alert('Transaksi Berhasil Dipending !!');
          </script>
        ";
      } else {
        echo "
          <script>
            alert('Transaksi Gagal !!');
          </script>
        ";
      }
  }else {
    echo "
        <script>
          document.location.href = '';
          alert('Transaksi Berhasil dipending !!');
        </script>
      ";
  }
}
?>

<?php
  // Update Data Produk SN dan Non SN 
  if ( isset($_POST["updateSn"]) ) {
    if( updateSn($_POST) > 0 ) {
      echo "
        <script>
          document.location.href = '';
        </script>
      ";
    } else {
      echo "
        <script>
          alert('Data Gagal edit');
        </script>
      ";
    }
  }

  // Update Data Harga Produk di Keranjang 
  if ( isset($_POST["updateQtyPenjualan"]) ) {
    if( updateQTYHarga($_POST) > 0 ) {
      echo "
        <script>
          document.location.href = '';
        </script>
      ";
    } else {
      echo "
        <script>
          alert('Data Gagal edit');
        </script>
      ";
    }
  }

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-8">
            <h1>Transaksi Kasir <b style="color: #007bff; ">Customer <?= $nameTipeHarga; ?></b></h1>
            <div class="btn-cash-piutang">
              <?php  
                // Ambil data dari URL Untuk memberikan kondisi transaksi Cash atau Piutang
                if (empty(abs((int)base64_decode($_GET['r'])))) {
                    $r = 0;
                } else {
                    $r = abs((int)base64_decode($_GET['r']));
                }
              ?>

              <?php if ( $r == 1 ) : ?>
              <a href="beli-langsung?customer=<?= $_GET['customer']; ?>" class="btn btn-default">Cash</a>
              <a href="beli-langsung?customer=<?= $_GET['customer']; ?>&r=MQ==" class="btn btn-primary">Piutang</a>
              <?php else : ?>
              <a href="beli-langsung?customer=<?= $_GET['customer']; ?>" class="btn btn-primary">Cash</a>
              <a href="beli-langsung?customer=<?= $_GET['customer']; ?>&r=MQ==" class="btn btn-default">Piutang</a>
              <?php endif; ?>
              <a class="btn btn-danger" data-toggle="modal" href='#modal-id-draft' data-backdrop="static">Pending</a>
              <div class="modal fade" id="modal-id-draft">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Data Transaksi Pending</h4>
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                      <?php  
                          $draft = query("SELECT * FROM invoice WHERE invoice_draft = 1 && invoice_kasir = $userId && invoice_cabang = $sessionCabang ORDER BY invoice_id DESC");
                      ?>
                      <div class="table-auto">
                        <table id="example7" class="table table-bordered table-striped">
                          <thead>
                          <tr>
                            <th style="width: 5px;">No.</th>
                            <th>Invoice</th>
                            <th style="width: 40% !important;">Tanggal</th>
                            <th>Customer</th>
                            <th class="text-center">Aksi</th>
                          </tr>
                          </thead>
                          <tbody>

                          <?php $i = 1; ?>
                          <?php foreach ( $draft as $row ) : ?>
                          <tr>
                              <td><?= $i; ?></td>
                              <td><?= $row['penjualan_invoice']; ?></td>
                              <td><?= tanggal_indo($row['invoice_tgl']); ?></td>
                              <td>
                                  <?php 
                                    $customer_id_draft = $row['invoice_customer']; 
                                    $namaCustomerDraft = mysqli_query($conn, "SELECT customer_nama FROM customer WHERE customer_id = $customer_id_draft");
                                    $namaCustomerDraft = mysqli_fetch_array($namaCustomerDraft);
                                    $customer_nama_draft = $namaCustomerDraft['customer_nama'];

                                    if ( $customer_id_draft < 1 ) {
                                      echo "Customer Umum";
                                    } else {
                                      echo $customer_nama_draft;
                                    }
                                  ?> 
                              </td>
                              <td class="orderan-online-button">
                                <a href="beli-langsung-draft?customer=<?= base64_encode($row['invoice_customer_category']); ?>&r=<?= base64_encode($row['invoice_piutang']); ?>&invoice=<?= base64_encode($row['penjualan_invoice']); ?>" title="Edit Data">
                                      <button class="btn btn-primary" type="submit">
                                         <i class="fa fa-edit"></i>
                                      </button>
                                  </a>
                                  <a href="beli-langsung-draft-delete?invoice=<?= $row['penjualan_invoice']; ?>&customer=<?= $_GET['customer']; ?>&cabang=<?= $sessionCabang; ?>" onclick="return confirm('Yakin dihapus ?')" title="Delete Data">
                                      <button class="btn btn-danger" type="submit">
                                         <i class="fa fa-trash"></i>
                                      </button>
                                  </a>
                              </td>
                          </tr>
                          <?php $i++; ?>
                        <?php endforeach; ?>
                        </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Barang</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>


    <section class="content">
    <?php  
      $userId = $_SESSION['user_id'];
      $keranjang = query("SELECT * FROM keranjang WHERE keranjang_id_kasir = $userId && keranjang_tipe_customer = $tipeHarga && keranjang_cabang = $sessionCabang ORDER BY keranjang_id ASC");

      $countInvoice = mysqli_query($conn, "select * from invoice where invoice_cabang = ".$sessionCabang." ");
      $countInvoice = mysqli_num_rows($countInvoice);
      if ( $countInvoice < 1 ) {
        $jmlPenjualan1 = 0;
      } else {
        $penjualan = query("SELECT * FROM invoice WHERE invoice_cabang = $sessionCabang ORDER BY invoice_id DESC lIMIT 1")[0];
        $jmlPenjualan1 = $penjualan['penjualan_invoice_count'];
      }
      $jmlPenjualan1 = $jmlPenjualan1 + 1;
    ?>
        <div class="col-lg-12">
        	<div class="card">
            <div class="card-header">
              <div class="row">
                <div class="col-md-8 col-lg-8">
                    <div class="card-invoice">
                      <span>No. Invoice: </span>
                      <?php  
                        $today = date("Ymd");
                        $di = $today.$jmlPenjualan1;
                      ?>
                      <input type="" name="" value="<?= $di; ?>">
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="cari-barang-parent">
                      <div class="row">
                        <div class="col-10">
                            <form action="" method="post">
                                <input type="hidden" name="keranjang_id_kasir" value="<?= $userId; ?>">
                                <input type="hidden" name="keranjang_cabang" value="<?= $sessionCabang; ?>">
                                <input type="hidden" name="tipe_harga" value="<?= $tipeHarga; ?>">
                                <input type="text" class="form-control" autofocus="" name="inputbarcode" placeholder="Barcode / Kode Barang" required="">
                            </form>
                        </div>
                        <div class="col-2">
                            <a class="btn btn-primary" title="Cari Produk" data-toggle="modal" id="cari-barang" href='#modal-id'>
                               <i class="fa fa-search"></i>
                            </a>
                        </div>
                      </div>
                    </div>
                </div>
                </div>
              </div>

            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <!-- Ganti bagian tabel keranjang dengan ini -->
                <table id="" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="width: 6%;">No.</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th style="width: 15%;">Satuan</th>
                        <th style="text-align: center; width: 10%;">QTY</th>
                        <th style="text-align: center; width: 10%;">Diskon (%)</th>
                        <th style="width: 20%;">Sub Total</th>
                        <th style="text-align: center; width: 10%;">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $i          = 1; 
                            $total_beli = 0;
                            $total      = 0;
                        ?>
                        <?php foreach($keranjang as $row) : 
                            $bik = $row['barang_id'];
                            
                            // Query untuk mengambil data satuan
                            $query = "SELECT b.*, s1.satuan_nama as satuan_1, s2.satuan_nama as satuan_2, s3.satuan_nama as satuan_3 
                                    FROM barang b
                                    LEFT JOIN satuan s1 ON b.satuan_id = s1.satuan_id
                                    LEFT JOIN satuan s2 ON b.satuan_id_2 = s2.satuan_id
                                    LEFT JOIN satuan s3 ON b.satuan_id_3 = s3.satuan_id
                                    WHERE b.barang_id = '$bik'";
                            $brg = mysqli_fetch_array(mysqli_query($conn, $query));
                            // $querySatuan = "SELECT * FROM satuan WHERE satuan_id IN (
                            //                 SELECT satuan_id FROM barang_satuan WHERE barang_id = '".$bik."'
                            //                 )";
                            // $resultSatuan = mysqli_query($conn, $querySatuan);
                            $satuanOptions = [];
                            while($sat = mysqli_fetch_assoc($resultSatuan)) {
                                $satuanOptions[] = $sat;
                            }
                            
                            // Hitung subtotal dengan diskon
                            $diskonPersen = $row['keranjang_diskon_persen'] ?? 0;
                            $hargaSetelahDiskon = $row['keranjang_harga'] * (1 - ($diskonPersen/100));
                            $sub_total = $hargaSetelahDiskon * $row['keranjang_qty_view'];
                        ?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td>
                                <?= $row['keranjang_nama'] ?><br>
                                <small><a href="#!" id="keranjang-rak" data-id="<?= $bik; ?>"><u>Lihat Lokasi Rak</u></a></small>      
                            </td>
                            <td>Rp. <?= number_format($row['keranjang_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <select name="satuan_pembelian[]" class="form-control satuan-pilihan" 
                                        data-barangid="<?= $row['barang_id']; ?>" 
                                        data-keranjang-id="<?= $row['keranjang_id']; ?>">
                                    
                                    <!-- Satuan 1 (Utama) -->
                                    <option value="1" 
                                            data-konversi="1" 
                                            data-harga="<?= $brg['barang_harga']; ?>">
                                        <?= $brg['satuan_1'] ?> - Rp <?= number_format($brg['barang_harga']) ?>
                                    </option>
                                    
                                    <!-- Satuan 2 (jika ada) -->
                                    <?php if($brg['satuan_id_2'] > 0 && $brg['satuan_isi_2'] > 0): ?>
                                    <option value="2" 
                                            data-konversi="<?= $brg['satuan_isi_2'] ?>" 
                                            data-harga="<?= $brg['barang_harga_s2']; ?>">
                                        <?= $brg['satuan_2'] ?> - Rp <?= number_format($brg['barang_harga_s2']) ?>
                                        (1 <?= $brg['satuan_1'] ?> = <?= $brg['satuan_2'] ?> <?= $brg['satuan_isi_2'] ?>)
                                    </option>
                                    <?php endif; ?>
                                    
                                    <!-- Satuan 3 (jika ada) -->
                                    <?php if($brg['satuan_id_3'] > 0 && $brg['satuan_isi_3'] > 0): ?>
                                    <option value="3" 
                                            data-konversi="<?= $brg['satuan_isi_3'] ?>" 
                                            data-harga="<?= $brg['barang_harga_s3']; ?>">
                                        <?= $brg['satuan_3'] ?> - Rp <?= number_format($brg['barang_harga_s3']) ?>
                                        (1 <?= $brg['satuan_1'] ?> = <?= $brg['satuan_3'] ?> <?= $brg['satuan_isi_3'] ?>)
                                    </option>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <td style="text-align: center;"><?= $row['keranjang_qty_view']; ?></td>
                            <td style="text-align: center;">
                                <input type="number" class="form-control diskon-persen" 
                                      value="<?= $diskonPersen; ?>" min="0" max="100"
                                      data-id="<?= $row['keranjang_id']; ?>">
                            </td>
                            <td>Rp. <?= number_format($sub_total, 0, ',', '.'); ?></td>
                            <td class="orderan-online-button">
                                <a href="barang-zoom?id=<?= base64_encode($row['barang_id']); ?>" target="_blank" title="Lihat Data">
                                  <button class="btn btn-success" name="" >
                                      <i class="fa fa-eye"></i>
                                  </button> 
                                </a>
                                <a href="#!" title="Edit Data">
                                  <button class="btn btn-primary" name="" class="keranjang-pembelian" id="keranjang-qty" data-id="<?= $row['keranjang_id']; ?>">
                                      <i class="fa fa-pencil"></i>
                                  </button> 
                                </a>
                                <a href="beli-langsung-delete?id=<?= $row['keranjang_id']; ?>&customer=<?= $_GET['customer']; ?>&r=<?= $r; ?>" title="Delete Data" onclick="return confirm('Yakin dihapus ?')">
                                    <button class="btn btn-danger" type="submit" name="hapus">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            $total += $sub_total;
                            $i++; 
                        ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
              </div>
       
              <div class="btn-transaksi">
                <form role="form" action="" method="POST">
                  <div class="row">
                    <!-- Form Input Kiri -->
                    <div class="col-md-6 col-lg-6">
                        <!-- Customer Selection -->
                        <div class="filter-customer">
                            <div class="form-group">
                                <label>Customer <b style="color: #007bff; "><?= $nameTipeHarga; ?></b></label>
                                <select class="form-control select2bs4 pilihan-marketplace customer-select" required="" name="invoice_customer" id="customer-select">
                                    <?php if ( $r != 1 && $tipeHarga < 1 ) { ?>
                                    <option value="0" data-membership="0">Umum</option>
                                    <?php } ?>
                                    <?php  
                                        $customer = query("SELECT * FROM customer WHERE customer_cabang = $sessionCabang && customer_status = 1 && customer_category = $tipeHarga ORDER BY customer_id DESC ");
                                    ?>
                                    <?php foreach ( $customer as $ctr ) : ?>
                                        <?php if ( $ctr['customer_id'] > 1 && $ctr['customer_nama'] !== "Customer Umum" ) { ?>
                                        <option value="<?= $ctr['customer_id'] ?>" data-membership="<?= $ctr['customer_membership'] ?>">
                                            <?= $ctr['customer_nama'] ?> - <?= $ctr['customer_tlpn'] ?>
                                            <?php if($ctr['customer_membership'] == '1'): ?>
                                                <span style="color: #28a745; font-weight: bold;">[MEMBER]</span>
                                            <?php endif; ?>
                                        </option>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                </select>
                                <small>
                                    <a href="customer-add">Tambah Customer <i class="fa fa-plus"></i></a>
                                </small>
                                
                                <!-- Indikator Membership Discount -->
                                <div id="membership-indicator" class="mt-2" style="display: none;">
                                    <span class="badge badge-success">
                                        <i class="fa fa-star"></i> Member - Diskon 3% otomatis diterapkan
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- View Jika Select Dari Marketplace -->
                        <span id="beli-langsung-marketplace"></span>

                        <!-- Tipe Pembayaran -->
                        <div class="form-group">
                            <label>Tipe Pembayaran</label>
                            <select class="form-control" required="" name="invoice_tipe_transaksi">
                              <option selected="selected" value="0">Cash</option>
                              <option value="1">Transfer</option>
                            </select>
                        </div>

                        <!-- Kurir -->
                        <div class="form-group">
                            <label>Kurir</label>
                            <select class="form-control select2bs4" required="" name="invoice_kurir">
                              <?php if ( $dataTokoLogin['toko_ongkir'] > 0 ) { ?>
                              <option selected="selected" value="">-- Pilih Kurir --</option>
                              <?php } ?>
                              <option value="0">Tanpa Kurir</option>
                              <?php  
                                $kurir = query("SELECT * FROM user WHERE user_level = 'kurir' && user_cabang = $sessionCabang && user_status = '1' ORDER BY user_id DESC ");
                              ?>
                              <?php foreach ( $kurir as $row ) : ?>
                                <option value="<?= $row['user_id']; ?>">
                                  <?= $row['user_nama']; ?>  
                                </option>
                              <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Jatuh Tempo untuk Piutang -->
                        <?php if ( $r == 1 ) : ?>
                        <div class="form-group">
                            <label>Jatuh Tempo</label>
                            <?php  
                              $tgl1              = date('Y-m-d');
                              $jatuh_tempo_angka = 1;
                              $jatuh_tempo_teks  = "month";
                              $jatuh_tempo       = date('Y-m-d', strtotime('+'.$jatuh_tempo_angka.' '.$jatuh_tempo_teks, strtotime( $tgl1 )));
                            ?>
                            <input type="date" name="invoice_piutang_jatuh_tempo" value="<?= $jatuh_tempo; ?>" class="form-control" required>
                            <small style="color: red; margin-right: -10px;">
                                  <b>Default dari system 1 bulan kedepan</b> & ganti sesuai kebutuhan
                            </small>
                        </div>
                        <?php else : ?>
                          <input type="hidden" name="invoice_piutang_jatuh_tempo" value="0">
                        <?php endif; ?>

                        <!-- Hidden Inputs untuk Data Keranjang -->
                        <?php foreach ($keranjang as $stk) : ?>
                        <?php if ( $stk['keranjang_id_kasir'] === $userId ) { ?>
                          <input type="hidden" name="barang_ids[]" value="<?= $stk['barang_id']; ?>">
                          <input type="hidden" min="1" name="keranjang_qty[]" value="<?= $stk['keranjang_qty']; ?>"> 
                          <input type="hidden" min="1" name="keranjang_qty_view[]" value="<?= $stk['keranjang_qty_view']; ?>"> 
                          <input type="hidden" name="keranjang_konversi_isi[]" value="<?= $stk['keranjang_konversi_isi']; ?>"> 
                          <input type="hidden" name="keranjang_satuan[]" value="<?= $stk['keranjang_satuan']; ?>"> 
                          <input type="hidden" name="keranjang_harga_beli[]" value="<?= $stk['keranjang_harga_beli']; ?>">
                          <input type="hidden" name="keranjang_harga[]" value="<?= $stk['keranjang_harga']; ?>">
                          <input type="hidden" name="keranjang_diskon_persen[]" value="<?= $stk['keranjang_diskon_persen'] ?? 0; ?>"> 
                          <input type="hidden" name="keranjang_harga_parent[]" value="<?= $stk['keranjang_harga_parent']; ?>">
                          <input type="hidden" name="keranjang_harga_edit[]" value="<?= $stk['keranjang_harga_edit']; ?>">
                          <input type="hidden" name="keranjang_id_kasir[]" value="<?= $stk['keranjang_id_kasir']; ?>">

                          <input type="hidden" name="penjualan_invoice[]" value="<?= $di; ?>">
                          <input type="hidden" name="penjualan_date[]" value="<?= date("Y-m-d") ?>">

                          <input type="hidden" name="keranjang_barang_option_sn[]" value="<?= $stk['keranjang_barang_option_sn']; ?>">
                          <input type="hidden" name="keranjang_barang_sn_id[]" value="<?= $stk['keranjang_barang_sn_id']; ?>">
                          <input type="hidden" name="keranjang_sn[]" value="<?= $stk['keranjang_sn']; ?>">
                          <input type="hidden" name="invoice_customer_category2[]" value="<?= $tipeHarga; ?>">
                          <input type="hidden" name="keranjang_nama[]" value="<?= $stk['keranjang_nama']; ?>">
                          <input type="hidden" name="barang_kode_slug[]" value="<?= $stk['barang_kode_slug']; ?>">
                          <input type="hidden" name="keranjang_id_cek[]" value="<?= $stk['keranjang_id_cek']; ?>">
                          <input type="hidden" name="penjualan_cabang[]" value="<?= $sessionCabang; ?>">
                        <?php } ?>
                        <?php endforeach; ?>  

                        <!-- Hidden Inputs untuk Data Transaksi -->
                        <input type="hidden" name="penjualan_invoice2" value="<?= $di; ?>">
                        <input type="hidden" name="invoice_customer_category" value="<?= $tipeHarga; ?>">
                        <input type="hidden" name="kik" value="<?= $userId; ?>">
                        <input type="hidden" name="penjualan_invoice_count" value="<?= $jmlPenjualan1; ?>">
                        <input type="hidden" name="invoice_piutang" value="<?= $r; ?>">
                        <input type="hidden" name="invoice_piutang_lunas" value="0">
                        <input type="hidden" name="invoice_cabang" value="<?= $sessionCabang; ?>">
                        <input type="hidden" name="invoice_total_beli" value="<?= $total_beli; ?>">

                    </div>

                    <!-- Tabel Perhitungan Kanan -->
                    <div class="col-md-6 col-lg-6">
                      <div class="invoice-table">
                        <table class="table">
                          <!-- Total -->
                          <tr>
                              <td style="width: 110px;"><b>Total</b></td>
                              <td class="table-nominal">
                                 <span>Rp. </span>
                                 <span>
                                    <input type="text" name="invoice_total" id="angka2" class="a2"  value="<?= $total; ?>" onkeyup="return isNumberKey(event)" size="10" readonly>
                                 </span>
                              </td>
                          </tr>

                        <!-- Ongkir Dinamis untuk Inputan -->
                          <tr class="ongkir-dinamis none">
                              <td>Ongkir</td>
                              <td class="table-nominal tn">
                                 <span>Rp.</span> 
                                 <span class="ongkir-beli-langsung">
                                   <input type="number" name="invoice_ongkir" id="" class="b2 ongkir-dinamis-input" autocomplete="off" onkeyup="hitung2();" onkeypress="return hanyaAngka1(event)">
                                   <i class="fa fa-close fa-ongkir-dinamis"></i>
                                 </span>
                              </td>
                          </tr>

                          <tr class="ongkir-dinamis none">
                                <td><b>Sub Total</b></td>
                                <td class="table-nominal c2parent">
                                   <span>Rp. </span>
                                   <span>
                                      <input type="text" name="invoice_sub_total"  class="c2"  value="<?= $total; ?>" readonly>
                                   </span>
                                </td>
                                <tr>
                                    <td><b>Total Diskon</b></td>
                                    <td class="table-nominal">
                                        <!-- <span>Rp. </span> -->
                                        <span>
                                            <input type="text" name="invoice_diskon" id="total-diskon-display" class="form-control" value="0" readonly style="background-color: #f8f9fa;">
                                        </span>
                                    </td>
                                </tr>
                                <td class="table-nominal g2parent" style="display: none;">
                                   <span>Rp. </span>
                                   <span >
                                      <input type="text" name="invoice_sub_total"  class="g2"  value="<?= $total; ?>" readonly>
                                   </span>
                                </td>
                          </tr>

                          <tr class="ongkir-dinamis none">
                                <td>
                                    <b style="color: red;">
                                        <?php  
                                          if ( $r == 1 ) {
                                            echo "DP";
                                          } else {
                                            echo "Bayar";
                                          }
                                        ?>      
                                    </b>
                                </td>
                                <td class="table-nominal tn d2parent">
                                   <span>Rp.</span> 
                                   <span class="">
                                     <input type="number" name="angka1" id="angka1" class="d2 ongkir-dinamis-bayar" autocomplete="off" onkeyup="hitung3();" onkeypress="return hanyaAngka1(event)" size="10">
                                   </span>
                                </td>
                                <td class="table-nominal tn h2parent" style="display: none;">
                                   <span>Rp.</span> 
                                   <span class="" >
                                     <input type="number" name="angka1" id="angka1" class="h22 ongkir-dinamis-bayar" autocomplete="off" onkeyup="hitung7();" onkeypress="return hanyaAngka1(event)" size="10">
                                   </span>
                                </td>
                          </tr>

                          <tr class="ongkir-dinamis none">
                              <td>
                                  <?php  
                                    if ( $r == 1 ) {
                                        echo "Sisa Piutang";
                                    } else {
                                        echo "Kembali";
                                    }
                                  ?>  
                              </td>
                              <td class="table-nominal">
                                <span>Rp.</span>
                                 <span>
                                  <input type="text" name="hasil" id="hasil" class="e2" readonly size="10" disabled>
                                </span>
                              </td>
                          </tr>
                        <!-- End Ongkir Dinamis -->

                        <!-- Ongkir Statis untuk Inputan -->
                          <tr class="ongkir-statis">
                              <td>Ongkir</td>
                              <td class="table-nominal tn">
                                 <span>Rp.</span> 
                                 <span class="ongkir-beli-langsung">
                                   <input type="number" value="<?= $dataTokoLogin['toko_ongkir']; ?>" name="invoice_ongkir" id="" class="b2 ongkir-statis-input" readonly>
                                   <i class="fa fa-close fa-ongkir-statis"></i>
                                 </span>
                              </td>
                          </tr>
                          <tr class="ongkir-statis">
                              <td><b>Sub Total</b></td>
                              <td class="table-nominal">
                                 <span>Rp. </span>
                                 <span>
                                    <?php  
                                      $subTotal = $total + $dataTokoLogin['toko_ongkir'];
                                    ?>
                                    <input type="hidden" name=""  class="g21"  value="<?= $subTotal; ?>" readonly>
                                    <input type="text" name="invoice_sub_total"  class="c21"  value="<?= $subTotal; ?>" readonly>
                                 </span>
                              </td>
                          </tr>
                          <tr class="ongkir-statis">
                              <td>
                                  <b style="color: red;">
                                      <?php  
                                        if ( $r == 1 ) {
                                          echo "DP";
                                        } else {
                                          echo "Bayar";
                                        }
                                      ?>      
                                  </b>
                              </td>
                              <td class="table-nominal tn">
                                 <span>Rp.</span> 
                                 <span>
                                   <input type="number" name="angka1" id="angka1" class="d21 ongkir-statis-bayar" autocomplete="off" onkeyup="hitung4();" onkeypress="return hanyaAngka1(event)" size="10">
                                 </span>
                              </td>
                          </tr>
                          <tr class="ongkir-statis">
                              <td>
                                  <?php  
                                    if ( $r == 1 ) {
                                        echo "Sisa Piutang";
                                    } else {
                                        echo "Kembali";
                                    }
                                  ?>  
                              </td>
                              <td class="table-nominal">
                                <span>Rp.</span>
                                 <span>
                                  <input type="text" name="hasil" id="hasil" class="e21" readonly size="10" disabled>
                                </span>
                              </td>
                          </tr>
                        <!-- End Ongkir Statis -->
                        </table>
                      </div>

                      <!-- Payment Buttons -->
                      <div class="payment">
                        <?php  
                            $idKasirKeranjang = $_SESSION['user_id'];
                            $dataSn = mysqli_query($conn,"select * from keranjang where keranjang_barang_option_sn > 0 && keranjang_sn < 1 && keranjang_cabang = $sessionCabang && keranjang_id_kasir = $idKasirKeranjang");
                              $jmlDataSn = mysqli_num_rows($dataSn);
                            ?>
                            <?php if ( $jmlDataSn < 1 ) { ?>
                                <button class="btn btn-danger" type="submit" name="updateStockDraft">Transaksi Pending <i class="fa fa-file-o"></i></button>

                                <button class="btn btn-primary" type="submit" name="updateStock">Simpan Payment <i class="fa fa-shopping-cart"></i></button>
                              <?php } ?>

                              <?php if ( $jmlDataSn > 0 ) { ?>
                                <a href="#!" class="btn btn-default jmlDataSn" type="" name="">Transaksi Pending <i class="fa fa-file-o"></i></a>

                                <a href="#!" class="btn btn-default jmlDataSn" type="" name="">Simpan Payment <i class="fa fa-shopping-cart"></i></a>
                              <?php } ?>
                      </div>
                    </div>
                  </div>
               </form>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
</div>


    <div class="modal fade" id="modal-id" data-backdrop="static">
        <div class="modal-dialog modal-lg-pop-up">
          <div class="modal-content">
            <div class="modal-body">
                  <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Data barang Keseluruhan</h3>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="table-auto">
                    <table id="example1" class="table table-bordered table-striped" style="width: 100%;">
                      <thead>
                      <tr>
                        <th style="width: 5%;">No.</th>
                        <th>Kode Barang</th>
                        <th>Nama</th>
                        <th>
                          <?php  
                            echo "Harga <b style='color: #007bff;'>".$nameTipeHarga."</b>";
                          ?>
                        </th>
                        <th>Stock</th>
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
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
  
  <!-- Modal Update SN -->
  <div class="modal fade" id="modal-id-1">
    <div class="modal-dialog">
      <div class="modal-content">

        <form role="form" id="form-edit-no-sn" method="POST" action="">
          <div class="modal-header">
            <h4 class="modal-title">No. SN Produk</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body" id="data-keranjang-no-sn">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="updateSn" >Edit Data</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Modal Update QTY Penjualan -->
  <div class="modal fade" id="modal-id-2">
    <div class="modal-dialog">
      <div class="modal-content">

        <form role="form" id="form-edit-qty" method="POST" action="">
          <div class="modal-header">
            <h4 class="modal-title">Edit Produk</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body" id="data-keranjang-qty">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="updateQtyPenjualan" >Edit Data</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Modal Lihat Lokasi Rak -->
  <div class="modal fade" id="modal-id-3">
    <div class="modal-dialog">
      <div class="modal-content">

        <form role="form" id="form-lihat-rak" method="POST" action="">
          <div class="modal-header">
            <h4 class="modal-title">Lokasi Rak Obat</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body" id="data-lihat-rak">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <script>
    $(document).ready(function(){
        var table = $('#example1').DataTable( { 
             "processing": true,
             "serverSide": true,

             <?php if ( $tipeHarga == 1 ) : ?>
              "ajax": "beli-langsung-search-data-grosir-1.php?cabang=<?= $sessionCabang; ?>",
             <?php elseif ( $tipeHarga == 2 ) : ?>
              "ajax": "beli-langsung-search-data-grosir-2.php?cabang=<?= $sessionCabang; ?>",
             <?php else : ?>
              "ajax": "beli-langsung-search-data.php?cabang=<?= $sessionCabang; ?>",
             <?php endif; ?>

             "columnDefs": 
             [
              {
                "targets": 3,
                  "render": $.fn.dataTable.render.number( '.', '', '', 'Rp. ' )
                 
              },
              {
                "targets": -1,
                  "data": null,
                  "defaultContent": 
                  `<center>

                      <button class='btn btn-primary tblInsert' title="Tambah Keranjang">
                         <i class="fa fa-shopping-cart"></i> Pilih
                      </button>

                  </center>` 
              }
            ]
        });

        table.on('draw.dt', function () {
            var info = table.page.info();
            table.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + info.start;
            });
        });

        $('#example1 tbody').on( 'click', '.tblInsert', function () {
            var data = table.row( $(this).parents('tr')).data();
            var data0 = data[0];
            var data0 = btoa(data0);
            window.location.href = "beli-langsung-add?id="+ data0 + "&customer=<?= $_GET['customer']; ?>&r=<?= $r; ?>";
        });

    });
  </script>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $("#example1").DataTable();
  });
  $(function () {
    $("#example7").DataTable();
  });
</script>
<script>
    // Fungsi validasi input angka
    function hanyaAngka(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }
    
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    // ================== VERSI DINAMIS ================== //
    function hitungOngkirDinamis() {
        var totalBelanja = parseFloat($(".a2").val()) || 0;
        var ongkir = parseFloat($(".b2").val()) || 0;
        var subTotal = totalBelanja + ongkir;
        $(".c2").val(subTotal);
        $(".g2").val(subTotal); // Untuk perhitungan diskon
        hitungDiskonDinamis(); // Update diskon setelah ongkir berubah
    }

    function hitungDiskonDinamis() {
        var subTotal = parseFloat($(".g2").val()) || 0;
        var diskon = parseFloat($(".f2").val()) || 0;
        var totalSetelahDiskon = subTotal - diskon;
        $(".c2").val(totalSetelahDiskon);
        hitungBayarDinamis(); // Update pembayaran setelah diskon berubah
    }

    function hitungBayarDinamis() {
        var totalBayar = parseFloat($(".c2").val()) || 0;
        var dibayar = parseFloat($(".d2").val()) || 0;
        var kembalian = dibayar - totalBayar;
        $(".e2").val(kembalian);
    }

    // ================== VERSI STATIS ================== //
    function hitungOngkirStatis() {
        var totalBelanja = parseFloat($(".a2").val()) || 0;
        var ongkir = parseFloat($(".b2").val()) || 0;
        var subTotal = totalBelanja + ongkir;
        $(".c21").val(subTotal);
        $(".g21").val(subTotal); // Untuk perhitungan diskon
        hitungDiskonStatis(); // Update diskon setelah ongkir berubah
    }

    function hitungDiskonStatis() {
        var subTotal = parseFloat($(".g21").val()) || 0;
        var diskon = parseFloat($(".f21").val()) || 0;
        var totalSetelahDiskon = subTotal - diskon;
        $(".c21").val(totalSetelahDiskon);
        hitungBayarStatis(); // Update pembayaran setelah diskon berubah
    }

    function hitungBayarStatis() {
        var totalBayar = parseFloat($(".c21").val()) || 0;
        var dibayar = parseFloat($(".d21").val()) || 0;
        var kembalian = dibayar - totalBayar;
        $(".e21").val(kembalian);
    }

    // Inisialisasi event handlers
    $(document).ready(function() {
        // Versi Dinamis
        $(".b2").on('keyup', hitungOngkirDinamis);
        $(".f2").on('keyup', hitungDiskonDinamis);
        $(".d2").on('keyup', hitungBayarDinamis);
        
        // Versi Statis
        $(".f21").on('keyup', hitungDiskonStatis);
        $(".d21").on('keyup', hitungBayarStatis);
        
        // Jalankan perhitungan awal
        hitungOngkirDinamis();
        hitungOngkirStatis();
    });
</script>
<script>
  $(function () {

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  });
</script>

<script>
  $(document).ready(function(){
      $(".pilihan-marketplace").change(function(){
          $(this).find("option:selected").each(function(){
              var optionValue = $(this).attr("value");
              if(optionValue){
                  $(".box1").not("." + optionValue).hide();
                  $("." + optionValue).show();
              } else{
                  $(".box1").hide();
              }
          });
      }).change();

      // Memanggil Pop Up Data Produk SN dan Non SN
      $(document).on('click','#keranjang_sn',function(e){
          e.preventDefault();
          $("#modal-id-1").modal('show');
          $.post('beli-langsung-sn.php',
            {id:$(this).attr('data-id')},
            function(html){
              $("#data-keranjang-no-sn").html(html);
            }   
          );
        });


      // Memanggil Pop Up Data Edit QTY
      $(document).on('click','#keranjang-qty',function(e){
          e.preventDefault();
          $("#modal-id-2").modal('show');
          $.post('beli-langsung-edit-qty.php?customer=<?= $tipeHarga; ?>',
            {id:$(this).attr('data-id')},
            function(html){
              $("#data-keranjang-qty").html(html);
            }   
          );
        });

      // Memanggil Pop Up Data Edit Harga
      $(document).on('click','#keranjang-harga',function(e){
          e.preventDefault();
          $("#modal-id-2").modal('show');
          $.post('beli-langsung-edit-harga.php?customer=<?= $tipeHarga; ?>',
            {id:$(this).attr('data-id')},
            function(html){
              $("#data-keranjang-harga").html(html);
            }   
          );
        });

      // Memanggil Pop Up Lihat Rak
      $(document).on('click','#keranjang-rak',function(e){
          e.preventDefault();
          $("#modal-id-3").modal('show');
          $.post('beli-langsung-lihat-rak.php',
            {id:$(this).attr('data-id')},
            function(html){
              $("#data-lihat-rak").html(html);
            }   
          );
        });

      $(".jmlDataSn").click(function(){
        alert("Anda Tidak Bisa Melanjutkan Transaksi Karena data No. SN Masih Ada yang Kosong !!");
      });

      // View Hidden Ongkir
      $(".fa-ongkir-statis").click(function(){
          $(".ongkir-statis").addClass("none");
          $(".ongkir-statis-input").attr("name", "");
          $(".ongkir-dinamis-input").attr("name", "invoice_ongkir");

          $(".ongkir-statis-diskon").attr("name", "");
          $(".ongkir-dinamis-diskon").attr("name", "invoice_diskon");

          $(".ongkir-statis-bayar").attr("name", "");
          $(".ongkir-dinamis-bayar").attr("name", "angka1");

          // $(".ongkir-dinamis-bayar").attr("required", true);
          $(".ongkir-statis-bayar").removeAttr("required");
          $(".ongkir-statis-diskon").removeAttr("required");
          $(".ongkir-dinamis-diskon").attr("required", true);
          $(".ongkir-dinamis").removeClass("none");
      });

      $(".fa-ongkir-dinamis").click(function(){
          $(".ongkir-dinamis").addClass("none");
          $(".ongkir-dinamis-input").attr("name", "");
          $(".ongkir-statis-input").attr("name", "invoice_ongkir");

          $(".ongkir-dinamis-diskon").attr("name", "");
          $(".ongkir-statis-diskon").attr("name", "invoice_diskon");

          $(".ongkir-dinamis-bayar").attr("name", "");
          $(".ongkir-statis-bayar").attr("name", "angka1");

          // $(".ongkir-dinamis-bayar").removeAttr("required");
          $(".ongkir-dinamis-diskon").removeAttr("required");
          $(".ongkir-statis-diskon").attr("required", true);
          $(".ongkir-statis-bayar").attr("required", true);
          $(".ongkir-statis").removeClass("none");
      });
  });

  // load halaman di pilihan select jenis usaha
  $('#beli-langsung-marketplace').load('beli-langsung-marketplace.php');

</script>

</body>
</html>

<script>
  // Aksi Select Status
  function myFunction() {
    var x = document.getElementById("mySelect").value;
    if ( x === "1" ) {
      document.location.href = "beli-langsung?customer=<?= base64_encode(1); ?>";

    } else if ( x === "2" ) {
      document.location.href = "beli-langsung?customer=<?= base64_encode(2); ?>";

    } else {
      document.location.href = "beli-langsung?customer=<?= base64_encode(0); ?>";
    }
  }
</script>

<script>
  $(document).ready(function(){
    
    // Handle perubahan satuan
    $('.satuan-pilihan').change(function(){
        var $option = $(this).find(':selected');
        var hargaBaru = parseFloat($option.data('harga'));
        var konversi = parseFloat($option.data('konversi'));
        var $row = $(this).closest('tr');
        
        // Update harga di tampilan
        $row.find('td:eq(2)').html('Rp. ' + hargaBaru.toLocaleString('id-ID'));
        
        // Hitung ulang subtotal
        var qty = parseFloat($row.find('td:eq(4)').text());
        var diskon = parseFloat($row.find('.diskon-persen').val()) || 0;
        var subtotal = hargaBaru * qty * (1 - (diskon/100));
        
        // Update subtotal
        $row.find('td:eq(6)').html('Rp. ' + subtotal.toLocaleString('id-ID'));
        
        // Update total keseluruhan
        hitungTotalBelanja();
    });
    
    // Handle perubahan diskon - DIPINDAHKAN KE LUAR DARI CHANGE EVENT
    $('.diskon-persen').on('change', function() {
        var keranjangId = $(this).data('id');
        var diskon = $(this).val();
        var $row = $(this).closest('tr');
        var hargaText = $row.find('td:eq(2)').text().replace('Rp. ', '').replace(/\./g, '').replace(/,/g, '');
        var harga = parseFloat(hargaText) || 0;
        var qty = parseFloat($row.find('td:eq(4)').text()) || 0;
        
        // Validasi diskon tidak boleh lebih dari 100%
        if(diskon > 100) {
            $(this).val(100);
            diskon = 100;
        }
        if(diskon < 0) {
            $(this).val(0);
            diskon = 0;
        }
        
        // Kirim request update ke server
        $.ajax({
            url: 'beli-langsung-update-diskon.php',
            type: 'POST',
            data: {
                keranjang_id: keranjangId,
                diskon: diskon
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    // Hitung ulang semua total termasuk total diskon
                    hitungTotalBelanja();
                } else {
                    alert('Gagal menyimpan diskon: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Terjadi kesalahan saat menyimpan diskon');
            }
        });
    });

    // Handle perubahan customer - DIPINDAHKAN KE LUAR
    $('#customer-select').change(function() {
        var selectedOption = $(this).find(':selected');
        var isMembership = selectedOption.data('membership') == '1';
        
        console.log('Customer changed:', selectedOption.val(), 'Is Member:', isMembership); // Debug
        
        if (isMembership) {
            // Tampilkan indikator membership
            $('#membership-indicator').show();
            
            // Terapkan diskon 3% ke semua item di keranjang
            applyMembershipDiscount();
        } else {
            // Sembunyikan indikator membership
            $('#membership-indicator').hide();
            
            // Reset diskon membership
            removeMembershipDiscount();
        }
    });
    
    // Fungsi untuk menerapkan diskon membership 3%
    function applyMembershipDiscount() {
        console.log('Applying membership discount...'); // Debug
        
        $('tbody tr').each(function() {
            var $row = $(this);
            var $diskonInput = $row.find('.diskon-persen');
            
            if($diskonInput.length > 0) {
                var currentDiskon = parseFloat($diskonInput.val()) || 0;
                
                console.log('Current discount:', currentDiskon); // Debug
                
                // Set diskon ke 3% (atau tambahkan 3% jika sudah ada diskon)
                var newDiskon = Math.max(currentDiskon, 3); // Ambil yang lebih besar antara diskon saat ini atau 3%
                
                $diskonInput.val(newDiskon);
                
                // Trigger change event untuk menghitung ulang subtotal dan simpan ke database
                $diskonInput.trigger('change');
            }
        });
    }
    
    // Fungsi untuk menghapus diskon membership
    function removeMembershipDiscount() {
        console.log('Removing membership discount...'); // Debug
        
        $('tbody tr').each(function() {
            var $row = $(this);
            var $diskonInput = $row.find('.diskon-persen');
            
            if($diskonInput.length > 0) {
                var currentDiskon = parseFloat($diskonInput.val()) || 0;
                
                // Jika diskon adalah 3% atau lebih (kemungkinan dari membership), reset ke 0
                // Atau bisa dimodifikasi sesuai business logic
                if (currentDiskon >= 3) {
                    $diskonInput.val(0);
                    
                    // Trigger change event untuk menghitung ulang subtotal dan simpan ke database
                    $diskonInput.trigger('change');
                }
            }
        });
    }
    
    // Jalankan saat halaman pertama kali dimuat untuk cek status customer
    setTimeout(function() {
        $('#customer-select').trigger('change');
    }, 500); // Delay untuk memastikan DOM sudah siap
    
    // Jika ada customer yang sudah terpilih saat page load
    if($('#customer-select').val()) {
            $('#customer-select').trigger('change');
        }
    });

    function hitungTotalDiskon() {
        var totalDiskon = 0;
        
        $('tbody tr').each(function() {
            var $row = $(this);
            var hargaText = $row.find('td:eq(2)').text().replace('Rp. ', '').replace(/\./g, '').replace(/,/g, '');
            var harga = parseFloat(hargaText) || 0;
            var qty = parseFloat($row.find('td:eq(4)').text()) || 0;
            var diskonPersen = parseFloat($row.find('.diskon-persen').val()) || 0;
            
            // Hitung nominal diskon per item
            var nominalDiskonPerItem = (harga * qty) * (diskonPersen / 100);
            totalDiskon += nominalDiskonPerItem;
        });
        
        // Update field total diskon
        $('#total-diskon-display').val(totalDiskon.toFixed(0));
        
        return totalDiskon;
    }

    // Fungsi untuk menghitung total belanja - DIPINDAHKAN KE LUAR DOCUMENT READY
    function hitungTotalBelanja() {
        var total = 0;
        var totalDiskon = 0; // Tambah variabel ini
        
        $('tbody tr').each(function() {
            var $row = $(this);
            var hargaText = $row.find('td:eq(2)').text().replace('Rp. ', '').replace(/\./g, '').replace(/,/g, '');
            var harga = parseFloat(hargaText) || 0;
            var qty = parseFloat($row.find('td:eq(4)').text()) || 0;
            var diskon = parseFloat($row.find('.diskon-persen').val()) || 0;
            
            var subtotal = harga * qty * (1 - (diskon/100));
            
            // Hitung total diskon
            var nominalDiskonPerItem = (harga * qty) * (diskon / 100);
            totalDiskon += nominalDiskonPerItem;
            
            // Update subtotal di tampilan
            $row.find('td:eq(6)').html('Rp. ' + subtotal.toLocaleString('id-ID'));
            
            total += subtotal;
        });
        
        // Update total diskon display
        $('#total-diskon-display').val(totalDiskon.toFixed(0));
        
        // Update total di bagian pembayaran
        $('#angka2, .a2').val(total);
        
        // Update perhitungan ongkir
        updateOngkirCalculation(total);
    }

    // Fungsi terpisah untuk update perhitungan ongkir
    function updateOngkirCalculation(total) {
        if ($('.ongkir-dinamis').is(':visible')) {
            var ongkir = parseFloat($('.b2').val()) || 0;
            var diskon = parseFloat($('.f2').val()) || 0; // Jika ada field diskon
            var subtotal = total + ongkir - diskon;
            
            $('.c2').val(subtotal);
            $('.g2').val(subtotal);
        } else if ($('.ongkir-statis').is(':visible')) {
            var ongkir = parseFloat($('.ongkir-statis-input').val()) || 0;
            var diskon = parseFloat($('.f21').val()) || 0;
            var subtotal = total + ongkir - diskon;
            
            $('.c21').val(subtotal);
            $('.g21').val(subtotal);
        }
    }
</script>