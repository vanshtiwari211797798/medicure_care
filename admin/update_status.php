<?php
include '../includes/conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $conn->real_escape_string($_POST['new_status']);

    // Step 1: Update main order status
    $sqlMain = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
    $mainResult = $conn->query($sqlMain);

    if ($mainResult) {
        // Step 2: Check if the main order has a free product (offer)
        $sqlOffer = "SELECT id FROM orders WHERE is_offer_product = 1 AND main_order_id = $order_id";
        $offerResult = $conn->query($sqlOffer);

        if ($offerResult && $offerResult->num_rows > 0) {
            // Step 3: Update the free product status
            $sqlUpdateOffer = "UPDATE orders SET status = '$new_status' WHERE is_offer_product = 1 AND main_order_id = $order_id";
            $conn->query($sqlUpdateOffer);
        }

        // ✅ Everything done, redirect
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Main product update failed: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
