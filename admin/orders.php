<?php
include 'header.php';
include '../includes/conn.php';

// SQL query to fetch recent orders with related details
$query = "SELECT o.*, 
            (SELECT COUNT(*) FROM orders WHERE main_order_id = o.id) as has_free_product,
            (SELECT id FROM orders WHERE main_order_id = o.id LIMIT 1) as free_product_id 
            FROM orders o 
            WHERE o.main_order_id IS NULL 
            ORDER BY o.id DESC LIMIT 5";
$result = $conn->query($query);

if (!$result) {
    echo "Error executing query: {$conn->error}";
    $result = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Dashboard</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Main Container Styling */
        .container.mt-5 {
            width: 80%;
            margin: 20px auto 20px 20%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Scrollable Content */
        .scrollable-content {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            padding-right: 10px;
        }

        /* Scrollbar styling */
        .scrollable-content::-webkit-scrollbar {
            width: 8px;
        }

        .scrollable-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .scrollable-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .scrollable-content::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Two Column Layout */
        .two-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Dashboard Section */
        .dashboard-section {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Section Header */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            background-color: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }

        .section-header h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
            font-weight: 600;
        }

        .section-header i {
            margin-right: 10px;
            color: #6c757d;
        }

        /* Table Styling */
        .table-responsive {
            overflow-x: auto;
            padding: 0 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            position: sticky;
            top: 0;
            white-space: nowrap;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        /* Status Badges */
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }

        .status.pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .status.processing {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }

        .status.shipped {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status.delivered {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status.cancelled {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Buttons and Actions */
        .delete-btn {
            background-color: #f8d7da;
            color: #721c24;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .delete-btn:hover {
            background-color: #f1b0b7;
        }

        select {
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        select:hover {
            border-color: #aaa;
        }

        /* View All Link */
        .view-all {
            padding: 15px 20px;
            text-align: right;
            border-top: 1px solid #eee;
        }

        .view-all a {
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
        }

        .view-all a:hover {
            color: #007bff;
        }

        .view-all i {
            margin-left: 5px;
            font-size: 12px;
        }

        /* Search and Filter Section */
        .search-filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .filter-box {
            min-width: 200px;
        }

        .search-box input, .filter-box select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-box input {
            padding-left: 35px;
        }

        .search-box::before {
            content: "\f002";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .search-box input:focus, .filter-box select:focus {
            outline: none;
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Product Image */
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #eee;
        }

        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .container.mt-5 {
                margin-left: 0;
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            th, td {
                padding: 8px 10px;
                font-size: 14px;
            }
            
            .search-filter-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box, .filter-box {
                min-width: 100%;
            }
        }

        /* No results message */
        .no-results {
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
            display: none;
        }
    </style>

</head>

<body>

    <div class="container mt-5">
        <div class="scrollable-content">
            <div class="search-filter-container">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search by name, contact, email..." onkeyup="searchOrders()">
                </div>
                <div class="filter-box">
                    <select id="statusFilter" onchange="filterOrders()">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="filter-box">
                    <select id="deliveryFilter" onchange="filterOrders()">
                        <option value="">All Delivery Methods</option>
                        <option value="Track On">Track On</option>
                        <option value="DTDC">DTDC</option>
                        <!-- <option value="Bluedart">Bluedart</option>
                        <option value="Delhivery">Delhivery</option>
                        <option value="India Post">India Post</option> -->
                    </select>
                </div>
            </div>
            
            <div class="two-column">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h3><i class="fas fa-receipt"></i> Recent Orders</h3>
                    </div>
                    <div class="table-responsive">
                        <table id="ordersTable">
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
            <th>Delivered By</th>
            <th>Order Details</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo htmlspecialchars($row['id']); ?></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <img src="uploads/<?php echo htmlspecialchars($row['image'] ?: 'https://via.placeholder.com/50'); ?>" 
                                alt="Product" class="product-image" width="50">
                            <span><?php echo htmlspecialchars($row['product_name']); ?></span>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['phone']); ?><br>
                        <?php echo htmlspecialchars($row['email']); ?>
                    </td>
                    <td>₹<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                    <td>
                        <span class="status <?php echo strtolower($row['status']); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="delete-btn" title="Delete Order">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <select style="padding: 2px 5px;" onchange="updateStatus(this, <?php echo $row['id']; ?>)">
                                <option disabled>Update Status</option>
                                <?php
                                $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                                foreach($statuses as $status) {
                                    $selected = (strtolower($row['status']) == $status) ? 'selected' : '';
                                    echo "<option value=\"$status\" $selected>" . ucfirst($status) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                    <td>
                        <select style="padding: 2px 5px;" onchange="showTrackingModal(this.value, <?php echo $row['id']; ?>)">
                            <option disabled selected>Select</option>
                            <?php
                            $delivery_methods = ['Track On', 'DTDC', 'Bluedart', 'Delhivery', 'India Post'];
                            foreach($delivery_methods as $method) {
                                $selected = ($row['delivered_by'] == $method) ? 'selected' : '';
                                echo "<option value=\"$method\" $selected>$method</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <a href="order_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="10" class="text-center">No orders found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Modal for Tracking Number -->
<div id="trackingModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:1000;">
    <div style="background:#fff; padding:20px; border-radius:8px; width:300px; text-align:center; position:relative;">
        <h3>Enter Tracking ID</h3>
        <input type="text" id="trackingInput" placeholder="Tracking ID" style="width:100%; padding:8px; margin:10px 0;">
        <input type="hidden" id="orderIdInput">
        <input type="hidden" id="deliveryMethodInput">
        <button onclick="submitTracking()" style="padding:6px 12px;">Submit</button>
        <button onclick="closeModal()" style="padding:6px 12px; margin-left:10px;">Cancel</button>
    </div>
</div>

<script>
function showTrackingModal(deliveryMethod, orderId) {
    document.getElementById('trackingModal').style.display = 'flex';
    document.getElementById('orderIdInput').value = orderId;
    document.getElementById('deliveryMethodInput').value = deliveryMethod;
}

function closeModal() {
    document.getElementById('trackingModal').style.display = 'none';
    document.getElementById('trackingInput').value = '';
}

function submitTracking() {
    const trackingNumber = document.getElementById('trackingInput').value.trim();
    const orderId = document.getElementById('orderIdInput').value;
    const deliveryMethod = document.getElementById('deliveryMethodInput').value;

    if (trackingNumber === '') {
        alert('Please enter tracking number');
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_tracking.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (this.status == 200) {
            alert("Tracking info updated!");
            closeModal();
            location.reload();
        } else {
            alert("Error updating tracking info.");
        }
    };
    xhr.send("order_id=" + orderId + "&tracking_number=" + encodeURIComponent(trackingNumber) + "&delivered_by=" + encodeURIComponent(deliveryMethod));
}
</script>

                        </table>
                        <div class="no-results" id="noResults">No orders match your search criteria.</div>
                    </div>
                    <div class="view-all">
                        <a href="orders.php">View All Orders <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Search function
        function searchOrders() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('ordersTable');
            const tr = table.getElementsByTagName('tr');
            const noResults = document.getElementById('noResults');
            let hasResults = false;

            // Skip header row (index 0)
            for (let i = 1; i < tr.length; i++) {
                const tdName = tr[i].getElementsByTagName('td')[2]; // Customer name column
                const tdContact = tr[i].getElementsByTagName('td')[3]; // Contact column
                
                if (tdName && tdContact) {
                    const txtValueName = tdName.textContent || tdName.innerText;
                    const txtValueContact = tdContact.textContent || tdContact.innerText;
                    
                    if (txtValueName.toUpperCase().indexOf(filter) > -1 || 
                        txtValueContact.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        hasResults = true;
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
            
            // Show no results message if no matches
            noResults.style.display = hasResults ? "none" : "block";
        }

        // Filter function
        function filterOrders() {
            const statusFilter = document.getElementById('statusFilter').value;
            const deliveryFilter = document.getElementById('deliveryFilter').value;
            const table = document.getElementById('ordersTable');
            const tr = table.getElementsByTagName('tr');
            const noResults = document.getElementById('noResults');
            let hasResults = false;

            // Skip header row (index 0)
            for (let i = 1; i < tr.length; i++) {
                const tdStatus = tr[i].getElementsByTagName('td')[6]; // Status column
                const tdDelivery = tr[i].getElementsByTagName('td')[8]; // Delivery method column
                
                if (tdStatus && tdDelivery) {
                    const statusText = tdStatus.textContent || tdStatus.innerText;
                    const deliverySelect = tdDelivery.getElementsByTagName('select')[0];
                    const selectedDelivery = deliverySelect ? deliverySelect.value : '';
                    
                    const statusMatch = statusFilter === "" || 
                                      statusText.toLowerCase().includes(statusFilter.toLowerCase());
                    const deliveryMatch = deliveryFilter === "" || 
                                        selectedDelivery === deliveryFilter;
                    
                    if (statusMatch && deliveryMatch) {
                        tr[i].style.display = "";
                        hasResults = true;
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
            
            // Show no results message if no matches
            noResults.style.display = hasResults ? "none" : "block";
        }

        // Update status function (simulated)
        // Update status function with server communication
        function updateStatus(selectElement, orderId) {
            const newStatus = selectElement.value;
            const statusSpan = selectElement.closest('tr').querySelector('.status');
            
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (this.status == 200) {
                    // Update the status class and text
                    statusSpan.className = 'status ' + newStatus;
                    statusSpan.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    alert("Order status updated successfully!");
                } else {
                    alert("Error updating order status.");
                    selectElement.value = statusSpan.textContent.toLowerCase(); // Reset to original value
                }
            };
            xhr.send("order_id=" + orderId + "&status=" + encodeURIComponent(newStatus));
        }
        // Update delivery method function (simulated)
        function updateDeliveryMethod(selectElement, orderId) {
            const newMethod = selectElement.value;
            
            // In a real application, you would send this to your server
            console.log(`Order ${orderId} delivery method updated to: ${newMethod}`);
            
            // Show a success message (you can replace this with your preferred notification system)
            alert(`Order #${orderId} delivery method updated to ${newMethod}!`);
        }

        // Delete order function (simulated)
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.closest('tr').querySelector('td').textContent.substring(1);
                if (confirm(`Are you sure you want to delete order #${orderId}?`)) {
                    // In a real application, you would send this to your server
                    console.log(`Order ${orderId} deleted`);
                    this.closest('tr').remove();
                    
                    // Check if any orders are left
                    const table = document.getElementById('ordersTable');
                    if (table.getElementsByTagName('tr').length <= 1) {
                        document.getElementById('noResults').style.display = 'block';
                    }
                    
                    alert(`Order #${orderId} has been deleted!`);
                }
            });
        });
    </script>
</body>

</html>
