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
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Jenis Pelanggan</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item"><a href="customer">Customers</a></li>
              <li class="breadcrumb-item active">Jenis Pelanggan</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>


    <?php  
    	$data = query("SELECT * FROM customer WHERE customer_cabang = $sessionCabang ORDER BY customer_id DESC");
    ?>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Status Keanggotaan Pelanggan</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>No.</th>
                    <th style="text-transform: capitalize;">Nama Customer</th>
                    <th style="text-align: center;">Jenis Pelanggan</th>
                    <th style="text-align: center; width: 14%;">Aksi</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php $i = 1; ?>
                  <?php foreach ( $data as $row ) : ?>
                    <?php if ( $row['customer_id'] > 1 && $row['customer_nama'] !== "Customer Umum" ) { ?>
                  <tr>
                    	<td><?= $i; ?></td>
                    	<td><?= $row['customer_nama']; ?></td>
                      <td style="text-align: center;">
                      	<?php 
                      		if ( isset($row['customer_membership']) && $row['customer_membership'] === "1" ) {
                      			echo "<b class='text-success'>Member</b>";
                      		} else {
                      			echo "<b>Non-Member</b>";
                      		}
                      	?>		
                      </td>
                      <td class="orderan-online-button">
                        <?php $id = $row["customer_id"]; ?>
                      	<a href="membership-edit?id=<?= $id; ?>" title="Ubah Status">
                            <button class="btn btn-primary" type="submit">
                              <i class="fa fa-edit"></i> Ubah Status
                            </button>
                        </a>
                      </td>
                  </tr>
                    <?php } ?>
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
</div>


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