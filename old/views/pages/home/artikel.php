<?php
// File: views/pages/home/articles.php
require_once dirname(__FILE__) . '/../../../controllers/ArtikelController.php';
$artikelController = new ArtikelController();

try {
    // Menggunakan metode getAllArtikel()
    $articles = $artikelController->getAllArtikel();
} catch (Exception $e) {
    $articles = [];
    error_log("Gagal mengambil artikel: " . $e->getMessage());
}
?>

<section class="articles-section">
  <div class="articles-container">
    <div class="articles-header fade-up">
      <h2 class="articles-title">Artikel Terbaru</h2>
      <a href="#" class="articles-see-all">
        Lihat semua artikel
        <span class="arrow-icon">→</span>
      </a>
    </div>
    
    <div class="articles-wrapper fade-in">
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                <article class="article-card">
                    <a href="/views/pages/article-detail/index.php?id=<?php echo urlencode($article['id']); ?>" class="article-link">
                        <div class="article-image">
                        <?php
                            // Penanganan path gambar
                            if (!empty($article['image'])) {
                                $filename = basename($article['image']);
                                $imagePath = "/public/image-artikel/" . $filename;
                            } else {
                                $imagePath = '/assets/images/default-article.svg';
                            }
                        ?>
                        <img 
                            src="<?php echo htmlspecialchars($imagePath); ?>" 
                            alt="Gambar Artikel <?php echo isset($article['title']) ? htmlspecialchars($article['title']) : 'Tidak diketahui'; ?>"
                            onerror="this.onerror=null; this.src='/assets/images/default-article.svg';"
                        >
                        </div>
                        <div class="article-content">
                            <h3 class="article-title"><?php echo isset($article['title']) ? htmlspecialchars($article['title']) : 'Judul tidak tersedia'; ?></h3>
                            <p class="article-subtitle"><?php echo isset($article['subtitle']) ? htmlspecialchars($article['subtitle']) : 'Subjudul tidak tersedia'; ?></p>
                            <div class="article-meta">
                                <p class="article-date">Dipublikasikan: <?php echo isset($article['created_at']) ? htmlspecialchars(date('d M Y', strtotime($article['created_at']))) : 'Tidak diketahui'; ?></p>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-articles-message">Tidak ada artikel tersedia saat ini. Silakan cek kembali nanti.</p>
        <?php endif; ?>
    </div>
  </div>
</section>
