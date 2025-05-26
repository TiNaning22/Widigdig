<?php
// File: controllers/UserController.php

require_once dirname(__FILE__) . '/../models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function getUserProfile($userId) {
        return $this->userModel->getUserById($userId);
    }

    public function updateProfilePicture($userId, $profilePicture) {
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
            if ($this->userModel->updateProfilePicture($userId, $relativePath, $updated_at)) {
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
}
?>