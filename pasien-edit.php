<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
  if ( $levelLogin === "kasir" && $levelLogin === "kurir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }
?>

<?php
// ambil data di URL
$id = $_GET["id"];

// query data pasien berdasarkan id
$pasien = query("SELECT * FROM pasien WHERE pasien_id = $id")[0];

// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // cek apakah data berhasil diubah atau tidak
  if( ubahPasien($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'pasien';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('Data gagal diubah');
      </script>
    ";
  }
}
?>

<?php 
function ubahPasien($data) {
  global $conn;
  
  $id = $data["pasien_id"];
  $pasien_kode = htmlspecialchars($data["pasien_kode"]);
  $pasien_nama = htmlspecialchars($data["pasien_nama"]);
  $pasien_alamat = htmlspecialchars($data["pasien_alamat"]);
  $pasien_hp = htmlspecialchars($data["pasien_hp"]);
  $pasien_email = htmlspecialchars($data["pasien_email"]);
  $pasien_kota = htmlspecialchars($data["pasien_kota"]);
  $pasien_kodepos = htmlspecialchars($data["pasien_kodepos"]);
  $pasien_status = $data["pasien_status"];
  
  // query update data
  $query = "UPDATE pasien SET 
              pasien_kode = '$pasien_kode',
              pasien_nama = '$pasien_nama',
              pasien_alamat = '$pasien_alamat',
              pasien_hp = '$pasien_hp',
              pasien_email = '$pasien_email',
              pasien_kota = '$pasien_kota',
              pasien_kodepos = '$pasien_kodepos',
              pasien_status = '$pasien_status'
            WHERE pasien_id = $id
          ";
  
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
          <h1>Edit Data Pasien</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Pasien</li>
            <li class="breadcrumb-item active">Edit Data</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Form Edit Data Pasien</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form role="form" action="" method="post">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 col-lg-6">
                    <input type="hidden" name="pasien_id" value="<?= $pasien['pasien_id']; ?>">
                    <div class="form-group">
                      <label for="pasien_kode">Kode Pasien</label>
                      <input type="text" name="pasien_kode" class="form-control" id="pasien_kode" value="<?= $pasien['pasien_kode']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="pasien_nama">Nama Pasien</label>
                      <input type="text" name="pasien_nama" class="form-control" id="pasien_nama" value="<?= $pasien['pasien_nama']; ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="pasien_alamat">Alamat</label>
                      <textarea name="pasien_alamat" class="form-control" id="pasien_alamat" required><?= $pasien['pasien_alamat']; ?></textarea>
                    </div>
                    <div class="form-group">
                      <label for="pasien_hp">No. HP</label>
                      <input type="text" name="pasien_hp" class="form-control" id="pasien_hp" value="<?= $pasien['pasien_hp']; ?>" required>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                      <label for="pasien_email">Email</label>
                      <input type="email" name="pasien_email" class="form-control" id="pasien_email" value="<?= $pasien['pasien_email']; ?>">
                    </div>
                    <div class="form-group">
                      <label for="pasien_kota">Kota</label>
                      <input type="text" name="pasien_kota" class="form-control" id="pasien_kota" value="<?= $pasien['pasien_kota']; ?>">
                    </div>
                    <div class="form-group">
                      <label for="pasien_kodepos">Kode Pos</label>
                      <input type="text" name="pasien_kodepos" class="form-control" id="pasien_kodepos" value="<?= $pasien['pasien_kodepos']; ?>">
                    </div>
                    <div class="form-group">
                      <label for="pasien_status">Status</label>
                      <select name="pasien_status" required="" class="form-control" id="pasien_status">
                        <option value="">-- Pilih Status --</option>
                        <?php if ( $pasien['pasien_status'] === "1" ) : ?>
                          <option value="1" selected>Aktif</option>
                          <option value="0">Tidak Aktif</option>
                        <?php else : ?>
                          <option value="1">Aktif</option>
                          <option value="0" selected>Tidak Aktif</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                </div>
                </div>
              </div>
              <!-- /.card-body -->

              <div class="card-footer text-right">
                <button type="button" class="btn btn-default" id="pasien-edit-cancel">Batal</button>
                <button type="submit" name="submit" class="btn btn-primary">Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  $(document).ready(function(){
    $("#pasien-edit-cancel").click(function(){
      window.location.href = "pasien";
    });
  });
</script>

<?php include '_footer.php'; ?>