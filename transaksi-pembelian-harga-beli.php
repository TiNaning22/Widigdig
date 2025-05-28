<?php 
  include 'aksi/halau.php'; 
  include 'aksi/functions.php';
  $id = $_POST['id'];

  // Join with barang table to get barang_harga_beli
  $keranjang = query("
    SELECT kp.*, b.barang_harga_beli 
    FROM keranjang_pembelian kp 
    JOIN barang b ON kp.barang_id = b.barang_id 
    WHERE kp.keranjang_id = $id
  ")[0];
?>
	
<input type="hidden" name="keranjang_id" value="<?= $id; ?>">
<input type="hidden" name="barang_id" value="<?= $keranjang['barang_id']; ?>">
<div class="form-group">
    <label for="keranjang_harga">Harga per Satuan</label>
    <!-- Use barang_harga_beli as the default value -->
    <input type="number" name="keranjang_harga" class="form-control" id="keranjang_harga" 
           placeholder="Harga Beli di supplier per Produk" 
           value="<?= $keranjang['barang_harga_beli']; ?>" required>
</div>