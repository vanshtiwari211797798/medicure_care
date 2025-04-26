<?php
ob_start();
session_start();
include("includes/conn.php");
include("includes/header.php");
if (!isset($_SESSION['user'])) {
    header('Location:login.php');
}
$id = $_GET['product_id'] ?? 0;
$table = $_GET['table'] ?? 'products';

// Sanitize table name
$allowed_tables = ['products', 'latestproducts', 'dealsoftheday'];
if (!in_array($table, $allowed_tables)) {
    die("Invalid table source");
}

// Fetch the correct product from the specified table
$res = mysqli_query($conn, "SELECT * FROM `$table` WHERE id=$id");
if ($row = mysqli_fetch_assoc($res)) {
    $row['source_table'] = $table; // Store source for form use

    // Fetch special offer product(s) linked to this product
    $offer_products = [];
    $offers_query = mysqli_query($conn, "SELECT * FROM product_offers WHERE product_id = $id");
    if ($offers_query && mysqli_num_rows($offers_query) > 0) {
        while ($offer = mysqli_fetch_assoc($offers_query)) {
            $offer_products[] = $offer;
        }
    }

} else {
    die("Product not found.");
}
?>

<div class="buy-now-container">
    <div class="product-preview">
        <div id="left">
            <div class="product-image-container">
                <img id="pro_img" src="admin/uploads/<?= $row['image'] ?? 'default.jpg' ?>"
                    alt="<?= $row['product_name'] ?? $row['name'] ?>">

                <?php if (isset($row['original_price']) && $row['original_price'] > $row['sale_price']): ?>
                    <div class="product-badge">
                        <?= round(($row['original_price'] - $row['sale_price']) / $row['original_price'] * 100) ?>% OFF
                    </div>
                <?php endif; ?>

                <!-- Thumbnails -->
                <div class="thumbnail-gallery">
                    
                    <?php if (!empty($row['sub_image1'])): ?>
                        <img class="thumbnail" src="admin/uploads/<?= $row['sub_image1'] ?>" alt="Sub Image 1"
                            onclick="changeMainImage(this)">
                    <?php endif; ?>
                    <?php if (!empty($row['sub_image2'])): ?>
                        <img class="thumbnail" src="admin/uploads/<?= $row['sub_image2'] ?>" alt="Sub Image 2"
                            onclick="changeMainImage(this)">
                    <?php endif; ?>
                    <?php if (!empty($row['sub_image3'])): ?>
                        <img class="thumbnail" src="admin/uploads/<?= $row['sub_image3'] ?>" alt="Sub Image 3"
                            onclick="changeMainImage(this)">
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <?php
                // Fetch product offers that match the current product ID
                $offers_query = mysqli_query($conn, "SELECT * FROM product_offers WHERE product_id = $id");
                if (mysqli_num_rows($offers_query) > 0): ?>
                    <div id="spe-offer">
                        <div class="product-offers">
                            <h3>Free Product</h3>
                            <div class="offers-list">
                                <?php while ($offer = mysqli_fetch_assoc($offers_query)): ?>
                                    <div class="offer-item">
                                        <img src="./admin/uploads/<?= $offer['offer_product_image'] ?>"
                                            alt="<?= $offer['offer_product_name'] ?>" class="offer-image">
                                        <div class="offer-details">
                                            <h4><?= $offer['offer_product_name'] ?></h4>
                                            <p>MRP: ₹<?= number_format($offer['offer_product_mrp'], 2) ?></p>
                                            <p>Quantity: <?= $offer['offer_product_qty'] ?></p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
            <div class="delivery-info">
                <div class="delivery-item">
                    <span class="delivery-icon">✓</span>
                    <span>Expected delivery in 3-5 business days</span>
                </div>
                <div class="delivery-item">
                    <span class="delivery-icon">✓</span>
                    <span>Easy 7-day returns</span>
                </div>
            </div>
        </div>

        <div id="right">
            <div class="product-info">
                <h2><?= $row['product_name'] ?? $row['name'] ?? 'Unnamed Product' ?></h2>
                <div class="price-section">
                    <span
                        class="current-price">₹<?= number_format($row['sale_price'] ?? $row['price'] ?? 0, 2) ?></span>
                    <?php if (isset($row['price']) && $row['price'] > $row['sale_price']): ?>
                        <span class="original-price">₹<?= number_format($row['price'], 2) ?></span>
                    <?php endif; ?>
                </div>
                <p class="product-description"><?= $row['description'] ?? 'No description available.' ?></p>
            </div>
        </div>
    </div>

    <form method="POST" action="place_single_order.php" id="orderForm">
        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
        <input type="hidden" name="source_table" value="<?= $row['source_table'] ?>">
        <input type="hidden" id="base_price" value="<?= $row['sale_price'] ?? $row['price'] ?? 0 ?>">
        <input type="hidden" id="delivery_charge" value="<?= $row['delivery'] ?? 0 ?>">
        <input type="hidden" name="total_amount" id="total_amount" value="">

        <?php foreach ($offer_products as $index => $offer): ?>
            <input type="hidden" name="offer_products[<?= $index ?>][id]" value="<?= $offer['id'] ?>">
            <input type="hidden" name="offer_products[<?= $index ?>][name]" value="<?= $offer['offer_product_name'] ?>">
            <input type="hidden" name="offer_products[<?= $index ?>][price]" value="<?= $offer['offer_product_mrp'] ?>">
            <input type="hidden" name="offer_products[<?= $index ?>][image]" value="<?= $offer['offer_product_image'] ?>">
            <input type="hidden" name="offer_products[<?= $index ?>][quantity]" value="<?= $offer['offer_product_qty'] ?>">
        <?php endforeach; ?>

        <div class="form-section">
            <h3 class="section-title">Shipping Information</h3>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter your full name">
                <div class="error-message" id="name-error">Please enter a valid name</div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" value="<?= $_SESSION['user'] ?>" name="email" required
                    placeholder="Enter your email" readonly>
                <div class="error-message" id="email-error">Please enter a valid email</div>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required placeholder="Enter 10-digit mobile number"
                    pattern="\d{10}" minlength="10" maxlength="10"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10);">
                <div class="error-message" id="phone-error">Please enter a valid 10-digit number</div>
            </div>

            <div class="form-group">
                <label for="address">Shipping Address</label>
                <textarea id="address" name="address" required rows="4"
                    placeholder="Enter complete address with pincode">
                </textarea>
                <div class="error-message" id="address-error">Address must be at least 10 characters</div>
            </div>

            <div class="form-group">
                <label for="pincode">Pincode</label>
                <input type="number" id="pincode" name="pincode" required placeholder="Enter 6-digit pincode"
                    minlength="6" maxlength="6" oninput="if(this.value.length > 6) this.value = this.value.slice(0,6);">
                <div class="error-message" id="pincode-error">Please enter a valid 6-digit pincode</div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Order Summary</h3>

            <div class="form-group">
                <label>Quantity</label>
                <div class="quantity-selector">
                    <button type="button" class="quantity-btn" id="decrease-qty">-</button>
                    <input type="number" class="quantity-input" name="quantity" id="quantity" min="1" max="10" value="1"
                        required>
                    <button type="button" class="quantity-btn" id="increase-qty">+</button>
                </div>
            </div>

            <div class="price-breakdown">
                <div class="price-row">
                    <span class="price-label">Product Price</span>
                    <span class="price-value"
                        id="item-price">₹<?= number_format($row['sale_price'] ?? $row['price'] ?? 0, 2) ?></span>
                </div>
                <div class="price-row">
                    <span class="price-label">Quantity</span>
                    <span class="price-value" id="quantity-display">1</span>
                </div>
                <div class="price-row">
                    <span class="price-label">Subtotal</span>
                    <span class="price-value"
                        id="subtotal">₹<?= number_format($row['sale_price'] ?? $row['price'] ?? 0, 2) ?></span>
                </div>

                <?php
                $offers_query = mysqli_query($conn, "SELECT * FROM product_offers WHERE product_id = $id");
                if (mysqli_num_rows($offers_query) > 0):
                    while ($offer = mysqli_fetch_assoc($offers_query)): ?>
                        <div class="price-row">
                            <span class="price-label">free Product: <?= $offer['offer_product_name'] ?></span>
                            <span class="price-value">₹<?= number_format($offer['offer_product_mrp'], 2) ?></span>
                        </div>
                    <?php endwhile;
                endif; ?>

                <div class="price-row">
                    <span class="price-label">Delivery Charge</span>
                    <span class="price-value"
                        id="delivery-charge">₹<?= number_format(floatval($row['delivery'] ?? 0), 2) ?></span>
                </div>

                <div class="price-row">
                    <span class="price-label">GST (<?= $row['gst'] ?>%) INCLUDED</span>
                    <span class="price-value"></span>
                </div>

                <div class="price-row total-row">
                    <span class="price-label">Total Amount</span>
                    <span class="price-value"
                        id="total-amount">₹<?= number_format((float)($row['sale_price'] ?? $row['price'] ?? 0) + (float)($row['delivery'] ?? 0), 2) ?></span>
                </div>

                <div class="price-row">
                    <!-- <span class="price-label">Apply Coupon</span> -->
                    <div class="coupon-input">
                        <input type="hidden" name="coupon_code_input" id="coupon_code" placeholder="Enter coupon code">
                        <button type="button" style="visibility: hidden;" id="apply_coupon" class="copy-coupon">Apply</button>
                    </div>
                    <div id="coupon_message" style="margin-top: 5px;"></div>
                </div>

                <!-- Hidden Fields -->
                <input type="hidden" id="discount_amount" value="0">
                <input type="hidden" id="coupon_id" name="coupon_id" value="">

                <!-- Available Coupons -->
                <div class="available-coupons">
                    <h4>Available Coupons</h4>
                    <?php
                    $coupon_query = mysqli_query($conn, "SELECT id, code, discount, expiry_date, is_active 
                                        FROM coupons 
                                        WHERE is_active = 1 
                                        AND expiry_date >= CURDATE() AND type='regular'");
                    if ($coupon_query && mysqli_num_rows($coupon_query) > 0):
                        while ($coupon = mysqli_fetch_assoc($coupon_query)):
                            ?>
                            <div class="coupon-item">
                                <div>
                                    <span>Code: <?= htmlspecialchars($coupon['code']) ?></span><br>
                                    <span>Discount: <?= htmlspecialchars($coupon['discount']) ?>%</span><br>
                                    <!-- <span>Expires: <?= htmlspecialchars($coupon['expiry_date']) ?></span> -->
                                </div>
                                <button type="button" class="copy-coupon"
                                    onclick="copyCoupon('<?= htmlspecialchars($coupon['code']) ?>')">
                                    Copy Code
                                </button>
                            </div>
                            <?php
                        endwhile;
                    else:
                        echo "<p>No active coupons available</p>";
                    endif;
                    ?>
                </div>
            </div>
        </div>
        <div class="secure-checkout">
            <span class="secure-icon">🔒</span>
            <span>256-bit SSL Secured Checkout • Your information is protected</span>
        </div>

        <button id="pay_button" type="button">
            <span>PROCEED TO PAYMENT</span>
            <span
                id="pay-amount">₹<?= number_format((float)($row['sale_price'] ?? $row['price'] ?? 0) + (float)($row['delivery'] ?? 0), 2) ?></span>
        </button>
    </form>

    <!-- Payment Options Popup -->
    <div class="payment-popup-overlay" id="paymentPopup">
        <div class="payment-popup-container">
            <div class="payment-popup-header">
                <h3>Choose Payment Method</h3>
                <button class="close-popup" id="closePopup">&times;</button>
            </div>
            <div class="payment-options">
                <div class="payment-option" id="razorpayOption">
                    <input type="radio" name="paymentMethod" id="razorpayMethod" value="razorpay" checked>
                    <label for="razorpayMethod">
                        <img src="images/razorpay-icon.png" alt="Razorpay">
                        <span>Pay Online (Credit/Debit Card, UPI, Net Banking)</span>
                    </label>
                </div>
                <div class="payment-option" id="codOption">
                    <input type="radio" name="paymentMethod" id="codMethod" value="cod">
                    <label for="codMethod">
                        <img src="images/cod-icon.png" alt="Cash on Delivery">
                        <span>Cash on Delivery (Pay when you receive)</span>
                    </label>
                </div>
            </div>
            <button class="confirm-payment-btn" id="confirmPayment">Confirm Payment Method</button>
        </div>
    </div>
</div>
<section id="featured-products">
    <div class="container_featured-products">
        <h2>Similor Products</h2>

        <div class="product-grid">
            <?php
           $name = isset($_GET['name']) ? $_GET['name'] : '';
           $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : '';
            $result = $conn->query("SELECT * FROM products WHERE product_name = '$name' AND id !='$product_id' ORDER BY id DESC LIMIT 8");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $product_id = $row['id'];
                    $product_name = $row['product_name'];
                    $desc = $row['description'];
                    $image = "admin/uploads/" . $row['image'];
                    $net_price = number_format($row['price'], 2);
                    $selling_price = number_format($row['sale_price'], 2);
                    $discount = $row['discount'];
                    $stock = $row['stock'];
                    ?>
                    <div class="product-card" onclick="window.location.href='product-detail.php?product_id=<?= $row['id'] ?>&name=<?=$product_name?>'"
                        style="cursor: pointer;">
                        <span class='discount-badge'><?= $discount ?>% OFF</span>
                        <img src='<?= $image ?>' alt='<?= $product_name ?>'>
                        <h3><?= $product_name ?></h3>
                        <p><?= $desc ?></p>
                        <p class='price'>
                            <span class='old-price'>₹<?= $net_price ?></span>
                            ₹<?= $selling_price ?>
                        </p>
                        <p><strong>Stock:</strong> <?= ($stock > 0 ? 'In Stock' : 'Out of Stock') ?></p>
                        <div class='btn-group'>
                            <a href="add_to_cart.php?table=products&id=<?= $product_id ?>" class="btn add-to-cart">Add to
                                Cart</a>
                            <a href="buy.php?table=products&product_id=<?= $product_id ?>&name=<?=$product_name?>" class="btn buy-now">Buy Now</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No Similors Products found.</p>";
            }
            ?>
        </div>
    </div>
</section>

<script>
    // copyCoupon
    function copyCoupon(code) {
        navigator.clipboard.writeText(code)
            .then(() => {
                // Optional: Show a tooltip or temporary message
                alert('Coupon code copied: ' + code);
            })
            .catch(err => {
                console.error('Failed to copy: ', err);
            });
    }
    // ==== Image Thumbnail Switching ====
    function changeMainImage(thumbnail) {
        const mainImage = document.getElementById('pro_img');
        mainImage.src = thumbnail.src;
        mainImage.alt = thumbnail.alt;

        // Toggle active class
        document.querySelectorAll('.thumbnail').forEach(img => {
            img.classList.remove('active');
        });
        thumbnail.classList.add('active');
    }

    document.querySelectorAll('.thumbnail').forEach(thumbnail => {
        thumbnail.addEventListener('click', function () {
            changeMainImage(this);
        });
    });

    // ==== Quantity and Price Update ====
    const quantityInput = document.getElementById('quantity');
    const basePrice = parseFloat(document.getElementById('base_price').value);
    const deliveryCharge = parseFloat(document.getElementById('delivery_charge').value) || 0;
    let currentCoupon = null;

    function updatePrice() {
        const quantity = parseInt(quantityInput.value);
        const subtotal = basePrice * quantity;
        let totalAmount = subtotal + deliveryCharge;
        let discountAmount = 0;

        // If coupon is applied, recalculate discount
        if (currentCoupon) {
            discountAmount = calculateDiscount(currentCoupon.discount, subtotal, currentCoupon.max_discount);
            totalAmount = subtotal + deliveryCharge - discountAmount;
        }

        document.getElementById('quantity-display').textContent = quantity;
        document.getElementById('item-price').textContent = '₹' + basePrice.toFixed(2);
        document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
        document.getElementById('delivery-charge').textContent = '₹' + deliveryCharge.toFixed(2);
        document.getElementById('total-amount').textContent = '₹' + totalAmount.toFixed(2);
        document.getElementById('pay-amount').textContent = '₹' + totalAmount.toFixed(2);
        document.getElementById('total_amount').value = totalAmount.toFixed(2);

        // Update discount display if coupon is applied
        if (currentCoupon) {
            document.getElementById('discount-value').textContent = '-₹' + discountAmount.toFixed(2);
            document.getElementById('discount_amount').value = discountAmount.toFixed(2);
        } else {
            // Remove discount row if exists
            const discountRow = document.getElementById('discount-row');
            if (discountRow) {
                discountRow.remove();
            }
            document.getElementById('discount_amount').value = '0';
        }
    }

    document.getElementById('increase-qty').addEventListener('click', function () {
        let qty = parseInt(quantityInput.value);
        if (qty < 10) {
            quantityInput.value = qty + 1;
            updatePrice();
        }
    });

    document.getElementById('decrease-qty').addEventListener('click', function () {
        let qty = parseInt(quantityInput.value);
        if (qty > 1) {
            quantityInput.value = qty - 1;
            updatePrice();
        }
    });

    quantityInput.addEventListener('change', function () {
        let qty = parseInt(quantityInput.value);
        if (qty < 1) qty = 1;
        if (qty > 10) qty = 10;
        quantityInput.value = qty;
        updatePrice();
    });

    // ==== Coupon Code Handling ====
    document.getElementById('apply_coupon').addEventListener('click', function () {
        const couponCode = document.getElementById('coupon_code').value.trim();
        const baseAmount = parseFloat(document.getElementById('base_price').value);
        const quantity = parseInt(document.getElementById('quantity').value);
        const subtotal = baseAmount * quantity;

        if (!couponCode) {
            document.getElementById('coupon_message').textContent = 'Please enter a coupon code';
            document.getElementById('coupon_message').style.color = 'red';
            return;
        }

        // AJAX call to validate coupon
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'validate_coupon.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    currentCoupon = {
                        id: response.coupon_id,
                        code: couponCode,
                        discount: response.discount,
                        max_discount: response.max_discount || 0
                    };

                    const discountAmount = calculateDiscount(response.discount, subtotal, response.max_discount);
                    const totalAmount = subtotal + deliveryCharge - discountAmount;

                    // Update UI
                    document.getElementById('coupon_message').textContent = response.message;
                    document.getElementById('coupon_message').style.color = 'green';
                    document.getElementById('total-amount').textContent = '₹' + totalAmount.toFixed(2);
                    document.getElementById('pay-amount').textContent = '₹' + totalAmount.toFixed(2);
                    document.getElementById('total_amount').value = totalAmount.toFixed(2);
                    document.getElementById('discount_amount').value = discountAmount.toFixed(2);
                    document.getElementById('coupon_id').value = response.coupon_id;

                    // Add discount row if it doesn't exist
                    if (!document.getElementById('discount-row')) {
                        const priceBreakdown = document.querySelector('.price-breakdown');
                        const totalRow = document.querySelector('.total-row');
                        const discountRow = document.createElement('div');
                        discountRow.className = 'price-row';
                        discountRow.id = 'discount-row';
                        discountRow.innerHTML = `
                            <span class="price-label">Discount Applied (${response.discount}%)</span>
                            <span class="price-value" id="discount-value">-₹${discountAmount.toFixed(2)}</span>
                        `;

                        if (totalRow && totalRow.parentNode === priceBreakdown) {
                            priceBreakdown.insertBefore(discountRow, totalRow);
                        } else {
                            priceBreakdown.appendChild(discountRow);
                        }
                    } else {
                        document.getElementById('discount-value').textContent = `-₹${discountAmount.toFixed(2)}`;
                    }
                } else {
                    document.getElementById('coupon_message').textContent = response.message;
                    document.getElementById('coupon_message').style.color = 'red';
                    resetCoupon();
                }
            } else {
                document.getElementById('coupon_message').textContent = 'Error validating coupon';
                document.getElementById('coupon_message').style.color = 'red';
            }
        };
        xhr.send('coupon_code=' + encodeURIComponent(couponCode) + '&amount=' + subtotal);
    });

    function calculateDiscount(discountPercent, subtotal, maxDiscount) {
        const discountAmount = (subtotal * discountPercent) / 100;
        return maxDiscount > 0 ? Math.min(discountAmount, maxDiscount) : discountAmount;
    }

    function resetCoupon() {
        currentCoupon = null;
        document.getElementById('coupon_code').value = '';
        document.getElementById('coupon_message').textContent = '';
        document.getElementById('coupon_id').value = '';
        updatePrice();
    }

    // ==== Form Validation ====
    function validateForm() {
        let isValid = true;

        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const address = document.getElementById('address').value.trim();
        const pincode = document.getElementById('pincode').value.trim();

        // Name
        if (name.length < 3) {
            document.getElementById('name-error').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('name-error').style.display = 'none';
        }

        // Email
        const emailPattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if (!emailPattern.test(email)) {
            document.getElementById('email-error').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('email-error').style.display = 'none';
        }

        // Phone
        if (!/^\d{10}$/.test(phone)) {
            document.getElementById('phone-error').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('phone-error').style.display = 'none';
        }

        // Address
        if (address.length < 10) {
            document.getElementById('address-error').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('address-error').style.display = 'none';
        }

        // Pincode
        if (!/^\d{6}$/.test(pincode)) {
            document.getElementById('pincode-error').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('pincode-error').style.display = 'none';
        }

        if (!isValid) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        return isValid;
    }

    // ==== Payment Popup Handling ====
    document.getElementById("pay_button").addEventListener("click", function () {
        if (validateForm()) {
            document.getElementById('paymentPopup').style.display = 'flex';
        }
    });

    document.getElementById('closePopup').addEventListener('click', function () {
        document.getElementById('paymentPopup').style.display = 'none';
    });

    document.getElementById('confirmPayment').addEventListener('click', function () {
        const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
        document.getElementById('paymentPopup').style.display = 'none';

        if (paymentMethod === 'cod') {
            processCOD();
        } else {
            processRazorpay();
        }
    });

    function processCOD() {
        let product_id = '<?= $row['id'] ?>';
        let product_name = '<?= $row['product_name'] ?>';
        let quantity = document.getElementById("quantity-display").textContent;
        let name = document.getElementById("name").value;
        let email = document.getElementById("email").value;
        let phone = document.getElementById("phone").value;
        let address = document.getElementById("address").value;
        let pincode = document.getElementById("pincode").value;
        let price = document.getElementById("total-amount").textContent.replace("₹", "").trim();
        let img = '<?= $row['image'] ?>';
        let delivery_charge = '<?= $row['delivery'] ?? 0 ?>';
        let source_table = '<?= $row['source_table'] ?>';
        let coupon_id = document.getElementById("coupon_id").value;
        let discount_amount = document.getElementById("discount_amount").value;

        // Create a hidden form and submit it
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = 'process_cod.php';

        let addField = (name, value) => {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        };

        addField('product_id', product_id);
        addField('product_name', product_name);
        addField('quantity', quantity);
        addField('name', name);
        addField('email', email);
        addField('phone', phone);
        addField('address', address);
        addField('pincode', pincode);
        addField('amount', price);
        addField('img', img);
        addField('delivery_charge', delivery_charge);
        addField('payment_method', 'cod');
        addField('payment_status', 'Pending');
        addField('source_table', source_table);
        addField('coupon_id', coupon_id);
        addField('discount_amount', discount_amount);

        // Add offer products data
        <?php foreach ($offer_products as $index => $offer): ?>
            addField('offer_products[<?= $index ?>][id]', '<?= $offer['id'] ?>');
            addField('offer_products[<?= $index ?>][name]', '<?= $offer['offer_product_name'] ?>');
            addField('offer_products[<?= $index ?>][price]', '<?= $offer['offer_product_mrp'] ?>');
            addField('offer_products[<?= $index ?>][image]', '<?= $offer['offer_product_image'] ?>');
            addField('offer_products[<?= $index ?>][quantity]', '<?= $offer['offer_product_qty'] ?>');
        <?php endforeach; ?>

        document.body.appendChild(form);
        form.submit();
    }

    function processRazorpay() {
        let product_id = '<?= $row['id'] ?>';
        let product_name = '<?= $row['product_name'] ?>';
        let quantity = document.getElementById("quantity-display").textContent;
        let name = document.getElementById("name").value;
        let email = document.getElementById("email").value;
        let phone = document.getElementById("phone").value;
        let address = document.getElementById("address").value;
        let pincode = document.getElementById("pincode").value;
        let price = document.getElementById("total-amount").textContent.replace("₹", "").trim();
        let mainAmount = parseFloat(price) * 100;
        let img = '<?= $row['image'] ?>';
        let delivery_charge = '<?= $row['delivery'] ?? 0 ?>';
        let source_table = '<?= $row['source_table'] ?>';
        let coupon_id = document.getElementById("coupon_id").value;
        let discount_amount = document.getElementById("discount_amount").value;

        let options = {
            "key": "rzp_live_pGkJ9YKFp0XEnE",
            "amount": mainAmount,
            "currency": "INR",
            "name": "MediCure",
            "description": "Payment for Order",
            "image": "",
            "handler": function (response) {
                alert("Payment Successful! Payment ID: " + response.razorpay_payment_id);

                // Create a form to submit all data including offer products
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = 'process_payment.php';

                let addField = (name, value) => {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    form.appendChild(input);
                };

                addField('product_id', product_id);
                addField('product_name', product_name);
                addField('quantity', quantity);
                addField('payment_id', response.razorpay_payment_id);
                addField('name', name);
                addField('email', email);
                addField('phone', phone);
                addField('address', address);
                addField('pincode', pincode);
                addField('amount', mainAmount / 100);
                addField('img', img);
                addField('delivery_charge', delivery_charge);
                addField('payment_status', "Success");
                addField('source_table', source_table);
                addField('coupon_id', coupon_id);
                addField('discount_amount', discount_amount);

                // Add offer products data
                <?php foreach ($offer_products as $index => $offer): ?>
                    addField('offer_products[<?= $index ?>][id]', '<?= $offer['id'] ?>');
                    addField('offer_products[<?= $index ?>][name]', '<?= $offer['offer_product_name'] ?>');
                    addField('offer_products[<?= $index ?>][price]', '<?= $offer['offer_product_mrp'] ?>');
                    addField('offer_products[<?= $index ?>][image]', '<?= $offer['offer_product_image'] ?>');
                    addField('offer_products[<?= $index ?>][quantity]', '<?= $offer['offer_product_qty'] ?>');
                <?php endforeach; ?>

                document.body.appendChild(form);
                form.submit();
            },
            "prefill": {
                "name": name,
                "email": email,
                "contact": phone
            },
            "theme": {
                "color": "#3399cc"
            }
        };

        let rzp1 = new Razorpay(options);
        rzp1.open();
    }

    // Run on page load
    updatePrice();
</script>

<style>
    .available-coupons {
        margin: 25px 0;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .available-coupons h4 {
        font-size: 1.3rem;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #eaeaea;
    }

    .coupon-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        margin-bottom: 12px;
        background-color: white;
        border-radius: 8px;
        border-left: 4px solid #4CAF50;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .coupon-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .coupon-item div span {
        display: block;
        font-size: 0.9rem;
        color: #555;
        line-height: 1.5;
    }

    .coupon-item div span:first-child {
        font-weight: 600;
        color: #333;
    }

    .copy-coupon {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .copy-coupon:hover {
        background-color: #3d8b40;
        transform: scale(1.03);
    }

    .copy-coupon:active {
        transform: scale(0.98);
    }

    .available-coupons p {
        color: #666;
        font-style: italic;
        text-align: center;
        padding: 10px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .coupon-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .copy-coupon {
            align-self: flex-end;
            padding: 6px 12px;
        }
    }

    @media (max-width: 480px) {
        .available-coupons {
            padding: 15px;
        }

        .coupon-item div span {
            font-size: 0.85rem;
        }

        .copy-coupon {
            font-size: 0.8rem;
        }
    }

    /* Payment Popup Styles */
    .payment-popup-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .payment-popup-container {
        background-color: #fff;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .payment-popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .payment-popup-header h3 {
        margin: 0;
        font-size: 1.5rem;
        color: #333;
    }

    .close-popup {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
    }

    .payment-options {
        margin-bottom: 20px;
    }

    .payment-option {
        display: flex;
        align-items: center;
        padding: 15px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        transition: all 0.3s;
    }

    .payment-option:hover {
        border-color: #3399cc;
    }

    .payment-option input[type="radio"] {
        margin-right: 15px;
    }

    .payment-option label {
        display: flex;
        align-items: center;
        cursor: pointer;
        width: 100%;
    }

    .payment-option img {
        width: 40px;
        margin-right: 15px;
    }

    .confirm-payment-btn {
        width: 100%;
        padding: 12px;
        background-color: #3399cc;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .confirm-payment-btn:hover {
        background-color: #2a7ca5;
    }
</style>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<?php include("includes/footer.php"); ?>