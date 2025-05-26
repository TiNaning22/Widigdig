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

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Laporan Laba/Rugi Penjualan Per Produk</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Laba/Rugi Per Produk</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-default">
        <div class="card-header">
          <h3 class="card-title">Filter Data Berdasarkan Tanggal dan Produk</h3>
        </div>
        <form role="form" action="" method="POST">
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                  <div class="form-group">
                      <label for="tanggal_awal">Tanggal Awal</label>
                      <input type="date" name="tanggal_awal" class="form-control" required>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="form-group">
                      <label for="tanggal_akhir">Tanggal Akhir</label>
                      <input type="date" name="tanggal_akhir" class="form-control" required>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="form-group">
                      <label>Produk</label>
                      <select class="form-control select2bs4" required name="barang_id">
                          <option selected value="">-- Pilih Produk --</option>
                          <?php  
                            $produk = query("SELECT * FROM barang WHERE barang_cabang = $sessionCabang ORDER BY barang_id DESC ");
                          ?>
                          <?php foreach ( $produk as $row ) : ?>
                            <option value="<?= $row['barang_id'] ?>"><?= $row['barang_nama'] ?></option>
                          <?php endforeach; ?>
                        </select>
                  </div>
              </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" name="submit" class="btn btn-primary">
                  <i class="fa fa-filter"></i> Filter
                </button>
            </div>
          </div>
        </form>
      </div>
  </section>

  <?php if( isset($_POST["submit"]) ){ ?>
      <?php  
        $tanggal_awal  = $_POST['tanggal_awal'];
        $tanggal_akhir = $_POST['tanggal_akhir'];
        $barang_id     = $_POST['barang_id'];
      ?>
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Laporan Laba/Rugi Per Produk</h3>
          </div>
          <div class="card-body">
            <div class="table-auto">
              <table id="laporan-laba-rugi" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No.</th>
                  <th>Invoice</th>
                  <th>Tanggal</th>
                  <th>Produk</th>
                  <th>QTY Terjual</th>
                  <th>Harga Jual</th>
                  <th>Harga Modal</th>
                  <th>Total Pendapatan</th>
                  <th>Total Modal</th>
                  <th>Keuntungan</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                  $i = 1; 
                  $totalPendapatan = 0;
                  $totalModal = 0;
                  $queryPenjualan = $conn->query("SELECT penjualan.*, barang.barang_nama, barang.harga_jual, barang.harga_modal
                             FROM penjualan 
                             JOIN barang ON penjualan.barang_id = barang.barang_id
                             WHERE penjualan_cabang = '".$sessionCabang."' 
                             AND penjualan_barang_id = '".$barang_id."' 
                             AND penjualan_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' 
                             ORDER BY penjualan_id DESC");
                  while ($rowProduct = mysqli_fetch_array($queryPenjualan)) {
                    $totalPendapatan += $rowProduct['barang_qty'] * $rowProduct['harga_jual'];
                    $totalModal += $rowProduct['barang_qty'] * $rowProduct['harga_modal'];
                    $keuntungan = ($rowProduct['harga_jual'] - $rowProduct['harga_modal']) * $rowProduct['barang_qty'];
                ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $rowProduct['penjualan_invoice']; ?></td>
                  <td><?= $rowProduct['penjualan_date']; ?></td>
                  <td><?= $rowProduct['barang_nama']; ?></td>
                  <td><?= $rowProduct['barang_qty']; ?></td>
                  <td>Rp <?= number_format($rowProduct['harga_jual'], 0, ',', '.'); ?></td>
                  <td>Rp <?= number_format($rowProduct['harga_modal'], 0, ',', '.'); ?></td>
                  <td>Rp <?= number_format($rowProduct['barang_qty'] * $rowProduct['harga_jual'], 0, ',', '.'); ?></td>
                  <td>Rp <?= number_format($rowProduct['barang_qty'] * $rowProduct['harga_modal'], 0, ',', '.'); ?></td>
                  <td>Rp <?= number_format($keuntungan, 0, ',', '.'); ?></td>
                </tr>
                <?php $i++; } ?>
                <tr>
                  <td colspan="7"><b>Total</b></td>
                  <td><b>Rp <?= number_format($totalPendapatan, 0, ',', '.'); ?></b></td>
                  <td><b>Rp <?= number_format($totalModal, 0, ',', '.'); ?></b></td>
                  <td><b>Rp <?= number_format($totalPendapatan - $totalModal, 0, ',', '.'); ?></b></td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php  } ?>
</div>
<?php include '_footer.php'; ?>
<script>
  $(function () {
    $('.select2bs4').select2({ theme: 'bootstrap4' });
  });
</script>
