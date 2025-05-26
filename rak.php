<?php
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
  error_reporting(0);

  // Redirect jika level login adalah "kasir" atau "kurir"
  if ($levelLogin === "kasir" || $levelLogin === "kurir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }

  // Koneksi ke database
  include 'koneksi.php';

  // Proses edit jika form edit disubmit
  if (isset($_POST['edit_rak'])) {
    $rakId = intval($_POST['rak_id']);
    $namaRak = $_POST['nama_rak'];
    
    // Update the rak
    $updateQuery = "UPDATE rak SET nama_rak = ? WHERE rak_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $namaRak, $rakId);
    
    if ($updateStmt->execute()) {
      echo "<script>alert('Rak berhasil diupdate!');</script>";
    } else {
      echo "<script>alert('Gagal mengupdate rak: " . $conn->error . "');</script>";
    }
    
    $updateStmt->close();
    
    // Redirect to prevent resubmission on refresh
    echo "<script>window.location.href = 'rak.php';</script>";
    exit;
  }

  // Proses delete jika ada parameter id_delete
  if (isset($_GET['id_delete']) && !empty($_GET['id_delete'])) {
    $rakId = intval($_GET['id_delete']);
    
    // Delete the rak
    $deleteQuery = "DELETE FROM rak WHERE rak_id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $rakId);
    
    if ($deleteStmt->execute()) {
      echo "<script>alert('Rak berhasil dihapus!');</script>";
    } else {
      echo "<script>alert('Gagal menghapus rak: " . $conn->error . "');</script>";
    }
    
    $deleteStmt->close();
    
    // Redirect to prevent resubmission on refresh
    echo "<script>window.location.href = 'rak.php';</script>";
    exit;
  }

  // Query untuk mengambil data rak
  // Add this after line 69, before the HTML content starts
$query = "SELECT r.*, t.toko_nama 
          FROM rak r
          LEFT JOIN toko t ON r.toko_id = t.toko_id";
$result = $conn->query($query);
$rakData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rakData[] = $row;
    }
}

// Only close the connection after all database operations
// $conn->close(); - Move this to the end of the file
  
?>



<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Data Rak</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Rak</li>
          </ol>
        </div>
        <div class="tambah-data">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahRak">
            Tambah Rak
          </button>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Rak Keseluruhan</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="example1" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th style="width: 6%;">No.</th>
      <th>Nama Rak</th>
      <th>Toko</th>
      <th style="text-align: center; width: 14%">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    // Update your query to join with toko table
    $query = "SELECT r.*, t.toko_nama 
              FROM rak r
              LEFT JOIN toko t ON r.toko_id = t.toko_id";
    $result = $conn->query($query);
    $rakData = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rakData[] = $row;
        }
    }
    
    if (!empty($rakData)): 
    ?>
      <?php foreach ($rakData as $index => $rak): ?>
        <tr>
          <td><?php echo $index + 1; ?></td>
          <td><?php echo htmlspecialchars($rak['nama_rak']); ?></td>
          <td><?php echo htmlspecialchars($rak['toko_nama'] ?? 'Tidak ada toko'); ?></td>
          <td style="text-align: center;">
            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEditRak" onclick="editRak(<?php echo $rak['rak_id']; ?>, '<?php echo htmlspecialchars($rak['nama_rak']); ?>', <?php echo $rak['toko_id'] ?? 'null'; ?>)">
              Edit
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRak(<?php echo $rak['rak_id']; ?>)">
              Hapus
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="4" style="text-align: center;">Tidak ada data rak</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
            </div>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>

<!-- Modal Tambah Rak -->
<div class="modal fade" id="modalTambahRak" tabindex="-1" role="dialog" aria-labelledby="modalTambahRakLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahRakLabel">Tambah Rak</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formTambahRak" action="rak-add-process.php" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="namaRak">Nama Rak</label>
            <input type="text" class="form-control" id="namaRak" name="nama_rak" required placeholder="Masukkan nama rak">
          </div>
          
          <?php
          // Re-establish database connection to ensure it's active
          include 'aksi/koneksi.php';
          
          // Query to get stores
          $queryToko = "SELECT toko_id, toko_nama FROM toko WHERE toko_status = 1";
          $resultToko = $conn->query($queryToko);
          ?>
          
          <div class="form-group">
            <label for="tokoId">Toko</label>
            <select class="form-control" id="tokoId" name="toko_id" required>
              <option value="">Pilih Toko</option>
              <?php
              if ($resultToko && $resultToko->num_rows > 0) {
                  while ($rowToko = $resultToko->fetch_assoc()) {
                      echo '<option value="' . $rowToko['toko_id'] . '">' . htmlspecialchars($rowToko['toko_nama']) . '</option>';
                  }
              } else {
                  echo '<option value="" disabled>Tidak ada data toko</option>';
              }
              ?>
            </select>
          </div>
          
          <?php
          // For debugging - show how many stores were found
          if ($resultToko) {
              echo '<!-- Found ' . $resultToko->num_rows . ' stores -->';
          } else {
              echo '<!-- Query error: ' . $conn->error . ' -->';
          }
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Rak -->
<div class="modal fade" id="modalEditRak" tabindex="-1" role="dialog" aria-labelledby="modalEditRakLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditRakLabel">Edit Rak</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formEditRak" action="rak.php" method="POST">
        <div class="modal-body">
          <input type="hidden" id="editRakId" name="rak_id">
          <input type="hidden" name="edit_rak" value="1">
          <div class="form-group">
            <label for="editNamaRak">Nama Rak</label>
            <input type="text" class="form-control" id="editNamaRak" name="nama_rak" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>

<!-- JavaScript untuk mengisi form edit dan menghapus data -->
<script>
function editRak(id, nama, tokoId) {
    document.getElementById('editRakId').value = id;
    document.getElementById('editNamaRak').value = nama;
    // You might want to add code here to set the store in the edit form too
}

function deleteRak(id) {
    if (confirm('Apakah Anda yakin ingin menghapus rak ini?')) {
        window.location.href = 'rak.php?id_delete=' + id;
    }
}

$(function () {
    $("#example1").DataTable();
});
</script>