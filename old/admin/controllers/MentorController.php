<?php
//file: controllers/MentorController.php
include_once dirname(__FILE__) . '/../services/database.php';
include_once dirname(__FILE__) . '/../models/MentorModel.php';

class MentorController
{
    protected $mentorModel;

    public function __construct()
    {
        $this->mentorModel = new MentorModel();
    }

    public function getAllMentors()
    {
        return $this->mentorModel->getAllMentors();
    }

    public function getMentorById($mentorId)
    {
        return $this->mentorModel->getMentorById($mentorId);
    }

    public function login($email, $password)
    {
        $mentor = $this->mentorModel->loginMentor($email, $password);
        if ($mentor) {
            session_start();
            $_SESSION['mentor_id'] = $mentor['id'];
            $_SESSION['mentor_email'] = $mentor['email'];
            $_SESSION['mentor_name'] = $mentor['name'];
            header('Location: ../views/pages/dashboard/dashboard.php');
            exit();
        } else {
            return "Email atau Password salah!";
        }
    }

    public function register($email, $name, $password, $password_confirm, $phone_number)
    {
        return $this->mentorModel->registerMentor($email, $name, $password, $password_confirm, $phone_number);
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: ../auth/login/login.php');
        exit();
    }

    public function update($mentorId, $email, $name, $phone_number, $salary_recived, $salary_remaining)
    {
        $data = [
            'email' => $email,
            'name' => $name,
            'phone_number' => $phone_number,
            'salary_recived' => $salary_recived,
            'salary_remaining' => $salary_remaining,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->mentorModel->updateMentor($mentorId, $data)) {
            return "Mentor berhasil diupdate!";
        } else {
            return "Error: Gagal mengupdate mentor";
        }
    }

    public function updateProfilePicture($mentorId, $profilePicture)
    {
        $uploadDir = '../../../../public/profile-picture/';
        $created_at = date('Y-m-d H:i:s');
        $updated_at = $created_at;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!isset($profilePicture) || $profilePicture['error'] !== UPLOAD_ERR_OK) {
            return "Error: Upload gagal";
        }

        $fileExtension = strtolower(pathinfo($profilePicture['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $newFileName;

        $allowedTypes = ['jpeg', 'jpg', 'png', 'gif'];
        if (!in_array($fileExtension, $allowedTypes)) {
            return "Error: Tipe file tidak diizinkan. Gunakan JPEG, PNG, atau GIF";
        }

        $maxFileSize = 5 * 1024 * 1024;
        if ($profilePicture['size'] > $maxFileSize) {
            return "Error: Ukuran file terlalu besar. Maksimal 5MB";
        }

        if (move_uploaded_file($profilePicture['tmp_name'], $uploadPath)) {
            $relativePath = '../../../../public/profile-picture/' . $newFileName;

            if ($this->mentorModel->updateProfilePicture($mentorId, $relativePath, $updated_at)) {
                return true;
            } else {
                unlink($uploadPath);
                return "Error: Gagal menyimpan di database";
            }
        }
        return "Error: Gagal memindahkan file";
    }

    public function getTotalMentor()
    {
        $result = $this->mentorModel->getTotalMentor();
        return [
            'success' => true,
            'data' => $result['total'] ?? 0
        ];
    }
    
    public function delete($mentorId)
    {
        if ($this->mentorModel->deleteMentor($mentorId)) {
            return [
                'success' => true,
                'message' => 'Mentor berhasil dihapus!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal menghapus mentor'
            ];
        }
    }
}
