<?php
session_start();

// Define allowed pages
$userAllowedPages = ['home', 'checkout', 'login', 'register', 'userprofile', 'mentor', 'loginmentor'];

$adminAllowedPages = [
    'login' => 'admin/views/pages/login/login.php',
    'dashboard' => 'admin/views/pages/dashboard/dashboard.php',
    'buku' => 'admin/views/pages/buku/buku.php',
    'mentor' => 'admin/views/pages/mentor/mentor.php',
    'kelas' => 'admin/views/pages/kelas/kelas.php'
];

// Determine if we're accessing admin section
$isAdmin = isset($_GET['admin']);

if ($isAdmin) {
    // Admin routing logic
    if (!isset($_SESSION['admin_id'])) {
        $page = 'login';
    } else {
        $page = $_GET['page'] ?? 'dashboard';
    }
    if (array_key_exists($page, $adminAllowedPages)) {
        include $adminAllowedPages[$page];
    } else {
        echo "404 - Admin page not found!";
    }
} else {
    // User routing logic
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    
    if (in_array($page, $userAllowedPages)) {
        // Konstruksi path file secara dinamis
        $pagePath = "views/pages/{$page}/{$page}.php";
        
        if (file_exists($pagePath)) {
            include $pagePath;
        } else {
            echo "Halaman tidak ditemukan";
        }
    } else {
        include "views/pages/home/home.php";
    }
}
?>