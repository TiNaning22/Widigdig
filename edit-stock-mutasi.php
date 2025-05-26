<?php 
include '_header.php';
include '_nav.php';
include '_sidebar.php'; 
error_reporting(0);

// Cek level akses
if ($levelLogin === "kasir" || $levelLogin === "kurir") {
    echo "<script>document.location.href = 'bo';</script>";
    exit;
}

// Ambil data di URL
$id = abs((int)base64_decode($_GET['id']));

// Query data barang berdasarkan ID
$barang = query("SELECT b.*, t.toko_nama FROM barang b 
                LEFT JOIN toko t ON b.barang_cabang = t.toko_cabang 
                WHERE b.barang_id = $id")[0];

// Query untuk mengambil semua data cabang
$cabangData = query("SELECT * FROM toko WHERE toko_status = 1 ORDER BY toko_cabang ASC");

// Jika tombol submit ditekan
if (isset($_POST["submit"])) {
    $stock_lama = $barang["barang_stock"];
    $stock_baru = $_POST["barang_stock"];
    $keterangan = $_POST["keterangan"];
    $barang_id = $id;
    $barang_cabang = $barang["barang_cabang"];
    $cabang_tujuan = $_POST["cabang_tujuan"];
    
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Update the stock
        $update = mysqli_query($conn, "UPDATE barang SET barang_stock = '$stock_baru' WHERE barang_id = '$barang_id'");
        
        // Record the log
        $log = mysqli_query($conn, "INSERT INTO stock_log (barang_id, barang_cabang, stock_sebelum, stock_sesudah, keterangan) 
                            VALUES ('$barang_id', '$barang_cabang', '$stock_lama', '$stock_baru', '$keterangan')");
        
        // If a destination branch is selected, handle the transfer
        if (!empty($cabang_tujuan) && $cabang_tujuan != $barang_cabang) {
            // Check if the product exists in the destination branch
            $cek_barang = query("SELECT * FROM barang WHERE barang_kode = '{$barang['barang_kode']}' AND barang_cabang = '$cabang_tujuan'");
            
            $jumlah_transfer = $stock_lama - $stock_baru;
            
            if (count($cek_barang) > 0) {
                // If exists, update the stock
                $barang_tujuan_id = $cek_barang[0]['barang_id'];
                $stock_tujuan_lama = $cek_barang[0]['barang_stock'];
                $stock_tujuan_baru = $stock_tujuan_lama + $jumlah_transfer;
                
                $update_tujuan = mysqli_query($conn, "UPDATE barang SET barang_stock = '$stock_tujuan_baru' WHERE barang_id = '$barang_tujuan_id'");
                
                // Log the destination stock change
                $log_tujuan = mysqli_query($conn, "INSERT INTO stock_log (barang_id, barang_cabang, stock_sebelum, stock_sesudah, keterangan) 
                                        VALUES ('$barang_tujuan_id', '$cabang_tujuan', '$stock_tujuan_lama', '$stock_tujuan_baru', 'Transfer dari cabang $barang_cabang: $keterangan')");
                
                if (!$update_tujuan || !$log_tujuan) {
                    throw new Exception("Gagal update stock di cabang tujuan");
                }
            } else {
                // If not exists, create new product entry in the destination branch
                $insert_barang = mysqli_query($conn, "INSERT INTO barang (
                    barang_kode, barang_nama, barang_kategori, barang_harga_beli, barang_harga, barang_stock, barang_cabang
                ) VALUES (
                    '{$barang['barang_kode']}', '{$barang['barang_nama']}', '{$barang['barang_kategori']}', 
                    '{$barang['barang_harga_beli']}', '{$barang['barang_harga']}', '$jumlah_transfer', '$cabang_tujuan'
                )");
                
                $new_barang_id = mysqli_insert_id($conn);
                
                // Log the new product creation
                $log_tujuan = mysqli_query($conn, "INSERT INTO stock_log (barang_id, barang_cabang, stock_sebelum, stock_sesudah, keterangan) 
                                        VALUES ('$new_barang_id', '$cabang_tujuan', '0', '$jumlah_transfer', 'Transfer baru dari cabang $barang_cabang: $keterangan')");
                
                if (!$insert_barang || !$log_tujuan) {
                    throw new Exception("Gagal membuat barang baru di cabang tujuan");
                }
            }
        }
                            
        if ($update && $log) {
            mysqli_commit($conn);
            echo "<script>
                alert('Stock berhasil diperbarui!');
                document.location.href = 'koreksi-stock';
            </script>";
        } else {
            throw new Exception("Database error");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Gagal memperbarui stock: " . $e->getMessage() . "');</script>";
    }
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Stock Barang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="bo">Home</a></li>
                        <li class="breadcrumb-item"><a href="koreksi-stock">Koreksi Stock</a></li>
                        <li class="breadcrumb-item active">Edit Stock</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form role="form" action="" method="post">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Form Edit Stock</h3>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="barang_id" value="<?= $barang['barang_id']; ?>">
                                
                                <div class="form-group">
                                    <label for="barang_kode">Kode Barang</label>
                                    <input type="text" class="form-control" id="barang_kode" value="<?= $barang['barang_kode']; ?>" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label for="barang_nama">Nama Barang</label>
                                    <input type="text" class="form-control" id="barang_nama" value="<?= $barang['barang_nama']; ?>" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label for="barang_cabang">Cabang Saat Ini</label>
                                    <input type="text" class="form-control" id="barang_cabang" value="<?php 
                                        $cabang = $barang['barang_cabang'];
                                        if ($cabang == 0) {
                                            echo "Pusat";
                                        } else {
                                            echo $barang['toko_nama'] . " (Cabang " . $cabang . ")";
                                        }
                                    ?>" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label for="cabang_tujuan">Pindahkan ke Cabang</label>
                                    <select class="form-control" id="cabang_tujuan" name="cabang_tujuan">
                                        <option value="">-- Pilih Cabang Tujuan --</option>
                                        <?php foreach ($cabangData as $c): ?>
                                            <?php if ($c['toko_cabang'] != $barang['barang_cabang']): ?>
                                                <option value="<?= $c['toko_cabang']; ?>">
                                                    <?php if ($c['toko_cabang'] == 0): ?>
                                                        Pusat
                                                    <?php else: ?>
                                                        <?= $c['toko_nama']; ?> (Cabang <?= $c['toko_cabang']; ?>)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Kosongkan jika tidak ingin memindahkan stock</small>
                                </div>
                                
                                
                                <div class="form-group">
                                    <label for="stock_lama">Stock Saat Ini</label>
                                    <input type="number" class="form-control" id="stock_lama" value="<?= $barang['barang_stock']; ?>" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label for="barang_stock">Stock Baru</label>
                                    <input type="number" name="barang_stock" class="form-control" id="barang_stock" value="<?= $barang['barang_stock']; ?>" required>
                                    <small class="form-text text-muted">Jika memindahkan stock, jumlah pengurangan akan ditambahkan ke cabang tujuan</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="keterangan">Keterangan Perubahan</label>
                                    <textarea name="keterangan" class="form-control" id="keterangan" rows="3" placeholder="Alasan perubahan stock (wajib diisi)" required></textarea>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                                <a href="koreksi-stock" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<?php include '_footer.php'; ?>

<script>
$(function() {
    
    
    // Validasi form sebelum submit
    $('form').submit(function() {
        var stockLama = parseInt($('#stock_lama').val());
        var stockBaru = parseInt($('#barang_stock').val());
        var keterangan = $('#keterangan').val().trim();
        var cabangTujuan = $('#cabang_tujuan').val();
        
        if (stockLama === stockBaru && cabangTujuan === '') {
            alert('Stock baru tidak boleh sama dengan stock lama jika tidak memindahkan ke cabang lain!');
            return false;
        }
        
        if (cabangTujuan !== '' && stockBaru > stockLama) {
            alert('Stock baru tidak boleh lebih besar dari stock lama saat memindahkan ke cabang lain!');
            return false;
        }
        
        if (keterangan === '') {
            alert('Keterangan perubahan stock harus diisi!');
            return false;
        }
        
        return true;
    });
    
    // Handling cabang tujuan change
    $('#cabang_tujuan').change(function() {
        var cabangTujuan = $(this).val();
        if (cabangTujuan !== '') {
            $('#barang_stock').attr('placeholder', 'Masukkan stock yang tersisa setelah pemindahan');
        } else {
            $('#barang_stock').attr('placeholder', '');
        }
    });
}); // Make sure this closing parenthesis is present
</script>