<?php
// Path: views/pages/payment/succes.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/pages/login/login.php");
    exit;
}

$courseName = isset($_GET['course_name']) ? htmlspecialchars($_GET['course_name']) : 'Course';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Sukses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5 text-center">
        <div class="alert alert-success" role="alert">
            <h2>Transfer Berhasil</h2>
            <p>Pembayaran untuk kursus <strong><?php echo $courseName; ?></strong> telah diterima.</p>
            <p>Tim kami akan segera memverifikasi bukti transfer Anda.</p>
            <a href="/views/pages/dashboard/index.php" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>