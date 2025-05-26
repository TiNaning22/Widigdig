<?php
// Include necessary files
include dirname(__DIR__, 3) . '/services/database.php'; // Database connection
include dirname(__DIR__, 3) . '/models/MentorModel.php';  // MentorModel
include dirname(__DIR__, 3) . '/autoload.php'; // Autoload (if any)

session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input from the login form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Initialize MentorModel
    $mentorModel = new MentorModel();

    // Attempt to login with the provided email and password
    $mentor = $mentorModel->loginMentor($email, $password);

    if ($mentor) {
        // Login successful
        $_SESSION['mentor_id'] = $mentor['id'];
        $_SESSION['mentor_name'] = $mentor['name'];
        $_SESSION['mentor_email'] = $mentor['email'];
        // Redirect to the dashboard or home page
        header('Location: dashboard.php');
        exit();
    } else {
        // Login failed, show error message
        $_SESSION['login_error'] = 'Email or password is incorrect';
        header('Location: loginmentor.php');
        exit();
    }
}
?>