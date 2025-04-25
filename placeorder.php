<?php
// Show errors (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include DB connection
include './includes/conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cart_checkout'])) {
    // Get data from the form
    $product_ids = $_POST['product_ids']; // array of product IDs
    $name = $_POST['name'];
    $address = $_POST['address'];
    $shipping_charge = $_POST['shipping_charge'];
    $order_date = date('Y-m-d');
    $status = "Pending";
    $source_table = "products";

    if (!is_array($product_ids) || empty($product_ids)) {
        die("No products selected.");
    }

    foreach ($product_ids as $product_id) {
        $product_id = intval($product_id);

        // ✅ Fetch product_name, sale_price, image
        $stmt = $conn->prepare("SELECT product_name, sale_price, image FROM products WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($product = $result->fetch_assoc()) {
            $product_name = $product['product_name'];
            $price = $product['sale_price'];
            $image = $product['image']; // ✅ fixed
            $quantity = 1; // Default quantity

            // ✅ Insert into orders with image
            $insert = $conn->prepare("INSERT INTO orders (product_id, quantity, source_table, order_date, name, address, status, product_name, price, image)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$insert) {
                die("Insert prepare failed: " . $conn->error);
            }

            $insert->bind_param(
                "iissssssds",
                $product_id,     // i
                $quantity,       // i
                $source_table,   // s
                $order_date,     // s
                $name,           // s
                $address,        // s
                $status,         // s
                $product_name,   // s ✅
                $price,          // d ✅
                $image           // s
            );

            $insert->execute();
        } else {
            echo "Product not found for ID: $product_id <br>";
        }
    }

    // ✅ Optional: clear cart logic can go here if needed

    // Redirect to thank you page
    header("Location: thank_you.php");
    exit();

} else {
    echo "Invalid request.";
}
?>