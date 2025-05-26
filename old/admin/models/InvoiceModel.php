<?php
// File: models/InvoiceModel.php
require_once dirname(__FILE__) . '/../services/database.php';

class InvoiceModel
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function getAllInvoices()
    {
        $query = "SELECT i.*, u.name as user_name, k.name as class_name 
            FROM invoices i 
            LEFT JOIN users u ON i.user_id = u.id
            LEFT JOIN kelas k ON i.kelas_id = k.id";
        $result = mysqli_query($this->conn, $query);
        $invoicesList = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $invoicesList[] = $row;
        }
        return $invoicesList;
    }

    public function getMonthlyOrders()
    {
        $query = "SELECT 
            MONTH(created_at) as month,
            COUNT(*) as total_orders
            FROM invoices
            WHERE YEAR(created_at) = YEAR(CURRENT_DATE())
            GROUP BY MONTH(created_at)
            ORDER BY month";

        $result = mysqli_query($this->conn, $query);

        // Initialize array with zeros for all months
        $monthlyOrders = array_fill(1, 12, 0);

        // Fill in actual values
        while ($row = mysqli_fetch_assoc($result)) {
            $monthlyOrders[$row['month']] = (int)$row['total_orders'];
        }

        return array_values($monthlyOrders);
    }

    public function getInvoiceById($invoiceId)
    {
        $query = "SELECT * FROM invoices WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $invoiceId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function insertInvoice($data)
    {
        // Cek apakah koneksi berhasil
        if (!$this->conn) {
            error_log("Connection failed: " . mysqli_connect_error());
            return false;
        }

        // Tentukan waktu default untuk created_at dan updated_at
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        // Tentukan nilai default untuk created_at dan updated_at jika tidak ada dalam data
        $data['created_at'] = $data['created_at'] ?? $created_at;
        $data['updated_at'] = $data['updated_at'] ?? $updated_at;

        // Mulai transaksi
        mysqli_begin_transaction($this->conn);

        try {
            // Query untuk memasukkan data ke tabel invoices
            $query = "INSERT INTO invoices (user_id, kelas_id, name, payment_price, nominal, no_rekening, image_pay, bank_name, transfer_date, approval, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($this->conn, $query);
            if (!$stmt) {
                throw new Exception("Prepare statement failed for invoices: " . mysqli_error($this->conn));
            }

            // Binding parameter untuk query invoices
            mysqli_stmt_bind_param(
                $stmt,
                "iissdsssssss",
                $data['user_id'],
                $data['kelas_id'],
                $data['name'],
                $data['payment_price'],
                $data['nominal'],
                $data['no_rekening'],
                $data['image_pay'],
                $data['bank_name'],
                $data['transfer_date'],
                $data['approval'],
                $data['created_at'],
                $data['updated_at']
            );

            // Eksekusi query untuk invoices
            $executeResult = mysqli_stmt_execute($stmt);
            if (!$executeResult) {
                throw new Exception("Execute statement failed for invoices: " . mysqli_stmt_error($stmt));
            }

            // Query untuk memasukkan data ke tabel kelas_users
            $kelasQuery = "INSERT INTO kelas_users (user_id, kelas_id, created_at, updated_at) VALUES (?, ?, ?, ?)";
            $stmt2 = mysqli_prepare($this->conn, $kelasQuery);
            if (!$stmt2) {
                throw new Exception("Prepare statement failed for kelas_users: " . mysqli_error($this->conn));
            }

            // Binding parameter untuk query kelas_users
            mysqli_stmt_bind_param(
                $stmt2,
                "iiss",
                $data['user_id'],
                $data['kelas_id'],
                $data['created_at'],
                $data['updated_at']
            );

            // Eksekusi query untuk kelas_users
            $executeResult2 = mysqli_stmt_execute($stmt2);
            if (!$executeResult2) {
                throw new Exception("Execute statement failed for kelas_users: " . mysqli_stmt_error($stmt2));
            }

            // Jika kedua query berhasil, commit transaksi
            mysqli_commit($this->conn);
            return true; // Data berhasil dimasukkan

        } catch (Exception $e) {
            // Jika terjadi kesalahan, rollback transaksi
            mysqli_rollback($this->conn);
            error_log("Error: " . $e->getMessage());
            return false; // Gagal memasukkan data
        } finally {
            // Menutup statement setelah selesai
            if (isset($stmt)) {
                mysqli_stmt_close($stmt);
            }
            if (isset($stmt2)) {
                mysqli_stmt_close($stmt2);
            }
        }
    }

    public function updateInvoice($invoiceId, $data)
    {
        // Query untuk memperbarui data di tabel invoices
        $query = "UPDATE invoices SET status = ?, approval = ?, updated_at = ? WHERE id = ?";

        // Siapkan pernyataan SQL untuk eksekusi
        $stmt = mysqli_prepare($this->conn, $query);

        // Dapatkan waktu saat ini untuk updated_at
        $updated_at = date('Y-m-d H:i:s');

        // Bind parameter sesuai dengan tipe data yang sesuai
        mysqli_stmt_bind_param(
            $stmt,
            "sssi", // format tipe data
            $data['status'],
            $data['approval'],
            $updated_at,
            $invoiceId  // Parameter terakhir untuk id invoice yang akan diupdate
        );

        // Eksekusi pernyataan SQL
        $executeResult = mysqli_stmt_execute($stmt);

        // Cek apakah eksekusi berhasil
        if ($executeResult) {
            return true; // Data berhasil diperbarui
        } else {
            return false; // Gagal memperbarui data
        }
    }

    public function deleteInvoice($invoiceId)
    {
        $query = "DELETE FROM invoices WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $invoiceId);
        return mysqli_stmt_execute($stmt);
    }

    public function getdetailinvoicesbyid($invoiceId)
    {
        $query = "SELECT i.status, i.name, i.payment_price, i.nominal, i.no_rekening, 
                  i.image_pay, i.bank_name, u.name as user_name, k.name as class_name
                  FROM invoices i 
                  LEFT JOIN users u ON i.user_id = u.id 
                  LEFT JOIN kelas k ON i.kelas_id = k.id
                  WHERE i.id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $invoiceId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }
}
