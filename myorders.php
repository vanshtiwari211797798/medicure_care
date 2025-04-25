<?php
include("includes/header.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

$query = "SELECT * FROM orders WHERE email='$user' ORDER BY order_date DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<style>
    :root {
        --primary-color: #4361ee;
        --primary-light: #eef2ff;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --danger-color: #f72585;
        --success-color: #4cc9f0;
        --warning-color: #f8961e;
        --light-color: #f8f9fa;
        --light-gray: #e9ecef;
        --medium-gray: #adb5bd;
        --dark-color: #212529;
        --text-muted: #6c757d;
        --border-radius: 12px;
        --border-radius-sm: 8px;
        --box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --box-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .orders-container {
        max-width: 1400px;
        margin: 50px auto;
        padding: 40px;
        background: #ffffff;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .orders-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .orders-header h2 {
        margin: 0;
        color: var(--dark-color);
        font-size: 28px;
        font-weight: 700;
        position: relative;
        padding-bottom: 10px;
    }

    .orders-header h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        border-radius: 3px;
    }

    .order-filters {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .filter-dropdown {
        padding: 10px 15px;
        border: 1px solid var(--light-gray);
        border-radius: var(--border-radius-sm);
        background: white;
        font-size: 14px;
        cursor: pointer;
        transition: var(--transition);
    }

    .filter-dropdown:hover {
        border-color: var(--medium-gray);
    }

    .search-input {
        padding: 10px 15px;
        border: 1px solid var(--light-gray);
        border-radius: var(--border-radius-sm);
        min-width: 250px;
        font-size: 14px;
        transition: var(--transition);
    }

    .search-input:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(72, 149, 239, 0.2);
    }

    .no-orders {
        text-align: center;
        padding: 60px 40px;
        background: var(--light-color);
        border-radius: var(--border-radius-sm);
        color: var(--text-muted);
        font-size: 18px;
    }

    .no-orders i {
        font-size: 48px;
        color: var(--medium-gray);
        margin-bottom: 20px;
        display: block;
    }

    .no-orders .btn {
        margin-top: 20px;
    }

    .order-cards {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .order-card {
        background: white;
        border-radius: var(--border-radius-sm);
        box-shadow: var(--box-shadow-sm);
        border: 1px solid var(--light-gray);
        overflow: hidden;
        transition: var(--transition);
    }

    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .order-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 25px;
        background: var(--light-color);
        border-bottom: 1px solid var(--light-gray);
    }

    .order-id {
        font-weight: 600;
        color: var(--dark-color);
    }

    .order-date {
        color: var(--text-muted);
        font-size: 14px;
    }

    .order-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-completed {
        background-color: #d4edda;
        color: #155724;
    }

    .status-processing {
        background-color: #cce5ff;
        color: #004085;
    }

    .status-shipped {
        background-color: #e2e3e5;
        color: #383d41;
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }

    .order-card-body {
        padding: 25px;
    }

    .order-products {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .product-row {
        display: grid;
        grid-template-columns: 80px 1fr auto;
        gap: 20px;
        align-items: center;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--light-gray);
    }

    .product-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: var(--border-radius-sm);
        border: 1px solid var(--light-gray);
    }

    .product-info {
        display: flex;
        flex-direction: column;
    }

    .product-name {
        font-weight: 600;
        margin-bottom: 5px;
        color: var(--dark-color);
    }

    .product-sku {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .product-price {
        font-weight: 600;
        color: var(--dark-color);
    }

    .product-qty {
        color: var(--text-muted);
        font-size: 14px;
    }

    .order-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 25px;
        padding-top: 25px;
        border-top: 1px solid var(--light-gray);
    }

    .summary-item {
        display: flex;
        flex-direction: column;
    }

    .summary-label {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .summary-value {
        font-weight: 500;
    }

    .order-actions {
        display: flex;
        gap: 15px;
        margin-top: 25px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 10px 18px;
        border-radius: var(--border-radius-sm);
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
        border: 1px solid var(--primary-color);
    }

    .btn-primary:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(67, 97, 238, 0.2);
    }

    .btn-outline {
        background-color: transparent;
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
    }

    .btn-outline:hover {
        background-color: rgba(67, 97, 238, 0.1);
        transform: translateY(-2px);
    }

    .btn-sm {
        padding: 8px 14px;
        font-size: 13px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 40px;
        gap: 10px;
    }

    .page-item {
        display: inline-flex;
    }

    .page-link {
        padding: 8px 14px;
        border-radius: var(--border-radius-sm);
        color: var(--dark-color);
        text-decoration: none;
        transition: var(--transition);
        border: 1px solid var(--light-gray);
    }

    .page-link:hover {
        background-color: var(--light-color);
    }

    .page-link.active {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    /* Responsive styles */
    @media (max-width: 992px) {
        .orders-container {
            padding: 30px;
        }
    }

    @media (max-width: 768px) {
        .orders-container {
            padding: 25px 20px;
            margin: 30px 20px;
        }

        .order-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .order-filters {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }

        .search-input {
            width: 100%;
            min-width: auto;
        }

        .product-row {
            grid-template-columns: 60px 1fr;
        }

        .product-qty {
            grid-column: 1 / -1;
        }

        .order-summary {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .orders-container {
            padding: 20px 15px;
        }

        .order-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            padding: 15px;
        }

        .order-card-body {
            padding: 20px 15px;
        }

        .order-actions {
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="orders-container">
    <div class="orders-header">
        <h2>Order History</h2>
        <div class="order-filters">
            <select class="filter-dropdown">
                <option>All Orders</option>
                <option>Last 30 Days</option>
                <option>Past 6 Months</option>
                <option>2023</option>
            </select>
            <select class="filter-dropdown">
                <option>All Statuses</option>
                <option>Processing</option>
                <option>Shipped</option>
                <option>Delivered</option>
                <option>Cancelled</option>
            </select>
            <input type="text" class="search-input" placeholder="Search orders...">
        </div>
    </div>

    <?php
    // First, fetch all orders grouped by main_order_id
    $orders = [];
    while ($order = mysqli_fetch_assoc($result)) {
        if ($order['is_offer_product'] == 1) {
            $orders[$order['main_order_id']]['offers'][] = $order;
        } else {
            $orders[$order['id']]['main'] = $order;
        }
    }
    ?>

    <?php if (!empty($orders)): ?>
        <div class="order-cards">
            <?php foreach ($orders as $order_id => $order_group):
                $main_order = $order_group['main'] ?? null;
                $offer_products = $order_group['offers'] ?? [];

                // Skip if this is just an offer product without main order
                if (!$main_order)
                    continue;
                ?>
                <div class="order-card">
                    <div class="order-card-header">
                        <div>
                            <div class="order-id">Order #<?= htmlspecialchars($main_order['id']) ?></div>
                            <div class="order-id">Tracking Number : <?= htmlspecialchars($main_order['tracking_number']) ?></div>
                            <div class="order-date">Placed on <?= date('M d, Y', strtotime($main_order['order_date'])) ?></div>
                        </div>
                        <div class="order-status status-<?= strtolower($main_order['status']) ?>">
                            <?= htmlspecialchars($main_order['status']) ?>
                        </div>
                    </div>

                    <div class="order-card-body">
                        <!-- Main Product -->
                        <div class="order-products">
                            <div class="product-row main-product">
                                <img src="./admin/uploads/<?= htmlspecialchars($main_order['image']) ?>"
                                    alt="<?= htmlspecialchars($main_order['product_name']) ?>" class="product-image">
                                <div class="product-info">
                                    <div class="product-name">
                                        <?= htmlspecialchars($main_order['product_name']) ?>
                                    </div>
                                    <div class="product-sku">SKU: <?= htmlspecialchars($main_order['product_id']) ?></div>
                                    <div class="product-price">
                                        ₹<?= number_format($main_order['price'], 2) ?>
                                    </div>
                                </div>
                                <div class="product-qty">Qty: <?= htmlspecialchars($main_order['quantity']) ?></div>
                            </div>

                            <!-- Offer Products -->
                            <?php foreach ($offer_products as $offer): ?>
                                <div class="product-row offer-product">
                                    <img src="./admin/uploads/<?= htmlspecialchars($offer['image']) ?>"
                                        alt="<?= htmlspecialchars($offer['product_name']) ?>" class="product-image">
                                    <div class="product-info">
                                        <div class="product-name">
                                            <?= htmlspecialchars($offer['product_name']) ?>
                                            <span class="free-badge">FREE</span>
                                        </div>
                                        <div class="product-sku">SKU: <?= htmlspecialchars($offer['product_id']) ?></div>
                                        <div class="product-price">
                                            FREE
                                        </div>
                                    </div>
                                    <div class="product-qty">Qty: <?= htmlspecialchars($offer['quantity']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="order-summary">
                            <div class="summary-item">
                                <span class="summary-label">Payment Status</span>
                                <span class="summary-value"><?= htmlspecialchars($main_order['payment_status']) ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Total Amount</span>
                                <span
                                    class="summary-value">₹<?= number_format((float)$main_order['price'] + (float)$main_order['delivery_charge'], 2) ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Payment Method</span>
                                <span class="summary-value"><?= htmlspecialchars($main_order['payment_method']) ?></span>
                            </div>
                        </div>

                        <div class="order-actions">
                            <a href="invoice.php?order_id=<?= $main_order['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-file-invoice"></i> View Invoice
                            </a>
                            <a href="TrackOrder.php?order_id=<?= $main_order['id'] ?>" class="btn btn-outline">
                                <i class="fas fa-truck"></i> Track Order
                            </a>

                            <?php if ($main_order['status'] === 'Processing'): ?>
                                <a href="#" class="btn btn-outline">
                                    <i class="fas fa-times"></i> Cancel Order
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination">
            <div class="page-item">
                <a href="#" class="page-link"><i class="fas fa-chevron-left"></i></a>
            </div>
            <div class="page-item">
                <a href="#" class="page-link active">1</a>
            </div>
            <div class="page-item">
                <a href="#" class="page-link">2</a>
            </div>
            <div class="page-item">
                <a href="#" class="page-link">3</a>
            </div>
            <div class="page-item">
                <a href="#" class="page-link"><i class="fas fa-chevron-right"></i></a>
            </div>
        </div>
    <?php else: ?>
        <div class="no-orders">
            <i class="fas fa-box-open"></i>
            <p>You haven't placed any orders yet</p>
            <p>Start shopping to see your order history here</p>
            <a href="products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<style>
    .orders-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .orders-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .order-filters {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-dropdown,
    .search-input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .order-card {
        border: 1px solid #e1e1e1;
        border-radius: 8px;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .order-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e1e1e1;
    }

    .order-id {
        font-weight: bold;
        font-size: 1.1em;
    }

    .order-date {
        color: #666;
        font-size: 0.9em;
    }

    .order-status {
        padding: 4px 10px;
        border-radius: 4px;
        font-weight: 500;
        font-size: 0.9em;
    }

    .status-processing {
        background-color: #cce5ff;
        color: #004085;
    }

    .status-shipped {
        background-color: #d4edda;
        color: #155724;
    }

    .status-delivered {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .order-card-body {
        padding: 15px;
    }

    .product-row {
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .product-row.main-product {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .product-row.offer-product {
        padding-left: 30px;
        position: relative;
    }

    .product-row.offer-product:before {
        content: "+";
        position: absolute;
        left: 15px;
        color: #28a745;
        font-weight: bold;
    }

    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 15px;
    }

    .product-info {
        flex-grow: 1;
    }

    .product-name {
        font-weight: 500;
        margin-bottom: 5px;
    }

    .free-badge {
        background: #4cc9f0;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
        margin-left: 8px;
    }

    .product-sku {
        color: #666;
        font-size: 0.85em;
        margin-bottom: 5px;
    }

    .product-price {
        font-weight: bold;
        color: #28a745;
    }

    .product-qty {
        min-width: 60px;
        text-align: right;
    }

    .order-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin: 20px 0;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
    }

    .summary-label {
        color: #666;
    }

    .summary-value {
        font-weight: 500;
    }

    .order-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 8px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.9em;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        border: 1px solid #007bff;
    }

    .btn-outline {
        background-color: transparent;
        color: #007bff;
        border: 1px solid #007bff;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 30px;
    }

    .page-link {
        display: inline-block;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        color: #007bff;
    }

    .page-link.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .no-orders {
        text-align: center;
        padding: 40px 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin-top: 20px;
    }

    .no-orders i {
        font-size: 3em;
        color: #6c757d;
        margin-bottom: 15px;
    }

    .no-orders p {
        margin: 10px 0;
        color: #6c757d;
    }
</style>
<?php include("includes/footer.php"); ?>