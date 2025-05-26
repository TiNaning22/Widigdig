<?php
// File: controllers/ArtikelController.php

require_once dirname(__FILE__) . '/../models/ArtikelModel.php';

class ArtikelController {
    private $artikelModel;

    public function __construct() {
        $this->artikelModel = new ArtikelModel();
    }

    public function getAllArtikel() {
        return $this->artikelModel->getAllArtikel();
    }

    public function getArtikelById($artikelId) {
        $artikel = $this->artikelModel->getArtikelById($artikelId);

        // Tambahkan nilai default jika data tidak ditemukan
        $artikel['subtitle'] = $artikel['subtitle'] ?? 'No subtitle available';
        $artikel['image'] = $artikel['image'] ?? null;

        return $artikel;
    }

    public function createArtikel($title, $subtitle, $content, $image) {
        $imageFileName = uniqid() . '_' . basename($image['name']);

        $imagePath = '../../../public/image-artikel/' . $imageFileName;

        $createdAt = $updatedAt = date('Y-m-d H:i:s');

        if (!file_exists('../../../public/image-artikel')) mkdir('../../../public/image-artikel', 0777, true);

        if (!move_uploaded_file($image['tmp_name'], $imagePath)) return "Failed to upload image.";

        return $this->artikelModel->insertArtikel($title, $subtitle, $content, $imagePath, $createdAt, $updatedAt);
    }

    public function updateArtikel($artikelId, $title, $subtitle, $content, $image) {
        $existingArtikel = $this->artikelModel->getArtikelById($artikelId);
    
        // Gunakan file lama jika tidak ada file baru yang diunggah
        $imagePath = !empty($image['name']) ? '../../../public/image-artikel/' . uniqid() . '_' . basename($image['name']) : $existingArtikel['image'];

        $updatedAt = date('Y-m-d H:i:s');
    
        // Buat direktori jika belum ada
        if (!file_exists('../../../public/image-artikel')) mkdir('../../../public/image-artikel', 0777, true);
    
        // Pindahkan file baru (jika ada)
        if (!empty($image['name']) && !move_uploaded_file($image['tmp_name'], $imagePath)) {
            return "Failed to upload image.";
        }
    
        return $this->artikelModel->updateArtikel($artikelId, $title, $subtitle, $content, $imagePath, $updatedAt);
    }

    public function deleteArtikel($artikelId) {
        return $this->artikelModel->deleteArtikel($artikelId);
    }
}
?>
