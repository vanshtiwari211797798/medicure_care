<?php
// session_start();
include("includes/header.php");
include("includes/conn.php");

?>



<div id="allproduct" style="position: relative;">
    <!-- Sidebar -->
    <div class="sidebar-section">
        <h3 class="sidebar-title">Categories</h3>
        <ul class="category-list">
            <?php
            $categoryQuery = "SELECT DISTINCT category FROM products ORDER BY category ASC";
            $categoryResult = mysqli_query($conn, $categoryQuery);

            echo '<li class="category-item' . (!isset($_GET['category']) ? ' active' : '') . '">';
            echo '<a href="?category=" class="category-link">All Products</a>';
            echo '</li>';

            if (mysqli_num_rows($categoryResult) > 0) {
                while ($catRow = mysqli_fetch_assoc($categoryResult)) {
                    $isActive = (isset($_GET['category']) && $_GET['category'] == $catRow['category']) ? ' active' : '';
                    echo '<li class="category-item' . $isActive . '">';
                    echo '<a href="?category=' . urlencode($catRow['category']) . '" class="category-link">';
                    echo htmlspecialchars($catRow['category']);
                    echo '</a>';
                    echo '</li>';
                }
            }
            ?>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title">
            <?= isset($_GET['category']) && $_GET['category'] !== '' ? htmlspecialchars($_GET['category']) . ' Products' : 'All Products' ?>
        </h1>
        <div class="product-container">
            <?php
            if (isset($_GET['category']) && $_GET['category'] !== '') {
                $cate = mysqli_real_escape_string($conn, $_GET['category']);
                $sql = "SELECT p.*, po.product_id FROM products p LEFT JOIN product_offers po ON p.id = po.product_id WHERE p.category='$cate'";
            } else {
                $sql = "SELECT p.*, po.product_id FROM products p LEFT JOIN product_offers po ON p.id = po.product_id ORDER BY p.id DESC";
            }

            $data = mysqli_query($conn, $sql);
            if (mysqli_num_rows($data) > 0) {
                while ($row = mysqli_fetch_array($data)) {
                    ?>
                    <div class="product-card" onclick="window.location.href='product-detail.php?product_id=<?= $row['id'] ?>'"
                        style="cursor: pointer;">
                        <?php if ($row['discount'] > 0): ?>
                            <div class="discount-badge"><?= $row['discount'] ?>% OFF</div>
                        <?php endif; ?>

                        <?php if (!empty($row['offer'])): ?>
                            <div class="discount-badge" style="background: #4CAF50; left: 10px;">
                                <?= htmlspecialchars($row['offer']) ?>
                            </div>
                        <?php endif; ?>

                        <img src="admin/uploads/<?= $row['image'] ?>" class="product-image" alt="<?= $row['product_name'] ?>"
                            loading="lazy">

                        <div class="product-details">
                            <h2 class="product-name"><?= $row['product_name'] ?></h2>
                            <p class="product-description"><?= $row['description'] ?></p>

                            <?php if ($row['stock'] <= 0): ?>
                                <p class="stock-status">Out of Stock</p>
                            <?php endif; ?>

                            <div class="price-section">
                                <?php if ($row['discount'] > 0): ?>
                                    <span class="original-price">Rs <?= number_format($row['price'], 2) ?></span>
                                <?php endif; ?>
                                <span class="sale-price">Rs <?= number_format($row['sale_price'], 2) ?></span>
                            </div>

                            <p class="category"><?= $row['category'] ?></p>
                            <?php if ($row['product_id']): ?>
                                <a href="AdditionalOffer.php?id=<?= $row['id'] ?>" onclick="event.stopPropagation();"
                                    style="text-decoration: none;"></a>
                                <p class="category" style="background: #ffeeba; color: #856404; cursor: pointer;">Additional
                                    Offer</p>
                                </a>
                            <?php endif; ?>

                            <div class="button-group">
                                <a href="add_to_cart.php?table=products&id=<?= $row['id'] ?>" class="btn add-to-cart"
                                    onclick="event.stopPropagation();">Add to Cart</a>
                                <a href="buy.php?product_id=<?= $row['id'] ?>" class="btn buy-now"
                                    onclick="event.stopPropagation();">Buy Now</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="no-products">No products available in this category</div>';
            }
            ?>
        </div>
    </div>
</div>
<style>
    .coupon-bar {
        position: absolute;
        right: 0px;
        top: 10px;
        background: #ff5722;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
        z-index: 1;
        cursor: pointer;
    }

    .coupon-bar:hover {
        background-color: #ff5721;
    }

    /* Main Layout */
    #allproduct {
        display: flex;
        gap: 30px;
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .sidebar-section {
        flex: 0 0 250px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 20px;
        height: 80vh; /* Set fixed height */
        position: sticky;
        top: 20px;
        overflow-y: auto; /* Add vertical scrollbar when needed */
    }

    /* Custom scrollbar styling */
    .sidebar-section::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-section::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .sidebar-section::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .sidebar-section::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .main-content {
        flex: 1;
        padding: 15px;
    }

    /* Category Sidebar */
    .sidebar-title {
        font-size: 1rem;
        margin-bottom: 20px;
        color: #333;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .category-item {
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }

    .category-link {
        display: block;
        padding: 10px 15px;
        color: #555;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.3s ease;
        font-size: 15px;
    }

    .category-link:hover {
        background-color: #f5f5f5;
        color: #333;
        transform: translateX(3px);
    }

    .category-item.active .category-link {
        background-color: #007bff;
        color: white;
        font-weight: 500;
    }

    /* Page Title */
    .page-title {
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    /* Product Grid - Made cards smaller */
    .product-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
    }

    /* .product-card {
        border: 1px solid #eaeaea;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background: #fff;
        display: flex;
        flex-direction: column;
        height: 100%;
    } */

    /* .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    } */

    .discount-badge {
        position: absolute;
        top: 8px;
        background: #ff5722;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 400;
        z-index: 1;
    }

    .product-image {
        width: 100%;
        height: 180px;
        object-fit: contain;
        padding: 2px;
        background: #f9f9f9;
        border-bottom: 1px solid #f0f0f0;
    }

    .product-details {
        padding: 4px;
        flex-grow: 1;
        display: flex;
        /* gap: 2px; */
        flex-direction: column;
    }

    .product-name {
        font-size: 0.9rem;
        color: #333;
        font-weight: 600;
    }

    .product-description {
        font-size: 7px;
        color: #666;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .stock-status {
        color: #e53935;
        font-weight: 500;
        font-size: 5px;
        margin-bottom: 8px;
    }

    .price-section {
        /* margin: 0px 0 12px; */
    }

    .original-price {
        text-decoration: line-through;
        color: #999;
        font-size: 14px;
        margin-right: 6px;
    }

    .sale-price {
        font-size: 1.1rem;
        color: #e53935;
        font-weight: 600;
    }

    .category {
        font-size: 12px;
        color: #666;
        margin-bottom: 12px;
        background: #f5f5f5;
        padding: 3px 6px;
        border-radius: 3px;
        display: inline-block;
    }

    .button-group {
        display: flex;
        gap: 8px;
        margin-top: auto;
    }

    .btn {
        flex: 1;
        color: white;
        border: none;
        padding: 8px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
        text-align: center;
        text-decoration: none;
        font-size: 13px;
    }

    .add-to-cart {
        background: #4CAF50;
    }

    .add-to-cart:hover {
        background: #3d8b40;
    }

    .buy-now {
        background: #2196F3;
    }

    .buy-now:hover {
        background: #0d8bf2;
    }

    .no-products {
        text-align: center;
        padding: 40px;
        font-size: 16px;
        color: #666;
        grid-column: 1 / -1;
        background: #f9f9f9;
        border-radius: 8px;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        #allproduct {
            gap: 18px;
        }

        .sidebar-section {
            flex: 0 0 200px;
        }
    }

    @media (max-width: 992px) {
        #allproduct {
            flex-direction: column;
        }

        .sidebar-section {
            position: static;
            margin-bottom: 25px;
        }

        .product-container {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .product-container {
            gap: 15px;
        }

        .product-image {
            height: 160px;
        }

        .page-title {
            font-size: 1.6rem;
        }
    }

    /* Mobile view - 2 cards per row */
    @media (max-width: 576px) {
        .product-container {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        /* .product-card {
            border-radius: 6px;
        } */

        .product-image {
            height: 140px;
            padding: 8px;
        }

        .product-details {
            padding: 12px;
        }

        .product-name {
            font-size: 0.9rem;
        }

        .page-title {
            font-size: 1.4rem;
            margin-bottom: 15px;
        }

        .btn {
            padding: 7px;
            font-size: 12px;
        }

        .discount-badge {
            font-size: 11px;
            padding: 3px 6px;
        }
    }

    @media (max-width: 400px) {
        .product-container {
            grid-template-columns: 1fr;
        }

        .sidebar-section {
            padding: 12px;
        }

        .category-link {
            padding: 7px 10px;
        }
    }
</style>

<?php
include("includes/footer.php");
?>