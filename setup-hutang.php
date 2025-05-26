<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 

  // Debug Database Connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  if ($levelLogin === "kasir") {
    echo "<script>document.location.href = 'bo';</script>";
  }

  // Initialize variables
  $id = "";
  $no_ref = "";
  $supplier = "";
  $tanggal_transaksi = date('Y-m-d');
  $nominal = 0;
  $tanggal_jatuh_tempo = date('Y-m-d', strtotime('+30 days'));
  $keterangan = "";
  $error = "";
  $success = "";
  $isEdit = false;

  // Process form submission
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
      // Extract common form fields
      $no_ref = mysqli_real_escape_string($conn, $_POST['no_ref']);
      $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
      $tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
      $nominal = str_replace(['.', ','], ['', '.'], $_POST['nominal']); // Convert formatted number to decimal
      $tanggal_jatuh_tempo = mysqli_real_escape_string($conn, $_POST['tanggal_jatuh_tempo']);
      $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

      // CREATE
      if ($_POST['action'] == 'add') {
        // Validate required fields
        if (empty($no_ref) || empty($supplier) || empty($tanggal_transaksi) || empty($nominal) || empty($tanggal_jatuh_tempo)) {
          $error = "Semua field harus diisi kecuali keterangan!";
        } else {
          $query = "INSERT INTO hutang_awal (no_ref, supplier, tanggal_transaksi, nominal, tanggal_jatuh_tempo, keterangan, cabang) 
                    VALUES ('$no_ref', '$supplier', '$tanggal_transaksi', $nominal, '$tanggal_jatuh_tempo', '$keterangan', '$sessionCabang')";
          
          if (mysqli_query($conn, $query)) {
            $success = "Data hutang berhasil ditambahkan";
            // Reset form
            $no_ref = "";
            $supplier = "";
            $tanggal_transaksi = date('Y-m-d');
            $nominal = 0;
            $tanggal_jatuh_tempo = date('Y-m-d', strtotime('+30 days'));
            $keterangan = "";
          } else {
            $error = "Error: " . mysqli_error($conn);
          }
        }
      }
      
      // UPDATE
      elseif ($_POST['action'] == 'edit') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        
        // Validate required fields
        if (empty($no_ref) || empty($supplier) || empty($tanggal_transaksi) || empty($nominal) || empty($tanggal_jatuh_tempo)) {
          $error = "Semua field harus diisi kecuali keterangan!";
        } else {
          $query = "UPDATE hutang_awal 
                    SET no_ref = '$no_ref', 
                        supplier = '$supplier', 
                        tanggal_transaksi = '$tanggal_transaksi', 
                        nominal = $nominal, 
                        tanggal_jatuh_tempo = '$tanggal_jatuh_tempo', 
                        keterangan = '$keterangan', 
                        updated_at = NOW() 
                    WHERE id = $id AND cabang = '$sessionCabang'";
          
          if (mysqli_query($conn, $query)) {
            $success = "Data hutang berhasil diperbarui";
            // Reset form
            $id = "";
            $no_ref = "";
            $supplier = "";
            $tanggal_transaksi = date('Y-m-d');
            $nominal = 0;
            $tanggal_jatuh_tempo = date('Y-m-d', strtotime('+30 days'));
            $keterangan = "";
            $isEdit = false;
          } else {
            $error = "Error: " . mysqli_error($conn);
          }
        }
      }
      
      // DELETE
      elseif ($_POST['action'] == 'delete') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        
        $query = "DELETE FROM hutang_awal WHERE id = $id AND cabang = '$sessionCabang'";
        
        if (mysqli_query($conn, $query)) {
          $success = "Data hutang berhasil dihapus";
        } else {
          $error = "Error: " . mysqli_error($conn);
        }
      }
    }
  }

  // EDIT - fetch data to populate the form
  if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $query = "SELECT * FROM hutang_awal WHERE id = $id AND cabang = '$sessionCabang'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
      $data = mysqli_fetch_assoc($result);
      $id = $data['id'];
      $no_ref = $data['no_ref'];
      $supplier = $data['supplier'];
      $tanggal_transaksi = $data['tanggal_transaksi'];
      $nominal = $data['nominal'];
      $tanggal_jatuh_tempo = $data['tanggal_jatuh_tempo'];
      $keterangan = $data['keterangan'];
      $isEdit = true;
    } else {
      $error = "Data tidak ditemukan!";
    }
  }

  // READ - Get all hutang records for this branch
  $queryHutangAwal = mysqli_query($conn, "SELECT * FROM hutang_awal WHERE cabang = '$sessionCabang' ORDER BY tanggal_jatuh_tempo ASC");
  if (!$queryHutangAwal) {
    $error = "Query error: " . mysqli_error($conn);
  }
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Setup Hutang Awal</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item"><a href="hutang-awal">Hutang Awal</a></li>
            <li class="breadcrumb-item active">Setup Hutang</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <!-- Form Card -->
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title"><?= $isEdit ? 'Edit Data Hutang' : 'Tambah Data Hutang Baru'; ?></h3>
        </div>
        
        <!-- Display errors or success messages -->
        <?php if (!empty($error)) { ?>
          <div class="alert alert-danger alert-dismissible mt-3 mx-3">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Error!</h5>
            <?= $error; ?>
          </div>
        <?php } ?>
        
        <?php if (!empty($success)) { ?>
          <div class="alert alert-success alert-dismissible mt-3 mx-3">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-check"></i> Sukses!</h5>
            <?= $success; ?>
          </div>
        <?php } ?>
        
        <form method="post" action="">
          <div class="card-body">
            <input type="hidden" name="id" value="<?= $id; ?>">
            <input type="hidden" name="action" value="<?= $isEdit ? 'edit' : 'add'; ?>">
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="no_ref">Nomor Referensi <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="no_ref" name="no_ref" value="<?= $no_ref; ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="supplier">Supplier <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="supplier" name="supplier" value="<?= $supplier; ?>" required>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="tanggal_transaksi">Tanggal Transaksi <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" value="<?= $tanggal_transaksi; ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="tanggal_jatuh_tempo">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" value="<?= $tanggal_jatuh_tempo; ?>" required>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="nominal">Nominal (Rp) <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="nominal" name="nominal" value="<?= number_format($nominal, 0, ',', '.'); ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="keterangan">Keterangan</label>
                  <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= $keterangan; ?></textarea>
                </div>
              </div>
            </div>
          </div>
          
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> <?= $isEdit ? 'Update' : 'Simpan'; ?>
            </button>
            <?php if ($isEdit) { ?>
              <a href="setup-hutang" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
              </a>
            <?php } ?>
          </div>
        </form>
      </div>

      <!-- Data Table Card -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Data Hutang Awal</h3>
        </div>
        <div class="card-body">
          <?php if ($queryHutangAwal && mysqli_num_rows($queryHutangAwal) > 0) { ?>
          <div class="table-responsive">
            <table id="hutangTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>No. Ref</th>
                  <th>Supplier</th>
                  <th>Tanggal Transaksi</th>
                  <th>Nominal</th>
                  <th>Jatuh Tempo</th>
                  <th>Status</th>
                  <th>Keterangan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $i = 1;
                $today = date('Y-m-d');
                while($data = mysqli_fetch_array($queryHutangAwal)) { 
                  // Determine status based on due date
                  $status = "Belum Jatuh Tempo";
                  $statusClass = "badge-success";
                  if ($data['tanggal_jatuh_tempo'] < $today) {
                    $status = "Jatuh Tempo";
                    $statusClass = "badge-danger";
                  } elseif ($data['tanggal_jatuh_tempo'] == $today) {
                    $status = "Jatuh Tempo Hari Ini";
                    $statusClass = "badge-warning";
                  }
                ?>
                <tr>
                  <td><?= $i++; ?></td>
                  <td><?= $data['no_ref']; ?></td>
                  <td><?= $data['supplier']; ?></td>
                  <td><?= date('d-m-Y', strtotime($data['tanggal_transaksi'])); ?></td>
                  <td>Rp. <?= number_format($data['nominal'], 0, ',', '.'); ?></td>
                  <td><?= date('d-m-Y', strtotime($data['tanggal_jatuh_tempo'])); ?></td>
                  <td><span class="badge <?= $statusClass; ?>"><?= $status; ?></span></td>
                  <td><?= $data['keterangan']; ?></td>
                  <td>
                    <div class="btn-group">
                      <a href="setup-hutang?edit=<?= $data['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                      </a>
                      <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?= $data['id']; ?>">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                    
                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="deleteModal<?= $data['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            Apakah Anda yakin ingin menghapus data hutang dengan nomor referensi <strong><?= $data['no_ref']; ?></strong> untuk supplier <strong><?= $data['supplier']; ?></strong>?
                          </div>
                          <div class="modal-footer">
                            <form method="post" action="">
                              <input type="hidden" name="id" value="<?= $data['id']; ?>">
                              <input type="hidden" name="action" value="delete">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                              <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <?php } else { ?>
            <div class="alert alert-info">
              Belum ada data hutang awal. Silakan tambahkan data baru menggunakan form di atas.
            </div>
          <?php } ?>
        </div>
      </div>

    </div>
  </section>
</div>

<?php include '_footer.php'; ?>

<script>
  $(function() {
    // Inisialisasi DataTable
    $("#hutangTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      "pageLength": 10,
      "language": {
        "lengthMenu": "Tampilkan _MENU_ data per halaman",
        "zeroRecords": "Data tidak ditemukan",
        "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
        "infoEmpty": "Tidak ada data yang tersedia",
        "infoFiltered": "(difilter dari _MAX_ total data)",
        "search": "Cari:",
        "paginate": {
          "first": "Pertama",
          "last": "Terakhir",
          "next": "Selanjutnya",
          "previous": "Sebelumnya"
        }
      }
    });

    // Format currency input
    $('#nominal').on('keyup', function() {
      let value = $(this).val().replace(/[^\d]/g, '');
      $(this).val(formatCurrency(value));
    });

    function formatCurrency(angka) {
      var number_string = angka.toString(),
          split = number_string.split(','),
          sisa = split[0].length % 3,
          rupiah = split[0].substr(0, sisa),
          ribuan = split[0].substr(sisa).match(/\d{3}/gi);
          
      if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
      }
      
      return rupiah;
    }
  });
</script>