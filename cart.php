<?php
session_start();
include("includes/conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Avoid duplicates
    if (!in_array($productId, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $productId;
    }

    // Redirect back to previous page or viewcart
    header("Location: viewcart.php");
    exit;
} else {
    // If accessed directly or invalid request
    header("Location: index.php");
    exit;
}

