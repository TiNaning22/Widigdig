<?php 

function writeLog($message) {
    $logDir = __DIR__ . '/logs/';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    $logFile = $logDir . 'invoice_edit.log';
    $timestamp = date("Y-m-d H:i:s");
    $user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'unknown';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $logMessage = "[{$timestamp}] [USER:{$user}] [IP:{$ip}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

error_reporting(0);
include '_header.php';
include '_nav.php';
include '_sidebar.php';

if ($levelLogin === "kurir") {
    echo "<script>document.location.href = 'bo';</script>";
}

if ($dataTokoLogin['toko_status'] < 1) {
    echo "<script>
        alert('Status Toko Tidak Aktif!');
        document.location.href = 'bo';
    </script>";
}

// Insert via Barcode
if(isset($_POST["inputbarcode"])){
    if(tambahKeranjangPembelianBarcode($_POST) > 0) {
        echo "<script>document.location.href = '';</script>";
    } 
}

// Update QTY
if(isset($_POST["update"])){
    if(updateQTYpembelian($_POST) === 0) {
        echo "<script>alert('Belum Input QTY!');</script>";
    } elseif(updateQTYpembelian($_POST) > 0) {
        echo "<script>document.location.href = '';</script>";
    }
}

// Finalize Purchase
$inv = $_POST["pembelian_invoice_parent2"];
if(isset($_POST["updateStock"])){
    $sql = mysqli_query($conn, "SELECT * FROM invoice_pembelian WHERE pembelian_invoice_parent='$inv' AND invoice_pembelian_cabang = '$sessionCabang'");
    
    if(mysqli_num_rows($sql) == 0){
        if(updateStockPembelian($_POST) > 0) {
            echo "<script>document.location.href = 'invoice-pembelian?no=".$inv."';</script>";
        } else {
            echo "<script>alert('Transaksi Gagal');</script>";
        }
    } else {
        echo "<script>document.location.href = 'invoice-pembelian?no=".$inv."';</script>";
    }
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Transaksi Pembelian Produk</h1>
                    <div class="btn-cash-piutang">
                        <?php $r = empty(abs((int)base64_decode($_GET['r']))) ? 0 : abs((int)base64_decode($_GET['r'])); ?>
                        <?php if ($r == 1) : ?>
                            <a href="transaksi-pembelian" class="btn btn-default">Cash</a>
                            <a href="transaksi-pembelian?r=MQ==" class="btn btn-primary">Hutang</a>
                        <?php else : ?>
                            <a href="transaksi-pembelian" class="btn btn-primary">Cash</a>
                            <a href="transaksi-pembelian?r=MQ==" class="btn btn-default">Hutang</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="col-lg-12">
            <span id="transaksi-pembelian-keranjang"></span>
        </div>
    </section>
</div>

<!-- Modal Produk -->
<div class="modal fade" id="modal-id" data-backdrop="static">
    <div class="modal-dialog modal-lg-pop-up">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Barang</h3>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="card-body">
                        <div class="table-auto">
                            <table id="example1" class="table table-bordered table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">No.</th>
                                        <th>Kode Barang</th>
                                        <th>Nama</th>
                                        <th>Satuan</th>
                                        <th>Stock</th>
                                        <th style="text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Invoice -->
<div id="modal-tambah-invoice" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" id="form-tambah-invoice" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Input No. Invoice</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>No. Invoice</label>
                        <input type="text" class="form-control" name="invoice_pembelian_number_input" required>
                    </div>  
                    <input type="hidden" name="invoice_pembelian_number_parent" value="<?= date("Ymd").(mysqli_num_rows(mysqli_query($conn,"SELECT * FROM invoice_pembelian")) + 1); ?>">    
                    <input type="hidden" name="invoice_pembelian_number_user" value="<?= $_SESSION['user_id']; ?>">    
                    <input type="hidden" name="invoice_pembelian_cabang" value="<?= $sessionCabang; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>   
        </div>
    </div>
</div>

<div class="modal fade" id="modal-edit-invoice">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-edit-invoice" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Invoice</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="data-edit-invoice">
                    <!-- Data akan diisi via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="invoice_edit" data-id="<?= $invoice['id']; ?>">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Harga -->
<div class="modal fade" id="modal-id-2">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" id="form-edit-harga-beli" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Harga Pembelian</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="data-keranjang-pembelian"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Edit Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '_footer.php'; ?>

<script>
$(document).ready(function(){
    function writeLog(message) {
        if (typeof console !== 'undefined') {
            console.log('[CLIENT] ' + message);
        }
        // Bisa juga dikirim ke server untuk logging terpusat
        $.post('log-client.php', {message: message});
    }
    // Load keranjang
    $(document).ready(function(){
    // Load keranjang
    $('#transaksi-pembelian-keranjang').load('transaksi-pembelian-keranjang.php?r=<?= $r; ?>');
    
    // Cek apakah DataTable sudah diinisialisasi
    if (!$.fn.DataTable.isDataTable('#example1')) {
          var table = $('#example1').DataTable({ 
              "processing": true,
              "serverSide": true,
              "ajax": "transaksi-pembelian-search-data.php?cabang=<?= $sessionCabang; ?>",
              "columnDefs": [
                  {
                      "targets": 3,
                      "render": $.fn.dataTable.render.number('.', '', '', 'Rp. ')
                  },
                  {
                      "targets": -1,
                      "data": null,
                      "defaultContent": `<center><button class='btn btn-primary tblInsert' title="Tambah"><i class="fa fa-shopping-cart"></i> Pilih</button></center>` 
                  }
              ],
              "destroy": true // Tambahkan ini untuk memungkinkan re-inisialisasi
          });

          table.on('draw.dt', function () {
              var info = table.page.info();
              table.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
                  cell.innerHTML = i + 1 + info.start;
              });
          });

          $('#example1 tbody').on('click', '.tblInsert', function () {
              var data = table.row($(this).parents('tr')).data();
              window.location.href = "transaksi-pembelian-add?id="+ btoa(data[0]) + "&r=<?= $r; ?>";
          });
      }
  });

  $(document).on('click', '.invoice-edit-btn', function(e) {
        e.preventDefault();
        var invoiceId = $(this).data('id');
        console.log('Invoice ID:', invoiceId);

        if (!invoiceId) {
            alert('Invoice ID tidak ditemukan');
            return;
        }

        $.ajax({
            url: 'get-invoice-data.php',
            type: 'POST',
            data: {id: invoiceId},
            success: function(response) {
                $('#data-edit-invoice').html(response);
                $('#modal-edit-invoice').modal('show');
            },
            error: function(xhr) {
                alert('Gagal memuat data invoice');
                console.log('error:', xhr);
            }
        });
    });

  // Handle submit form edit
  $('#form-edit-invoice').submit(function(e) {
      e.preventDefault();
      
      // Tampilkan loading
      Swal.fire({
          title: 'Memproses',
          html: 'Sedang menyimpan perubahan...',
          allowOutsideClick: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });
      
      $.ajax({
          url: 'update-invoice.php',
          type: 'POST',
          data: $(this).serialize(),
          dataType: 'json', // Pastikan ini ada
          success: function(response) {
              Swal.close();
              
              // Tidak perlu parse karena sudah JSON (dataType: 'json')
              if(response.status == 'success') {
                  $('#modal-edit-invoice').modal('hide');
                  Swal.fire('Sukses', response.message || 'Invoice berhasil diperbarui', 'success');
                  
                  // Refresh data
                  $('#transaksi-pembelian-keranjang').load('transaksi-pembelian-keranjang.php?r=<?= $r; ?>');
              } else {
                  Swal.fire('Error', response.message || 'Terjadi kesalahan', 'error');
              }
          },
          error: function(xhr, status, error) {
              Swal.close();
              
              console.error("Error:", error);
              console.log("Response:", xhr);
              
              try {
                  // Coba parse error response jika mungkin
                  var errResponse = JSON.parse(xhr);
                  Swal.fire('Error', errResponse.message || 'Terjadi kesalahan saat update', 'error');
              } catch(e) {
                  // Jika tidak bisa parse, tampilkan error umum
                  Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
              }
          }
      });
  });

  $('#form-tambah-invoice').submit(function(e){
    e.preventDefault();
      $.ajax({
          url: "transaksi-pembelian-input-no-invoice.php",
          type: "post",
          data: $(this).serialize(),
          dataType: 'json', // Pastikan ini ada
          success: function(response) {
              // Tidak perlu parse karena sudah JSON
              if (response.hasil === "sukses") {
                  $('#modal-tambah-invoice').modal('hide');
                  $('#transaksi-pembelian-keranjang').load('transaksi-pembelian-keranjang.php?r=<?= $r; ?>');
                  Swal.fire('Sukses', 'Data Berhasil Disimpan', 'success');
              } else {
                  Swal.fire('Error', response.error || 'Terjadi kesalahan', 'error');
              }
          },
          error: function(xhr, status, error) {
              console.error(xhr.responseText);
              try {
                  // Coba parse error response
                  var errResponse = JSON.parse(xhr.responseText);
                  Swal.fire('Error', errResponse.message || 'Terjadi kesalahan', 'error');
              } catch(e) {
                  // Jika tidak bisa parse, tampilkan raw error
                  Swal.fire('Error', 'Terjadi kesalahan: ' + xhr.statusText, 'error');
              }
          }
      });
  });

    table.on('draw.dt', function () {
        var info = table.page.info();
        table.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });

    $('#example1 tbody').on('click', '.tblInsert', function () {
        var data = table.row($(this).parents('tr')).data();
        window.location.href = "transaksi-pembelian-add?id="+ btoa(data[0]) + "&r=<?= $r; ?>";
    });

    // Form Invoice
    $("#form-edit-invoice").submit(function(e) {
        e.preventDefault();
        
        var dataform = $("#form-edit-invoice").serialize();
        $.ajax({
          url: "transaksi-pembelian-edit-invoice-proses.php",
          data: dataform,
          type: "post",
          success: function(result) {
            var hasil = JSON.parse(result);
            if (hasil.hasil !== "sukses") {
            } else {
              $('#modal-id-3').modal('hide');
              Swal.fire(
                'Sukses !!',
                'Data Berhasil diupdate',
                'success'
              );
              $('#transaksi-pembelian-keranjang').load('transaksi-pembelian-keranjang.php?r=<?= $r; ?>');
            }
          }
        });
      });

    // Edit Harga
    $(document).on('click', '.keranjang-pembelian', function(e) { // Ganti selector ke class
        e.preventDefault();
        $("#modal-id-2").modal('show');
        
        $.post('transaksi-pembelian-harga-beli.php', {
            id: $(this).data('id')
        }, function(html) {
            $("#data-keranjang-pembelian").html(html);
        });
    })

    $("#form-edit-harga-beli").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "transaksi-pembelian-harga-beli-proses.php",
            data: $(this).serialize(),
            type: "post",
            success: function(result) {
                var hasil = JSON.parse(result);
                if (hasil.hasil === "sukses") {
                    $('#modal-id-2').modal('hide');
                    Swal.fire('Sukses', 'Data Berhasil diupdate', 'success');
                    $('#transaksi-pembelian-keranjang').load('transaksi-pembelian-keranjang.php?r=<?= $r; ?>');
                }
            }
        });
    });
});
</script>