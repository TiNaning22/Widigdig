<div id="addMentorModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2 style="text-align: center; margin-bottom: 25px;">Add Mentor</h2>
        <div class="form-container">
            <form id="addMentorForm">
                <div class="form-group">
                    <label for="mentorName">Mentor Name:</label>
                    <input type="text" id="mentorName" name="mentorName" required>
                </div>
                
                <div class="form-group">
                    <label for="mentorEmail">Mentor Email:</label>
                    <input type="email" id="mentorEmail" name="mentorEmail" required>
                </div>
                
                <div class="form-group">
                    <label for="mentorSpecialty">Mentor Specialty:</label>
                    <input type="text" id="mentorSpecialty" name="mentorSpecialty" required>
                </div>
                
                <button type="submit">Add Mentor</button>
            </form>
        </div>
    </div>
</div>