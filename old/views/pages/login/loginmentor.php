<?php
include dirname(__FILE__) . '/../../../controllers/MentorController.php';

session_start();

if (isset($_SESSION['mentor_id'])) {
    header('Location: ../../pages/mentor/mentor.php'); 
    exit();
}

$mentorModel = new MentorModel($conn);
$authController = new AuthMentorController($mentorModel);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = "Email dan Password wajib diisi.";
    } else {
        $loginMessage = $authController->login($email, $password);
        if ($loginMessage !== true) {
            $message = $loginMessage;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../assets/css/login/login.css">
    <title>Login Mentor</title>
</head>
<body>
<div class="container my-3" style="max-width: 200px; position: absolute; top: 0; left: 0; padding-left: 50px; padding-top: 20px">
    <a href="../home/home.php" style="text-decoration: none; color: inherit;">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
</div>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row border rounded-5 p-3 bg-white shadow box-area">
        <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #000842;">
                <div class="featured-image mb-8">
                    <img src="../../../assets/images/login.svg" class="img-fluid" style="width: 200px; height: 300px;" alt="Featured Image">
                </div>
                <p class="text-white fs-2" style="font-family: 'Manrope', sans-serif; font-weight: 600;">Masuk sebagai Mentor</p>
            </div>

            <div class="col-md-5 right-box" style="padding-left: 40px;">
                <div class="header-text mb-4">
                    <h2>KelasSore</h2>
                    <p>Selamat datang kembali di KelasSore.</p>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Masukkan E-mail" required>
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
                    <div>
                        <button type="submit" class="btn btn-primary w-100">Masuk</button>
                    </div>
                </form>
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
