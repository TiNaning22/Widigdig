<?php
require_once dirname(__FILE__) . '/../../../controllers/KelasController.php';
require_once dirname(__FILE__) . '/../../../controllers/CatatanController.php';

// Initialize controllers
$kelasController = new KelasController();
$catatanController = new CatatanController();

$kelasId = $_GET['id'] ?? null;
$kelas = null;
$mentor = null;
$message = '';

try {
    if ($kelasId) {
        // Fetch specific class by ID
        $kelas = $kelasController->getKelasById($kelasId);

        // Fetch mentor details by class ID
        $mentor = $kelasController->getmentorbykelasid($kelasId);

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['catatan_submit']) && isset($_SESSION['user_id'])) {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $mentorId = $mentor['id'] ?? null; // Ensure mentor ID exists
            $createdAt = date('Y-m-d H:i:s');
            $updatedAt = $createdAt;

            if ($mentorId && $title && $content) {
                $catatanController->createCatatan($mentorId, $title, $content, $createdAt, $updatedAt);
                $message = 'Catatan kamu berhasil dikirim ke mentor kamu.';
            } else {
                $message = 'Please fill in all fields.';
            }
        }
    } else {
        throw new Exception("Class ID not provided.");
    }
} catch (Exception $e) {
    $kelas = null;
    error_log("Failed to fetch class: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Features</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700&display=swap" rel="stylesheet">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const details = document.querySelectorAll('details');

            details.forEach((detail) => {
                const content = detail.querySelector('.list-items');
                const summary = detail.querySelector('summary');

                content.style.transition = 'all 0.3s ease';

                summary.addEventListener('click', (e) => {
                    e.preventDefault();

                    if (detail.hasAttribute('open')) {
                        content.style.maxHeight = '0';
                        content.style.opacity = '0';

                        setTimeout(() => {
                            detail.removeAttribute('open');
                        }, 300);
                        return;
                    }

                    details.forEach((otherDetail) => {
                        if (otherDetail !== detail && otherDetail.hasAttribute('open')) {
                            const otherContent = otherDetail.querySelector('.list-items');
                            otherContent.style.maxHeight = '0';
                            otherContent.style.opacity = '0';

                            setTimeout(() => {
                                otherDetail.removeAttribute('open');
                            }, 300);
                        }
                    });

                    detail.setAttribute('open', '');
                    content.getBoundingClientRect();
                    content.style.maxHeight = content.scrollHeight + 'px';
                    content.style.opacity = '1';
                });

                if (!detail.hasAttribute('open')) {
                    content.style.maxHeight = '0';
                    content.style.opacity = '0';
                }
            });
        });
    </script>
</head>

<body>
    <div class="row-container">
        <!-- Accordion Section -->
        <div class="accordion">
            <?php if ($kelas): ?>
                <?php if (!empty($kelas['sesion_1'])): ?>
                    <details class="kelas-item">
                        <summary>
                            <h5>Sesi 1</h5>
                        </summary>
                        <div class="list-items">
                            <button class="session-content-btn"
                                onclick="window.open('<?= htmlspecialchars($kelas['sesion_1']); ?>', '_blank')">
                                Masuk Ke Sesi 1
                            </button>
                        </div>
                    </details>
                <?php endif; ?>
                <?php if (!empty($kelas['sesion_2'])): ?>
                    <details class="kelas-item">
                        <summary>
                            <h5>Sesi 2</h5>
                        </summary>
                        <div class="list-items">
                            <button class="session-content-btn"
                                onclick="window.open('<?= htmlspecialchars($kelas['sesion_2']); ?>', '_blank')">
                                Masuk Ke Sesi 2
                            </button>
                        </div>
                    </details>
                <?php endif; ?>
                <?php if (!empty($kelas['sesion_3'])): ?>
                    <details class="kelas-item">
                        <summary>
                            <h5>Sesi 3</h5>
                        </summary>
                        <div class="list-items">
                            <button class="session-content-btn"
                                onclick="window.open('<?= htmlspecialchars($kelas['sesion_3']); ?>', '_blank')">
                                Masuk Ke Sesi 3
                            </button>
                        </div>
                    </details>
                <?php endif; ?>
            <?php else: ?>
                <p>No class details available.</p>
            <?php endif; ?>
        </div>


        <!-- Cards Section -->
        <div class="card-container">
            <?php if ($mentor): ?>
                <div class="card-coursesfeature1">
                    <h4>Mentor Profile</h4>
                    <div class="avatar-text">
                        <div class="avatar-container">
                            <img class="avatar"
                                src="<?= !empty($mentor['profile_picture'])
                                            ? 'path_to_images/' . htmlspecialchars($mentor['profile_picture'])
                                            : 'default-avatar.jpg'; ?>"
                                alt="Avatar">
                        </div>
                        <div class="text">
                            <p><strong><?= htmlspecialchars($kelas['name_mentor']); ?></strong></p>
                        </div>
                    </div>
                    <ul class="list-itemscolumn">
                        <li>Email: <?= htmlspecialchars($mentor['email'] ?? 'No Email Provided'); ?></li>
                        <li>Phone: <?= htmlspecialchars($mentor['phone_number'] ?? 'N/A'); ?></li>
                    </ul>
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success" role="alert">
                            <?= htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Note Form -->
                    <div class="note-form">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <h4>Kirim catatan ke mentor</h4>
                            <form method="POST" class="mt-3">
                                <div class="form-group">
                                    <label for="noteTitle" class="form-label">Judul</label>
                                    <input type="text" name="title" id="noteTitle" class="form-control" placeholder="Masukkan judul laporan" required>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="noteContent" class="form-label">Isi</label>
                                    <textarea name="content" id="noteContent" class="form-control" rows="5" placeholder="Masukkan isi laporan di sini..." required></textarea>
                                </div>
                                <button type="submit" name="catatan_submit" class="btn btn-primary mt-3">Kirim laporan</button>
                            </form>
                        <?php else: ?>
                            <p></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <p>Mentor data not found for this class.</p>
            <?php endif; ?>

            <!-- Include the book component -->
            <?php include_once dirname(__FILE__) . '/../class/book.php'; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>