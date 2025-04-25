<?php
session_start();
ob_start();
include("includes/conn.php");
include("includes/header.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user'];
?>

<h2 style="text-align: center;">Your Cart</h2>

<div class="cart-wrapper">
    <div class="cart-left">
        <div class="cart-header">
            <h2>Your Shopping Cart</h2>
        </div>
        <!-- php code here for apply coupon-->
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['cpn_button'])) {
                $coupon_name = $_POST['coupon_name'];
                $sqlFetchCpn = "SELECT * FROM coupons WHERE code='$coupon_name'";
                $dataCpn = mysqli_query($conn, $sqlFetchCpn);
                if (mysqli_num_rows($dataCpn) > 0) {
                    $resCpn = mysqli_fetch_assoc($dataCpn);
                    $discount = isset($resCpn['discount']) ? $resCpn['discount'] : '';
                    echo "
                          <script>
       document.getElementById('coupon_name').readonly = true ;
            
        </script>
                    ";
                } else {
                    echo "
                        <script>
                            alert('Invalid coupon');
                        </script>
                    ";
                }
            }
        }
        ?>
        <form action="update_cart.php" method="post">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $subtotal = 0;
                    $delivery = 0;
                    $total = 0;
                    $total_items = 0;
                    $has_out_of_stock = false;
                    $delivery_charges = []; // To store individual delivery charges

                    $sql = "SELECT c.*, CAST(p.delivery AS DECIMAL(10,2)) as product_delivery FROM cart c 
                            JOIN products p ON c.product_id = p.id 
                            WHERE c.user_id = '$user_id'";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $itemTotal = $row['total'];
                            $subtotal += $itemTotal;
                            $total_items += $row['quantity'];

                            // Add product's delivery charge to the array
                            $delivery_charges[] = $row['product_delivery'];

                            // Check stock from products table
                            $product_id = $row['product_id'];
                            $stock_check = mysqli_query($conn, "SELECT stock FROM products WHERE id = '$product_id'");
                            $product = mysqli_fetch_assoc($stock_check);
                            if ($product && isset($product['stock'])) {
                                $stock_status = ($product['stock'] > 0) ? "In Stock" : "Out of Stock";
                                if ($product['stock'] <= 0) {
                                    $has_out_of_stock = true;
                                }
                            } else {
                                $stock_status = "Product Not Found";
                                $has_out_of_stock = true;
                            }

                            echo '
                            <tr>
                                <td data-label="Product">
                                    <div class="product-info">
                                        <img src="./admin/uploads/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['product_name']) . '">
                                        <div>
                                            <p>' . htmlspecialchars($row['product_name']) . '</p>
                                            <strong style="color:' . ($stock_status === "In Stock" ? "green" : "red") . ';">' . $stock_status . '</strong>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Price">₹' . number_format($row['sell_price'], 2) . '</td>
                                <td data-label="Quantity">
                                    <input type="number" name="quantity[' . $row['id'] . ']" value="' . $row['quantity'] . '" min="1">
                                </td>
                                <td data-label="Total">₹' . number_format($itemTotal, 2) . '</td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <button type="button" class="remove-btn" onclick="if(confirm(\'Remove this item from cart?\')) window.location.href=\'remove_from_cart.php?id=' . $row['id'] . '\'">
                                            🗑️
                                        </button>
                                        <a href="buy.php?table=products&product_id=' . $row['product_id'] . '" class="btn btn-primary" style="padding: 5px 10px; font-size: 14px;">
                                            Order Now
                                        </a>
                                    </div>
                                </td>
                            </tr>';
                        }

                        // Calculate total delivery charge (maximum of all individual delivery charges)
                        $delivery = !empty($delivery_charges) ? max($delivery_charges) : 0;
                    } else {
                        echo '<tr><td colspan="5">
                            <div class="empty-cart">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p>Your cart is empty</p>
                                <a href="index.php" class="btn btn-outline">Continue Shopping</a>
                            </div>
                        </td></tr>';
                        $delivery = 0;
                    }
                    $discount = isset($discount) ? $discount : 0;
                    $discount = ($discount / 100) * $subtotal;
                    $total = $subtotal + $delivery - $discount
                    ?>
                </tbody>
            </table>


            <?php if (mysqli_num_rows($result) > 0 && !$has_out_of_stock): ?>
                <div class="cart-actions">
                    <button type="submit" name="update_cart" class="btn-outline">Update Cart</button>
                    <a href="checkout.php?price=<?= $total ?>&delivery_charge=<?= $delivery ?>" class="btn btn-primary" id="checkoutBtn">Proceed to Checkout</a>
                </div>
            <?php elseif ($has_out_of_stock): ?>
                <div class="cart-actions">
                    <button type="submit" name="update_cart" class="btn-outline">Update Cart</button>
                    <button class="btn btn-disabled" disabled>Cannot Checkout (Out of Stock Items)</button>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <style>
        .coupon-input {
            padding: 5px;
            width: 60%;
            margin-top: 5px;
        }

        .apply-btn {
            padding: 10px 20px;
            margin-left: 5px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        .apply-btn:hover {
            background-color: #218838;
        }
    </style>

    <div class="cart-summary">
        <h3>Order Summary</h3>


        <!-- Coupon Input Section -->
        <div class="summary-row">
            <!-- <span class="summary-label">Coupon Code:</span> -->
            <form action="" method="post">
                <input type="text" name="coupon_name" id="coupon_code" placeholder="Enter coupon code" class="coupon-input" />
                <input type="submit" id="cpn_button" name="cpn_button" value="Apply" class="apply-btn">
            </form>
        </div>


        <div class="summary-row">
            <span class="summary-label">Total Items:</span>
            <span class="summary-value"><?php echo $total_items; ?></span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Subtotal:</span>
            <span class="summary-value">₹<?php echo number_format($subtotal, 2); ?></span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Delivery Charge:</span>
            <span class="summary-value">₹<?php echo number_format($delivery, 2); ?></span>
        </div>
        <div class="divider"></div>
        <div class="summary-row summary-total">
            <span>Total:</span>
            <span id="deliver">₹<?php echo number_format($total, 2); ?></span>
        </div>

        <a href="index.php" class="continue-link">← Continue Shopping</a>
    </div>

</div>

<script>
    const hasOutOfStock = <?php echo json_encode($has_out_of_stock); ?>;
    const checkoutBtn = document.getElementById("checkoutBtn");

    if (checkoutBtn) {
        checkoutBtn.addEventListener("click", function(e) {
            if (hasOutOfStock) {
                e.preventDefault();
                alert("One or more items in your cart are out of stock. Please remove or update them to proceed.");
            } else {
                window.location.href = "checkout.php";
            }
        });
    }
</script>

<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --danger-color: #f72585;
        --success-color: #4cc9f0;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --gray-color: #6c757d;
        --border-radius: 8px;
        --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: #f5f7ff;
        color: var(--dark-color);
        line-height: 1.6;
    }

    .cart-wrapper {
        display: flex;
        flex-direction: column;
        max-width: 1200px;
        margin: 40px auto;
        gap: 30px;
        padding: 0 20px;
    }

    .cart-left,
    .cart-summary {
        background: #ffffff;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        border: 1px solid rgba(0, 0, 0, 0.05);
        padding: 30px;
    }

    .cart-header {
        text-align: center;
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 15px;
    }

    .cart-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 25%;
        width: 50%;
        height: 3px;
        background: linear-gradient(to right, var(--primary-color), var(--accent-color));
        border-radius: 3px;
    }

    .cart-header h2 {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 10px;
    }

    .cart-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 15px;
    }

    .cart-table th {
        background-color: var(--primary-color);
        color: white;
        font-weight: 500;
        padding: 15px;
        text-align: left;
    }

    .cart-table th:first-child {
        border-top-left-radius: var(--border-radius);
    }

    .cart-table th:last-child {
        border-top-right-radius: var(--border-radius);
    }

    .cart-table td {
        padding: 15px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        vertical-align: middle;
    }

    .cart-table tr:last-child td {
        border-bottom: none;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .product-info img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: var(--border-radius);
        border: 1px solid rgba(0, 0, 0, 0.1);
        transition: var(--transition);
    }

    .product-info img:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .product-info p {
        margin: 0;
        font-size: 16px;
        font-weight: 500;
        color: var(--dark-color);
    }

    .product-info small {
        font-size: 14px;
        color: var(--gray-color);
        display: block;
        margin-top: 5px;
    }

    input[type="number"] {
        width: 70px;
        padding: 10px;
        font-size: 15px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: var(--border-radius);
        text-align: center;
        transition: var(--transition);
    }

    input[type="number"]:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 2px rgba(72, 149, 239, 0.2);
    }

    .remove-btn {
        background-color: transparent;
        border: none;
        color: var(--danger-color);
        font-size: 18px;
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }

    .remove-btn:hover {
        background-color: rgba(247, 37, 133, 0.1);
        transform: scale(1.1);
    }

    .cart-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 30px;
    }

    .coupon-input {
        flex: 1;
        min-width: 200px;
        padding: 12px 15px;
        font-size: 15px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: var(--border-radius);
        transition: var(--transition);
    }

    .coupon-input:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 2px rgba(72, 149, 239, 0.2);
    }

    .btn {
        padding: 12px 25px;
        border: none;
        cursor: pointer;
        border-radius: var(--border-radius);
        font-weight: 500;
        font-size: 15px;
        transition: var(--transition);
        text-align: center;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
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

    .cart-summary h3 {
        font-size: 22px;
        margin: 0 0 20px;
        color: var(--dark-color);
        font-weight: 700;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .summary-row:last-child {
        margin-bottom: 0;
    }

    .summary-label {
        color: var(--gray-color);
    }

    .summary-value {
        font-weight: 500;
    }

    .summary-total {
        font-size: 18px;
        font-weight: 700;
        color: var(--dark-color);
        margin: 20px 0;
    }

    .divider {
        border: none;
        height: 1px;
        background-color: rgba(0, 0, 0, 0.05);
        margin: 20px 0;
    }

    .checkout-btn {
        width: 100%;
        padding: 15px;
        background-color: var(--success-color);
        color: white;
        font-weight: 600;
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: var(--transition);
        font-size: 16px;
    }

    .checkout-btn:hover {
        background-color: #3ab7d8;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(76, 201, 240, 0.2);
    }

    .checkout-btn:disabled {
        background-color: var(--gray-color);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .continue-link {
        display: inline-block;
        margin-top: 20px;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
    }

    .continue-link:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }

    .empty-cart {
        text-align: center;
        padding: 40px 0;
    }

    .empty-cart svg {
        width: 80px;
        height: 80px;
        margin-bottom: 20px;
        color: var(--gray-color);
    }

    .empty-cart p {
        font-size: 18px;
        color: var(--gray-color);
        margin-bottom: 20px;
    }

    /* Responsive styles */
    @media (min-width: 992px) {
        .cart-wrapper {
            flex-direction: row;
        }

        .cart-left {
            flex: 3;
        }

        .cart-summary {
            flex: 1;
            position: sticky;
            top: 20px;
            height: fit-content;
        }
    }

    @media (max-width: 768px) {
        .cart-table thead {
            display: none;
        }

        .cart-table tr {
            display: block;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: var(--border-radius);
            padding: 15px;
        }

        .cart-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .cart-table td:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .cart-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--dark-color);
            margin-right: 15px;
        }

        .product-info {
            width: 100%;
        }
    }

    @media (max-width: 480px) {

        .cart-left,
        .cart-summary {
            padding: 20px;
        }

        .cart-header h2 {
            font-size: 24px;
        }

        .product-info img {
            width: 60px;
            height: 60px;
        }

        .cart-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<?php include("includes/footer.php"); ?>