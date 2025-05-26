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
        // First, get the existing book data to check if we should keep the old file paths
        $existingBook = $this->getBookById($bookId);

        // If no new ebook file is uploaded, retain the existing one
        if (empty($ebookFilePath)) {
            $ebookFilePath = $existingBook['ebook_file'];
        }

        // If no new image is uploaded, retain the existing one
        if (empty($imagePath)) {
            $imagePath = $existingBook['image'];
        }

        // Update query
        $query = "UPDATE books 
                  SET title = ?, description = ?, ebook_file = ?, image = ?, rating = ?, updated_at = ? 
                  WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "ssssssi", $title, $description, $ebookFilePath, $imagePath, $rating, $updatedAt, $bookId);

        // Execute the query and return the result
        return mysqli_stmt_execute($stmt);
    }

    public function deleteBook($bookId)
    {
        $query = "DELETE FROM books WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $bookId);
        return mysqli_stmt_execute($stmt);
    }
}
