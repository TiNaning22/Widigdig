<?php 
session_start();
if (!isset($_SESSION['login'])) {
  header("Location: index.php");
  exit;
}

require 'functions.php';

$levelLogin = $_SESSION['login_level'];
if ($levelLogin === "kasir" && $levelLogin === "kurir") {
  echo "
    <script>
      document.location.href = 'bo';
    </script>
  ";
  exit;
}

// Ambil id dari parameter URL
$id = $_GET["id"];

// Query untuk memastikan pasien tidak memiliki tindakan terkait
$tindakan = mysqli_query($conn, "SELECT * FROM tindakan WHERE tindakan_pasien = " . $id);
$jmlTindakan = mysqli_num_rows($tindakan);

if ($jmlTindakan > 0) {
  echo "
    <script>
      alert('Pasien tidak dapat dihapus karena memiliki tindakan terkait!');
      document.location.href = 'pasien';
    </script>
  ";
  exit;
}

// Jika tidak ada tindakan terkait, lakukan penghapusan
if (hapusPasien($id) > 0) {
  echo "
    <script>
      alert('Data berhasil dihapus!');
      document.location.href = 'pasien';
    </script>
  ";
} else {
  echo "
    <script>
      alert('Data gagal dihapus!');
      document.location.href = 'pasien';
    </script>
  ";
}

function hapusPasien($id) {
  global $conn;
  mysqli_query($conn, "DELETE FROM pasien WHERE pasien_id = $id");
  return mysqli_affected_rows($conn);
}
?>