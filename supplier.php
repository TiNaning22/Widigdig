<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  // Fix logical error - change AND (&&) to OR (||)
  if ($levelLogin === "kasir" || $levelLogin === "kurir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }
    
  // Debug connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Data Supplier</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Supplier</li>
          </ol>
        </div>
        <div class="tambah-data">
          <a href="supplier-add" class="btn btn-primary">Tambah Data</a>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <?php
    // Check if supplier table exists
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'supplier'");
    if (mysqli_num_rows($check_table) == 0) {
      echo "<div class='alert alert-danger'>Table 'supplier' does not exist. Please create the table first.</div>";
    } else {
      // Debug query
      $queryCheck = mysqli_query($conn, "SELECT COUNT(*) as total FROM supplier WHERE supplier_cabang = $sessionCabang");
      $count = mysqli_fetch_assoc($queryCheck);
      // echo "Found " . $count['total'] . " suppliers for cabang $sessionCabang";
    }  
    
    // Use proper error handling for query
    $query = "SELECT * FROM supplier WHERE supplier_cabang = $sessionCabang ORDER BY supplier_id DESC";
    $result = mysqli_query($conn, $query);
    if (!$result) {
      echo "<div class='alert alert-danger'>Query error: " . mysqli_error($conn) . "</div>";
    }
    $data = [];
    if ($result) {
      while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
      }
    }
  ?>
  
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Supplier Keseluruhan</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <?php if (!empty($data)) { ?>
            <div class="table-auto">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th style="width: 5%;">No.</th>
                  <th>Kode</th>
                  <th>Nama</th>
                  <th>No. WhatsApp</th>
                  <th>Email</th>
                  <th>Kota</th>
                  <th>Nama Perusahaan</th>
                  <th style="text-align: center; width: 10%;">Status</th>
                  <th style="text-align: center; width: 14%;">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1; ?>
                <?php foreach ($data as $row) : ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $row['supplier_kode']; ?></td>
                  <td><?= $row['supplier_nama']; ?></td>
                  <td><?= $row['supplier_wa']; ?></td>
                  <td><?= $row['supplier_email']; ?></td>
                  <td><?= $row['supplier_kota']; ?></td>
                  <td><?= $row['supplier_company']; ?></td>
                  <td style="text-align: center;">
                    <?php 
                      if ($row['supplier_status'] === "1") {
                        echo "<b>Aktif</b>";
                      } else {
                        echo "<b style='color: red;'>Tidak Aktif</b>";
                      }
                    ?>
                  </td>
                  <td class="orderan-online-button">
                    <?php $id = $row["supplier_id"]; ?>
                    <a href="supplier-zoom?id=<?= $id; ?>" title="Zoom Data">
                      <button class="btn btn-success" type="submit">
                        <i class="fa fa-search"></i>
                      </button>
                    </a>
                    <a href="supplier-edit?id=<?= $id; ?>" title="Edit Data">
                      <button class="btn btn-primary" type="submit">
                        <i class="fa fa-edit"></i>
                      </button>
                    </a>

                    <?php
                      // Check supplier relationships with proper error handling  
                      $pembelian = mysqli_query($conn, "SELECT * FROM invoice_pembelian WHERE invoice_supplier = ".$id);
                      if (!$pembelian) {
                        echo "<div class='alert alert-danger'>Error checking pembelian: " . mysqli_error($conn) . "</div>";
                        $jmlPembelian = 0;
                      } else {
                        $jmlPembelian = mysqli_num_rows($pembelian);
                      }
                    ?>

                    <?php if ($jmlPembelian < 1) { ?>
                    <a href="supplier-delete?id=<?= $id; ?>" onclick="return confirm('Yakin dihapus ?')" title="Delete Data">
                      <button class="btn btn-danger" type="submit" name="hapus">
                        <i class="fa fa-trash-o"></i>
                      </button>
                    </a>
                    <?php } ?>

                    <?php if ($jmlPembelian > 0) { ?>
                    <a href="#!" title="Delete Data" disabled>
                      <button class="btn btn-default" type="" name="hapus">
                        <i class="fa fa-trash-o"></i>
                      </button>
                    </a>
                    <?php } ?>
                  </td>
                </tr>
                <?php $i++; ?>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php } else { ?>
              <div class="alert alert-info">
                Belum ada data supplier. Silakan tambah data baru.
              </div>
            <?php } ?>
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
</div>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- AdminLTE App -->
<!-- <script src="dist/js/adminlte.min.js"></script> -->
<!-- AdminLTE for demo purposes -->
<!-- <script src="dist/js/demo.js"></script> -->
<!-- page script -->
<script>
$(function () {
  // Check if the table is already initialized
  if ($.fn.DataTable.isDataTable('#example1')) {
    // If already initialized, destroy it first
    $('#example1').DataTable().destroy();
  }
  
  // Then initialize it again
  $("#example1").DataTable({
    "responsive": true,
    "autoWidth": false,
  });
});
</script>
</body>
</html>