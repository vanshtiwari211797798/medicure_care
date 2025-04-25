<?php
include("includes/header.php");
?>

<?php
include("includes/conn.php");
$query = "SELECT * FROM contactinfo WHERE id = 1";
$result = mysqli_query($conn, $query);
$contact = mysqli_fetch_assoc($result);
?>


<!-- Contact Section -->
<section class="contact">
    <div class="contact_container">
        <h2>Contact Us</h2>
        <div class="contact-grid">
            <div class="contact-info">
                <h3>Get in Touch</h3>
                <p>Have questions or need assistance? We're here to help! Reach out to us through any of these channels.
                </p>

                <div class="contact-method">
                    <div class="contact-method-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-method-content">
                        <h4>Email</h4>
                        <p><?= htmlspecialchars($contact['email']) ?></p>
                    </div>
                </div>

                <div class="contact-method">
                    <div class="contact-method-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="contact-method-content">
                        <h4>Phone</h4>
                        <p><?= htmlspecialchars($contact['phone']) ?></p>
                    </div>
                </div>

                <div class="contact-method">
                    <div class="contact-method-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-method-content">
                        <h4>Address</h4>
                        <p><?= $contact['address'] ?></p>
                    </div>
                </div>

                <div class="contact-method">
                    <div class="contact-method-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="contact-method-content">
                        <h4>Business Hours</h4>
                        <p><?= $contact['hours'] ?></p>
                    </div>
                </div>
            </div>

            <div class="contact-form">
                <h3>Send Us a Message</h3>
                <form id="message-form">
                    <div class="form-group">
                        <label for="contact-name">Full Name</label>
                        <input type="text" id="contact-name" required placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label for="contact-email">Email Address</label>
                        <input type="email" id="contact-email" required placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label for="contact-subject">Subject</label>
                        <input type="text" id="contact-subject" required placeholder="What's this about?">
                    </div>
                    <div class="form-group">
                        <label for="contact-message">Your Message</label>
                        <textarea id="contact-message" rows="5" required placeholder="How can we help you?"></textarea>
                    </div>
                    <button type="submit" class="btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
include("includes/footer.php");
?>