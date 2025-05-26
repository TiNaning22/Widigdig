<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
  if ($levelLogin === "kasir") {
    echo "<script>document.location.href = 'bo';</script>";
  }
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Laporan Rekap Laba/Rugi Penjualan Harian</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Rekap Laba/Rugi Harian</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

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
        <form role="form" action="" method="POST">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="tanggal">Tanggal</label>
                  <input type="date" name="tanggal" class="form-control" id="tanggal" required>
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
    </div>
  </section>

  <?php if(isset($_POST["submit"])): ?>
    <?php  
      $tanggal = $_POST['tanggal'];
      $queryLabaRugi = $conn->query("SELECT penjualan.penjualan_invoice, penjualan.penjualan_date, 
                                        SUM(penjualan.penjualan_harga) AS total_pendapatan, 
                                        SUM(barang.barang_modal * penjualan.barang_qty) AS total_modal, 
                                        (SUM(penjualan.penjualan_harga) - SUM(barang.barang_modal * penjualan.barang_qty)) AS total_keuntungan
                                  FROM penjualan 
                                  JOIN barang ON penjualan.barang_id = barang.barang_id
                                  WHERE penjualan_cabang = '$sessionCabang' AND penjualan_date = '$tanggal'
                                  GROUP BY penjualan_date");
    ?>
    <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Laporan Rekap Laba/Rugi Harian</h3>
            </div>
            <div class="card-body">
              <div class="table-auto">
                <table id="laporan-rekap-laba-rugi" class="table table-bordered table-striped table-laporan">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th>Tanggal</th>
                      <th>Total Pendapatan</th>
                      <th>Total Modal</th>
                      <th>Total Keuntungan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                      $i = 1;
                      while ($row = mysqli_fetch_array($queryLabaRugi)):
                    ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= $row['penjualan_date']; ?></td>
                      <td><?= number_format($row['total_pendapatan'], 0, ',', '.'); ?></td>
                      <td><?= number_format($row['total_modal'], 0, ',', '.'); ?></td>
                      <td><?= number_format($row['total_keuntungan'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>
</div>

<?php include '_footer.php'; ?>
<script>
  $(function () {
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
  });
</script>
</body>
</html>
