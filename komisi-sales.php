<?php
include '_header.php';
include '_nav.php';
include '_sidebar.php';

// Fungsi untuk memformat angka ke Rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Handle Update Komisi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_komisi'])) {
    $sales_id = $_POST['sales_id'];
    $komisi = str_replace(['Rp ', '.', ','], '', $_POST['komisi']); // Hapus format Rupiah sebelum update

    // Query untuk update komisi
    $query = "UPDATE sales SET komisi = ? WHERE sales_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $komisi, $sales_id);

    if ($stmt->execute()) {
        $success_message = "Komisi berhasil diperbarui!";
    } else {
        $error_message = "Gagal memperbarui komisi.";
    }

    $stmt->close();
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Data Sales</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Data Sales</li>
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
            <h3 class="card-title">Daftar Sales</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <!-- Notifikasi Sukses/Gagal -->
            <?php if (isset($success_message)) : ?>
              <div class="alert alert-success"><?= $success_message; ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)) : ?>
              <div class="alert alert-danger"><?= $error_message; ?></div>
            <?php endif; ?>

            <div class="table-auto">
              <table id="sales-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>ID Sales</th>
                    <th>Nama Sales</th>
                    <th>No. HP</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Status</th>
                    <th>Cabang</th>
                    <th>Komisi</th>
                    <th>Tanggal Dibuat</th>
                    <th>Terakhir Diupdate</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  // Query data dari table sales
                  $querySales = "SELECT 
                                sales_id,
                                sales_nama,
                                sales_hp,
                                sales_email,
                                sales_alamat,
                                sales_status,
                                sales_cabang,
                                komisi,
                                created_at,
                                updated_at
                            FROM sales
                            ORDER BY sales_nama ASC";

                  $dataSales = query($querySales);

                  // Cek apakah ada data
                  if (empty($dataSales)) {
                    echo "<tr><td colspan='12' class='text-center'>Tidak ada data sales.</td></tr>";
                  } else {
                    foreach ($dataSales as $row) :
                      // Tentukan status sales
                      $status = ($row['sales_status'] == 1) ?
                        '<span class="badge badge-success">Aktif</span>' :
                        '<span class="badge badge-danger">Non-Aktif</span>';
                  ?>
                      <tr>
                        <td><?= $i; ?></td>
                        <td><?= $row['sales_id']; ?></td>
                        <td><?= $row['sales_nama']; ?></td>
                        <td><?= $row['sales_hp']; ?></td>
                        <td><?= $row['sales_email']; ?></td>
                        <td><?= $row['sales_alamat']; ?></td>
                        <td><?= $status; ?></td>
                        <td><?= $row['sales_cabang']; ?></td>
                        <td class="komisi"><?= formatRupiah($row['komisi']); ?></td>
                        <td><?= $row['created_at']; ?></td>
                        <td><?= $row['updated_at']; ?></td>
                        <td>
                          <!-- Tombol untuk membuka modal edit komisi -->
                          <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal<?= $row['sales_id']; ?>">
                            Edit Komisi
                          </button>
                        </td>
                      </tr>

                      <!-- Modal Edit Komisi -->
                      <div class="modal fade" id="editModal<?= $row['sales_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $row['sales_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="editModalLabel<?= $row['sales_id']; ?>">Edit Komisi Sales</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form method="POST">
                                <input type="hidden" name="sales_id" value="<?= $row['sales_id']; ?>">
                                <div class="form-group">
                                  <label for="komisi">Komisi</label>
                                  <input type="text" class="form-control" id="komisi" name="komisi" value="<?= formatRupiah($row['komisi']); ?>">
                                </div>
                                <button type="submit" name="update_komisi" class="btn btn-primary">Simpan Perubahan</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
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
    if (!$.fn.DataTable.isDataTable('#sales-table')) {
      $("#sales-table").DataTable({
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
      }).buttons().container().appendTo('#sales-table_wrapper .col-md-6:eq(0)');
    }
  });
</script>
</body>
</html>