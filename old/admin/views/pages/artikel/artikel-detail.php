<?php
// File: views/pages/artikel/artikel-detail.php

include_once dirname(__FILE__) . '/../../../controllers/ArtikelController.php';
include_once dirname(__FILE__) . '/../../../models/ArtikelModel.php';
include_once dirname(__FILE__) . '/../../../services/database.php';

$artikelController = new ArtikelController();

$artikelId = isset($_GET['id']) ? $_GET['id'] : null;
$artikel = null;

if ($artikelId) {
    $artikel = $artikelController->getArtikelById($artikelId);
    
    if (!$artikel) {
        echo "Artikel tidak ditemukan.";
        exit;
    }
} else {
    echo "Invalid artikel ID.";
    exit;
}

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
    <title>Detail Artikel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Include TinyMCE script -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        /* Global reset and font */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        /* Body styling */
        body {
            background-color: #f0f2f5;
            color: #333;
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            width: 100%;
        }

        .artikel-detail-container {
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .artikel-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .artikel-header h1 {
            font-size: 2em;
            color: #333;
            font-weight: bold;
        }

        .artikel-content {
            margin-top: 20px;
        }

        .info-item {
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-bottom: 20px;
            word-wrap: break-word;  /* This ensures text wraps within the container */
            overflow: hidden; /* Prevents content from overflowing */
        }

        .info-label {
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .info-value {
            color: #333;
            line-height: 1.6;
        }

        .artikel-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 15px;
        }

        .back-button {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 1em;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .content-text {
            white-space: pre-line;
            text-align: justify;
        }

        /* To ensure that content inside 'info-item' stays within bounds */
        .info-item img {
            width: 25%; 
            height: auto;
            margin-top: 15px;
        }

        .info-item .info-value {
            display: block;
            clear: both;
        }

        /* Responsive design for smaller screens */
        @media screen and (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
            }

            .artikel-detail-container {
                margin: 10px;
                padding: 20px;
            }

            .artikel-header h1 {
                font-size: 1.5em;
            }

            /* Stacking content vertically for small screens */
            .artikel-content .info-item {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include '../../../views/layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="artikel-detail-container">
                <div class="artikel-header">
                    <h1>Detail Artikel</h1>
                </div>

                <div class="artikel-content">
                    <div class="info-item">
                        <div class="info-label">Image</div>
                        <div class="info-value">
                            <?php if (!empty($artikel['image'])): ?>
                                <img src="../../../assets/images/artikels/<?php echo htmlspecialchars($artikel['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($artikel['title']); ?>"
                                     class="artikel-image">
                            <?php else: ?>
                                <p>No image available.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Title</div>
                        <div class="info-value"><?php echo htmlspecialchars($artikel['title']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Subtitle</div>
                        <div class="info-value"><?php echo htmlspecialchars($artikel['subtitle']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Content</div>
                        <div class="info-value content-text">
                            <?php echo $artikel['content']; ?>
                        </div>
                    </div>

                </div>

                <a href="artikel.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- TinyMCE initialization -->
    <script>
        tinymce.init({
            selector: '#content',  // Applies TinyMCE to the textarea with id 'content'
            plugins: 'a11ychecker advlist anchor autolink codesample link lists media searchreplace table wordcount',
            toolbar: 'undo redo | bold italic underline | link image | alignleft aligncenter alignright | bullist numlist outdent indent',
            height: 400,  // Set the height of the editor
            branding: false,  // Disable TinyMCE branding
        });
    </script>
</body>
</html>
