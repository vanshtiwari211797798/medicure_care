<?php
include 'includes/conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $source_table = $_POST['source_table'];
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $order_date = date("Y-m-d");
    $status = "pending";
    $image = $_POST['image']; // 🆕 Get image from POST

    // Get product info from the source table
    $stmt = $conn->prepare("SELECT product_name, sale_price FROM `$source_table` WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('Product not found.'); history.back();</script>";
        exit;
    }

    $product = $result->fetch_assoc();
    $product_name = $product['product_name'];
    $price = $product['sale_price'];

    // Insert into orders table with image
    $insert = $conn->prepare("INSERT INTO orders (product_id, quantity, source_table, order_date, name, address, status, product_name, price, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("iisssssdss", $product_id, $quantity, $source_table, $order_date, $name, $address, $status, $product_name, $price, $image);

    if ($insert->execute()) {
        echo "<script>alert('Order placed successfully!'); window.location.href='thank_you.php';</script>";
    } else {
        echo "Error placing order: " . $insert->error;
    }

    $insert->close();
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
