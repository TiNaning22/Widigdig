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

// query data bank berdasarkan id
$bank = query("SELECT * FROM bank WHERE bank_id = $id")[0];

// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // cek apakah data berhasil diubah atau tidak
  if( ubahBank($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'bank';
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
function ubahBank($data) {
  global $conn;
  
  $id = $data["bank_id"];
  $bank_nama = htmlspecialchars($data["bank_nama"]);
  $bank_status = $data["bank_status"];
  
  // query update data
  $query = "UPDATE bank SET 
              bank_nama = '$bank_nama',
              bank_status = '$bank_status'
            WHERE bank_id = $id
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
          <h1>Edit Data Bank</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Bank</li>
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
              <h3 class="card-title">Form Edit Data Bank</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form role="form" action="" method="post">
              <div class="card-body">
                <input type="hidden" name="bank_id" value="<?= $bank['bank_id']; ?>">
                <div class="form-group">
                  <label for="bank_nama">Nama Bank</label>
                  <input type="text" name="bank_nama" class="form-control" id="bank_nama" value="<?= $bank['bank_nama']; ?>" required>
                </div>
                <div class="form-group">
                  <label for="bank_status">Status</label>
                  <select name="bank_status" required="" class="form-control" id="bank_status">
                    <option value="">-- Pilih Status --</option>
                    <?php if ( $bank['bank_status'] === "1" ) : ?>
                      <option value="1" selected>Aktif</option>
                      <option value="0">Tidak Aktif</option>
                    <?php else : ?>
                      <option value="1">Aktif</option>
                      <option value="0" selected>Tidak Aktif</option>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
              <!-- /.card-body -->

              <div class="card-footer text-right">
                <button type="button" class="btn btn-default" id="bank-edit-cancel">Batal</button>
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
    $("#bank-edit-cancel").click(function(){
      window.location.href = "bank";
    });
  });
</script>

<?php include '_footer.php'; ?>