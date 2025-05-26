<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
  if ( $levelLogin === "kurir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }
?>

<?php
// Process add operation
if (isset($_POST["tambah"])) {
  $produkNama = htmlspecialchars($_POST['produk_nama']);
  $tanggal = htmlspecialchars($_POST['tanggal']);
  $stokAwal = htmlspecialchars($_POST['stok_awal']);
  $stokMasuk = htmlspecialchars($_POST['stok_masuk']);
  $stokKeluar = htmlspecialchars($_POST['stok_keluar']);
  $sisaStok = $stokAwal + $stokMasuk - $stokKeluar;
  $keterangan = htmlspecialchars($_POST['keterangan']);
  
  $query = "INSERT INTO stok_kartu (
              produk_nama,
              stok_kartu_tanggal,
              stok_kartu_stok_awal,
              stok_kartu_masuk,
              stok_kartu_keluar,
              stok_kartu_sisa,
              stok_kartu_keterangan,
              stok_kartu_cabang
            ) VALUES (
              '$produkNama',
              '$tanggal',
              '$stokAwal',
              '$stokMasuk',
              '$stokKeluar',
              '$sisaStok',
              '$keterangan',
              '$sessionCabang'
            )";
  
  $tambah = mysqli_query($conn, $query);
  
  if ($tambah) {
    echo "
      <script>
        alert('Data stok kartu berhasil ditambahkan');
        document.location.href = 'stok-kartu';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('Data stok kartu gagal ditambahkan');
        document.location.href = 'stok-kartu';
      </script>
    ";
  }
}

// Process edit operation
if (isset($_POST["edit"])) {
  $id = htmlspecialchars($_POST['id']);
  $produkNama = htmlspecialchars($_POST['produk_nama']);
  $tanggal = htmlspecialchars($_POST['tanggal']);
  $stokAwal = htmlspecialchars($_POST['stok_awal']);
  $stokMasuk = htmlspecialchars($_POST['stok_masuk']);
  $stokKeluar = htmlspecialchars($_POST['stok_keluar']);
  $sisaStok = $stokAwal + $stokMasuk - $stokKeluar;
  $keterangan = htmlspecialchars($_POST['keterangan']);
  
  $query = "UPDATE stok_kartu SET 
              produk_nama = '$produkNama',
              stok_kartu_tanggal = '$tanggal',
              stok_kartu_stok_awal = '$stokAwal',
              stok_kartu_masuk = '$stokMasuk',
              stok_kartu_keluar = '$stokKeluar',
              stok_kartu_sisa = '$sisaStok',
              stok_kartu_keterangan = '$keterangan'
            WHERE stok_kartu_id = '$id'";
  
  $edit = mysqli_query($conn, $query);
  
  if ($edit) {
    echo "
      <script>
        alert('Data stok kartu berhasil diupdate');
        document.location.href = 'stok-kartu';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('Data stok kartu gagal diupdate');
        document.location.href = 'stok-kartu';
      </script>
    ";
  }
}

// Process delete operation
if (isset($_GET["delete"])) {
  $id = htmlspecialchars($_GET["delete"]);
  
  // Make sure the ID is a valid integer
  if (is_numeric($id)) {
    // Convert to integer to prevent SQL injection
    $id = (int)$id;
    
    // Check if the record belongs to the current branch
    $check = mysqli_query($conn, "SELECT * FROM stok_kartu WHERE stok_kartu_id = $id AND stok_kartu_cabang = $sessionCabang");
    
    if (mysqli_num_rows($check) > 0) {
      $hapus = mysqli_query($conn, "DELETE FROM stok_kartu WHERE stok_kartu_id = $id");
      
      if ($hapus) {
        echo "
          <script>
            alert('Data stok kartu berhasil dihapus');
            document.location.href = 'kartu-stock';
          </script>
        ";
      } else {
        echo "
          <script>
            alert('Data stok kartu gagal dihapus: " . mysqli_error($conn) . "');
            document.location.href = 'kartu-stock';
          </script>
        ";
      }
    } else {
      echo "
        <script>
          alert('Data tidak ditemukan atau tidak memiliki akses');
          document.location.href = 'kartu-stock';
        </script>
      ";
    }
  } else {
    echo "
      <script>
        alert('ID tidak valid');
        document.location.href = 'kartu-stock';
      </script>
    ";
  }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Kartu Stok Obat</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Kartu Stok</li>
          </ol>
        </div>
        <div class="tambah-data">
          <a href="stock-kartu-add" class="btn btn-primary">Tambah Data</a>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <?php  
    $data = query("SELECT * FROM stok_kartu
                  WHERE stok_kartu_cabang = $sessionCabang 
                  ORDER BY stok_kartu_tanggal DESC, stok_kartu_id DESC");
  ?>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Kartu Stok Obat <small class="text-danger">(Laporan Wajib Dinas Kesehatan)</small></h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-auto">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th style="width: 5%;">No.</th>
                  <th>Nama Obat</th>
                  <th>Tanggal Transaksi</th>
                  <th>Stok Awal</th>
                  <th>Jumlah Barang Masuk</th>
                  <th>Jumlah Barang Keluar</th>
                  <th>Sisa Stok</th>
                  <th>Keterangan</th>
                  <th style="text-align: center; width: 14%;">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1; ?>
                <?php foreach ( $data as $row ) : ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $row['produk_nama']; ?></td>
                  <td><?= date('d/m/Y', strtotime($row['stok_kartu_tanggal'])); ?></td>
                  <td><?= number_format($row['stok_kartu_stok_awal']); ?></td>
                  <td><?= number_format($row['stok_kartu_masuk']); ?></td>
                  <td><?= number_format($row['stok_kartu_keluar']); ?></td>
                  <td><?= number_format($row['stok_kartu_sisa']); ?></td>
                  <td><?= $row['stok_kartu_keterangan']; ?></td>
                  <td class="orderan-online-button">
                    <?php $id = $row["stok_kartu_id"]; ?>
                    <a href="stok-kartu-edit?id=<?= $id; ?>" title="Edit Data">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-edit"></i>
                        </button>
                    </a>
                
                    <a href="kartu-stock?delete=<?= $id; ?>" onclick="return confirm('Yakin dihapus ?')" title="Delete Data">
                        <button class="btn btn-danger" type="submit">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    </a>
                  </td>
                </tr>
                <?php $i++; ?>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>

<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- page script -->
<script>
  $(function () {
    $("#example1").DataTable();
  });
</script>