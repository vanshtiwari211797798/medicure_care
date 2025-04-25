<?php include("includes/header.php");
?>

<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap">
<style>
    :root {
        --primary: #4CAF50;
        --primary-dark: #388E3C;
        --secondary: #2196F3;
        --dark: #212121;
        --light: #f5f5f5;
        --gray: #757575;
        --white: #ffffff;
    }

    

    body {
        color: var(--dark);
        line-height: 1.6;
        background-color: var(--light);
    }

    .containerr {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Header Styles */
    header {
        background-color: var(--white);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0;
    }

    .logo {
        /*  */
        font-size: 28px;
        font-weight: 600;
        color: var(--primary);
        text-decoration: none;
    }

    .logo span {
        color: var(--secondary);
    }

    .nav-links {
        display: flex;
        list-style: none;
    }

    .nav-links li {
        margin-left: 30px;
    }

    .nav-links a {
        text-decoration: none;
        color: var(--dark);
        font-weight: 500;
        transition: color 0.3s;
    }

    .nav-links a:hover {
        color: var(--primary);
    }

    .active {
        color: var(--primary) !important;
    }

    /* Hero Section */
    .hero {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1505751172876-fa1923c5c528?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
        background-size: cover;
        background-position: center;
        height: 400px;
        display: flex;
        align-items: center;
        color: var(--white);
        text-align: center;
        margin-bottom: 50px;
    }

    .hero-content {
        max-width: 800px;
        margin: 0 auto;
    }

    .hero h1 {
        
        font-size: 48px;
        margin-bottom: 20px;
    }

    .hero p {
        font-size: 18px;
        margin-bottom: 30px;
    }

    .btn {
        display: inline-block;
        background-color: var(--primary);
        color: var(--white);
        padding: 12px 30px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: var(--primary-dark);
    }

    /* Blog Section */
    .blog-section {
        padding: 50px 0;
    }

    .section-title {
        
        font-size: 36px;
        text-align: center;
        margin-bottom: 40px;
        color: var(--dark);
    }

    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
    }

    .blog-card {
        background-color: var(--white);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .blog-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .blog-img {
        height: 220px;
        overflow: hidden;
    }

    .blog-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .blog-card:hover .blog-img img {
        transform: scale(1.1);
    }

    .blog-content {
        padding: 25px;
    }

    .blog-category {
        display: inline-block;
        color: var(--primary);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .blog-title {
        
        font-size: 22px;
        margin-bottom: 15px;
        line-height: 1.4;
    }

    .blog-excerpt {
        color: var(--gray);
        margin-bottom: 20px;
    }

    .blog-meta {
        display: flex;
        align-items: center;
        font-size: 14px;
        color: var(--gray);
    }

    .blog-meta img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 10px;
    }

    /* Newsletter */
    .newsletter {
        background-color: var(--primary);
        padding: 60px 0;
        margin: 50px 0;
        color: var(--white);
        text-align: center;
    }

    .newsletter h2 {
        
        font-size: 32px;
        margin-bottom: 20px;
    }

    .newsletter p {
        max-width: 600px;
        margin: 0 auto 30px;
    }

    .newsletter-form {
        display: flex;
        max-width: 500px;
        margin: 0 auto;
    }

    .newsletter-form input {
        flex: 1;
        padding: 15px;
        border: none;
        border-radius: 30px 0 0 30px;
        font-family: 'Poppins', sans-serif;
    }

    .newsletter-form button {
        background-color: var(--dark);
        color: var(--white);
        border: none;
        padding: 0 25px;
        border-radius: 0 30px 30px 0;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .newsletter-form button:hover {
        background-color: #000;
    }

    /* Footer */
    footer {
        background-color: var(--dark);
        color: var(--white);
        padding: 60px 0 20px;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-column h3 {
        
        font-size: 20px;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }

    .footer-column h3::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 2px;
        background-color: var(--primary);
    }

    .footer-column p {
        margin-bottom: 15px;
        color: #bdbdbd;
    }

    .footer-links {
        list-style: none;
    }

    .footer-links li {
        margin-bottom: 10px;
    }

    .footer-links a {
        color: #bdbdbd;
        text-decoration: none;
        transition: color 0.3s;
    }

    .footer-links a:hover {
        color: var(--primary);
    }

    .social-links {
        display: flex;
        gap: 15px;
    }

    .social-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: #424242;
        border-radius: 50%;
        color: var(--white);
        transition: background-color 0.3s;
    }

    .social-links a:hover {
        background-color: var(--primary);
    }

    .copyright {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #424242;
        color: #bdbdbd;
        font-size: 14px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .navbar {
            flex-direction: column;
        }

        .nav-links {
            margin-top: 20px;
        }

        .nav-links li {
            margin: 0 10px;
        }

        .hero h1 {
            font-size: 36px;
        }

        .newsletter-form {
            flex-direction: column;
        }

        .newsletter-form input,
        .newsletter-form button {
            width: 100%;
            border-radius: 30px;
        }

        .newsletter-form button {
            margin-top: 10px;
            padding: 15px;
        }
    }
</style>


<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Your Journey to Better Health Starts Here</h1>
        <p>Evidence-based articles to help you live a healthier, happier life</p>
        <a href="#" class="btn">Explore Articles</a>
    </div>
</section>

<!-- Blog Section -->
<section class="blog-section">
    <div class="containerr">
        <h2 class="section-title">Latest Health Articles</h2>
        <div class="blog-grid">
            <!-- Blog Card 1 -->
            <article class="blog-card">
                <div class="blog-img">
                    <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                        alt="Healthy Food">
                </div>
                <div class="blog-content">
                    <span class="blog-category">Nutrition</span>
                    <h3 class="blog-title">10 Superfoods You Should Be Eating Daily</h3>
                    <p class="blog-excerpt">Discover the nutrient-packed foods that can boost your immunity and overall
                        health with minimal calories.</p>
                    <div class="blog-meta">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Author">
                        <span>Dr. Sarah Johnson • May 15, 2023 • 8 min read</span>
                    </div>
                </div>
            </article>

            <!-- Blog Card 2 -->
            <article class="blog-card">
                <div class="blog-img">
                    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                        alt="Woman Exercising">
                </div>
                <div class="blog-content">
                    <span class="blog-category">Fitness</span>
                    <h3 class="blog-title">The 20-Minute Workout for Busy Professionals</h3>
                    <p class="blog-excerpt">Time-efficient exercises that deliver maximum results, perfect for those
                        with packed schedules.</p>
                    <div class="blog-meta">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Author">
                        <span>Mark Williams • May 10, 2023 • 6 min read</span>
                    </div>
                </div>
            </article>

            <!-- Blog Card 3 -->
            <article class="blog-card">
                <div class="blog-img">
                    <img src="https://images.unsplash.com/photo-1498409785966-ab341407de6e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                        alt="Meditation">
                </div>
                <div class="blog-content">
                    <span class="blog-category">Mental Health</span>
                    <h3 class="blog-title">Mindfulness Meditation: A Beginner's Guide</h3>
                    <p class="blog-excerpt">Learn how just 10 minutes of daily meditation can reduce stress and improve
                        your focus and emotional well-being.</p>
                    <div class="blog-meta">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Author">
                        <span>Dr. Emily Chen • May 5, 2023 • 10 min read</span>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="newsletter">
    <div class="containerr">
        <h2>Join Our Health Community</h2>
        <p>Subscribe to our newsletter and receive weekly health tips, exclusive content, and special offers.</p>
        <form class="newsletter-form">
            <input type="email" placeholder="Your email address" required>
            <button type="submit">Subscribe</button>
        </form>
    </div>
</section>

<!-- Blog Section 2 -->
<section class="blog-section">
    <div class="containerr">
        <h2 class="section-title">Popular Reads</h2>
        <div class="blog-grid">
            <!-- Blog Card 4 -->
            <article class="blog-card">
                <div class="blog-img">
                    <img src="https://images.unsplash.com/photo-1545205597-3d9d02c29597?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                        alt="Sleep">
                </div>
                <div class="blog-content">
                    <span class="blog-category">Wellness</span>
                    <h3 class="blog-title">The Science of Sleep: How to Optimize Your Rest</h3>
                    <p class="blog-excerpt">Evidence-based strategies to improve your sleep quality and wake up
                        refreshed every morning.</p>
                    <div class="blog-meta">
                        <img src="https://randomuser.me/api/portraits/men/22.jpg" alt="Author">
                        <span>Dr. Robert Kim • April 28, 2023 • 12 min read</span>
                    </div>
                </div>
            </article>

            <!-- Blog Card 5 -->
            <article class="blog-card">
                <div class="blog-img">
                    <img src="https://images.unsplash.com/photo-1535914254981-b5012eebbd15?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                        alt="Gut Health">
                </div>
                <div class="blog-content">
                    <span class="blog-category">Nutrition</span>
                    <h3 class="blog-title">Gut Health 101: Foods That Support Your Microbiome</h3>
                    <p class="blog-excerpt">Learn how to nourish your gut bacteria for better digestion, immunity, and
                        even mental health.</p>
                    <div class="blog-meta">
                        <img src="https://randomuser.me/api/portraits/women/54.jpg" alt="Author">
                        <span>Dr. Lisa Patel • April 20, 2023 • 9 min read</span>
                    </div>
                </div>
            </article>

            <!-- Blog Card 6 -->
            <article class="blog-card">
                <div class="blog-img">
                    <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                        alt="Yoga">
                </div>
                <div class="blog-content">
                    <span class="blog-category">Fitness</span>
                    <h3 class="blog-title">Yoga for Stress Relief: 5 Poses to Try Today</h3>
                    <p class="blog-excerpt">Simple yoga sequences that can help calm your mind and relieve tension in
                        just 15 minutes.</p>
                    <div class="blog-meta">
                        <img src="https://randomuser.me/api/portraits/women/76.jpg" alt="Author">
                        <span>Jessica Lee • April 15, 2023 • 7 min read</span>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<?php include("includes/footer.php");
?>