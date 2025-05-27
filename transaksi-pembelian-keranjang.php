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
                        <th>Diskon</th>
                        <th style="width: 15%;">Sub Total</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1; 
                    $total = 0;
                    $totalDiskon = 0;
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
                        
                        $sub_total = $row['keranjang_harga'] * $row['keranjang_qty'];
                        $diskon = $row['keranjang_diskon'] ?? 0;
                        $sub_total_setelah_diskon = $sub_total * (1 - ($diskon/100));
                        
                        if ($row['keranjang_id_kasir'] === $_SESSION['user_id']) {
                            $total += $sub_total_setelah_diskon;
                            $totalDiskon += $sub_total - $sub_total_setelah_diskon;
                            $hargaBeliTotal += $row['keranjang_harga_beli'] * $row['keranjang_qty'];
                    ?>
                    <tr>
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
                            Rp. <?= number_format($row['keranjang_harga'], 0, ',', '.'); ?>
                            <span class="keranjang-right">
                                <!-- FIX: Tambahkan class yang benar -->
                                <button class="btn-success edit-harga-beli" 
                                        data-id="<?= $row['keranjang_id']; ?>">
                                    <i class="fa fa-edit"></i>
                                </button>    
                            </span>
                        </td>
                        <td style="text-align: center; width: 11%;">
                            <form role="form" action="" method="post">
                                <input type="hidden" name="keranjang_id" value="<?= $row['keranjang_id']; ?>">
                                <input type="number" min="1" name="keranjang_qty" value="<?= $row['keranjang_qty'] ?>" onkeypress="return hanyaAngka(event)" style="text-align: center; width: 60%;"> 
                                <input type="hidden" name="stock_brg" value="<?= $brg['barang_stock']; ?>">
                                <button class="btn-primary" type="submit" name="update">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <input type="number" name="diskon[]" class="form-control diskon-input" value="<?= $diskon; ?>" placeholder="Diskon (%)">
                        </td>
                        <td>Rp. <?= number_format($sub_total_setelah_diskon, 0, ',', '.'); ?></td>
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

        <!-- Rest of the form content remains the same -->
        <div class="btn-transaksi">
            <form role="form" action="" method="POST">
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
                                    <td class="table-nominal">
                                        Rp. <?= number_format($total, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Diskon</b></td>
                                    <td class="table-nominal">
                                        Rp. <?= number_format($totalDiskon, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>PPN (11%)</b></td>
                                    <td class="table-nominal">
                                        Rp. <?= number_format($total * 0.11, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>TOTAL</b></td>
                                    <td class="table-nominal">
                                        Rp. <?= number_format($total * 1.11, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Margin Kotor</b></td>
                                    <td class="table-nominal">
                                        Rp. <?= number_format(($total - $hargaBeliTotal), 0, ',', '.'); ?>
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
                                        <?php 
                                        foreach ($keranjang as $stk) : 
                                            if ($stk['keranjang_id_kasir'] === $_SESSION['user_id']) {
                                        ?>
                                            <input type="hidden" name="barang_ids[]" value="<?= $stk['barang_id']; ?>">
                                            <input type="hidden" name="keranjang_qty[]" value="<?= $stk['keranjang_qty'] ?>"> 
                                            <input type="hidden" name="keranjang_id_kasir[]" value="<?= $_SESSION['user_id']; ?>">
                                            <input type="hidden" name="kik" value="<?= $_SESSION['user_id']; ?>">
                                            <input type="hidden" name="pembelian_invoice[]" value="<?= $in; ?>">
                                            <input type="hidden" name="pembelian_invoice_parent[]" value="<?= $inDelete; ?>">
                                            <input type="hidden" name="pembelian_date[]" value="<?= date("Y-m-d") ?>">
                                            <input type="hidden" name="barang_harga_beli[]" value="<?= $stk['keranjang_harga']; ?>">
                                            <input type="hidden" name="pembelian_cabang[]" value="<?= $sessionCabang; ?>">
                                            <input type="hidden" name="diskon_value[]" value="<?= $row['keranjang_diskon'] ?? 0; ?>">
                                        <?php } ?>
                                        <?php endforeach; ?>  
                                        
                                        <input type="hidden" name="pembelian_invoice2" value="<?= $in; ?>">
                                        <input type="hidden" name="invoice_pembelian_number_delete" value="<?= $inDelete; ?>">
                                        <input type="hidden" name="pembelian_invoice_parent2" value="<?= $inDelete; ?>">
                                        <input type="hidden" name="invoice_hutang" value="<?= $r; ?>">
                                        <input type="hidden" name="invoice_hutang_lunas" value="0">
                                        <input type="hidden" name="invoice_pembelian_cabang" value="<?= $sessionCabang; ?>">
                                        <input type="hidden" name="ppn_value" value="11">
                                        
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

    // Event handler untuk edit invoice
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

    // Konversi Satuan
    $(document).on('change', '.satuan-pilihan', function() {
        var selectedOption = $(this).find('option:selected');
        var konversi = selectedOption.data('konversi');
        var row = $(this).closest('tr');
        
        row.find('.qty-input').attr('data-konversi', konversi);
        hitungSubtotal(row);
    });

    // Event handler untuk input bayar - auto calculate kembalian
    $(document).on('keyup', '#angka1', function() {
        hitung2();
    });

    // Hitung Subtotal
    function hitungSubtotal(row) {
        var qty = parseFloat(row.find('input[name="keranjang_qty"]').val()) || 0;
        var konversi = parseFloat(row.find('.satuan-pilihan option:selected').data('konversi')) || 1;
        var harga = parseFloat(row.find('.harga-input').val()) || 0;
        var diskon = parseFloat(row.find('.diskon-input').val()) || 0;
        
        var qtySebenarnya = qty * konversi;
        var subtotal = qtySebenarnya * harga * (1 - (diskon/100));
        
        row.find('.subtotal').text(formatRupiah(subtotal));
        hitungTotal();
    }

    // Format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Hitung Total
    function hitungTotal() {
        var total = 0;
        $('tbody tr').each(function() {
            var subtotalText = $(this).find('.subtotal').text().replace('Rp ', '').replace(/\./g, '');
            total += parseFloat(subtotalText) || 0;
        });
        
        $('#total-transaksi').text(formatRupiah(total));
        $('#ppn-transaksi').text(formatRupiah(total * 0.11));
        $('#grand-total').text(formatRupiah(total * 1.11));
    }
});

// Function untuk validasi hanya angka (untuk input qty)
function hanyaAngka(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

// Function untuk validasi hanya angka dengan titik untuk format ribuan (untuk input bayar)
function hanyaAngka1(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    // Allow: backspace, delete, tab, escape, enter, decimal point
    if (charCode == 46 || charCode == 8 || charCode == 9 || charCode == 27 || charCode == 13 ||
        // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
        (charCode == 65 && (evt.ctrlKey === true || evt.metaKey === true)) ||
        (charCode == 67 && (evt.ctrlKey === true || evt.metaKey === true)) ||
        (charCode == 86 && (evt.ctrlKey === true || evt.metaKey === true)) ||
        (charCode == 88 && (evt.ctrlKey === true || evt.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (charCode >= 35 && charCode <= 40)) {
        return true;
    }
    // Ensure that it is a number and stop the keypress
    if ((evt.shiftKey || (charCode < 48 || charCode > 57)) && (charCode < 96 || charCode > 105)) {
        return false;
    }
    return true;
}

// Function untuk menghitung kembalian/sisa hutang
function hitung2() {
    // Ambil total yang harus dibayar (termasuk PPN)
    var totalText = $('.table-nominal').eq(3).text(); // Ambil total dari baris "TOTAL"
    var totalBayar = parseFloat(totalText.replace(/[^\d]/g, '')) || 0;
    
    // Ambil jumlah yang dibayar
    var bayar = parseFloat($("#angka1").val().replace(/\./g, '')) || 0;
    
    // Hitung selisih
    var selisih = bayar - totalBayar;
    
    // Format dan tampilkan hasil
    if (selisih >= 0) {
        // Jika bayar >= total, tampilkan kembalian
        $("#hasil").val(formatAngka(selisih));
    } else {
        // Jika bayar < total, tampilkan sisa hutang (negatif)
        $("#hasil").val(formatAngka(Math.abs(selisih)));
    }
}

// Function untuk format angka dengan titik ribuan
function formatAngka(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Auto format input bayar dengan titik ribuan
$(document).on('keyup', '#angka1', function() {
    var value = $(this).val().replace(/\./g, '');
    if (value) {
        $(this).val(formatAngka(value));
    }
    hitung2();
});

</script>