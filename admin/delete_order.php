<?php
include '../includes/conn.php';

// Add before any HTML
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    $stmt = $conn->prepare("DELETE FROM orders WHERE product_id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=Order+deleted+successfully");
        exit();
    } else {
        echo "Error deleting order: " . $conn->error;
    }
} else {
    echo "Invalid request!";
}
?>
