<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "kurir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }  
?>

<?php  
  // Mengambil data sales dari database
  $data = query("SELECT * FROM sales WHERE sales_cabang = $sessionCabang ORDER BY sales_id DESC");
  
  // Proses tambah sales
  if(isset($_POST['tambah_sales'])) {
    $sales_nama = htmlspecialchars($_POST['sales_nama']);
    $sales_hp = htmlspecialchars($_POST['sales_hp']);
    $sales_email = htmlspecialchars($_POST['sales_email']);
    $sales_alamat = htmlspecialchars($_POST['sales_alamat']);
    $sales_status = htmlspecialchars($_POST['sales_status']);
    $sales_cabang = $sessionCabang;
    $created_at = date("Y-m-d H:i:s");
    
    // Insert data sales
    $query = "INSERT INTO sales (sales_nama, sales_hp, sales_email, sales_alamat, sales_status, sales_cabang, created_at) VALUES ('$sales_nama', '$sales_hp', '$sales_email', '$sales_alamat', '$sales_status', '$sales_cabang', '$created_at')";
    
    $result = mysqli_query($conn, $query);
    
    if($result) {
      echo "
        <script>
          alert('Sales berhasil ditambahkan!');
          document.location.href = 'sales';
        </script>
      ";
    } else {
      echo "
        <script>
          alert('Sales gagal ditambahkan!');
          document.location.href = 'sales';
        </script>
      ";
    }
  }

  // Proses edit sales
  if(isset($_POST['edit_sales'])) {
    $sales_id = htmlspecialchars($_POST['sales_id']);
    $sales_nama = htmlspecialchars($_POST['sales_nama']);
    $sales_hp = htmlspecialchars($_POST['sales_hp']);
    $sales_email = htmlspecialchars($_POST['sales_email']);
    $sales_alamat = htmlspecialchars($_POST['sales_alamat']);
    $sales_status = htmlspecialchars($_POST['sales_status']);
    $updated_at = date("Y-m-d H:i:s");
    
    // Update data sales
    $query = "UPDATE sales SET 
              sales_nama = '$sales_nama', 
              sales_hp = '$sales_hp', 
              sales_email = '$sales_email', 
              sales_alamat = '$sales_alamat', 
              sales_status = '$sales_status', 
              updated_at = '$updated_at' 
              WHERE sales_id = $sales_id";
    
    $result = mysqli_query($conn, $query);
    
    if($result) {
      echo "
        <script>
          alert('Sales berhasil diupdate!');
          document.location.href = 'sales';
        </script>
      ";
    } else {
      echo "
        <script>
          alert('Sales gagal diupdate!');
          document.location.href = 'sales';
        </script>
      ";
    }
  }

  // Proses hapus sales
  if(isset($_POST['hapus_sales'])) {
    $sales_id = htmlspecialchars($_POST['sales_id']);
    
    // Delete data sales
    $query = "DELETE FROM sales WHERE sales_id = $sales_id";
    
    $result = mysqli_query($conn, $query);
    
    if($result) {
      echo "
        <script>
          alert('Sales berhasil dihapus!');
          document.location.href = 'sales';
        </script>
      ";
    } else {
      echo "
        <script>
          alert('Sales gagal dihapus!');
          document.location.href = 'sales';
        </script>
      ";
    }
  }
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Kelola Sales</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Kelola Sales</li>
            </ol>
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
              <h3 class="card-title">Data Sales</h3>
              <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-tambah-sales">
                <i class="fas fa-plus"></i> Tambah Sales
              </button>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>No.</th>
                    <th>Nama Sales</th>
                    <th>No. HP</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th style="text-align: center; width: 18%;">Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php $i = 1; ?>
                  <?php foreach ( $data as $row ) : ?>
                  <tr>
                    <td><?= $i; ?></td>
                    <td><?= $row['sales_nama']; ?></td>
                    <td><?= $row['sales_hp']; ?></td>
                    <td><?= $row['sales_email']; ?></td>
                    <td>
                      <?php 
                        if ( $row['sales_status'] === "1" ) {
                          echo "<span class='badge badge-success'>Aktif</span>";
                        } else {
                          echo "<span class='badge badge-danger'>Non-Aktif</span>";
                        }
                      ?>    
                    </td>
                    <td class="text-center">
                      <?php $id = $row["sales_id"]; ?>
                      <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-edit-sales-<?= $id; ?>" title="Edit">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-detail-sales-<?= $id; ?>" title="Detail">
                        <i class="fa fa-eye"></i>
                      </button>
                      <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-hapus-sales-<?= $id; ?>" title="Hapus">
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <?php $i++; ?>
                  <?php endforeach; ?>
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

  <!-- Modal Tambah Sales -->
  <div class="modal fade" id="modal-tambah-sales">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Tambah Sales Baru</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="sales_nama">Nama Sales</label>
              <input type="text" class="form-control" id="sales_nama" name="sales_nama" required>
            </div>
            <div class="form-group">
              <label for="sales_hp">No. HP</label>
              <input type="text" class="form-control" id="sales_hp" name="sales_hp" required>
            </div>
            <div class="form-group">
              <label for="sales_email">Email</label>
              <input type="email" class="form-control" id="sales_email" name="sales_email">
            </div>
            <div class="form-group">
              <label for="sales_alamat">Alamat</label>
              <textarea class="form-control" id="sales_alamat" name="sales_alamat" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label for="sales_status">Status</label>
              <select class="form-control" id="sales_status" name="sales_status" required>
                <option value="1">Aktif</option>
                <option value="0">Non-Aktif</option>
              </select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <button type="submit" name="tambah_sales" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  <!-- Modal Edit dan Detail Sales -->
  <?php foreach ( $data as $row ) : ?>
    <?php $id = $row["sales_id"]; ?>
    
    <!-- Modal Edit Sales -->
    <div class="modal fade" id="modal-edit-sales-<?= $id; ?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit Sales</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="post">
            <div class="modal-body">
              <input type="hidden" name="sales_id" value="<?= $id; ?>">
              <div class="form-group">
                <label for="sales_nama">Nama Sales</label>
                <input type="text" class="form-control" id="sales_nama" name="sales_nama" value="<?= $row['sales_nama']; ?>" required>
              </div>
              <div class="form-group">
                <label for="sales_hp">No. HP</label>
                <input type="text" class="form-control" id="sales_hp" name="sales_hp" value="<?= $row['sales_hp']; ?>" required>
              </div>
              <div class="form-group">
                <label for="sales_email">Email</label>
                <input type="email" class="form-control" id="sales_email" name="sales_email" value="<?= $row['sales_email']; ?>">
              </div>
              <div class="form-group">
                <label for="sales_alamat">Alamat</label>
                <textarea class="form-control" id="sales_alamat" name="sales_alamat" rows="3"><?= $row['sales_alamat']; ?></textarea>
              </div>
              <div class="form-group">
                <label for="sales_status">Status</label>
                <select class="form-control" id="sales_status" name="sales_status" required>
                  <option value="1" <?= ($row['sales_status'] === "1") ? "selected" : ""; ?>>Aktif</option>
                  <option value="0" <?= ($row['sales_status'] === "0") ? "selected" : ""; ?>>Non-Aktif</option>
                </select>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
              <button type="submit" name="edit_sales" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>

    <!-- Modal Detail Sales -->
    <div class="modal fade" id="modal-detail-sales-<?= $id; ?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Detail Sales</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <tr>
                <th>Nama Sales</th>
                <td><?= $row['sales_nama']; ?></td>
              </tr>
              <tr>
                <th>No. HP</th>
                <td><?= $row['sales_hp']; ?></td>
              </tr>
              <tr>
                <th>Email</th>
                <td><?= $row['sales_email']; ?></td>
              </tr>
              <tr>
                <th>Alamat</th>
                <td><?= $row['sales_alamat']; ?></td>
              </tr>
              <tr>
                <th>Status</th>
                <td>
                  <?php 
                    if ( $row['sales_status'] === "1" ) {
                      echo "<span class='badge badge-success'>Aktif</span>";
                    } else {
                      echo "<span class='badge badge-danger'>Non-Aktif</span>";
                    }
                  ?>
                </td>
              </tr>
              <tr>
                <th>Tanggal Dibuat</th>
                <td><?= date('d F Y H:i', strtotime($row['created_at'])); ?></td>
              </tr>
              <?php if(!empty($row['updated_at'])): ?>
              <tr>
                <th>Terakhir Diupdate</th>
                <td><?= date('d F Y H:i', strtotime($row['updated_at'])); ?></td>
              </tr>
              <?php endif; ?>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>

    <!-- Modal Hapus Sales -->
    <div class="modal fade" id="modal-hapus-sales-<?= $id; ?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Konfirmasi Hapus</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="post">
            <div class="modal-body">
              <input type="hidden" name="sales_id" value="<?= $id; ?>">
              <p>Apakah Anda yakin ingin menghapus sales <strong><?= $row['sales_nama']; ?></strong>?</p>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
              <button type="submit" name="hapus_sales" class="btn btn-danger">Ya, Hapus</button>
            </div>
          </form>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
  <?php endforeach; ?>

<?php include '_footer.php'; ?>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- page script -->
<script>
  $(function () {
    $("#example1").DataTable();
  });
</script>
</body>
</html>