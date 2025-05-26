<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ($levelLogin === "kurir") {
    echo "<script>document.location.href = 'bo';</script>";
  }  
?>
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Laporan Penjualan Sales</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Laporan Penjualan Sales</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-12">
          <?php  
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            // Get all sales for the filter dropdown
            try {
              $allSales = query("SELECT sales_id, sales_nama FROM sales WHERE sales_cabang = '$sessionCabang' AND sales_status = 1 ORDER BY sales_nama ASC");
            } catch (Exception $e) {
              echo "<!-- Error loading sales: " . $e->getMessage() . " -->";
              $allSales = [];
            }
            
            // Get selected sales_id from filter, if any
            $selectedSalesId = isset($_GET['sales_id']) ? (int)$_GET['sales_id'] : 0;
            
            try {
              // Base query
              $sql = "SELECT 
                      ps.id as penjualan_id,
                      ps.penjualan_invoice,
                      ps.penjualan_date,
                      s.sales_nama,
                      s.sales_id,
                      ps.penjualan_total as total_penjualan
                    FROM penjualan_sales ps
                    LEFT JOIN sales s ON ps.penjualan_sales_id = s.sales_id
                    WHERE ps.penjualan_cabang = '$sessionCabang' 
                      AND ps.penjualan_sales_id > 0";
              
              // Add sales filter if selected
              if ($selectedSalesId > 0) {
                $sql .= " AND ps.penjualan_sales_id = $selectedSalesId";
              }
              
              // Add order by
              $sql .= " ORDER BY ps.penjualan_date DESC";
              
              $data = query($sql);
              
              echo "<!-- Debug: " . count($data) . " data ditemukan -->";
            } catch (Exception $e) {
              echo "<!-- Error: " . $e->getMessage() . " -->";
              $data = [];
            }
          ?>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Laporan Penjualan Sales</h3>
            </div>
            <div class="card-body">
              <!-- Sales Filter Form -->
              <form method="get" class="mb-4">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="sales_id">Filter berdasarkan Sales:</label>
                      <div class="input-group">
                        <select name="sales_id" id="sales_id" class="form-control">
                          <option value="0">-- Semua Sales --</option>
                          <?php foreach ($allSales as $sales) : ?>
                            <option value="<?= $sales['sales_id'] ?>" <?= $selectedSalesId == $sales['sales_id'] ? 'selected' : '' ?>>
                              <?= $sales['sales_nama'] ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                          <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                          </button>
                          <?php if ($selectedSalesId > 0) : ?>
                            <a href="laporan-sales" class="btn btn-default">
                              <i class="fas fa-sync"></i> Reset
                            </a>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              
              <?php if (empty($data)): ?>
                <div class="alert alert-info">Belum ada data penjualan sales<?= $selectedSalesId > 0 ? ' untuk sales yang dipilih' : '' ?>.</div>
              <?php else: ?>
              <div class="table-auto">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>No</th>
                    <th>Invoice</th>
                    <th>Tanggal</th>
                    <th>Sales</th>
                    <th>Total Penjualan</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $i = 1; 
                    $grand_total = 0;
                  ?>
                  <?php foreach ($data as $row) : ?>
                  <?php 
                    $total = isset($row['total_penjualan']) ? $row['total_penjualan'] : 0;
                    $grand_total += $total;
                  ?>
                  <tr>
                    <td><?= $i; ?></td>
                    <td><?= $row['penjualan_invoice'] ?? $row['penjualan_id']; ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($row['penjualan_date'])); ?></td>
                    <td><?= $row['sales_nama'] ?? 'Tidak ada sales'; ?></td>
                    <td>Rp. <?= number_format($total, 0, ',', '.'); ?></td>
                    <td class="text-center">
                      <a href="penjualan-detail?no=<?= $row['penjualan_invoice'] ?? $row['penjualan_id']; ?>" target="_blank" title="Detail">
                
                      </a>
                    </td>
                  </tr>
                  <?php $i++; ?>
                  <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="4" class="text-right">Grand Total</th>
                      <th>Rp. <?= number_format($grand_total, 0, ',', '.'); ?></th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
<?php include '_footer.php'; ?>
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $("#example1").DataTable();
  });
</script>
</body>
</html>