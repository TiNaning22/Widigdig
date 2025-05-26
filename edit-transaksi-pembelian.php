<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "kasir") {
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
          <h1>Data Transaksi Pembelian yang diedit (retur)</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Retur Pembelian</li>
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
            <h3 class="card-title">Laporan Edit Transaksi Pembelian</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="laporan-edit-transaksi-pembelian" class="table table-bordered table-striped table-laporan">
                <thead>
                <tr>
                  <th style="width: 6%;">No.</th>
                  <th style="width: 13%;">Invoice</th>
                  <th>Tanggal Pembelian</th>
                  <th>Tanggal Edit</th>
                  <th>Supplier</th>
                  <th>Kasir yang Edit</th>
                  <th>Total</th>
                  <th style="width: 11%;">Aksi</th>
                </tr>
                </thead>
                <tbody>

                <?php 
                  $i = 1; 
                  $total = 0;
                  
                  $queryInvoice = $conn->query("SELECT 
                      invoice_pembelian.invoice_pembelian_id,
                      invoice_pembelian.pembelian_invoice, 
                      invoice_pembelian.invoice_tgl, 
                      supplier.supplier_id, 
                      supplier.supplier_nama, 
                      supplier.supplier_company, 
                      invoice_pembelian.invoice_total, 
                      invoice_pembelian.invoice_date_edit, 
                      user.user_id, 
                      user.user_nama, 
                      user.user_no_hp, 
                      invoice_pembelian.invoice_pembelian_cabang
                    FROM invoice_pembelian 
                    JOIN supplier ON invoice_pembelian.invoice_supplier = supplier.supplier_id
                    JOIN user ON invoice_pembelian.invoice_kasir_edit = user.user_id
                    WHERE invoice_pembelian_cabang = '".$sessionCabang."'
                    ORDER BY invoice_pembelian_id DESC");
                  
                  if(mysqli_num_rows($queryInvoice) > 0) {
                    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                      $total += $rowProduct['invoice_total'];
                  ?>
                  <tr>
                    <td><?= $i; ?></td>
                    <td><?= $rowProduct['pembelian_invoice']; ?></td>
                    <td><?= $rowProduct['invoice_tgl']; ?></td>
                    <td><?= $rowProduct['invoice_date_edit']; ?></td>
                    <td>
                      <?= $rowProduct['supplier_nama']; ?> - 
                      <?= $rowProduct['supplier_company']; ?> 
                    </td>
                    <td><?= $rowProduct['user_nama']; ?></td>
                    <td>Rp. <?= number_format($rowProduct['invoice_total'], 0, ',', '.'); ?></td>
                    <td>
                      <?php  
                        $no_wa = substr_replace($rowProduct['user_no_hp'],'62',0,1)
                      ?>
                      <a href="https://api.whatsapp.com/send?phone=<?= $no_wa; ?>&text=Transaksi dengan No. Invoice Pembelian <?= $rowProduct['pembelian_invoice']; ?> atas Supplier: <?= $rowProduct['supplier_nama']; ?> - <?= $rowProduct['supplier_company']; ?>  Terjadi perubahan Data. Kasir yang merubah dengan nama <?= $rowProduct['user_nama']; ?>. Bisa dijelaskan kenapa terjadi perubahan transaksi ?" title="Tanya Kasir" target="_blank">
                        <button class="btn btn-success" type="submit">
                           <i class="fa fa-whatsapp"></i>
                        </button>
                      </a>
                      <a href="edit-transaksi-detail-pembelian?no=<?= $rowProduct['invoice_pembelian_id']; ?>-invoice-<?= $rowProduct['pembelian_invoice']; ?>" title="Lihat Data" target="_blank">
                        <button class="btn btn-primary" type="submit">
                           <i class="fa fa-eye"></i>
                        </button>
                      </a>
                    </td>
                  </tr>
                  <?php 
                      $i++; 
                    }
                  ?>
                  <tr>
                    <td colspan="6">
                      <b>Total</b>
                    </td>
                    <td colspan="2">
                      Rp. <?= number_format($total, 0, ',', '.'); ?>
                    </td>
                  </tr>
                  <?php
                  } else {
                  ?>
                  <tr>
                    <td colspan="8" class="text-center">
                      Tidak ada data
                    </td>
                  </tr>
                  <?php
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
    $("#laporan-edit-transaksi-pembelian").DataTable({
      "responsive": true,
      "autoWidth": false,
      "order": [[0, "asc"]],
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "language": {
        "emptyTable": "Tidak ada data",
        "zeroRecords": "Tidak ada data"
      }
    });
  });
</script>
</body>
</html>