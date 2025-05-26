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
            <h1>Laporan Pembelian Per Produk</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Pembelian Per Produk</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>


    <section class="content">
      <div class="container-fluid">
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Filter Data Berdasrkan Tanggal dan Produk</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
            </div>
          </div>
          <!-- /.card-header -->
          <form role="form" action="" method="POST">
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="form-control" id="tanggal_awal" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" id="tanggal_akhir" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_akhir">Produk</label>
                        <select class="form-control select2bs4" required="" name="barang_id">
                            <option selected="selected" value="">-- Pilih Produk --</option>
                            <?php  
                              $produk = query("SELECT * FROM barang ORDER BY barang_id DESC ");
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
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Laporan Produk Pembelian</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="Laporan-produk-pembelian" class="table table-bordered table-striped table-laporan">
                  <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 13%;">Invoice</th>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>QTY Pembelian</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $i = 1; 
                    $total = 0;
                    $queryPembelian = $conn->query("SELECT pembelian.pembelian_id, pembelian.pembelian_barang_id, pembelian.pembelian_invoice, pembelian.pembelian_date, pembelian.barang_id, pembelian.barang_qty, pembelian.pembelian_cabang, barang.barang_id, barang.barang_nama
                               FROM pembelian 
                               JOIN barang ON pembelian.barang_id = barang.barang_id
                               WHERE pembelian_cabang = '".$sessionCabang."' && pembelian_barang_id = '".$barang_id."' && pembelian_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' 
                               ORDER BY pembelian_id DESC
                               ");
                    while ($rowProduct = mysqli_fetch_array($queryPembelian)) {
                    $total += $rowProduct['barang_qty'];
                  ?>
                  <tr>
                    	<td><?= $i; ?></td>
                      <td><?= $rowProduct['pembelian_invoice']; ?></td>
                      <td><?= $rowProduct['pembelian_date']; ?></td>
                      <td><?= $rowProduct['barang_nama']; ?></td>
                      <td><?= $rowProduct['barang_qty']; ?></td>
                  </tr>
                  <?php $i++; ?>
                  <?php } ?>
                  <tr>
                      <td colspan="5">
                        <b>Total <span style="color: red;">Pembelian <?= mysqli_num_rows($queryPembelian); ?>x</span> dengan Jumlah Keseluruhan <span style="color: red">QTY Pembelian <?= $total; ?></span></b>
                      </td>
                  </tr>
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
    <?php  } ?>
  </div>
</div>



<?php include '_footer.php'; ?>
<script>
  $(function () {

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  });
</script>
</body>
</html>