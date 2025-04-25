<?php include("includes/conn.php"); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Product Details - MediCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f3f4f6;
            color: #1f2937;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .product-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }

        .product-images {
            flex: 1 1 400px;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: contain;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        .thumbnails {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .thumbnails img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 2px solid transparent;
            border-radius: 8px;
            cursor: pointer;
        }

        .thumbnails img.active {
            border-color: #2563eb;
        }

        .product-info {
            flex: 1 1 500px;
        }

        .product-info h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .product-info p {
            margin: 10px 0;
            line-height: 1.6;
        }

        .product-info .price {
            font-size: 22px;
            color: #10b981;
            font-weight: bold;
            margin-top: 10px;
        }

        .btn-add-to-cart {
            margin-top: 20px;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-add-to-cart:hover {
            background: #1e40af;
        }

        @media (max-width: 768px) {
            .product-wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <?php
        // Example product ID from GET (replace with dynamic logic if needed)
        $productId = isset($_GET['id']) ? intval($_GET['id']) : 1;
        $sql = "SELECT * FROM products WHERE id = $productId";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        ?>

        <div class="product-wrapper">
            <div class="product-images">
                <img id="mainImage" src="admin/uploads/<?php echo $row['image']; ?>" alt="Product Image"
                    class="main-image" />
                <div class="thumbnails">
                    <img src="admin/uploads/<?php echo $row['image']; ?>" alt="Thumb 1" class="active"
                        onclick="changeImage(this)" />
                    <!-- Additional thumbnails (for now, duplicates as placeholder) -->
                    <img src="admin/uploads/<?php echo $row['image']; ?>" alt="Thumb 2" onclick="changeImage(this)" />
                    <img src="admin/uploads/<?php echo $row['image']; ?>" alt="Thumb 3" onclick="changeImage(this)" />
                </div>
            </div>
            <div class="product-info">
                <h2><?php echo $row['name']; ?></h2>
                <p><?php echo $row['description']; ?></p>
                <p class="price">Rs. <?php echo $row['sale_price']; ?>
                    <del style="color: #6b7280; margin-left: 10px;">Rs. <?php echo $row['price']; ?></del>
                </p>
                <form method="post" action="cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>" />
                    <button class="btn-add-to-cart" type="submit" name="add_to_cart">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function changeImage(elem) {
            const mainImage = document.getElementById('mainImage');
            mainImage.src = elem.src;

            const thumbnails = document.querySelectorAll('.thumbnails img');
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            elem.classList.add('active');
        }
    </script>

</body>

</html>

<?php include("footer.php"); ?>