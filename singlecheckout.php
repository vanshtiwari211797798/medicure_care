<?php
include("includes/conn.php");
include("includes/header.php");

$id = intval($_GET['id'] ?? 0);
$table = $_GET['table'] ?? 'products';

$allowed_tables = ['products', 'latestproducts', 'dealsoftheday'];
if (!in_array($table, $allowed_tables)) {
    echo "<h2 style='text-align:center; padding:50px;'>Invalid product source.</h2>";
    exit;
}

$res = mysqli_query($conn, "SELECT * FROM `$table` WHERE id = $id");
if (!$res || mysqli_num_rows($res) === 0) {
    echo "<h2 style='text-align:center; padding:50px;'>Product not found.</h2>";
    exit;
}

$row = mysqli_fetch_assoc($res);
?>

<style>
    :root {
        --primary: #2563eb;
        --primary-light: #3b82f6;
        --primary-dark: #1d4ed8;
        --text-dark: #1e293b;
        --text-medium: #475569;
        --text-light: #64748b;
        --border-light: #e2e8f0;
        --border-medium: #cbd5e1;
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --success: #10b981;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --radius-sm: 0.25rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: var(--bg-light);
        margin: 0;
        padding: 0;
        color: var(--text-dark);
        line-height: 1.6;
    }

    .buy-now-container {
        max-width: 800px;
        margin: 3rem auto;
        background: var(--bg-white);
        padding: 2.5rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-light);
    }

    .product-preview {
        display: flex;
        gap: 2rem;
        align-items: flex-start;
        margin-bottom: 2.5rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--border-light);
    }

    .product-image-container {
        width: 240px;
        height: 240px;
        border-radius: var(--radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-light);
        flex-shrink: 0;
    }

    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-image-container:hover img {
        transform: scale(1.03);
    }

    .product-info {
        flex: 1;
    }

    .product-title {
        margin: 0 0 1rem 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-dark);
        line-height: 1.3;
    }

    .product-description {
        margin: 1rem 0;
        color: var(--text-medium);
        line-height: 1.6;
        font-size: 1rem;
    }

    .product-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin: 1.5rem 0;
    }

    .product-meta {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-medium);
    }

    .meta-icon {
        color: var(--success);
        font-size: 1.1rem;
    }

    form {
        margin-top: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: var(--text-dark);
        position: relative;
        padding-bottom: 0.5rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 3rem;
        height: 2px;
        background: var(--primary);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-medium);
        font-size: 1rem;
        transition: all 0.2s ease;
        background-color: var(--bg-light);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        background-color: var(--bg-white);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .quantity-btn {
        width: 2.5rem;
        height: 2.5rem;
        background: var(--bg-light);
        border: 1px solid var(--border-medium);
        border-radius: var(--radius-sm);
        font-size: 1.25rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .quantity-btn:hover {
        background: var(--border-light);
    }

    .quantity-input {
        width: 4rem;
        text-align: center;
        font-weight: 500;
    }

    .btn-primary {
        background-color: var(--primary);
        color: white;
        padding: 1rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
        margin-top: 1rem;
        box-shadow: var(--shadow-sm);
        letter-spacing: 0.5px;
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .secure-checkout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin: 2rem 0 1rem;
        color: var(--success);
        font-weight: 500;
        font-size: 0.95rem;
    }

    .secure-icon {
        font-size: 1.25rem;
    }

    @media (max-width: 768px) {
        .buy-now-container {
            margin: 1.5rem;
            padding: 1.5rem;
            border-radius: var(--radius-md);
        }

        .product-preview {
            flex-direction: column;
            gap: 1.5rem;
        }

        .product-image-container {
            width: 100%;
            height: auto;
            aspect-ratio: 1/1;
        }

        .product-title {
            font-size: 1.5rem;
        }

        .section-title {
            font-size: 1.1rem;
        }
    }
</style>

<div class="buy-now-container">
    <div class="product-preview">
        <div class="product-image-container">
            <img src="admin/uploads/<?= htmlspecialchars($row['image']) ?>"
                alt="<?= htmlspecialchars($row['product_name']) ?>">
        </div>

        <div class="product-info">
            <h1 class="product-title"><?= htmlspecialchars($row['product_name']) ?></h1>
            <p class="product-description"><?= htmlspecialchars($row['description']) ?></p>

            <div class="product-price">₹<?= number_format(htmlspecialchars($row['sale_price']), 2) ?></div>

            <div class="product-meta">
                <div class="meta-item">
                    <span class="meta-icon">✓</span>
                    <span>Free Shipping</span>
                </div>
                <div class="meta-item">
                    <span class="meta-icon">✓</span>
                    <span>Easy Returns</span>
                </div>
                <div class="meta-item">
                    <span class="meta-icon"></span>
                    <?php if ($row['stock'] > 0): ?>
                        <span id="stockStatus" style="color: green; font-weight: bold;">In Stock</span>
                    <?php else: ?>
                        <span id="stockStatus" style="color: red; font-weight: bold;">Out of Stock</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="place_single_order.php" id="buyNowForm">
        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
        <input type="hidden" name="source_table" value="<?= $table ?>">
        <input type="hidden" name="image" value="<?= htmlspecialchars($row['image']) ?>">

        <div class="form-section">
            <h2 class="section-title">Order Details</h2>

            <div class="form-group">
                <label class="form-label">Quantity</label>
                <div class="quantity-selector">
                    <button type="button" class="quantity-btn" id="decrease-qty">-</button>
                    <input type="number" class="form-control quantity-input" name="quantity" min="1" value="1" required>
                    <button type="button" class="quantity-btn" id="increase-qty">+</button>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="section-title">Shipping Information</h2>

            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required
                    placeholder="Enter your full name">
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Shipping Address</label>
                <textarea class="form-control" id="address" name="address" required
                    placeholder="Enter complete shipping address"></textarea>
            </div>
        </div>

        <div class="secure-checkout">
            <span class="secure-icon">🔒</span>
            <span>Secure Checkout • 256-bit SSL Encryption</span>
        </div>

        <button type="submit" class="btn-primary" id="placeOrderBtn">Place Order</button>
    </form>
</div>

<script>
    // Quantity selector
    document.getElementById('decrease-qty').addEventListener('click', function () {
        const qty = document.querySelector('.quantity-input');
        if (parseInt(qty.value) > 1) {
            qty.value = parseInt(qty.value) - 1;
        }
    });

    document.getElementById('increase-qty').addEventListener('click', function () {
        const qty = document.querySelector('.quantity-input');
        qty.value = parseInt(qty.value) + 1;
    });

    // Prevent form submission if out of stock
    document.getElementById('buyNowForm').addEventListener('submit', function (e) {
        const stockText = document.getElementById('stockStatus').textContent.trim();
        if (stockText === 'Out of Stock') {
            e.preventDefault();
            alert("Sorry, this product is currently out of stock. Please check back later.");
        }
    });
</script>


<?php include("includes/footer.php"); ?>