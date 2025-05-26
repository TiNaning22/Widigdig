<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
  if ($levelLogin === "kurir") {
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
            <h1>Laporan Rekap Sisa Hutang Per Cabang</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Rekap Sisa Hutang Per Cabang</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <?php
    // Preload detail data if modal is requested
    $detailData = [];
    $cabangNama = "";
    $totalSisaHutang = 0;
    
    if (isset($_GET['detail_cabang']) && !empty($_GET['detail_cabang'])) {
      $cabangId = mysqli_real_escape_string($conn, $_GET['detail_cabang']);
      
      // Get cabang name
      $queryCabangName = "SELECT toko_nama FROM toko WHERE toko_id = '$cabangId'";
      $resultCabangName = mysqli_query($conn, $queryCabangName);
      if ($resultCabangName && mysqli_num_rows($resultCabangName) > 0) {
        $rowCabangName = mysqli_fetch_assoc($resultCabangName);
        $cabangNama = $rowCabangName['toko_nama'];
      }
      
      // Get detail data
      $queryDetail = "SELECT 
          ip.invoice_pembelian_id,
          ip.invoice_nomor,
          DATE_FORMAT(ip.invoice_tanggal, '%d/%m/%Y') AS invoice_tanggal,
          DATE_FORMAT(ip.invoice_jatuh_tempo, '%d/%m/%Y') AS invoice_jatuh_tempo,
          s.supplier_nama,
          ip.invoice_total,
          ip.invoice_bayar,
          ip.invoice_hutang_lunas
      FROM 
          invoice_pembelian ip
      JOIN 
          supplier s ON ip.invoice_supplier = s.supplier_id
      WHERE 
          ip.invoice_pembelian_cabang = '$cabangId'
          AND ip.invoice_hutang_lunas = 0
      ORDER BY 
          ip.invoice_tanggal DESC";
      
      $resultDetail = mysqli_query($conn, $queryDetail);
      if ($resultDetail) {
        while ($row = mysqli_fetch_assoc($resultDetail)) {
          $detailData[] = $row;
          $totalSisaHutang += ($row['invoice_total'] - $row['invoice_bayar']);
        }
      }
      
      // If modal requested, show modal on page load
      echo "
      <script>
        $(document).ready(function() {
          $('#detailModal').modal('show');
        });
      </script>";
    }
    ?>

    <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Laporan Rekap Sisa Hutang per Cabang</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="rekap-sisa-hutang" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Cabang</th>
                    <th>Kota</th>
                    <th>Jumlah Invoice</th>
                    <th>Total Sisa Hutang</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $i = 1; 
                    $grand_total = 0;
                    
                    // Query untuk mendapatkan data cabang dan sisa hutang
                    $queryCabang = "SELECT 
                      c.toko_id AS cabang_id, 
                      c.toko_nama AS cabang_nama,
                      c.toko_kota AS cabang_kota,
                      (SELECT COUNT(*) FROM invoice_pembelian ip 
                       WHERE ip.invoice_pembelian_cabang = c.toko_id 
                       AND ip.invoice_hutang_lunas = 0) AS jumlah_invoice,
                      (SELECT COALESCE(SUM(ip2.invoice_total - ip2.invoice_bayar), 0) 
                       FROM invoice_pembelian ip2 
                       WHERE ip2.invoice_pembelian_cabang = c.toko_id 
                       AND ip2.invoice_hutang_lunas = 0) AS total_sisa_hutang
                    FROM toko c
                    GROUP BY c.toko_id
                    ORDER BY total_sisa_hutang DESC";

                    $resultCabang = mysqli_query($conn, $queryCabang);

                    if (!$resultCabang) {
                      die("Query Error: " . mysqli_error($conn));
                    }

                    while ($rowCabang = mysqli_fetch_array($resultCabang)) {
                      $grand_total += $rowCabang['total_sisa_hutang'];
                      $status = ($rowCabang['total_sisa_hutang'] > 0) ? '<span class="badge badge-danger">Ada Hutang</span>' : '<span class="badge badge-success">Lunas</span>';
                  ?>
                  <tr>
                      <td><?= $i; ?></td>
                      <td><?= $rowCabang['cabang_nama']; ?></td>
                      <td><?= $rowCabang['cabang_kota']; ?></td>
                      <td><?= $rowCabang['jumlah_invoice']; ?> Invoice</td>
                      <td>Rp. <?= number_format($rowCabang['total_sisa_hutang'], 0, ',', '.'); ?></td>
                      <td><?= $status; ?></td>
                  </tr>
                  <?php $i++; ?>
                  <?php } ?>
                  <tr>
                      <td colspan="4">
                        <b>Grand Total</b>
                      </td>
                      <td colspan="3">
                        <b>Rp. <?php echo number_format($grand_total, 0, ',', '.'); ?></b>
                      </td>
                  </tr>
                </tbody>
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
  </div>

  <!-- Modal Detail Hutang -->
  <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailModalLabel">Detail Sisa Hutang Cabang: <?= $cabangNama; ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table id="detail-hutang-table" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>No. Invoice</th>
                  <th>Tanggal</th>
                  <th>Jatuh Tempo</th>
                  <th>Supplier</th>
                  <th>Total Invoice</th>
                  <th>Sudah Dibayar</th>
                  <th>Sisa Hutang</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($detailData)): ?>
                  <tr>
                    <td colspan="9" class="text-center">Tidak ada data hutang</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($detailData as $index => $item): ?>
                    <?php
                      $sisaHutang = $item['invoice_total'] - $item['invoice_bayar'];
                      $status = ($sisaHutang > 0) ? 
                        '<span class="badge badge-danger">Belum Lunas</span>' : 
                        '<span class="badge badge-success">Lunas</span>';
                    ?>
                    <tr>
                      <td><?= $index + 1; ?></td>
                      <td><?= $item['invoice_nomor']; ?></td>
                      <td><?= $item['invoice_tanggal']; ?></td>
                      <td><?= $item['invoice_jatuh_tempo']; ?></td>
                      <td><?= $item['supplier_nama']; ?></td>
                      <td>Rp. <?= number_format($item['invoice_total'], 0, ',', '.'); ?></td>
                      <td>Rp. <?= number_format($item['invoice_bayar'], 0, ',', '.'); ?></td>
                      <td>Rp. <?= number_format($sisaHutang, 0, ',', '.'); ?></td>
                      <td><?= $status; ?></td>
                    </tr>
                  <?php endforeach; ?>
                  <tr>
                    <td colspan="7"><b>Total Sisa Hutang</b></td>
                    <td colspan="2"><b>Rp. <?= number_format($totalSisaHutang, 0, ',', '.'); ?></b></td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="printDetail()">Cetak</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

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
    // Inisialisasi DataTable untuk tabel rekap sisa hutang
    $("#rekap-sisa-hutang").DataTable({
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
    }).buttons().container().appendTo('#rekap-sisa-hutang_wrapper .col-md-6:eq(0)');
    
    // Inisialisasi DataTable untuk tabel detail hutang jika ada data
    if ($("#detail-hutang-table tbody tr").length > 1) {
      $("#detail-hutang-table").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 10,
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
      });
    }
  });
  
  // Fungsi untuk mencetak detail hutang
  function printDetail() {
    const cabangNama = "<?= $cabangNama; ?>";
    const printWindow = window.open('', '_blank');
    
    let printContent = `
      <!DOCTYPE html>
      <html>
      <head>
        <title>Detail Sisa Hutang Cabang: ${cabangNama}</title>
        <link rel="stylesheet" href="dist/css/adminlte.min.css">
        <style>
          body { font-family: Arial, sans-serif; }
          .text-center { text-align: center; }
          .mt-4 { margin-top: 1.5rem; }
          table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
          th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
          th { background-color: #f2f2f2; }
          .text-right { text-align: right; }
          @media print {
            .no-print { display: none; }
            .page-break { page-break-after: always; }
          }
        </style>
      </head>
      <body>
        <div class="container">
          <h3 class="text-center">Detail Sisa Hutang Cabang: ${cabangNama}</h3>
          <p class="text-center">Tanggal Cetak: ${new Date().toLocaleDateString('id-ID')}</p>
          
          <table>
            <thead>
              <tr>
                <th>No.</th>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>Jatuh Tempo</th>
                <th>Supplier</th>
                <th>Total Invoice</th>
                <th>Sudah Dibayar</th>
                <th>Sisa Hutang</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
    `;
    
    // Salin data dari tabel modal
    $('#detail-hutang-table tbody tr').each(function() {
      printContent += '<tr>';
      $(this).find('td').each(function() {
        printContent += '<td>' + $(this).html() + '</td>';
      });
      printContent += '</tr>';
    });
    
    printContent += `
            </tbody>
          </table>
          
          <div class="mt-4 no-print">
            <button onclick="window.print()">Cetak</button>
            <button onclick="window.close()">Tutup</button>
          </div>
        </div>
        
        <script>
          // Otomatis membuka dialog cetak
          window.onload = function() {
            window.print();
          }
        </script>
      </body>
      </html>
    `;
    
    printWindow.document.open();
    printWindow.document.write(printContent);
    printWindow.document.close();
  }
</script>
</body>
</html>