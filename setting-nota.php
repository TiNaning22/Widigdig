<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
// ambil data toko aktif
$toko = query("SELECT * FROM toko WHERE toko_status = 1");

// Ambil pengaturan nota saat ini jika ada
$notaSetting = query("SELECT * FROM nota_setting");
$notaSetting = !empty($notaSetting) ? $notaSetting[0] : [];

// Jika memilih toko untuk melihat setting lebar nota
if(isset($_POST["pilih_toko"])){
  $selectedTokoId = $_POST["toko_id"];
  $selectedToko = query("SELECT * FROM toko WHERE toko_id = $selectedTokoId")[0];
  // Update nilai lebar nota berdasarkan toko yang dipilih
  $notaLebar = $selectedToko['toko_print'];
} else {
  $notaLebar = isset($notaSetting['nota_lebar']) ? $notaSetting['nota_lebar'] : 8;
}

// cek apakah tombol simpan sudah ditekan
if(isset($_POST["submit"])){
  // cek apakah data berhasil di update atau tidak
  if(updateNotaSetting($_POST) > 0) {
    echo "
      <script>
        alert('Pengaturan nota berhasil disimpan');
        document.location.href = 'setting-nota';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('Gagal menyimpan pengaturan nota');
      </script>
    ";
  }
}

// Cek apakah tombol preview sudah ditekan
if(isset($_POST["preview"])){
  // Tampilkan preview di halaman yang sama
  $showPreview = true;
  
  // Ambil data dari form untuk preview
  $previewTokoId = $_POST["nota_toko_id"];
  $previewToko = query("SELECT * FROM toko WHERE toko_id = $previewTokoId")[0];
  
  $previewData = [
    'toko' => $previewToko,
    'nota_lebar' => $_POST["nota_lebar"],
    'nota_font_size' => $_POST["nota_font_size"],
    'nota_header_height' => $_POST["nota_header_height"],
    'nota_margin' => $_POST["nota_margin"],
    'nota_show_logo' => $_POST["nota_show_logo"],
    'nota_show_alamat' => $_POST["nota_show_alamat"],
    'nota_show_telp' => $_POST["nota_show_telp"],
    'nota_show_email' => $_POST["nota_show_email"],
    'nota_footer_text' => $_POST["nota_footer_text"]
  ];
} else {
  $showPreview = false;
}
?>
<?php
// Fungsi untuk menambahkan atau mengupdate pengaturan nota
function updateNotaSetting($data) {
    global $conn;
    
    // Ambil data dari form
    $notaId = isset($data["nota_id"]) ? $data["nota_id"] : "";
    $tokoId = htmlspecialchars($data["nota_toko_id"]);
    $lebar = htmlspecialchars($data["nota_lebar"]);
    $fontSize = htmlspecialchars($data["nota_font_size"]);
    $headerHeight = htmlspecialchars($data["nota_header_height"]);
    $margin = htmlspecialchars($data["nota_margin"]);
    $showLogo = htmlspecialchars($data["nota_show_logo"]);
    $showAlamat = htmlspecialchars($data["nota_show_alamat"]);
    $showTelp = htmlspecialchars($data["nota_show_telp"]);
    $showEmail = htmlspecialchars($data["nota_show_email"]);
    $footerText = htmlspecialchars($data["nota_footer_text"]);
    
    // Cek apakah data sudah ada (update) atau perlu ditambahkan (insert)
    if(!empty($notaId)) {
        // Query update data
        $query = "UPDATE nota_setting SET 
                  nota_toko_id = $tokoId,
                  nota_lebar = $lebar,
                  nota_font_size = $fontSize,
                  nota_header_height = $headerHeight,
                  nota_margin = $margin,
                  nota_show_logo = $showLogo,
                  nota_show_alamat = $showAlamat,
                  nota_show_telp = $showTelp,
                  nota_show_email = $showEmail,
                  nota_footer_text = '$footerText',
                  nota_updated = NOW()
                  WHERE nota_id = $notaId";
                  
        mysqli_query($conn, $query);
    } else {
        // Query tambah data baru
        $query = "INSERT INTO nota_setting (
                    nota_toko_id, 
                    nota_lebar, 
                    nota_font_size, 
                    nota_header_height, 
                    nota_margin, 
                    nota_show_logo, 
                    nota_show_alamat, 
                    nota_show_telp, 
                    nota_show_email, 
                    nota_footer_text, 
                    nota_created, 
                    nota_updated
                  ) 
                  VALUES (
                    $tokoId, 
                    $lebar, 
                    $fontSize, 
                    $headerHeight, 
                    $margin, 
                    $showLogo, 
                    $showAlamat, 
                    $showTelp, 
                    $showEmail, 
                    '$footerText', 
                    NOW(), 
                    NOW()
                  )";
                  
        mysqli_query($conn, $query);
    }
    
    return mysqli_affected_rows($conn);
}
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Setting Pencetakan Nota</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Setting Nota</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <?php if($showPreview): ?>
    <!-- Preview Section -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Preview Nota - <?= $previewToko['toko_nama']; ?></h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div style="margin:0 auto; width: <?= $previewData['nota_lebar'] * 37.8; ?>px; border: 1px dashed #ccc; padding: <?= $previewData['nota_margin']; ?>mm; font-family: monospace; font-size: <?= $previewData['nota_font_size']; ?>pt;">
                  <!-- Header Nota -->
                  <div style="text-align: center; margin-bottom: 10px; min-height: <?= $previewData['nota_header_height']; ?>mm;">
                    <?php if($previewData['nota_show_logo'] == 1): ?>
                      <div style="margin-bottom: 5px;"><strong>[LOGO TOKO]</strong></div>
                    <?php endif; ?>
                    <div style="font-weight: bold; font-size: <?= $previewData['nota_font_size'] + 2; ?>pt;"><?= $previewToko['toko_nama']; ?></div>
                    <?php if($previewData['nota_show_alamat'] == 1): ?>
                      <div><?= $previewToko['toko_alamat']; ?>, <?= $previewToko['toko_kota']; ?></div>
                    <?php endif; ?>
                    <?php if($previewData['nota_show_telp'] == 1): ?>
                      <div>Telp: <?= $previewToko['toko_tlpn']; ?> / WA: <?= $previewToko['toko_wa']; ?></div>
                    <?php endif; ?>
                    <?php if($previewData['nota_show_email'] == 1): ?>
                      <div>Email: <?= $previewToko['toko_email']; ?></div>
                    <?php endif; ?>
                  </div>
                  
                  <!-- Garis Pemisah -->
                  <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                  
                  <!-- Informasi Nota -->
                  <div style="margin-bottom: 10px;">
                    <table style="width: 100%;">
                      <tr>
                        <td>No. Nota</td>
                        <td>: INV-123456</td>
                      </tr>
                      <tr>
                        <td>Tanggal</td>
                        <td>: <?= date('d/m/Y H:i'); ?></td>
                      </tr>
                      <tr>
                        <td>Kasir</td>
                        <td>: Admin</td>
                      </tr>
                      <tr>
                        <td>Pelanggan</td>
                        <td>: Customer</td>
                      </tr>
                    </table>
                  </div>
                  
                  <!-- Garis Pemisah -->
                  <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                  
                  <!-- Item Pembelian -->
                  <div style="margin-bottom: 10px;">
                    <table style="width: 100%;">
                      <tr>
                        <td colspan="4" style="border-bottom: 1px dashed #000;">Item</td>
                      </tr>
                      <tr>
                        <td style="width: 40%;">Produk ABC</td>
                        <td style="width: 20%; text-align: right;">2 x</td>
                        <td style="width: 20%; text-align: right;">25,000</td>
                        <td style="width: 20%; text-align: right;">50,000</td>
                      </tr>
                      <tr>
                        <td>Produk XYZ</td>
                        <td style="text-align: right;">1 x</td>
                        <td style="text-align: right;">30,000</td>
                        <td style="text-align: right;">30,000</td>
                      </tr>
                    </table>
                  </div>
                  
                  <!-- Garis Pemisah -->
                  <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                  
                  <!-- Total -->
                  <div style="margin-bottom: 10px;">
                    <table style="width: 100%;">
                      <tr>
                        <td style="width: 60%; text-align: right;">Subtotal</td>
                        <td style="width: 40%; text-align: right;">80,000</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">Diskon</td>
                        <td style="text-align: right;">0</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">Ongkir</td>
                        <td style="text-align: right;"><?= number_format($previewToko['toko_ongkir']); ?></td>
                      </tr>
                      <tr>
                        <td style="text-align: right; font-weight: bold;">TOTAL</td>
                        <td style="text-align: right; font-weight: bold;"><?= number_format(80000 + $previewToko['toko_ongkir']); ?></td>
                      </tr>
                    </table>
                  </div>
                  
                  <!-- Garis Pemisah -->
                  <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                  
                  <!-- Footer -->
                  <div style="text-align: center; margin-top: 10px;">
                    <?= $previewData['nota_footer_text']; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12 col-lg-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Pengaturan Format Pencetakan Nota</h3>
              </div>
              <!-- /.card-header -->
              
              <!-- Form Pilih Toko untuk ambil setting lebar nota -->
              <form role="form" action="" method="post" class="mb-0">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="toko_id">Pilih Toko untuk Mengambil Setting Lebar Nota</label>
                        <select name="toko_id" id="toko_id" class="form-control" required>
                          <option value="">-- Pilih Toko --</option>
                          <?php foreach($toko as $row) : ?>
                            <option value="<?= $row['toko_id']; ?>">
                              <?= $row['toko_nama']; ?> - <?= $row['toko_kota']; ?> (<?= $row['toko_cabang'] == 0 ? 'Pusat' : 'Cabang '.$row['toko_cabang']; ?>)
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                      <div class="form-group mb-0">
                        <button type="submit" name="pilih_toko" class="btn btn-info">Ambil Setting</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              
              <!-- Form Setting Nota -->
              <form role="form" action="" method="post">
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <input type="hidden" name="nota_id" value="<?= isset($notaSetting['nota_id']) ? $notaSetting['nota_id'] : ''; ?>">
                          <label for="nota_toko_id">Toko untuk Setting Nota</label>
                          <select name="nota_toko_id" id="nota_toko_id" class="form-control" required>
                            <option value="">-- Pilih Toko --</option>
                            <?php foreach($toko as $row) : ?>
                              <option value="<?= $row['toko_id']; ?>" <?= isset($notaSetting['nota_toko_id']) && $notaSetting['nota_toko_id'] == $row['toko_id'] ? 'selected' : ''; ?>>
                                <?= $row['toko_nama']; ?> - <?= $row['toko_kota']; ?> (<?= $row['toko_cabang'] == 0 ? 'Pusat' : 'Cabang '.$row['toko_cabang']; ?>)
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="nota_lebar">Lebar Nota (cm)</label>
                          <input type="number" name="nota_lebar" class="form-control" id="nota_lebar" value="<?= $notaLebar; ?>" step="0.1" min="5" max="21" required onkeypress="return hanyaAngkaDecimal(event)">
                          <small class="form-text text-muted">Nilai default diambil dari pengaturan toko</small>
                        </div>
                        <div class="form-group">
                          <label for="nota_font_size">Ukuran Font (pt)</label>
                          <input type="number" name="nota_font_size" class="form-control" id="nota_font_size" value="<?= isset($notaSetting['nota_font_size']) ? $notaSetting['nota_font_size'] : '9'; ?>" min="7" max="12" required onkeypress="return hanyaAngka(event)">
                        </div>
                        <div class="form-group">
                          <label for="nota_header_height">Tinggi Header (mm)</label>
                          <input type="number" name="nota_header_height" class="form-control" id="nota_header_height" value="<?= isset($notaSetting['nota_header_height']) ? $notaSetting['nota_header_height'] : '20'; ?>" min="10" max="50" required onkeypress="return hanyaAngka(event)">
                        </div>
                        <div class="form-group">
                          <label for="nota_margin">Margin (mm)</label>
                          <input type="number" name="nota_margin" class="form-control" id="nota_margin" value="<?= isset($notaSetting['nota_margin']) ? $notaSetting['nota_margin'] : '5'; ?>" min="3" max="15" required onkeypress="return hanyaAngka(event)">
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="nota_show_logo">Tampilkan Logo</label>
                          <select name="nota_show_logo" id="nota_show_logo" class="form-control">
                            <option value="1" <?= isset($notaSetting['nota_show_logo']) && $notaSetting['nota_show_logo'] == 1 ? 'selected' : ''; ?>>Ya</option>
                            <option value="0" <?= isset($notaSetting['nota_show_logo']) && $notaSetting['nota_show_logo'] == 0 ? 'selected' : ''; ?>>Tidak</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="nota_show_alamat">Tampilkan Alamat Toko</label>
                          <select name="nota_show_alamat" id="nota_show_alamat" class="form-control">
                            <option value="1" <?= isset($notaSetting['nota_show_alamat']) && $notaSetting['nota_show_alamat'] == 1 ? 'selected' : ''; ?>>Ya</option>
                            <option value="0" <?= isset($notaSetting['nota_show_alamat']) && $notaSetting['nota_show_alamat'] == 0 ? 'selected' : ''; ?>>Tidak</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="nota_show_telp">Tampilkan No. Telepon</label>
                          <select name="nota_show_telp" id="nota_show_telp" class="form-control">
                            <option value="1" <?= isset($notaSetting['nota_show_telp']) && $notaSetting['nota_show_telp'] == 1 ? 'selected' : ''; ?>>Ya</option>
                            <option value="0" <?= isset($notaSetting['nota_show_telp']) && $notaSetting['nota_show_telp'] == 0 ? 'selected' : ''; ?>>Tidak</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="nota_show_email">Tampilkan Email</label>
                          <select name="nota_show_email" id="nota_show_email" class="form-control">
                            <option value="1" <?= isset($notaSetting['nota_show_email']) && $notaSetting['nota_show_email'] == 1 ? 'selected' : ''; ?>>Ya</option>
                            <option value="0" <?= isset($notaSetting['nota_show_email']) && $notaSetting['nota_show_email'] == 0 ? 'selected' : ''; ?>>Tidak</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="nota_footer_text">Teks Footer Nota</label>
                          <textarea name="nota_footer_text" id="nota_footer_text" class="form-control" rows="3"><?= isset($notaSetting['nota_footer_text']) ? $notaSetting['nota_footer_text'] : 'Terima kasih atas kunjungan Anda'; ?></textarea>
                        </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-right">
                  <button type="submit" name="preview" class="btn btn-info mr-2">Preview</button>
                  <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

<?php include '_footer.php'; ?>

<script>
    // Fungsi untuk memvalidasi input angka
    function hanyaAngka(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
      return true;
    }
    
    // Fungsi untuk memvalidasi input angka desimal
    function hanyaAngkaDecimal(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode;
      if (charCode > 31 && (charCode != 46) && (charCode < 48 || charCode > 57))
        return false;
      return true;
    }
</script>