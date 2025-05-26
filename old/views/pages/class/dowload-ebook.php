<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__DIR__, 3) . '/models/BookModel.php';
require_once dirname(__DIR__, 3) . '/services/database.php';

function logError($message) {
    $logDir = dirname(__DIR__, 3) . '/logs';
    $logFile = $logDir . '/download-ebook.log';
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $formattedMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Validate book ID
    if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
        logError('Invalid book ID: ' . ($_GET['book_id'] ?? 'No ID provided'));
        http_response_code(400);
        die('Invalid book ID');
    }

    $bookId = intval($_GET['book_id']);
    $bookModel = new BookModel();

    // Fetch the book details
    $book = $bookModel->getBookById($bookId);

    if (!$book || empty($book['ebook_file'])) {
        logError("Book not found or no ebook file for ID: $bookId");
        http_response_code(404);
        die('Ebook not found');
    }

    // Normalize the file path
    $bookFile = str_replace('\\', '/', $book['ebook_file']);
    $bookFile = ltrim($bookFile, '/');

    // Construct full file path
    $basePath = dirname(__DIR__, 3);
    $filePath = $basePath . '/public/book-file/' . basename($bookFile);

    // Validate file existence and readability
    if (!file_exists($filePath)) {
        logError("File not found: $filePath");
        http_response_code(404);
        die('Ebook file does not exist');
    }

    if (!is_readable($filePath)) {
        logError("File not readable: $filePath");
        http_response_code(403);
        die('Unable to read file');
    }

    // Get file details
    $fileName = basename($filePath);
    $fileSize = filesize($filePath);
    
    // Detect MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($finfo, $filePath);
    finfo_close($finfo);

    // Validate file type (expanded allowed types)
    $allowedTypes = [
        'application/pdf', 
        'application/epub+zip', 
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];
    
    if (!in_array($fileType, $allowedTypes)) {
        logError("Invalid file type: $fileType");
        http_response_code(400);
        die('Invalid file type');
    }

    // Clear output buffer
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Send download headers
    header('Content-Type: ' . $fileType);
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . $fileSize);
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Output file in chunks
    $handle = fopen($filePath, 'rb');
    if ($handle === false) {
        logError("Failed to open file: $filePath");
        http_response_code(500);
        die('Failed to process file');
    }

    while (!feof($handle)) {
        $buffer = fread($handle, 8192);
        if ($buffer === false) {
            break;
        }
        echo $buffer;
        flush();
    }

    fclose($handle);
    exit;

} catch (Exception $e) {
    logError('Download error: ' . $e->getMessage());
    http_response_code(500);
    die('An error occurred during download');
}
?>