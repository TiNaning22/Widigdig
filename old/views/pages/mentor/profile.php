<?php
session_start();

// Include header component
include dirname(__FILE__) . '/header.php';
include dirname(__FILE__) . '/../../../controllers/CatatanController.php';

// Check if mentor is logged in
if (!isset($_SESSION['mentor_id'])) {
    header('Location: /views/pages/login/login.php');
    exit();
}

$mentorId = $_SESSION['mentor_id'];

// Include necessary files
include dirname(__FILE__) . '/../../../controllers/MentorCOntroller.php';
include dirname(__FILE__) . '/../../../services/database.php';

$AuthMentorController = new AuthMentorController();
$mentor = $AuthMentorController->getMentorProfile($mentorId);
$catatanController = new CatatanController();
$catatanList = $catatanController->getAllCatatan();

if (!$mentor) {
    session_destroy();
    header('Location: /views/pages/login/login.php');
    exit();
}

$defaultProfilePicture = 'data:image/svg+xml;base64,' . base64_encode('
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ddd" width="80px" height="80px">
    <circle cx="12" cy="8" r="4" />
    <path d="M12 14c-5 0-9 2.5-9 5v1h18v-1c0-2.5-4-5-9-5z"/>
</svg>
');

$profilePicture = !empty($mentor['profile_picture']) ? $mentor['profile_picture'] : $defaultProfilePicture;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/mentor/mentor.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .container{
            padding-top: 20px;
        }
            .section-header h2 {
        color: #333;
        font-family: 'Roboto', sans-serif;

        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }
        .carousel-container {
            position: relative;
            max-width: 1200px;
            margin: 2rem auto;
            overflow: hidden;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .catatan-card {
            flex: 0 0 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-right: 1rem;
            padding: 1rem;
            text-align: left;
        }

        .catatan-card h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .catatan-card p {
            font-size: 0.9rem;
            color: #555;
        }

        .catatan-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s;
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            z-index: 10;
        }

      

        .carousel-btn:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .no-catatan {
            text-align: center;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 8px;
            color: #666;
        }




.carousel-btn:hover {
    background-color: rgba(0, 0, 0, 0.8);
}

/* No Catatan Style */
.no-catatan {
    text-align: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 8px;
    color: #666;
}

/* Responsive Design */
@media (max-width: 768px) {
    .catatan-card {
        flex: 0 0 80%; /* Smaller width for mobile */
    }
}


    .no-catatan {
        grid-column: 1 / -1;
        text-align: center;
        padding: 2rem;
        background: #f8f9fa;
        border-radius: 8px;
        color: #666;
    }

    </style>
</head>
<body>
    <?php renderHeaderProfile(); ?>

    <div class="container">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars(explode(' ', $mentor['name'])[0]); ?></h1>
        </div>

        <div class="profile-section">
            <div class="profile-header">
                <div class="profile-header-left">
                    <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" class="profile-image">
                    <div>
                        <h2><?php echo htmlspecialchars($mentor['name']); ?></h2>
                        <p><?php echo htmlspecialchars($mentor['email']); ?></p>
                    </div>
                </div>
            </div>

            <form action="#" method="POST">
                <div class="form-group mb-3">
                    <label>Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($mentor['name']); ?>" readonly>
                </div>
                <div class="form-group mb-3">
                    <label>Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($mentor['email']); ?>" readonly>
                </div>
                <div class="form-group mb-3">
                    <label>Phone Number</label>
                    <input type="tel" class="form-control" value="<?php echo htmlspecialchars($mentor['phone_number']); ?>" readonly>
                </div>

                <!-- Logout button with modal -->
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    Logout
                </button>
            </form>
        </div>
        <section>
    <div class="section-header">
        <h2>Catatan dari Murid</h2>
    </div>

    <div class="carousel-container">
        <div class="carousel-track">
            <?php if (!empty($catatanList)): ?>
                <?php foreach ($catatanList as $catatan): ?>
                    <div class="catatan-card">
                        <h3><?= htmlspecialchars($catatan['title']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($catatan['content'])) ?></p>
                        <small><?= date('d M Y', strtotime($catatan['created_at'])) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-catatan">
                    <p>Belum ada catatan tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
    </div>

    <!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <!-- Tombol Logout -->
                <a href="/views/pages/userprofile/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>


    
</body>
<footer>
    <!-- Include footer component -->
    <?php include dirname(__FILE__) . '/../../layouts/footer.php'; ?>
</footer>
<script>
        const track = document.querySelector('.carousel-track');
        const prevButton = document.querySelector('.prev-btn');
        const nextButton = document.querySelector('.next-btn');

        let currentIndex = 0;

        function updateCarousel() {
            const cardWidth = document.querySelector('.catatan-card').offsetWidth + 16; // Card width + margin
            track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
        }


        // Auto-scroll every 5 seconds
        setInterval(() => {
            const cardCount = document.querySelectorAll('.catatan-card').length;
            currentIndex = (currentIndex + 1) % cardCount;
            updateCarousel();
        }, 5000);
    </script>
</html>
