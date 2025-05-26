<?php
session_start();
include dirname(__FILE__) . '/../../../controllers/InvoiceController.php';
include dirname(__FILE__) . '/../../layouts/header.php';
$userId = $_SESSION['user_id'] ?? 3; // Default ke 3 jika session tidak diatur
$invoicesController = new InvoicesController();

try {
    // Ambil kelas yang sudah dibeli user
    $purchasedClasses = $invoicesController->getkelasuser($userId) ?? [];
} catch (Exception $e) {
    $purchasedClasses = [];
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelas Anda - KelasSore</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/dashboard/header.css">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        .login, .join {
            color: black;
            text-decoration: none;
        }

        .login:hover, .join:hover {
            color: #333;
        }

        .class-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            transition: transform 0.3s ease;
        }

        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .class-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }

        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .section-header {
            text-align: center;
            margin: 40px 0;
        }

        .memer {
                    margin-bottom: 50px;

            font-weight: bold;
            color: #333;
        }
          .rules-section {
        padding: 60px 0;
        background: #f8f9fa;
    }

    .rules-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .rules-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .rules-header {
        padding: 25px;
        position: relative;
        overflow: hidden;
    }

    .rules-header.regular {
        background: linear-gradient(135deg, #6366f1, #818cf8);
    }

    .rules-header.private {
        background: linear-gradient(135deg, #10b981, #34d399);
    }

    .rules-header h3 {
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .rules-content {
        padding: 30px;
    }

    .rules-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .rules-list li {
        position: relative;
        padding-left: 28px;
        margin-bottom: 15px;
        color: #4b5563;
        line-height: 1.6;
    }

    .rules-list li::before {
        content: "✓";
        position: absolute;
        left: 0;
        color: #6366f1;
        font-weight: bold;
    }

    .private .rules-list li::before {
        color: #10b981;
    }

    .section-title {
        margin-bottom: 50px;
        text-align: center;
        position: relative;
    }

    .section-title::after {
        content: "";
        display: block;
        width: 60px;
        height: 4px;
        background: #6366f1;
        margin: 20px auto 0;
        border-radius: 2px;
    }

    .rules-subtitle {
        font-size: 1.1rem;
        font-weight: 600;
        color: #374151;
        margin: 25px 0 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
    }
    </style>
</head>
<body>
<?php renderHeader(); ?>
    <section class="courses">
        <div class="container">
            <div class="section-header">
                <h2 class="memer">Kelas Anda</h2>
            </div>
            <div class="slider-wrapper">
                <div class="class-grid">
                    <?php if (!empty($purchasedClasses)): ?>
                        <?php foreach ($purchasedClasses as $class): ?>
                            <a href="../class/index.php?id=<?php echo urlencode($class['id']); ?>" class="class-card-link">
                                <div class="class-card">
                                    <?php
                                    $imagePath = !empty($class['image']) 
                                        ? '/public/image-class/' . basename($class['image']) 
                                        : '/assets/images/default-course.svg';
                                    ?>
                                    <img 
                                        src="<?php echo htmlspecialchars($imagePath); ?>" 
                                        alt="Gambar Kelas <?php echo isset($class['image']) ? htmlspecialchars($class['image']) : 'Tidak diketahui'; ?>"
                                        onerror="this.onerror=null; this.src='/assets/images/default-course.svg';"
                                    >   
                                    <h3><?php echo htmlspecialchars($class['name']); ?></h3>
                                    <p>Instructor: <?php echo htmlspecialchars($class['name_mentor']); ?></p>
                                    <div class="price">Rp <?php echo number_format($class['price'], 0, ',', '.'); ?></div>
                                    <div class="meta" style="text-align: left; margin-bottom: 5px;">
                                        <span>Sesi Kelas di Mulai: <?php echo htmlspecialchars($class['schedule']); ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Belum ada kelas yang terbayarkan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <section class="rules-section">
    <div class="container">
        <h2 class="memer">Ketentuan Kelas</h2>
        <div class="row g-4">
            <!-- Regular Class Card -->
            <div class="col-lg-6">
                <div class="rules-card">
                    <div class="rules-header regular">
                        <h3>Kelas Reguler</h3>
                    </div>
                    <div class="rules-content">
                        <h4 class="rules-subtitle">Ketentuan:</h4>
                        <ul class="rules-list">
                            <li>Kelas dimulai ketika kuota 25 peserta sudah terpenuhi. Jika dalam 30 hari kuota belum terpenuhi, kelas akan tetap dimulai</li>
                            <li>3 sesi pembelajaran setiap hari Sabtu via Zoom</li>
                            <li>Durasi maksimal 3 jam per sesi</li>
                            <li>Diskusi grup WhatsApp dengan mentor (13.00-20.00 WIB)</li>
                            <li>Lifetime access untuk modul dan video rekaman</li>
                        </ul>
                        
                        <h4 class="rules-subtitle">Teknis Kelas:</h4>
                        <ul class="rules-list">
                            <li>Maksimal 25 peserta per kelas</li>
                            <li>3 pertemuan (teori dan latihan via Zoom)</li>
                            <li>Coaching di luar kelas via chat dan voice note</li>
                            <li>Bebas bertanya selama kelas berlangsung</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Private Class Card -->
            <div class="col-lg-6">
                <div class="rules-card">
                    <div class="rules-header private">
                        <h3>Kelas Private</h3>
                    </div>
                    <div class="rules-content">
                        <h4 class="rules-subtitle">Ketentuan:</h4>
                        <ul class="rules-list">
                            <li>2 pertemuan (maksimal 120 menit per pertemuan)</li>
                            <li>Focus on case - pembelajaran berbasis masalah</li>
                            <li>One-on-one learning (1 peserta)</li>
                            <li>Waktu fleksibel sesuai kesepakatan</li>
                        </ul>
                        
                        <h4 class="rules-subtitle">Teknis Kelas:</h4>
                        <ul class="rules-list">
                            <li>Pembelajaran individual</li>
                            <li>2 pertemuan via Zoom (teori dan latihan)</li>
                            <li>Coaching 24 jam via chat dan voice note</li>
                            <li>Bebas bertanya dan konsultasi case</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleMenu() {
            document.querySelector('.nav-links').classList.toggle('active');
        }

        function slideLeft() {
            document.querySelector(".class-grid").scrollBy({ left: -300, behavior: "smooth" });
        }

        function slideRight() {
            document.querySelector(".class-grid").scrollBy({ left: 300, behavior: "smooth" });
        }

        // Function to show class rules
       
    </script>
</body>
</html>