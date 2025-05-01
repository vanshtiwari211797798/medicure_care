<?php
include '../includes/conn.php';
include 'header.php';

// FETCH PRODUCT IF EDIT MODE
$product = [];
$offer = []; // For storing offer data if in edit mode

// Fetch for Edit
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        // Fetch offer data if exists
        $offer_stmt = $conn->prepare("SELECT * FROM product_offers WHERE product_id=?");
        $offer_stmt->bind_param("i", $id);
        $offer_stmt->execute();
        $offer_result = $offer_stmt->get_result();
        if ($offer_result->num_rows > 0) {
            $offer = $offer_result->fetch_assoc();
        }
    }
}

// Add or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process main product data first
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $product_name = trim($_POST['product_name']);
    $qty = trim($_POST['qty']);
    $description = trim($_POST['description']);
    $price = (float) $_POST['price'];
    $delivery = (int) $_POST['delivery'];
    $stock = (int) $_POST['stock'];
    $discount = (float) $_POST['discount'];
    $gst = (float) $_POST['gst'];
    $sale_price = (float) $_POST['sale_price'];
    $category = trim($_POST['category']);

    // Existing image names from hidden inputs
    $existing_image = $_POST['existing_image'] ?? '';
    $existing_sub1 = $_POST['existing_sub_image1'] ?? '';
    $existing_sub2 = $_POST['existing_sub_image2'] ?? '';
    $existing_sub3 = $_POST['existing_sub_image3'] ?? '';

    // Main Image Upload
    $image_name = uploadImage('image', $existing_image);

    // Sub Image Uploads
    $sub_image1 = uploadImage('sub_image1', $existing_sub1);
    $sub_image2 = uploadImage('sub_image2', $existing_sub2);
    $sub_image3 = uploadImage('sub_image3', $existing_sub3);

    if ($id > 0) {
        // Update existing product
        $stmt = $conn->prepare("UPDATE products SET product_name=?,qty=?, description=?, price=?, delivery=?, stock=?, discount=?, gst=?, sale_price=?, category=?, image=?, sub_image1=?, sub_image2=?, sub_image3=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("sssdddddssssssi", $product_name,$qty, $description, $price, $delivery, $stock, $discount, $gst, $sale_price, $category, $image_name, $sub_image1, $sub_image2, $sub_image3, $id);
            $stmt->execute();
        } else {
            $_SESSION['error'] = "Prepare failed: {$conn->error}";
            header("Location: products.php");
            exit;
        }
    } else {
        // Insert new product
        $stmt = $conn->prepare("INSERT INTO products (product_name,qty, description, price, delivery, stock, discount, gst, sale_price, category, image, sub_image1, sub_image2, sub_image3) VALUES (?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssddddddsssss", $product_name,$qty,$description, $price, $delivery, $stock, $discount, $gst, $sale_price, $category, $image_name, $sub_image1, $sub_image2, $sub_image3);
            $stmt->execute();
            $id = $stmt->insert_id; // Get the newly inserted product ID
        } else {
            $_SESSION['error'] = "Prepare failed: {$conn->error}";
            header("Location: products.php");
            exit;
        }
    }

    // Process offer product data if provided
    if (!empty($_POST['offer_product_name'])) {
        $offer_id = $offer['id'] ?? 0;
        $offer_name = trim($_POST['offer_product_name']);
        $offer_desc = trim($_POST['offer_product_description']);
        $offer_mrp = (float) $_POST['offer_product_mrp'];
        $offer_qty = (int) $_POST['offer_product_qty'];

        // Handle existing offer image
        $existing_offer_image = $offer['offer_product_image'] ?? '';

        // Upload new offer image if provided
        $offer_image = uploadImage('offer_product_image', $existing_offer_image);

        if ($offer_id > 0) {
            // Update existing offer
            $offer_stmt = $conn->prepare("UPDATE product_offers SET 
            offer_product_name=?, 
            offer_product_description=?, 
            offer_product_image=?, 
            offer_product_mrp=?, 
            offer_product_qty=? 
            WHERE id=?");
            $offer_stmt->bind_param("sssdii", $offer_name, $offer_desc, $offer_image, $offer_mrp, $offer_qty, $offer_id);
        } else {
            // Insert new offer
            $offer_stmt = $conn->prepare("INSERT INTO product_offers 
            (product_id, offer_product_name, offer_product_description, offer_product_image, offer_product_mrp, offer_product_qty) 
            VALUES (?, ?, ?, ?, ?, ?)");
            $offer_stmt->bind_param("isssdi", $id, $offer_name, $offer_desc, $offer_image, $offer_mrp, $offer_qty);
        }
        $offer_stmt->execute();
    } elseif (isset($offer['id'])) {
        // If offer name is empty but there was an existing offer, delete it
        $delete_stmt = $conn->prepare("DELETE FROM product_offers WHERE id=?");
        $delete_stmt->bind_param("i", $offer['id']);
        $delete_stmt->execute();

        // Also delete the offer image if it exists
        if (!empty($offer['offer_product_image']) && file_exists("uploads/{$offer['offer_product_image']}")) {
            unlink("uploads/{$offer['offer_product_image']}");
        }
    }

    $_SESSION['success'] = "Product " . ($id > 0 ? "updated" : "added") . " successfully!";
    header("Location: products.php");
    exit;
}

// Image Upload Helper Function
function uploadImage($input_name, $existing_file = '')
{
    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $new_name = time() . '_' . uniqid() . '.' . $ext;
            $upload_path = "uploads/{$new_name}";
            if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $upload_path)) {
                if (!empty($existing_file) && file_exists("uploads/$existing_file")) {
                    unlink("uploads/$existing_file");
                }
                return $new_name;
            } else {
                $_SESSION['error'] = "Upload failed for $input_name.";
            }
        } else {
            $_SESSION['error'] = "Invalid format for $input_name.";
        }
    }
    return $existing_file;
}

// Delete Product
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // First delete the offer if exists (and its image)
    $offer_stmt = $conn->prepare("SELECT offer_product_image FROM product_offers WHERE product_id=?");
    $offer_stmt->bind_param("i", $id);
    $offer_stmt->execute();
    $offer_res = $offer_stmt->get_result();
    if ($offer_res->num_rows > 0) {
        $offer_row = $offer_res->fetch_assoc();
        if (!empty($offer_row['offer_product_image']) && file_exists("uploads/{$offer_row['offer_product_image']}")) {
            unlink("uploads/{$offer_row['offer_product_image']}");
        }
        // Delete the offer record
        $conn->query("DELETE FROM product_offers WHERE product_id=$id");
    }

    // Then delete the main product (and its images)
    $stmt = $conn->prepare("SELECT image, sub_image1, sub_image2, sub_image3 FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        // Delete all product images
        $images = [$row['image'], $row['sub_image1'], $row['sub_image2'], $row['sub_image3']];
        foreach ($images as $img) {
            if (!empty($img) && file_exists("uploads/$img")) {
                unlink("uploads/$img");
            }
        }
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['success'] = "Product and associated offer deleted!";
    header("Location: products.php");
    exit;
}
?>

<div class="form-container">
    <!-- Display success/error messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']);
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <h2><?= isset($_GET['edit']) ? 'Edit Product' : 'Add New Product' ?></h2>

    <form method="POST" enctype="multipart/form-data" oninput="calculateSalePrice()">
        <input type="hidden" name="id" value="<?= $product['id'] ?? '' ?>">
        <input type="hidden" name="existing_image" value="<?= $product['image'] ?? '' ?>">

        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="product_name" value="<?= htmlspecialchars($product['product_name'] ?? '') ?>"
                required>
        </div>
        <div class="form-group">
            <label>Qty</label>
            <input type="text" name="qty" value="<?= htmlspecialchars($product['qty'] ?? '') ?>"
                required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" required><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Price (₹)</label>
            <input type="number" name="price" id="price" value="<?= htmlspecialchars($product['price'] ?? '') ?>"
                step="0.01" min="0" required>
        </div>
        
        <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" value="<?= $product['stock'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label>Delivery Charge</label>
            <input type="number" name="delivery" value="<?= $product['delivery'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label>Discount (%)</label>
            <input type="number" name="discount" id="discount"
                value="<?= htmlspecialchars($product['discount'] ?? 0) ?>" step="0.01" min="0" max="100" required>
        </div>

        <div class="form-group">
            <label for="gst">GST (%)</label>
            <select name="gst" id="gst" required>
                <?php
                $gstOptions = [0, 5, 12, 18, 28];
                $selectedGst = $product['gst'] ?? 0;
                foreach ($gstOptions as $option) {
                    $selected = ($selectedGst == $option) ? 'selected' : '';
                    echo "<option value=\"$option\" $selected>$option%</option>";
                }
                ?>
            </select>
        </div>


        <div class="form-group">
            <label>Sale Price (₹)</label>
            <input type="number" name="sale_price" id="sale_price"
                value="<?= htmlspecialchars($product['sale_price'] ?? '') ?>" step="0.01" class="readonly-input"
                readonly>
            <small>(Price after discount + GST)</small>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category" required>
                <option value="">-- Select Category --</option>
                <?php
                // Fetch all categories from `categories` table
                $catResult = $conn->query("SELECT name FROM categories ORDER BY name ASC");
                $selectedCategory = $product['category'] ?? '';

                if ($catResult && $catResult->num_rows > 0) {
                    while ($row = $catResult->fetch_assoc()) {
                        $catName = htmlspecialchars($row['name']);
                        $selected = ($catName == $selectedCategory) ? 'selected' : '';
                        echo "<option value=\"$catName\" $selected>$catName</option>";
                    }
                } else {
                    echo '<option disabled>No categories found</option>';
                }
                ?>
            </select>
        </div>


        <div class="form-group">
            <label>Main Image</label>
            <input type="file" name="image" accept="image/jpeg, image/png, image/gif">
            <?php if (!empty($product['image'])): ?>
                <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="preview-image">
                <p>Current main image: <?= htmlspecialchars($product['image']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Sub Image 1</label>
            <input type="file" name="sub_image1" accept="image/jpeg, image/png, image/gif">
            <?php if (!empty($product['sub_image1'])): ?>
                <img src="uploads/<?= htmlspecialchars($product['sub_image1']) ?>" class="preview-image">
                <p>Current sub image 1: <?= htmlspecialchars($product['sub_image1']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Sub Image 2</label>
            <input type="file" name="sub_image2" accept="image/jpeg, image/png, image/gif">
            <?php if (!empty($product['sub_image2'])): ?>
                <img src="uploads/<?= htmlspecialchars($product['sub_image2']) ?>" class="preview-image">
                <p>Current sub image 2: <?= htmlspecialchars($product['sub_image2']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Sub Image 3</label>
            <input type="file" name="sub_image3" accept="image/jpeg, image/png, image/gif">
            <?php if (!empty($product['sub_image3'])): ?>
                <img src="uploads/<?= htmlspecialchars($product['sub_image3']) ?>" class="preview-image">
                <p>Current sub image 3: <?= htmlspecialchars($product['sub_image3']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" id="toggleOffer" onclick="toggleOfferForm()"> Add Additional Offer Products
            </label>
        </div>

        <div id="offerForm"
            style="<?= !empty($offer) ? 'display: block;' : 'display: none;' ?> border: 1px solid #ddd; padding: 15px; margin-top: 15px;">
            <h4>Additional Offer Product Details</h4>

            <div class="form-group">
                <label>Offer Product Name</label>
                <input type="text" name="offer_product_name" placeholder="e.g. Free Mask Pack"
                    value="<?= !empty($offer) ? htmlspecialchars($offer['offer_product_name']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Offer Product Description</label>
                <textarea name="offer_product_description" placeholder="Brief about the offer product...">
            <?= !empty($offer) ? htmlspecialchars($offer['offer_product_description']) : '' ?>
        </textarea>
            </div>

            <div class="form-group">
                <label>Offer Product Image</label>
                <input type="file" name="offer_product_image" accept="image/jpeg, image/png, image/gif">
                <?php if (!empty($offer['offer_product_image'])): ?>
                    <input type="hidden" name="existing_offer_image" value="<?= $offer['offer_product_image'] ?>">
                    <img src="uploads/<?= htmlspecialchars($offer['offer_product_image']) ?>" class="preview-image">
                    <p>Current offer image: <?= htmlspecialchars($offer['offer_product_image']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>MRP (₹)</label>
                <input type="number" name="offer_product_mrp" step="0.01"
                    value="<?= !empty($offer) ? htmlspecialchars($offer['offer_product_mrp']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Offer Quantity</label>
                <input type="number" name="offer_product_qty" placeholder="e.g. 1"
                    value="<?= !empty($offer) ? htmlspecialchars($offer['offer_product_qty']) : '' ?>">
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><?= isset($_GET['edit']) ? 'Update' : 'Add' ?> Product</button>
    </form>


    <script>
        function toggleOfferForm() {
            const offerSection = document.getElementById('offerForm');
            const toggle = document.getElementById('toggleOffer');
            offerSection.style.display = toggle.checked ? 'block' : 'none';
        }

        function calculateSalePrice() {
            let price = parseFloat(document.getElementById('price').value) || 0;
            let discount = parseFloat(document.getElementById('discount').value) || 0;
            let gst = parseFloat(document.getElementById('gst').value) || 0;

            // Calculate discounted price
            let discountedPrice = price - (price * discount / 100);

            // Calculate GST amount
            let gstAmount = discountedPrice * gst / 100;

            // Calculate final sale price (discounted price + GST)
            let salePrice = discountedPrice + gstAmount;

            document.getElementById('sale_price').value = salePrice.toFixed(2);
        }

        // Calculate on page load if editing
        <?php if (isset($_GET['edit'])): ?>
            window.onload = function () {
                calculateSalePrice();
                document.querySelector(".form-container").scrollIntoView({ behavior: 'smooth' });
            };
        <?php endif; ?>
    </script>

    <hr class="divider">

    <h2 style="text-align:center; font-size: 24px; margin-top: 30px;">All Products</h2>

    <form method="GET" class="search-container">
        <input type="text" name="search" placeholder="Search products..."
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="search-input">
        <button type="submit" class="search-btn">Search</button>
    </form>

    <div class="products-grid">
        <?php
        $search = $_GET['search'] ?? '';
        $search = $conn->real_escape_string($search);

        $query = "
            SELECT *, 
                CASE 
                    WHEN product_name LIKE '%$search%' THEN 1
                    WHEN description LIKE '%$search%' THEN 2
                    ELSE 3 
                END AS priority
            FROM products
            " . (!empty($search) ? " WHERE product_name LIKE '%$search%' OR description LIKE '%$search%'" : "") . "
            ORDER BY priority ASC, id DESC
        ";

        $res = $conn->query($query);

        if ($res && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $discountedPrice = $row['price'] * (1 - $row['discount'] / 100);
                $gstAmount = $discountedPrice * $row['gst'] / 100;
                $finalPrice = $discountedPrice + $gstAmount;

                echo "
                    <div class='product-card'>
                        <img src='uploads/{$row['image']}' alt='{$row['product_name']}' class='product-image'>
                        <div class='product-content'>
                            <h3 class='product-title'>" . htmlspecialchars($row['product_name']) . "</h3>

                            <div class='product-meta'>
                                <span>" . htmlspecialchars($row['category']) . "</span>
                            </div>

                            <div>
                                <span class='product-discount'>₹{$row['price']}</span>
                                <span class='product-price'>₹{$finalPrice}</span>
                                <span>({$row['discount']}% off + {$row['gst']}% GST)</span>
                            </div>

                            <div class='product-actions'>
                                <a href='products.php?edit={$row['id']}' class='action-btn edit-btn'>Edit</a>
                                <a href='products.php?delete={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this product?\")' class='action-btn delete-btn'>Delete</a>
                            </div>
                        </div>
                    </div>
                ";
            }
        } else {
            echo "<p style='text-align:center;'>No products found.</p>";
        }
        ?>
    </div>
</div>

<style>
    .form-container {
        width: 900px;
        margin-left: 20%;
        padding: 4rem;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .form-container h2 {
        margin-bottom: 1rem;
        font-size: 22px;
        color: #2d3748;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.5rem;
    }

    .form-group {
        margin-bottom: 1.2rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        color: #4a5568;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #cbd5e0;
        font-size: 14px;
        background-color: #f9f9f9;
    }

    .readonly-input {
        background-color: #edf2f7;
    }

    .preview-image {
        margin-top: 10px;
        max-width: 100px;
        height: auto;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    .btn-primary {
        background-color: #3182ce;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #2b6cb0;
    }

    .divider {
        margin: 2rem auto;
        border: none;
        border-top: 1px solid #e2e8f0;
        width: 90%;
    }

    .search-container {
        max-width: 700px;
        margin: 0 auto 2rem;
        display: flex;
        gap: 10px;
    }

    .search-input {
        flex: 1;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #cbd5e0;
    }

    .search-btn {
        background-color: #38a169;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .search-btn:hover {
        background-color: #2f855a;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 2fr));
        gap: 10px;
        padding: 0 10px 40px;
    }

    .product-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.2s ease;
    }

    .product-card:hover {
        transform: scale(1.01);
    }

    .product-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .product-content {
        padding: 15px;
    }

    .product-title {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
        color: #2d3748;
    }

    .product-description {
        font-size: 14px;
        color: #4a5568;
        margin: 10px 0;
    }

    .product-meta {
        font-size: 13px;
        color: #718096;
        margin-bottom: 10px;
    }

    .product-discount {
        text-decoration: line-through;
        color: #e53e3e;
        margin-right: 10px;
        font-weight: 500;
    }

    .product-price {
        font-weight: 600;
        color: #2f855a;
        margin-right: 5px;
    }

    .product-actions {
        margin-top: 10px;
    }

    .action-btn {
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 4px;
        margin-right: 8px;
        text-decoration: none;
        color: white;
    }

    .edit-btn {
        background-color: #4299e1;
    }

    .edit-btn:hover {
        background-color: #2b6cb0;
    }

    .delete-btn {
        background-color: #e53e3e;
    }

    .delete-btn:hover {
        background-color: #c53030;
    }
</style>
</body>

</html>