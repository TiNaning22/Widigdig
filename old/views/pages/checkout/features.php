<?php
require_once dirname(__FILE__) . '/../../../controllers/KelasController.php';

$kelasController = new KelasController();

$kelasId = $_GET['id'] ?? null;
$kelas = null;
$mentor = null;

try {
    if ($kelasId) {
        $kelas = $kelasController->getKelasById($kelasId);
        $mentor = $kelasController->getmentorbykelasid($kelasId);
    } else {
        throw new Exception("Class ID not provided.");
    }
} catch (Exception $e) {
    $kelas = null;
    error_log("Failed to fetch class: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Features</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700&display=swap" rel="stylesheet">

   <style>
    /* General Styles */
    .row-container {
        width: 100%;
        padding: 0 20px; /* Adjust padding for smaller screens */
    }

    .accordion {
        margin-bottom: 16px;
    }

    .kelas-item {
        margin-bottom: 12px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .kelas-item summary {
        padding: 16px;
        cursor: pointer;
        list-style: none;
    }

    .kelas-item summary h5 {
        font-size: 16px; /* Adjust font size for smaller screens */
        font-weight: 600;
        margin: 0;
    }

    .list-items {
        padding: 16px;
    }

    .list-items p {
    font-size: 14px;
    color: #666;
    margin: 0;
    white-space: pre-wrap; /* Ini akan memastikan baris baru dan spasi dipertahankan */
    }

    .card-coursesfeature1 {
        padding: 20px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
    }

    .card-coursesfeature1 h4 {
        font-size: 18px; /* Adjust font size for smaller screens */
        margin-bottom: 16px;
    }

    .avatar-text {
        display: flex;
        align-items: center;
        margin-bottom: 16px;
    }

    .avatar-container {
        margin-right: 16px;
    }

    .avatar {
        width: 50px; /* Adjust avatar size for smaller screens */
        height: 50px;
        border-radius: 50%;
    }

    .text p {
        font-size: 14px; /* Adjust font size for smaller screens */
        margin: 0;
    }

    .list-itemscolumn {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .list-itemscolumn li {
        font-size: 14px; /* Adjust font size for smaller screens */
        margin-bottom: 8px;
    }

    /* Media Queries for Mobile Devices */
    @media (max-width: 480px) {
        .row-container {
            padding: 0 10px; /* Further reduce padding for very small screens */
        }

        .kelas-item summary h5 {
            font-size: 14px; /* Further reduce font size for very small screens */
        }

        .list-items p {
            font-size: 12px; /* Further reduce font size for very small screens */
        }

        .card-coursesfeature1 {
            padding: 15px; /* Further reduce padding for very small screens */
        }

        .card-coursesfeature1 h4 {
            font-size: 16px; /* Further reduce font size for very small screens */
        }

        .avatar {
            width: 40px; /* Further reduce avatar size for very small screens */
            height: 40px;
        }

        .text p {
            font-size: 12px; /* Further reduce font size for very small screens */
        }

        .list-itemscolumn li {
            font-size: 12px; /* Further reduce font size for very small screens */
        }
    }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const details = document.querySelectorAll('details');

            details.forEach((detail) => {
                const content = detail.querySelector('.list-items');
                const summary = detail.querySelector('summary');

                content.style.transition = 'all 0.3s ease';

                summary.addEventListener('click', (e) => {
                    e.preventDefault();

                    if (detail.hasAttribute('open')) {
                        content.style.maxHeight = '0';
                        content.style.opacity = '0';

                        setTimeout(() => {
                            detail.removeAttribute('open');
                        }, 300);
                        return;
                    }

                    details.forEach((otherDetail) => {
                        if (otherDetail !== detail && otherDetail.hasAttribute('open')) {
                            const otherContent = otherDetail.querySelector('.list-items');
                            otherContent.style.maxHeight = '0';
                            otherContent.style.opacity = '0';

                            setTimeout(() => {
                                otherDetail.removeAttribute('open');
                            }, 300);
                        }
                    });

                    detail.setAttribute('open', '');
                    content.getBoundingClientRect();
                    content.style.maxHeight = content.scrollHeight + 'px';
                    content.style.opacity = '1';
                });

                if (!detail.hasAttribute('open')) {
                    content.style.maxHeight = '0';
                    content.style.opacity = '0';
                }
            });
        });
    </script>
</head>
<body>
   <div class="row-container" style=" padding-left: 100px; padding-right: 100px;">
    <!-- Accordion Section -->
    <div class="accordion" style="margin-bottom: 24px; margin-right: 24px">
    <?php if ($kelas): ?>
        <?php if (!empty($kelas['what_will_learn_1'])): ?>
            <details class="kelas-item" style="margin-bottom: 16px; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                <summary style="padding: 16px; cursor: pointer; list-style: none;">
                    <h5 style="font-size: 18px; font-weight: 600; margin: 0;">Kurikulum</h5>
                </summary>
                <div class="list-items" style="padding: 16px;">
                    <p style="margin: 0; font-size: 14px; color: #666; white-space: pre-wrap;"><?= nl2br(htmlspecialchars($kelas['what_will_learn_1'])); ?></p>
                </div>
            </details>
        <?php endif; ?>
        <?php if (!empty($kelas['what_will_learn_2'])): ?>
            <details class="kelas-item" style="margin-bottom: 16px; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                <summary style="padding: 16px; cursor: pointer; list-style: none;">
                    <h5 style="font-size: 18px; font-weight: 600; margin: 0;">Kurikulum</h5>
                </summary>
                <div class="list-items" style="padding: 16px;">
                    <p style="margin: 0; font-size: 14px; color: #666; white-space: pre-wrap;"><?= nl2br(htmlspecialchars($kelas['what_will_learn_2'])); ?></p>
                </div>
            </details>
        <?php endif; ?>
        <?php if (!empty($kelas['what_will_learn_3'])): ?>
            <details class="kelas-item" style="margin-bottom: 16px; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                <summary style="padding: 16px; cursor: pointer; list-style: none;">
                    <h5 style="font-size: 18px; font-weight: 600; margin: 0;">Kurikulum</h5>
                </summary>
                <div class="list-items" style="padding: 16px;">
                    <p style="margin: 0; font-size: 14px; color: #666; white-space: pre-wrap;"><?= nl2br(htmlspecialchars($kelas['what_will_learn_3'])); ?></p>
                </div>
            </details>
        <?php endif; ?>
    <?php else: ?>
        <p style="text-align: center; color: #666">No class details available.</p>
    <?php endif; ?>
    </div>

   <div class="card-container">
    <?php if ($mentor): ?>
        <div class="card-coursesfeature1">
            <h4>Profil Pengajar</h4>
            <div class="avatar-text">
                <div class="avatar-container">
                <img class="avatar"
                        src="<?= !empty($mentor['profile_picture']) 
                                ? '/public/profile-picture/' . htmlspecialchars($mentor['profile_picture']) 
                                : '/public/profile-picture/default-avatar.jpg'; ?>" 
                        alt="Avatar">
                </div>
                <div class="text">
                    <p><strong><?= htmlspecialchars($kelas['name_mentor']); ?></strong></p>
                </div>
            </div>
            <ul class="list-itemscolumn">
                <li>Email: <?= htmlspecialchars($mentor['email'] ?? 'No Email Provided'); ?></li>
                <li>Phone: <?= htmlspecialchars($mentor['phone_number'] ?? 'N/A'); ?></li>
            </ul>
        </div>
    <?php else: ?>
        <p>Mentor data not found for this class.</p>
    <?php endif; ?>
</div>


</div>
</body>
</html>