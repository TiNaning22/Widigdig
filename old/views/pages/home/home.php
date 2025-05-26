<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KelasSore.com</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Quicksand:wght@500&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../../assets/css/home/home.css">
    <link rel="stylesheet" href="../../../../assets/css/home/content.css">
    <link rel="stylesheet" href="../../../../assets/css/home/ourcourses.css">
    <link rel="stylesheet" href="../../../../assets/css/home/privatecourses.css">
    <link rel="stylesheet" href="../../../../assets/css/home/publiccourses.css">
    <link rel="stylesheet" href="../../../../assets/css/home/partner.css">
    <link rel="stylesheet" href="../../../../assets/css/home/animation.css">
    <link rel="stylesheet" href="../../../../assets/css/home/banner.css">
    <link rel="stylesheet" href="../../../../assets/css/home/artikel.css">
    <style>
        body {
            font-family: 'Quicksand', sans-serif !important;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 10%;
            background-color: #ffffff;
            border-bottom: 1px solid #ddd;
            position: relative;
            width: 100%;
            box-sizing: border-box;
        }

        .logo {
            max-height: 50px;
            display: flex;
            align-items: center;
        }

        .logo img {
            max-height: 50px;
        }

        .hamburger-menu {
            font-size: 24px;
            cursor: pointer;
            display: none;
        }

        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .nav-links .join {
            background-color: #001A45;
            color: #fff;
        }

        .nav-links .login {
            background-color: #fff;
            border: 2px solid #001A45;
            color: #001A45;
        }

        .nav-links a:hover {
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            header {
                padding: 15px 5%;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .nav-links {
                display: flex;
                flex-direction: row;
                gap: 8px;
                margin: 0;
                padding: 0;
            }

            .nav-links a {
                padding: 8px 16px;
                font-size: 14px;
            }

            .logo img {
                max-height: 40px;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 12px 4%;
            }

            .nav-links {
                gap: 6px;
            }

            .nav-links a {
                padding: 6px 12px;
                font-size: 13px;
            }

            .logo img {
                max-height: 35px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ?>
    <header>
        <div class="logo">
            <a href="../home/home.php">
                <img src="../../../../assets/images/logo.svg" alt="KelasSore Logo">
            </a>
        </div>

        <div class="nav-links">
            <?php
            if (!isset($_SESSION['user_id'])) {
                // User is not logged in - show login and register buttons
                echo '<a href="/views/pages/login/login.php" class="login">Masuk</a>';
            } else {
                echo '<a href="/views/pages/dashoardUser/index.php" class="join">Kelas Anda</a>';
                echo '<a href="/views/pages/userprofile/userprofile.php" class="join">Profile</a>';
            }
            ?>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="text slide-in-left">
            <h1 style="padding-right: 48px; font-family: 'Montserrat', sans-serif; font-weight: 700; color: #000957;">
                Selamat Datang di Kelas Sore!
            </h1>
            <p style="font-family: 'Quicksand', sans-serif; padding-right: 48px; margin-top: 24px; line-height: 1.6;">
                Tingkatkan keterampilan Anda bersama Kelas Sore, platform kursus online dengan berbagai pilihan topik menarik,
                seperti teknologi, pengembangan karier, dan pembelajaran bahasa. Belajar kapan saja dan di mana saja dengan mudah
                dan fleksibel.
            </p>
        </div>
        <div class="home-images slide-in-right">
            <div class="slide-container">
                <img src="../../../../assets/images/homecontent.svg" alt="Example 4" class="slide">
                <img src="../../../../assets/images/kursus.svg" alt="Example 5" class="slide">
            </div>
        </div>
    </section>

    <?php include('content.php'); ?>
    <?php include('ourcourses.php'); ?>
    <?php include('privatecourses.php'); ?>
    <?php include('publiccourses.php'); ?>
    <?php include('partner.php'); ?>
    <?php include('artikel.php'); ?>

    <!-- Bottom Hero Section -->
    <section class="hero">
        <div class="text slide-in-left">
            <!-- Judul menggunakan Montserrat -->
            <h1 class="text-bottom" style="font-family: 'Montserrat', sans-serif; font-weight: 700; color: #000957;">
                Kelas Sore!
            </h1>

            <!-- Body menggunakan Quicksand -->
            <p style="font-family: 'Quicksand', sans-serif; font-weight: 500; line-height: 1.6; color: #333;">
                KelasSore.com merupakan platform digital education learning yang memberikan materi seputar dunia pendidikan,
                karir dan bisnis yang berfokus pada teknik penerapan dan pemecahan masalah yang dihadapi oleh peserta didik.
                Proses pembelajaran yang menyenangkan, Mentor yang menguasai bidangnya, proses mentoring setiap waktu,
                fleksibilitas mentor dan peserta didik akan membuat proses pembelajaran menghasilkan output yang berkualitas.
            </p>
        </div>
        <div class="images-bottom slide-in-right">
            <img src="../../../../assets/images/kursus.svg" alt="Example 1">
        </div>
    </section>


    <?php include dirname(__FILE__) . '/../../layouts/footer.php'  ?>

    <!-- Scripts -->
    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
    </script>
    <script src="../../../../assets/js/animation.js"></script>
    <script src="../../../../assets/js/privateslide.js"></script>
    <script src="../../../../assets/js/publicslide.js"></script>
    <script src="../../../../assets/js/homeslide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>