<?php
session_start();
include 'includes/conn.php'; 

// Initialize variables
$productOffers = [];
$error = '';

// Fetch product offers from the database
$query = "SELECT product_name, offer_title, discount, valid_until FROM product_offers WHERE status = 'active' ORDER BY valid_until ASC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productOffers[] = $row;
    }
} else {
    $error = "No product offers are available at the moment.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Offers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .offer {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }

        .offer:last-child {
            border-bottom: none;
        }

        .product-name {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        .offer-title {
            margin: 10px 0;
            color: #555;
        }

        .offer-discount {
            font-size: 16px;
            color: #28a745;
            font-weight: bold;
        }

        .offer-valid {
            font-size: 14px;
            color: #888;
        }

        .error {
            text-align: center;
            color: #e74c3c;
            font-size: 16px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product Offers</h1>

        <?php if (!empty($productOffers)): ?>
            <?php foreach ($productOffers as $offer): ?>
                <div class="offer">
                    <div class="product-name"><?php echo htmlspecialchars($offer['product_name']); ?></div>
                    <div class="offer-title"><?php echo htmlspecialchars($offer['offer_title']); ?></div>
                    <div class="offer-discount">Discount: <?php echo htmlspecialchars($offer['discount']); ?>%</div>
                    <div class="offer-valid">Valid Until: <?php echo date('F j, Y', strtotime($offer['valid_until'])); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>