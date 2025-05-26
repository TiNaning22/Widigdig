<?php
// File: views/pages/mentor/mentor-detail.php

// Include necessary files
include_once dirname(__FILE__) . '/../../../controllers/MentorController.php';
include_once dirname(__FILE__) . '/../../../models/MentorModel.php';
include_once dirname(__FILE__) . '/../../../services/database.php';

// Initialize controller
$mentorController = new MentorController();

// Get mentor ID from URL
$mentorId = isset($_GET['id']) ? $_GET['id'] : null;
$mentor = null;

if ($mentorId) {
    $mentor = $mentorController->getMentorById($mentorId);
}

// Redirect if no mentor found
if (!$mentor) {
    header('Location: mentor.php');
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
    <title>Detail Mentor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/mentor/mentor.css">
    <style>
        .mentor-detail-container {
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px;
        }

        .mentor-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .mentor-profile-image {
            width: 120px;
            height: 120px;
            border-radius: 60px;
            margin-right: 20px;
            object-fit: cover;
        }

        .mentor-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 1.1em;
        }

        .back-button {
            padding: 8px 16px;
            background: #f0f0f0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <?php include '../../../views/layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <a href="mentor.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            
            <div class="mentor-detail-container">
                <div class="mentor-header">
                    <img 
                        src="<?php echo !empty($mentor['profile_picture']) 
                            ? htmlspecialchars($mentor['profile_picture']) 
                            : '../../../assets/images/default-profile.png'; ?>" 
                        alt="Profile Picture"
                        class="mentor-profile-image"
                    >
                    <div>
                        <h1><?php echo htmlspecialchars($mentor['name']); ?></h1>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($mentor['email']); ?></p>
                    </div>
                </div>

                <div class="mentor-info-grid">
                    <div class="info-item">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value"><?php echo htmlspecialchars($mentor['name']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($mentor['email']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Nomor Handphone</div>
                        <div class="info-value"><?php echo htmlspecialchars($mentor['phone_number']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Gaji Diterima</div>
                        <div class="info-value">Rp <?php echo number_format($mentor['salary_recived'], 0, ',', '.'); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Gaji Tersisa</div>
                        <div class="info-value">Rp <?php echo number_format($mentor['salary_remaining'], 0, ',', '.'); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Terakhir Diperbarui</div>
                        <div class="info-value"><?php echo date('d F Y H:i', strtotime($mentor['updated_at'])); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>