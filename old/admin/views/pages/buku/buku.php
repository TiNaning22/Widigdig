<?php
require_once dirname(__FILE__) . '/../../../controllers/BookController.php';

$bookController = new BookController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'create') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $ebookFile = $_FILES['ebook_file'];
        $image = $_FILES['image'];

        if ($bookController->createBook($title, $description, $ebookFile, $image)) {
            header("Location: buku.php");
        }
    }

    if ($action === 'update') {
        $bookId = $_POST['book_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $ebookFile = $_FILES['ebook_file'];
        $image = $_FILES['image'];

        if ($bookController->updateBook($bookId, $title, $description, $ebookFile, $image)) {
            header("Location: buku.php");
        }
    }

    // Delete Book
    if (isset($_POST['book_id']) && $_POST['action'] === 'delete') {
        $bookId = $_POST['book_id'];
        if ($bookController->deleteBook($bookId)) {
            header("Location: buku.php");
        }
    }
}

// Fetch all books
$books = $bookController->getAllBooks();

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../../views/pages/login/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/buku/buku.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
   /* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 30px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 8px;
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.modal-header {
    text-align: center;
    font-size: 24px;
    margin-bottom: 20px;
}

.close-modal {
    position: absolute;
    right: 10px;
    top: 10px;
    font-size: 24px;
    cursor: pointer;
    color: #888;
}

.close-modal:hover {
    color: #000;
}

.modal form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.modal form input,
.modal form textarea {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    width: 100%; /* Make form inputs take full width */
}

.modal form textarea {
    resize: vertical;
}

.modal form button {
    padding: 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    width: 100%; /* Ensure buttons also take full width */
}

.modal form button[type="submit"],
.modal form button[type="button"] {
    width: 100%;
}

.modal form button[type="submit"] {
    background-color: #4CAF50;
    color: white;
}

.modal form button[type="button"] {
    background-color: #f44336;
    color: white;
}

.button-group {
    display: flex;
    justify-content: space-between;
}

.modal form button {
    padding: 8px 12px; /* Mengurangi ukuran padding tombol */
    font-size: 14px; /* Mengurangi ukuran font tombol */
}

.modal form button[type="submit"],
.modal form button[type="button"] {
    width: 48%; /* Menjaga tombol lebih kecil dan seimbang di dalam button-group */
}

.modal form button[type="submit"] {
    background-color: #4CAF50;
    color: white;
}

.modal form button[type="button"] {
    background-color: #f44336;
    color: white;
}

.modal-preview {
    display: flex;
    gap: 15px;
}

.modal-preview img {
    max-width: 100px;
    border-radius: 8px;
}

/* Additional Style Adjustments */
.modal form .form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.modal form .form-group label {
    font-weight: bold;
    font-size: 16px;
}

.modal form .form-group input,
.modal form .form-group textarea {
    width: 100%;
    padding: 12px;
    font-size: 14px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.modal form .form-group input[type="file"] {
    padding: 5px;
}

.modal form .form-group textarea {
    min-height: 100px;
}

.modal .modal-content h2 {
    margin-top: 0;
    text-align: center;
}

/* Ensuring modal content is centered and has equal spacing on all sides */
.modal-content {
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

    </style>
</head>
<body>
    <?php include '../../../views/layout/sidebar.php'; ?>
    <div class="main-content">
        <div class="container">
            <div class="top-bar">
                <h1>Buku</h1>
                <button class="add-mentor-btn" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Book
                </button>
            </div>

            <div class="deals-table">
                <div class="deals-header">
                    <h2>Buku Details</h2>
                </div>

                <table id="booksTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Buku</th>
                            <th>Rating</th>
                            <th>Deskripsi</th>
                            <th>File Ebook</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <div class="product-info">
                                    <div class="product-image">
                                        <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="Book Image" width="50">
                                    </div>
                                    <span><?php echo htmlspecialchars($book['title'] ?? 'No Title'); ?></span>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($book['rating'] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars($book['description'] ?? 'No Description'); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($book['ebook_file']); ?>" target="_blank" class="btn-download">
                                    <i class="fa fa-download" aria-hidden="true"></i> Download Ebook
                                </a>
                            </td>
                            <td>
                                <!-- Tombol Update -->
                                <button onclick="showUpdateForm(
                                    '<?php echo $book['id']; ?>', 
                                    '<?php echo htmlspecialchars($book['title']); ?>', 
                                    '<?php echo htmlspecialchars($book['description']); ?>', 
                                    '<?php echo htmlspecialchars($book['ebook_file']); ?>', 
                                    '<?php echo htmlspecialchars($book['image']); ?>'
                                )" class="btn-update">
                                    <i class="fa fa-edit"></i> Update
                                </button>

                                <!-- Tombol Delete -->
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" name="action" value="delete" class="btn-delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Add Book Modal -->
            <div id="addBookModal" class="modal">
               <div class="modal-content">
                <span class="close-modal" onclick="closeAddModal()">&times;</span>
                <h2>Add New Book</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">
            
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" required>
                    </div>
            
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" required></textarea>
                    </div>
            
                    <div class="form-group">
                        <label for="ebook_file">Ebook File</label>
                        <input type="file" name="ebook_file" id="ebook_file" required>
                    </div>
            
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" name="image" id="image" required>
                    </div>
            
                    <div class="button-group">
                        <button type="submit">Add Book</button>
                        <button type="button" onclick="closeAddModal()">Cancel</button>
                    </div>
                </form>
            </div>

            </div>

            <!-- Update Book Form -->
            <div id="updateBookForm" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeUpdateModal()">&times;</span>
                    <h2>Update Book</h2>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="book_id" id="update_book_id">
                        
                        <div>
                            <label>Title</label>
                            <input type="text" name="title" id="update_title" required>
                        </div>

                        <div>
                            <label>Description</label>
                            <textarea name="description" id="update_description" required></textarea>
                        </div>

                        <div>
                            <label>Ebook File</label>
                            <input type="file" name="ebook_file" id="update_ebook_file">
                            <small>Current file: <span id="current_ebook_file">No File</span></small>
                        </div>

                        <div>
                            <label>Image</label>
                            <input type="file" name="image" id="update_image">
                            <small>Current image: <span id="current_image">No Image</span></small>
                        </div>

                        <div class="button-group">
                            <button type="submit">Update Book</button>
                            <button type="button" onclick="closeUpdateModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#booksTable').DataTable(); // Initialize DataTable
        });

        function openAddModal() {
            document.getElementById('addBookModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addBookModal').style.display = 'none';
        }

        function closeUpdateModal() {
            document.getElementById('updateBookForm').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('addBookModal');
            if (event.target == modal) {
                closeAddModal();
            }

            const updateModal = document.getElementById('updateBookForm');
            if (event.target == updateModal) {
                closeUpdateModal();
            }
        }

        function showUpdateForm(bookId, title, description, ebookFile, image) {
            document.getElementById('updateBookForm').style.display = 'block';
            document.getElementById('update_book_id').value = bookId;
            document.getElementById('update_title').value = title;
            document.getElementById('update_description').value = description;
            document.getElementById('current_ebook_file').innerHTML = ebookFile ? ebookFile : 'No File';
            document.getElementById('current_image').innerHTML = image ? image : 'No Image';
        }
    </script>
</body>
</html>
