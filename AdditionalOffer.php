<?php
session_start();
include("includes/header.php");
include("includes/conn.php");

// Initialize variables
$product = [];
$offers = [];
$error = '';

// Get product ID from the URL
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($product_id <= 0) {
    $error = "Invalid product ID.";
} else {
    // Fetch main product details
    $product_query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($product_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();

    if ($product_result && $product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();

        // Fetch associated offers
        $offer_query = "
            SELECT * FROM product_offers 
            WHERE product_id = ?
            ORDER BY created_at DESC";

        $stmt = $conn->prepare($offer_query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $offer_result = $stmt->get_result();

        if ($offer_result && $offer_result->num_rows > 0) {
            while ($row = $offer_result->fetch_assoc()) {
                $offers[] = $row;
            }
        }
    } else {
        $error = "Product not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Offers</title>
    <style>
        :root {
            --primary-color: #4a6bff;
            --secondary-color: #f8f9fa;
            --accent-color: #ff6b6b;
            --text-color: #333;
            --light-text: #6c757d;
            --border-color: #e0e0e0;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --border-radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7ff;
            color: var(--text-color);
            line-height: 1.6;
        }

        .containerr {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        h1, h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        h1 {
            font-size: 2.2rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }

        h2 {
            font-size: 1.8rem;
            margin-top: 2rem;
            text-align: left;
        }

        /* Main Product Section */
        .main-product {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .product-image {
            width: 300px;
            height: 300px;
            object-fit: contain;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
        }

        .product-details {
            flex: 1;
            min-width: 300px;
        }

        .product-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .product-description {
            color: var(--light-text);
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .product-price, .sale-price, .product-discount {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .product-price {
            text-decoration: line-through;
            color: var(--light-text);
        }

        .sale-price {
            color: var(--accent-color);
            font-weight: 700;
            font-size: 1.4rem;
        }

        .product-discount {
            display: inline-block;
            background: #28a74520;
            color: #28a745;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Offers Section */
        .offer-section {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .offers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .offer {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .offer:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .offer-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
        }

        .offer-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .offer-description {
            color: var(--light-text);
            margin-bottom: 1rem;
        }

        .offer-price, .offer-quantity, .offer-id, .offer-date {
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .offer-price {
            font-weight: 600;
            color: var(--text-color);
        }

        .offer-quantity {
            color: var(--light-text);
        }

        .offer-id {
            color: var(--light-text);
            font-size: 0.85rem;
        }

        .offer-date {
            color: var(--light-text);
            font-size: 0.85rem;
            font-style: italic;
        }

        /* Error Message */
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
            margin: 2rem 0;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-product {
                flex-direction: column;
                align-items: center;
            }

            .product-image {
                width: 100%;
                max-width: 300px;
            }

            .offers-grid {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <div class="containerr">
        <h1>Product Details & Offers</h1>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php else: ?>
            <!-- Main Product Section -->
            <div class="main-product">
                <img src="admin/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                <div class="product-details">
                    <div class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                    <div class="product-description"><?php echo htmlspecialchars($product['description']); ?></div>
                    <div class="product-price">MRP: ₹<?php echo number_format($product['price'], 2); ?></div>
                    <?php if ($product['discount'] > 0): ?>
                        <div class="product-discount">Discount: <?php echo htmlspecialchars($product['discount']); ?>%</div>
                        <div class="sale-price">Sale Price: ₹<?php echo number_format($product['sale_price'], 2); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Offers Section -->
            <?php if (!empty($offers)): ?>
                <div class="offer-section">
                    <h2 class="offer-title">Available Offers</h2>
                    <div class="offers-grid">
                        <?php foreach ($offers as $offer): ?>
                            <div class="offer">
                                <img src="admin/uploads/<?php echo htmlspecialchars($offer['offer_product_image']); ?>"
                                    alt="<?php echo htmlspecialchars($offer['offer_product_name']); ?>" class="offer-image">
                                <div class="offer-details">
                                    <div class="offer-name"><?php echo htmlspecialchars($offer['offer_product_name']); ?></div>
                                    <div class="offer-description"><?php echo htmlspecialchars($offer['offer_product_description']); ?></div>
                                    <div class="offer-price">MRP: ₹<?php echo number_format($offer['offer_product_mrp'], 2); ?></div>
                                    <div class="offer-quantity">Quantity: <?php echo htmlspecialchars($offer['offer_product_qty']); ?></div>
                                    <div class="offer-id">Offer ID: <?php echo htmlspecialchars($offer['offer_id']); ?></div>
                                    <div class="offer-date">Added: <?php echo date('F j, Y', strtotime($offer['created_at'])); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>