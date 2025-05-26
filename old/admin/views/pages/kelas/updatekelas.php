<?php
require_once dirname(__FILE__) . '/../../../controllers/KelasController.php';
require_once dirname(__FILE__) . '/../../../controllers/MentorController.php';
require_once dirname(__FILE__) . '/../../../controllers/BookController.php';

// Initialize controllers
$kelasController = new KelasController();
$mentorController = new MentorController();
$bookController = new BookController();

// Get all mentors and books for dropdowns
$allMentors = $mentorController->getAllMentors();
$allBooks = $bookController->getAllBooks();
?>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    z-index: 1000;
}

.modal-content {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    width: 90%;
    max-width: 900px;
    height: 85vh;
    max-height: 800px;
    margin: 20px auto;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #ddd;
}

.modal-body {
    padding: 20px;
    overflow-y: auto;
    flex-grow: 1;
    margin: 0 10px;
}

.form-container {
    max-width: 100%;
    margin: 0 auto;
    padding: 10px;
}

.form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
}

.form-group label {
    font-weight: bold;
    font-size: 14px;
    margin-bottom: 8px;
}

.form-group input, .form-group select, .form-group textarea {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    height: 40px;
}

.form-group textarea {
    height: 80px;
    resize: vertical;
}

.form-actions {
    grid-column: span 2;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
    padding: 20px;
    background-color: #fff;
    border-top: 1px solid #ddd;
}

.btn-primary, .btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 8px 16px;
    min-width: 100px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
    border: none;
    transition: background-color 0.2s ease;
}

.btn-primary {
    background-color: #4CAF50;
    color: white;
}

.btn-secondary {
    background-color: #f44336;
    color: white;
}

fieldset {
    grid-column: span 2;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-top: 20px;
}

fieldset textarea {
    margin-bottom: 10px;
    width: 100%;
}

legend {
    font-weight: bold;
    font-size: 16px;
    padding: 0 10px;
}

.close {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 28px;
    cursor: pointer;
    color: #666;
}

#updateBookIds {
    height: 120px !important;
}

.modal-content h1 {
    font-size: 24px;
    margin: 0;
    padding: 20px;
    border-bottom: 1px solid #ddd;
}
</style>

<div id="updateKelasModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <div class="modal-header">
            <h1>Edit Kelas</h1>
        </div>
        <div class="modal-body">
            <form id="updateKelasForm" action="?action=update" method="post" enctype="multipart/form-data" class="form-container">
            <input type="hidden" name="id" id="updateKelasId">

            <div class="form-group">
                <label for="updateName">Nama Kelas:</label>
                <input type="text" name="name" id="updateName" required>
            </div>

            <div class="form-group">
                <label for="updateDescription">Deskripsi Kelas:</label>
                <textarea name="description" id="updateDescription" required></textarea>
            </div>

            <div class="form-group">
                <label for="updateMentorId">Pilih Mentor:</label>
                <select name="mentor_id" id="updateMentorId" required>
                    <option value="">Pilih Mentor</option>
                    <?php foreach ($allMentors as $mentor): ?>
                        <option value="<?= htmlspecialchars($mentor['id']); ?>">
                            <?= htmlspecialchars($mentor['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="updateNameMentor">Nama Mentor:</label>
                <input type="text" name="name_mentor" id="updateNameMentor" readonly>
            </div>

            <div class="form-group">
                <label for="updateCategory">Kategori:</label>
                <select name="category" id="updateCategory" required>
                    <option value="Private">Private</option>
                    <option value="Reguler">Reguler</option>
                </select>
            </div>

            <div class="form-group">
                <label for="updatePrice">Harga:</label>
                <input type="number" name="price" id="updatePrice" required>
            </div>

            <div class="form-group">
                <label for="updateQuota">Kuota:</label>
                <input type="number" name="quota" id="updateQuota" required>
            </div>

            <div class="form-group">
                <label for="updateQuotaLeft">Kuota Tersisa:</label>
                <input type="number" name="quota_left" id="updateQuotaLeft" required>
            </div>

            <div class="form-group">
                <label for="updateSchedule">Schedule:</label>
                <input type="text" name="schedule" id="updateSchedule" required>
            </div>

            <div class="form-group">
                <label for="updateEndDate">Tanggal Selesai:</label>
                <input type="date" name="end_date" id="updateEndDate" required>
            </div>

            <div class="form-group">
                <label for="updateSesion1">Sesion 1:</label>
                <input type="url" name="sesion_1" id="updateSesion1" required>
            </div>

            <div class="form-group">
                <label for="updateSesion2">Sesion 2:</label>
                <input type="url" name="sesion_2" id="updateSesion2" required>
            </div>

            <div class="form-group">
                <label for="updateSesion3">Sesion 3:</label>
                <input type="url" name="sesion_3" id="updateSesion3" required>
            </div>

            <div class="form-group">
                <label for="updateLinkWa">Link WhatsApp:</label>
                <input type="url" name="link_wa" id="updateLinkWa" required>
            </div>

            <div class="form-group">
                <label for="updateLinkYoutube">Link YouTube:</label>
                <input type="url" name="link_youtube" id="updateLinkYoutube">
            </div>

            <div class="form-group">
                <label for="updateStatus">Status:</label>
                <select name="status" id="updateStatus" required>
                    <option value="buka">Buka</option>
                    <option value="tutup">Tutup</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="updateWhatWillLearn1">Kurikulum 1:</label>
                <textarea name="what_will_learn_1" id="updateWhatWillLearn1" placeholder="Topik 1" required></textarea>
            </div>

            <div class="form-group">
                <label for="updateWhatWillLearn2">Kurikulum 2:</label>
                <textarea name="what_will_learn_2" id="updateWhatWillLearn2" placeholder="Topik 2" required></textarea>
            </div>

            <div class="form-group">
                <label for="updateWhatWillLearn3">Kurikulum 3:</label>
                <textarea name="what_will_learn_3" id="updateWhatWillLearn3" placeholder="Topik 3" required></textarea>
            </div>

            <div class="form-group">
                <label for="updateImage">Gambar Kelas:</label>
                <input type="file" name="image" id="updateImage">
                <div id="imageContainer"></div>
            </div>

            <div class="form-group">
                <label for="updateBookIds">Buku yang Dibaca:</label>
                <select name="book_ids[]" id="updateBookIds" multiple>
                    <?php foreach ($allBooks as $book): ?>
                        <option value="<?= htmlspecialchars($book['id']); ?>">
                            <?= htmlspecialchars($book['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Update Kelas</button>
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    return date.toISOString().split('T')[0];
}

function updateMentorName() {
    const mentorId = document.getElementById('updateMentorId').value;
    const selectedMentor = <?= json_encode($allMentors); ?>.find(mentor => mentor.id == mentorId);
    
    if (selectedMentor) {
        document.getElementById('updateNameMentor').value = selectedMentor.name;
    } else {
        document.getElementById('updateNameMentor').value = '';
    }
}

document.getElementById('updateMentorId').addEventListener('change', updateMentorName);

function openEditModal(dealData) {
    const updateModal = document.getElementById('updateKelasModal');
    updateModal.style.display = 'block';
    document.body.style.overflow = 'hidden';

    document.getElementById('updateKelasId').value = dealData.id;
    document.getElementById('updateName').value = dealData.name;
    document.getElementById('updateDescription').value = dealData.description;
    document.getElementById('updateMentorId').value = dealData.mentor_id;
    document.getElementById('updateCategory').value = dealData.category;
    document.getElementById('updatePrice').value = dealData.price;
    document.getElementById('updateQuota').value = dealData.quota;
    document.getElementById('updateQuotaLeft').value = dealData.quota_left;
    document.getElementById('updateSchedule').value = dealData.schedule;
    document.getElementById('updateEndDate').value = formatDate(dealData.end_date);
    document.getElementById('updateLinkWa').value = dealData.link_wa;
    document.getElementById('updateLinkYoutube').value = dealData.link_youtube;
    document.getElementById('updateStatus').value = dealData.status;
    document.getElementById('updateWhatWillLearn1').value = dealData.what_will_learn_1;
    document.getElementById('updateWhatWillLearn2').value = dealData.what_will_learn_2;
    document.getElementById('updateWhatWillLearn3').value = dealData.what_will_learn_3;
    document.getElementById('updateSesion1').value = dealData.sesion_1;
    document.getElementById('updateSesion2').value = dealData.sesion_2;
    document.getElementById('updateSesion3').value = dealData.sesion_3;

    updateMentorName();

    const imageContainer = document.getElementById('imageContainer');
    if (dealData.image) {
        const imageElement = document.createElement('img');
        imageElement.src = dealData.image;
        imageElement.alt = 'Kelas Image';
        imageElement.style.maxWidth = '100%';
        imageElement.style.marginTop = '10px';
        imageContainer.innerHTML = '';
        imageContainer.appendChild(imageElement);
    } else {
        imageContainer.innerHTML = 'No image available';
    }
}

function closeEditModal() {
    const updateModal = document.getElementById('updateKelasModal');
    updateModal.style.display = 'none';
    document.body.style.overflow = '';
}

window.onclick = function(event) {
    const modal = document.getElementById('updateKelasModal');
    if (event.target == modal) {
        closeEditModal();
    }
}
</script>