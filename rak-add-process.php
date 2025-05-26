<?php
session_start();
include '_header.php';

// Validate inputs
if (empty($_POST['nama_rak'])) {
  echo "
    <script>
      alert('Nama rak tidak boleh kosong');
      window.location='rak.php';
    </script>
  ";
  exit;
}

// Get store ID from session or form
$toko_id = isset($_SESSION['toko_id']) ? $_SESSION['toko_id'] : null;
if (!$toko_id && isset($_POST['toko_id'])) {
    $toko_id = (int)$_POST['toko_id'];
}

if (!$toko_id) {
  echo "
    <script>
      alert('Toko ID tidak valid. Silakan pilih toko.');
      window.location='rak.php';
    </script>
  ";
  exit;
}

// Log for debugging
error_log("Processing rack add with toko_id: " . $toko_id);

$nama_rak = htmlspecialchars($_POST['nama_rak']);
$created = date('Y-m-d H:i:s');

// Get branch info from toko table
$checkStoreQuery = "SELECT toko_cabang FROM toko WHERE toko_id = ?";
$stmtStore = $conn->prepare($checkStoreQuery);
$stmtStore->bind_param("i", $toko_id);
$stmtStore->execute();
$storeResult = $stmtStore->get_result();

if ($storeResult->num_rows === 0) {
  echo "
    <script>
      alert('Toko tidak ditemukan. ID: " . $toko_id . "');
      window.location='rak.php';
    </script>
  ";
  $stmtStore->close();
  exit;
}

// Fetch branch ID from store data
$storeData = $storeResult->fetch_assoc();
$cabang = (int)$storeData['toko_cabang'];
$stmtStore->close();

// Insert data
$sql = "INSERT INTO rak (nama_rak, cabang, toko_id, created_at) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siis", $nama_rak, $cabang, $toko_id, $created);

if ($stmt->execute()) {
  echo "
    <script>
      alert('Data rak berhasil ditambahkan');
      window.location='rak.php';
    </script>
  ";
} else {
  echo "
    <script>
      alert('Data rak gagal ditambahkan. Error: " . $stmt->error . "');
      window.location='rak.php';
    </script>
  ";
}

$stmt->close();
$conn->close();
?>