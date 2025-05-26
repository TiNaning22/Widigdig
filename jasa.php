<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 

  if ($levelLogin === "kasir") {
    echo "<script>document.location.href = 'bo';</script>";
  }

  // Cek jika tombol submit untuk tambah data ditekan
  if (isset($_POST["submit_tambah"])) {
    if (tambahJasa($_POST) > 0) {
      echo "<script>document.location.href = 'jasa';</script>";
    } else {
      echo "<script>alert('Data gagal ditambahkan');</script>";
    }
  }

  // Cek jika tombol submit untuk edit data ditekan
  if (isset($_POST["submit_edit"])) {
    if (ubahJasa($_POST) > 0) {
      echo "<script>document.location.href = 'jasa';</script>";
    } else {
      echo "<script>alert('Data gagal diubah');</script>";
    }
  }

  // Cek jika ada parameter id untuk delete
  if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    if (hapusJasa($id) > 0) {
      echo "<script>alert('Data berhasil dihapus'); document.location.href = 'jasa';</script>";
    } else {
      echo "<script>alert('Data gagal dihapus'); document.location.href = 'jasa';</script>";
    }
  }

  // Function tambah data
  function tambahJasa($data) {
    global $conn;
    $nama_jasa = htmlspecialchars($data["nama_jasa"]);
    $satuan = htmlspecialchars($data["satuan"]);
    $hna_ppn = htmlspecialchars($data["hna_ppn"]);
    $harga_jual = htmlspecialchars($data["harga_jual"]);
    $margin = htmlspecialchars($data["margin"]);
    $diskon = htmlspecialchars($data["diskon"]);
    $golongan_produk = htmlspecialchars($data["golongan_produk"]);
    $rak = htmlspecialchars($data["rak"]);
    $cabang = htmlspecialchars($data["cabang"]);

    $query = "INSERT INTO jasa VALUES (
                '', 
                '$nama_jasa', 
                '$satuan', 
                '$hna_ppn', 
                '$harga_jual', 
                '$margin', 
                '$diskon', 
                '$golongan_produk', 
                '$rak', 
                '$cabang',
                CURRENT_TIMESTAMP,
                NULL
              )";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
  }

  // Function edit data
  function ubahJasa($data) {
    global $conn;
    $id = htmlspecialchars($data["id"]);
    $nama_jasa = htmlspecialchars($data["nama_jasa"]);
    $satuan = htmlspecialchars($data["satuan"]);
    $hna_ppn = htmlspecialchars($data["hna_ppn"]);
    $harga_jual = htmlspecialchars($data["harga_jual"]);
    $margin = htmlspecialchars($data["margin"]);
    $diskon = htmlspecialchars($data["diskon"]);
    $golongan_produk = htmlspecialchars($data["golongan_produk"]);
    $rak = htmlspecialchars($data["rak"]);

    $query = "UPDATE jasa SET 
                nama_jasa = '$nama_jasa',
                satuan = '$satuan',
                hna_ppn = '$hna_ppn',
                harga_jual = '$harga_jual',
                margin = '$margin',
                diskon = '$diskon',
                golongan_produk = '$golongan_produk',
                rak = '$rak',
                updated_at = CURRENT_TIMESTAMP
              WHERE id = $id";
    
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
  }

  // Function hapus data
  function hapusJasa($id) {
    global $conn;
    $query = "DELETE FROM jasa WHERE id = $id";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
  }

  // Query untuk ambil data golongan produk
  $queryGolongan = mysqli_query($conn, "SELECT * FROM golongan_produk ORDER BY nama_golongan ASC");

  // Ambil semua data jasa
  $queryJasa = mysqli_query($conn, "SELECT * FROM jasa WHERE cabang = '$sessionCabang' ORDER BY nama_jasa ASC");
  
   $queryRak = mysqli_query($conn, "SELECT * FROM rak ORDER BY nama_rak ASC");
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Data Jasa</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Jasa</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

          <!-- Button trigger modal tambah -->
          <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahJasa">
            Tambah Data
          </button>

          <!-- Modal Tambah Data -->
          <div class="modal fade" id="modalTambahJasa" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Tambah Jasa</h5>
                  <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                  </button>
                </div>
                <form action="" method="post">
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Nama Jasa</label>
                          <input type="text" name="nama_jasa" class="form-control" required>
                        </div>
                        <div class="form-group">
                          <label>Satuan</label>
                          <input type="text" name="satuan" class="form-control" required>
                        </div>
                        <div class="form-group">
                          <label>HNA+PPN</label>
                          <input type="number" name="hna_ppn" class="form-control" required>
                        </div>
                        <div class="form-group">
                          <label>Harga Jual</label>
                          <input type="number" name="harga_jual" class="form-control" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Margin (%)</label>
                          <input type="number" name="margin" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group">
                          <label>Diskon (%)</label>
                          <input type="number" name="diskon" class="form-control" step="0.01" value="0">
                        </div>
                        <div class="form-group">
                          <label>Golongan Produk</label>
                          <select name="golongan_produk" class="form-control" required>
                            <option value="">-- Pilih Golongan --</option>
                            <?php while($golongan = mysqli_fetch_array($queryGolongan)) { ?>
                              <option value="<?= $golongan['id']; ?>"><?= $golongan['nama_golongan']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="form-group">
                          <label>Rak</label>
                          <select name="rak" class="form-control" required>
                            <option value="">-- Pilih Rak --</option>
                            <?php while ($rak = mysqli_fetch_array($queryRak)) { ?>
                              <option value="<?= $rak['rak_id']; ?>"><?= $rak['nama_rak']; ?></option>
                            <?php } ?>
                          </select>
                        </div>

                      </div>
                    </div>
                    <input type="hidden" name="cabang" value="<?= $sessionCabang; ?>">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="submit_tambah" class="btn btn-primary">Simpan</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Tabel Data -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Data Jasa</h3>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Nama Jasa</th>
                    <th>Satuan</th>
                    <th>HNA+PPN</th>
                    <th>Harga Jual</th>
                    <th>Margin</th>
                    <th>Diskon</th>
                    <th>Golongan</th>
                    <th>Rak</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $i = 1; 
                  while($data = mysqli_fetch_array($queryJasa)) { 
                    // Ambil nama golongan
                    $idGolongan = $data['golongan_produk'];
                    $queryNamaGolongan = mysqli_query($conn, "SELECT nama_golongan FROM golongan_produk WHERE id = $idGolongan");
                    $namaGolongan = mysqli_fetch_array($queryNamaGolongan);
                  ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= $data['nama_jasa']; ?></td>
                      <td><?= $data['satuan']; ?></td>
                      <td>Rp. <?= number_format($data['hna_ppn'], 0, ',', '.'); ?></td>
                      <td>Rp. <?= number_format($data['harga_jual'], 0, ',', '.'); ?></td>
                      <td><?= $data['margin']; ?>%</td>
                      <td><?= $data['diskon']; ?>%</td>
                      <td><?= $namaGolongan['nama_golongan']; ?></td>
                     <?php 
                      $idRak = $data['rak'];
                      $queryNamaRak = mysqli_query($conn, "SELECT nama_rak FROM rak WHERE rak_id = '$idRak'");
                      $namaRak = mysqli_fetch_array($queryNamaRak);
                     ?>
                      <td><?= $namaRak['nama_rak']; ?></td>

                      <td>
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalEditJasa<?= $data['id']; ?>">
                          <i class="fa fa-edit"></i>
                        </button>
                        <a href="?delete=<?= $data['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin hapus data?')">
                          <i class="fa fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                    
                    <!-- Modal Edit untuk Setiap Data -->
                    <div class="modal fade" id="modalEditJasa<?= $data['id']; ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Edit Jasa</h5>
                            <button type="button" class="close" data-dismiss="modal">
                              <span>&times;</span>
                            </button>
                          </div>
                          <form action="" method="post">
                            <div class="modal-body">
                              <input type="hidden" name="id" value="<?= $data['id']; ?>">
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label>Nama Jasa</label>
                                    <input type="text" name="nama_jasa" class="form-control" value="<?= $data['nama_jasa']; ?>" required>
                                  </div>
                                  <div class="form-group">
                                    <label>Satuan</label>
                                    <input type="text" name="satuan" class="form-control" value="<?= $data['satuan']; ?>" required>
                                  </div>
                                  <div class="form-group">
                                    <label>HNA+PPN</label>
                                    <input type="number" name="hna_ppn" class="form-control" value="<?= $data['hna_ppn']; ?>" required>
                                  </div>
                                  <div class="form-group">
                                    <label>Harga Jual</label>
                                    <input type="number" name="harga_jual" class="form-control" value="<?= $data['harga_jual']; ?>" required>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label>Margin (%)</label>
                                    <input type="number" name="margin" class="form-control" step="0.01" value="<?= $data['margin']; ?>" required>
                                  </div>
                                  <div class="form-group">
                                    <label>Diskon (%)</label>
                                    <input type="number" name="diskon" class="form-control" step="0.01" value="<?= $data['diskon']; ?>">
                                  </div>
                                  <div class="form-group">
                                    <label>Golongan Produk</label>
                                    <select name="golongan_produk" class="form-control" required>
                                      <?php 
                                      $queryGolonganEdit = mysqli_query($conn, "SELECT * FROM golongan_produk ORDER BY nama_golongan ASC");
                                      while($golongan = mysqli_fetch_array($queryGolonganEdit)) { 
                                      ?>
                                        <option value="<?= $golongan['id']; ?>" <?= $data['golongan_produk'] == $golongan['id'] ? 'selected' : ''; ?>><?= $golongan['nama_golongan']; ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>
                                 <div class="form-group">
                                      <label>Rak</label>
                                      <select name="rak" class="form-control" required>
                                        <?php 
                                        $queryRakEdit = mysqli_query($conn, "SELECT * FROM rak ORDER BY nama_rak ASC");
                                        while($rak = mysqli_fetch_array($queryRakEdit)) { 
                                        ?>
                                          <option value="<?= $rak['rak_id']; ?>" <?= $data['rak'] == $rak['rak_id'] ? 'selected' : ''; ?>>
                                            <?= $rak['nama_rak']; ?>
                                          </option>
                                        <?php } ?>
                                      </select>
                                    </div>

                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                              <button type="submit" name="submit_edit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
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

<script>
  $(function() {
    // DataTable initialization
    $("#example1").DataTable({
    "responsive": true,
    "lengthChange": false,
    "autoWidth": false,
    "destroy": true, // Menangani error re-inisialisasi
    "buttons": ["copy", "csv", "excel", "pdf", "print"]
});

    
    // Auto calculate margin when hna_ppn and harga_jual change
    $('input[name="hna_ppn"], input[name="harga_jual"]').on('input', function() {
      var form = $(this).closest('form');
      var hna = parseFloat(form.find('input[name="hna_ppn"]').val()) || 0;
      var hargaJual = parseFloat(form.find('input[name="harga_jual"]').val()) || 0;
      
      if (hna > 0 && hargaJual > 0) {
        var margin = ((hargaJual - hna) / hna * 100).toFixed(2);
        form.find('input[name="margin"]').val(margin);
      }
    });
    
    // Auto calculate harga jual when hna_ppn and margin change
    $('input[name="hna_ppn"], input[name="margin"]').on('input', function() {
      var form = $(this).closest('form');
      var hna = parseFloat(form.find('input[name="hna_ppn"]').val()) || 0;
      var margin = parseFloat(form.find('input[name="margin"]').val()) || 0;
      
      if (hna > 0 && form.find('input[name="harga_jual"]').is(':focus') === false) {
        var hargaJual = Math.round(hna * (1 + margin / 100));
        form.find('input[name="harga_jual"]').val(hargaJual);
      }
    });
  });
</script>