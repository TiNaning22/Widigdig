<?php
// File: models/BookModel.php

require_once dirname(__FILE__) . '/../services/database.php';

class BookModel
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function getAllBooks()
    {
        $query = "SELECT * FROM books";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            error_log("Query error: " . mysqli_error($this->conn));
            return [];
        }

        $books = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }

        return $books;
    }

    public function getBookById($bookId)
    {
        $query = "SELECT * FROM books WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $bookId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function insertBook($title, $description, $ebookFilePath, $imagePath, $rating, $createdAt, $updatedAt)
    {
        $query = "INSERT INTO books (title, description, ebook_file, image, rating, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssss", $title, $description, $ebookFilePath, $imagePath, $rating, $createdAt, $updatedAt);
        return mysqli_stmt_execute($stmt);
    }

    public function updateBook($bookId, $title, $description, $ebookFilePath, $imagePath, $rating, $updatedAt)
    {
        $query = "UPDATE books 
                  SET title = ?, description = ?, ebook_file = ?, image = ?, rating = ?, updated_at = ? 
                  WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssi", $title, $description, $ebookFilePath, $imagePath, $rating, $updatedAt, $bookId);

        if (!mysqli_stmt_execute($stmt)) {
            error_log("Error executing update query: " . mysqli_error($this->conn));
            return false;
        }

        return true;
    }


    public function deleteBook($bookId)
    {
        $query = "DELETE FROM books WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $bookId);
        return mysqli_stmt_execute($stmt);
    }

    public function getTotalBooks()
    {
        $query = "SELECT COUNT(*) as total FROM books";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }
}
