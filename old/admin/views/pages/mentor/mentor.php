<?php
include_once dirname(__FILE__) . '/../../../controllers/MentorController.php';
include_once dirname(__FILE__) . '/../../../models/MentorModel.php';
include_once dirname(__FILE__) . '/../../../services/database.php';

$mentorController = new MentorController();
$message = '';

// Create Mentor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_mentor'])) {
    $message = $mentorController->register(
        $_POST['email'],
        $_POST['name'],
        $_POST['password'],
        $_POST['password_confirm'],
        $_POST['phone_number']
    );
}

// Update Mentor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_mentor'])) {
    // Handle profile picture upload
    $profilePictureResult = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        $profilePictureResult = $mentorController->updateProfilePicture($_POST['mentor_id'], $_FILES['profile_picture']);
        if (is_string($profilePictureResult) && strpos($profilePictureResult, 'Error') !== false) {
            $message = $profilePictureResult;
        }
    }

    // Only proceed with other updates if there was no error with the profile picture
    if (empty($message)) {
        $message = $mentorController->update(
            $_POST['mentor_id'],
            $_POST['email'],
            $_POST['name'],
            $_POST['phone_number'],
            $_POST['salary_recived'],
            $_POST['salary_remaining']
        );
    }
}

// Fetch mentors
$mentors = $mentorController->getAllMentors();

// Fetch specific mentor for edit
$editMentor = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editMentor = $mentorController->getMentorById($_GET['edit']);
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
    <title>Mentor Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/mentor/mainmentor.css">
    <link rel="stylesheet" href="../../../assets/css/mentor/mentormodal.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
</head>

<body>
    <?php include '../../../views/layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="top-bar">
                <h1>Manajemen Mentor</h1>
                <button class="add-mentor-btn">
                    <i class="fas fa-plus"></i> Buat Akun Mentor
                </button>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="mentors-table">
                <h1>Daftar Mentor</h1>
                <table id="mentorsTable" class="display">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mentor</th>
                            <th>Email</th>
                            <th>Nomor Handphone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($mentors as $mentor): ?>
                            <tr>

                                <td><?php echo $no++; ?></td>

                                <td>
                                    <div class="mentor-info">
                                        <div class="mentor-image">
                                            <?php if (!empty($mentor['profile_picture'])): ?>
                                                <img src="<?php echo htmlspecialchars($mentor['profile_picture']); ?>" alt="Profile Picture">
                                            <?php else: ?>
                                                <img src="../../../assets/images/default-profile.png" alt="Default Profile">
                                            <?php endif; ?>
                                        </div>
                                        <span><?php echo htmlspecialchars($mentor['name']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($mentor['email']); ?></td>
                                <td><?php echo htmlspecialchars($mentor['phone_number']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="mentor-detail.php?id=<?php echo $mentor['id']; ?>" class="btn btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <a href="?edit=<?php echo $mentor['id']; ?>" class="btn btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <!-- Add this delete button -->
                                        <button class="btn btn-delete" onclick="confirmDelete(<?php echo $mentor['id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addMentorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2><?php echo $editMentor ? 'Edit Mentor' : 'Tambah Mentor Baru'; ?></h2>

            <form action="" method="POST" enctype="multipart/form-data">
                <?php if ($editMentor): ?>
                    <input type="hidden" name="mentor_id" value="<?php echo $editMentor['id']; ?>">
                    <input type="hidden" name="update_mentor" value="1">
                <?php else: ?>
                    <input type="hidden" name="create_mentor" value="1">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name"
                            value="<?php echo $editMentor ? htmlspecialchars($editMentor['name']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Nomor Handphone</label>
                        <input type="tel" id="phone_number" name="phone_number"
                            value="<?php echo $editMentor ? htmlspecialchars($editMentor['phone_number']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                            value="<?php echo $editMentor ? htmlspecialchars($editMentor['email']) : ''; ?>" required>
                    </div>

                    <?php if (!$editMentor): ?>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!$editMentor): ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password_confirm">Konfirmasi Password</label>
                            <input type="password" id="password_confirm" name="password_confirm" required>
                        </div>
                        <div class="form-group">
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($editMentor): ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="profile_picture">Foto Profil</label>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif">
                            <?php if (!empty($editMentor['profile_picture'])): ?>
                                <div class="current-profile-picture">
                                    <img src="<?php echo htmlspecialchars($editMentor['profile_picture']); ?>"
                                        alt="Current Profile Picture" style="max-width: 100px;">
                                    <p>Foto Profil Saat Ini</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="salary_recived">Gaji Diterima</label>
                            <input type="number" id="salary_recived" name="salary_recived"
                                value="<?php echo $editMentor['salary_recived'] ?? 0; ?>">
                        </div>
                        <div class="form-group">
                            <label for="salary_remaining">Gaji Tersisa</label>
                            <input type="number" id="salary_remaining" name="salary_remaining"
                                value="<?php echo $editMentor['salary_remaining'] ?? 0; ?>">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editMentor ? 'Update Mentor' : 'Tambah Mentor'; ?>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#mentorsTable').DataTable();

            <?php if ($editMentor): ?>
                showModal();
            <?php endif; ?>
        });

        const modal = document.getElementById('addMentorModal');
        const addMentorButton = document.querySelector('.add-mentor-btn');

        function showModal() {
            modal.classList.add('active');
        }

        function closeModal() {
            modal.classList.remove('active');
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.pathname);
            }
        }

        addMentorButton.addEventListener('click', showModal);

        window.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        };

        document.querySelector('.close').addEventListener('click', closeModal);

        // Preview uploaded image
        document.getElementById('profile_picture')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const currentPicture = document.querySelector('.current-profile-picture');
                    if (currentPicture) {
                        currentPicture.querySelector('img').src = e.target.result;
                    } else {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'current-profile-picture';
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Profile Picture Preview" style="max-width: 100px;">
                            <p>Preview Foto Profil Baru</p>
                        `;
                        document.getElementById('profile_picture').parentNode.appendChild(previewDiv);
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        function confirmDelete(mentorId) {
            if (confirm('Apakah Anda yakin ingin menghapus mentor ini?')) {
                deleteMentor(mentorId);
            }
        }

        function deleteMentor(mentorId) {
            $.ajax({
                url: 'delete_mentor.php',
                type: 'POST',
                data: {
                    mentor_id: mentorId
                },
                dataType: 'json', // Add this line to specify we're expecting JSON
                success: function(response) {
                    // Remove JSON.parse since jQuery will handle it
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus mentor');
                }
            });
        }
    </script>
</body>

</html>