<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
  if ( $levelLogin === "kasir" && $levelLogin === "kurir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }
?>

<?php
// ambil data di URL
$id = $_GET["id"];

// query data pasien berdasarkan id
$pasien = query("SELECT * FROM pasien WHERE pasien_id = $id")[0];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Detail Pasien</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Pasien</li>
            <li class="breadcrumb-item active">Detail</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Informasi Pasien</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <table class="table table-bordered">
                    <tr>
                      <td width="30%"><b>Kode Pasien</b></td>
                      <td width="70%"><?= $pasien['pasien_kode']; ?></td>
                    </tr>
                    <tr>
                      <td><b>Nama Pasien</b></td>
                      <td><?= $pasien['pasien_nama']; ?></td>
                    </tr>
                    <tr>
                      <td><b>Alamat</b></td>
                      <td><?= $pasien['pasien_alamat']; ?></td>
                    </tr>
                    <tr>
                      <td><b>No. HP</b></td>
                      <td><?= $pasien['pasien_hp']; ?></td>
                    </tr>
                  </table>
                </div>
                <div class="col-md-6">
                  <table class="table table-bordered">
                    <tr>
                      <td width="30%"><b>Email</b></td>
                      <td width="70%"><?= $pasien['pasien_email']; ?></td>
                    </tr>
                    <tr>
                      <td><b>Kota</b></td>
                      <td><?= $pasien['pasien_kota']; ?></td>
                    </tr>
                    <tr>
                      <td><b>Kode Pos</b></td>
                      <td><?= $pasien['pasien_kodepos']; ?></td>
                    </tr>
                    <tr>
                      <td><b>Status</b></td>
                      <td>
                        <?php if ($pasien['pasien_status'] === "1") : ?>
                          <span class="badge badge-success">Aktif</span>
                        <?php else : ?>
                          <span class="badge badge-danger">Tidak Aktif</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer text-right">
              <a href="pasien" class="btn btn-default">Kembali</a>
              <a href="pasien-edit?id=<?= $pasien['pasien_id']; ?>" class="btn btn-primary">Edit Data</a>
            </div>
          </div>

          <!-- Riwayat Tindakan Pasien -->
          <?php  
            $dataTindakan = query("SELECT * FROM tindakan 
                                   JOIN users ON tindakan.tindakan_user = users.user_id
                                   WHERE tindakan_pasien = $id 
                                   ORDER BY tindakan_id DESC");
          ?>
          
          <?php if (!empty($dataTindakan)) : ?>
          <div class="card card-info">
            <div class="card-header">
              <h3 class="card-title">Riwayat Tindakan</h3>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Tanggal</th>
                      <th>Tindakan</th>
                      <th>Dokter</th>
                      <th>Catatan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($dataTindakan as $row) : ?>
                    <tr>
                      <td><?= $i; ?></td>
                      <td><?= date('d/m/Y', strtotime($row['tindakan_tanggal'])); ?></td>
                      <td><?= $row['tindakan_nama']; ?></td>
                      <td><?= $row['user_nama']; ?></td>
                      <td><?= $row['tindakan_catatan']; ?></td>
                    </tr>
                    <?php $i++; ?>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include '_footer.php'; ?>