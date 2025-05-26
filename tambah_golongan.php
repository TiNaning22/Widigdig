<?php
include '_header.php';
include '_nav.php';
include '_sidebar.php';

// Cek jika tombol submit ditekan
if (isset($_POST["submit"])) {
    $nama_golongan = htmlspecialchars($_POST["nama_golongan"]);

    // Query insert ke database
    $query = "INSERT INTO golongan_produk (nama_golongan) VALUES ('$nama_golongan')";
    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        echo "<script>alert('Golongan berhasil ditambahkan'); document.location.href = 'tambah_golongan.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan golongan');</script>";
    }
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Tambah Golongan Produk</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Tambah Golongan</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Form Tambah Golongan</h3>
            </div>
            <form action="" method="post">
              <div class="card-body">
                <div class="form-group">
                  <label>Nama Golongan</label>
                  <input type="text" name="nama_golongan" class="form-control" required>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                <a href="jasa.php" class="btn btn-secondary">Kembali</a>
              </div>
            </form>
          </div>
        </div>

        <!-- Tabel Data Golongan -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Data Golongan Produk</h3>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Nama Golongan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $i = 1;
                  $queryGolongan = mysqli_query($conn, "SELECT * FROM golongan_produk ORDER BY nama_golongan ASC");
                  while ($data = mysqli_fetch_array($queryGolongan)) { 
                  ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= $data['nama_golongan']; ?></td>
                      <td>
                        <a href="?delete=<?= $data['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                          <i class="fa fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</div>

<?php include '_footer.php'; ?>

<!-- Script DataTable -->
<script>
  $(document).ready(function() {
    $("#example1").DataTable();
  });
</script>

<?php
// Proses hapus golongan
if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    $query = "DELETE FROM golongan_produk WHERE id = $id";
    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        echo "<script>alert('Golongan berhasil dihapus'); document.location.href = 'tambah_golongan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus golongan');</script>";
    }
}
?>
