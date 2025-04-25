<?php

include '../includes/conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $order_id = intval($_POST['order_id']);
    $tracking_number = trim($_POST['tracking_number']);
    $delivered_by = trim($_POST['delivered_by']);

    if ($order_id > 0 && !empty($tracking_number) && !empty($delivered_by)) {
        $stmt = $conn->prepare("UPDATE orders SET tracking_number = ?, delivered_by = ? WHERE id = ?");
        $stmt->bind_param("ssi", $tracking_number, $delivered_by, $order_id);
        if ($stmt->execute()) {
            echo "success";
        } else {
            http_response_code(500);
            echo "Error updating";
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo "Invalid input";
    }
}
?>
