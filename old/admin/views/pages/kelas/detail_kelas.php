<?php
require_once dirname(__FILE__) . '/../../../controllers/KelasController.php';

// Initialize controller
$kelasController = new KelasController();

// Get kelas ID from URL
$kelasId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$kelasId) {
    header('Location: kelas.php');
    exit();
}

// Get kelas details
$kelas = $kelasController->getKelasById($kelasId);

if (!$kelas) {
    header('Location: kelas.php');
    exit();
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
    <title>Detail Kelas - <?php echo htmlspecialchars($kelas['name']); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/kelas/kelas.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #4b5563;
            --accent-color: #f3f4f6;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --success-color: #22c55e;
            --danger-color: #ef4444;
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text-primary);
            line-height: 1.6;
            background-color: #f8fafc;
        }

        .main-content {
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .top-bar h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .back-btn {
            padding: 0.75rem 1.5rem;
            background: var(--secondary-color);
            color: white;
            border-radius: var(--border-radius);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
            font-weight: 500;
        }

        .back-btn:hover {
            background: #374151;
            transform: translateY(-1px);
        }

        .kelas-detail {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .kelas-header {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2.5rem;
            margin-bottom: 3rem;
            align-items: start;
        }

        .kelas-image {
            width: 100%;
            height: 250px;
            overflow: hidden;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .kelas-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .kelas-image img:hover {
            transform: scale(1.05);
        }

        .kelas-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .kelas-info h2 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
        }

        .price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .section {
            margin-bottom: 2.5rem;
            padding: 1.5rem;
            background: var(--accent-color);
            border-radius: var(--border-radius);
        }

        .section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .info-card {
            background: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .session-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .session-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .session-card h4 {
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0 0 1rem 0;
            color: var(--primary-color);
        }

        .learn-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .learn-item {
            background: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            /* Tambahkan ini jika diperlukan */
            white-space: pre-wrap;
            /* Mempertahankan spasi dan baris baru */
            word-wrap: break-word;
            /* Memastikan kata yang panjang tidak keluar dari container */
        }

        .learn-item i {
            color: var(--success-color);
            font-size: 1.25rem;
            margin-top: 0.25rem;
        }

        .link-grid {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .link-item {
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: var(--transition);
            font-weight: 500;
        }

        .youtube {
            background: #ff0000;
        }

        .youtube:hover {
            background: #cc0000;
            transform: translateY(-2px);
        }

        .whatsapp {
            background: #25d366;
        }

        .whatsapp:hover {
            background: #1fa855;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .kelas-header {
                grid-template-columns: 1fr;
            }

            .kelas-image {
                height: 200px;
            }

            .session-grid,
            .learn-grid {
                grid-template-columns: 1fr;
            }

            .link-grid {
                flex-direction: column;
            }

            .link-item {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

</head>

<body>
    <?php include '../../../views/layout/sidebar.php'; ?>
    <div class="main-content">
        <div class="container">
            <div class="top-bar">
                <h1>Detail Kelas</h1>
                <a href="kelas.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="kelas-detail">
                <div class="kelas-header">
                    <div class="kelas-image">
                        <img src="../../../assets/uploads/kelas/<?php echo htmlspecialchars($kelas['image']); ?>"
                            alt="<?php echo htmlspecialchars($kelas['name']); ?>">
                    </div>
                    <div class="kelas-info">
                        <h2><?php echo htmlspecialchars($kelas['name']); ?></h2>
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span>Mentor: <?php echo htmlspecialchars($kelas['name_mentor']); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-tag"></i>
                            <span>Kategori: <?php echo htmlspecialchars($kelas['category']); ?></span>
                        </div>
                        <div class="price">
                            Rp.<?php echo number_format($kelas['price'], 2); ?>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h3><i class="fas fa-info-circle"></i> Deskripsi</h3>
                    <p><?php echo nl2br(htmlspecialchars($kelas['description'])); ?></p>
                </div>

                <div class="section">
                    <h3><i class="fas fa-clipboard-list"></i> Informasi Kelas</h3>
                    <div class="info-grid">
                        <div class="info-card">
                            <div class="info-item">
                                <i class="fas fa-users"></i>
                                <span>Kuota: <?php echo htmlspecialchars($kelas['quota']); ?></span>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-item">
                                <i class="fas fa-user-check"></i>
                                <span>Kuota Tersisa: <?php echo htmlspecialchars($kelas['quota_left']); ?></span>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Jadwal: <?php echo htmlspecialchars($kelas['schedule']); ?></span>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span>Tanggal Berakhir: <?php echo htmlspecialchars($kelas['end_date']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h3><i class="fas fa-chalkboard-teacher"></i> Sesi Pembelajaran</h3>
                    <div class="session-grid">
                        <div class="session-card">
                            <h4>Sesi 1</h4>
                            <p><?php echo nl2br(htmlspecialchars($kelas['sesion_1'])); ?></p>
                        </div>
                        <div class="session-card">
                            <h4>Sesi 2</h4>
                            <p><?php echo nl2br(htmlspecialchars($kelas['sesion_2'])); ?></p>
                        </div>
                        <div class="session-card">
                            <h4>Sesi 3</h4>
                            <p><?php echo nl2br(htmlspecialchars($kelas['sesion_3'])); ?></p>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h3><i class="fas fa-graduation-cap"></i> Kurikulum</h3>
                    <div class="learn-grid">
                        <div class="learn-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo nl2br(htmlspecialchars($kelas['what_will_learn_1'])); ?></span>
                        </div>
                        <div class="learn-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo nl2br(htmlspecialchars($kelas['what_will_learn_2'])); ?></span>
                        </div>
                        <div class="learn-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo nl2br(htmlspecialchars($kelas['what_will_learn_3'])); ?></span>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h3><i class="fas fa-link"></i> Link</h3>
                    <div class="link-grid">
                        <a href="<?php echo htmlspecialchars($kelas['link_youtube']); ?>" target="_blank" class="link-item youtube">
                            <i class="fab fa-youtube"></i>
                            <span>Video YouTube</span>
                        </a>
                        <a href="<?php echo htmlspecialchars($kelas['link_wa']); ?>" target="_blank" class="link-item whatsapp">
                            <i class="fab fa-whatsapp"></i>
                            <span>Grup WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>