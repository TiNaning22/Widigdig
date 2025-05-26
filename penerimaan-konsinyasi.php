<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
  
  // Proses form update jika ada
  if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $tanggal_penarikan = $_POST['tanggal_penarikan'];
    
    // Update data
    $query = "UPDATE penerimaan_konsinyasi SET 
              tanggal_penarikan = '$tanggal_penarikan'
              WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
      echo "<script>alert('Data berhasil diupdate!');</script>";
    } else {
      echo "<script>alert('Gagal mengupdate data: " . mysqli_error($conn) . "');</script>";
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
          <h1>Penerimaan Konsinyasi</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Penerimaan Konsinyasi</li>
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
            <h3 class="card-title">Data Penerimaan Konsinyasi</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="konsinyasi-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No.</th>
                  <th>Nama Sales</th>
                  <th>Nama Barang</th>
                  <th>Tanggal Penitipan</th>
                  <th>Tanggal Penarikan</th>
                  <th>Jumlah</th>
                  <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                  $i = 1; 
                  // Query semua data penerimaan konsinyasi
                  $konsinyasi = query("SELECT pk.*, b.barang_nama 
                                      FROM penerimaan_konsinyasi pk
                                      LEFT JOIN barang_internal b ON pk.barang_id = b.barang_id
                                      ORDER BY pk.tanggal_penitipan DESC");
                  
                  // Cek apakah ada data
                  if (empty($konsinyasi)) {
                    echo "<tr><td colspan='7' class='text-center'>Tidak ada data penerimaan konsinyasi.</td></tr>";
                  } else {
                    foreach($konsinyasi as $row) :
                ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $row['sales_nama']; ?></td>
                  <td><?= $row['barang_nama']; ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_penitipan'])); ?></td>
                  <td><?= $row['tanggal_penarikan'] ? date('d-m-Y', strtotime($row['tanggal_penarikan'])) : '-'; ?></td>
                  <td><?= $row['jumlah']; ?></td>
                  <td class="text-center">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal<?= $row['id']; ?>">
                      <i class="fas fa-edit"></i> Edit
                    </button>
                  </td>
                </tr>
                
                <!-- Modal Edit untuk setiap row -->
                <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $row['id']; ?>" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel<?= $row['id']; ?>">Edit Tanggal Penarikan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <form action="" method="post">
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?= $row['id']; ?>">
                          
                          <div class="form-group">
                            <label>Nama Sales</label>
                            <input type="text" class="form-control" value="<?= $row['sales_nama']; ?>" readonly>
                          </div>
                          
                          <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" class="form-control" value="<?= $row['barang_nama']; ?>" readonly>
                          </div>
                          
                          <div class="form-group">
                            <label>Tanggal Penitipan</label>
                            <input type="text" class="form-control" value="<?= date('d-m-Y', strtotime($row['tanggal_penitipan'])); ?>" readonly>
                          </div>
                          
                          <div class="form-group">
                            <label>Jumlah</label>
                            <input type="text" class="form-control" value="<?= $row['jumlah']; ?>" readonly>
                          </div>
                          
                          <div class="form-group">
                            <label for="tanggal_penarikan">Tanggal Penarikan</label>
                            <input type="date" class="form-control" id="tanggal_penarikan" name="tanggal_penarikan" 
                                  value="<?= $row['tanggal_penarikan'] ?? ''; ?>">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                          <button type="submit" name="update" class="btn btn-primary">Simpan</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <!-- End Modal Edit -->
                
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
    // Inisialisasi DataTable
    if (!$.fn.DataTable.isDataTable('#konsinyasi-table')) {
      $("#konsinyasi-table").DataTable({
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
      }).buttons().container().appendTo('#konsinyasi-table_wrapper .col-md-6:eq(0)');
    }
  });
</script>
</body>
</html>