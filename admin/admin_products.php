<?php
include("../includes/conn.php");
include("header.php");

// Handle filtering and searching
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Base query
$query = "SELECT * FROM products WHERE 1";

// Apply category filter
if ($categoryFilter) {
    $query .= " AND category = '" . mysqli_real_escape_string($conn, $categoryFilter) . "'";
}

// Apply search filter
if ($searchQuery) {
    $query .= " AND product_name LIKE '%" . mysqli_real_escape_string($conn, $searchQuery) . "%'";
}

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Inventory | Admin Panel</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 80%;
            margin-left: 20%;
            padding: 20px;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow-x: auto;
        }

        h2 {
            color: var(--secondary-color);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }

        .inventory-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .inventory-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-export {
            background-color: var(--success-color);
            color: white;
        }

        .btn-export:hover {
            background-color: #218838;
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        .inventory-table thead {
            background-color: var(--secondary-color);
            color: white;
            position: sticky;
            top: 0;
        }

        .inventory-table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
        }

        .inventory-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
            white-space: nowrap;
        }

        .inventory-table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }

        .inventory-table img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .stock-high {
            color: var(--success-color);
            font-weight: 500;
        }

        .stock-medium {
            color: var(--warning-color);
            font-weight: 500;
        }

        .stock-low {
            color: var(--danger-color);
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
        }

        .badge-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }

        .badge-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .currency {
            font-family: 'Courier New', monospace;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .inventory-table {
                font-size: 12px;
            }

            .inventory-table th,
            .inventory-table td {
                padding: 8px 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="inventory-header">
            <h2>Product Inventory</h2>
            <div class="inventory-actions">
                <form method="GET" action="products.php">
                    <!-- Category Filter -->
                    <select name="category" onchange="this.form.submit()">
                        <option value="">-- All Categories --</option>
                        <?php
                        $catResult = $conn->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
                        while ($catRow = $catResult->fetch_assoc()) {
                            $selected = ($categoryFilter == $catRow['category']) ? 'selected' : '';
                            echo "<option value=\"" . htmlspecialchars($catRow['category']) . "\" $selected>" . htmlspecialchars($catRow['category']) . "</option>";
                        }
                        ?>
                    </select>

                    <!-- Search Bar -->
                    <input type="text" name="search" value="<?= htmlspecialchars($searchQuery) ?>"
                        placeholder="Search by product name" oninput="this.form.submit()">
                </form>
            </div>
        </div>

        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Image</th>
                    <th class="text-right">Price (₹)</th>
                    <th class="text-center">Discount</th>
                    <th class="text-center">GST</th>
                    <th class="text-right">Sale Price (₹)</th>
                    <th class="text-center">Stock</th>
                    <th>Category</th>
                    <th>Created At</th>
                    <th>Update Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $stock = $row['stock'] ?? 0;
                    $stockClass = '';
                    if ($stock > 20) {
                        $stockClass = 'stock-high';
                    } elseif ($stock > 5) {
                        $stockClass = 'stock-medium';
                    } else {
                        $stockClass = 'stock-low';
                    }

                    // Format prices
                    $price = number_format($row['price'], 2);
                    $salePrice = number_format($row['sale_price'], 2);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><img src="uploads/<?= htmlspecialchars($row['image']) ?>"
                                alt="<?= htmlspecialchars($row['product_name']) ?>"></td>
                        <td class="text-right currency">₹<?= $price ?></td>
                        <td class="text-center"><?= $row['discount'] ?>%</td>
                        <td class="text-center"><?= $row['gst'] ?>%</td>
                        <td class="text-right currency">₹<?= $salePrice ?></td>
                        <td class="text-center <?= $stockClass ?>"><?= $stock ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <form method="GET" action="products.php">
                                <input type="hidden" name="edit" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
// Close the connection
mysqli_free_result($result);
?>