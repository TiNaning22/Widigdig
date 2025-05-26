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
            <h1>Penjualan Baru</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Penjualan Baru</li>
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
              <h3 class="card-title">Form Penjualan Baru</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <form action="tambah-penjualan.php" method="post">
                <div class="row">
                  <!-- Informasi Penjualan -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Tanggal Penjualan</label>
                      <input type="date" name="penjualan_date" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                      <label>No. Invoice</label>
                      <?php
                        // Generate nomor invoice
                        $today = date('Ymd');
                        $query = "SELECT MAX(penjualan_invoice) as invoice FROM penjualan WHERE penjualan_invoice LIKE '{$today}%'";
                        $result = mysqli_query($conn, $query);
                        $data = mysqli_fetch_assoc($result);
                        $lastInvoice = $data['invoice'];
                        
                        $lastNumber = (int) substr($lastInvoice, 8, 4);
                        $nextNumber = $lastNumber + 1;
                        $nextInvoice = $today . sprintf('%04s', $nextNumber);
                      ?>
                      <input type="text" name="penjualan_invoice" class="form-control" value="<?= $nextInvoice; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label>Pilih Pelanggan</label>
                      <select name="penjualan_pelanggan" class="form-control select2" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php
                          // Changed from pelanggan to customer table as per the file you shared
                          $customers = query("SELECT * FROM customer WHERE customer_cabang = $sessionCabang ORDER BY customer_nama ASC");
                          foreach($customers as $row):
                            if ($row['customer_id'] > 1) { // Skip the first customer if it's a system record
                        ?>
                        <option value="<?= $row['customer_id']; ?>"><?= $row['customer_nama']; ?> 
                          <?php if(isset($row['customer_hp']) && !empty($row['customer_hp'])): ?> 
                            - <?= $row['customer_hp']; ?>
                          <?php endif; ?>
                          <?php if(isset($row['customer_membership']) && $row['customer_membership'] === "1"): ?> 
                            (Member)
                          <?php endif; ?>
                        </option>
                        <?php 
                            }
                          endforeach; 
                        ?>
                      </select>
                    </div>
                  </div>
                  <!-- Informasi Sales -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Pilih Sales</label>
                      <select name="penjualan_sales_id" class="form-control select2">
                          <option value="">-- Pilih Sales --</option>
                          <?php
                          $query_sales = "SELECT * FROM sales WHERE sales_cabang = $sessionCabang AND sales_status = 1 ORDER BY sales_nama ASC";
                          $result_sales = mysqli_query($conn, $query_sales);
                          if ($result_sales && mysqli_num_rows($result_sales) > 0) {
                              while ($row = mysqli_fetch_assoc($result_sales)) {
                          ?>
                                  <option value="<?= $row['sales_id']; ?>">
                                      <?= $row['sales_nama']; ?>
                                  </option>
                          <?php 
                              }
                          }
                          ?>
                      </select>
                    </div>

                    <div class="form-group">
                      <label>Jenis Pembayaran</label>
                      <select name="penjualan_tipe_pembayaran" class="form-control" required>
                        <option value="">-- Pilih Jenis Pembayaran --</option>
                        <option value="cash">Cash</option>
                        <option value="kredit">Kredit</option>
                        <option value="transfer">Transfer</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Keterangan</label>
                      <textarea name="penjualan_keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan..."></textarea>
                    </div>
                  </div>
                </div>

                <hr>
                <h4>Pilih Barang</h4>
                
                <div class="table-responsive">
                  <table class="table table-bordered" id="product_table">
                    <thead>
                      <tr>
                        <th>Produk</th>
                        <th width="150px">Harga</th>
                        <th width="100px">Jumlah</th>
                        <th width="150px">Total</th>
                        <th width="50px">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr id="row-1">
                        <td>
                          <select name="barang_id[]" class="form-control select-barang" required>
                            <option value="">-- Pilih Barang --</option>
                            <!-- Options will be populated via AJAX -->
                          </select>
                          <!-- Product info will be displayed here -->
                        </td>
                        <td>
                          <input type="number" name="barang_harga[]" class="form-control barang-harga" readonly>
                        </td>
                        <td>
                          <input type="number" name="barang_qty[]" class="form-control barang-qty" min="1" value="1" required>
                        </td>
                        <td>
                          <input type="number" name="barang_total[]" class="form-control barang-total" readonly>
                        </td>
                        <td>
                          <button type="button" class="btn btn-danger btn-sm btn-hapus" disabled>
                            <i class="fa fa-trash"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                    
                  </table>
                </div>
                
                <div class="row mt-4">
                  <div class="col-md-8">
                    <!-- Space for additional fields -->
                  </div>
                  <div class="col-md-4">
                    <div class="form-group row">
                      <label class="col-sm-4 col-form-label">Total</label>
                      <div class="col-sm-8">
                        <input type="number" name="penjualan_total" id="grand-total" class="form-control" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-sm-4 col-form-label">Bayar</label>
                      <div class="col-sm-8">
                        <input type="number" name="penjualan_bayar" id="pembayaran" class="form-control" required>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-sm-4 col-form-label">Kembali</label>
                      <div class="col-sm-8">
                        <input type="number" name="penjualan_kembali" id="kembalian" class="form-control" readonly>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="text-right mt-4">
                  <a href="penjualan" class="btn btn-secondary">Batal</a>
                 <button type="submit" name="submit" class="btn btn-primary">Simpan Penjualan</button>
                </div>
              </form>
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

<!-- Select2 -->
<script src="plugins/select2/js/select2.full.min.js"></script>
<!-- Custom JS for this page -->
<script>
$(function () {
  // Initialize row counter
  let rowCount = 1;
  
  // Initialize select2
  $('.select2').select2();
  
  // Handle barang selection
  $(document).on('change', '.select-barang', function() {
    const row = $(this).closest('tr');
    const selectedOption = $(this).find(':selected');
    const harga = selectedOption.data('harga') || 0;
    const qty = parseInt(row.find('.barang-qty').val()) || 1;
    
    row.find('.barang-harga').val(harga);
    row.find('.barang-total').val(harga * qty);
    
    // Show additional product info if selected
    if (selectedOption.val()) {
      const kode = selectedOption.data('kode');
      const stok = selectedOption.data('stok');
      const kategori = selectedOption.data('kategori');
      
      // You can show this information in a tooltip or in a small info box
      const infoHtml = `
        <div class="product-info mt-1 small">
          <span class="badge badge-info">Kode: ${kode}</span>
          <span class="badge badge-success">Stok: ${stok}</span>
          <span class="badge badge-primary">Kategori: ${kategori}</span>
        </div>
      `;
      
      // Remove any existing info
      row.find('.product-info').remove();
      
      // Add the new info after the select
      $(this).after(infoHtml);
    } else {
      row.find('.product-info').remove();
    }
    
    hitungTotal();
  });
  
  // Handle quantity change
  $(document).on('input', '.barang-qty', function() {
    const row = $(this).closest('tr');
    const harga = parseFloat(row.find('.barang-harga').val()) || 0;
    const qty = parseInt($(this).val()) || 0;
    
    row.find('.barang-total').val(harga * qty);
    
    hitungTotal();
  });
  
  // Add new row
  $('#tambah-barang').click(function() {
    rowCount++;
    const newRow = $('#row-1').clone();
    
    // Clear values and update IDs
    newRow.attr('id', 'row-' + rowCount);
    newRow.find('.select-barang').val('').select2('destroy');
    newRow.find('.barang-harga').val('');
    newRow.find('.barang-qty').val(1);
    newRow.find('.barang-total').val('');
    newRow.find('.btn-hapus').prop('disabled', false);
    newRow.find('.product-info').remove();
    
    // Add new row to table
    $('#product_table tbody').append(newRow);
    
    // Reinitialize select2 for the new row
    newRow.find('.select-barang').select2();
    
    // Load product data for this new row
    loadProductsForRow(newRow.find('.select-barang'));
  });
  
  // Remove row
  $(document).on('click', '.btn-hapus', function() {
    $(this).closest('tr').remove();
    hitungTotal();
  });
  
  // Calculate payment change
  $(document).on('input', '#pembayaran', function() {
    const total = parseFloat($('#grand-total').val()) || 0;
    const bayar = parseFloat($(this).val()) || 0;
    
    $('#kembalian').val(bayar - total);
  });
  
  // Calculate grand total
  function hitungTotal() {
    let total = 0;
    $('.barang-total').each(function() {
      total += parseFloat($(this).val()) || 0;
    });
    
    $('#grand-total').val(total);
    
    // Recalculate kembalian
    const bayar = parseFloat($('#pembayaran').val()) || 0;
    $('#kembalian').val(bayar - total);
  }
  
  // Function to load product data for dropdown
  function loadProductsForRow(selectElement) {
    $.ajax({
      url: 'get-barang-data.php',
      data: { cabang: <?= $sessionCabang; ?> },
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        let options = '<option value="">-- Pilih Barang --</option>';
        
        $.each(data, function(index, item) {
          options += '<option value="' + item.barang_id + '" ' +
                    'data-kode="' + item.barang_kode + '" ' +
                    'data-harga="' + item.barang_harga + '" ' +
                    'data-stok="' + item.barang_stock + '" ' +
                    'data-kategori="' + item.kategori_nama + '">' +
                    item.barang_nama +
                    '</option>';
        });
        
        selectElement.html(options);
        selectElement.select2();
      },
      error: function(xhr, status, error) {
        console.error('Error loading product data:', error);
        alert('Gagal memuat data produk. Silakan coba lagi.');
      }
    });
  }
  
  // Initial load of product data for first row
  loadProductsForRow($('.select-barang'));
});
</script>
</body>
</html>