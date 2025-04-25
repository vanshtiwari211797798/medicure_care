<style>
    :root {
        --primary-color: #4361ee;
        --primary-light: #eef2ff;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --dark-color: #212529;
        --light-color: #f8f9fa;
        --text-muted: #6c757d;
        --border-radius: 8px;
        --transition: all 0.3s ease;
    }

    footer {
        background-color: var(--dark-color);
        color: white;
        padding: 60px 0 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        margin-top: 80px;
    }

    .footer_container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-col {
        margin-bottom: 30px;
    }

    .footer-col h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
        color: white;
    }

    .footer-col h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 2px;
        background: var(--accent-color);
    }

    .footer-col p {
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .footer-col ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-col ul li {
        margin-bottom: 12px;
    }

    .footer-col ul li a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: var(--transition);
        display: inline-block;
    }

    .footer-col ul li a:hover {
        color: white;
        transform: translateX(5px);
    }

    .contact-method {
        margin-top: 20px;
    }

    .contact-method h4 {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 8px;
        color: white;
    }

    .social-links {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .social-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: white;
        transition: var(--transition);
    }

    .social-links a:hover {
        background: var(--accent-color);
        transform: translateY(-3px);
    }

    .copyright {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding: 25px 0;
        text-align: center;
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
    }

    .newsletter-form {
        margin-top: 20px;
    }

    .newsletter-input {
        width: 100%;
        padding: 12px 15px;
        border: none;
        border-radius: var(--border-radius);
        margin-bottom: 10px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .newsletter-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .newsletter-btn {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-weight: 500;
        transition: var(--transition);
        width: 100%;
    }

    .newsletter-btn:hover {
        background: var(--primary-color);
    }

    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }
</style>


<!-- Footer -->
<footer>
    <div class="footer_container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3>Medi<span style="color: #4895ef;">Cure</span></h3>
                <p>Your trusted online pharmacy for genuine medicines and healthcare products. We deliver quality
                    healthcare right to your doorstep.</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/share/1BxrvK6B1h/?mibextid=qi2Omg" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.instagram.com/medicurehealthcare_9?igsh=cmcxaGM5ZjIxMmc3" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Shop Products</a></li>
                    <li><a href="health-blog.php">Health Blog</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="faq.php">FAQs</a></li>
                    <li><a href="./admin/login.php">Admin Login</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Customer Service</h3>
                <ul>
                    <li><a href="ShippingPolicy.php">Shipping Policy</a></li>
                    <li><a href="returns.php">Returns & Refunds</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms of Service</a></li>
                    <li><a href="prescription.php">Prescription Info</a></li>
                    
                </ul>
            </div>

            <?php
            include("includes/conn.php");
            $query = "SELECT * FROM contactinfo WHERE id = 1";
            $result = mysqli_query($conn, $query);
            $contact = mysqli_fetch_assoc($result);
            ?>

            <div class="footer-col">
                <h3>Contact Us</h3>
                <div class="contact-method">
                    <i class="fas fa-envelope"></i>
                    <p><?= htmlspecialchars($contact['email']) ?></p>
                </div>
                <div class="contact-method">
                    <i class="fas fa-phone-alt"></i>
                    <p><?= htmlspecialchars($contact['phone']) ?></p>
                </div>
                <div class="contact-method">
                    <i class="fas fa-map-marker-alt"></i>
                    <p><?= htmlspecialchars($contact['address']) ?></p>
                </div>

                <div class="newsletter-form">
                    <h4>Subscribe to Newsletter</h4>
                    <input type="email" class="newsletter-input" placeholder="Your email address">
                    <button type="submit" class="newsletter-btn">Subscribe</button>
                </div>
            </div>
        </div>

        <div class="copyright">
            <p>&copy; 2025 MediCure. All rights reserved. | GST : 09BDXPV9983J1ZX </p>
        </div>
    </div>
</footer>

<script src="js/script.js"></script>
</body>

</html>