<?php
// File: models/CatatanModel.php

require_once dirname(__FILE__) . '/../services/database.php';  

class CatatanModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getAllCatatan() {
        $query = "SELECT * FROM catatans";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            error_log("Query error: " . mysqli_error($this->conn));
            return [];
        }

        $catatan = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $catatan[] = $row;
        }

        return $catatan;
    }

    public function getCatatanById($catatanId) {
        $query = "SELECT * FROM catatans WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $catatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function insertCatatan($mentorId ,$title, $content, $createdAt, $updatedAt) {
        $query = "INSERT INTO catatans (mentor_id , title, content, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "issss", $mentorId ,$title, $content, $createdAt, $updatedAt);
        return mysqli_stmt_execute($stmt);
    }

    public function updateCatatan($catatanId, $title, $content, $updatedAt) {
        $query = "UPDATE catatans 
                  SET title = ?, content = ?, updated_at = ? 
                  WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $title, $content, $updatedAt, $catatanId);
    
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Error executing update query: " . mysqli_error($this->conn));
            return false;
        }
    
        return true;
    }

    public function deleteCatatan($catatanId) {
        $query = "DELETE FROM catatans WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $catatanId);
        return mysqli_stmt_execute($stmt);
    }
}
?>
