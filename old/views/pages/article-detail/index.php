<?php
// Start the session to check login status
session_start();

// Include your article controller
require_once dirname(__FILE__) . '/../../../controllers/ArtikelController.php';
$artikelController = new ArtikelController();

// Check if the article ID is provided via URL
if (isset($_GET['id'])) {
    $artikelId = $_GET['id'];
    // Get article details by ID
    $article = $artikelController->getArtikelById($artikelId);
} else {
    // Handle case where article ID is not provided
    echo "Artikel tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Artikel</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Quicksand:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #f7f7f7;
        }
        header {
            background-color: #ffffff;
            border-bottom: 1px solid #ddd;
        }
        .article-header h1 {
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
            color: #001A45;
        }
        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }
        .article-content p {
            margin-bottom: 1.5rem;
        }
        .article-content h3 {
            margin: 2rem 0 1.5rem;
            color: #001A45;
            font-weight: bold;
        }
        .article-content ol, .article-content ul {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }
        .article-content li {
            margin-bottom: 1rem;
        }
        .article-header .text-muted {
            text-align: center;
        }
        .navbar-brand img {
            max-height: 50px;
        }
        .navbar .nav-link {
            font-weight: 600;
            color: #001A45;
        }
        .navbar .nav-link.btn-primary {
            color: #001A45;
            border-radius: 50px;
        }
        .navbar .nav-link.btn-primary:hover {
            opacity: 0.8;
        }
        .article-image img {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="../home/home.php">
                    <img src="../../../../assets/images/logo.svg" alt="KelasSore Logo">
                </a>
                <!-- Hamburger Menu -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Navbar Links -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php
                        if (!isset($_SESSION['user_id']) && !isset($_SESSION['mentor_id'])) {
                            echo '<li class="nav-item"><a class="nav-link" href="/views/pages/login/login.php">Masuk</a></li>';
                            echo '<li class="nav-item"><a class="nav-link btn-primary" href="/views/pages/register/register.php">Daftar</a></li>';
                        } else {
                            echo '<li class="nav-item"><a class="nav-link btn-primary" href="logout.php">Log Out</a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content Section -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <!-- Article Header -->
                <div class="article-header mb-4 text-center">
                    <h1 class="display-4"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <p class="text-muted">Dipublikasikan: <?php echo date('d M Y', strtotime($article['created_at'])); ?></p>
                </div>

                <!-- Article Image -->
                <div class="article-image mb-4">
                    <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="Gambar Artikel" class="img-fluid w-100">
                </div>

                <!-- Article Subtitle -->
                <div class="article-subtitle mb-4">
                    <h3 class="h4"><?php echo htmlspecialchars($article['subtitle']); ?></h3>
                </div>

                <!-- Divider -->
                <div class="my-4 border-top"></div>

                <!-- Article Content -->
                <div class="article-content">
                    <?php echo $article['content']; // Allow HTML content to render ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <?php include_once dirname(__FILE__) . '/../../layouts/footer.php'; ?>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>