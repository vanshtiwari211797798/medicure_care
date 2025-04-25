<?php
session_start();
ob_start();
include("includes/conn.php");

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['table'])) {
    echo "<script>alert('Invalid request.'); history.back();</script>";
    exit;
}

$id = intval($_GET['id']);
$table = $_GET['table'];

// Whitelist allowed tables
$valid_tables = ['products', 'latestproducts', 'dealsoftheday'];
if (!in_array($table, $valid_tables)) {
    echo "<script>alert('Invalid product source.'); history.back();</script>";
    exit;
}

$sql = "SELECT * FROM $table WHERE id = $id";
$data = mysqli_query($conn, $sql);

if (mysqli_num_rows($data) > 0) {
    $row = mysqli_fetch_assoc($data);

    // Map fields based on table
    if ($table === 'products') {
        $product_name = $row['product_name'];
        $category = $row['category'];
        $price = $row['price'];
        $sell_price = $row['sale_price'];
        $description = $row['description'];
        $image = $row['image'];
    } elseif ($table === 'latestproducts') {
        $product_name = $row['product_name'];
        $category = $row['category'];
        $price = $row['net_price'];
        $sell_price = $row['selling_price'];
        $description = $row['description'];
        $image = $row['image'];
    } elseif ($table === 'dealsoftheday') {
        $product_name = $row['product_name'];
        $category = isset($row['category']) ? $row['category'] : 'Deals';
        $price = $row['netprice'];
        $sell_price = $row['sale_price'];
        $description = $row['description'];
        $image = $row['image'];
    }

    // Set discount (use DB value if available in dealsoftheday)
    if ($table === 'dealsoftheday' && isset($row['discount']) && is_numeric($row['discount'])) {
        $percentage_off = floatval($row['discount']);
    } elseif ($price > 0) {
        $percentage_off = round((($price - $sell_price) / $price) * 100, 2);
    } else {
        $percentage_off = 0;
    }

    $user_id = $_SESSION['user'];
    $product_id = $row['id'];
    $quantity = 1;
    $total = $sell_price * $quantity;

    // Check if product is already in cart
    $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");
    if (mysqli_num_rows($check) > 0) {
        $existing = mysqli_fetch_assoc($check);
        $new_qty = $existing['quantity'] + 1;
        $new_total = $new_qty * $sell_price;
        mysqli_query($conn, "UPDATE cart SET quantity='$new_qty', total='$new_total' WHERE user_id='$user_id' AND product_id='$product_id'");
    } else {
        mysqli_query($conn, "INSERT INTO cart (product_name, category, price, sell_price, percentage_off, user_id, product_id, description, image, quantity, total) 
            VALUES ('$product_name', '$category', '$price', '$sell_price', '$percentage_off', '$user_id', '$product_id', '$description', '$image', '$quantity', '$total')");
    }

    echo "<script>alert('Product added to cart successfully!'); window.location.href='viewcart.php';</script>";
} else {
    echo "<script>alert('Product not found.'); history.back();</script>";
}
?>
