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
          <h1>Daftar Toko</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Daftar Toko</li>
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
            <h3 class="card-title">Data Toko</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No.</th>
                  <th>Nama Toko</th>
                  <th>Kota</th>
                  <th>Alamat</th>
                  <th>Telepon</th>
                  <th>WhatsApp</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th>Cabang</th>
                  <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                  $i = 1; 
                  // Query semua data toko
                  $tokos = query("SELECT * FROM toko ORDER BY toko_id ASC");
                  
                  // Cek apakah ada data
                  if (empty($tokos)) {
                    echo "<tr><td colspan='10' class='text-center'>Tidak ada data toko.</td></tr>";
                  } else {
                    foreach($tokos as $row) :
                ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $row['toko_nama']; ?></td>
                  <td><?= $row['toko_kota']; ?></td>
                  <td><?= $row['toko_alamat']; ?></td>
                  <td><?= $row['toko_tlpn']; ?></td>
                  <td><?= $row['toko_wa']; ?></td>
                  <td><?= $row['toko_email']; ?></td>
                  <td>
                    <?php if ($row['toko_status'] == 1) : ?>
                      <span class="badge badge-success">Aktif</span>
                    <?php else : ?>
                      <span class="badge badge-danger">Tidak Aktif</span>
                    <?php endif; ?>
                  </td>
                  <td><?= $row['toko_cabang']; ?></td>
                  <td class="text-center">
                    <a href="toko-edit?id=<?= $row['toko_id']; ?>" class="btn btn-info btn-sm">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    <?php if ($levelLogin === "super admin") : ?>
                    <a href="toko-delete?id=<?= $row['toko_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data?')">
                      <i class="fas fa-trash"></i> Hapus
                    </a>
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
  if (!$.fn.DataTable.isDataTable('#example1')) {
    $("#example1").DataTable({
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
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  }
});
</script>
</body>
</html>