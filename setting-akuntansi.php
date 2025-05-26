<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  // Remove the role restriction to allow all users to access this page
  // Process form submissions
  if(isset($_POST['saveGeneralSettings'])) {
    // Update general settings
    $periode_bulan = $_POST['periode_bulan'];
    $periode_tahun = $_POST['periode_tahun'];
    $tarif_pajak = $_POST['tarif_pajak'];
    
    // Check if settings exist first
    $checkSettings = $conn->query("SELECT * FROM settings_akuntansi WHERE sa_cabang = '".$sessionCabang."'");
    
    if(mysqli_num_rows($checkSettings) > 0) {
      // Update existing settings
      $updateSettings = $conn->query("UPDATE settings_akuntansi SET 
                                      sa_periode_bulan = '".$periode_bulan."',
                                      sa_periode_tahun = '".$periode_tahun."',
                                      sa_tarif_pajak = '".$tarif_pajak."'
                                      WHERE sa_cabang = '".$sessionCabang."'");
    } else {
      // Insert new settings
      $insertSettings = $conn->query("INSERT INTO settings_akuntansi 
                                     (sa_periode_bulan, sa_periode_tahun, sa_tarif_pajak, sa_cabang) 
                                     VALUES 
                                     ('".$periode_bulan."', '".$periode_tahun."', '".$tarif_pajak."', '".$sessionCabang."')");
    }
    
    echo "<script>alert('Pengaturan umum berhasil disimpan!');</script>";
  }
  
  // Process account form
  if(isset($_POST['saveAccount'])) {
    $kode_akun = $_POST['kode_akun'];
    $nama_akun = $_POST['nama_akun'];
    $kategori_akun = $_POST['kategori_akun'];
    $laporan_keuangan = $_POST['laporan_keuangan'];
    $saldo_normal = $_POST['saldo_normal'];
    
    // Check if account already exists
    $checkAccount = $conn->query("SELECT * FROM akun WHERE akun_kode = '".$kode_akun."' AND akun_cabang = '".$sessionCabang."'");
    
    if(mysqli_num_rows($checkAccount) > 0) {
      echo "<script>alert('Kode akun sudah digunakan!');</script>";
    } else {
      $insertAccount = $conn->query("INSERT INTO akun 
                                    (akun_kode, akun_nama, akun_kategori, akun_laporan_keuangan, akun_saldo_normal, akun_cabang) 
                                    VALUES 
                                    ('".$kode_akun."', '".$nama_akun."', '".$kategori_akun."', '".$laporan_keuangan."', '".$saldo_normal."', '".$sessionCabang."')");
      
      if($insertAccount) {
        echo "<script>alert('Akun berhasil ditambahkan!');</script>";
      } else {
        echo "<script>alert('Gagal menambahkan akun: " . mysqli_error($conn) . "');</script>";
      }
    }
  }
  
  // Process account update
  if(isset($_POST['updateAccount'])) {
    $akun_id = $_POST['akun_id'];
    $kode_akun = $_POST['kode_akun'];
    $nama_akun = $_POST['nama_akun'];
    $kategori_akun = $_POST['kategori_akun'];
    $laporan_keuangan = $_POST['laporan_keuangan'];
    $saldo_normal = $_POST['saldo_normal'];
    
    $updateAccount = $conn->query("UPDATE akun SET 
                                  akun_kode = '".$kode_akun."',
                                  akun_nama = '".$nama_akun."',
                                  akun_kategori = '".$kategori_akun."',
                                  akun_laporan_keuangan = '".$laporan_keuangan."',
                                  akun_saldo_normal = '".$saldo_normal."'
                                  WHERE akun_id = '".$akun_id."' AND akun_cabang = '".$sessionCabang."'");
    
    if($updateAccount) {
      echo "<script>alert('Akun berhasil diperbarui!');</script>";
    } else {
      echo "<script>alert('Gagal memperbarui akun: " . mysqli_error($conn) . "');</script>";
    }
  }
  
  // Process account deletion
  if(isset($_GET['deleteAkun']) && !empty($_GET['id'])) {
    $akun_id = $_GET['id'];
    
    // Check if account is in use
    $checkUsage = $conn->query("SELECT * FROM jurnal_detail WHERE jurnal_akun = '".$akun_id."'");
    
    if(mysqli_num_rows($checkUsage) > 0) {
      echo "<script>alert('Akun tidak dapat dihapus karena sudah digunakan dalam transaksi!');</script>";
    } else {
      $deleteAccount = $conn->query("DELETE FROM akun WHERE akun_id = '".$akun_id."' AND akun_cabang = '".$sessionCabang."'");
      
      if($deleteAccount) {
        echo "<script>alert('Akun berhasil dihapus!'); window.location.href='setting-akuntansi.php';</script>";
      } else {
        echo "<script>alert('Gagal menghapus akun: " . mysqli_error($conn) . "');</script>";
      }
    }
  }
  
  // Get current settings
  $settingsQuery = $conn->query("SELECT * FROM settings_akuntansi WHERE sa_cabang = '".$sessionCabang."'");
  $settings = mysqli_fetch_assoc($settingsQuery);
  
  // If no settings found, set defaults
  if(!$settings) {
    $settings = [
      'sa_periode_bulan' => date('m'),
      'sa_periode_tahun' => date('Y'),
      'sa_tarif_pajak' => 10
    ];
  }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Setting Sistem Akuntansi</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Setting Akuntansi</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Nav tabs -->
      <ul class="nav nav-tabs" id="settingTabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">Pengaturan Umum</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="accounts-tab" data-toggle="tab" href="#accounts" role="tab" aria-controls="accounts" aria-selected="false">Daftar Akun</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="add-account-tab" data-toggle="tab" href="#add-account" role="tab" aria-controls="add-account" aria-selected="false">Tambah Akun</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="reports-tab" data-toggle="tab" href="#reports" role="tab" aria-controls="reports" aria-selected="false">Laporan Keuangan</a>
        </li>
      </ul>

      <!-- Tab content -->
      <div class="tab-content">
        <!-- General Settings Tab -->
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Pengaturan Umum Akuntansi</h3>
            </div>
            <div class="card-body">
              <form action="" method="POST">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Periode Bulan</label>
                      <select name="periode_bulan" class="form-control">
                        <option value="01" <?= $settings['sa_periode_bulan'] == '01' ? 'selected' : ''; ?>>Januari</option>
                        <option value="02" <?= $settings['sa_periode_bulan'] == '02' ? 'selected' : ''; ?>>Februari</option>
                        <option value="03" <?= $settings['sa_periode_bulan'] == '03' ? 'selected' : ''; ?>>Maret</option>
                        <option value="04" <?= $settings['sa_periode_bulan'] == '04' ? 'selected' : ''; ?>>April</option>
                        <option value="05" <?= $settings['sa_periode_bulan'] == '05' ? 'selected' : ''; ?>>Mei</option>
                        <option value="06" <?= $settings['sa_periode_bulan'] == '06' ? 'selected' : ''; ?>>Juni</option>
                        <option value="07" <?= $settings['sa_periode_bulan'] == '07' ? 'selected' : ''; ?>>Juli</option>
                        <option value="08" <?= $settings['sa_periode_bulan'] == '08' ? 'selected' : ''; ?>>Agustus</option>
                        <option value="09" <?= $settings['sa_periode_bulan'] == '09' ? 'selected' : ''; ?>>September</option>
                        <option value="10" <?= $settings['sa_periode_bulan'] == '10' ? 'selected' : ''; ?>>Oktober</option>
                        <option value="11" <?= $settings['sa_periode_bulan'] == '11' ? 'selected' : ''; ?>>November</option>
                        <option value="12" <?= $settings['sa_periode_bulan'] == '12' ? 'selected' : ''; ?>>Desember</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Periode Tahun</label>
                      <select name="periode_tahun" class="form-control">
                        <?php
                          $currentYear = date('Y');
                          for($i = $currentYear - 5; $i <= $currentYear + 5; $i++) {
                            echo '<option value="'.$i.'" '.($settings['sa_periode_tahun'] == $i ? 'selected' : '').'>'.$i.'</option>';
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Tarif Pajak (%)</label>
                      <input type="number" name="tarif_pajak" class="form-control" value="<?= $settings['sa_tarif_pajak']; ?>" min="0" max="100">
                    </div>
                  </div>
                </div>
                <button type="submit" name="saveGeneralSettings" class="btn btn-primary">Simpan Pengaturan</button>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Accounts List Tab -->
        <div class="tab-pane fade" id="accounts" role="tabpanel" aria-labelledby="accounts-tab">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Daftar Akun</h3>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="accountsList" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="width: 5%">No</th>
                      <th style="width: 10%">Kode Akun</th>
                      <th style="width: 25%">Nama Akun</th>
                      <th style="width: 15%">Kategori</th>
                      <th style="width: 15%">Laporan Keuangan</th>
                      <th style="width: 10%">Saldo Normal</th>
                      <th style="width: 20%">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $akunQuery = $conn->query("SELECT * FROM akun WHERE akun_cabang = '".$sessionCabang."' ORDER BY akun_kode ASC");
                      $no = 1;
                      
                      while($akun = mysqli_fetch_assoc($akunQuery)) {
                        // Define category display text
                        $kategori = '';
                        switch($akun['akun_kategori']) {
                          case 1: $kategori = 'Aset'; break;
                          case 2: $kategori = 'Kewajiban'; break;
                          case 3: $kategori = 'Ekuitas'; break;
                          case 4: $kategori = 'Pendapatan'; break;
                          case 5: $kategori = 'Beban'; break;
                          default: $kategori = 'Lainnya'; break;
                        }
                        
                        // Define financial report category
                        $laporanKeuangan = '';
                        switch($akun['akun_laporan_keuangan']) {
                          case 1: $laporanKeuangan = 'Neraca'; break;
                          case 2: $laporanKeuangan = 'Laba Rugi'; break;
                          case 3: $laporanKeuangan = 'Arus Kas'; break;
                          default: $laporanKeuangan = '-'; break;
                        }
                        
                        // Define normal balance
                        $saldoNormal = $akun['akun_saldo_normal'] == 'D' ? 'Debit' : 'Kredit';
                    ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <td><?= $akun['akun_kode']; ?></td>
                      <td><?= $akun['akun_nama']; ?></td>
                      <td><?= $kategori; ?></td>
                      <td><?= $laporanKeuangan; ?></td>
                      <td><?= $saldoNormal; ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-warning" 
                                data-toggle="modal" 
                                data-target="#editAkunModal" 
                                data-id="<?= $akun['akun_id']; ?>"
                                data-kode="<?= $akun['akun_kode']; ?>"
                                data-nama="<?= $akun['akun_nama']; ?>"
                                data-kategori="<?= $akun['akun_kategori']; ?>"
                                data-laporan="<?= $akun['akun_laporan_keuangan']; ?>"
                                data-saldo="<?= $akun['akun_saldo_normal']; ?>">
                          Edit
                        </button>
                        <a href="?deleteAkun=1&id=<?= $akun['akun_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus akun ini?')">Hapus</a>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Add Account Tab -->
        <div class="tab-pane fade" id="add-account" role="tabpanel" aria-labelledby="add-account-tab">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Tambah Akun Baru</h3>
            </div>
            <div class="card-body">
              <form action="" method="POST">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Kode Akun</label>
                      <input type="text" name="kode_akun" class="form-control" placeholder="Contoh: 1-1000" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Nama Akun</label>
                      <input type="text" name="nama_akun" class="form-control" placeholder="Nama Akun" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Kategori Akun</label>
                      <select name="kategori_akun" class="form-control" required>
                        <option value="">- Pilih Kategori -</option>
                        <option value="1">Aset</option>
                        <option value="2">Kewajiban</option>
                        <option value="3">Ekuitas</option>
                        <option value="4">Pendapatan</option>
                        <option value="5">Beban</option>
                        <option value="6">Lainnya</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Laporan Keuangan</label>
                      <select name="laporan_keuangan" class="form-control" required>
                        <option value="">- Pilih Laporan -</option>
                        <option value="1">Neraca</option>
                        <option value="2">Laba Rugi</option>
                        <option value="3">Arus Kas</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Saldo Normal</label>
                      <select name="saldo_normal" class="form-control" required>
                        <option value="">- Pilih Saldo Normal -</option>
                        <option value="D">Debit</option>
                        <option value="K">Kredit</option>
                      </select>
                    </div>
                  </div>
                </div>
                <button type="submit" name="saveAccount" class="btn btn-primary">Simpan Akun</button>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Reports Tab -->
        <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Laporan Keuangan</h3>
      </div>
      <div class="card-body">
        <div class="row gap-3">
          <!-- Laba Rugi -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-success text-white">
                <h5 class="card-title">Laba Rugi</h5>
              </div>
              <div class="card-body">
                <p>Laporan aktivitas operasional perusahaan.</p>
                <form action="laporan-laba-rugi.php" method="GET" target="_blank">
                  <div class="form-group">
                    <label>Periode</label>
                    <div class="row">
                      <div class="col-md-6">
                        <input type="date" name="tanggal_awal" class="form-control" value="<?= date('Y-m-01'); ?>" required>
                      </div>
                      <div class="col-md-6">
                        <input type="date" name="tanggal_akhir" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                      </div>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-success mt-2">Lihat Laporan</button>
                </form>
              </div>
            </div>
          </div>

          <!-- Arus Kas -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-info text-white">
                <h5 class="card-title">Arus Kas</h5>
              </div>
              <div class="card-body">
                <p>Laporan arus masuk dan keluar kas perusahaan.</p>
                <form action="arus-stock" method="GET" target="_blank">
                  <div class="form-group">
                    <label>Periode</label>
                    <div class="row">
                      <div class="col-md-6">
                        <input type="date" name="tanggal_awal" class="form-control" value="<?= date('Y-m-01'); ?>" required>
                      </div>
                      <div class="col-md-6">
                        <input type="date" name="tanggal_akhir" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                      </div>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-info mt-2">Lihat Laporan</button>
                </form>
              </div>
            </div>
          </div>
        </div> <!-- End Row -->
      </div> <!-- End Card Body -->
    </div> <!-- End Card -->
  </div> <!-- End Container -->
</div> <!-- End Tab Pane -->

      </div>
    </div>
  </section>
</div>

<!-- Edit Account Modal -->
<div class="modal fade" id="editAkunModal" tabindex="-1" role="dialog" aria-labelledby="editAkunModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editAkunModalLabel">Edit Akun</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST">
        <div class="modal-body">
          <input type="hidden" name="akun_id" id="edit_akun_id">
          <div class="form-group">
            <label>Kode Akun</label>
            <input type="text" name="kode_akun" id="edit_kode_akun" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Nama Akun</label>
            <input type="text" name="nama_akun" id="edit_nama_akun" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Kategori Akun</label>
            <select name="kategori_akun" id="edit_kategori_akun" class="form-control" required>
              <option value="1">Aset</option>
              <option value="2">Kewajiban</option>
              <option value="3">Ekuitas</option>
              <option value="4">Pendapatan</option>
              <option value="5">Beban</option>
              <option value="6">Lainnya</option>
            </select>
          </div>
          <div class="form-group">
            <label>Laporan Keuangan</label>
            <select name="laporan_keuangan" id="edit_laporan_keuangan" class="form-control" required>
              <option value="1">Neraca</option>
              <option value="2">Laba Rugi</option>
              <option value="3">Arus Kas</option>
            </select>
          </div>
          <div class="form-group">
            <label>Saldo Normal</label>
            <select name="saldo_normal" id="edit_saldo_normal" class="form-control" required>
              <option value="D">Debit</option>
              <option value="K">Kredit</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" name="updateAccount" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    // Initialize DataTable
    $("#accountsList").DataTable();
    
    // Handle account edit modal
    $('#editAkunModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var akun_id = button.data('id');
      var kode_akun = button.data('kode');
      var nama_akun = button.data('nama');
      var kategori_akun = button.data('kategori');
      var laporan_keuangan = button.data('laporan');
      var saldo_normal = button.data('saldo');
      
      var modal = $(this);
      modal.find('#edit_akun_id').val(akun_id);
      modal.find('#edit_kode_akun').val(kode_akun);
      modal.find('#edit_nama_akun').val(nama_akun);
      modal.find('#edit_kategori_akun').val(kategori_akun);
      modal.find('#edit_laporan_keuangan').val(laporan_keuangan);
      modal.find('#edit_saldo_normal').val(saldo_normal);
    });
    
    // Set active tab based on URL hash
    var url = document.location.toString();
    if (url.match('#')) {
      $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    } 

    // Change hash for page-reload
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
      window.location.hash = e.target.hash;
    });
  });
</script>
</body>
</html>