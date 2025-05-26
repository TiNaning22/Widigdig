<?php 
include 'aksi/koneksi.php';

// Get form data
$tanggal = $_POST['tanggal'];
$nama_kas = $_POST['nama_kas'];
$jenis_kas = $_POST['jenis_kas'];
$nominal = $_POST['nominal'];
$keterangan = $_POST['keterangan'];
$cabang = $_POST['cabang'];

// Validate input
if(empty($tanggal) || empty($nama_kas) || empty($jenis_kas) || empty($nominal) || empty($cabang) || empty($keterangan) ) {
    echo "
        <script>
            alert('Data tidak boleh kosong!');
            window.location.href = 'arus-stock';
        </script>
    ";
    exit;
}

// Insert data into database
$query = "INSERT INTO arus_stock (tanggal, nama_kas, jenis_kas, nominal, keterangan, cabang) 
          VALUES ('$tanggal', '$nama_kas', '$jenis_kas', '$nominal', '$keterangan', '$cabang')";

if(mysqli_query($conn, $query)) {
    echo "
        <script>
            alert('Data berhasil ditambahkan!');
            window.location.href = 'arus-stock';
        </script>
    ";
} else {
    echo "
        <script>
            alert('Gagal menambahkan data: " . mysqli_error($conn) . "');
            window.location.href = 'arus-stock';
        </script>
    ";
}

mysqli_close($conn);
?>