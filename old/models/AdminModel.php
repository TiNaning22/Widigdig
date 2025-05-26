<?php
// File : models/AdminModel.php
include dirname(__DIR__) . '/services/services.php';


class AdminModel
{

    public function getAdminByEmail($email)
    {
        global $conn;
        $query = "SELECT * FROM admins WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function createAdmin($email, $username, $password)
    {
        global $conn;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $created_at = date("Y-m-d H:i:s");
        $updated_at = $created_at;

        $query = "INSERT INTO admins (email, username, password, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param($stmt, "sssss", $email, $username, $hashedPassword, $created_at, $updated_at);

        return mysqli_stmt_execute($stmt);
    }

    public function loginAdmin($email, $password)
    {
        $result = $this->getAdminByEmail($email);
        if (mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            if (password_verify($password, $admin['password'])) {
                return $admin;
            }
        }
        return false;
    }

    public function registerAdmin($email, $username, $password, $password_confirm,)
    {
        if (empty($email) || empty($username) || empty($password)) {
            return "Semua field harus diisi!";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Format email tidak valid!";
        }

        if (strlen($password) < 6) {
            return "Password minimal 6 karakter!";
        }

        if ($password === $password_confirm) {
            $result = $this->getAdminByEmail($email);
            if (mysqli_num_rows($result) > 0) {
                return "Email sudah terdaftar!";
            } else {
                if ($this->createAdmin($email, $username, $password)) {
                    return "Pendaftaran berhasil, silakan login!";
                } else {
                    return "Terjadi kesalahan saat pendaftaran!";
                }
            }
        } else {
            return "Password tidak cocok!";
        }
    }
}
