<?php
// File: models/ArtikelModel.php

require_once dirname(__FILE__) . '/../services/database.php';  

class ArtikelModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getAllArtikel() {
        $query = "SELECT * FROM artikels";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            error_log("Query error: " . mysqli_error($this->conn));
            return [];
        }

        $artikel = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $artikel[] = $row;
        }

        return $artikel;
    }

    public function getArtikelById($artikelId) {
        $query = "SELECT * FROM artikels WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $artikelId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function insertArtikel($title, $subtitle, $content, $imagePath, $createdAt, $updatedAt) {
        $query = "INSERT INTO artikels (title, subtitle, content, image, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $title, $subtitle, $content, $imagePath, $createdAt, $updatedAt);
        return mysqli_stmt_execute($stmt);
    }

    public function updateArtikel($artikelId, $title, $subtitle, $content, $imagePath, $updatedAt) {
        $query = "UPDATE artikels 
                  SET title = ?, subtitle = ?, content = ?, image = ?, updated_at = ? 
                  WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssi", $title, $subtitle, $content, $imagePath, $updatedAt, $artikelId);
    
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Error executing update query: " . mysqli_error($this->conn));
            return false;
        }
    
        return true;
    }

    public function deleteArtikel($artikelId) {
        $query = "DELETE FROM artikels WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $artikelId);
        return mysqli_stmt_execute($stmt);
    }
}
?>