<?php
// Path: views/pages/login/login.php
include dirname(__FILE__) . '/../../../controllers/AuthController.php';

// Start session at the beginning
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: views/pages/home/home.php');
    exit();
}

// Instantiate AuthController
$authController = new AuthController();

// Handle login form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $remember_me = isset($_POST['remember_me']) ? true : false;

    $message = $authController->login($email, $password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../assets/css/login/login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600&family=Roboto:wght@400;700&family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
    <title>Login</title>
    <style>
        h2 {
            font-family: 'Roboto', sans-serif;
        }
        p {
            font-family: 'Quicksand', sans-serif;
        }
    </style>
</head>
<body>
<div class="container my-3" style="max-width: 200px; position: absolute; top: 0; left: 0; padding-left: 50px; padding-top: 20px">
    <a href="javascript:history.back()" style="text-decoration: none; color: inherit;">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
</div>
    <!----------------------- Main Container -------------------------->
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <!----------------------- Login Container -------------------------->
        <div class="row border rounded-5 p-3 bg-white shadow box-area">
            <!--------------------------- Left Box ----------------------------->
            <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #000842;">
                <div class="featured-image mb-8">
                    <img src="../../../assets/images/login.svg" class="img-fluid" style="width: 200px; height: 300px;" alt="Featured Image">
                </div>
                <p class="text-white fs-2" style="font-family: 'Manrope', sans-serif; font-weight: 600;">Masuk ke KelasSore</p>
            </div>

            <!--------------------------- Right Box ---------------------------->
            <div class="col-md-5 right-box">
                <div class="row align-items-center">
                    <div class="header-text mb-4">
                        <h2>KelasSore</h2>
                        <p>Selamat datang kembali di KelasSore</p>
                    </div>

                    <?php 
                    // Display login error message if exists
                    if (!empty($message)) {
                        echo "<div class='alert alert-danger'>" . htmlspecialchars($message) . "</div>";
                    }
                    ?>

                    <form action="login.php" method="POST">
                        <div class="input-group mb-3">
                            <input type="text" name="email" class="form-control form-control-lg bg-light fs-6" 
                                placeholder="Masukkan E-mail" 
                                style="font-weight: 400;" 
                                required>
                        </div>
                        <div class="input-group mb-1 position-relative">
                            <input type="password" name="password" id="password" class="form-control form-control-lg bg-light fs-6" 
                                placeholder="Masukkan kata sandi" 
                                style="font-weight: 400;" 
                                required>
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3">
                                <i class="bi bi-eye-slash" id="togglePassword" style="cursor: pointer;"></i>
                            </span>
                        </div>
                        <div class="input-group mb-5 d-flex justify-content-between">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="formCheck" name="remember_me">
                                <label for="formCheck" class="form-check-label text-secondary"><small>Ingat saya</small></label>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">Masuk</button>
                        </div>
                    </form>
                    <div class="input-group mb-3">
                    <a href="../login/loginmentor.php" class="btn btn-lg btn-light w-100 fs-6">
                            <small>Masuk sebagai Mentor</small>
                    </a>
                    </div>
                    <div class="row">
                        <small>Belum punya akun? <a href="../register/register.php">Daftar</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle icon class
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
