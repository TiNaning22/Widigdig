<?php
require_once dirname(__DIR__, 2) . '/controllers/CatatanController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mentorId = $_POST['mentor_id'] ?? null;
    $mentorNotes = $_POST['mentor_notes'] ?? null;

    if ($mentorId && !empty($mentorNotes)) {
        $catatanController = new CatatanController();
        $createdAt = date('Y-m-d H:i:s');
        $updatedAt = $createdAt;

        try {
            $catatanController->createCatatan($mentorId, 'Mentor Notes', $mentorNotes, $createdAt, $updatedAt);
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?message=success');
        } catch (Exception $e) {
            error_log("Failed to save mentor notes: " . $e->getMessage());
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?message=error');
        }
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?message=invalid');
    }
    exit;
}
