<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 

  if ($levelLogin === "kasir") {
    echo "<script>document.location.href = 'bo';</script>";
  }

  // Cek jika tombol submit untuk tambah data ditekan
  if (isset($_POST["submit_tambah"])) {
    if (tambahArusStock($_POST) > 0) {
      echo "<script>document.location.href = 'arus-stock';</script>";
    } else {
      echo "<script>alert('Data gagal ditambahkan');</script>";
    }
  }

  // Cek jika tombol submit untuk edit data ditekan
  if (isset($_POST["submit_edit"])) {
    if (ubahArusStock($_POST) > 0) {
      echo "<script>document.location.href = 'arus-stock';</script>";
    } else {
      echo "<script>alert('Data gagal diubah');</script>";
    }
  }

  // Cek jika ada parameter id untuk delete
  if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    if (hapusArusStock($id) > 0) {
      echo "<script>alert('Data berhasil dihapus'); document.location.href = 'arus-stock';</script>";
    } else {
      echo "<script>alert('Data gagal dihapus'); document.location.href = 'arus-stock';</script>";
    }
  }

  // Function tambah data
  function tambahArusStock($data) {
    global $conn;
    $tanggal = htmlspecialchars($data["tanggal"]);
    $nama_kas = htmlspecialchars($data["nama_kas"]);
    $jenis_kas = htmlspecialchars($data["jenis_kas"]);
    $nominal = htmlspecialchars($data["nominal"]);
    $keterangan = htmlspecialchars($data["keterangan"]);
    $cabang = htmlspecialchars($data["cabang"]);

    $query = "INSERT INTO arus_stock VALUES (
                '', 
                '$tanggal', 
                '$nama_kas', 
                '$jenis_kas', 
                '$nominal', 
                '$keterangan', 
                '$cabang',
                CURRENT_TIMESTAMP,
                NULL
              )";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
  }

  // Function edit data
  function ubahArusStock($data) {
    global $conn;
    $id = htmlspecialchars($data["id"]);
    $tanggal = htmlspecialchars($data["tanggal"]);
    $nama_kas = htmlspecialchars($data["nama_kas"]);
    $jenis_kas = htmlspecialchars($data["jenis_kas"]);
    $nominal = htmlspecialchars($data["nominal"]);
    $keterangan = htmlspecialchars($data["keterangan"]);

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

  // Function hapus data
  function hapusArusStock($id) {
    global $conn;
    $query = "DELETE FROM arus_stock WHERE id = $id";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
  }

  // Ambil semua data
  $queryArusStock = mysqli_query($conn, "SELECT * FROM arus_stock WHERE cabang = '$sessionCabang' ORDER BY tanggal DESC");
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Data Arus Kas</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Arus Kas</li>
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
          <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahArusStock">
            Tambah Data
          </button>

          <!-- Modal Tambah Data -->
          <div class="modal fade" id="modalTambahArusStock" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Tambah Arus Kas</h5>
                  <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                  </button>
                </div>
                <form action="" method="post">
                  <div class="modal-body">
                    <div class="form-group">
                      <label>Tanggal</label>
                      <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label>Nama Kas</label>
                      <input type="text" name="nama_kas" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label>Jenis Kas</label>
                      <select name="jenis_kas" class="form-control" required>
                        <option value="masuk">Masuk</option>
                        <option value="keluar">Keluar</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Nominal</label>
                      <input type="number" name="nominal" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label>Keterangan</label>
                      <textarea name="keterangan" class="form-control"></textarea>
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
              <h3 class="card-title">Data Arus Stock</h3>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Nama Kas</th>
                    <th>Jenis</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; while($data = mysqli_fetch_array($queryArusStock)) { ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                      <td><?= $data['nama_kas']; ?></td>
                      <td><?= ucfirst($data['jenis_kas']); ?></td>
                      <td>Rp. <?= number_format($data['nominal'], 0, ',', '.'); ?></td>
                      <td><?= $data['keterangan']; ?></td>
                      <td>
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalEditArusStock<?= $data['id']; ?>">
                          <i class="fa fa-edit"></i>
                        </button>
                        <a href="?delete=<?= $data['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin hapus data?')">
                          <i class="fa fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                    
                    <!-- Modal Edit untuk Setiap Data -->
                    <div class="modal fade" id="modalEditArusStock<?= $data['id']; ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Edit Arus Stock</h5>
                            <button type="button" class="close" data-dismiss="modal">
                              <span>&times;</span>
                            </button>
                          </div>
                          <form action="" method="post">
                            <div class="modal-body">
                              <input type="hidden" name="id" value="<?= $data['id']; ?>">
                              <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal']; ?>" required>
                              </div>
                              <div class="form-group">
                                <label>Nama Kas</label>
                                <input type="text" name="nama_kas" class="form-control" value="<?= $data['nama_kas']; ?>" required>
                              </div>
                              <div class="form-group">
                                <label>Jenis Kas</label>
                                <select name="jenis_kas" class="form-control" required>
                                  <option value="masuk" <?= $data['jenis_kas'] == 'masuk' ? 'selected' : ''; ?>>Masuk</option>
                                  <option value="keluar" <?= $data['jenis_kas'] == 'keluar' ? 'selected' : ''; ?>>Keluar</option>
                                </select>
                              </div>
                              <div class="form-group">
                                <label>Nominal</label>
                                <input type="number" name="nominal" class="form-control" value="<?= $data['nominal']; ?>" required>
                              </div>
                              <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="keterangan" class="form-control"><?= $data['keterangan']; ?></textarea>
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

<!-- Modal Konfirmasi Delete -->
<div class="modal fade" id="modalDeleteConfirm" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Hapus</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus data ini?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <a href="#" id="btn-delete-confirm" class="btn btn-danger">Hapus</a>
      </div>
    </div>
  </div>
</div>

<?php include '_footer.php'; ?>

<script>
  // Script untuk fungsi delete dengan konfirmasi modal (opsional)
  $(document).ready(function() {
    $('.btn-delete').click(function(e) {
      e.preventDefault();
      var deleteUrl = $(this).attr('href');
      $('#btn-delete-confirm').attr('href', deleteUrl);
      $('#modalDeleteConfirm').modal('show');
    });
  });
</script>