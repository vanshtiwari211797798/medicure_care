<?php
include 'includes/conn.php';
session_start();

if (isset($_GET['order_id'])) {
    $orderId = intval($_GET['order_id']);

    // Fetch main order details
    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();

        // Fetch offer products if this is a main order
        if ($order['is_offer_product'] == 0) {
            $offer_sql = "SELECT * FROM orders WHERE main_order_id = ?";
            $offer_stmt = $conn->prepare($offer_sql);
            $offer_stmt->bind_param('i', $orderId);
            $offer_stmt->execute();
            $offer_result = $offer_stmt->get_result();
            $offer_products = $offer_result->fetch_all(MYSQLI_ASSOC);
        } else {
            // If this is an offer product, fetch its main order
            $main_sql = "SELECT * FROM orders WHERE id = ?";
            $main_stmt = $conn->prepare($main_sql);
            $main_stmt->bind_param('i', $order['main_order_id']);
            $main_stmt->execute();
            $main_result = $main_stmt->get_result();
            $main_order = $main_result->fetch_assoc();
        }
    } else {
        die("Order not found.");
    }
} else {
    die("Invalid order ID.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $order['id'] ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        #logo {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
            border-radius: 50%;
        }

        #logo img {
            max-width: 150px;
            height: auto;
            object-fit: contain;
            border-radius: 50%;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            #logo img {
                max-width: 120px;
            }
            
            .invoice-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
            
            .company-info {
                text-align: left;
                order: -1;
            }
            
            .card-header h2 {
                font-size: 1.5rem;
            }
            
            .card-header h4 {
                font-size: 1.2rem;
            }
            
            .row.mb-4 {
                flex-direction: column;
                gap: 20px;
            }
            
            .col-md-6 {
                width: 100%;
            }
            
            .product-table th, 
            .product-table td {
                padding: 8px 4px;
                font-size: 0.85rem;
            }
            
            .product-table th:nth-child(2),
            .product-table td:nth-child(2) {
                display: none;
            }
            
            .free-badge {
                font-size: 0.7em;
                padding: 1px 4px;
            }
            
            .card-footer .btn {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .card-footer .btn:last-child {
                margin-bottom: 0;
            }
        }

        @media (max-width: 414px) {
            #logo img {
                max-width: 100px;
            }
            
            .card-header p,
            .company-info p {
                font-size: 0.85rem;
                margin-bottom: 0.3rem;
            }
            
            .product-table {
                font-size: 0.8rem;
            }
            
            .product-table th, 
            .product-table td {
                padding: 6px 3px;
            }
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background-color: white;
                font-size: 12pt;
            }

            .card {
                border: none;
            }
            
            /* Ensure table columns are visible when printing */
            .product-table th:nth-child(2),
            .product-table td:nth-child(2) {
                display: table-cell !important;
            }
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .company-info {
            text-align: right;
        }

        .product-table th {
            background-color: #f8f9fa;
            white-space: nowrap;
        }
        
        .product-table {
            width: 100%;
            overflow-x: auto;
        }

        .offer-product {
            background-color: #f8f9fa;
        }

        .free-badge {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8em;
            white-space: nowrap;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        /* Ensure table doesn't overflow on small screens */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>

<body>
    <div class="container mt-3 mt-md-5 mb-3 mb-md-5">
        <div class="card" id="invoice">
            <div class="card-header">
                <div class="invoice-header">
                    <div>
                        <h2>Invoice</h2>
                        <p class="mb-0">Order #<?= $order['id'] ?></p>
                        <p class="mb-0">Date: <?= date('F j, Y', strtotime($order['order_date'])) ?></p>
                    </div>
                    <!-- <div id="logo">
                        <img src="./images/logo.jpg" alt="">
                    </div> -->
                    <div class="company-info">
                        <h4>MediCureCare</h4>
                        <p class="mb-0">Flat No.: 1583</p>
                        <p class="mb-0">District: Gorakhpur</p>
                        <p class="mb-0">State: Uttar Pradesh</p>
                        <p class="mb-0">PIN Code: 273006</p>
                        <p class="mb-0">Road: ADITYAPURI COLONY , Gorakhpur</p>
                        <p class="mb-0">medicurehealthcare1421@gmail.com / 8808888589</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Customer Information</h5>
                        <p><strong>Name:</strong> <?= htmlspecialchars($order['name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Shipping Information</h5>
                        <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                        <p><strong>Pincode:</strong> <?= htmlspecialchars($order['pincode']) ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-<?=
                            $order['status'] == 'delivered' ? 'success' :
                            ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                                <?= ucfirst($order['status']) ?>
                            </span></p>
                    </div>
                </div>

                <h5>Order Summary</h5>
                <div class="table-responsive">
                    <table class="table product-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Main Product -->
                            <tr>
                                <td>
                                    <?= htmlspecialchars($order['product_name']) ?>
                                    <?php if ($order['is_offer_product'] == 1): ?>
                                        <span class="free-badge">FREE OFFER</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($order['product_id']) ?></td>
                                <td><?= $order['is_offer_product'] == 1 ? '<span class="free-badge">FREE</span>' : '₹' . number_format($order['price'], 2) ?>
                                </td>
                                <td><?= $order['quantity'] ?></td>
                                <td><?= $order['is_offer_product'] == 1 ? '<span class="free-badge">FREE</span>' : '₹' . number_format($order['price'] * $order['quantity'], 2) ?>
                                </td>
                            </tr>

                            <!-- Offer Products -->
                            <?php if (isset($offer_products) && !empty($offer_products)): ?>
                                <?php foreach ($offer_products as $offer): ?>
                                    <tr class="offer-product">
                                        <td>
                                            <?= htmlspecialchars($offer['product_name']) ?>
                                            <span class="free-badge">FREE OFFER</span>
                                        </td>
                                        <td><?= htmlspecialchars($offer['product_id']) ?></td>
                                        <td><span class="free-badge">FREE</span></td>
                                        <td><?= $offer['quantity'] ?></td>
                                        <td><span class="free-badge">FREE</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- If this is an offer product, show its main product -->
                            <?php if (isset($main_order)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($main_order['product_name']) ?></td>
                                    <td><?= htmlspecialchars($main_order['product_id']) ?></td>
                                    <td>₹<?= number_format($main_order['price'], 2) ?></td>
                                    <td><?= $main_order['quantity'] ?></td>
                                    <td>₹<?= number_format($main_order['price'] * $main_order['quantity'], 2) ?></td>
                                </tr>
                            <?php endif; ?>

                            <!-- Delivery Charge -->
                            <tr>
                                <td colspan="4" class="text-end">Delivery Charge:</td>
                                <td>₹<?= number_format($order['delivery_charge'], 2) ?></td>
                            </tr>

                            <!-- Total -->
                            <tr class="total-row">
                                <td colspan="4" class="text-end">Total Amount:</td>
                                <td>₹<?= number_format($order['price'] * $order['quantity'] + $order['delivery_charge'], 2) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="payment-info mt-4">
                    <h5>Payment Information</h5>
                    <p><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?></p>
                    <p><strong>Payment Status:</strong> <span class="badge bg-<?=
                        $order['payment_status'] == 'Success' ? 'success' : 'warning' ?>">
                            <?= ucfirst($order['payment_status']) ?>
                        </span></p>
                    <?php if (!empty($order['payment_id'])): ?>
                        <p><strong>Payment ID:</strong> <?= htmlspecialchars($order['payment_id']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-footer text-center no-print">
                <button class="btn btn-success me-2" onclick="window.print()">Print Invoice</button>
                <!-- <button class="btn btn-success" onclick="downloadPDF()">Download as PDF</button> -->
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.getElementById("invoice");
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'invoice_<?= $order['id'] ?>.pdf',
                image: { type: 'jpeg', quality: 1 },
                html2canvas: { scale: 2, useCORS: true, logging: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait', compress: true }
            };

            html2pdf().set(opt).from(element).save().catch(err => console.error('PDF generation failed:', err));
        }
    </script>
</body>

</html>