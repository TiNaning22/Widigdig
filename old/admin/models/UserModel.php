<?php
// File: models/UserModel.php

require_once dirname(__FILE__) . '/../services/database.php';

class UserModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getUserById($adminId) {
        $query = "SELECT * FROM admins WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $adminId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function updateProfilePicture($adminId, $profilePicturePath, $updated_at) {
        $query = "UPDATE admins SET profile_picture = ?, updated_at = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $profilePicturePath, $updated_at, $adminId);
        return mysqli_stmt_execute($stmt);
    }

    public function getTotalUser() {
        $query = "SELECT COUNT(*) as total FROM users";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }
    
}
?>
