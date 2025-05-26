<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
  if ( $levelLogin !== "super admin" ) {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Data Tingkat Akses User</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Access Levels</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>


    <?php  
    	$data = query("SELECT * FROM user WHERE user_id > 1 ORDER BY user_id DESC");
    ?>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Data Tingkat Akses User</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No.</th>
                  <th style="text-transform: capitalize;">Nama</th>
                  <th>No. Hp</th>
                  <th>Email</th>
                  <th>Level</th>
                  <th>Cabang</th>
                  <th style="text-align: center;">Status</th>
                  <th style="text-align: center; vertical-align: middle;">Aksi</th>
                </tr>
                </thead>
                <tbody>

                <?php $i = 1; ?>
                <?php foreach ( $data as $row ) : ?>
                <tr>
                  	<td><?= $i; ?></td>
                  	<td><?= $row['user_nama']; ?></td>
                   	<td><?= $row['user_no_hp']; ?></td>
                    <td><?= $row['user_email']; ?></td>
                   	<td style="text-transform: capitalize;"><?= $row['user_level']; ?></td>
                    <td>
                      <?php 
                        if ($row['user_cabang'] == 0) {
                          echo "Pusat";
                        } else {
                          echo "Cabang " . $row['user_cabang'];
                        }
                      ?>
                    </td>
                    <td style="text-align: center;">
                    	<?php 
                    		if ( $row['user_status'] === "1" ) {
                    			echo "<b>Aktif</b>";
                    		} else {
                    			echo "<b style='color: red;'>Tidak Aktif</b>";
                    		}
                    	?>		
                    </td>
                    <td class="orderan-online-button" >
                        <?php 
                            $idUser = $row["user_id"];
                        ?>
                        <a href="access-level-edit?id=<?= $idUser; ?>" title="Edit Data">
                            <button class="btn btn-primary" type="button">
                                <i class="fa fa-edit"></i>
                            </button>
                        </a>
                    </td>
                </tr>
                <?php $i++; ?>
            	<?php endforeach; ?>
              </table>
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

<div class="modal fade" id="modal-id-2">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Warning</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body text-center">
        <h4>
            Data User ada di dalam data Invoice Jadi <b>TIDAK BISA DIHAPUS</b>
        </h4>
        <small>
            Jika ingin menghapus user ini Lakukan <b>Hapus Semua Data Invoice</b> dengan Transaksi <b>user tersebut !!</b>
        </small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $("#example1").DataTable();
  });
</script>
</body>
</html>