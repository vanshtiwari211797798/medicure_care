<?php
session_start();
include("includes/conn.php");


$data = json_decode(file_get_contents("php://input"), true);

$userEmail = $_SESSION['user'] ?? '';
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$address = $data['address'];
$pincode = $data['pincode'];
$paymentId = $data['razorpay_payment_id'];
$delivery_charge = $data['delivery_charge'];
$payment_method = "razorpay";

if ($userEmail) {
    $cartItems = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$userEmail'");
    while ($item = mysqli_fetch_assoc($cartItems)) {
        $product_id = $item['product_id'];
        $product_name = $item['product_name'];
        $price = $item['sell_price'];
        $quantity = $item['quantity'];
        $image = $item['image'];

        $orderSql = "INSERT INTO orders (product_id, product_name, quantity, name, address, price, image, email,phone,pincode,payment_id,payment_method,delivery_charge)
                    VALUES ('$product_id', '$product_name', '$quantity', '$name', '$address', '$price', '$image', '$email','$phone','$pincode','$paymentId','$payment_method','$delivery_charge')";
        mysqli_query($conn, $orderSql);
    }

    // Optional: Empty the cart
    mysqli_query($conn, "DELETE FROM cart WHERE user_id='$userEmail'");

    echo "Order saved";
} else {
    echo "No user logged in";
}
