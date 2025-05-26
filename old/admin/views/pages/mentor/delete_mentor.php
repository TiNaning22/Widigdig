<?php
include_once dirname(__FILE__) . '/../../../controllers/MentorController.php';

header('Content-Type: application/json'); // Set header first

session_start();
if (!isset($_SESSION['admin_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mentor_id'])) {
    $mentorController = new MentorController();
    $result = $mentorController->delete($_POST['mentor_id']);

    echo json_encode($result);
    exit();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit();
}
