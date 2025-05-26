<?php
// File: controllers/InvoicesController.php
require_once dirname(__FILE__) . '/../models/InvoiceModel.php';

class InvoicesController
{
    private $invoiceModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
    }

    // Create new invoice
    public function createInvoice($data)
    {
        $insertResult = $this->invoiceModel->insertInvoice($data);
        if ($insertResult) {
            return json_encode(['success' => true, 'message' => 'Invoice created successfully']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to create invoice']);
        }
    }

    // Read all invoices
    public function getAllInvoices()
    {
        $invoices = $this->invoiceModel->getAllInvoices();
        return json_encode(['success' => true, 'data' => $invoices]);
    }

    // Read a specific invoice by ID
    public function getInvoiceById($invoiceId)
    {
        $invoice = $this->invoiceModel->getInvoiceById($invoiceId);
        if ($invoice) {
            return json_encode(['success' => true, 'data' => $invoice]);
        } else {
            return json_encode(['success' => false, 'message' => 'Invoice not found']);
        }
    }

    public function updateInvoice($invoiceId, $data)
    {
        $updateResult = $this->invoiceModel->updateInvoice($invoiceId, $data);
        if ($updateResult) {
            return json_encode(['success' => true, 'message' => 'Invoice updated successfully']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to update invoice']);
        }
    }


    public function deleteInvoice($invoiceId)
    {
        $deleteResult = $this->invoiceModel->deleteInvoice($invoiceId);
        if ($deleteResult) {
            return json_encode(['success' => true, 'message' => 'Invoice deleted successfully']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to delete invoice']);
        }
    }

    public function getkelasuser($user_id) {
        $kelasUser = $this->invoiceModel->getKelasUserDetail($user_id);
        return $kelasUser; // Langsung kembalikan hasil array data kelas
    }

    public function getmentorkelas($mentor_id) {
        $mentorKelas = $this->invoiceModel->getMentorKelasDetail($mentor_id);
        return $mentorKelas; // Langsung kembalikan hasil array data kelas
    }
}
?>