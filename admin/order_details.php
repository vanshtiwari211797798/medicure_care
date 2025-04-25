<?php
include '../includes/conn.php';

if (!isset($_GET['id'])) {
    echo "Invalid order ID.";
    exit;
}

$order_id = intval($_GET['id']);
$sql = "SELECT * FROM orders WHERE id = $order_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Order not found.";
    exit;
}

$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details - #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your style file here -->
</head>
<style>
    /* General Reset */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f9f9f9;
    color: #333;
}

/* Container */
.container {
    max-width: 700px;
    margin: 40px auto;
    padding: 30px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
}

/* Headings */
h2 {
    font-size: 26px;
    color: #2c3e50;
    margin-bottom: 20px;
    border-bottom: 2px solid #ececec;
    padding-bottom: 10px;
}

h3 {
    font-size: 20px;
    color: #2980b9;
    margin-bottom: 10px;
    margin-top: 30px;
}

/* Info Sections */
.order-info p,
.product-info p,
.payment-info p {
    margin: 8px 0;
    font-size: 15px;
}

strong {
    color: #444;
}

/* Product Image */
.product-info img {
    margin-top: 10px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 4px;
    background-color: #fafafa;
}

/* Button */
.btn {
    display: inline-block;
    margin-top: 25px;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 6px;
    background-color: #2980b9;
    color: #fff;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #1c5980;
}

.btn-secondary {
    background-color: #7f8c8d;
}

.btn-secondary:hover {
    background-color: #636e72;
}

</style>
<body>
    <div class="container">
        <h2>Order Details - #<?php echo $order_id; ?></h2>
        <div class="order-info">
            <h3>Customer Info</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
        </div>

        <div class="product-info">
            <h3>Product Info</h3>
            <img src="uploads/<?php echo htmlspecialchars($order['image']); ?>" alt="Product Image" style="width:100px;">
            <p><strong>Product Name:</strong> <?php echo htmlspecialchars($order['product_name']); ?></p>
            <p><strong>Price:</strong> ₹<?php echo number_format($order['price'], 2); ?></p>
            <p><strong>Quantity:</strong> <?php echo $order['quantity'] ?? '1'; ?></p>
        </div>

        <div class="payment-info">
            <h3>Payment & Delivery</h3>
            <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($order['payment_status']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
            <p><strong>Delivered By:</strong> <?php echo htmlspecialchars($order['delivered_by']); ?></p>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
        </div>

        <a href="orders.php" class="btn btn-secondary">← Back to Orders</a>
    </div>
</body>
</html>
