<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "kasir" && $levelLogin === "kurir" ) {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }  
?>
<?php  

// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // Ambil data dari form
  $sales_nama = $_POST['sales_nama'];
  $barang_id = $_POST['barang_id'];
  $tanggal_penitipan = $_POST['tanggal_penitipan'];
  $jumlah = $_POST['jumlah'];

  // Query untuk menambahkan data konsinyasi
  $query = "INSERT INTO penerimaan_konsinyasi (sales_nama, barang_id, tanggal_penitipan, jumlah) 
            VALUES ('$sales_nama', $barang_id, '$tanggal_penitipan', $jumlah)";

  if (mysqli_query($conn, $query)) {
    echo "
      <script>
        alert('Data konsinyasi berhasil ditambahkan.');
        document.location.href = 'penerimaan-konsinyasi';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('Data gagal ditambahkan: " . mysqli_error($conn) . "');
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
          <h1>Tambah Data Konsinyasi</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Tambah Data Konsinyasi</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <form role="form" action="" method="post">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Data Konsinyasi</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                      <label for="sales_nama">Nama Sales</label>
                      <input type="text" name="sales_nama" class="form-control" id="sales_nama" placeholder="Input Nama Sales" required>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                      <label for="barang_id">Barang</label>
                      <select name="barang_id" class="form-control" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php
                          // Query semua barang
                          $barangs = query("SELECT * FROM barang_internal ORDER BY barang_nama ASC");
                          foreach ($barangs as $barang) :
                        ?>
                        <option value="<?= $barang['barang_id']; ?>">
                          <?= $barang['barang_nama']; ?> (Stok: <?= $barang['barang_stock']; ?>)
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                      <label for="tanggal_penitipan">Tanggal Penitipan</label>
                      <input type="date" name="tanggal_penitipan" class="form-control" id="tanggal_penitipan" required>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                      <label for="jumlah">Jumlah</label>
                      <input type="number" name="jumlah" class="form-control" id="jumlah" placeholder="Input Jumlah" required>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-body -->

              <div class="card-footer text-right">
                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
<!-- /.content-wrapper -->

<?php include '_footer.php'; ?>