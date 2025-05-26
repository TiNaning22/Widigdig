<?php
// File: views/pages/home/privatecourses.php
require_once dirname(__FILE__) . '/../../../controllers/KelasController.php';
$kelasController = new KelasController();

try {
    // Using the new showPrivateKelas() method
    $courses = $kelasController->showPrivateKelas();
} catch (Exception $e) {
    $courses = [];
    error_log("Gagal mengambil kursus private: " . $e->getMessage());
}
?>

<section class="private-courses">
    <div class="private-container" style="padding: 40px 56px; max-width: 1280px; margin: 0 auto;">
        <div class="private-header fade-up" style="margin-bottom: 32px;">
            <h2 class="private-title" style="font-size: 24px; font-weight: 600; color: #333;">Kursus Lainnya</h2>
        </div>

        <div class="private-cards-wrapper fade-in" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; padding: 16px 0;">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="private-card" style="background: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease;">
                        <a href="/views/pages/checkout/index.php?id=<?php echo urlencode($course['id']); ?>" class="private-card-link" style="text-decoration: none; color: inherit; display: block;">
                            <div class="private-card-image" style="width: 100%; height: 180px; overflow: hidden; border-radius: 8px 8px 0 0;">
                                <?php
                                if (!empty($course['image'])) {
                                    $filename = basename($course['image']);
                                    $imagePath = "/public/image-class/" . $filename;
                                } else {
                                    $imagePath = '/../../../assets/images/404.jpg';
                                }
                                ?>
                                <img
                                    src="<?php echo htmlspecialchars($imagePath); ?>"
                                    alt="Gambar Kelas <?php echo isset($course['name']) ? htmlspecialchars($course['name']) : 'Tidak diketahui'; ?>"
                                    onerror="this.onerror=null; this.src='/assets/images/404.jpg';"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="private-card-content" style="padding: 16px;">
                                <h3 class="private-course-title" style="font-size: 18px; font-weight: 600; margin-bottom: 8px;"><?php echo isset($course['name']) ? htmlspecialchars($course['name']) : 'Nama tidak tersedia'; ?></h3>
                                <p class="private-instructor-name" style="font-size: 14px; color: #666; margin-bottom: 12px;"><?php echo isset($course['name_mentor']) ? htmlspecialchars($course['name_mentor']) : 'Tidak diketahui'; ?></p>
                                <p class="private-price" style="font-size: 16px; font-weight: 600; color: #2c5282; margin-bottom: 8px;">Rp <?php echo isset($course['price']) ? number_format($course['price'], 0, ',', '.') : '0'; ?></p>
                                <div class="private-students" style="font-size: 14px; color: #666; margin-bottom: 4px;">Kuota: <?php echo isset($course['quota_left']) ? $course['quota_left'] : '0'; ?></div>
                                <p class="private-status" style="font-size: 14px; color: #666;">Status: <?php echo isset($course['status']) ? htmlspecialchars($course['status']) : 'Tidak tersedia'; ?></p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-courses-message" style="text-align: center; color: #666; padding: 32px; grid-column: 1 / -1;">Tidak ada kursus private tersedia saat ini. Silakan cek kembali nanti.</p>
            <?php endif; ?>
        </div>
    </div>
</section>