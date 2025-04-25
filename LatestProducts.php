<?php
include 'includes/conn.php'; // Make sure this file exists and has DB connection

// Fetch only "Latest Products" category products
$query = "SELECT * FROM products WHERE category = 'Latest Products' ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Latest Products</title>
    <style>
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .product-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            width: 300px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
        }

        .product-card h3 {
            margin: 10px 0 5px;
        }

        .product-card p {
            margin: 5px 0;
        }

        .stock.in {
            color: green;
        }

        .stock.out {
            color: red;
        }

        .price-box {
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h1>Latest Products</h1>

    <?php if ($result->num_rows > 0): ?>
        <div class="product-grid">
            <?php while($row = $result->fetch_assoc()): 
                $stock_status = ($row['stock'] > 0) ? "<span class='stock in'>In Stock</span>" : "<span class='stock out'>Out of Stock</span>";
            ?>
                <div class="product-card">
                    <img src="admin/uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
                    <h3><?= htmlspecialchars($row['product_name']) ?></h3>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <p>Price: ₹<?= $row['price'] ?></p>
                    <p>Discount: <?= $row['discount'] ?>%</p>
                    <p>GST: <?= $row['gst'] ?>%</p>
                    <p>Selling Price: ₹<?= $row['sale_price'] ?></p>
                    <p><?= $stock_status ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No Latest Products found.</p>
    <?php endif; ?>

</body>
</html>
