<?php
include dirname(__FILE__) . '/../../../controllers/ArtikelController.php';

$artikelController = new ArtikelController();
$artikels = $artikelController->getAllArtikel();

// Urutkan data berdasarkan waktu terbaru
if (!empty($artikels)) {
    usort($artikels, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}

// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $artikelId = $_POST['id'];
    $result = $artikelController->deleteArtikel($artikelId);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete artikel']);
    }
    exit;
}

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../../views/pages/login/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $content = $_POST['content'];
    $image = $_FILES['image'];

    $result = $artikelController->createArtikel($title, $subtitle, $content, $image);

    if ($result === true) {
        // Get the latest data after insertion
        $newArtikels = $artikelController->getAllArtikel();
        usort($newArtikels, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        // Return the new data along with success message
        echo json_encode([
            'success' => true,
            'data' => $newArtikels
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => $result]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/artikel/artikel.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <style>
        .deals-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-add {
            background-color: #4f46e5;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }

        .btn-add:hover {
            background-color:rgb(8, 0, 155);
        }

        .text-ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-add {
    background-color: #4f46e5;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
}

.btn-add:hover {
    background-color: rgb(8, 0, 155);
}

        
        .action-buttons a {
    text-decoration: none !important;
}


        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .btn-delete i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <?php include '../../../views/layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="top-bar">
                <h1>Artikel</h1>
                <a href="addartikel.php" class="btn-add">+ Add Artikel</a>

            </div>
            
            <div class="deals-table">
                <div class="deals-header">
                    <h2>Artikel Management</h2>
                </div>

                <div id="tableContainer">
                    <?php if (empty($artikels)): ?>
                        <div class="empty-state">
                            <i class="fas fa-newspaper"></i>
                            <p>Tidak ada artikel yang tersedia saat ini.</p>
                        </div>
                    <?php else: ?>
                        <table id="artikelTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Subtitle</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($artikels as $artikel): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <img src="../../../assets/images/artikels/<?php echo htmlspecialchars($artikel['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($artikel['title']); ?>"
                                                 class="artikel-image">
                                        </td>
                                        <td class="text-ellipsis"><?php echo htmlspecialchars($artikel['title']); ?></td>
                                        <td class="text-ellipsis"><?php echo htmlspecialchars($artikel['subtitle']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="artikel-detail.php?id=<?php echo $artikel['id']; ?>" class="btn btn-info">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                                <a href="artikel-edit.php?id=<?php echo $artikel['id']; ?>" class="btn-edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <button type="button" class="btn-delete" onclick="deleteArtikel(<?php echo $artikel['id']; ?>)">
                                                    <i class="fas fa-trash"></i> Delete
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
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#artikelTable').DataTable();
        });

        function deleteArtikel(id) {
            if (confirm('Are you sure you want to delete this artikel?')) {
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete artikel: ' + response.error);
                        }
                    },
                    error: function() {
                        alert('An error occurred while trying to delete the artikel');
                    }
                });
            }
        }
    </script>
</body>
</html>