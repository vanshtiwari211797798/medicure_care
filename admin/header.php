<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Panel</title>
    <link rel="icon" type="image/x-icon" href="../images/logo.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <h2><i class="fas fa-user-shield"></i> Admin Panel</h2>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

        <button class="dropdown-btn"><i class="fas fa-desktop"></i> Manage Frontend <i
                class="dropdown-icon fas fa-chevron-down"></i></button>
        <div class="dropdown-container">
            <a href="slider.php"><i class="fas fa-sliders-h"></i> Slider Section</a>
            <a href="Category.php"><i class="fas fa-tags"></i> Category</a>
            <a href="products.php"><i class="fas fa-box-open"></i> Category Products</a>
            <a href="banner.php"><i class="fas fa-image"></i> Banner</a>
            <!-- <a href="LatestProducts.php"><i class="fas fa-star"></i> Latest Products</a> -->
            <a href="HealthArticles.php"><i class="fas fa-newspaper"></i> Health Articles</a>
            
            <a href="FeaturedBrands.php"><i class="fas fa-award"></i> Featured Brands</a>
            <a href="admin-reviews.php"><i class="fas fa-quote-left"></i>Testimonials</a>
            <a href="about.php"><i class="fas fa-quote-left"></i>About Us Page</a>
        </div>
        <a href="CouponCode.php"><i class="fas fa-user-cog"></i>Coupon Code</a>
        <a href="admin_products.php"><i class="fas fa-user-cog"></i>Stock</a>
        <a href="orders.php"><i class="fas fa-clock"></i>All Orders</a>
        <a href="edit_contactinfo.php"><i class="fas fa-user-cog"></i>Edit Contact INFO</a>
        <a href="admin_profile.php"><i class="fas fa-user-cog"></i> Admin Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <script>
        const dropdown = document.querySelector(".dropdown-btn");
        const container = document.querySelector(".dropdown-container");
        const dropdownIcon = document.querySelector(".dropdown-icon");

        dropdown.addEventListener("click", () => {
            container.style.display = container.style.display === "block" ? "none" : "block";
            dropdownIcon.className = container.style.display === "block"
                ? "dropdown-icon fas fa-chevron-up"
                : "dropdown-icon fas fa-chevron-down";
        });
    </script>