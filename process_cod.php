<?php
session_start();
include 'includes/conn.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file setup
$log_file = 'cod_errors.log';
file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Starting COD processing\n", FILE_APPEND);

try {
    // Validate required fields
    $required = ['product_id', 'product_name', 'name', 'email', 'phone', 'address', 'pincode'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Get form data with validation
    $product_id = intval($_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $quantity = intval($_POST['quantity'] ?? 1);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $price = floatval($_POST['amount'] ?? 0);
    $image = mysqli_real_escape_string($conn, $_POST['img'] ?? '');
    $delivery_charge = floatval($_POST['delivery_charge'] ?? 0);
    $payment_method = 'cod';
    $payment_status = 'Pending';
    $source_table = mysqli_real_escape_string($conn, $_POST['source_table'] ?? 'products');
    $status = 'Pending';
    $payment_id = 'COD' . time();

    if (!$email) {
        throw new Exception("Invalid email address");
    }

    if (strlen($phone) < 10) {
        throw new Exception("Phone number must be at least 10 digits");
    }

    // Check database connection
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Start transaction
    mysqli_begin_transaction($conn);

    // Insert main order
    $sql = "INSERT INTO orders (
                product_id, product_name, quantity, source_table, name, 
                address, status, price, image, payment_status, order_date, 
                email, phone, pincode, payment_id, payment_method, 
                delivery_charge, is_offer_product
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, 0)";

    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Preparing SQL: $sql\n", FILE_APPEND);

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }

    $bind = mysqli_stmt_bind_param($stmt, "isissssdsssssssd", 
        $product_id, $product_name, $quantity, $source_table, $name,
        $address, $status, $price, $image, $payment_status,
        $email, $phone, $pincode, $payment_id, $payment_method, $delivery_charge
    );

    if (!$bind) {
        throw new Exception("Bind failed: " . mysqli_stmt_error($stmt));
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }

    $main_order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Process offer products if any
    if (!empty($_POST['offer_products']) && is_array($_POST['offer_products'])) {
        foreach ($_POST['offer_products'] as $offer) {
            // Validate offer product data
            if (empty($offer['id']) || empty($offer['name'])) {
                file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Invalid offer product data\n", FILE_APPEND);
                continue;
            }

            $offer_product_id = intval($offer['id']);
            $offer_product_name = mysqli_real_escape_string($conn, $offer['name']);
            $offer_product_price = floatval($offer['price'] ?? 0);
            $offer_product_image = mysqli_real_escape_string($conn, $offer['image'] ?? '');
            $offer_product_qty = intval($offer['quantity'] ?? 1);

            $offer_sql = "INSERT INTO orders (
                            product_id, product_name, quantity, source_table, name, 
                            address, status, price, image, payment_status, order_date, 
                            email, phone, pincode, payment_id, payment_method, 
                            delivery_charge, is_offer_product, main_order_id
                          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, 1, ?)";

            $offer_stmt = mysqli_prepare($conn, $offer_sql);
            if (!$offer_stmt) {
                throw new Exception("Offer prepare failed: " . mysqli_error($conn));
            }

            $source = 'product_offers';
            $delivery = 0;
            $offer_bind = mysqli_stmt_bind_param($offer_stmt, "isissssdsssssssdi", 
                $offer_product_id, $offer_product_name, $offer_product_qty, $source,
                $name, $address, $status, $offer_product_price, $offer_product_image, $payment_status,
                $email, $phone, $pincode, $payment_id, $payment_method,
                $delivery, $main_order_id
            );

            if (!$offer_bind) {
                throw new Exception("Offer bind failed: " . mysqli_stmt_error($offer_stmt));
            }

            if (!mysqli_stmt_execute($offer_stmt)) {
                throw new Exception("Offer execute failed: " . mysqli_stmt_error($offer_stmt));
            }

            mysqli_stmt_close($offer_stmt);
        }
    }

    // Commit transaction
    mysqli_commit($conn);
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Order $main_order_id processed successfully\n", FILE_APPEND);
    header("Location: order_success.php?order_id=$main_order_id");
    // Redirect to success page
    header("Location: order_success.php?order_id=" . $main_order_id);
    exit;

} catch (Exception $e) {
    // Rollback transaction if it was started
    if (isset($conn) && mysqli_thread_id($conn)) {
        mysqli_rollback($conn);
    }

    // Log the error
    $error_msg = "COD Processing Error: " . $e->getMessage();
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] $error_msg\n", FILE_APPEND);
    error_log($error_msg);

    // Redirect to error page
    header("Location: order_error.php?message=" . urlencode($error_msg));
    exit;
}

// Close connection if still open
if (isset($conn) && mysqli_thread_id($conn)) {
    mysqli_close($conn);
}