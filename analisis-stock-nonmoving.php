<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ($levelLogin === "kasir") {
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
          <h1>Laporan Stok Non-Moving</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Stok Non-Moving</li>
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
            <h3 class="card-title">Filter Laporan</h3>
          </div>
          <div class="card-body">
            <form method="get" action="">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Interval Waktu Non-Moving</label>
                    <select name="interval" class="form-control">
                      <option value="3" <?= isset($_GET['interval']) && $_GET['interval'] == '3' ? 'selected' : ''; ?>>3 Bulan</option>
                      <option value="6" <?= isset($_GET['interval']) && $_GET['interval'] == '6' ? 'selected' : ''; ?>>6 Bulan</option>
                      <option value="12" <?= isset($_GET['interval']) && $_GET['interval'] == '12' ? 'selected' : ''; ?>>1 Tahun</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori_id" class="form-control">
                      <option value="">Semua Kategori</option>
                      <?php
                        $queryKategori = $conn->query("SELECT * FROM kategori ORDER BY kategori_nama ASC");
                        while ($rowKategori = mysqli_fetch_array($queryKategori)) {
                          $selected = (isset($_GET['kategori_id']) && $_GET['kategori_id'] == $rowKategori['kategori_id']) ? 'selected' : '';
                          echo '<option value="'.$rowKategori['kategori_id'].'" '.$selected.'>'.$rowKategori['kategori_nama'].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary">Filter</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Stok Non-Moving</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="laporan-stok-non-moving" class="table table-bordered table-striped table-laporan">
                <thead>
                <tr>
                  <th style="width: 6%;">No.</th>
                  <th style="width: 13%;">Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Kategori</th>
                  <th>Stok</th>
                  <th>Harga Beli</th>
                  <th>Harga Jual</th>
                  <th>Tanggal Terakhir Dibeli</th>
                  <th>Tanggal Terakhir Terjual</th>
                  <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                  $interval = isset($_GET['interval']) ? $_GET['interval'] : 3; // Default 3 bulan
                  $kategoriFilter = isset($_GET['kategori_id']) && !empty($_GET['kategori_id']) ? "AND kategori.kategori_id = '".$_GET['kategori_id']."'" : "";
                  
                  $i = 1;
                  
                  // Query untuk mendapatkan data barang dengan tanggal terakhir terjual
                  $query = "SELECT 
                              b.barang_id, 
                              b.barang_kode, 
                              b.barang_nama, 
                              b.barang_stock, 
                              b.barang_harga_beli, 
                              b.barang_harga, 
                              b.barang_tanggal, 
                              k.kategori_nama,
                              s.satuan_nama,
                              (SELECT MAX(penjualan_date) FROM penjualan WHERE barang_id = b.barang_id AND penjualan_cabang = '".$sessionCabang."') AS tanggal_terakhir_terjual
                            FROM 
                              barang b
                            JOIN 
                              kategori k ON b.kategori_id = k.kategori_id
                            JOIN 
                              satuan s ON b.satuan_id = s.satuan_id
                            WHERE 
                              b.barang_cabang = '".$sessionCabang."' AND
                              b.barang_stock > 0
                              $kategoriFilter
                            ORDER BY 
                              tanggal_terakhir_terjual ASC, 
                              b.barang_tanggal ASC";
                              
                  $result = $conn->query($query);
                  
                  while ($row = mysqli_fetch_array($result)) {
                    $today = new DateTime();
                    $tanggalTerakhirTerjual = !empty($row['tanggal_terakhir_terjual']) ? new DateTime($row['tanggal_terakhir_terjual']) : null;
                    $tanggalPembelian = new DateTime($row['barang_tanggal']);
                    
                    // Jika belum pernah terjual, gunakan tanggal pembelian sebagai acuan
                    $tanggalAcuan = $tanggalTerakhirTerjual !== null ? $tanggalTerakhirTerjual : $tanggalPembelian;
                    
                    $diff = $today->diff($tanggalAcuan);
                    $bulanMandeg = ($diff->y * 12) + $diff->m;
                    
                    // Hanya tampilkan barang yang tidak terjual sesuai interval
                    if ($bulanMandeg >= $interval) {
                      // Tentukan status berdasarkan lama tidak terjual
                      if ($bulanMandeg >= 12) {
                        $status = '<span class="badge badge-danger">Deadstock (> 1 Tahun)</span>';
                      } elseif ($bulanMandeg >= 6) {
                        $status = '<span class="badge badge-warning">Deadstock (> 6 Bulan)</span>';
                      } else {
                        $status = '<span class="badge badge-info">Deadstock (> 3 Bulan)</span>';
                      }
                ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $row['barang_kode']; ?></td>
                  <td><?= $row['barang_nama']; ?></td>
                  <td><?= $row['kategori_nama']; ?></td>
                  <td><?= $row['barang_stock']; ?> <?= $row['satuan_nama']; ?></td>
                  <td>Rp. <?= number_format($row['barang_harga_beli'], 0, ',', '.'); ?></td>
                  <td>Rp. <?= number_format($row['barang_harga'], 0, ',', '.'); ?></td>
                  <td><?= date('d F Y', strtotime($row['barang_tanggal'])); ?></td>
                  <td>
                    <?= !empty($row['tanggal_terakhir_terjual']) ? date('d F Y', strtotime($row['tanggal_terakhir_terjual'])) : 'Belum Pernah Terjual'; ?>
                  </td>
                  <td><?= $status; ?></td>
                </tr>
                <?php 
                      $i++;
                    }
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
<?php include '_footer.php'; ?>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $("#laporan-stok-non-moving").DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
    });
  });
</script>
</body>
</html>