<?php 
error_reporting(0);
include '_header-artibut.php';
$r = $_GET['r'];

$userId = $_SESSION['user_id'];
$keranjang = query("SELECT * FROM keranjang_pembelian WHERE keranjang_id_kasir = $userId AND keranjang_cabang = $sessionCabang ORDER BY keranjang_id ASC");

// Generate invoice number
$today = date("Ymd");
$di = $today.(mysqli_num_rows(mysqli_query($conn,"SELECT * FROM invoice_pembelian")) + 1);

// Get invoice data
$invoiceNumber = mysqli_query($conn, "SELECT * FROM invoice_pembelian_number WHERE invoice_pembelian_number_parent = '$di' AND invoice_pembelian_number_user = $userId AND invoice_pembelian_cabang = $sessionCabang");
$inParent = mysqli_fetch_array($invoiceNumber);
$inId = $inParent['invoice_pembelian_number_id'] ?? 0;
$in = $inParent['invoice_pembelian_number_input'] ?? null;
$inDelete = $inParent['invoice_pembelian_number_delete'] ?? $di;
?>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-8 col-lg-8">
                <div class="card-invoice">
                    <span>No. Invoice: </span>
                    <input type="text" value="<?= $in ?? 'Belum diisi'; ?>" readonly style="border: 1px solid #eaeaea;">

                    <?php if ($in == null) : ?>
                        <span data-toggle="modal" data-target="#modal-tambah-invoice">
                            <i class="fa fa-pencil" style="color: green; cursor: pointer;"></i>
                        </span>
                    <?php else : ?>
                        <!-- FIX: Gunakan $inId yang sudah terdefinisi -->
                        <span class="invoice-edit-btn" data-id="<?= $inId; ?>">
                            <i class="fa fa-edit" style="color: blue; cursor: pointer;"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <div class="cari-barang-parent">
                    <div class="row">
                        <div class="col-10">
                            <form action="" method="post">
                                <input type="hidden" name="keranjang_id_kasir" value="<?= $_SESSION['user_id']; ?>">
                                <input type="hidden" name="keranjang_cabang" value="<?= $sessionCabang; ?>">
                                <input type="text" class="form-control" autofocus name="inputbarcode" placeholder="Barcode / Kode Barang" required>
                            </form>
                        </div>
                        <div class="col-2">
                            <a class="btn btn-primary" title="Cari Produk" data-toggle="modal" id="cari-barang" href='#modal-id'>
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-auto">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 6%;">No.</th>
                        <th>Nama</th>
                        <th>Satuan</th>
                        <th>Harga Beli</th>
                        <th style="text-align: center;">QTY</th>
                        <th>Diskon (%)</th>
                        <th style="width: 15%;">Sub Total</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1; 
                    $subtotalSebelumDiskon = 0;
                    $totalDiskon = 0;
                    $totalSetelahDiskon = 0;
                    $hargaBeliTotal = 0;
                    ?>
                    
                    <?php foreach($keranjang as $row) : 
                        $bik = $row['barang_id'];
                        $query = "SELECT b.*, s1.satuan_nama as satuan_1, s2.satuan_nama as satuan_2, s3.satuan_nama as satuan_3 
                                 FROM barang b
                                 LEFT JOIN satuan s1 ON b.satuan_id = s1.satuan_id
                                 LEFT JOIN satuan s2 ON b.satuan_id_2 = s2.satuan_id
                                 LEFT JOIN satuan s3 ON b.satuan_id_3 = s3.satuan_id
                                 WHERE b.barang_id = '$bik'";
                        $brg = mysqli_fetch_array(mysqli_query($conn, $query));
                        
                        $subtotal = $row['keranjang_harga'] * $row['keranjang_qty'];
                        $diskonPersen = $row['keranjang_diskon'] ?? 0;
                        $diskonNominal = $subtotal * ($diskonPersen / 100);
                        $subtotalSetelahDiskon = $subtotal - $diskonNominal;
                        
                        if ($row['keranjang_id_kasir'] === $_SESSION['user_id']) {
                            $subtotalSebelumDiskon += $subtotal;
                            $totalDiskon += $diskonNominal;
                            $totalSetelahDiskon += $subtotalSetelahDiskon;
                            $hargaBeliTotal += $brg['barang_harga_beli'] * $row['keranjang_qty'];
                    ?>
                    <tr data-keranjang-id="<?= $row['keranjang_id']; ?>">
                        <td><?= $i; ?></td>
                        <td><?= $row['keranjang_nama']; ?></td>
                        <td>
                            <select name="satuan_pembelian[]" class="form-control satuan-pilihan" data-barangid="<?= $row['barang_id']; ?>">
                                <option value="1" data-konversi="1"><?= $brg['satuan_1'] ?></option>
                                <?php if($brg['satuan_id_2'] > 0): ?>
                                <option value="2" data-konversi="<?= $brg['satuan_isi_1'] ?>">
                                    <?= $brg['satuan_2'] ?> (1 <?= $brg['satuan_2'] ?> = <?= $brg['satuan_isi_1'] ?> <?= $brg['satuan_1'] ?>)
                                </option>
                                <?php endif; ?>
                                <?php if($brg['satuan_id_3'] > 0): ?>
                                <option value="3" data-konversi="<?= $brg['satuan_isi_2'] ?>">
                                    <?= $brg['satuan_3'] ?> (1 <?= $brg['satuan_3'] ?> = <?= $brg['satuan_isi_2'] ?> <?= $brg['satuan_1'] ?>)
                                </option>
                                <?php endif; ?>
                            </select>
                        </td>
                        <td>
                            <span class="harga-text" data-harga="<?= $row['keranjang_harga']; ?>" data-harga-beli="<?= $brg['barang_harga_beli']; ?>">
                                Rp. <?= number_format($row['keranjang_harga'], 0, ',', '.'); ?>
                            </span>
                            <span class="keranjang-right">
                                <button class="btn-success edit-harga-beli" data-id="<?= $row['keranjang_id']; ?>">
                                    <i class="fa fa-edit"></i>
                                </button>    
                            </span>
                        </td>
                        <td style="text-align: center; width: 11%;">
                            <form role="form" action="" method="post">
                                <input type="hidden" name="keranjang_id" value="<?= $row['keranjang_id']; ?>">
                                <input type="number" min="1" name="keranjang_qty" value="<?= $row['keranjang_qty'] ?>" 
                                       class="qty-input" onkeypress="return hanyaAngka(event)" style="text-align: center; width: 60%;"> 
                                <input type="hidden" name="stock_brg" value="<?= $brg['barang_stock']; ?>">
                                <button class="btn-primary" type="submit" name="update">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <input type="number" name="diskon_item" class="form-control diskon-input" 
                                   value="<?= $diskonPersen; ?>" placeholder="0" min="0" max="100" step="0.01"
                                   data-keranjang-id="<?= $row['keranjang_id']; ?>">
                        </td>
                        <td class="subtotal">Rp. <?= number_format($subtotalSetelahDiskon, 0, ',', '.'); ?></td>
                        <td style="text-align: center; width: 6%;">
                            <a href="transaksi-pembelian-delete?id=<?= $row['keranjang_id']; ?>&r=<?= $r; ?>" title="Delete" onclick="return confirm('Yakin dihapus?')">
                                <button class="btn btn-danger" type="submit" name="hapus">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </a>
                        </td>
                    </tr>
                    <?php $i++; ?>
                    <?php } ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="btn-transaksi">
            <form role="form" action="" method="POST" id="form-transaksi">
                <div class="row">
                    <div class="col-md-6 col-lg-7">
                        <div class="filter-customer">
                            <div class="form-group">
                                <label>Supplier</label>
                                <select class="form-control select2bs4" required name="invoice_supplier">
                                    <option value="">-- Pilih Supplier --</option>
                                    <?php  
                                    $supplier = query("SELECT * FROM supplier WHERE supplier_cabang = $sessionCabang AND supplier_status = '1' ORDER BY supplier_id DESC");
                                    foreach ($supplier as $ctr) : ?>
                                        <option value="<?= $ctr['supplier_id'] ?>">
                                            <?= $ctr['supplier_nama']; ?> - <?= $ctr['supplier_company']; ?>    
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small><a href="supplier-add">Tambah Supplier <i class="fa fa-plus"></i></a></small>
                            </div>

                            <div class="form-group">
                                <label>No. Referensi</label>
                                <input type="text" name="no_referensi" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>No. Faktur</label>
                                <input type="text" name="no_faktur" class="form-control">
                            </div>

                            <?php if ($r == 1) : ?>
                            <div class="form-group">
                                <label>Jatuh Tempo</label>
                                <input type="date" name="invoice_hutang_jatuh_tempo" class="form-control" required>
                            </div>
                            <?php else : ?>
                                <input type="hidden" name="invoice_hutang_jatuh_tempo" value="0">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-5">
                        <div class="invoice-table">
                            <table class="table">
                                <tr>
                                    <td><b>Subtotal</b></td>
                                    <td class="table-nominal" id="subtotal-display">
                                        Rp. <?= number_format($subtotalSebelumDiskon, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Diskon</b></td>
                                    <td class="table-nominal" id="diskon-display">
                                        Rp. <?= number_format($totalDiskon, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Setelah Diskon</b></td>
                                    <td class="table-nominal" id="setelah-diskon-display">
                                        Rp. <?= number_format($totalSetelahDiskon, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>PPN (11%)</b></td>
                                    <td class="table-nominal" id="ppn-display">
                                        Rp. <?= number_format($totalSetelahDiskon * 0.11, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>TOTAL</b></td>
                                    <td class="table-nominal" id="total-display">
                                        Rp. <?= number_format($totalSetelahDiskon * 1.11, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Margin Kotor</b></td>
                                    <td class="table-nominal" id="margin-display">
                                        Rp. <?= number_format(($totalSetelahDiskon - $hargaBeliTotal), 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Margin (%)</b></td>
                                    <td class="table-nominal" id="margin-persen-display">
                                        <?php 
                                        $marginPersen = ($hargaBeliTotal > 0) ? (($totalSetelahDiskon - $hargaBeliTotal) / $hargaBeliTotal) * 100 : 0;
                                        echo number_format($marginPersen, 2, ',', '.') . '%';
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b style="color: red;">
                                            <?= ($r == 1) ? "DP" : "Bayar"; ?>      
                                        </b>
                                    </td>
                                    <td class="table-nominal tn">
                                        <span>Rp.</span> 
                                        <span>
                                            <input type="text" name="angka1" id="angka1" class="a2" autocomplete="off" onkeyup="hitung2();" required onkeypress="return hanyaAngka1(event)" size="10">
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= ($r == 1) ? "Sisa Hutang" : "Kembali"; ?>  
                                    </td>
                                    <td class="table-nominal">
                                        <span>Rp.</span>
                                        <span>
                                            <input type="text" name="hasil" id="hasil" class="c2" readonly size="10" disabled>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <!-- Hidden inputs untuk data transaksi -->
                                        <?php foreach ($keranjang as $stk) : 
                                            if ($stk['keranjang_id_kasir'] === $_SESSION['user_id']) {
                                        ?>
                                            <input type="hidden" name="barang_ids[]" value="<?= $stk['barang_id']; ?>">
                                            <input type="hidden" name="keranjang_qty[]" value="<?= $stk['keranjang_qty'] ?>"> 
                                            <input type="hidden" name="keranjang_id_kasir[]" value="<?= $_SESSION['user_id']; ?>">
                                            <input type="hidden" name="pembelian_invoice[]" value="<?= $in; ?>">
                                            <input type="hidden" name="pembelian_invoice_parent[]" value="<?= $inDelete; ?>">
                                            <input type="hidden" name="pembelian_date[]" value="<?= date("Y-m-d") ?>">
                                            <input type="hidden" name="barang_harga_beli[]" value="<?= $stk['keranjang_harga']; ?>">
                                            <input type="hidden" name="pembelian_cabang[]" value="<?= $sessionCabang; ?>">
                                            <input type="hidden" name="diskon_item_value[]" value="<?= $stk['keranjang_diskon'] ?? 0; ?>" class="diskon-hidden">
                                        <?php } ?>
                                        <?php endforeach; ?>  
                                        
                                        <!-- Data invoice -->
                                        <input type="hidden" name="kik" value="<?= $_SESSION['user_id']; ?>">
                                        <input type="hidden" name="pembelian_invoice2" value="<?= $in; ?>">
                                        <input type="hidden" name="invoice_pembelian_number_delete" value="<?= $inDelete; ?>">
                                        <input type="hidden" name="pembelian_invoice_parent2" value="<?= $inDelete; ?>">
                                        <input type="hidden" name="invoice_hutang" value="<?= $r; ?>">
                                        <input type="hidden" name="invoice_hutang_lunas" value="0">
                                        <input type="hidden" name="invoice_pembelian_cabang" value="<?= $sessionCabang; ?>">
                                        <input type="hidden" name="ppn_value" value="11">
                                        
                                        <!-- Total calculations -->
                                        <input type="hidden" name="subtotal_sebelum_diskon" id="subtotal-hidden" value="<?= $subtotalSebelumDiskon; ?>">
                                        <input type="hidden" name="total_diskon" id="total-diskon-hidden" value="<?= $totalDiskon; ?>">
                                        <input type="hidden" name="total_setelah_diskon" id="total-setelah-diskon-hidden" value="<?= $totalSetelahDiskon; ?>">
                                        <input type="hidden" name="total_ppn" id="total-ppn-hidden" value="<?= $totalSetelahDiskon * 0.11; ?>">
                                        <input type="hidden" name="grand_total" id="grand-total-hidden" value="<?= $totalSetelahDiskon * 1.11; ?>">
                                        
                                        <!-- Margin calculations -->
                                        <input type="hidden" name="margin_kotor" id="margin-kotor-hidden" value="<?= $totalSetelahDiskon - $hargaBeliTotal; ?>">
                                        <input type="hidden" name="margin_persen" id="margin-persen-hidden" value="<?= $marginPersen; ?>">
                                        <input type="hidden" name="harga_beli_total" id="harga-beli-total-hidden" value="<?= $hargaBeliTotal; ?>">
                                        
                                        <div class="payment">
                                            <?php  
                                            $idKasir = $_SESSION['user_id'];
                                            $keranjang = mysqli_query($conn,"SELECT keranjang_harga FROM keranjang_pembelian WHERE keranjang_harga < 1 AND keranjang_id_kasir = $idKasir AND keranjang_cabang = $sessionCabang");
                                            $jmlKeranjang = mysqli_num_rows($keranjang);
                                            ?>

                                            <?php if ($in != null) : ?>
                                                <?php if ($jmlKeranjang < 1) : ?>
                                                    <button class="btn btn-primary" type="submit" name="updateStock">Simpan Payment <i class="fa fa-shopping-cart"></i></button>
                                                <?php else : ?>
                                                    <a class="btn btn-default btn-disabled">Simpan Payment <i class="fa fa-shopping-cart"></i></a>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <a class="btn btn-default" disabled>Simpan Payment <i class="fa fa-shopping-cart"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function () {
    $('.select2bs4').select2({ theme: 'bootstrap4' });
    
    $(document).on('click', '.btn-disabled', function(){
        alert("Harga Beli Masih ada yang bernilai kosong (Rp.0) !! Segera Update Harga Pembelian Barang per Produk ..");
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

    // Event handler untuk edit harga beli
    $(document).on('click', '.edit-harga-beli', function(e) {
        e.preventDefault();
        $("#modal-id-2").modal('show');
        
        $.post('transaksi-pembelian-harga-beli.php', {
            id: $(this).data('id')
        }, function(html) {
            $("#data-keranjang-pembelian").html(html);
        });
    });

    // Handle submit form edit invoice
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
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if(response.status == 'success') {
                    $('#modal-edit-invoice').modal('hide');
                    Swal.fire('Sukses', response.message || 'Invoice berhasil diperbarui', 'success');
                    location.reload();
                } else {
                    Swal.fire('Error', response.message || 'Terjadi kesalahan', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                
                console.error("Error:", error);
                console.log("Response:", xhr.responseText);
                
                try {
                    var errResponse = JSON.parse(xhr.responseText);
                    Swal.fire('Error', errResponse.message || 'Terjadi kesalahan saat update', 'error');
                } catch(e) {
                    Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
                }
            }
        });
    });

    // Handle submit form edit harga beli
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
                    location.reload();
                }
            }
        });
    });

    // Fungsi untuk menghitung dan update subtotal per item
    function hitungSubtotalItem(row) {
        var qty = parseFloat(row.find('.qty-input').val()) || 0;
        var harga = parseFloat(row.find('.harga-text').data('harga')) || 0;
        var diskonPersen = parseFloat(row.find('.diskon-input').val()) || 0;
        
        var subtotal = qty * harga;
        var diskonNominal = subtotal * (diskonPersen / 100);
        var subtotalSetelahDiskon = subtotal - diskonNominal;
        
        row.find('.subtotal').text('Rp. ' + subtotalSetelahDiskon.toLocaleString('id-ID'));
        
        // Update hidden input untuk diskon
        var keranjangId = row.find('.diskon-input').data('keranjang-id');
        $('input[name="diskon_item_value[]"]').each(function(index) {
            if ($(this).closest('tr').length === 0) { // Hidden input tidak dalam tr
                var currentIndex = $('input[name="diskon_item_value[]"]').index(this);
                var targetRow = $('tr[data-keranjang-id]').eq(currentIndex);
                if (targetRow.data('keranjang-id') == keranjangId) {
                    $(this).val(diskonPersen);
                }
            }
        });
        
        hitungTotalKeseluruhan();
    }

    // Fungsi untuk menghitung total keseluruhan
    function hitungTotalKeseluruhan() {
        var subtotalSebelumDiskon = 0;
        var totalDiskon = 0;
        var totalSetelahDiskon = 0;
        var hargaBeliTotal = 0;
        
        $('tbody tr').each(function() {
            var qty = parseFloat($(this).find('.qty-input').val()) || 0;
            var harga = parseFloat($(this).find('.harga-text').data('harga')) || 0;
            var diskonPersen = parseFloat($(this).find('.diskon-input').val()) || 0;
            var hargaBeli = parseFloat($(this).find('.harga-text').data('harga-beli')) || 0;
            
            var subtotal = qty * harga;
            var diskonNominal = subtotal * (diskonPersen / 100);
            var subtotalItem = subtotal - diskonNominal;
            
            subtotalSebelumDiskon += subtotal;
            totalDiskon += diskonNominal;
            totalSetelahDiskon += subtotalItem;
            hargaBeliTotal += (qty * hargaBeli);
        });
        
        var totalPPN = totalSetelahDiskon * 0.11;
        var grandTotal = totalSetelahDiskon + totalPPN;
        var marginKotor = totalSetelahDiskon - hargaBeliTotal;
        var marginPersen = (hargaBeliTotal > 0) ? (marginKotor / hargaBeliTotal) * 100 : 0;
        
        // Update tampilan
        $('#subtotal-display').text('Rp. ' + subtotalSebelumDiskon.toLocaleString('id-ID'));
        $('#diskon-display').text('Rp. ' + totalDiskon.toLocaleString('id-ID'));
        $('#setelah-diskon-display').text('Rp. ' + totalSetelahDiskon.toLocaleString('id-ID'));
        $('#ppn-display').text('Rp. ' + totalPPN.toLocaleString('id-ID'));
        $('#total-display').text('Rp. ' + grandTotal.toLocaleString('id-ID'));
        $('#margin-display').text('Rp. ' + marginKotor.toLocaleString('id-ID'));
        $('#margin-persen-display').text(marginPersen.toFixed(2) + '%');
        
        // Update hidden inputs
        $('#subtotal-hidden').val(subtotalSebelumDiskon);
        $('#total-diskon-hidden').val(totalDiskon);
        $('#total-setelah-diskon-hidden').val(totalSetelahDiskon);
        $('#total-ppn-hidden').val(totalPPN);
        $('#grand-total-hidden').val(grandTotal);
        $('#margin-kotor-hidden').val(marginKotor);
        $('#margin-persen-hidden').val(marginPersen);
        $('#harga-beli-total-hidden').val(hargaBeliTotal);
        
        // Update nilai b2 untuk perhitungan kembalian
        $('.b2').val(grandTotal);
        hitung2(); // Recalculate kembalian
    }

    // Event listener untuk perubahan diskon
    $(document).on('input change', '.diskon-input', function() {
        var row = $(this).closest('tr');
        var keranjangId = $(this).data('keranjang-id');
        var diskonValue = $(this).val();
        
        // Update diskon ke database via AJAX
        $.ajax({
            url: 'update-diskon-keranjang.php',
            type: 'POST',
            data: {
                keranjang_id: keranjangId,
                diskon: diskonValue
            },
            success: function(response) {
                console.log('Diskon updated:', response);
            }
        });
        
        hitungSubtotalItem(row);
    });

    // Event listener untuk perubahan qty
    $(document).on('input change', '.qty-input', function() {
        hitungSubtotalItem($(this).closest('tr'));
    });

    // Hitung total awal saat halaman dimuat
    hitungTotalKeseluruhan();
    
    // Set nilai awal untuk b2 (total yang harus dibayar)
    var grandTotal = parseFloat($('#grand-total-hidden').val()) || 0;
    $('.b2').val(grandTotal);
});

function hanyaAngka(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

function hanyaAngka1(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;
    return true;
}

function hitung2() {
    var bayar = parseFloat($(".a2").val().replace(/\./g, '')) || 0;
    var total = parseFloat($('#grand-total-hidden').val()) || 0;
    var kembalian = bayar - total;
    
    if (kembalian < 0) {
        kembalian = Math.abs(kembalian); // Untuk hutang, tampilkan nilai positif
    }
    
    $(".c2").val(kembalian.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
}
</script>