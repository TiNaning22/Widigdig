<?php 
include '_header.php';
include '_nav.php';
include '_sidebar.php'; 

if ($levelLogin === "kasir" || $levelLogin === "kurir") {
    echo "<script>document.location.href = 'bo';</script>";
    exit;
}

// Tambah Data
if (isset($_POST["submit"])) {
    $nama_user = htmlspecialchars($_POST["nama_user"]);
    $jam_masuk = $_POST["jam_masuk"];
    $jam_pulang = $_POST["jam_pulang"];
    
    $query = "INSERT INTO penutupan_shift (nama_user, jam_masuk, jam_pulang) 
              VALUES ('$nama_user', '$jam_masuk', '$jam_pulang')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil ditambahkan'); window.location='penutupan-shift.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data');</script>";
    }
}

// Hapus Data
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $hapus = mysqli_query($conn, "DELETE FROM penutupan_shift WHERE id = $id");
    
    if ($hapus) {
        echo "<script>alert('Data berhasil dihapus'); window.location='penutupan-shift.php';</script>";
    } else {
        echo "<script>alert('Data gagal dihapus');</script>";
    }
}

// Ambil Data
$data = query("SELECT * FROM penutupan_shift ORDER BY id DESC");
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Penutupan Shift</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="bo">Home</a></li>
                        <li class="breadcrumb-item active">Penutupan Shift</li>
                    </ol>
                </div>
                <div class="tambah-data">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">Tambah Data</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Penutupan Shift</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama User</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($data as $row) : ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= htmlspecialchars($row['nama_user']); ?></td>
                                        <td><?= $row['jam_masuk']; ?></td>
                                        <td><?= $row['jam_pulang']; ?></td>
                                        <td><?= $row['tanggal']; ?></td>
                                        <td>
                                            <?php $id = $row["id"]; ?>
                                          
                                            <a href="penutupan-shift.php?delete=<?= $id; ?>" onclick="return confirm('Yakin dihapus?')" title="Hapus Data">
                                                <button class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah Data -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah Data Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama User</label>
                        <input type="text" name="nama_user" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jam Pulang</label>
                        <input type="time" name="jam_pulang" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '_footer.php'; ?>

<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
    $(function () {
        $("#example1").DataTable();
    });
</script>
</body>
</html>
