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
            <h1>Laporan Pembelian Per Faktur</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Laporan Pembelian Per Faktur</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="row">
        <div class="col-12">
          <!-- Filter Form -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Filter Laporan</h3>
            </div>
            <div class="card-body">
              <form id="filterForm" method="get">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Rentang Tanggal:</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                          </span>
                        </div>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01') ?>">
                        <div class="input-group-prepend input-group-append">
                          <span class="input-group-text">sampai</span>
                        </div>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d') ?>">
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Supplier:</label>
                      <select name="supplier_id" id="supplier_id" class="form-control">
                        <option value="">-- Semua Supplier --</option>
                        <?php
                          $suppliers = query("SELECT supplier_id, supplier_nama FROM supplier WHERE supplier_cabang = '$sessionCabang' ORDER BY supplier_nama ASC");
                          foreach ($suppliers as $supplier) {
                            $selected = (isset($_GET['supplier_id']) && $_GET['supplier_id'] == $supplier['supplier_id']) ? 'selected' : '';
                            echo "<option value='".$supplier['supplier_id']."' $selected>".$supplier['supplier_nama']."</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                  
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Staf:</label>
                      <select name="user_id" id="user_id" class="form-control">
                        <option value="">-- Semua Staf --</option>
                        <?php
                          $users = query("SELECT user_id, user_nama FROM user WHERE user_cabang = '$sessionCabang' ORDER BY user_nama ASC");
                          foreach ($users as $user) {
                            $selected = (isset($_GET['user_id']) && $_GET['user_id'] == $user['user_id']) ? 'selected' : '';
                            echo "<option value='".$user['user_id']."' $selected>".$user['user_nama']."</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-12">
                    <button type="button" id="btnFilter" class="btn btn-primary">
                      <i class="fas fa-filter"></i> Filter
                    </button>
                    <button type="button" id="btnReset" class="btn btn-default">
                      <i class="fas fa-sync"></i> Reset
                    </button>
                    
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Data Pembelian Per Faktur</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="laporanTable" class="table table-bordered table-striped" style="width: 100%">
                  <thead>
                    <tr>
                      <th style="width: 5%;">No.</th>
                      <th style="width: 12%;">No. Faktur</th>
                      <th style="width: 15%;">Tanggal</th>
                      <th>Supplier</th>
                      <th>Staf</th>
                      <th>Total Pembelian</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    // Default filter values
                    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
                    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
                    $supplierId = isset($_GET['supplier_id']) ? $_GET['supplier_id'] : '';
                    $userId = isset($_GET['user_id']) ? $_GET['user_id'] : '';
                    
                    // Build the SQL query based on filters
                    $sql = "SELECT DISTINCT
                            ip.invoice_pembelian_id,
                            ip.pembelian_invoice,
                            ip.invoice_date,
                            ip.invoice_total,
                            s.supplier_id,
                            s.supplier_nama,
                            u.user_id,
                            u.user_nama
                            FROM invoice_pembelian ip
                            LEFT JOIN supplier s ON ip.invoice_supplier = s.supplier_id
                            LEFT JOIN user u ON ip.invoice_kasir = u.user_id
                            WHERE ip.invoice_pembelian_cabang = '$sessionCabang'
                            AND DATE(ip.invoice_date) BETWEEN '$startDate' AND '$endDate'";
                    
                    // Add filters if provided
                    if (!empty($supplierId)) {
                        $sql .= " AND ip.invoice_supplier = '$supplierId'";
                    }
                    if (!empty($userId)) {
                        $sql .= " AND ip.invoice_kasir = '$userId'";
                    }
                    
                    $sql .= " ORDER BY ip.invoice_date DESC";
                    
                    // Execute query
                    $result = mysqli_query($conn, $sql);
                    $no = 1;
                    $totalSum = 0;
                    
                    // Display data
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $totalSum += $row['invoice_total'];
                            $formattedDate = date('d/m/Y H:i', strtotime($row['invoice_date']));
                            echo "<tr>
                                <td>".$no."</td>
                                <td>".$row['pembelian_invoice']."</td>
                                <td>".$formattedDate."</td>
                                <td>".$row['supplier_nama']."</td>
                                <td>".$row['user_nama']."</td>
                                <td>Rp. ".number_format($row['invoice_total'], 0, ',', '.')."</td>
                            </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>Tidak ada data yang tersedia</td></tr>";
                    }
                  ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="5" style="text-align: right">Total:</th>
                      <th>Rp. <?= number_format($totalSum, 0, ',', '.') ?></th>
                      <th></th>
                    </tr>
                  </tfoot>
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
  </div>
</div>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>

<script>
$(document).ready(function(){
    // Initialize DataTable with existing data
    var table = $('#laporanTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
    });

    // Filter button click
    $('#btnFilter').on('click', function() {
        // Get filter values
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        var supplierId = $('#supplier_id').val();
        var userId = $('#user_id').val();
        
        // Redirect with filter parameters
        window.location.href = 'laporan-pembelian-perfaktur.php?start_date=' + startDate + 
                              '&end_date=' + endDate + 
                              '&supplier_id=' + supplierId +
                              '&user_id=' + userId;
    });

    // Reset button click
    $('#btnReset').on('click', function() {
        window.location.href = 'laporan-pembelian-perfaktur.php';
    });

    // Print report button click
    $('#btnCetak').on('click', function() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        var supplierId = $('#supplier_id').val();
        var userId = $('#user_id').val();
        
        var url = 'cetak-laporan-pembelian-perfaktur.php?start_date=' + startDate + 
                '&end_date=' + endDate + 
                '&supplier_id=' + supplierId +
                '&user_id=' + userId +
                '&cabang=<?= $sessionCabang ?>';
        
        window.open(url, '_blank');
    });

    // For detail button
    $(document).on('click', '.tblDetail', function() {
        var fakturId = $(this).data('faktur');
        window.open('pembelian-detail?no=' + fakturId, '_blank');
    });

    // For print invoice button
    $(document).on('click', '.tblPrint', function() {
        var fakturId = $(this).data('faktur');
        window.open('cetak-pembelian?no=' + fakturId, '_blank');
    });
});
</script>

<?php include '_footer.php'; ?>
</body>
</html>