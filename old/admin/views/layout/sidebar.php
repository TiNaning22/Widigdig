<?php
if (!function_exists('isActive')) {
    function isActive($menuItem)
    {
        $currentUrl = $_SERVER['REQUEST_URI'];
        if ($currentUrl == '/' || $currentUrl == '/index.php' || strpos($currentUrl, 'dashboard') !== false) {
            $currentPage = 'dashboard';
        } else {
            preg_match('/pages\/([^\/]+)/', $currentUrl, $matches);
            $currentPage = isset($matches[1]) ? $matches[1] : 'dashboard';
        }
        return (strtolower($menuItem) === strtolower($currentPage)) ? 'active' : '';
    }
}

$navItems = [
    ['icon' => 'fa-home', 'text' => 'Dashboard', 'url' => '/admin/views/pages/dashboard/dashboard.php'],
    ['icon' => 'fa-money-bill', 'text' => 'Pembayaran', 'url' => '/admin/views/pages/pembayaran/pembayaran.php'],
    ['icon' => 'fa-chalkboard-teacher', 'text' => 'Mentor', 'url' => '/admin/views/pages/mentor/mentor.php'],
    ['icon' => 'fa-graduation-cap', 'text' => 'Kelas', 'url' => '/admin/views/pages/kelas/kelas.php'],
    ['icon' => 'fa-book', 'text' => 'Buku', 'url' => '/admin/views/pages/buku/buku.php'],
    ['icon' => 'fa-newspaper', 'text' => 'Artikel', 'url' => '/admin/views/pages/artikel/artikel.php'],
];
?>

<div class="sidebar">
    <div class="logo">
        <img src="../../../public/logo.svg" alt="Logo" style="max-width: 100%; height: auto;">
    </div>
    <div class="nav-container">
        <nav>
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo $item['url']; ?>"
                    class="nav-item <?php echo isActive($item['text']); ?>">
                    <i class="fas <?php echo $item['icon']; ?>"></i>
                    <?php echo $item['text']; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="logout-container">
            <a href="/admin/controllers/auth-handler.php?action=logout"
                class="nav-item logout-btn"
                onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <i class="fas fa-power-off"></i>
                Logout
            </a>
        </div>
    </div>
</div>

<style>
    .sidebar {
        width: 250px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        background-color: #fff;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: flex;
        flex-direction: column;
    }

    .logo {
        font-size: 24px;
        font-weight: bold;
        color: #4A6CF7;
        margin-bottom: 30px;
    }

    .nav-container {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: calc(100vh - 100px);
    }

    .nav-item {
        padding: 12px 15px;
        margin-bottom: 5px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #666;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .nav-item:hover {
        background-color: #f0f0f0;
    }

    .nav-item.active {
        background-color: #4A6CF7;
        color: white;
        text-decoration: none;
    }

    .nav-item i {
        width: 20px;
    }

    .logout-container {
        margin-top: auto;
        border-top: 1px solid #eee;
        padding-top: 10px;
    }

    .logout-btn {
        color: #dc3545;
    }

    .logout-btn:hover {
        background-color: #dc3545;
        color: white;
    }
</style>