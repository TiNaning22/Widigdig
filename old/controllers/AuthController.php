<?php 
// File: controllers/AuthController.php

include dirname(__FILE__) . '/../services/database.php';
include dirname(__FILE__) . '/../models/AuthModel.php';

class AuthController {
    protected $authModel;

    public function __construct() {
        $this->authModel = new AuthModel(); // Inisialisasi model
    }

    // Fungsi untuk login
    public function login($email, $password) {
        $user = $this->authModel->loginUser($email, $password);
        if ($user) {
            session_start(); // Pastikan memulai session sebelum set session
            // Setelah login berhasil, simpan data pengguna ke session
            $_SESSION['user_id'] = $user['id']; // ID pengguna
            $_SESSION['user_email'] = $user['email']; // Email pengguna
            $_SESSION['user_name'] = $user['name']; // Nama pengguna
            header('Location: ../home/home.php'); // Sesuaikan path
            exit();
        } else {
            return "Email atau Password salah!";
        }
    }

    // Fungsi untuk registrasi
    public function register($email, $name, $password, $password_confirm, $phone_number) {
        $message = $this->authModel->registerUser($email, $name, $password, $password_confirm, $phone_number);
        return $message;
    }

  // Fungsi untuk logout
public function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Pastikan session dimulai jika belum
    }   
    session_unset(); // Hapus semua variabel session
    session_destroy(); // Hancurkan session
    header('Location: ../login/login.php'); // Redirect ke halaman login
    exit(); // Pastikan eksekusi berhenti setelah redirect
}


    // Fungsi untuk menangani form login
    public function handleLoginForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $message = $this->login($email, $password);
            
            if ($message) {
                header("Location: login.php?message=" . urlencode($message));
                exit();
            }
        }
    }

    // Fungsi untuk menangani form register
    public function handleRegisterForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $name = $_POST['name'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            $phone_number = $_POST['phone_number'];
            $message = $this->register($email, $name, $password, $password_confirm, $phone_number);
            
            header("Location: register.php?message=" . urlencode($message));
            exit();
        }
    }
}
?>