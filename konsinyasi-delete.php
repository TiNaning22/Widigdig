<?php
include 'koneksi.php'; // Sesuaikan dengan file koneksi Anda

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus data
    $query = "DELETE FROM penerimaan_konsinyasi WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "
          <script>
            alert('Data berhasil dihapus.');
            window.location.href = 'penerimaan-konsinyasi.php';
          </script>
        ";
    } else {
        echo "
          <script>
            alert('Gagal menghapus data: " . mysqli_error($conn) . "');
            window.location.href = 'penerimaan-konsinyasi.php';
          </script>
        ";
    }
} else {
    echo "
      <script>
        alert('ID tidak valid.');
        window.location.href = 'penerimaan-konsinyasi.php';
      </script>
    ";
}
?>