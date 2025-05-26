<?php 
  include 'aksi/halau.php'; 
  include 'aksi/functions.php';
  $id = $_POST['id'];

  $barang = query("SELECT * FROM barang WHERE barang_id = $id")[0];
?>


	   <div class="form-group">
        <label for="barang_penyimpanan">Posisi</label>
        <input type="teks" name="barang_penyimpanan" class="form-control" id="barang_penyimpanan" value="<?= $barang['barang_penyimpanan']; ?>" readonly>
    </div>
