<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Aktivitas User</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Aktivitas User</li>
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
            <h3 class="card-title">Data User</h3>
            <?php if ($levelLogin === "super admin") : ?>
            <div class="card-tools">
              <a href="user-add" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah User
              </a>
            </div>
            <?php endif; ?>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="user-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No.</th>
                  <th>Nama User</th>
                  <th>No. HP</th>
                  <th>Alamat</th>
                  <th>Email</th>
                  <th>Tanggal Dibuat</th>
                  <th>Level</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                  $i = 1; 
                  // Query semua data user
                  $users = query("SELECT * FROM user ORDER BY user_id ASC");
                  
                  // Cek apakah ada data
                  if (empty($users)) {
                    echo "<tr><td colspan='8' class='text-center'>Tidak ada data user.</td></tr>";
                  } else {
                    foreach($users as $row) :
                ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $row['user_nama']; ?></td>
                  <td><?= $row['user_no_hp']; ?></td>
                  <td><?= $row['user_alamat']; ?></td>
                  <td><?= $row['user_email']; ?></td>
                  <td><?= $row['user_create']; ?></td>
                  <td>
                    <?php if ($row['user_level'] === "super admin") : ?>
                      <span class="badge badge-success">Super Admin</span>
                    <?php elseif ($row['user_level'] === "admin") : ?>
                      <span class="badge badge-primary">Admin</span>
                    <?php else : ?>
                      <span class="badge badge-secondary">User</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <?php if ($levelLogin === "super admin") : ?>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php 
                    $i++; 
                    endforeach;
                  } 
                ?>
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
<!-- /.content-wrapper -->

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- DataTables Buttons extension -->
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script>
  $(function () {
    // Periksa apakah tabel sudah diinisialisasi sebagai DataTable
    if (!$.fn.DataTable.isDataTable('#user-table')) {
      $("#user-table").DataTable({
        "responsive": true, 
        "lengthChange": true, 
        "autoWidth": false,
        "pageLength": 25,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
        "language": {
          "search": "Cari:",
          "lengthMenu": "Tampilkan _MENU_ data per halaman",
          "zeroRecords": "Tidak ditemukan data yang sesuai",
          "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
          "infoFiltered": "(disaring dari _MAX_ data keseluruhan)",
          "paginate": {
              "first": "Pertama",
              "last": "Terakhir",
              "next": "Selanjutnya",
              "previous": "Sebelumnya"
          }
        }
      }).buttons().container().appendTo('#user-table_wrapper .col-md-6:eq(0)');
    }
  });
</script>
</body>
</html>