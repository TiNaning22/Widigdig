<?php
include dirname(__FILE__) . '/../../../controllers/ArtikelController.php';

$artikelController = new ArtikelController();

// Get artikel ID from URL parameter
$artikelId = $_GET['id'] ?? null;

if (!$artikelId) {
    header('Location: artikel.php');
    exit;
}



// Fetch existing artikel data
$artikel = $artikelController->getArtikelById($artikelId);

if (!$artikel) {
    header('Location: artikel.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? null;
    $subtitle = $_POST['subtitle'] ?? null;
    $content = $_POST['content'] ?? null;
    $image = $_FILES['image'] ?? null;

    $result = $artikelController->updateArtikel($artikelId, $title, $subtitle, $content, $image);

    if ($result === true) {
        header('Location: artikel.php');
        exit;
    } else {
        $error = $result;
    }
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
    <title>Edit Artikel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/artikel/artikel.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: [
                'advlist autolink lists link image charmap preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table help wordcount'
            ],
            toolbar: 'undo redo | styles | bold italic underline strikethrough | ' +
                     'alignleft aligncenter alignright alignjustify | ' +
                     'bullist numlist outdent indent | link image | ' +
                     'removeformat | help',
            menubar: 'file edit view insert format tools table help',
            height: 400,
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            },
            images_upload_handler: function (blobInfo, success, failure) {
                failure('Image upload is not implemented yet');
            }
        });
    </script>
    <style>
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .current-image {
            margin: 10px 0;
            max-width: 200px;
        }

        .tox-tinymce {
            border-radius: 5px !important;
            margin-bottom: 15px;
        }

        .form-actions {
            justify-content: right;
            display: flex;
            margin-top: 20px;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include '../../../views/layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="top-bar">
                <h1>Edit Artikel</h1>
            </div>

            <div class="form-container">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Judul Artikel</label>
                        <input type="text" id="title" name="title" class="form-control" 
                               value="<?php echo htmlspecialchars($artikel['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="subtitle">Subjudul</label>
                        <input type="text" id="subtitle" name="subtitle" class="form-control" 
                               value="<?php echo htmlspecialchars($artikel['subtitle']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="content">Konten</label>
                        <textarea id="content" name="content" class="form-control" required>
                            <?php echo htmlspecialchars($artikel['content']); ?>
                        </textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Gambar</label>
                        <?php if (!empty($artikel['image'])): ?>
                            <div>
                                <img src="<?php echo htmlspecialchars($artikel['image']); ?>" 
                                     alt="Current image" class="current-image">
                                <p>Current image: <?php echo basename($artikel['image']); ?></p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        <small>Leave empty to keep the current image</small>
                    </div>

                    <div class="form-actions">
                        <a href="artikel.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>