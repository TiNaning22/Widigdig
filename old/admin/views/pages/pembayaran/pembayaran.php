<?php
include dirname(__FILE__) . '/../../../controllers/InvoiceController.php';

$invoicesController = new InvoicesController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_invoice_status') {
    $invoiceId = $_POST['invoice_id'] ?? null;
    $newStatus = $_POST['status'] ?? null;

    if ($invoiceId && $newStatus) {
        $data = [
            'status' => $newStatus,
        ];

        $response = json_decode($invoicesController->updateInvoice($invoiceId, $data), true);

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }
}

$response = json_decode($invoicesController->getAllInvoices(), true);
$payments = [];

if ($response['success'] && isset($response['data'])) {
    $payments = $response['data'];

    usort($payments, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
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
    <title>Invoice User</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/pembayaran/pembayaran.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <style>
        .empty-state {
            text-align: center;
            padding: 2rem;
            background-color: #f9fafb;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }

        .empty-state i {
            font-size: 3rem;
            color: #9ca3af;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #4b5563;
            font-size: 1rem;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-content {
            background: #fff;
            margin: 5% auto;
            width: 90%;
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: relative;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f8f9fa;
            border-radius: 12px 12px 0 0;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.4rem;
            color: #1f2937;
            font-weight: 600;
        }

        .close-btn {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 24px;
            color: #6b7280;
            cursor: pointer;
            border: none;
            background: none;
            transition: color 0.2s;
        }

        .close-btn:hover {
            color: #1f2937;
        }

        .modal-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.95rem;
            color: #1f2937;
            background-color: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group select option {
            padding: 10px;
        }

        .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-radius: 0 0 12px 12px;
            background-color: #f8f9fa;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            border: none;
            transition: all 0.2s;
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #4b5563;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .btn-update {
            background: #3b82f6;
            color: white;
        }

        .btn-update:hover {
            background: #2563eb;
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Status badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            text-align: center;
            display: inline-block;
        }

        .status-waiting {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Loading spinner */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Toast notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-size: 0.95rem;
            z-index: 1100;
            display: none;
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        .toast-success {
            background-color: #059669;
        }

        .toast-error {
            background-color: #dc2626;
        }
    </style>
</head>

<body>
    <?php include '../../../views/layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="top-bar">
                <h1>Faktur Pembayaran</h1>
            </div>

            <div class="deals-table">
                <div class="deals-header">
                    <h2>Status Pembayaran</h2>
                </div>

                <?php if (empty($payments)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Tidak ada data pembayaran yang tersedia saat ini.</p>
                    </div>
                <?php else: ?>
                    <table id="invoiceTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama User</th>
                                <th>Nama Kelas</th>
                                <th>Nominal</th>
                                <th>Status Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($payment['user_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($payment['class_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($payment['nominal'] ?? ''); ?></td>
                                    <td>
                                        <span class="status-badge <?php
                                                                    echo match ($payment['status']) {
                                                                        'menunggu konfirmasi' => 'status-waiting',
                                                                        'terbayar' => 'status-paid',
                                                                        'gagal' => 'status-failed',
                                                                        default => ''
                                                                    };
                                                                    ?>">
                                            <?php echo htmlspecialchars($payment['status'] ?? ''); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="invoice-detail.php?id=<?php echo $payment['id']; ?>" class="btn btn-info">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <button class="btn btn-edit" onclick="openModal(
                                        '<?php echo $payment['id']; ?>',
                                        '<?php echo $payment['status'] ?? ''; ?>'
                                    )">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="updateInvoiceStatusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Update Invoice Status</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="updateInvoiceStatusForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_invoice_status">
                    <input type="hidden" id="invoiceId" name="invoice_id">

                    <div class="form-group">
                        <label for="paymentStatus">
                            <i class="fas fa-money-check-alt"></i> Payment Status
                        </label>
                        <select id="paymentStatus" name="status" required>
                            <option value="menunggu konfirmasi">Menunggu Pembayaran</option>
                            <option value="terbayar">Terbayar</option>
                            <option value="gagal">Gagal</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-update">
                        <span class="spinner"></span>
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#invoiceTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true
            });
        });

        const modal = document.getElementById('updateInvoiceStatusModal');
        const invoiceIdInput = document.getElementById('invoiceId');
        const paymentStatusSelect = document.getElementById('paymentStatus');

        function openModal(invoiceId, currentPaymentStatus) {
            invoiceIdInput.value = invoiceId;
            paymentStatusSelect.value = currentPaymentStatus || 'menunggu pembayaran';
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        }

        const form = document.getElementById('updateInvoiceStatusForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Invoice status updated successfully!');
                        closeModal();
                        location.reload();
                    } else {
                        alert('Failed to update invoice status: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        });
    </script>
</body>

</html>