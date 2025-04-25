<?php
include '../includes/conn.php';
include 'header.php';

$admin_id = 1;

$query = "SELECT username, profile_image FROM admins WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
    } else {
        $admin = [
            'username' => 'Admin',
            'profile_image' => 'https://via.placeholder.com/40'
        ];
    }
} else {
    // Fallback in case query fails
    $admin = [
        'username' => 'Admin',
        'profile_image' => 'https://via.placeholder.com/40'
    ];
}
?>


<style>
    :root {
        --primary-color: #3498db;
        --primary-light: #5dade2;
        --secondary-color: #2980b9;
        --accent-color: #e74c3c;
        --light-color: #ecf0f1;
        --dark-color: #2c3e50;
        --success-color: #2ecc71;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --border-radius: 8px;
        --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }

    body {
        background-color: #f5f7fb;
        color: var(--dark-color);
        line-height: 1.6;
    }

    /* Main Content */
    .main-content {
        padding: 20px;
        margin-left: 250px;
        /* Adjust if you have a sidebar */
        transition: var(--transition);
    }

    /* Header */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: white;
        box-shadow: var(--box-shadow);
        border-radius: var(--border-radius);
        margin-bottom: 20px;
    }

    .header-left h2 {
        color: var(--dark-color);
        font-size: 1.5rem;
        font-weight: 600;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-profile img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--light-color);
    }

    .user-info h5 {
        margin: 0;
        font-size: 0.95rem;
        color: var(--dark-color);
        font-weight: 600;
    }

    .user-info p {
        margin: 0;
        font-size: 0.8rem;
        color: #7f8c8d;
    }

    .notification-icon {
        position: relative;
        color: var(--dark-color);
        cursor: pointer;
        font-size: 1.2rem;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--accent-color);
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        font-weight: bold;
    }

    /* Dashboard Metrics */
    .dashboard-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .metric-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
    }

    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .metric-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .metric-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .metric-title {
        font-size: 0.9rem;
        color: #7f8c8d;
        margin-bottom: 5px;
    }

    .metric-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 5px;
    }

    .metric-change {
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .metric-change.positive {
        color: var(--success-color);
    }

    .metric-change.negative {
        color: var(--danger-color);
    }

    /* Color variants */
    .metric-card.sales .metric-icon {
        background-color: var(--primary-color);
    }

    .metric-card.customers .metric-icon {
        background-color: var(--success-color);
    }

    .metric-card.inventory .metric-icon {
        background-color: var(--warning-color);
    }

    .metric-card.prescriptions .metric-icon {
        background-color: var(--accent-color);
    }

    /* Dashboard Sections */
    .dashboard-section {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        box-shadow: var(--box-shadow);
        margin-bottom: 20px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-header h3 {
        color: var(--dark-color);
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-header h3 i {
        color: var(--primary-color);
    }

    /* Tables */
    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    th {
        font-weight: 600;
        color: var(--dark-color);
        font-size: 0.85rem;
        text-transform: uppercase;
        background-color: #f8f9fa;
    }

    td {
        font-size: 0.9rem;
        color: #555;
    }

    .status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .btn-view-more {
        background-color: #007bff;
        color: white;
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 5px;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-view-more:hover {
        background-color: #0056b3;
    }



    .status.completed {
        background-color: rgba(46, 204, 113, 0.1);
        color: var(--success-color);
    }

    .status.pending {
        background-color: rgba(243, 156, 18, 0.1);
        color: var(--warning-color);
    }

    .status.cancelled {
        background-color: rgba(231, 76, 60, 0.1);
        color: var(--danger-color);
    }

    /* List Items */
    .list-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .item-icon {
        width: 40px;
        height: 40px;
        background-color: #f5f5f5;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: var(--dark-color);
        font-size: 1rem;
    }

    .item-info {
        flex: 1;
    }

    .item-name {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 3px;
    }

    .item-detail {
        font-size: 0.8rem;
        color: #7f8c8d;
    }

    .item-stock {
        font-weight: 600;
        color: var(--accent-color);
    }

    /* Layout */
    .two-column {
        display: grid;
        grid-template-columns: 2fr;
        gap: 20px;
    }

    .scrollable-content {
        max-height: 400px;
        overflow-y: auto;
    }


    .view-all {
        text-align: right;
        margin-top: 15px;
    }

    .view-all a {
        color: var(--primary-color);
        text-decoration: none;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: var(--transition);
    }

    .view-all a:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }

    .dashboard-section {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        max-height: 400px;
        /* fixed height */
        overflow-y: auto;
        /* enables vertical scrolling */
    }

    .dashboard-section::-webkit-scrollbar {
        width: 6px;
    }

    .dashboard-section::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }

    .delete-btn {
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
    }

    .delete-btn i {
        font-size: 16px;
    }



    /* Responsive Styles */
    @media (max-width: 1200px) {
        .two-column {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .header-right {
            width: 100%;
            justify-content: space-between;
        }

        .dashboard-overview {
            grid-template-columns: 1fr 1fr;
        }

        /* .main-content {
                margin-left: 0;
                padding: 15px;
            } */
    }

    @media (max-width: 576px) {
        .dashboarRecent Ordersd-overview {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>

<body>
    <div class="main-content">
        <div class="header">
            <div class="header-left">
                <h2>Dashboard Overview</h2>
            </div>
            <div class="header-right">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>

                <div class="user-profile">
                    <img src="<?= htmlspecialchars($admin['profile_image']) ?>" alt="User Profile"
                        style="width: 40px; height: 40px; border-radius: 50%;">
                    <div class="user-info">
                        <h5><?= htmlspecialchars($admin['username']) ?></h5>
                        <p>Admin</p>
                    </div>
                </div>
            </div>

        </div>

        <?php
        $query = "SELECT SUM(price * quantity) as total_sales FROM orders";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $totalSellingAmount = $row['total_sales'] ?? 0;
        ?>

        <!-- Dashboard Overview Metrics -->
        <div class="dashboard-overview">
            <div class="metric-card sales">
                <div class="metric-header">
                    <div>
                        <div class="metric-title">Total Sales Amount</div>
                        <div class="metric-value">₹<?= number_format($totalSellingAmount, 2) ?></div>
                        <div class="metric-change positive">
                            <i class="fas fa-dollar-sign"></i> From all orders
                        </div>
                    </div>
                    <div class="metric-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>

            <?php
            $query = "SELECT COUNT(*) as total_customers FROM admin_users WHERE role = 0";
            $result = $conn->query($query);
            $row = $result->fetch_assoc();
            $totalCustomers = $row['total_customers'];
            ?>

            <div class="metric-card customers">
                <div class="metric-header">
                    <div>
                        <div class="metric-title">Total Customers</div>
                        <div class="metric-value"><?= $totalCustomers ?></div>
                    </div>
                    <div class="metric-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </div>



            <div class="metric-card inventory">
                <div class="metric-header">
                    <div>
                        <?php
                        // Total stock items count
                        $totalStockQuery = "SELECT COUNT(*) AS total_stock FROM products";
                        $totalStockResult = mysqli_query($conn, $totalStockQuery);
                        $totalStockData = mysqli_fetch_assoc($totalStockResult);
                        $totalStockCount = $totalStockData['total_stock'];

                        // Products added today
                        $today = date('Y-m-d');
                        $todayAddedQuery = "SELECT COUNT(*) AS added_today FROM products WHERE DATE(created_at) = '$today'";
                        $todayAddedResult = mysqli_query($conn, $todayAddedQuery);
                        $todayAddedData = mysqli_fetch_assoc($todayAddedResult);
                        $todayAddedCount = $todayAddedData['added_today'];
                        ?>

                        <div class="metric-title">Total Product</div>
                        <div class="metric-value"><?= $totalStockCount ?></div>
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <?= $todayAddedCount ?> added today
                        </div>
                    </div>
                    <div class="metric-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>

                <div style="margin-top: 15px; text-align: right;">
                    <a href="admin_products.php" class="btn-view-more">View More</a>
                </div>
            </div>

        </div>

        <!-- Recent Orders Section -->
        <?php
        $sql = "SELECT o.*, 
            (SELECT COUNT(*) FROM orders WHERE main_order_id = o.id) as has_free_product,
            (SELECT id FROM orders WHERE main_order_id = o.id LIMIT 1) as free_product_id 
            FROM orders o 
            WHERE o.main_order_id IS NULL 
            ORDER BY o.id DESC LIMIT 5";
        $result = $conn->query($sql);
        if (!$result) {
            echo "Error executing query: {$conn->error}";
            $result = false;
        }
        ?>
        <div class="scrollable-content">
            <div class="two-column">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h3><i class="fas fa-receipt"></i> Recent Orders</h3>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Amount</th>
                                    <th>Quantity</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                    <th>Dilevered By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result):
                                    while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?= htmlspecialchars($row['id']) ?></td>
                                            <td>
                                                <div style="display:flex; align-items:center; gap:10px;">
                                                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Product"
                                                        width="50" height="50" style="object-fit:cover; border-radius:6px;">
                                                    <span><?= htmlspecialchars($row['product_name']) ?></span>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($row['name']); ?></td>
                                            <td>
                                                <?= htmlspecialchars($row['phone']); ?><br>
                                                <?= htmlspecialchars($row['email']); ?>
                                            </td>
                                            <td>₹<?= number_format($row['price'] + $row['delivery_charge'], 2); ?></td>
                                            <td><?= htmlspecialchars($row['quantity']); ?></td>
                                            <td><?= ucfirst($row['payment_status']); ?></td>
                                            <td>
                                                <span class="status <?= strtolower($row['status']); ?>">
                                                    <?= ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 8px;">
                                                    <form method="POST" action="update_status.php">
                                                        <input type="hidden" name="order_id" value="<?= $row['id']; ?>">
                                                        <?php if ($row['has_free_product'] > 0): ?>
                                                            <input type="hidden" name="free_product_id"
                                                                value="<?= $row['free_product_id']; ?>">
                                                            <input type="hidden" name="update_both" value="1">
                                                            <select name="new_status" onchange="this.form.submit()"
                                                                style="padding: 2px 5px;">
                                                                <option disabled selected>Update Both Orders</option>
                                                                <option value="pending">Pending</option>
                                                                <option value="processing">Processing</option>
                                                                <option value="shipped">Shipped</option>
                                                                <option value="delivered">Delivered</option>
                                                                <option value="cancelled">Cancelled</option>
                                                            </select>
                                                        <?php else: ?>
                                                            <select name="new_status" onchange="this.form.submit()"
                                                                style="padding: 2px 5px;">
                                                                <option disabled selected>Update Status</option>
                                                                <option value="pending">Pending</option>
                                                                <option value="processing">Processing</option>
                                                                <option value="shipped">Shipped</option>
                                                                <option value="delivered">Delivered</option>
                                                                <option value="cancelled">Cancelled</option>
                                                            </select>
                                                        <?php endif; ?>
                                                    </form>
                                                </div>
                                            </td>
                                            <td>
                                                <form method="POST" action="update_delivered_by.php">
                                                    <input type="hidden" name="order_id" value="<?= $row['id']; ?>">
                                                    <select name="delivered_by" onchange="this.form.submit()"
                                                        style="padding: 2px 5px;">
                                                        <option disabled selected>
                                                            <?= htmlspecialchars($row['delivered_by'] ?? 'Status') ?></option>
                                                        <option value="Track On">Track On</option>
                                                        <option value="DTDC">DTDC</option>
                                                        <!-- <option value="Bluedart">Bluedart</option>
                                                        <option value="Delhivery">Delhivery</option>
                                                        <option value="India Post">India Post</option> -->
                                                    </select>
                                                </form>
                                            </td>

                                        </tr>
                                    <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="view-all">
                        <a href="orders.php">View All Orders <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

</body>

</html>