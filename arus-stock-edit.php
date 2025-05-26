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
<?php
// ambil data di URL
$id = abs((int)base64_decode($_GET['id']));

// query data berdasarkan id
$arus_stock = query("SELECT * FROM arus_stock WHERE id = $id")[0];

// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // var_dump($_POST);
  // cek apakah data berhasil diubah atau tidak
  if( ubahArusStock($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'arus-stock';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('data gagal diubah');
      </script>
    ";
  }
  
}

// Function to edit arus stock
function ubahArusStock($data) {
  global $conn;
  
  $id = htmlspecialchars($data["id"]);
  $tanggal = htmlspecialchars($data["tanggal"]);
  $nama_kas = htmlspecialchars($data["nama_kas"]);
  $jenis_kas = htmlspecialchars($data["jenis_kas"]);
  $nominal = htmlspecialchars($data["nominal"]);
  $keterangan = htmlspecialchars($data["keterangan"]);
  
  // Query update data
  $query = "UPDATE arus_stock SET 
              tanggal = '$tanggal',
              nama_kas = '$nama_kas',
              jenis_kas = '$jenis_kas',
              nominal = '$nominal',
              keterangan = '$keterangan',
              updated_at = CURRENT_TIMESTAMP
            WHERE id = $id";
  
  mysqli_query($conn, $query);
  
  return mysqli_affected_rows($conn);
}
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit Arus Stock</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Arus Stock</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Form Edit Arus Stock</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <input type="hidden" name="id" value="<?= $arus_stock['id']; ?>">
                      <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" id="tanggal" value="<?= $arus_stock['tanggal']; ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="nama_kas">Nama Kas</label>
                        <input type="text" name="nama_kas" class="form-control" id="nama_kas" value="<?= $arus_stock['nama_kas']; ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="jenis_kas">Jenis Kas</label>
                        <select name="jenis_kas" id="jenis_kas" class="form-control" required>
                          <option value="">-- Pilih Jenis Kas --</option>
                          <option value="masuk" <?= ($arus_stock['jenis_kas'] == 'masuk') ? 'selected' : ''; ?>>Masuk</option>
                          <option value="keluar" <?= ($arus_stock['jenis_kas'] == 'keluar') ? 'selected' : ''; ?>>Keluar</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input type="number" name="nominal" class="form-control" id="nominal" value="<?= $arus_stock['nominal']; ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3"><?= $arus_stock['keterangan']; ?></textarea>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <button type="submit" name="submit" class="btn btn-primary">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
<?php include '_footer.php'; ?>
</body>
</html>