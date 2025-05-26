<?php
// File: models/AuthModel.php
// oke
class AuthModel
{
    // Fungsi untuk mendapatkan admin berdasarkan email
    public function getUserByEmail($email)
    {
        global $conn;
        $query = "SELECT * FROM admins WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function createUser($email, $name, $password)
    {
        global $conn;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $created_at = date("Y-m-d H:i:s");
        $updated_at = $created_at;

        $query = "INSERT INTO admins (email, name, password, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param($stmt, "sssss", $email, $name, $hashedPassword, $created_at, $updated_at);

        return mysqli_stmt_execute($stmt);
    }

    public function loginUser($email, $password)
    {
        $result = $this->getUserByEmail($email);
        if (mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            if (password_verify($password, $admin['password'])) {
                return $admin; // Jika password valid, kembalikan data admin
            }
        }
        return false; // Return false jika login gagal
    }

    // Fungsi untuk registrasi admin
    public function registerUser($email, $name, $password, $password_confirm,)
    {
        // Validasi input
        if (empty($email) || empty($name) || empty($password)) {
            return "Semua field harus diisi!";
        }

        // Validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Format email tidak valid!";
        }

        // Validasi panjang password
        if (strlen($password) < 6) {
            return "Password minimal 6 karakter!";
        }

        if ($password === $password_confirm) {
            // Cek apakah email sudah terdaftar
            $result = $this->getUserByEmail($email);
            if (mysqli_num_rows($result) > 0) {
                return "Email sudah terdaftar!";
            } else {
                // Buat pengguna baru
                if ($this->createUser($email, $name, $password)) {
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
