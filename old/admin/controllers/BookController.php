<?php
// File: controllers/BookController.php

require_once dirname(__FILE__) . '/../models/BookModel.php';

class BookController
{
    private $bookModel;

    public function __construct()
    {
        $this->bookModel = new BookModel();
    }

    public function getAllBooks()
    {
        return $this->bookModel->getAllBooks();
    }

    public function getBookById($bookId)
    {
        $book = $this->bookModel->getBookById($bookId);

        // Tambahkan nilai default jika data tidak ditemukan
        $book['description'] = $book['description'] ?? 'No description available';
        $book['ebook_file'] = $book['ebook_file'] ?? null;
        $book['image'] = $book['image'] ?? null;

        return $book;
    }

    public function createBook($title, $description, $ebookFile, $image)
    {
        $ebookFileName = uniqid() . '_' . basename($ebookFile['name']);
        $imageFileName = uniqid() . '_' . basename($image['name']);

        $ebookFilePath = '../../../../public/book-file/' . $ebookFileName;
        $imagePath = '../../../../public/image-book/' . $imageFileName;

        $createdAt = $updatedAt = date('Y-m-d H:i:s');
        $rating = 0;

        if (!file_exists('../../../../public/book-file')) mkdir('../../../../public/book-file', 0777, true);
        if (!file_exists('../../../../public/image-book')) mkdir('../../../../public/image-book', 0777, true);

        if (!move_uploaded_file($ebookFile['tmp_name'], $ebookFilePath)) return "Failed to upload ebook file.";
        if (!move_uploaded_file($image['tmp_name'], $imagePath)) return "Failed to upload image.";

        return $this->bookModel->insertBook($title, $description, $ebookFilePath, $imagePath, $rating, $createdAt, $updatedAt);
    }

    public function updateBook($bookId, $title, $description, $ebookFile, $image)
    {
        $existingBook = $this->bookModel->getBookById($bookId);

        // Gunakan file lama jika tidak ada file baru yang diunggah
        $ebookFilePath = !empty($ebookFile['name']) ? '../../../../public/book-file/' . uniqid() . '_' . basename($ebookFile['name']) : $existingBook['ebook_file'];
        $imagePath = !empty($image['name']) ? '../../../../public/image-book/' . uniqid() . '_' . basename($image['name']) : $existingBook['image'];

        // Buat direktori jika belum ada
        if (!file_exists('../../../../public/book-file')) mkdir('../../../../public/book-file', 0777, true);
        if (!file_exists('../../../../public/image-book')) mkdir('../../../../public/image-book', 0777, true);

        // Pindahkan file baru (jika ada)
        if (!empty($ebookFile['name']) && !move_uploaded_file($ebookFile['tmp_name'], $ebookFilePath)) {
            return "Failed to upload ebook file.";
        }
        if (!empty($image['name']) && !move_uploaded_file($image['tmp_name'], $imagePath)) {
            return "Failed to upload image.";
        }

        // Debug data sebelum update
        error_log("Book ID: $bookId");
        error_log("Title: $title");
        error_log("Description: $description");
        error_log("Ebook File Path: $ebookFilePath");
        error_log("Image Path: $imagePath");

        return $this->bookModel->updateBook($bookId, $title, $description, $ebookFilePath, $imagePath, $existingBook['rating'], date('Y-m-d H:i:s'));
    }


    public function deleteBook($bookId)
    {
        $existingBook = $this->bookModel->getBookById($bookId);
        $result = $this->bookModel->deleteBook($bookId);

        if ($result) {
            if (!empty($existingBook['ebook_file']) && file_exists($existingBook['ebook_file'])) {
                unlink($existingBook['ebook_file']);
            }
            if (!empty($existingBook['image']) && file_exists($existingBook['image'])) {
                unlink($existingBook['image']);
            }
        }

        return $result;
    }

    public function getTotalBooks()
    {
        $result = $this->bookModel->getTotalBooks();
        return [
            'success' => true,
            'data' => $result['total'] ?? 0
        ];
    }
}
