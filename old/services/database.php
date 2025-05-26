<?php
// File: services/database.php
$hostname = "localhost";
$username = "bpkbautodigital_psikolog";
$password = "bpkbautodigital_Admin";
$dbname = "bpkbautodigital_kelas";

// Membuat koneksi menggunakan mysqli (Object-Oriented)
$conn = mysqli_connect($hostname, $username, $password, $dbname);

// Cek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
