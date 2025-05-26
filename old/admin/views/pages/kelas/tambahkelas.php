<?php
require_once dirname(__FILE__) . '/../../../controllers/KelasController.php';
require_once dirname(__FILE__) . '/../../../controllers/BookController.php';

// Initialize controllers
$kelasController = new KelasController();
$bukuController = new BookController();

// Get all books
$allBooks = $bukuController->getAllBooks();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'mentor_id' => $_POST['mentor_id'] ?? '',
        'name_mentor' => $_POST['name_mentor'] ?? '',
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'category' => $_POST['category'],
        'price' => $_POST['price'],
        'quota' => $_POST['quota'],
        'quota_left' => $_POST['quota_left'],
        'schedule' => $_POST['schedule'],
        'end_date' => $_POST['end_date'],
        'link_wa' => $_POST['link_wa'],
        'link_youtube' => $_POST['link_youtube'],
        'status' => $_POST['status'],
        'sesion_1' => $_POST['sesion_1'],
        'sesion_2' => $_POST['sesion_2'],
        'sesion_3' => $_POST['sesion_3'],
        'what_will_learn_1' => $_POST['what_will_learn_1'],
        'what_will_learn_2' => $_POST['what_will_learn_2'],
        'what_will_learn_3' => $_POST['what_will_learn_3'],
    ];

    $bookIds = isset($_POST['book_ids']) ? $_POST['book_ids'] : [];
    $imageFile = $_FILES['image'];

    $result = $kelasController->createKelasWithBooks($data, $imageFile, $bookIds);

    if ($result === true) {
        header('Location: kelas.php');
        exit();
    } else {
        header('Location: kelas.php');
        exit();
    }
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
    <title>Tambah Kelas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/kelas/tambahkelas.css">
    <style>
        .form-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            background-color: #fff;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        fieldset {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
        }

        fieldset legend {
            font-size: 1.1rem;
            font-weight: bold;
            color: #555;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 16px;
            margin-top: 30px;
            padding: 20px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
            min-width: 140px;
        }

        .button-primary {
            background: linear-gradient(to right, #2563eb, #1d4ed8);
            color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        .button-primary:hover {
            background: linear-gradient(to right, #1d4ed8, #1e40af);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
        }

        .button-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px -1px rgba(37, 99, 235, 0.2);
        }

        .button-secondary {
            background: #ffffff;
            color: #4b5563;
            border: 2px solid #e5e7eb;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .button-secondary:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
        }

        .button-secondary:active {
            transform: translateY(0);
            background: #f3f4f6;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .what-will-learn-container {
            display: grid;
            gap: 15px;
        }
    </style>
</head>
<body>
    <?php include '../../../views/layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="form-container">
                <h1>Tambah Kelas</h1>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Nama Kelas:</label>
                            <input type="text" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Kategori:</label>
                            <select name="category" required>
                                <option value="Private">Private</option>
                                <option value="Reguler">Reguler</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi Kelas:</label>
                            <textarea name="description" rows="2" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="mentor_id">Pilih Mentor:</label>
                            <select name="mentor_id" required>
                                <option value="">Pilih Mentor</option>
                                <?php 
                                $allMentors = $kelasController->getAllMentors();
                                foreach ($allMentors as $mentor): ?>
                                    <option value="<?= $mentor['id']; ?>"><?= $mentor['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="name_mentor">Nama Mentor:</label>
                            <input type="text" name="name_mentor" id="name_mentor" readonly>
                        </div>

                        <div class="form-group">
                            <label for="price">Harga:</label>
                            <input type="number" name="price" required>
                        </div>

                        <div class="form-group">
                            <label for="quota">Kuota:</label>
                            <input type="number" name="quota" required>
                        </div>

                        <div class="form-group">
                            <label for="quota_left">Kuota Tersisa:</label>
                            <input type="number" name="quota_left" required>
                        </div>

                        <div class="form-group">
                            <label for="schedule">Jadwal:</label>
                            <input type="text" name="schedule" required>
                        </div>

                        <div class="form-group">
                            <label for="end_date">Tanggal Selesai:</label>
                            <input type="date" name="end_date" required>
                        </div>

                        <div class="form-group">
                            <label for="link_wa">Link WhatsApp:</label>
                            <input type="url" name="link_wa">
                        </div>

                        <div class="form-group">
                            <label for="link_youtube">Link YouTube:</label>
                            <input type="url" name="link_youtube">
                        </div>

                        <div class="form-group">
                            <label for="sesion_1">Sesion 1:</label>
                            <input type="url" name="sesion_1" required>
                        </div>

                        <div class="form-group">
                            <label for="sesion_2">Sesion 2:</label>
                            <input type="url" name="sesion_2" required>
                        </div>

                        <div class="form-group">
                            <label for="sesion_3">Sesion 3:</label>
                            <input type="url" name="sesion_3" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select name="status" required>
                                <option value="buka">Buka</option>
                                <option value="tutup">Tutup</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="image">Gambar Kelas:</label>
                            <input type="file" name="image">
                        </div>

                        <div class="form-group">
                            <label for="book_ids[]">Buku yang Dibaca:</label>
                            <select name="book_ids[]" multiple>
                                <?php foreach ($allBooks as $book): ?>
                                    <option value="<?= $book['id']; ?>"><?= $book['title']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <fieldset>
                        <legend>Kurikulum:</legend>
                        <div class="what-will-learn-container">
                            <div class="form-group">
                                <label for="what_will_learn_1">Sesi 1:</label>
                                <textarea name="what_will_learn_1" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="what_will_learn_2">Sesi 2:</label>
                                <textarea name="what_will_learn_2" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="what_will_learn_3">Sesi 3:</label>
                                <textarea name="what_will_learn_3" required></textarea>
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-actions">
                        <button type="submit" class="button button-primary">Tambah Kelas</button>
                        <a href="kelas.php" class="button button-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const mentors = <?php echo json_encode($allMentors); ?>;
        
        document.querySelector('[name="mentor_id"]').addEventListener('change', function() {
            const selectedMentorId = this.value;
            const mentorNameField = document.getElementById('name_mentor');
            const selectedMentor = mentors.find(mentor => mentor.id == selectedMentorId);
            mentorNameField.value = selectedMentor ? selectedMentor.name : '';
        });
    </script>
</body>
</html>