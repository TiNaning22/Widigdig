<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 

  // Cek akses level
  if ($levelLogin !== "admin" && $levelLogin !== "manager") {
    echo "<script>document.location.href = 'bo';</script>";
  }
  
  // Set default tanggal jika tidak ada di parameter
  $tanggal_awal = isset($_POST['tanggal_awal']) ? $_POST['tanggal_awal'] : date('Y-m-01');
  $tanggal_akhir = isset($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : date('Y-m-d');
  
  // Filter sales
  $sales_id = isset($_POST['sales_id']) ? $_POST['sales_id'] : "";
  
 $querySales = mysqli_query($conn, "SELECT * FROM sales WHERE sales_cabang = '$sessionCabang' AND sales_status = '1' ORDER BY sales_nama ASC");
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Laporan Penjualan Per Sales</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Laporan Penjualan Per Sales</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Filter Laporan</h3>
            </div>
            <div class="card-body">
              <form action="" method="post">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Tanggal Awal</label>
                      <input type="date" name="tanggal_awal" class="form-control" value="<?= $tanggal_awal; ?>" required>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Tanggal Akhir</label>
                      <input type="date" name="tanggal_akhir" class="form-control" value="<?= $tanggal_akhir; ?>" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Sales</label>
                      <select name="sales_id" class="form-control">
                        <option value="">-- Semua Sales --</option>
                        <?php while($sales = mysqli_fetch_array($querySales)) { ?>
                       <option value="<?= $sales['sales_id']; ?>" <?= $sales_id == $sales['sales_id'] ? 'selected' : ''; ?>>
                          <?= $sales['sales_nama']; ?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>&nbsp;</label>
                      <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <?php if (isset($_POST['tanggal_awal']) && isset($_POST['tanggal_akhir'])) { ?>
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Laporan Penjualan Per Sales</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" onclick="printReport()">
                  <i class="fas fa-print"></i> Print
                </button>
                <button type="button" class="btn btn-tool" onclick="exportExcel()">
                  <i class="fas fa-file-excel"></i> Export Excel
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tabelData">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Tanggal</th>
                      <th>No. Invoice</th>
                      <th>Nama Sales</th>
                      <th>Nama Pelanggan</th>
                      <th>Total Item</th>
                      <th>Total Omset</th>
                      <th>Total Laba</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    // Query untuk mendapatkan data penjualan berdasarkan filter
                    $filterSales = $sales_id != "" ? "AND p.sales_id = '$sales_id'" : "";
                    $queryPenjualan = mysqli_query($conn, "
                      SELECT 
                        p.penjualan_id,
                        p.penjualan_tanggal,
                        p.penjualan_invoice,
                        s.nama_sales,
                        pel.pelanggan_nama,
                        COUNT(pd.penjualan_detail_id) as total_item,
                        SUM(pd.penjualan_detail_total) as total_omset,
                        SUM(pd.penjualan_detail_total - pd.penjualan_detail_harga_beli * pd.penjualan_detail_qty) as total_laba
                      FROM penjualan p
                      LEFT JOIN sales s ON p.sales_id = s.id
                      LEFT JOIN pelanggan pel ON p.pelanggan_id = pel.pelanggan_id
                      LEFT JOIN penjualan_detail pd ON p.penjualan_id = pd.penjualan_id
                      WHERE p.cabang = '$sessionCabang'
                      AND p.penjualan_tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
                      $filterSales
                      GROUP BY p.penjualan_id
                      ORDER BY p.penjualan_tanggal DESC
                    ");
                    
                    $no = 1;
                    $totalOmset = 0;
                    $totalLaba = 0;
                    
                    while ($data = mysqli_fetch_array($queryPenjualan)) {
                      $totalOmset += $data['total_omset'];
                      $totalLaba += $data['total_laba'];
                    ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <td><?= date('d-m-Y', strtotime($data['penjualan_tanggal'])); ?></td>
                      <td><?= $data['penjualan_invoice']; ?></td>
                      <td><?= $data['nama_sales'] ? $data['nama_sales'] : 'Tidak ada sales'; ?></td>
                      <td><?= $data['pelanggan_nama']; ?></td>
                      <td><?= $data['total_item']; ?></td>
                      <td>Rp. <?= number_format($data['total_omset'], 0, ',', '.'); ?></td>
                      <td>Rp. <?= number_format($data['total_laba'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="6" class="text-right">Total:</th>
                      <th>Rp. <?= number_format($totalOmset, 0, ',', '.'); ?></th>
                      <th>Rp. <?= number_format($totalLaba, 0, ',', '.'); ?></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

          <!-- Ringkasan Sales -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Ringkasan Per Sales</h3>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tabelRingkasan">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama Sales</th>
                      <th>Total Transaksi</th>
                      <th>Total Omset</th>
                      <th>Total Laba</th>
                      <th>Persentase Kontribusi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    // Query untuk mendapatkan ringkasan per sales
                    $queryRingkasan = mysqli_query($conn, "
                      SELECT 
                        s.nama_sales,
                        COUNT(DISTINCT p.penjualan_id) as total_transaksi,
                        SUM(pd.penjualan_detail_total) as total_omset,
                        SUM(pd.penjualan_detail_total - pd.penjualan_detail_harga_beli * pd.penjualan_detail_qty) as total_laba
                      FROM penjualan p
                      LEFT JOIN sales s ON p.sales_id = s.id
                      LEFT JOIN penjualan_detail pd ON p.penjualan_id = pd.penjualan_id
                      WHERE p.cabang = '$sessionCabang'
                      AND p.penjualan_tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
                      $filterSales
                      GROUP BY p.sales_id
                      ORDER BY total_omset DESC
                    ");
                    
                    $no = 1;
                    $grandTotalOmset = 0;
                    $grandTotalLaba = 0;
                    
                    // Hitung grand total untuk persentase
                    $queryGrandTotal = mysqli_query($conn, "
                      SELECT 
                        SUM(pd.penjualan_detail_total) as grand_total_omset,
                        SUM(pd.penjualan_detail_total - pd.penjualan_detail_harga_beli * pd.penjualan_detail_qty) as grand_total_laba
                      FROM penjualan p
                      LEFT JOIN penjualan_detail pd ON p.penjualan_id = pd.penjualan_id
                      WHERE p.cabang = '$sessionCabang'
                      AND p.penjualan_tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
                    ");
                    $grandTotal = mysqli_fetch_array($queryGrandTotal);
                    
                    while ($data = mysqli_fetch_array($queryRingkasan)) {
                      $grandTotalOmset += $data['total_omset'];
                      $grandTotalLaba += $data['total_laba'];
                      $persentase = ($data['total_omset'] / $grandTotal['grand_total_omset']) * 100;
                    ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <td><?= $data['nama_sales'] ? $data['nama_sales'] : 'Tidak ada sales'; ?></td>
                      <td><?= $data['total_transaksi']; ?></td>
                      <td>Rp. <?= number_format($data['total_omset'], 0, ',', '.'); ?></td>
                      <td>Rp. <?= number_format($data['total_laba'], 0, ',', '.'); ?></td>
                      <td><?= number_format($persentase, 2); ?>%</td>
                    </tr>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="3" class="text-right">Total:</th>
                      <th>Rp. <?= number_format($grandTotalOmset, 0, ',', '.'); ?></th>
                      <th>Rp. <?= number_format($grandTotalLaba, 0, ',', '.'); ?></th>
                      <th>100%</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include '_footer.php'; ?>

<script>
  $(function() {
    // DataTable initialization
    $("#tabelData").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    });
    
    $("#tabelRingkasan").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    });
  });
  
  // Function untuk print report
  function printReport() {
    var printContents = document.getElementById('tabelData').outerHTML;
    var originalContents = document.body.innerHTML;
    
    document.body.innerHTML = `
      <div style="padding: 20px;">
        <h2 style="text-align: center;">Laporan Penjualan Per Sales</h2>
        <h4 style="text-align: center;">Periode: <?= date('d-m-Y', strtotime($tanggal_awal)); ?> s/d <?= date('d-m-Y', strtotime($tanggal_akhir)); ?></h4>
        ${printContents}
      </div>
    `;
    
    window.print();
    document.body.innerHTML = originalContents;
  }
  
  // Function untuk export Excel
  function exportExcel() {
    var table2excel = new Table2Excel();
    table2excel.export(document.getElementById('tabelData'), 'Laporan Penjualan Per Sales');
  }
</script>