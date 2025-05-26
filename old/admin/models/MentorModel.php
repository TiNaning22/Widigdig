<?php
// File: models/AuthModel.php
// oke
class MentorModel
{

    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function getAllMentors()
    {
        $query = "SELECT * FROM mentors";
        $result = mysqli_query($this->conn, $query);
        $mentorsList = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $mentorsList[] = $row;
        }
        return $mentorsList;
    }

    // Fungsi untuk mendapatkan mentor berdasarkan email
    public function getMentorByEmail($email)
    {
        global $conn;
        $query = "SELECT * FROM mentors WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function getMentorByPhoneNumber($phone_number)
    {
        global $conn;
        $query = "SELECT * FROM mentors WHERE phone_number = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $phone_number);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function createMentor($email, $name, $password, $phone_number = '')
    {
        global $conn;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $created_at = date("Y-m-d H:i:s");
        $updated_at = $created_at;

        $query = "INSERT INTO mentors (email, name, password, phone_number, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param($stmt, "ssssss", $email, $name, $hashedPassword, $phone_number, $created_at, $updated_at);

        return mysqli_stmt_execute($stmt);
    }

    public function loginMentor($email, $password)
    {
        $result = $this->getMentorByEmail($email);
        if (mysqli_num_rows($result) > 0) {
            $mentor = mysqli_fetch_assoc($result);
            if (password_verify($password, $mentor['password'])) {
                return $mentor; // Jika password valid, kembalikan data mentor
            }
        }
        return false; // Return false jika login gagal
    }

    // Fungsi untuk registrasi mentor
    public function registerMentor($email, $name, $password, $password_confirm, $phone_number)
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
            $result = $this->getMentorByEmail($email);
            if (mysqli_num_rows($result) > 0) {
                return "Email sudah terdaftar!";
            } else {
                // Buat pengguna baru
                if ($this->createMentor($email, $name, $password, $phone_number)) {
                    return "Pendaftaran berhasil, silakan login!";
                } else {
                    return "Terjadi kesalahan saat pendaftaran!";
                }
            }
        } else {
            return "Password tidak cocok!";
        }
    }

    public function getMentorById($mentorId)
    {
        $query = "SELECT * FROM mentors WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $mentorId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function updateProfilePicture($mentorId, $profilePicturePath, $updated_at)
    {
        $query = "UPDATE mentors SET profile_picture = ?, updated_at = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $profilePicturePath, $updated_at, $mentorId);
        return mysqli_stmt_execute($stmt);
    }

    public function updateMentor($mentorId, $data)
    {
        $query = "UPDATE mentors 
                  SET email = ?, name = ?, phone_number = ?, salary_recived = ?, salary_remaining = ?, updated_at = ?
                  WHERE id = ?";

        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssi",
            $data['email'],
            $data['name'],
            $data['phone_number'],
            $data['salary_recived'],
            $data['salary_remaining'],
            $data['updated_at'],
            $mentorId
        );

        return mysqli_stmt_execute($stmt);
    }

    public function getTotalMentor()
    {
        $query = "SELECT COUNT(*) as total FROM mentors";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }
    
    
    public function deleteMentor($mentorId) 
    {
        // Validate if mentor exists first
        $mentor = $this->getMentorById($mentorId);
        if (!$mentor) {
            return false;
        }

        $query = "DELETE FROM mentors WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $mentorId);
        
        return mysqli_stmt_execute($stmt);
    }
}
