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
            <h1>Laporan Rekap Sisa Piutang Per Cabang</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Rekap Sisa Piutang</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Filter Data Berdasarkan Tanggal</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
            </div>
          </div>
          <!-- /.card-header -->
          <form role="form" action="" method="POST">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="form-control" id="tanggal_awal" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" id="tanggal_akhir" required>
                    </div>
                </div>
              </div>
              <div class="card-footer text-right">
                  <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fa fa-filter"></i> Filter
                  </button>
              </div>
            </div>
          </form>
      </div>
    </section>

    <?php if( isset($_POST["submit"]) ){ ?>
        <?php  
          $tanggal_awal  = $_POST['tanggal_awal'];
          $tanggal_akhir = $_POST['tanggal_akhir'];
        ?>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Laporan Rekap Sisa Piutang Per Cabang</h3>
              <div class="float-right">
                <a href="laporan-sisapiutang-percabang-print?tanggal_awal=<?= $tanggal_awal; ?>&tanggal_akhir=<?= $tanggal_akhir; ?>" class="btn btn-success btn-sm" target="_blank">
                  <i class="fa fa-print"></i> Print/Export
                </a>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="rekap-sisa-piutang" class="table table-bordered table-striped table-laporan">
                  <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th>Cabang</th>
                    <th>Jumlah Invoice</th>
                    <th>Total Sisa Piutang</th>
                    <th>Aksi</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $i = 1; 
                    $grand_total = 0;
                    
                    // Query untuk mendapatkan data cabang dari tabel toko dan piutang dari berbagai sumber
                    // Menggabungkan data dari invoice, piutang_awal, dan tabel terkait
                    $queryCabang = $conn->query("
                      SELECT 
                        t.toko_id as cabang_id, 
                        t.toko_nama as cabang_nama,
                        (
                          -- Count invoice piutang yang belum lunas
                          SELECT COUNT(invoice_id) FROM invoice 
                          WHERE invoice_cabang = t.toko_id 
                          AND invoice_piutang = 1 
                          AND invoice_piutang_lunas = 0 
                          AND invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
                        ) +
                        (
                          -- Count piutang awal yang belum lunas
                          SELECT COUNT(id) FROM piutang_awal 
                          WHERE cabang = t.toko_nama 
                          AND status = 'belum_lunas' 
                          AND tanggal_transaksi BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
                        ) as jumlah_invoice,
                        (
                          -- Sum sisa piutang dari invoice
                          SELECT COALESCE(SUM(invoice_total - invoice_bayar), 0) FROM invoice 
                          WHERE invoice_cabang = t.toko_id 
                          AND invoice_piutang = 1 
                          AND invoice_piutang_lunas = 0 
                          AND invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
                        ) +
                        (
                          -- Sum sisa piutang awal
                          SELECT COALESCE(SUM(nominal_piutang), 0) FROM piutang_awal 
                          WHERE cabang = t.toko_nama 
                          AND status = 'belum_lunas' 
                          AND tanggal_transaksi BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
                        ) -
                        (
                          -- Sum pembayaran piutang
                          SELECT COALESCE(SUM(p.piutang_nominal), 0) FROM piutang p
                          JOIN invoice i ON p.piutang_invoice = i.invoice_id 
                          WHERE i.invoice_cabang = t.toko_id 
                          AND p.piutang_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
                        ) -
                        (
                          -- Sum kembalian piutang
                          SELECT COALESCE(SUM(pk.pl_nominal), 0) FROM piutang_kembalian pk
                          JOIN invoice i ON pk.pl_invoice = i.invoice_id 
                          WHERE i.invoice_cabang = t.toko_id 
                          AND pk.pl_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."'
                        ) as total_sisa_piutang
                      FROM toko t
                      WHERE t.toko_status = 1
                      GROUP BY t.toko_id
                      ORDER BY total_sisa_piutang DESC
                    ");

                    if (mysqli_num_rows($queryCabang) > 0) {
                      while ($rowCabang = mysqli_fetch_array($queryCabang)) {
                        // Skip cabang tanpa piutang
                        if ($rowCabang['jumlah_invoice'] == 0 && $rowCabang['total_sisa_piutang'] == 0) {
                          continue;
                        }
                        $grand_total += $rowCabang['total_sisa_piutang'];
                  ?>
                  <tr>
                      <td><?= $i; ?></td>
                      <td><?= $rowCabang['cabang_nama']; ?></td>
                      <td><?= $rowCabang['jumlah_invoice']; ?> Invoice</td>
                      <td>Rp. <?= number_format($rowCabang['total_sisa_piutang'], 0, ',', '.'); ?></td>
                      <td>
                        <a href="laporan-detail-sisa-piutang?cabang_id=<?= $rowCabang['cabang_id']; ?>&tanggal_awal=<?= $tanggal_awal; ?>&tanggal_akhir=<?= $tanggal_akhir; ?>" class="btn btn-info btn-sm" target="_blank">
                          <i class="fa fa-eye"></i> Detail
                        </a>
                      </td>
                  </tr>
                  <?php $i++; ?>
                  <?php 
                      }
                    } else {
                  ?>
                  <tr>
                    <td colspan="5" class="text-center">Tidak ada data piutang dalam periode ini</td>
                  </tr>
                  <?php
                    }
                  ?>
                  <tr>
                      <td colspan="3">
                        <b>Grand Total</b>
                      </td>
                      <td colspan="2">
                        <b>Rp. <?php echo number_format($grand_total, 0, ',', '.'); ?></b>
                      </td>
                  </tr>
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
    <?php  } ?>
  </div>
</div>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $("#rekap-sisa-piutang").DataTable({
      "responsive": true,
      "autoWidth": false,
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
    });
  });
</script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
    
    // Set default dates if empty
    if(!$('#tanggal_awal').val()) {
      // Set to first day of current month
      var date = new Date();
      var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
      var formattedDate = firstDay.toISOString().slice(0,10);
      $('#tanggal_awal').val(formattedDate);
    }
    
    if(!$('#tanggal_akhir').val()) {
      // Set to current date
      var today = new Date();
      var formattedDate = today.toISOString().slice(0,10);
      $('#tanggal_akhir').val(formattedDate);
    }
  });
</script>
</body>
</html>