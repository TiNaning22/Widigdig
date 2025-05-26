<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Navbar</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Navbar Custom Styles */
        .navbar {
            padding: 1rem 10%;
            background-color: #ffffff;
            border-bottom: 1px solid #ddd;
        }

        .navbar-brand img {
            max-height: 40px;
        }

        .navbar-toggler {
            border: none;
            background-color: transparent;
        }

        .navbar-toggler-icon {
            background-color: #333;
        }

        .navbar-nav {
            display: flex;
            gap: 15px;
        }

        .navbar-nav .nav-item .nav-link {
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 50px;
            color: #333;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-item .nav-link:hover {
            opacity: 0.8;
        }

        /* Responsive styles for navbar */
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem 5%;
            }

            .navbar-nav {
                flex-direction: column;
                text-align: center;
                padding-top: 10px;
            }

            .navbar-nav .nav-item .nav-link {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .navbar-nav .nav-item .nav-link {
                font-size: 0.9rem;
                padding: 8px 12px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
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
                    session_start();
                    if (!isset($_SESSION['user_id']) && !isset($_SESSION['mentor_id'])) {
                        echo '<li class="nav-item"><a class="nav-link" href="/views/pages/login/login.php">Masuk</a></li>';
                        echo '<li class="nav-item"><a class="nav-link" href="/views/pages/register/register.php">Daftar</a></li>';
                    } elseif (isset($_SESSION['mentor_id'])) {
                        echo '<li class="nav-item"><a class="nav-link" href="logout.php">Keluar</a></li>';
                    } elseif (isset($_SESSION['user_id'])) {
                        echo '<li class="nav-item"><a class="nav-link" href="">Kelas Anda</a></li>';
                        echo '<li class="nav-item"><a class="nav-link" href="logout.php">Keluar</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>