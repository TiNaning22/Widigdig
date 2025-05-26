<?php
// File: models/UserModel.php

require_once dirname(__FILE__) . '/../services/database.php';

class UserModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getUserById($userId) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function updateProfilePicture($userId, $profilePicturePath, $updated_at) {
        $query = "UPDATE users SET profile_picture = ?, updated_at = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $profilePicturePath, $updated_at, $userId);
        return mysqli_stmt_execute($stmt);
    }
}
?>
