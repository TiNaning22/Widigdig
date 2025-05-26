<?php 
  // Aktifkan error reporting untuk debugging
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  
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

  // Pastikan ID valid dan penanganan error jika ID tidak valid
  if(isset($_GET['id']) && !empty($_GET['id'])) {
    try {
      $id = abs((int)base64_decode($_GET['id']));
      
      // Cek apakah ID valid
      $barang_check = query("SELECT COUNT(*) as count FROM barang WHERE barang_id = $id")[0];
      if($barang_check['count'] == 0) {
        echo "<script>alert('Data produk tidak ditemukan!'); window.location.href='barang';</script>";
        exit;
      }
      
      $barang = query("SELECT * FROM barang WHERE barang_id = $id")[0];
    } catch(Exception $e) {
      echo "<script>alert('ID tidak valid!'); window.location.href='barang';</script>";
      exit;
    }
  } else {
    echo "<script>alert('ID tidak ditemukan!'); window.location.href='barang';</script>";
    exit;
  }

  // Proses Generate Barcode
  try {
    require_once "vendor/barcode/autoload.php";
    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
    
    // Buat nama file yang aman untuk sistem file
    $nama_file = preg_replace('/[^A-Za-z0-9\-]/', '', $barang["barang_nama"]);
    $barcode_path = 'vendor/barcode/img/'.$barang["barang_kode"].'-produk-'.$nama_file.'-cabang-'.$sessionCabang.'.png';
    
    // Generate barcode
    $barcode_data = $generator->getBarcode($barang["barang_kode"], $generator::TYPE_CODE_128, 3, 50);
    
    // Cek apakah direktori ada dan dapat ditulis
    $dir = 'vendor/barcode/img/';
    if (!file_exists($dir)) {
      mkdir($dir, 0755, true);
    }
    
    if (!is_writable($dir)) {
      echo "<script>alert('Direktori $dir tidak dapat ditulis. Periksa permission!');</script>";
    } else {
      file_put_contents($barcode_path, $barcode_data);
    }
  } catch(Exception $e) {
    echo "<script>alert('Error saat generate barcode: " . $e->getMessage() . "');</script>";
  }
?>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1>Generate Barcode <b>Produk <?= htmlspecialchars($barang['barang_nama']); ?></b></h1>
        </div>
        <div class="col-sm-4">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Barcode</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Barcode</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 col-lg-6">
                  <div class="detail-barcode-box" id="detail-barcode-box">
                    <b class="title-barcode-box"><?= htmlspecialchars($barang["barang_nama"]); ?></b><br>
                    
                    <?php if(file_exists($barcode_path)): ?>
                      <img src="<?= $barcode_path; ?>?v=<?= time(); ?>" alt="Barcode Produk <?= htmlspecialchars($barang["barang_nama"]); ?>" class="img-fluid">
                    <?php else: ?>
                      <div class="alert alert-danger">Barcode image tidak dapat ditemukan!</div>
                    <?php endif; ?>
                    
                    <div class="row">
                      <div class="col-3">
                        <b>Rp</b>
                      </div>

                      <div class="col-9">
                        <b style="float: right;"><?= number_format($barang["barang_harga"], 0, ',', '.'); ?></b>
                      </div>
                    </div>
                  </div>

                  <div class="card-footer text-right">
                    <input id="btn_convert" class="btn btn-primary" type="button" value="Download" />
                  </div>
                  <br>
                </div>

                <div class="col-md-6 col-lg-6">
                  <form action="barang-generate-barcode-lots" method="post" target="_blank">
                    <div class="form-group">
                      <label for="barang_kode">Cetak Barcode Sesuai Jumlah Keinginan</label>
                      <input type="number" name="input_barcode" class="form-control" id="barang_kode" placeholder="Contoh: 26" required>
                      <input type="hidden" name="input_kode" value="<?= htmlspecialchars($barang['barang_kode']); ?>">
                    </div>
                    <div class="card-footer text-right">
                      <button type="submit" name="submit" class="btn btn-primary">Generate Barcode</button>
                    </div>
                    <br>
                  </form>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include '_footer.php'; ?>
<!-- Pastikan jQuery sudah dimuat sebelum script html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  // Tambahkan listener setelah DOM selesai dimuat
  document.getElementById("btn_convert").addEventListener("click", function() {
    console.log("Button diklik");
    
    try {
      html2canvas(document.getElementById("detail-barcode-box"), {
        allowTaint: true,
        useCORS: true,
        logging: true,
        backgroundColor: "#ffffff"
      }).then(function(canvas) {
        console.log("Canvas berhasil dibuat");
        var anchorTag = document.createElement("a");
        document.body.appendChild(anchorTag);
        anchorTag.download = "<?= preg_replace('/[^A-Za-z0-9\-]/', '', $barang["barang_nama"]); ?>-kode-barcode-<?= $barang["barang_kode"]; ?>-cabang-<?= $sessionCabang; ?>.jpg";
        anchorTag.href = canvas.toDataURL("image/jpeg", 0.9);
        anchorTag.click();
        document.body.removeChild(anchorTag);
      }).catch(function(error) {
        console.error("Error saat membuat canvas:", error);
        alert("Terjadi kesalahan saat mengkonversi barcode: " + error.message);
      });
    } catch(e) {
      console.error("Error saat eksekusi html2canvas:", e);
      alert("Terjadi kesalahan: " + e.message);
    }
  });
});
</script>