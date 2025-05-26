<?php
define('BASE_PATH', dirname(__DIR__, 3)); 

require_once BASE_PATH . '/models/KelasModel.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$kelasModel = new KelasModel();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /error.php?msg=Invalid%20Course%20ID");
    exit;
}

$courseId = intval($_GET['id']);

try {
    $course = $kelasModel->getKelasById($courseId);
} catch (Exception $e) {
    error_log("Failed to fetch course: " . $e->getMessage());
    header("Location: /error.php?msg=Course%20not%20found");
    exit;
}

if ($course) {
    $courseName = htmlspecialchars($course['name']);
    $courseDescription = htmlspecialchars($course['description']);
    $courseInstructor = htmlspecialchars($course['name_mentor']);
    $courseStudents = $course['quota'] - $course['quota_left'];
    $courseQuotaLeft = $course['quota_left'];
    $courseQuota = $course['quota'];
    $coursePrice = $course['price'];
    $courseSchedule = htmlspecialchars($course['schedule'] ?? 'Schedule not available');
    
    // Simplified image path handling
    if (!empty($course['image'])) {
        $courseImage = "/public/image-class/" . htmlspecialchars($course['image']);
    } else {
        $courseImage = '/assets/images/default-course.svg';
    }
} else {
    header("Location: /error.php?msg=Course%20not%20found");
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);

// WhatsApp Group URL handling
$waGroupUrl = !empty($course['link_wa']) 
    ? htmlspecialchars($course['link_wa']) 
    : '#';

$joinWaButtonClass = !empty($course['link_wa']) 
    ? 'join-group' 
    : 'join-group disabled';

$joinWaButtonText = !empty($course['link_wa']) 
    ? 'Join Group WA' 
    : 'Group Link Unavailable';

// YouTube URL handling
$youtubeUrl = !empty($course['link_youtube']) 
    ? htmlspecialchars($course['link_youtube']) 
    : '#';

$joinYtButtonClass = !empty($course['link_youtube']) 
    ? 'join-youtube' 
    : 'join-youtube disabled';

$joinYtButtonText = !empty($course['link_youtube']) 
    ? 'Watch on YouTube' 
    : 'Video Unavailable';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            font-size: 15px;
            font-weight: 400;
        }
        .course-header h1 {
            font-family: 'Roboto', sans-serif;
            font-weight: 700;
        }
        .course-content p {
            text-align: justify;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem 1rem 1rem 2rem;
            background: #F1F4FF;
            border-radius: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        .join-group, .join-youtube {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .join-group {
            background-color: #25D366;
            color: white;
        }
        .join-youtube {
            background-color: #FF0000;
            color: white;
        }
        .join-group:hover, .join-youtube:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        .disabled {
            background-color: #ccc;
            cursor: not-allowed;
            pointer-events: none;
        }
        .course-image {
            padding-left: 1.5rem; /* Memberikan jarak antara gambar dan teks di atasnya */
            padding-top: 1.5rem;
        }

        .course-image img {
            max-width: 100%; /* Gambar tidak akan melebihi lebar container */
            height: auto; /* Tinggi gambar akan disesuaikan secara proporsional */
            border-radius: 16px;
            display: block; /* Menghilangkan ruang ekstra di bawah gambar */
            margin-left: auto; /* Pusatkan gambar secara horizontal */
            margin-right: auto; /* Pusatkan gambar secara horizontal */
            object-fit: cover; /* Pastikan gambar menutupi area yang tersedia tanpa distorsi */
            width: 100%; /* Gambar akan mengisi lebar container */
            max-height: 400px; /* Atur tinggi maksimum gambar */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="course-header">
            <h1><?php echo $courseName; ?></h1>
        </div>

        <div class="course-content">
            <div class="course-details">
                <p><?php echo $courseDescription; ?></p>
                <p><strong>Instructor:</strong> <?php echo $courseInstructor; ?></p>
            
                <div class="course-schedule">
                    <p><strong>Jadwal:</strong> <?php echo $courseSchedule; ?></p>
                </div>

                <div class="button-group">
                    <a href="<?php echo $waGroupUrl; ?>" 
                       class="<?php echo $joinWaButtonClass; ?>" 
                       <?php echo empty($course['link_wa']) ? 'disabled' : 'target="_blank"'; ?>>
                       <?php echo $joinWaButtonText; ?>
                    </a>
                    <a href="<?php echo $youtubeUrl; ?>" 
                       class="<?php echo $joinYtButtonClass; ?>" 
                       <?php echo empty($course['link_youtube']) ? 'disabled' : 'target="_blank"'; ?>>
                       <?php echo $joinYtButtonText; ?>
                    </a>
                </div>
            </div>
            <div class="course-image">
                <img 
                    src="<?php echo $courseImage; ?>" 
                    alt="<?php echo $courseName; ?>"
                    onerror="this.onerror=null; this.src='/assets/images/default-course.svg';"
                >
            </div>
        </div>
    </div>
</body>
</html>