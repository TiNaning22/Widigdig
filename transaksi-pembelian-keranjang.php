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

// PERBAIKAN 1 & 2: Hitung semua nilai termasuk margin di PHP
$i = 1; 
$subtotalSebelumDiskon = 0;
$totalDiskon = 0;
$totalSetelahDiskon = 0;
$hargaBeliTotal = 0;

// Hitung semua nilai dalam loop
foreach($keranjang as $row) : 
    if ($row['keranjang_id_kasir'] === $_SESSION['user_id']) {
        $bik = $row['barang_id'];
        
        // Ambil data barang termasuk harga beli
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
        $subtotalSetelahDiskonItem = $subtotal - $diskonNominal;
        
        $subtotalSebelumDiskon += $subtotal;
        $totalDiskon += $diskonNominal;
        $totalSetelahDiskon += $subtotalSetelahDiskonItem;
        
        // Hitung total harga beli (gunakan harga dari keranjang jika ada, fallback ke database)
        $hargaBeliItem = $row['keranjang_harga'] > 0 ? $row['keranjang_harga'] : $brg['barang_harga_beli'];
        $hargaBeliTotal += $hargaBeliItem * $row['keranjang_qty'];
    }
endforeach;

// PERBAIKAN 1: Standardisasi rumus margin - gunakan Grand Total (termasuk PPN)
$totalPPN = $totalSetelahDiskon * 0.11;
$grandTotal = $totalSetelahDiskon + $totalPPN;
$marginKotor = $grandTotal - $hargaBeliTotal;
$marginPersen = ($hargaBeliTotal > 0) ? ($marginKotor / $hargaBeliTotal) * 100 : 0;

// Batasi margin persen dalam range yang wajar
$marginPersen = max(-999, min(9999, $marginPersen));
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
                    foreach($keranjang as $row) : 
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
                    ?>
                    <tr data-keranjang-id="<?= $row['keranjang_id']; ?>">
                        <td><?= $i; ?></td>
                        <td><?= $row['keranjang_nama']; ?></td>
                        <td>
                            <select name="satuan_pembelian[]" class="form-control satuan-pilihan" 
                                    data-barangid="<?= $row['barang_id']; ?>" 
                                    data-keranjang-id="<?= $row['keranjang_id']; ?>">
                                
                                <!-- Satuan 1 (Utama) -->
                                <option value="1" 
                                        data-konversi="1" 
                                        data-harga="<?= $brg['barang_harga']; ?>">
                                    <?= $brg['satuan_1'] ?> - Rp <?= number_format($brg['barang_harga']) ?>
                                </option>
                                
                                <!-- Satuan 2 (jika ada) -->
                                <?php if($brg['satuan_id_2'] > 0 && $brg['satuan_isi_2'] > 0): ?>
                                <option value="2" 
                                        data-konversi="<?= $brg['satuan_isi_2'] ?>" 
                                        data-harga="<?= $brg['barang_harga_s2']; ?>">
                                    <?= $brg['satuan_2'] ?> - Rp <?= number_format($brg['barang_harga_s2']) ?>
                                    (1 <?= $brg['satuan_1'] ?> = <?= $brg['satuan_2'] ?> <?= $brg['satuan_isi_2'] ?>)
                                </option>
                                <?php endif; ?>
                                
                                <!-- Satuan 3 (jika ada) -->
                                <?php if($brg['satuan_id_3'] > 0 && $brg['satuan_isi_3'] > 0): ?>
                                <option value="3" 
                                        data-konversi="<?= $brg['satuan_isi_3'] ?>" 
                                        data-harga="<?= $brg['barang_harga_s3']; ?>">
                                    <?= $brg['satuan_3'] ?> - Rp <?= number_format($brg['barang_harga_s3']) ?>
                                    (1 <?= $brg['satuan_1'] ?> = <?= $brg['satuan_3'] ?> <?= $brg['satuan_isi_3'] ?>)
                                </option>
                                <?php endif; ?>
                            </select>
                        </td>
                        <td>
                            <span class="harga-text" 
                                data-harga="<?= $row['keranjang_harga'] ?: 0; ?>" 
                                data-harga-beli="<?= $brg['barang_harga_beli'] ?: 0; ?>">
                                Rp. <?= number_format($row['keranjang_harga'] ?: 0, 0, ',', '.'); ?>
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
                                        Rp. <?= number_format($totalPPN, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>TOTAL</b></td>
                                    <td class="table-nominal" id="total-display">
                                        Rp. <?= number_format($grandTotal, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Margin Kotor</b></td>
                                    <td class="table-nominal" id="margin-display">
                                        Rp. <?= number_format($marginKotor, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Margin (%)</b></td>
                                    <td class="table-nominal" id="margin-persen-display">
                                        <?= number_format($marginPersen, 2, ',', '.') ?>%
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
                                            <input type="hidden" name="keranjang_qty[]" value="<?= $stk['keranjang_qty'] ?>" 
                                                   class="qty-hidden" data-keranjang-id="<?= $stk['keranjang_id']; ?>"> 
                                            <input type="hidden" name="keranjang_id_kasir[]" value="<?= $_SESSION['user_id']; ?>">
                                            <input type="hidden" name="pembelian_invoice[]" value="<?= $in; ?>">
                                            <input type="hidden" name="pembelian_invoice_parent[]" value="<?= $inDelete; ?>">
                                            <input type="hidden" name="pembelian_date[]" value="<?= date("Y-m-d") ?>">
                                            <input type="hidden" name="barang_harga_beli[]" value="<?= $stk['keranjang_harga']; ?>" 
                                                   class="harga-hidden" data-keranjang-id="<?= $stk['keranjang_id']; ?>">
                                            <input type="hidden" name="pembelian_cabang[]" value="<?= $sessionCabang; ?>">
                                            <input type="hidden" name="satuan_pilihan[]" value="1" 
                                                   class="satuan-hidden" data-keranjang-id="<?= $stk['keranjang_id']; ?>">
                                            <input type="hidden" name="konversi_value[]" value="1" 
                                                   class="konversi-hidden" data-keranjang-id="<?= $stk['keranjang_id']; ?>">
                                            <input type="hidden" 
                                                name="diskon_item_value[]" 
                                                value="<?= $stk['keranjang_diskon'] ?? 0; ?>" 
                                                class="diskon-hidden"
                                                data-keranjang-id="<?= $stk['keranjang_id']; ?>">
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
                                        <input type="hidden" name="total_ppn" id="total-ppn-hidden" value="<?= $totalPPN; ?>">
                                        <input type="hidden" name="grand_total" id="grand-total-hidden" value="<?= $grandTotal; ?>">
                                        
                                        <!-- Margin calculations -->
                                        <input type="hidden" name="margin_kotor" id="margin-kotor-hidden" value="<?= $marginKotor; ?>">
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
        
        // Alert untuk harga beli kosong
        $(document).on('click', '.btn-disabled', function(){
            alert("Harga Beli Masih ada yang bernilai kosong (Rp.0) !! Segera Update Harga Pembelian Barang per Produk ..");
        });

        // ===== EDIT INVOICE HANDLER =====
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
                }
            });
        });

        // ===== EDIT HARGA BELI =====
        $(document).on('click', '.edit-harga-beli', function(e) {
            e.preventDefault();
            $("#modal-id-2").modal('show');
            
            var $row = $(this).closest('tr');
            var keranjangId = $(this).data('id');
            var currentHarga = parseFloat($row.find('.harga-text').attr('data-harga')) || 0;
            
            window.currentEditingRow = $row;
            window.currentKeranjangId = keranjangId;
            
            $("#data-keranjang-pembelian").html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>');
            
            $.ajax({
                url: 'transaksi-pembelian-harga-beli.php',
                type: 'POST',
                data: {
                    id: keranjangId,
                    harga_current: currentHarga,
                    keranjang_id: keranjangId
                },
                success: function(html) {
                    $("#data-keranjang-pembelian").html(html);
                },
                error: function(xhr) {
                    $("#data-keranjang-pembelian").html('<div class="text-center text-danger py-4">Gagal memuat data</div>');
                }
            });
        });

        // ===== SUBMIT EDIT HARGA BELI =====
        $(document).off('submit', '#form-edit-harga-beli').on('submit', '#form-edit-harga-beli', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $modal = $('#modal-id-2');
            var $submitBtn = $form.find('button[type="submit"]');
            var originalText = $submitBtn.html();
            
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            
            $.ajax({
                url: "transaksi-pembelian-harga-beli-proses.php",
                data: $form.serialize(),
                type: "post",
                dataType: "json",
                success: function(result) {
                    if (result.hasil === "sukses") {
                        var keranjangId = result.keranjang_id;
                        var hargaBaru = parseFloat(result.harga_baru) || 0;
                        
                        updateDOMHargaBeli(keranjangId, hargaBaru);
                        
                        setTimeout(function() {
                            $modal.modal('hide');
                            
                            setTimeout(function() {
                                Swal.fire({
                                    title: 'Sukses',
                                    text: 'Harga berhasil diupdate ke Rp. ' + formatNumber(hargaBaru),
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                
                                setTimeout(function() {
                                    hitungTotalKeseluruhan();
                                }, 100);
                            }, 200);
                        }, 300);
                        
                    } else {
                        Swal.fire('Error', result.pesan || 'Gagal update harga', 'error');
                        $submitBtn.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Terjadi kesalahan sistem: ' + error, 'error');
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // ===== FUNGSI UPDATE DOM HARGA BELI =====
        function updateDOMHargaBeli(keranjangId, hargaBaru) {
            var $targetRows = $('tr[data-keranjang-id="' + keranjangId + '"]');
            
            if ($targetRows.length === 0 && window.currentEditingRow) {
                $targetRows = window.currentEditingRow;
            }
            
            if ($targetRows.length === 0) {
                return;
            }
            
            $targetRows.each(function(index) {
                var $row = $(this);
                var $hargaText = $row.find('.harga-text');
                if ($hargaText.length) {
                    $hargaText.attr('data-harga', hargaBaru);
                    $hargaText.data('harga', hargaBaru);
                    $hargaText.text('Rp. ' + formatNumber(hargaBaru));
                }
                
                var $hargaInput = $row.find('input[name="barang_harga_beli[]"]');
                if ($hargaInput.length) {
                    $hargaInput.val(hargaBaru);
                }
                
                $row.find('input[data-type="harga-beli"]').val(hargaBaru);
                $row.attr('data-last-updated', new Date().getTime());
                
                setTimeout(function() {
                    hitungSubtotalItem($row);
                }, 50);
            });
            
            window.currentEditingRow = null;
            window.currentKeranjangId = null;
        }

        // ===== SUBMIT EDIT INVOICE =====
        $('#form-edit-invoice').submit(function(e) {
            e.preventDefault();
            
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
                    
                    try {
                        var errResponse = JSON.parse(xhr.responseText);
                        Swal.fire('Error', errResponse.message || 'Terjadi kesalahan saat update', 'error');
                    } catch(e) {
                        Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
                    }
                }
            });
        });

        // ===== SATUAN CHANGE HANDLER DENGAN KONVERSI OTOMATIS =====
        $(document).on('change', '.satuan-pilihan', function() {
            var $select = $(this);
            var selectedOption = $select.find('option:selected');
            var keranjangId = $select.data('keranjang-id');
            var barangId = $select.data('barangid');
            var satuanPilihan = $select.val();
            var konversi = parseFloat(selectedOption.data('konversi')) || 1;
            var hargaSatuan = parseFloat(selectedOption.data('harga')) || 0;
            var $row = $select.closest('tr');
            
            console.log('Satuan dipilih:', satuanPilihan);
            console.log('Konversi:', konversi);
            console.log('Harga satuan dari data:', hargaSatuan);
            
            // Ambil harga dasar (satuan 1) untuk perhitungan konversi
            var $satuanUtama = $select.find('option[value="1"]');
            var hargaDasar = parseFloat($satuanUtama.data('harga')) || 0;
            
            // Hitung harga berdasarkan konversi jika harga satuan tidak tersedia
            var hargaFinal = hargaSatuan;
            if (hargaSatuan <= 0 && hargaDasar > 0) {
                // Jika satuan lebih besar (misal: 1 dus = 12 pcs), maka harga dus = harga pcs * konversi
                hargaFinal = hargaDasar * konversi;
                console.log('Harga dihitung dari konversi:', hargaFinal);
            }
            
            // Update harga di tampilan
            updateHargaTampilan($row, hargaFinal);
            
            // Update keranjang via AJAX
            $.ajax({
                url: 'update-keranjang-satuan.php',
                method: 'POST',
                data: {
                    keranjang_id: keranjangId,
                    barang_id: barangId,
                    satuan_pilihan: satuanPilihan,
                    harga_satuan: hargaFinal,
                    konversi: konversi
                },
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        console.log('Keranjang berhasil diupdate');
                        // Hitung ulang subtotal dan total
                        hitungSubtotalItem($row);
                    } else {
                        alert('Gagal update satuan: ' + response.message);
                        // Kembalikan ke pilihan sebelumnya jika gagal
                        $select.val($select.data('previous-value') || '1');
                    }
                },
                error: function() {
                    alert('Error dalam update satuan');
                    // Kembalikan ke pilihan sebelumnya jika gagal
                    $select.val($select.data('previous-value') || '1');
                }
            });
            
            // Simpan nilai sebelumnya untuk rollback jika error
            $select.data('previous-value', satuanPilihan);
        });

        // ===== FUNGSI UPDATE HARGA TAMPILAN =====
        function updateHargaTampilan($row, hargaBaru) {
            var keranjangId = $row.data('keranjang-id');
            var $hargaText = $row.find('.harga-text');
            
            if ($hargaText.length) {
                $hargaText.attr('data-harga', hargaBaru);
                $hargaText.data('harga', hargaBaru);
                $hargaText.text('Rp. ' + formatNumber(hargaBaru));
                
                console.log('Harga tampilan diupdate ke:', hargaBaru);
            }
            
            // Update input tersembunyi jika ada
            var $hargaInput = $row.find('input[name="barang_harga_beli[]"]');
            if ($hargaInput.length) {
                $hargaInput.val(hargaBaru);
            }
        }

        // ===== FUNGSI HITUNG HARGA BERDASARKAN KONVERSI =====
        function hitungHargaKonversi(hargaDasar, konversi, tipeSatuan) {
            var hargaDasarFloat = parseFloat(hargaDasar) || 0;
            var konversiFloat = parseFloat(konversi) || 1;
            
            if (hargaDasarFloat <= 0) {
                return 0;
            }
            
            // Untuk satuan yang lebih besar (misal: dus), kalikan dengan konversi
            // Untuk satuan yang lebih kecil, bagi dengan konversi
            switch(tipeSatuan) {
                case '1': // Satuan dasar
                    return hargaDasarFloat;
                case '2': // Satuan ke-2 (biasanya lebih besar)
                    return hargaDasarFloat * konversiFloat;
                case '3': // Satuan ke-3 (biasanya lebih besar)
                    return hargaDasarFloat * konversiFloat;
                default:
                    return hargaDasarFloat;
            }
        }

        // ===== FUNGSI AUTO-CALCULATE SEMUA HARGA SATUAN =====
        function autoCalculateAllSatuanHarga() {
            $('.satuan-pilihan').each(function() {
                var $select = $(this);
                var $options = $select.find('option');
                var hargaDasar = 0;
                
                // Ambil harga dasar dari satuan 1
                $options.each(function() {
                    var $option = $(this);
                    if ($option.val() === '1') {
                        hargaDasar = parseFloat($option.data('harga')) || 0;
                        return false; // break
                    }
                });
                
                // Update harga untuk setiap opsi berdasarkan konversi
                $options.each(function() {
                    var $option = $(this);
                    var satuanValue = $option.val();
                    var konversi = parseFloat($option.data('konversi')) || 1;
                    var hargaExisting = parseFloat($option.data('harga')) || 0;
                    
                    // Jika harga belum ada atau = 0, hitung berdasarkan konversi
                    if (hargaExisting <= 0 && hargaDasar > 0 && satuanValue !== '1') {
                        var hargaKonversi = hitungHargaKonversi(hargaDasar, konversi, satuanValue);
                        $option.data('harga', hargaKonversi);
                        $option.attr('data-harga', hargaKonversi);
                        
                        // Update text option untuk menampilkan harga baru
                        var satuanNama = $option.text().split(' - ')[0];
                        var konversiText = $option.text().includes('(') ? 
                            ' ' + $option.text().split('(')[1] : '';
                        $option.text(satuanNama + ' - Rp ' + formatNumber(hargaKonversi) + 
                                   (konversiText ? '(' + konversiText : ''));
                    }
                });
            });
        }

        // ===== INISIALISASI HARGA SATUAN OTOMATIS =====
        $(document).ready(function() {
            // Jalankan auto calculate setelah DOM ready
            setTimeout(function() {
                autoCalculateAllSatuanHarga();
            }, 500);
        });

        function updateRowDisplay(keranjangId) {
            // Function untuk update tampilan row tanpa reload halaman
            var row = $('tr[data-keranjang-id="' + keranjangId + '"]');
            if (row.length) {
                hitungSubtotalItem(row);
            }
        }
        
        // ===== FUNGSI UPDATE HARGA BERDASARKAN SATUAN (LEGACY - DIPERBAIKI) =====
        function updateHargaBerdasarkanSatuan(row, barangId, satuanTerpilih, konversiValue, keranjangId, callback) {
            // Ambil harga dasar dari row
            var $satuanSelect = row.find('.satuan-pilihan');
            var $satuanUtama = $satuanSelect.find('option[value="1"]');
            var hargaDasar = parseFloat($satuanUtama.data('harga')) || 0;
            
            // Hitung harga berdasarkan konversi
            var hargaKonversi = hitungHargaKonversi(hargaDasar, konversiValue, satuanTerpilih);
            
            if (hargaKonversi > 0) {
                updateDOMHargaBeli(keranjangId, hargaKonversi);
                updateKeranjangSatuan(keranjangId, satuanTerpilih, hargaKonversi, konversiValue);
            }
            
            if (callback) callback();
        }
        
        // ===== UPDATE KERANJANG SATUAN =====
        function updateKeranjangSatuan(keranjangId, satuanPilihan, hargaKonversi, konversiValue) {
            $.ajax({
                url: 'update-keranjang-satuan.php',
                type: 'POST',
                data: {
                    keranjang_id: keranjangId,
                    satuan_pilihan: satuanPilihan,
                    harga_konversi: hargaKonversi,
                    konversi_value: konversiValue
                },
                error: function(xhr, status, error) {
                    console.log('Error update keranjang satuan:', error);
                }
            });
        }
        
        // ===== FORM SUBMIT HANDLER =====
        $('#form-transaksi').on('submit', function(e) {
            var diskonInconsistencies = [];
            
            $('tbody tr').each(function(index) {
                var $row = $(this);
                var keranjangId = $row.data('keranjang-id');
                var displayDiskon = parseFloat($row.find('.diskon-input').val()) || 0;
                var $hiddenDiskon = $('input[name="diskon_item_value[]"][data-keranjang-id="' + keranjangId + '"]');
                var hiddenDiskon = parseFloat($hiddenDiskon.val()) || 0;
                
                if (Math.abs(displayDiskon - hiddenDiskon) > 0.01) {
                    diskonInconsistencies.push({
                        keranjangId: keranjangId,
                        rowIndex: index,
                        displayDiskon: displayDiskon,
                        hiddenDiskon: hiddenDiskon
                    });
                }
            });
            
            if (diskonInconsistencies.length > 0) {
                e.preventDefault();
                
                diskonInconsistencies.forEach(function(item) {
                    var $hiddenInput = $('input[name="diskon_item_value[]"][data-keranjang-id="' + item.keranjangId + '"]');
                    $hiddenInput.val(item.displayDiskon);
                });
                
                Swal.fire({
                    title: 'Perbaikan Data',
                    text: 'Ditemukan inkonsistensi data diskon yang telah diperbaiki. Silakan submit ulang.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                
                return false;
            }
        });

        // ===== FUNGSI HITUNG SUBTOTAL ITEM =====
        function hitungSubtotalItem(row) {
            if (!row || !row.length) {
                return;
            }
            
            var qty = parseFloat(row.find('.qty-input').val()) || 0;
            var harga = parseFloat(row.find('.harga-text').attr('data-harga')) || 0;
            var diskonPersen = parseFloat(row.find('.diskon-input').val()) || 0;
            var keranjangId = row.data('keranjang-id');
            
            var subtotal = qty * harga;
            var diskonNominal = subtotal * (diskonPersen / 100);
            var subtotalSetelahDiskon = subtotal - diskonNominal;
            
            var $subtotalElement = row.find('.subtotal');
            if ($subtotalElement.length) {
                $subtotalElement.text('Rp. ' + formatNumber(subtotalSetelahDiskon));
            }
            
            var $hiddenDiskonInput = $('input[name="diskon_item_value[]"][data-keranjang-id="' + keranjangId + '"]');
            if ($hiddenDiskonInput.length) {
                $hiddenDiskonInput.val(diskonPersen);
            } else {
                var rowIndex = $('tbody tr').index(row);
                var $hiddenDiskonInputByIndex = $('input[name="diskon_item_value[]"]').eq(rowIndex);
                if ($hiddenDiskonInputByIndex.length) {
                    $hiddenDiskonInputByIndex.val(diskonPersen);
                }
            }
            
            setTimeout(hitungTotalKeseluruhan, 50);
        }

        // ===== FUNGSI HITUNG TOTAL KESELURUHAN =====
        function hitungTotalKeseluruhan() {
            var subtotalSebelumDiskon = 0;
            var totalDiskon = 0;
            var totalSetelahDiskon = 0;
            var hargaBeliTotal = 0;
            
            $('tbody tr').each(function(index) {
                var $row = $(this);
                var qty = parseFloat($row.find('.qty-input').val()) || 0;
                var harga = parseFloat($row.find('.harga-text').attr('data-harga')) || 0;
                var diskonPersen = parseFloat($row.find('.diskon-input').val()) || 0;
                var hargaBeli = parseFloat($row.find('.harga-text').attr('data-harga-beli')) || harga;
                
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
            var marginPersen = hargaBeliTotal > 0 ? (marginKotor / hargaBeliTotal) * 100 : 0;
            marginPersen = Math.max(0, Math.min(100, marginPersen));
            
            $('#subtotal-display').text('Rp. ' + formatNumber(subtotalSebelumDiskon));
            $('#diskon-display').text('Rp. ' + formatNumber(totalDiskon));
            $('#setelah-diskon-display').text('Rp. ' + formatNumber(totalSetelahDiskon));
            $('#ppn-display').text('Rp. ' + formatNumber(totalPPN));
            $('#total-display').text('Rp. ' + formatNumber(grandTotal));
            $('#margin-display').text('Rp. ' + formatNumber(marginKotor));
            $('#margin-persen-display').text(marginPersen.toFixed(2) + '%');
            
            $('#subtotal-hidden').val(subtotalSebelumDiskon);
            $('#total-diskon-hidden').val(totalDiskon);
            $('#total-setelah-diskon-hidden').val(totalSetelahDiskon);
            $('#total-ppn-hidden').val(totalPPN);
            $('#grand-total-hidden').val(grandTotal);
            $('#margin-kotor-hidden').val(marginKotor);
            $('#margin-persen-hidden').val(marginPersen);
            $('#harga-beli-total-hidden').val(hargaBeliTotal);
            
            $('.b2').val(grandTotal);
            
            hitung2();
        }

        // ===== DISKON INPUT HANDLER =====
        var diskonUpdateTimeout;
        $(document).on('input change', '.diskon-input', function() {
            var $this = $(this);
            var row = $this.closest('tr');
            var keranjangId = $this.data('keranjang-id');
            var diskonValue = parseFloat($this.val()) || 0;
            
            var $hiddenDiskonInput = $('input[name="diskon_item_value[]"][data-keranjang-id="' + keranjangId + '"]');
            if ($hiddenDiskonInput.length) {
                $hiddenDiskonInput.val(diskonValue);
            }
            
            hitungSubtotalItem(row);
            
            clearTimeout(diskonUpdateTimeout);
            
            diskonUpdateTimeout = setTimeout(function() {
                $.ajax({
                    url: 'update-discount-keranjang.php',
                    type: 'POST',
                    data: {
                        keranjang_id: keranjangId,
                        diskon: diskonValue
                    }
                });
            }, 500);
        });

        // ===== QTY INPUT HANDLER =====
        var qtyUpdateTimeout;
        $(document).on('input change', '.qty-input', function() {
            var $this = $(this);
            var row = $this.closest('tr');
            var keranjangId = row.data('keranjang-id');
            
            hitungSubtotalItem(row);
            
            clearTimeout(qtyUpdateTimeout);
            
            qtyUpdateTimeout = setTimeout(function() {
                $.ajax({
                    url: 'update-keranjang-qty-realtime.php',
                    type: 'POST',
                    data: {
                        keranjang_id: keranjangId,
                        qty: parseFloat($this.val()) || 1
                    }
                });
            }, 500);
        });

        // ===== INITIALIZATION =====
        setTimeout(function() {
            hitungTotalKeseluruhan();
            var grandTotal = parseFloat($('#grand-total-hidden').val()) || 0;
            $('.b2').val(grandTotal);
        }, 200);
        
        window.hitungSubtotalItem = hitungSubtotalItem;
        window.hitungTotalKeseluruhan = hitungTotalKeseluruhan;
        window.updateDOMHargaBeli = updateDOMHargaBeli;
        window.hitungHargaKonversi = hitungHargaKonversi;
        window.autoCalculateAllSatuanHarga = autoCalculateAllSatuanHarga;
    });

    // ===== UTILITY FUNCTIONS =====
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
            kembalian = Math.abs(kembalian);
        }
        
        $(".c2").val(kembalian.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
    }

    function formatNumber(angka) {
        var num = parseFloat(angka) || 0;
        return num.toLocaleString('id-ID');
    }

    function formatRupiah(angka) {
        return 'Rp. ' + formatNumber(angka);
    }

    function hitungHargaPerSatuanTerkecil(hargaBeli, konversiValue) {
        return parseFloat(hargaBeli) / parseFloat(konversiValue);
    }
</script>