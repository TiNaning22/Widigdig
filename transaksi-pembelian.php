<?php 
// Define writeLog function only if not already declared
if (!function_exists('writeLog')) {
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
}

include '_header.php';
include '_nav.php';
include '_sidebar.php';

if (!isset($levelLogin)) {
    die("Error: \$levelLogin not defined. Please check your authentication system.");
}

if (!isset($dataTokoLogin)) {
    die("Error: \$dataTokoLogin not defined. Please check your authentication system.");
}

if (!isset($sessionCabang)) {
    die("Error: \$sessionCabang not defined. Please check your session management.");
}

if (!isset($conn)) {
    die("Error: Database connection (\$conn) not defined. Please check your database connection file.");
}

// Check user level
if ($levelLogin === "kurir") {
    echo "<script>document.location.href = 'bo';</script>";
    exit; // Important: stop execution after redirect
}

// Check toko status
if ($dataTokoLogin['toko_status'] < 1) {
    echo "<script>
        alert('Status Toko Tidak Aktif!');
        document.location.href = 'bo';
    </script>";
    exit; // Important: stop execution after redirect
}

// Insert via Barcode
if(isset($_POST["inputbarcode"])){
    if(function_exists('tambahKeranjangPembelianBarcode')) {
        if(tambahKeranjangPembelianBarcode($_POST) > 0) {
            echo "<script>document.location.href = '';</script>";
            exit;
        }
    } else {
        die("Error: Function tambahKeranjangPembelianBarcode not found. Please check your functions file.");
    }
}

// Update QTY
if(isset($_POST["update"])){
    if(function_exists('updateQTYpembelian')) {
        if(updateQTYpembelian($_POST) === 0) {
            echo "<script>alert('Belum Input QTY!');</script>";
        } elseif(updateQTYpembelian($_POST) > 0) {
            echo "<script>document.location.href = '';</script>";
            exit;
        }
    } else {
        die("Error: Function updateQTYpembelian not found. Please check your functions file.");
    }
}

// Finalize Purchase
if(isset($_POST["updateStock"]) && isset($_POST["pembelian_invoice_parent2"])){
    $inv = mysqli_real_escape_string($conn, $_POST["pembelian_invoice_parent2"]);
    $sql = mysqli_query($conn, "SELECT * FROM invoice_pembelian WHERE pembelian_invoice_parent='$inv' AND invoice_pembelian_cabang = '$sessionCabang'");
    
    if(mysqli_num_rows($sql) == 0){
        if(function_exists('updateStockPembelian')) {
            if(updateStockPembelian($_POST) > 0) {
                echo "<script>document.location.href = 'invoice-pembelian?no=".$inv."';</script>";
                exit;
            } else {
                echo "<script>alert('Transaksi Gagal');</script>";
            }
        } else {
            die("Error: Function updateStockPembelian not found. Please check your functions file.");
        }
    } else {
        echo "<script>document.location.href = 'invoice-pembelian?no=".$inv."';</script>";
        exit;
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
                        <?php $r = empty(abs((int)base64_decode($_GET['r'] ?? ''))) ? 0 : abs((int)base64_decode($_GET['r'])); ?>
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
                    <input type="hidden" name="invoice_pembelian_number_user" value="<?= $_SESSION['user_id'] ?? ''; ?>">    
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
                    <button type="submit" class="btn btn-primary" id="invoice_edit">Simpan Perubahan</button>
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
        // Optional: Send to server for logging
        // $.post('log-client.php', {message: message});
    }
    
    // Load keranjang
    $('#transaksi-pembelian-keranjang').load('transaksi-pembelian-keranjang.php?r=<?= $r; ?>', function(response, status, xhr) {
        if (status == "error") {
            console.error('Error loading keranjang:', xhr.status, xhr.statusText);
            $('#transaksi-pembelian-keranjang').html('<div class="alert alert-danger">Error loading cart content. Status: ' + xhr.status + '</div>');
        }
    });
    
    // Initialize DataTable
    if (!$.fn.DataTable.isDataTable('#example1')) {
        var table = $('#example1').DataTable({ 
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "transaksi-pembelian-search-data.php?cabang=<?= $sessionCabang; ?>",
                "error": function(xhr, error, code) {
                    console.error('DataTable AJAX error:', error, xhr);
                }
            },
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
            "destroy": true
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

    // Invoice edit handler
    $(document).on('click', '.invoice-edit-btn', function(e) {
        e.preventDefault();
        var invoiceId = $(this).data('id');
        
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

    // Form edit invoice submit handler
    $('#form-edit-invoice').submit(function(e) {
        e.preventDefault();
        
        // Show loading
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memproses',
                html: 'Sedang menyimpan perubahan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        $.ajax({
            url: 'update-invoice.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }
                
                if(response.status == 'success') {
                    $('#modal-edit-invoice').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Sukses', response.message || 'Invoice berhasil diperbarui', 'success');
                    } else {
                        alert('Invoice berhasil diperbarui');
                    }
                    
                    // Refresh data
                    $('#transaksi-pembelian-keranjang').load('transaksi-pembelian-keranjang.php?r=<?= $r; ?>');
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', response.message || 'Terjadi kesalahan', 'error');
                    } else {
                        alert('Error: ' + (response.message || 'Terjadi kesalahan'));
                    }
                }
            },
            error: function(xhr, status, error) {
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }
                
                console.error("Error:", error, xhr);
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
                } else {
                    alert('Terjadi kesalahan: ' + error);
                }
            }
        });
    });

    // Form tambah invoice submit handler
    $('#form-tambah-invoice').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: "transaksi-pembelian-input-no-invoice.php",
            type: "post",
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.hasil === "sukses") {
                    $('#modal-tambah-invoice').modal('hide');
                    $('#transaksi-pembelian-keranjang').load('transaksi-pembelian-keranjang.php?r=<?= $r; ?>');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Sukses', 'Data Berhasil Disimpan', 'success');
                    } else {
                        alert('Data Berhasil Disimpan');
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', response.error || 'Terjadi kesalahan', 'error');
                    } else {
                        alert('Error: ' + (response.error || 'Terjadi kesalahan'));
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
                } else {
                    alert('Terjadi kesalahan: ' + error);
                }
            }
        });
    });

    // Edit harga handler
    $(document).on('click', '.keranjang-pembelian', function(e) {
        e.preventDefault();
        $("#modal-id-2").modal('show');
        
        $.post('transaksi-pembelian-harga-beli.php', {
            id: $(this).data('id')
        }, function(html) {
            $("#data-keranjang-pembelian").html(html);
        });
    });

    // Form edit harga submit handler
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
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Sukses', 'Data Berhasil diupdate', 'success');
                    } else {
                        alert('Data Berhasil diupdate');
                    }
                    $('#transaksi-pembelian-keranjang').load('transaksi-pembelian-keranjang.php?r=<?= $r; ?>');
                }
            }
        });
    });
});
</script>