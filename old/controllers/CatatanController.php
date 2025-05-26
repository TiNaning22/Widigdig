<?php
// File: controllers/CatatanController.php

require_once dirname(__FILE__) . '/../models/CatatanModel.php';

class CatatanController {
    private $catatanModel;

    public function __construct() {
        $this->catatanModel = new CatatanModel();
    }

    public function getAllCatatan() {
        return $this->catatanModel->getAllCatatan();
    }

    public function getCatatanById($catatanId) {
        return $this->catatanModel->getCatatanById($catatanId);
    }

    public function createCatatan($mentorId ,$title, $content, $createdAt, $updatedAt) {
        return $this->catatanModel->insertCatatan($mentorId ,$title, $content, $createdAt, $updatedAt);
    }

    public function updateCatatan($catatanId, $title, $content, $updatedAt) {
        return $this->catatanModel->updateCatatan($catatanId, $title, $content, $updatedAt);
    }

    public function deleteCatatan($catatanId) {
        return $this->catatanModel->deleteCatatan($catatanId);
    }
}
?>