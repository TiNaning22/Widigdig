<style>
/* Semua CSS original tetap ada */
.footer {
    background-color: #F3F7FF;
    width: 100%;
    padding: 48px 148px;
    border-top: 1px solid #e5e7eb;
    position: relative;
}

.footer-container {
    max-width: 1280px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    gap: 48px;
}

.footer-left {
    flex: 1;
    max-width: 400px;
}

.company-logo {
    font-size: 24px;
    font-weight: bold;
    color: #1a237e;
    margin-bottom: 16px;
}

.company-desc {
    color: #4b5563;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 24px;
}

.footer-social {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.footer-social a {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background-color: #e8eeff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4b5563;
    transition: all 0.2s;
}

.footer-social a:hover {
    background-color: #1a237e;
    color: white;
}

.footer-right {
    flex: 1;
    max-width: 400px;
}

.contact-info {
    margin-bottom: 24px;
}

.contact-info h3 {
    font-size: 18px;
    color: #111827;
    margin-bottom: 16px;
}

.contact-info p {
    color: #4b5563;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 8px;
}

.chat-button {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background-color: #1a237e;
    color: white;
    padding: 12px 24px;
    border-radius: 24px;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
    z-index: 1000;
}

.chat-button:hover {
    transform: translateY(-2px);
}

.footer-bottom {
    margin-top: 48px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
    text-align: center;
    color: #4b5563;
    font-size: 14px;
}

/* Responsive Breakpoints Original */
@media (max-width: 1200px) {
    .footer {
        padding: 48px 100px;
    }
}

@media (max-width: 968px) {
    .footer {
        padding: 48px 80px;
    }
    
    .footer-container {
        gap: 32px;
    }
}

@media (max-width: 768px) {
    .footer {
        padding: 48px 60px;
    }
    
    .footer-container {
        flex-direction: column;
        gap: 40px;
    }
    
    .footer-left,
    .footer-right {
        max-width: 100%;
    }
    
    .company-desc {
        font-size: 13px;
        text-align: start;
    }

    .footer-center {
        max-width: 100%;
    }

    .footer-center .payment-info {
        text-align: start;
    }

    .footer-center .payment-info h3,
    .footer-center .payment-info p {
        text-align: start;
    }

    .contact-info {
        text-align: start;
    }

    .contact-info h3 {
        text-align: start;
    }

    .contact-info p {
        text-align: start;
    }
}

@media (max-width: 480px) {
    .footer {
        padding: 48px 24px;
    }
    
    .company-logo {
        font-size: 20px;
        text-align: start;
    }
    
    .footer-social {
        gap: 12px;
    }
    
    .footer-social a {
        width: 32px;
        height: 32px;
    }
    
    .contact-info h3 {
        font-size: 16px;
        text-align: start;
    }
    
    .contact-info p {
        font-size: 13px;
        text-align: start;
    }

    .footer-center .payment-info,
    .footer-center .payment-info h3,
    .footer-center .payment-info p {
        text-align: start;
    }
    
    .footer-bottom {
        margin-top: 32px;
        padding-top: 20px;
        font-size: 12px;
    }
    
    .chat-button {
        padding: 10px 20px;
        font-size: 14px;
        bottom: 16px;
        right: 16px;
    }
}

/* Tambahan CSS untuk mengatur payment di tengah */
.footer-center {
    flex: 1;
    max-width: 400px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
}

.payment-info {
    text-align: center;
    width: 100%;
}

/* Update responsive untuk payment */
@media (max-width: 768px) {
    .footer-center {
        max-width: 100%;
    }
}
</style>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-left">
            <div class="company-logo">
                <img src="../../../assets/images/logo.svg" alt="logo" style="width: 200px;">
            </div>
            <div class="company-desc" style="font-family: 'Quicksand', sans-serif;">
                Platform digital education belajar yang memberikan materi seputar dunia pendidikan, karir dan bisnis.
            </div>
        </div>
        
        <div class="footer-center">
            <div class="payment-info">
        
                <h3 style="font-family: 'Montserrat', sans-serif;">Pembayaran</h3>
                <img src="../../../assets/images/bri.png" alt="BCA" style="width: 100px;">
                <p style="font-family: 'Quicksand', sans-serif;">No Rek: 7854 0101 2292 538</p>
                <p style="font-family: 'Quicksand', sans-serif;">An: Febrian Wahyu Wibowo</p>
            </div>
        </div>
        
        <div class="footer-right">
            <div class="contact-info">
                <h3>Info Kontak</h3>
                <p>Jl. Turi KM 1, Kepitu, Trimulyo, Kec. Sleman, Kab. Sleman, Yogyakarta 55513</p>
                <p>Tlp : +62 821-3574-3961</p>
                <p>WA : +62 821-3574-3961</p>
                <p>info@kelassore.com</p>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        Copyright © 2024 Kelas Sore | Powered by IT Solution
    </div>
</footer>