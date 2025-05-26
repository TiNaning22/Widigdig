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
// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // cek apakah data berhasil di tambahkan atau tidak
  if( tambahBank($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'bank';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('Data gagal ditambahkan');
      </script>
    ";
  }
}
?>

<?php 
function tambahBank($data) {
  global $conn;
  global $sessionCabang;
  
  $bank_nama = htmlspecialchars($data["bank_nama"]);
  $bank_status = $data["bank_status"];
  $bank_cabang = $sessionCabang;
  $bank_created = date("Y-m-d");
  
  // query insert data
  $query = "INSERT INTO bank VALUES ('', '$bank_nama', '$bank_status', '$bank_cabang', '$bank_created')";
  
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
          <h1>Tambah Data Bank</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Bank</li>
            <li class="breadcrumb-item active">Tambah Data</li>
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
              <h3 class="card-title">Form Tambah Data Bank</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form role="form" action="" method="post">
              <div class="card-body">
                <div class="form-group">
                  <label for="bank_nama">Nama Bank</label>
                  <input type="text" name="bank_nama" class="form-control" id="bank_nama" placeholder="Masukan Nama Bank" required>
                </div>
                <div class="form-group">
                  <label for="bank_status">Status</label>
                  <select name="bank_status" required="" class="form-control" id="bank_status">
                    <option value="">-- Pilih Status --</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                  </select>
                </div>
              </div>
              <!-- /.card-body -->

              <div class="card-footer text-right">
                <button type="button" class="btn btn-default" id="bank-add-cancel">Batal</button>
                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
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
    $("#bank-add-cancel").click(function(){
      window.location.href = "bank";
    });
  });
</script>

<?php include '_footer.php'; ?>