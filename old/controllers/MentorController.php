<?php 
// File: controllers/AuthController.php

include dirname(__FILE__) . '/../services/database.php';
include dirname(__FILE__) . '/../models/MentorModel.php';

class AuthMentorController {
    protected $mentorModel;

    public function __construct() {
        $this->mentorModel = new MentorModel(); // Inisialisasi model
    }

    public function login($email, $password) {
        $mentor = $this->mentorModel->loginMentor($email, $password);
        if ($mentor) {
            $_SESSION['mentor_id'] = $mentor['id'];
            $_SESSION['mentor_email'] = $mentor['email'];
            $_SESSION['mentor_name'] = $mentor['name'];
            header('Location: ../mentor/mentor.php'); 
            exit();
        } else {
            return "Email atau Password salah!";
        }
    }

    // Fungsi untuk registrasi
    public function register($email, $name, $password, $password_confirm, $phone_number) {
        $message = $this->mentorModel->registerMentor($email, $name, $password, $password_confirm, $phone_number);
        return $message;
    }

    // Fungsi untuk logout
    public function logout() {
        session_start();
        session_destroy(); // Menghancurkan session untuk logout
        header('Location: ../auth/login/login.php'); // Sesuaikan path
        exit();
    }

    // Fungsi untuk menangani form login
    // Fungsi untuk menangani form login
    public function handleLoginForm($email, $password) {
        return $this->login($email, $password);
    }

    // Fungsi untuk menangani form register
    public function handleRegisterForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $name = $_POST['name'];
            $phone_number = $_POST['phone_number'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            $message = $this->register($email, $name, $password, $password_confirm, $phone_number);
            
            if ($message) {
                exit();
            }
        }
    }

    public function getMentorProfile($mentorId) {
        return $this->mentorModel->getMentorById($mentorId);
    }

    public function updateProfilePicture($mentorId, $profilePicture) {
        // Tentukan direktori upload
        $uploadDir = '../public/profile-picture/';
        $created_at = date('Y-m-d H:i:s');
        $updated_at = $created_at;

        // Buat direktori jika belum ada
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Izin penuh
        }

        // Pastikan file diupload
        if (!isset($profilePicture) || $profilePicture['error'] !== UPLOAD_ERR_OK) {
            return "Error: Upload gagal";
        }

        // Generate nama file unik
        $fileExtension = strtolower(pathinfo($profilePicture['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $newFileName;

        // Validasi tipe file
        $allowedTypes = ['jpeg', 'jpg', 'png', 'gif'];
        if (!in_array($fileExtension, $allowedTypes)) {
            return "Error: Tipe file tidak diizinkan. Gunakan JPEG, PNG, atau GIF";
        }

        // Validasi ukuran file (maksimal 5MB)
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        if ($profilePicture['size'] > $maxFileSize) {
            return "Error: Ukuran file terlalu besar. Maksimal 5MB";
        }

        // Memindahkan file
        if (move_uploaded_file($profilePicture['tmp_name'], $uploadPath)) {
            $relativePath = '../public/profile-picture/' . $newFileName;

            // Update path gambar di database
            if ($this->mentorModel->updateProfilePicture($mentorId, $relativePath, $updated_at)) {
                return true;
            } else {
                // Hapus file jika gagal update database
                unlink($uploadPath);
                return "Error: Gagal menyimpan di database";
            }
        } else {
            return "Error: Gagal memindahkan file";
        }
    }

    public function update($email, $name, $phone_number, $salary_recived, $salary_remaining, $mentorId) {
        $data = [
            'email' => $email,
            'name' => $name,
            'phone_number' => $phone_number,
            'salary_recived' => $salary_recived,
            'salary_remaining' => $salary_remaining,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->mentorModel->updateMentor($mentorId, $data);
    }
}
?>