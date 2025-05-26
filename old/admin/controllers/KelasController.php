<?php
require_once dirname(__FILE__) . '/../models/KelasModel.php';
require_once dirname(__FILE__) . '/../models/BookModel.php';
require_once dirname(__FILE__) . '/../models/MentorModel.php'; // Model untuk Mentor

class KelasController
{
    private $kelasModel;
    private $bookModel;
    private $mentorModel; // Menambahkan MentorModel

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->bookModel = new BookModel();
        $this->mentorModel = new MentorModel(); // Inisialisasi MentorModel
    }

    // Method untuk mengambil semua mentor
    public function getAllMentors()
    {
        return $this->mentorModel->getAllMentors(); // Ambil semua data mentor dari database
    }

    public function getAllKelas()
    {
        return $this->kelasModel->getAllKelas();
    }

    public function getKelasById($kelasId)
    {
        return $this->kelasModel->getKelasById($kelasId);
    }

    public function createKelas($data, $imageFile)
    {
        $imageFileName = uniqid() . '_' . basename($imageFile['name']);
        $imageFilePath = '../../../../public/image-class/' . $imageFileName;

        $createdAt = $updatedAt = date('Y-m-d H:i:s');

        if (!file_exists('../../../../public/image-class')) mkdir('../../../../public/image-class', 0777, true);

        if (!move_uploaded_file($imageFile['tmp_name'], $imageFilePath)) {
            return "Failed to upload image.";
        }

        $data['image'] = $imageFilePath;
        $data['created_at'] = $createdAt;
        $data['updated_at'] = $updatedAt;

        // Ensure mentor_id is properly handled (set to NULL if empty)
        $data['mentor_id'] = isset($data['mentor_id']) && !empty($data['mentor_id']) ? $data['mentor_id'] : NULL;

        // Handle start_date and end_date similar to updateKelas
        foreach (['start_date', 'end_date'] as $dateField) {
            if (!empty($data[$dateField])) {
                $data[$dateField] = date('Y-m-d', strtotime($data[$dateField]));
            } else {
                $data[$dateField] = NULL;
            }

            return $this->kelasModel->insertKelas($data);
        }
    }

    

    public function updateKelas($kelasId, $data, $imageFile)
    {
        $existingKelas = $this->kelasModel->getKelasById($kelasId);

        // Determine image path
        if (!empty($imageFile['name'])) {
            // If a new image is uploaded
            $imageFileName = uniqid() . '_' . basename($imageFile['name']);
            $imageFilePath = '../../../../public/image-class/' . $imageFileName;

            if (!file_exists('../../../../public/image-class')) {
                mkdir('../../../../public/image-class', 0777, true);
            }

            if (!move_uploaded_file($imageFile['tmp_name'], $imageFilePath)) {
                return "Failed to upload image.";
            }

            $data['image'] = $imageFilePath;
        } else {
            // Keep existing image if no new image is uploaded
            $data['image'] = $existingKelas['image'];
        }

        return $this->kelasModel->updateKelas($kelasId, $data);
    }

    public function deleteKelas($kelasId)
    {
        // First, get the existing kelas to retrieve its image path
        $existingKelas = $this->kelasModel->getKelasById($kelasId);

        // Delete the record from the database
        $result = $this->kelasModel->deleteKelas($kelasId);

        // If deletion from database is successful, remove the image file
        if ($result && !empty($existingKelas['image']) && file_exists($existingKelas['image'])) {
            unlink($existingKelas['image']);
        }

        return $result;
    }

    public function createKelasWithBooks($data, $imageFile, $bookIds)
    {
        // Proses upload gambar dan penanganan data kelas
        $kelasId = $this->createKelas($data, $imageFile);

        // Debugging: Cek ID kelas
        error_log("Created Kelas ID: " . $kelasId);

        // Jika proses pembuatan kelas berhasil, tambahkan hubungan buku-kelas
        if ($kelasId !== false) {
            try {
                // Pastikan $bookIds adalah array yang valid
                if (is_array($bookIds) && !empty($bookIds)) {
                    // Konversi ke integer dan filter
                    $validBookIds = array_map('intval', $bookIds);
                    $validBookIds = array_filter($validBookIds);

                    // Debug: Tampilkan book IDs
                    error_log("Book IDs to attach: " . print_r($validBookIds, true));

                    $this->kelasModel->attachBooksToKelas($kelasId, $validBookIds);
                } else {
                    error_log("No book IDs provided or invalid book IDs array");
                }

                return $kelasId;
            } catch (Exception $e) {
                // Log error
                error_log("Error attaching books to kelas: " . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    public function updateKelasWithBooks($kelasId, $data, $imageFile, $bookIds)
    {
        // Proses update kelas
        $result = $this->updateKelas($kelasId, $data, $imageFile);

        // Jika proses update kelas berhasil, tambahkan hubungan buku-kelas
        if ($result) {
            try {
                // Pastikan $bookIds adalah array yang valid
                if (is_array($bookIds) && !empty($bookIds)) {
                    // Konversi ke integer dan filter
                    $validBookIds = array_map('intval', $bookIds);
                    $validBookIds = array_filter($validBookIds);

                    // Debug: Tampilkan book IDs
                    error_log("Book IDs to attach: " . print_r($validBookIds, true));

                    $this->kelasModel->attachBooksToKelas($kelasId, $validBookIds);
                } else {
                    error_log("No book IDs provided or invalid book IDs array");
                }

                return $result;
            } catch (Exception $e) {
                // Log error
                error_log("Error attaching books to kelas: " . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    public function gettotalkelas()
    {
        $result = $this->kelasModel->getTotalKelas();
        return [
            'success' => true,
            'data' => $result['total'] ?? 0
        ];
    }
}
