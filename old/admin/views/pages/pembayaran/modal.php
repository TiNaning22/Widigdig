<div id="updatePaymentStatusModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModal()">×</span>
        <h2>Update Payment Status</h2>
        <form id="updatePaymentStatusForm">
            <label for="paymentStatus">Payment Status:</label>
            <select id="paymentStatus" name="payment_status">
                <option value="menunggu konfirmasi">pending</option>
                <option value="terbayar">completed</option>
            </select>
            <input type="hidden" id="paymentId" name="payment_id" />
            <button type="submit" class="submit-btn">Update</button>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 30%;
    border-radius: 5px;
    position: relative;
}

.close-button {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 1.5rem;
    color: #333;
    cursor: pointer;
}

.submit-btn {
    margin-top: 10px;
    padding: 10px 15px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

.submit-btn:hover {
    background-color: #45a049;
}
</style>
