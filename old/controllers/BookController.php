<?php
// File: controllers/BookController.php

require_once dirname(__FILE__) . '/../models/BookModel.php';

class BookController {
    private $bookModel;

    public function __construct() {
        $this->bookModel = new BookModel();
    }

    public function getAllBooks() {
        return $this->bookModel->getAllBooks();
    }

    public function getBookById($bookId) {
        return $this->bookModel->getBookById($bookId);
    }

    public function createBook($title, $description, $ebookFile, $image) {
        // Generate unique file names
        $ebookFileName = uniqid() . '_' . basename($ebookFile['name']);
        $imageFileName = uniqid() . '_' . basename($image['name']);

        $ebookFilePath = '../public/book-file/' . $ebookFileName;
        $imagePath = '../public/image-book/' . $imageFileName;

        $createdAt = $updatedAt = date('Y-m-d H:i:s');
        $rating = 0; // Default rating value

        // Create necessary directories
        if (!file_exists('../public/book-file')) mkdir('../public/book-file', 0777, true);
        if (!file_exists('../public/image-book')) mkdir('../public/image-book', 0777, true);

        // Move uploaded files
        if (!move_uploaded_file($ebookFile['tmp_name'], $ebookFilePath)) return "Failed to upload ebook file.";
        if (!move_uploaded_file($image['tmp_name'], $imagePath)) return "Failed to upload image.";

        // Insert data
        return $this->bookModel->insertBook($title, $description, $ebookFilePath, $imagePath, $rating, $createdAt, $updatedAt);
    }

    public function updateBook($bookId, $title, $description, $ebookFile, $image) {
        // Get the current time for updatedAt
        $updatedAt = date('Y-m-d H:i:s');
    
        // Fetch the existing book details
        $existingBook = $this->bookModel->getBookById($bookId);
    
        // Determine file paths for ebook and image (use existing ones if no new file is uploaded)
        $ebookFilePath = !empty($ebookFile['name']) ? '../public/book-file/' . uniqid() . '_' . basename($ebookFile['name']) : $existingBook['ebook_file'];
        $imagePath = !empty($image['name']) ? '../public/image-book/' . uniqid() . '_' . basename($image['name']) : $existingBook['image'];
    
        // Create necessary directories
        if (!file_exists('../public/book-file')) mkdir('../public/book-file', 0777, true);
        if (!file_exists('../public/image-book')) mkdir('../public/image-book', 0777, true);
    
        // Move uploaded files if new files are provided
        if (!empty($ebookFile['name']) && !move_uploaded_file($ebookFile['tmp_name'], $ebookFilePath)) {
            return "Failed to upload ebook file.";
        }
        if (!empty($image['name']) && !move_uploaded_file($image['tmp_name'], $imagePath)) {
            return "Failed to upload image.";
        }

        // Get the existing rating, as it will not be updated
        $rating = $existingBook['rating'];
    
        // Call the model's updateBook function
        return $this->bookModel->updateBook($bookId, $title, $description, $ebookFilePath, $imagePath, $rating, $updatedAt);
    }    

    public function deleteBook($bookId) {
        // First, get the existing book to retrieve its file paths
        $existingBook = $this->bookModel->getBookById($bookId);
        
        // Delete the record from the database
        $result = $this->bookModel->deleteBook($bookId);
        
        // If deletion from database is successful, remove the files
        if ($result) {
            // Remove ebook file
            if (!empty($existingBook['ebook_file']) && file_exists($existingBook['ebook_file'])) {
                unlink($existingBook['ebook_file']);
            }
            
            // Remove image file
            if (!empty($existingBook['image']) && file_exists($existingBook['image'])) {
                unlink($existingBook['image']);
            }
        }
        
        return $result;
    }
}
?>
