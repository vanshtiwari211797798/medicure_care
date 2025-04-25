<?php
include("conn.php");
// session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare - Online Pharmacy</title>
    <link rel="icon" type="image/x-icon" href="images/logo.jpg">
    <link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2a7fba;
            --secondary-color: #1a3e5a;
            --accent-color: #ff6b6b;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --light-gray: #e9ecef;
            --success-color: #28a745;
            --border-radius: 8px;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }



        .cont {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Trending Products Section */
        .trending-products {
            padding: 60px 0;
            background-color: var(--light-color);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .section-header h2 {
            font-size: 28px;
            color: var(--secondary-color);
            position: relative;
            padding-bottom: 10px;
        }

        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: var(--primary-color);
        }

        .view-all {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }

        .view-all:hover {
            color: var(--secondary-color);
            transform: translateX(5px);
        }

        .view-all i {
            font-size: 12px;
        }

        /* Category Buttons */
        .product-categories {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            overflow-x: auto;
            padding-bottom: 10px;
            scrollbar-width: none;
        }

        .product-categories::-webkit-scrollbar {
            display: none;
        }

        .category-btn {
            padding: 8px 20px;
            background-color: white;
            border: 1px solid var(--light-gray);
            border-radius: 30px;
            font-weight: 600;
            color: var(--gray-color);
            cursor: pointer;
            transition: var(--transition);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .category-btn.active,
        .category-btn:hover {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 5px;
        }

        .product-card {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card p {
            color: #6b7280;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 0.7rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: var(--accent-color);
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            z-index: 1;
        }

        .product-image {
            height: 170px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background-color: #f9f9f9;
            border-bottom: 1px solid var(--light-gray);
            flex-shrink: 0;
            /* Prevent image container from shrinking */
        }

        .product-image img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .product-details {
            padding: 10px;
            flex-grow: 1;
            /* Allow details to grow and fill space */
            display: flex;
            flex-direction: column;
        }

        .product-details h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: var(--secondary-color);
        }

        .product-description {
            color: var(--gray-color);
            font-size: 14px;
            margin-bottom: 15px;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .price {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .original-price {
            font-size: 14px;
            color: var(--gray-color);
            text-decoration: line-through;
        }

        .add-to-cart {
            width: 100%;
            padding: 10px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: auto;
            /* Push button to bottom */
        }

        .add-to-cart:hover {
            background-color: var(--secondary-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 15px;
            }

            .product-image {
                height: 160px;
            }
        }

        @media (max-width: 480px) {
            .trending-products {
                padding: 40px 0;
            }

            .product-grid {
                grid-template-columns: 1fr 1fr;
            }

            .product-details {
                padding: 15px;
            }

            .product-image {
                height: 120px;
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="./images/logo.jpg" alt="">
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>

                    <!-- Cart link with login check -->
                    <li>
                        <a href="<?= isset($_SESSION['user']) ? 'viewcart.php' : 'login.php' ?>" class="cart-icon">
                            Cart
                            <?php
                            if (isset($_SESSION['user'])) {
                                $user_id = $_SESSION['user'];
                                $countQuery = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'");
                                $countData = mysqli_fetch_assoc($countQuery);
                               
                            }
                            ?>
                        </a>
                    </li>
                    <!-- User icon -->
                    <li>
                        <a href="<?= isset($_SESSION['user']) ? 'profile.php' : 'login.php' ?>" class="user-icon">
                            <i class="fas fa-user-circle" style="font-size:24px"></i>
                        </a>
                    </li>

                    <li>
                        <a class="action" href="./myorders.php">
                            <i class="fas fa-shopping-bag" style="font-size:24px"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="mobile-menu-btn">☰</div>
        </div>
    </header>

    <div id="loader">
        <div class="spinner"></div>
    </div>



    <style>
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            /* Or match your site's background */
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 6px solid #ccc;
            border-top-color: #007bff;
            /* Loader color */
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Base styles for all devices */
        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
        }

        nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            margin-left: 20px;
        }

        nav a {
            text-decoration: none;
            color: #333;
        }

        /* Mobile styles */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
                font-size: 24px;
                cursor: pointer;
                background: none;
                border: none;
                color: #333;
                z-index: 1001;
            }

            nav {
                position: fixed;
                top: 0;
                left: -100%;
                width: 67%;
                height: 100vh;
                background-color: #fff;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
                transition: left 0.3s ease;
                z-index: 1000;
                padding-top: 70px;
            }

            nav.active {
                left: 0;
            }

            nav ul {
                flex-direction: column;
                padding: 20px;
            }

            nav ul li {
                margin: 15px 0;
                margin-left: 0;
            }
        }

        /* Desktop styles */
        @media (min-width: 769px) {
            .mobile-menu-btn {
                display: none;
            }
        }
    </style>
    <script>
        window.addEventListener('load', function () {
            const loader = document.getElementById('loader');
            loader.style.display = 'none';
        });
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const nav = document.querySelector('nav');
            const navLinks = document.querySelectorAll('nav ul li a');

            // Toggle mobile menu
            mobileMenuBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                nav.classList.toggle('active');
                mobileMenuBtn.textContent = nav.classList.contains('active') ? '✕' : '☰';
            });

            // Close menu when a link is clicked
            navLinks.forEach(link => {
                link.addEventListener('click', function () {
                    nav.classList.remove('active');
                    mobileMenuBtn.textContent = '☰';
                });
            });

            // Close menu when clicking outside
            document.addEventListener('click', function (e) {
                if (!nav.contains(e.target) && e.target !== mobileMenuBtn) {
                    nav.classList.remove('active');
                    mobileMenuBtn.textContent = '☰';
                }
            });
        });
    </script>