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
          <h1>Analisis Harga Barang</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Analisis Harga</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Analisis Harga Barang</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="analisis-harga-barang" class="table table-bordered table-striped table-laporan">
                <thead>
                <tr>
                  <th style="width: 6%;">No.</th>
                  <th style="width: 13%;">Kode Barang</th>
                  <th>Nama</th>
                  <th>Kategori</th>
                  <th>Harga</th>
                  <th>Status Harga</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                  $i = 1; 
                  $queryProduct = $conn->query("SELECT barang.barang_id, barang.barang_kode, barang.barang_nama, barang.barang_harga, kategori.kategori_nama
                             FROM barang 
                             JOIN kategori ON barang.kategori_id = kategori.kategori_id
                             WHERE barang_cabang = '".$sessionCabang."' ORDER BY barang_harga DESC");
                  while ($rowProduct = mysqli_fetch_array($queryProduct)) {
                    $statusHarga = ($rowProduct['barang_harga'] > 100000) ? "Tidak Sesuai" : "Sesuai";
                ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $rowProduct['barang_kode']; ?></td>
                  <td><?= $rowProduct['barang_nama']; ?></td>
                  <td><?= $rowProduct['kategori_nama']; ?></td>
                  <td>Rp. <?= number_format($rowProduct['barang_harga'], 0, ',', '.'); ?></td>
                  <td>
                    <span class="badge <?= ($statusHarga == 'Tidak Sesuai') ? 'badge-danger' : 'badge-success'; ?>">
                      <?= $statusHarga; ?>
                    </span>
                  </td>
                </tr>
                <?php $i++; ?>
                <?php } ?>
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
</div>
</div>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $("#analisis-harga-barang").DataTable();
  });
</script>
</body>
</html>
