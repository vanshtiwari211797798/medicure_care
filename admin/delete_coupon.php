<?php
include '../includes/conn.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize ID

    $sql = "DELETE FROM coupons WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Coupon deleted successfully.";
    } else {
        echo "Error deleting coupon: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Coupon ID not provided.";
}

$conn->close();
?>
