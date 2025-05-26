<?php
// File: models/AuthModel.php
// oke
class AuthModel {
    // Fungsi untuk mendapatkan user berdasarkan email
    public function getUserByEmail($email) {
        global $conn;
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function getUserByPhoneNumber($phone_number) {
        global $conn;
        $query = "SELECT * FROM users WHERE phone_number = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $phone_number);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function createUser($email, $name, $password, $phone_number) {
        global $conn;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $created_at = date("Y-m-d H:i:s");
        $updated_at = $created_at;
        
        $query = "INSERT INTO users (email, name, password, phone_number, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param($stmt, "ssssss", $email, $name, $hashedPassword, $phone_number, $created_at, $updated_at);
        
        return mysqli_stmt_execute($stmt);
    }

    public function loginUser($email, $password) {
        $result = $this->getUserByEmail($email);
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                return $user; // Jika password valid, kembalikan data user
            }
        }
        return false; // Return false jika login gagal
    }

    // Fungsi untuk registrasi user
    public function registerUser($email, $name, $password, $password_confirm, $phone_number) {
        // Validasi input
        if (empty($email) || empty($name) || empty($password) || empty($phone_number)) {
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
                if ($this->createUser($email, $name, $password, $phone_number)) {
                    return "Pendaftaran berhasil, silakan login!";
                } else {
                    return "Terjadi kesalahan saat pendaftaran!";
                }
            }
        } if ($phone_number === $phone_number) {
            // Cek apakah nomor telepon sudah terdaftar
            $result = $this->getUserByPhoneNumber($phone_number);
            if (mysqli_num_rows($result) > 0) {
                return "Nomor telepon sudah terdaftar!";
            } else {
                // Buat pengguna baru
                if ($this->createUser($email, $name, $password, $phone_number)) {
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
?>