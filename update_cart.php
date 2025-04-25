<?php
session_start();
include("includes/conn.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user'];

if (isset($_POST['update_cart']) && isset($_POST['quantity'])) {
    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $qty = intval($qty);
        if ($qty < 1) $qty = 1;

        $cart_id = intval($cart_id);

        // ✅ FIXED: Wrapped user_id in quotes
        $get = mysqli_query($conn, "SELECT sell_price FROM cart WHERE id = $cart_id AND user_id = '$user_id'");

        if ($get && mysqli_num_rows($get) > 0) {
            $row = mysqli_fetch_assoc($get);
            $sell_price = $row['sell_price'];
            $total = $qty * $sell_price;

            // ✅ FIXED: Wrapped user_id in quotes
            $update = mysqli_query($conn, "UPDATE cart SET quantity = $qty, total = $total WHERE id = $cart_id AND user_id = '$user_id'");

            if (!$update) {
                echo "Update failed for cart_id $cart_id: " . mysqli_error($conn);
                exit;
            }

        } else {
            echo "Cart item not found for id $cart_id. Error: " . mysqli_error($conn);
            exit;
        }
    }
}

header("Location: viewcart.php");
exit;
?>
