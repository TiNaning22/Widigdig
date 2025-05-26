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
          <h1>Laporan Buku Piutang</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Buku Piutang</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Filter Section -->
  <section class="content">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Filter Laporan</h3>
      </div>
      <div class="card-body">
        <form action="" method="GET">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="status">Status Piutang:</label>
                <select name="status" id="status" class="form-control">
                  <option value="semua" <?= isset($_GET['status']) && $_GET['status'] == 'semua' ? 'selected' : ''; ?>>Semua</option>
                  <option value="belum-lunas" <?= isset($_GET['status']) && $_GET['status'] == 'belum-lunas' ? 'selected' : ''; ?>>Belum Lunas</option>
                  <option value="lunas" <?= isset($_GET['status']) && $_GET['status'] == 'lunas' ? 'selected' : ''; ?>>Lunas</option>
                  <option value="menunggak" <?= isset($_GET['status']) && $_GET['status'] == 'menunggak' ? 'selected' : ''; ?>>Menunggak</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="dari">Dari Tanggal:</label>
                <input type="date" name="dari" id="dari" class="form-control" value="<?= isset($_GET['dari']) ? $_GET['dari'] : date('Y-m-01'); ?>">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="sampai">Sampai Tanggal:</label>
                <input type="date" name="sampai" id="sampai" class="form-control" value="<?= isset($_GET['sampai']) ? $_GET['sampai'] : date('Y-m-d'); ?>">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="customer">Customer:</label>
                <select name="customer" id="customer" class="form-control select2">
                  <option value="">-- Semua Customer --</option>
                  <?php
                    $customers = query("SELECT * FROM customer WHERE customer_cabang = $sessionCabang ORDER BY customer_nama ASC");
                    foreach($customers as $customer) {
                      $selected = (isset($_GET['customer']) && $_GET['customer'] == $customer['customer_id']) ? 'selected' : '';
                      echo "<option value='".$customer['customer_id']."' ".$selected.">".$customer['customer_nama']."</option>";
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <button type="submit" class="btn btn-primary">Filter</button>
                <?php if(isset($_GET['status'])): ?>
                <a href="buku-piutang" class="btn btn-secondary">Reset</a>
                <button type="button" onclick="printReport()" class="btn btn-success"><i class="fa fa-print"></i> Cetak</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>

  <?php
  // Get filter parameters
  $status = isset($_GET['status']) ? $_GET['status'] : 'semua';
  $dariTanggal = isset($_GET['dari']) ? $_GET['dari'] : date('Y-m-01');
  $sampaiTanggal = isset($_GET['sampai']) ? $_GET['sampai'] : date('Y-m-d');
  $customerId = isset($_GET['customer']) ? $_GET['customer'] : '';

  // Prepare base query
  $whereClause = "a.invoice_cabang = $sessionCabang";

  // Add date filter
  $whereClause .= " AND (a.invoice_date BETWEEN '$dariTanggal' AND '$sampaiTanggal')";

  // Add customer filter if specified
  if(!empty($customerId)) {
    $whereClause .= " AND a.invoice_customer = $customerId";
  }

  // Add status filter
  switch($status) {
    case 'belum-lunas':
      $whereClause .= " AND a.invoice_piutang > 0 AND a.invoice_piutang_lunas = 0";
      break;
    case 'lunas':
      $whereClause .= " AND a.invoice_piutang_lunas > 0";
      break;
    case 'menunggak':
      $day = date("Y-m")."-01";
      $whereClause .= " AND a.invoice_piutang > 0 AND a.invoice_piutang_lunas = 0 AND EXISTS (
        SELECT 1 FROM piutang p WHERE p.piutang_invoice = a.penjualan_invoice AND p.piutang_date < '$day' AND p.piutang_cabang = $sessionCabang
      )";
      break;
    // case 'semua' or default: no additional filter needed
  }

  // Get data
  $query = "
    SELECT 
      a.invoice_id, 
      a.penjualan_invoice,
      a.invoice_date, 
      a.invoice_sub_total,
      a.invoice_piutang_dp,
      a.invoice_bayar,
      a.invoice_kembali,
      a.invoice_cabang, 
      a.invoice_kasir, 
      a.invoice_customer,
      a.invoice_piutang,
      a.invoice_piutang_lunas,
      a.invoice_piutang_jatuh_tempo,
      b.customer_id,
      b.customer_nama,
      b.customer_tlpn,
      c.user_id,
      c.user_nama,
      (
        SELECT MAX(piutang_date_time)
        FROM piutang
        WHERE piutang_invoice = a.penjualan_invoice AND piutang_cabang = $sessionCabang
      ) as terakhir_bayar
    FROM invoice a
    LEFT JOIN user c ON a.invoice_kasir = c.user_id
    LEFT JOIN customer b ON a.invoice_customer = b.customer_id
    WHERE $whereClause
    ORDER BY a.invoice_date DESC, a.penjualan_invoice DESC
  ";

  $dataLaporan = query($query);
  $totalPiutang = 0;
  $totalTerbayar = 0;
  $totalSisaPiutang = 0;
  ?>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <?php
              switch($status) {
                case 'belum-lunas':
                  echo "Data Piutang Belum Lunas";
                  break;
                case 'lunas':
                  echo "Data Piutang Lunas";
                  break;
                case 'menunggak':
                  echo "Data Piutang Menunggak";
                  break;
                default:
                  echo "Semua Data Piutang";
              }
              ?>
            </h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-responsive">
              <table id="laporan-piutang" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th style="width: 6%;">No.</th>
                  <th>Invoice</th>
                  <th>Tanggal Transaksi</th>
                  <th>Customer</th>
                  <th>Jatuh Tempo</th>
                  <th>Total Piutang</th>
                  <th>Terbayar</th>
                  <th>Sisa Piutang</th>
                  <th>Status</th>
                  <th>Terakhir Bayar</th>
                  <?php if($status == 'menunggak'): ?>
                  <th>Lama Menunggak</th>
                  <?php endif; ?>
                  <th style="text-align: center;">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1; ?>
                <?php foreach($dataLaporan as $row): ?>
                  <?php
                  $totalPiutang += $row['invoice_sub_total'];
                  $terbayar = $row['invoice_piutang_dp'] + $row['invoice_bayar'] - $row['invoice_kembali'];
                  $totalTerbayar += $terbayar;
                  $sisaPiutang = $row['invoice_sub_total'] - $terbayar;
                  $totalSisaPiutang += $sisaPiutang;
                  
                  // Determine status
                  $piutangStatus = "";
                  if($row['invoice_piutang_lunas'] > 0) {
                    $piutangStatus = "<span class='badge badge-success'>Lunas</span>";
                  } else {
                    // Check if overdue
                    $day = date("Y-m")."-01";
                    $checkMenunggak = query("SELECT 1 FROM piutang WHERE piutang_invoice = ".$row['penjualan_invoice']." AND piutang_date < '".$day."' AND piutang_cabang = $sessionCabang LIMIT 1");
                    
                    if(count($checkMenunggak) > 0) {
                      $piutangStatus = "<span class='badge badge-danger'>Menunggak</span>";
                    } else {
                      $piutangStatus = "<span class='badge badge-warning'>Belum Lunas</span>";
                    }
                  }
                  ?>
                  <tr>
                    <td><?= $i; ?></td>
                    <td><?= $row['penjualan_invoice']; ?></td>
                    <td><?= tanggal_indo($row['invoice_date']); ?></td>
                    <td><?= $row['customer_nama']; ?></td>
                    <td><?= tanggal_indo($row['invoice_piutang_jatuh_tempo']); ?></td>
                    <td class="text-right">Rp <?= number_format($row['invoice_sub_total'], 0, ',', '.'); ?></td>
                    <td class="text-right">Rp <?= number_format($terbayar, 0, ',', '.'); ?></td>
                    <td class="text-right">Rp <?= number_format($sisaPiutang, 0, ',', '.'); ?></td>
                    <td><?= $piutangStatus; ?></td>
                    <td><?= !empty($row['terakhir_bayar']) ? $row['terakhir_bayar'] : '-'; ?></td>
                    
                    <?php if($status == 'menunggak'): ?>
                    <td>
                      <?php  
                        // Only calculate if we're in menunggak status
                        if($piutangStatus == "<span class='badge badge-danger'>Menunggak</span>") {
                          // Get latest payment date
                          $piutangDateData = query("SELECT piutang_date FROM piutang WHERE piutang_invoice = ".$row['penjualan_invoice']." AND piutang_cabang = $sessionCabang ORDER BY piutang_id DESC LIMIT 1");
                          $piutang_date = !empty($piutangDateData) ? $piutangDateData[0]['piutang_date'] : $row['invoice_date'];

                          // Tanggal Utama
                          $tanggal = new DateTime($piutang_date);

                          // Tanggal Hari Ini
                          $today = new DateTime('today');

                          // Tahun
                          $tahun = $today->diff($tanggal)->y;

                          // Bulan
                          $bulan = $today->diff($tanggal)->m;

                          // Hari
                          $hari = $today->diff($tanggal)->d;

                          if ($tahun < 1 && $bulan > 0 && $hari > 0) {
                            $dateNunggak = $bulan." bulan, ".$hari." hari ";
                          } elseif ($tahun < 1 && $bulan < 1 && $hari > 0) {
                            $dateNunggak = $hari." hari ";
                          } elseif ($tahun < 1 && $bulan > 0 && $hari < 1) {
                            $dateNunggak = $bulan." bulan ";
                          } elseif ($tahun > 0 && $bulan < 1 && $hari > 0) {
                            $dateNunggak = $tahun." tahun, ".$hari." hari ";
                          } elseif ($tahun > 0 && $bulan < 1 && $hari < 1) {
                            $dateNunggak = $tahun." tahun ";
                          } else {
                            $dateNunggak = $tahun." tahun, ".$bulan." bulan, ".$hari." hari ";
                          }
                          echo $dateNunggak;
                        } else {
                          echo "-";
                        }
                      ?>
                    </td>
                    <?php endif; ?>
                    
                    <td class="orderan-online-button">
                      <a href="penjualan-zoom?no=<?= base64_encode($row['invoice_id']); ?>" target="_blank">
                        <button class='btn btn-info' title='Lihat Data'>
                          <i class='fa fa-eye'></i>
                        </button>
                      </a>&nbsp;

                      <a href="piutang-cicilan?no=<?= base64_encode($row['invoice_id']); ?>">
                        <button class='btn btn-success' title='Cicilan'>
                          <i class='fa fa-money'></i>
                        </button>
                      </a>&nbsp;

                      <?php if($row['invoice_piutang_lunas'] == 0 && $levelLogin !== "kasir"): ?>
                      <a href="piutang-edit?no=<?= base64_encode($row['invoice_id']); ?>" onclick="return confirm('Fitur ini digunkan untuk RETUR TRANSAKSI jika barang pembelian TIDAK JADI atau ingin Mengurangi QTY.. Apakah Anda Yakin !!!')">
                        <button class='btn btn-primary' title="Retur">
                          <i class='fa fa-edit'></i>
                        </button>
                      </a>&nbsp;
                      <?php endif; ?>

                      <a href="nota-cetak-piutang?no=<?= $row['invoice_id']; ?>" target="_blank">
                        <button class='btn btn-warning' title="Cetak Nota">
                          <i class='fa fa-print'></i>
                        </button>
                      </a>&nbsp;

                      <?php if($row['invoice_piutang_lunas'] == 0 && $levelLogin === "super admin"): ?>
                      <a href="penjualan-delete-invoice?id=<?= $row['invoice_id']; ?>&page=laporan-buku-piutang" onclick="return confirm('Apakah Anda Yakin Hapus Seluruh Data No. Invoice <?= $row['penjualan_invoice']; ?> ?')">
                        <button class='btn btn-danger' title="Delete Invoice">
                          <i class='fa fa-trash-o'></i>
                        </button>
                      </a>
                      <?php endif; ?>

                      <?php if($piutangStatus == "<span class='badge badge-danger'>Menunggak</span>"): ?>
                      <?php $no_wa = substr_replace($row['customer_tlpn'],'62',0,1); ?>
                      <a href="https://api.whatsapp.com/send?phone=<?= $no_wa; ?>&text=Halo <?= $row['customer_nama'];?>, Kami dari *<?= $dataTokoLogin['toko_nama']; ?> <?= $dataTokoLogin['toko_kota']; ?>* memberikan informasi bahwa transaksi *No Invoice <?= $row['penjualan_invoice'];?> dengan jumlah transaksi Rp <?= number_format($row['invoice_sub_total'], 0, ',', '.'); ?>* Belum Lunas dengan Sisa Piutang Rp <?= number_format($sisaPiutang, 0, ',', '.'); ?>.%0A%0ASub Total: Rp <?= number_format($row['invoice_sub_total'], 0, ',', '.'); ?>%2C%0ADP: Rp <?= number_format($row['invoice_piutang_dp'], 0, ',', '.'); ?>%2C%0ADP ditambah Total Cicilan: Rp <?= number_format($terbayar, 0, ',', '.'); ?> %2C%0A*Sisa Piutang: Rp <?= number_format($sisaPiutang, 0, ',', '.'); ?>*%2C%0A%0A%0AMohon Segera Dilunasi" target="_blank">
                        <button class='btn btn-success' title='Kirim WhatsApp'>
                          <i class='fa fa-whatsapp'></i>
                        </button>
                      </a>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php $i++; ?>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="5">TOTAL</th>
                    <th class="text-right">Rp <?= number_format($totalPiutang, 0, ',', '.'); ?></th>
                    <th class="text-right">Rp <?= number_format($totalTerbayar, 0, ',', '.'); ?></th>
                    <th class="text-right">Rp <?= number_format($totalSisaPiutang, 0, ',', '.'); ?></th>
                    <th colspan="<?= ($status == 'menunggak') ? '4' : '3'; ?>"></th>
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
  <!-- /.content -->
</div>

<!-- Print-friendly version (hidden by default) -->
<div id="print-section" style="display: none;">
  <div style="text-align: center; margin-bottom: 20px;">
    <h2><?= $dataTokoLogin['toko_nama']; ?> <?= $dataTokoLogin['toko_kota']; ?></h2>
    <h3>Laporan Buku Piutang</h3>
    <p>Periode: <?= tanggal_indo($dariTanggal); ?> s/d <?= tanggal_indo($sampaiTanggal); ?></p>
  </div>
  
  <table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
      <tr>
        <th>No.</th>
        <th>Invoice</th>
        <th>Tanggal</th>
        <th>Customer</th>
        <th>Jatuh Tempo</th>
        <th>Total Piutang</th>
        <th>Terbayar</th>
        <th>Sisa Piutang</th>
        <th>Status</th>
        <th>Terakhir Bayar</th>
        <?php if($status == 'menunggak'): ?>
        <th>Lama Menunggak</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; ?>
      <?php foreach($dataLaporan as $row): ?>
        <?php
        $terbayar = $row['invoice_piutang_dp'] + $row['invoice_bayar'] - $row['invoice_kembali'];
        $sisaPiutang = $row['invoice_sub_total'] - $terbayar;
        
        // Determine status text for printing
        $statusText = "";
        if($row['invoice_piutang_lunas'] > 0) {
          $statusText = "Lunas";
        } else {
          $day = date("Y-m")."-01";
          $checkMenunggak = query("SELECT 1 FROM piutang WHERE piutang_invoice = ".$row['penjualan_invoice']." AND piutang_date < '".$day."' AND piutang_cabang = $sessionCabang LIMIT 1");
          
          if(count($checkMenunggak) > 0) {
            $statusText = "Menunggak";
          } else {
            $statusText = "Belum Lunas";
          }
        }
        ?>
        <tr>
          <td><?= $i; ?></td>
          <td><?= $row['penjualan_invoice']; ?></td>
          <td><?= tanggal_indo($row['invoice_date']); ?></td>
          <td><?= $row['customer_nama']; ?></td>
          <td><?= tanggal_indo($row['invoice_piutang_jatuh_tempo']); ?></td>
          <td align="right">Rp <?= number_format($row['invoice_sub_total'], 0, ',', '.'); ?></td>
          <td align="right">Rp <?= number_format($terbayar, 0, ',', '.'); ?></td>
          <td align="right">Rp <?= number_format($sisaPiutang, 0, ',', '.'); ?></td>
          <td><?= $statusText; ?></td>
          <td><?= !empty($row['terakhir_bayar']) ? $row['terakhir_bayar'] : '-'; ?></td>
          
          <?php if($status == 'menunggak'): ?>
          <td>
            <?php  
              // Only calculate if we're in menunggak status
              if($statusText == "Menunggak") {
                // Get latest payment date
                $piutangDateData = query("SELECT piutang_date FROM piutang WHERE piutang_invoice = ".$row['penjualan_invoice']." AND piutang_cabang = $sessionCabang ORDER BY piutang_id DESC LIMIT 1");
                $piutang_date = !empty($piutangDateData) ? $piutangDateData[0]['piutang_date'] : $row['invoice_date'];

                // Tanggal Utama
                $tanggal = new DateTime($piutang_date);

                // Tanggal Hari Ini
                $today = new DateTime('today');

                // Calculate difference
                $tahun = $today->diff($tanggal)->y;
                $bulan = $today->diff($tanggal)->m;
                $hari = $today->diff($tanggal)->d;

                if ($tahun < 1 && $bulan > 0 && $hari > 0) {
                  $dateNunggak = $bulan." bulan, ".$hari." hari ";
                } elseif ($tahun < 1 && $bulan < 1 && $hari > 0) {
                  $dateNunggak = $hari." hari ";
                } elseif ($tahun < 1 && $bulan > 0 && $hari < 1) {
                  $dateNunggak = $bulan." bulan ";
                } elseif ($tahun > 0 && $bulan < 1 && $hari > 0) {
                  $dateNunggak = $tahun." tahun, ".$hari." hari ";
                } elseif ($tahun > 0 && $bulan < 1 && $hari < 1) {
                  $dateNunggak = $tahun." tahun ";
                } else {
                  $dateNunggak = $tahun." tahun, ".$bulan." bulan, ".$hari." hari ";
                }
                echo $dateNunggak;
              } else {
                echo "-";
              }
            ?>
          </td>
          <?php endif; ?>
        </tr>
        <?php $i++; ?>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="5">TOTAL</th>
        <th align="right">Rp <?= number_format($totalPiutang, 0, ',', '.'); ?></th>
        <th align="right">Rp <?= number_format($totalTerbayar, 0, ',', '.'); ?></th>
        <th align="right">Rp <?= number_format($totalSisaPiutang, 0, ',', '.'); ?></th>
        <th colspan="<?= ($status == 'menunggak') ? '3' : '2'; ?>"></th>
      </tr>
    </tfoot>
  </table>
  
  <div style="margin-top: 30px;">
    <table width="100%">
      <tr>
        <td width="70%"></td>
        <td align="center">
          <?= $dataTokoLogin['toko_kota']; ?>, <?= tanggal_indo(date('Y-m-d')); ?><br><br><br><br>
          <u><?= $userLogin['user_nama']; ?></u><br>
          <?= $levelLogin; ?>
        </td>
      </tr>
    </table>
  </div>
</div>

<script>
  // Print function
  function printReport() {
    var printContents = document.getElementById("print-section").innerHTML;
    var originalContents = document.body.innerHTML;
    
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
  }
  
  // Export to Excel
  function exportExcel() {
    window.open('export-excel-piutang.php?status=<?= $status; ?>&dari=<?= $dariTanggal; ?>&sampai=<?= $sampaiTanggal; ?>&customer=<?= $customerId; ?>&cabang=<?= $sessionCabang; ?>', '_blank');
  }
  
  $(function () {
    // Initialize DataTables
    $("#laporan-piutang").DataTable({
      "responsive": true,
      "autoWidth": false,
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
    });
    
    // Initialize Select2
    $('.select2').select2({
      theme: 'bootstrap4'
    });
  });
</script>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- Select2 -->
<script src="plugins/select2/js/select2.full.min.js"></script>