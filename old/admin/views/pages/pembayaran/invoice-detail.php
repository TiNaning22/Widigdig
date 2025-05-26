<?php
// File: views/pages/pembayaran/invoice-detail.php

// Include necessary files
include_once dirname(__FILE__) . '/../../../controllers/InvoiceController.php';
include_once dirname(__FILE__) . '/../../../models/InvoiceModel.php';
include_once dirname(__FILE__) . '/../../../services/database.php';

// Initialize the controller
$invoiceController = new InvoicesController();

// Get the invoice ID from the URL
$invoiceId = isset($_GET['id']) ? $_GET['id'] : null;
$invoice = null;

if ($invoiceId) {
    // Get the invoice details by ID
    $response = $invoiceController->getdetailinvoicesbyid($invoiceId);

    // Decode the JSON response into an associative array
    $invoiceData = json_decode($response, true);

    // Check if the response contains data
    if (isset($invoiceData['success']) && $invoiceData['success'] === true) {
        $invoice = $invoiceData['data'];

        // Handle the image path only after the invoice is successfully retrieved
        $fileName = $invoice['image_pay'];

        if (strpos($fileName, 'transfer_proof_') === false) {
            $fileName = 'transfer_proof_' . $fileName;
        }
        $imagePath = "http://kelassore.bpkbautodigital.com/public/uploads/transfer_proofs/" . $fileName;
    } else {
        // Handle error if the response doesn't have 'success' or 'data'
        echo "Error: Invoice data not found.";
        exit;
    }
} else {
    echo "Invalid invoice ID.";
    exit;
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
    <title>Detail Invoice</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/mentor/mentor.css">
    <style>
        .invoice-detail-container {
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px;
        }

        .invoice-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .invoice-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 1.1em;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .image-container {
            max-width: 100%;
            overflow: hidden;
        }

        .payment-image {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include '../../../views/layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="invoice-detail-container">
                <div class="invoice-header">
                    <h1>Detail Invoice</h1>
                </div>

                <div class="invoice-info-grid">
                    <!-- User Name -->
                    <div class="info-item">
                        <div class="info-label">Nama User</div>
                        <div class="info-value"><?php echo htmlspecialchars($invoice['user_name'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <!-- Class Name -->
                    <div class="info-item">
                        <div class="info-label">Nama Kelas</div>
                        <div class="info-value"><?php echo htmlspecialchars($invoice['class_name'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <!-- Status -->
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value"><?php echo htmlspecialchars($invoice['status'] ?? 'N/A'); ?></div>
                    </div>

                    <!-- Payment Price -->
                    <div class="info-item">
                        <div class="info-label">Payment Price</div>
                        <div class="info-value">Rp <?php echo number_format($invoice['payment_price'] ?? 0, 0, ',', '.'); ?></div>
                    </div>

                    <!-- Nominal -->
                    <div class="info-item">
                        <div class="info-label">Nominal</div>
                        <div class="info-value">Rp <?php echo number_format($invoice['nominal'] ?? 0, 0, ',', '.'); ?></div>
                    </div>

                    <!-- No Rekening -->
                    <div class="info-item">
                        <div class="info-label">No Rekening</div>
                        <div class="info-value"><?php echo htmlspecialchars($invoice['no_rekening'] ?? 'N/A'); ?></div>
                    </div>

                    <!-- Bank Name -->
                    <div class="info-item">
                        <div class="info-label">Bank Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($invoice['bank_name'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <!-- Payment Image -->
                    <div class="info-item">
                        <div class="info-label">Image Pay</div>
                        <div class="info-value image-container">
                            <?php if (!empty($invoice['image_pay'])): ?>
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                     alt="Payment Image" 
                                     class="payment-image">
                            <?php else: ?>
                                No image available.
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <a href="pembayaran.php" class="back-button">Back</a>
            </div>
        </div>
    </div>
</body>
</html>