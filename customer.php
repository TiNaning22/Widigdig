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
            <h1>Data Customer</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Customers</li>
            </ol>
          </div>
          <div class="tambah-data">
          	<a href="customer-add" class="btn btn-primary">Tambah Data</a>
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
              <h3 class="card-title">Data Customer Keseluruhan</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>No.</th>
                    <th style="text-transform: capitalize;">Nama</th>
                    <th>No. Hp</th>
                    <th>Alamat</th>
                    <th>Kategori</th>
                    <th style="text-align: center;">Status</th>
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
                     	<td><?= $row['customer_tlpn']; ?></td>
                     	<td>
                        <?php  
                          $alamat = $row['customer_alamat'];
                          $alamat1 = substr($row['customer_alamat'],0,18) . '...';
                            if ( str_word_count($alamat) > 2 ) {
                              echo($alamat1);
                            } else {
                              echo($alamat);
                            }
                        ?>    
                      </td>
                      <td>
                        <?php  
                          if ( $row['customer_category'] == 1 ) {
                              $customer_category = "Grosir 1";
                          } elseif ( $row['customer_category'] == 2 ) {
                              $customer_category = "Grosir 2";
                          } else {
                              $customer_category = "Umum";
                          }
                          echo $customer_category;
                        ?>
                      </td>
                      <td style="text-align: center;">
                      	<?php 
                      		if ( $row['customer_status'] === "1" ) {
                      			echo "<b>Aktif</b>";
                      		} else {
                      			echo "<b style='color: red;'>Tidak Aktif</b>";
                      		}
                      	?>		
                      </td>
                      <td class="orderan-online-button">
                        <?php $id = $row["customer_id"]; ?>
                      	<a href="customer-zoom?id=<?= $id; ?>" title="Zoom Data">
                              <button class="btn btn-success" type="submit">
                                 <i class="fa fa-search"></i>
                              </button>
                          </a>
                      	<a href="customer-edit?id=<?= $id; ?>" title="Edit Data">
                              <button class="btn btn-primary" type="submit">
                                 <i class="fa fa-edit"></i>
                              </button>
                          </a>
                          <a href="customer-delete?id=<?= $id; ?>" onclick="return confirm('Yakin dihapus ?')" title="Delete Data">
                              <button class="btn btn-danger" type="submit" name="hapus">
                                  <i class="fa fa-trash-o"></i>
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
<!-- AdminLTE App -->
<!-- <script src="dist/js/adminlte.min.js"></script> -->
<!-- AdminLTE for demo purposes -->
<!-- <script src="dist/js/demo.js"></script> -->
<!-- page script -->
<script>
  $(function () {
    $("#example1").DataTable();
  });
</script>
</body>
</html>
