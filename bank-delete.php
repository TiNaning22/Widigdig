<?php 
session_start();
if (!isset($_SESSION['login'])) {
  header("Location: index.php");
  exit;
}
require 'functions.php';
$levelLogin = $_SESSION['login_level'];
if ($levelLogin === "kasir" || $levelLogin === "kurir") {  // Changed && to ||
  echo "
    <script>
      document.location.href = 'bo';
    </script>
  ";
  exit;
}

// Ambil id dari parameter URL
$id = $_GET["id"];

// Query untuk memastikan bank tidak memiliki transaksi terkait
$transaksi = mysqli_query($conn, "SELECT * FROM transaksi WHERE transaksi_bank = " . $id);
$jmlTransaksi = mysqli_num_rows($transaksi);

if ($jmlTransaksi > 0) {
  echo "
    <script>
      alert('Bank tidak dapat dihapus karena memiliki transaksi terkait');
      document.location.href = 'bank';  // Removed .php extension
    </script>
  ";
  exit;
} else {
  // Query untuk menghapus bank dengan id yang dipilih
  $hapus = mysqli_query($conn, "DELETE FROM bank WHERE bank_id = " . $id);
  
  if ($hapus) {
    echo "
      <script>
        alert('Bank berhasil dihapus');
        document.location.href = 'bank';  // Removed .php extension
      </script>
    ";
  } else {
    echo "
      <script>
        alert('Bank gagal dihapus');
        document.location.href = 'bank';  // Removed .php extension
      </script>
    ";
  }
}
?>