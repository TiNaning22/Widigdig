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
$barang = query("SELECT * FROM barang WHERE barang_id = $id")[0];

// Jika tombol submit ditekan
if (isset($_POST["submit"])) {
    $stock = $_POST["barang_stock"];
    $update = mysqli_query($conn, "UPDATE barang SET barang_stock = '$stock' WHERE barang_id = '$id'");

    if ($update) {
        echo "<script>
            alert('Stock berhasil diperbarui!');
            document.location.href = 'barang';
        </script>";
    } else {
        echo "<script>alert('Gagal memperbarui stock!');</script>";
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
                                <h3 class="card-title">Edit Stock</h3>
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
                                    <label for="barang_stock">Stock</label>
                                    <input type="number" name="barang_stock" class="form-control" id="barang_stock" value="<?= $barang['barang_stock']; ?>" required>
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
