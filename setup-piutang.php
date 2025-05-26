<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 

  if ($levelLogin === "kasir") {
    echo "<script>document.location.href = 'bo';</script>";
  }

  // Cek jika tombol submit untuk tambah data ditekan
  if (isset($_POST["submit_tambah"])) {
    if (tambahPiutangAwal($_POST) > 0) {
      echo "<script>alert('Data berhasil ditambahkan'); document.location.href = 'setup-piutang';</script>";
    } else {
      echo "<script>alert('Data gagal ditambahkan');</script>";
    }
  }

  // Cek jika tombol submit untuk edit data ditekan
  if (isset($_POST["submit_edit"])) {
    if (ubahPiutangAwal($_POST) > 0) {
      echo "<script>alert('Data berhasil diubah'); document.location.href = 'setup-piutang';</script>";
    } else {
      echo "<script>alert('Data gagal diubah');</script>";
    }
  }

  // Cek jika ada parameter id untuk delete
  if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    if (hapusPiutangAwal($id) > 0) {
      echo "<script>alert('Data berhasil dihapus'); document.location.href = 'setup-piutang';</script>";
    } else {
      echo "<script>alert('Data gagal dihapus'); document.location.href = 'setup-piutang';</script>";
    }
  }

  // Function tambah data
  function tambahPiutangAwal($data) {
    global $conn;
    $no_ref = htmlspecialchars($data["no_ref"]);
    $nama_customer = htmlspecialchars($data["nama_customer"]);
    $tanggal_transaksi = htmlspecialchars($data["tanggal_transaksi"]);
    $nominal_piutang = htmlspecialchars($data["nominal_piutang"]);
    $tanggal_jatuh_tempo = htmlspecialchars($data["tanggal_jatuh_tempo"]);
    $keterangan = htmlspecialchars($data["keterangan"]);
    $cabang = htmlspecialchars($data["cabang"]);
    $status = "belum_lunas";

    $query = "INSERT INTO piutang_awal VALUES (
                '', 
                '$no_ref', 
                '$nama_customer', 
                '$tanggal_transaksi', 
                '$nominal_piutang', 
                '$tanggal_jatuh_tempo', 
                '$keterangan',
                '$status',
                '$cabang',
                CURRENT_TIMESTAMP,
                NULL
              )";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
  }

  // Function edit data
  function ubahPiutangAwal($data) {
    global $conn;
    $id = htmlspecialchars($data["id"]);
    $no_ref = htmlspecialchars($data["no_ref"]);
    $nama_customer = htmlspecialchars($data["nama_customer"]);
    $tanggal_transaksi = htmlspecialchars($data["tanggal_transaksi"]);
    $nominal_piutang = htmlspecialchars($data["nominal_piutang"]);
    $tanggal_jatuh_tempo = htmlspecialchars($data["tanggal_jatuh_tempo"]);
    $keterangan = htmlspecialchars($data["keterangan"]);
    $status = htmlspecialchars($data["status"]);

    $query = "UPDATE piutang_awal SET 
                no_ref = '$no_ref',
                nama_customer = '$nama_customer',
                tanggal_transaksi = '$tanggal_transaksi',
                nominal_piutang = '$nominal_piutang',
                tanggal_jatuh_tempo = '$tanggal_jatuh_tempo',
                keterangan = '$keterangan',
                status = '$status',
                updated_at = CURRENT_TIMESTAMP
              WHERE id = $id";
    
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
  }

  // Function hapus data
  function hapusPiutangAwal($id) {
    global $conn;
    $query = "DELETE FROM piutang_awal WHERE id = $id";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
  }

  // Ambil semua data
  $queryPiutangAwal = mysqli_query($conn, "SELECT * FROM piutang_awal WHERE cabang = '$sessionCabang' ORDER BY tanggal_transaksi DESC");
  
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Setup Saldo Awal Piutang</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Setup Piutang</li>
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
          <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahPiutang">
            Tambah Data Piutang
          </button>

          <!-- Modal Tambah Data -->
          <div class="modal fade" id="modalTambahPiutang" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Tambah Saldo Awal Piutang</h5>
                  <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                  </button>
                </div>
                <form action="" method="post">
                  <div class="modal-body">
                    <div class="form-group">
                      <label>No. Referensi</label>
                      <input type="text" name="no_ref" class="form-control" required>
                    </div>
                   <div class="form-group">
                      <label>Pilih Customer</label>
                      <select name="penjualan_pelanggan" class="form-control select2" required>
                        <option value="">-- Pilih Customer --</option>
                        <?php
                          // Changed from pelanggan to customer table as per the file you shared
                          $customers = query("SELECT * FROM customer WHERE customer_cabang = $sessionCabang ORDER BY customer_nama ASC");
                          foreach($customers as $row):
                            if ($row['customer_id'] > 1) { // Skip the first customer if it's a system record
                        ?>
                        <option value="<?= $row['customer_id']; ?>"><?= $row['customer_nama']; ?> 
                          <?php if(isset($row['customer_hp']) && !empty($row['customer_hp'])): ?> 
                            - <?= $row['customer_hp']; ?>
                          <?php endif; ?>
                          <?php if(isset($row['customer_membership']) && $row['customer_membership'] === "1"): ?> 
                            (Member)
                          <?php endif; ?>
                        </option>
                        <?php 
                            }
                          endforeach; 
                        ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Tanggal Transaksi</label>
                      <input type="date" name="tanggal_transaksi" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label>Nominal Piutang</label>
                      <input type="number" name="nominal_piutang" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label>Tanggal Jatuh Tempo</label>
                      <input type="date" name="tanggal_jatuh_tempo" class="form-control" required>
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
              <h3 class="card-title">Data Saldo Awal Piutang</h3>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>No. Ref</th>
                    <th>Customer</th>
                    <th>Tgl Transaksi</th>
                    <th>Nominal</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; while($data = mysqli_fetch_array($queryPiutangAwal)) { ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= $data['no_ref']; ?></td>
                      <td><?= $data['nama_customer']; ?></td>
                      <td><?= date('d-m-Y', strtotime($data['tanggal_transaksi'])); ?></td>
                      <td>Rp. <?= number_format($data['nominal_piutang'], 0, ',', '.'); ?></td>
                      <td><?= date('d-m-Y', strtotime($data['tanggal_jatuh_tempo'])); ?></td>
                      <td>
                        <?php if($data['status'] == 'lunas'): ?>
                          <span class="badge badge-success">Lunas</span>
                        <?php else: ?>
                          <span class="badge badge-warning">Belum Lunas</span>
                        <?php endif; ?>
                      </td>
                      <td><?= $data['keterangan']; ?></td>
                      <td>
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalEditPiutang<?= $data['id']; ?>">
                          <i class="fa fa-edit"></i>
                        </button>
                        <a href="?delete=<?= $data['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin hapus data?')">
                          <i class="fa fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                    
                    <!-- Modal Edit untuk Setiap Data -->
                    <div class="modal fade" id="modalEditPiutang<?= $data['id']; ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Edit Saldo Awal Piutang</h5>
                            <button type="button" class="close" data-dismiss="modal">
                              <span>&times;</span>
                            </button>
                          </div>
                          <form action="" method="post">
                            <div class="modal-body">
                              <input type="hidden" name="id" value="<?= $data['id']; ?>">
                              <div class="form-group">
                                <label>No. Referensi</label>
                                <input type="text" name="no_ref" class="form-control" value="<?= $data['no_ref']; ?>" required>
                              </div>
                              <div class="form-group">
                      <label>Pilih Customer</label>
                      <select name="penjualan_pelanggan" class="form-control select2" required>
                        <option value="">-- Pilih Customer --</option>
                        <?php
                          // Changed from pelanggan to customer table as per the file you shared
                          $customers = query("SELECT * FROM customer WHERE customer_cabang = $sessionCabang ORDER BY customer_nama ASC");
                          foreach($customers as $row):
                            if ($row['customer_id'] > 1) { // Skip the first customer if it's a system record
                        ?>
                        <option value="<?= $row['customer_id']; ?>"><?= $row['customer_nama']; ?> 
                          <?php if(isset($row['customer_hp']) && !empty($row['customer_hp'])): ?> 
                            - <?= $row['customer_hp']; ?>
                          <?php endif; ?>
                          <?php if(isset($row['customer_membership']) && $row['customer_membership'] === "1"): ?> 
                            (Member)
                          <?php endif; ?>
                        </option>
                        <?php 
                            }
                          endforeach; 
                        ?>
                      </select>
                    </div>
                              <div class="form-group">
                                <label>Tanggal Transaksi</label>
                                <input type="date" name="tanggal_transaksi" class="form-control" value="<?= $data['tanggal_transaksi']; ?>" required>
                              </div>
                              <div class="form-group">
                                <label>Nominal Piutang</label>
                                <input type="number" name="nominal_piutang" class="form-control" value="<?= $data['nominal_piutang']; ?>" required>
                              </div>
                              <div class="form-group">
                                <label>Tanggal Jatuh Tempo</label>
                                <input type="date" name="tanggal_jatuh_tempo" class="form-control" value="<?= $data['tanggal_jatuh_tempo']; ?>" required>
                              </div>
                              <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                  <option value="belum_lunas" <?= $data['status'] == 'belum_lunas' ? 'selected' : ''; ?>>Belum Lunas</option>
                                  <option value="lunas" <?= $data['status'] == 'lunas' ? 'selected' : ''; ?>>Lunas</option>
                                </select>
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

<?php include '_footer.php'; ?>

<script>
  $(document).ready(function() {
   if ($.fn.DataTable.isDataTable('#example1')) {
  $('#example1').DataTable().destroy();
}
$("#example1").DataTable({
  "responsive": true, 
  "lengthChange": false, 
  "autoWidth": false,
  "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>