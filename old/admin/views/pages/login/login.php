<?php
// Start session at the beginning
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: /admin/views/pages/dashboard/dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../../controllers/AuthController.php';
    $authController = new AuthController();
    $message = $authController->handleLoginForm($_POST['email'], $_POST['password']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KelasSore</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
  
    <style>
        body {
            font-family: 'Manrope', sans-serif;
            background: #f5f5f5;
        }
        .box-area {
            max-width: 930px;
            backdrop-filter: blur(7px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .left-box {
            background: linear-gradient(45deg, #000842, #001161) !important;
            position: relative;
            overflow: hidden;
        }
        .left-box::after {
            content: '';
            position: absolute;
            width: 150%;
            height: 150%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(0,0,0,0) 60%);
            top: -25%;
            left: -25%;
            animation: pulse 4s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .featured-image {
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }
        .featured-image:hover {
            transform: translateY(-5px);
        }
        .right-box {
            padding: 40px 30px;
        }
        .header-text h2 {
            font-weight: 700;
            font-size: 2rem;
            background: linear-gradient(45deg, #000842, #001161);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .form-control {
            border: 2px solid #e1e1e1;
            padding: 12px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #000842;
            box-shadow: 0 0 0 0.2rem rgba(0, 8, 66, 0.25);
        }
        .btn-primary {
            background: linear-gradient(45deg, #000842, #001161);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 8, 66, 0.3);
        }
        .btn-light {
            border: 2px solid #e1e1e1;
            padding: 12px;
            transition: all 0.3s ease;
        }
        .btn-light:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .form-check-input:checked {
            background-color: #000842;
            border-color: #000842;
        }
        a {
            color: #000842;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        a:hover {
            color: #001161;
            text-decoration: underline;
        }
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row border rounded-5 p-3 bg-white shadow box-area">
            <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box">
                <div class="featured-image mb-3">
                    <img src="../../../assets/images/login.svg" class="img-fluid" style="width: 250px; height: 350px;" alt="Featured Image">
                </div>
                <p class="text-white fs-2 mb-2" style="font-weight: 600;">Selamat datang kembali!</p>
                <p class="text-white-50 text-center mb-2">Masuk untuk melanjutkan perjalanan pembelajaran Anda</p>
            </div>
            <div class="col-md-6 right-box">
                <div class="row align-items-center">
                    <div class="header-text mb-4 text-center">
                        <h2>KelasSore</h2>
                        <p class="text-muted">Kami senang anda kembali.</p>
                    </div>
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                            </svg>
                            <div><?php echo htmlspecialchars($message); ?></div>
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST" id="loginForm">
                        <div class="input-group mb-4">
                            <input type="email" name="email" class="form-control form-control-lg bg-light"
                                placeholder="Masukkan E-mail" required
                                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                title="Please enter a valid email address">
                        </div>
                        <div class="input-group mb-4">
                            <input type="password" name="password" class="form-control form-control-lg bg-light"
                                placeholder="Masukkan Password" required
                                minlength="6"
                                title="Kata sandi harus minimal 6 karakter">
                        </div>
                        <div class="input-group mb-4 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember_me">
                                <label class="form-check-label text-secondary" for="remember">Ingat saya</label>
                            </div>
                          
                        </div>
                        <div class="input-group mb-4">
                            <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">
                                Masuk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in...';
            submitBtn.classList.add('loading');
        });
    </script>
</body>
</html>