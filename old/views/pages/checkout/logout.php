<?php
// File: views/pages/checkout/logout.php

session_start();
session_unset(); // Hapus semua variabel session
session_destroy(); // Hapus session
header("Location: index.php"); // Redirect ke halaman utama
exit;
?>
