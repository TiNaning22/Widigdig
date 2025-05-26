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
// Generate kode pasien
function generateKodePasien() {
  global $conn;
  global $sessionCabang;
  
  $queryKode = mysqli_query($conn, "SELECT max(pasien_kode) as kodeTerbesar FROM pasien WHERE pasien_cabang = $sessionCabang");
  $dataKode = mysqli_fetch_array($queryKode);
  $kodePasien = $dataKode['kodeTerbesar'];
  
  $urutan = (int) substr($kodePasien, 4, 6);
  $urutan++;
  
  $huruf = "PSN-";
  $kodePasien = $huruf . sprintf("%06s", $urutan);
  
  return $kodePasien;
}

// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // cek apakah data berhasil di tambahkan atau tidak
  if( tambahPasien($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'pasien';
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

<!-- tambahPasien function -->
<?php 
function tambahPasien($data) {
  global $conn;
  global $sessionCabang;
  
  $pasien_kode = htmlspecialchars($data["pasien_kode"]);
  $pasien_nama = htmlspecialchars($data["pasien_nama"]);
  $pasien_alamat = htmlspecialchars($data["pasien_alamat"]);
  $pasien_hp = htmlspecialchars($data["pasien_hp"]);
  $pasien_email = htmlspecialchars($data["pasien_email"]);
  $pasien_kota = htmlspecialchars($data["pasien_kota"]);
  $pasien_kodepos = htmlspecialchars($data["pasien_kodepos"]);
  $pasien_status = $data["pasien_status"];
  $pasien_cabang = $sessionCabang;
  $pasien_created = date("Y-m-d");
  
  // query insert data
  $query = "INSERT INTO pasien VALUES ('', '$pasien_kode', '$pasien_nama', '$pasien_alamat', '$pasien_hp', '$pasien_email', '$pasien_kota', '$pasien_kodepos', '$pasien_status', '$pasien_cabang', '$pasien_created')";
  
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
          <h1>Tambah Data Pasien</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Pasien</li>
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
              <h3 class="card-title">Form Tambah Data Pasien</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form role="form" action="" method="post">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                      <label for="pasien_kode">Kode Pasien</label>
                      <input type="text" name="pasien_kode" class="form-control" id="pasien_kode" value="<?= generateKodePasien(); ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="pasien_nama">Nama Pasien</label>
                      <input type="text" name="pasien_nama" class="form-control" id="pasien_nama" placeholder="Masukan Nama Pasien" required>
                    </div>
                    <div class="form-group">
                      <label for="pasien_alamat">Alamat</label>
                      <textarea name="pasien_alamat" class="form-control" id="pasien_alamat" placeholder="Masukan Alamat Pasien" required></textarea>
                    </div>
                    <div class="form-group">
                      <label for="pasien_hp">No. HP</label>
                      <input type="text" name="pasien_hp" class="form-control" id="pasien_hp" placeholder="Masukan No. HP Pasien" required>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                      <label for="pasien_email">Email</label>
                      <input type="email" name="pasien_email" class="form-control" id="pasien_email" placeholder="Masukan Email Pasien">
                    </div>
                    <div class="form-group">
                      <label for="pasien_kota">Kota</label>
                      <input type="text" name="pasien_kota" class="form-control" id="pasien_kota" placeholder="Masukan Kota">
                    </div>
                    <div class="form-group">
                      <label for="pasien_kodepos">Kode Pos</label>
                      <input type="text" name="pasien_kodepos" class="form-control" id="pasien_kodepos" placeholder="Masukan Kode Pos">
                    </div>
                    <div class="form-group">
                      <label for="pasien_status">Status</label>
                      <select name="pasien_status" required="" class="form-control" id="pasien_status">
                        <option value="">-- Pilih Status --</option>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-body -->

              <div class="card-footer text-right">
                <button type="button" class="btn btn-default" id="pasien-add-cancel">Batal</button>
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
    $("#pasien-add-cancel").click(function(){
      window.location.href = "pasien";
    });
  });
</script>

<?php include '_footer.php'; ?>