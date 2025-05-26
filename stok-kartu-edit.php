<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
  if ( $levelLogin === "kurir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }
?>

<?php
// Get data for edit
$id = $_GET["id"];
$stokKartu = query("SELECT * FROM stok_kartu WHERE stok_kartu_id = " . $id)[0];

// Process edit operation
if (isset($_POST["edit"])) {
  $id = htmlspecialchars($_POST['id']);
  $produkNama = htmlspecialchars($_POST['produk_nama']);
  $tanggal = htmlspecialchars($_POST['tanggal']);
  $stokAwal = htmlspecialchars($_POST['stok_awal']);
  $stokMasuk = htmlspecialchars($_POST['stok_masuk']);
  $stokKeluar = htmlspecialchars($_POST['stok_keluar']);
  $sisaStok = $stokAwal + $stokMasuk - $stokKeluar;
  $keterangan = htmlspecialchars($_POST['keterangan']);
  
  $query = "UPDATE stok_kartu SET 
              produk_nama = '$produkNama',
              stok_kartu_tanggal = '$tanggal',
              stok_kartu_stok_awal = '$stokAwal',
              stok_kartu_masuk = '$stokMasuk',
              stok_kartu_keluar = '$stokKeluar',
              stok_kartu_sisa = '$sisaStok',
              stok_kartu_keterangan = '$keterangan'
            WHERE stok_kartu_id = '$id'";
  
  $edit = mysqli_query($conn, $query);
  
  if ($edit) {
    echo "
      <script>
        alert('Data stok kartu berhasil diupdate');
        document.location.href = 'kartu-stock';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('Data stok kartu gagal diupdate');
        document.location.href = 'stok-kartu';
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
        <div class="col-sm-6">
          <h1>Edit Kartu Stok</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item"><a href="stok-kartu">Kartu Stok</a></li>
            <li class="breadcrumb-item active">Edit Data</li>
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
            <h3 class="card-title">Edit Data Kartu Stok</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <form role="form" action="" method="post">
              <div class="card-body">
                <input type="hidden" name="id" value="<?= $stokKartu['stok_kartu_id']; ?>">
                <div class="form-group">
                  <label for="produk_nama">Nama Obat</label>
                  <input type="text" name="produk_nama" class="form-control" id="produk_nama" required value="<?= $stokKartu['produk_nama']; ?>">
                </div>
                <div class="form-group">
                  <label for="tanggal">Tanggal Transaksi</label>
                  <input type="date" name="tanggal" class="form-control" id="tanggal" required value="<?= $stokKartu['stok_kartu_tanggal']; ?>">
                </div>
                <div class="form-group">
                  <label for="stok_awal">Jumlah Stok Awal</label>
                  <input type="number" name="stok_awal" class="form-control" id="stok_awal" required value="<?= $stokKartu['stok_kartu_stok_awal']; ?>">
                </div>
                <div class="form-group">
                  <label for="stok_masuk">Jumlah Barang Masuk</label>
                  <input type="number" name="stok_masuk" class="form-control" id="stok_masuk" required value="<?= $stokKartu['stok_kartu_masuk']; ?>">
                </div>
                <div class="form-group">
                  <label for="stok_keluar">Jumlah Barang Keluar</label>
                  <input type="number" name="stok_keluar" class="form-control" id="stok_keluar" required value="<?= $stokKartu['stok_kartu_keluar']; ?>">
                </div>
                <div class="form-group">
                  <label for="keterangan">Keterangan</label>
                  <textarea name="keterangan" class="form-control" id="keterangan" rows="3"><?= $stokKartu['stok_kartu_keterangan']; ?></textarea>
                </div>
              </div>
              <!-- /.card-body -->

              <div class="card-footer text-right">
                <button type="button" class="btn btn-default" onclick="window.location.href='kartu-stock'">Batal</button>
                <button type="submit" name="edit" class="btn btn-primary">Update</button>
              </div>
            </form>
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

<?php include '_footer.php'; ?>