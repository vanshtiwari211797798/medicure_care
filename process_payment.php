<?php
include("includes/conn.php"); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch and sanitize input for main product
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $payment_id = mysqli_real_escape_string($conn, $_POST['payment_id'] ?? '');
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $price = mysqli_real_escape_string($conn, $_POST['amount']);
    $image = mysqli_real_escape_string($conn, $_POST['img']);
    $delivery_charge = mysqli_real_escape_string($conn, $_POST['delivery_charge'] ?? 0);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']); // "Success" or "Pending"
    $source_table = mysqli_real_escape_string($conn, $_POST['source_table'] ?? 'products');
    $payment_method = isset($_POST['payment_method']) ? mysqli_real_escape_string($conn, $_POST['payment_method']) : 'razorpay';

    // Insert main product into orders table
    $query = "INSERT INTO orders 
              (product_id, product_name, quantity, payment_id, name, email, phone, 
               address, pincode, price, image, payment_status, delivery_charge, 
               source_table, payment_method, is_offer_product, order_date) 
              VALUES 
              ('$product_id', '$product_name', '$quantity', '$payment_id', '$name', '$email', '$phone', 
               '$address', '$pincode', '$price', '$image', '$payment_status', '$delivery_charge', 
               '$source_table', '$payment_method', 0, NOW())";

    if (mysqli_query($conn, $query)) {
        $main_order_id = mysqli_insert_id($conn); // Get the ID of the main order
        
        // Process offer products if any
        if (isset($_POST['offer_products']) && is_array($_POST['offer_products'])) {
            foreach ($_POST['offer_products'] as $offer) {
                $offer_product_id = mysqli_real_escape_string($conn, $offer['id']);
                $offer_product_name = mysqli_real_escape_string($conn, $offer['name']);
                $offer_product_price = mysqli_real_escape_string($conn, $offer['price']);
                $offer_product_image = mysqli_real_escape_string($conn, $offer['image']);
                $offer_product_qty = mysqli_real_escape_string($conn, $offer['quantity']);
                
                // Insert offer product into orders table
                $offer_query = "INSERT INTO orders 
                               (product_id, product_name, quantity, payment_id, name, email, phone, 
                                address, pincode, price, image, payment_status, delivery_charge, 
                                source_table, payment_method, is_offer_product, main_order_id, order_date) 
                               VALUES 
                               ('$offer_product_id', '$offer_product_name', '$offer_product_qty', '$payment_id', '$name', '$email', '$phone', 
                                '$address', '$pincode', '$offer_product_price', '$offer_product_image', '$payment_status', '0', 
                                'product_offers', '$payment_method', 1, '$main_order_id', NOW())";
                
                mysqli_query($conn, $offer_query);
            }
        }

        // Redirect to success page
        $message = $payment_status === 'Success' 
            ? "Order Placed Successfully! Payment ID: $payment_id" 
            : "Order Placed Successfully! Payment will be collected on delivery.";
        
        echo "<script>alert('$message'); window.location.href='myorders.php';</script>";
    } else {
        echo "<script>alert('Order Failed! Please try again.'); window.location.href='index.php';</script>";
    }
} else {
    // Handle GET request (for backward compatibility)
    if (isset($_GET['payment_id'])) {
        // Fetch and sanitize input
        $product_id = mysqli_real_escape_string($conn, $_GET['product_id']);
        $product_name = mysqli_real_escape_string($conn, $_GET['product_name']);
        $quantity = mysqli_real_escape_string($conn, $_GET['quantity']);
        $payment_id = mysqli_real_escape_string($conn, $_GET['payment_id']);
        $name = mysqli_real_escape_string($conn, $_GET['name']);
        $email = mysqli_real_escape_string($conn, $_GET['email']);
        $phone = mysqli_real_escape_string($conn, $_GET['phone']);
        $address = mysqli_real_escape_string($conn, $_GET['address']);
        $pincode = mysqli_real_escape_string($conn, $_GET['pincode']);
        $price = mysqli_real_escape_string($conn, $_GET['mainAmount']);
        $image = mysqli_real_escape_string($conn, $_GET['img']);
        $payment_status = mysqli_real_escape_string($conn, $_GET['payment_status']);
        $delivery_charge = 0; // Default for backward compatibility
        $source_table = 'products'; // Default for backward compatibility
        $payment_method = 'razorpay'; // Default for backward compatibility

        // Insert into orders table
        $query = "INSERT INTO orders 
                 (product_id, product_name, quantity, payment_id, name, email, phone, 
                  address, pincode, price, image, payment_status, delivery_charge, 
                  source_table, payment_method, is_offer_product, order_date) 
                 VALUES 
                 ('$product_id', '$product_name', '$quantity', '$payment_id', '$name', '$email', '$phone', 
                  '$address', '$pincode', '$price', '$image', '$payment_status', '$delivery_charge', 
                  '$source_table', '$payment_method', 0, NOW())";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Order Placed Successfully! Payment ID: $payment_id'); window.location.href='myorders.php';</script>";
        } else {
            echo "<script>alert('Order Failed! Please try again.'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid Request!'); window.location.href='index.php';</script>";
    }
}
?>