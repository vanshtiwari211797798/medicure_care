<?php include 'header.php'; ?>
<?php include '../includes/conn.php'; ?>

<div id="container">
    <?php
    

    // Handle Delete
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $conn->query("DELETE FROM latestproducts WHERE id = $id");
        header("Location: LatestProducts.php");
        exit;
    }

    // Handle Form Submission (Insert or Update)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST['id'];
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $net_price = $_POST['net_price'];
        $selling_price = $_POST['selling_price'];
        $discount_bar = $_POST['discount_bar'];
        $category = $_POST['category'];

        if ($_FILES['image']['name']) {
            $image = $_FILES['image']['name'];
            $tmp = $_FILES['image']['tmp_name'];
            move_uploaded_file($tmp, "uploads/" . $image);
        } else {
            $image = $_POST['existing_image'] ?? '';
        }

        if ($id) {
            $conn->query("UPDATE latestproducts SET product_name='$product_name', description='$description', image='$image', net_price='$net_price', selling_price='$selling_price', discount_bar='$discount_bar', category='$category' WHERE id=$id");
        } else {
            $conn->query("INSERT INTO latestproducts (product_name, description, image, net_price, selling_price, discount_bar, category) VALUES ('$product_name', '$description', '$image', '$net_price', '$selling_price', '$discount_bar', '$category')");
        }

        header("Location: LatestProducts.php");
        exit;
    }

    // If Edit
    $edit = false;
    $product = ['id' => '', 'product_name' => '', 'description' => '', 'image' => '', 'net_price' => '', 'selling_price' => '', 'discount_bar' => '', 'category' => ''];

    if (isset($_GET['edit'])) {
        $edit = true;
        $id = $_GET['edit'];
        $res = $conn->query("SELECT * FROM latestproducts WHERE id=$id");
        $product = $res->fetch_assoc();
    }
    ?>

    <h2 id="form-title"><?= $edit ? 'Edit Product' : 'Add New Product' ?></h2>

    <form method="POST" enctype="multipart/form-data" id="product-form" oninput="calculateSellingPrice()">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <input type="hidden" name="existing_image" value="<?= $product['image'] ?>">

        <label>Product Name:</label><br>
        <input type="text" name="product_name" value="<?= $product['product_name'] ?>" required
            style="width:100%; padding:8px;"><br><br>

        <label>Description:</label><br>
        <input type="text" name="description" value="<?= $product['description'] ?>" required
            style="width:100%; padding:8px;"><br><br>

        <label>Product Image:</label><br>
        <input type="file" name="image"><br>
        <?php if ($edit && $product['image'])
            echo "<img src='uploads/{$product['image']}' height='60' style='margin-top:10px;'>"; ?><br><br>

        <label>Net Price (₹):</label><br>
        <input type="number" name="net_price" id="net-price" value="<?= $product['net_price'] ?>" required
            style="width:100%; padding:8px;"><br><br>

        <label>Discount (%):</label><br>
        <input type="number" name="discount_bar" id="discount-bar" value="<?= $product['discount_bar'] ?>" required
            style="width:100%; padding:8px;"><br><br>

        <label>Selling Price (₹):</label><br>
        <input type="number" name="selling_price" id="selling-price" value="<?= $product['selling_price'] ?>" readonly
            style="width:100%; padding:8px; background:#f1f1f1;"><br><br>

        <label>Category:</label><br>
        <input type="text" name="category" value="<?= $product['category'] ?>" required
            style="width:100%; padding:8px;"><br><br>

        <button type="submit" style="padding: 10px 20px;"><?= $edit ? 'Update' : 'Add' ?> Product</button>
    </form>

    <hr style="margin:40px 0;">

    <h2>Latest Products</h2>

    <table border="1" cellpadding="10" cellspacing="0" width="100%" style="border-collapse: collapse;">
        <thead>
            <tr style="background: #f4f4f4;">
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Net Price</th>
                <th>Discount (%)</th>
                <th>Selling Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM latestproducts ORDER BY id DESC");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td><img src='uploads/{$row['image']}' height='60'></td>
                    <td>{$row['product_name']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['category']}</td>
                    <td>₹{$row['net_price']}</td>
                    <td>{$row['discount_bar']}%</td>
                    <td>₹{$row['selling_price']}</td>
                    <td>
                        <a href='LatestProducts.php?edit={$row['id']}'>✏️ Edit</a> | 
                        <a href='LatestProducts.php?delete={$row['id']}' onclick='return confirm(\"Delete this product?\")'>❌ Delete</a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        function calculateSellingPrice() {
            let net = parseFloat(document.getElementById("net-price").value) || 0;
            let discount = parseFloat(document.getElementById("discount-bar").value) || 0;
            let selling = net - ((net * discount) / 100);
            document.getElementById("selling-price").value = selling.toFixed(2);
        }
    </script>
</div>
<!-- close container -->
</body>

</html>
<style>
    #container {
        padding: 30px;
        font-family: Arial, sans-serif;
        background-color: #fafafa;
        margin-left: 17%;
    }

    #form-title,
    #table-title {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    #product-form {
        max-width: 500px;
        background: #fff;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 40px;
    }

    #product-form label {
        font-weight: bold;
        color: #555;
    }

    #product-form input[type="text"],
    #product-form input[type="number"],
    #product-form input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #bbb;
        border-radius: 5px;
        margin-top: 5px;
    }

    #selling-price {
        background-color: #f1f1f1;
        cursor: not-allowed;
    }

    #submit-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 12px 25px;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s ease;
    }

    #submit-btn:hover {
        background-color: #0056b3;
    }

    #product-preview {
        margin-top: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    #products-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 40px;
        background-color: white;
    }

    #products-table th,
    #products-table td {
        text-align: left;
        padding: 12px 15px;
        border: 1px solid #ddd;
    }

    #products-table th {
        background-color: #f4f4f4;
        font-weight: bold;
        color: #333;
    }

    #products-table img {
        border-radius: 4px;
    }

    #products-table a {
        text-decoration: none;
        margin-right: 10px;
        color: #007bff;
    }

    #products-table a:hover {
        color: #d9534f;
    }

    #section-divider {
        border: none;
        height: 1px;
        background: #ccc;
    }
</style>