<?php
include '../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $delivered_by = $conn->real_escape_string($_POST['delivered_by']);

    $sql = "UPDATE orders SET delivered_by = '$delivered_by' WHERE id = $order_id";

    if ($conn->query($sql)) {
        header("Location: dashboard.php"); // or wherever you came from
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>
