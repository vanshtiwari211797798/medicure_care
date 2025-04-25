<?php include 'header.php'; ?>
<?php include '../includes/conn.php'; ?>

<?php
// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM dealsoftheday WHERE id = $id");
    header("Location: dealsoftheday.php");
    exit;
}

// Handle Insert or Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $productname = $_POST['productname'];
    $aboutproduct = $_POST['aboutproduct'];
    $netprice = $_POST['netprice'];
    $sellingprice = $_POST['sellingprice'];
    $discount = $_POST['discount'];

    // Image upload
    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "uploads/" . $image);
    } else {
        $image = $_POST['existing_image'];
    }

    if ($id) {
        $conn->query("UPDATE dealsoftheday SET 
            image='$image', 
            product_name='$productname', 
            description='$aboutproduct', 
            netprice='$netprice', 
            sale_price='$sellingprice', 
            discount='$discount' 
            WHERE id=$id");
    } else {
        $conn->query("INSERT INTO dealsoftheday 
            (image, product_name, description, netprice, sale_price, discount) 
            VALUES 
            ('$image', '$productname', '$aboutproduct', '$netprice', '$sellingprice', '$discount')");
    }

    header("Location: dealsoftheday.php");
    exit;
}

// Edit Mode
$edit = false;
$deal = [
    'id' => '',
    'image' => '',
    'product_name' => '',
    'description' => '',
    'netprice' => '',
    'sale_price' => '',
    'discount' => ''
];

if (isset($_GET['edit'])) {
    $edit = true;
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM dealsoftheday WHERE id = $id");
    $deal = $res->fetch_assoc();
}
?>

<div id="container">
    <h2 id="form-title"><?= $edit ? 'Edit Deal of the Day' : 'Add New Deal of the Day' ?></h2>

    <form method="POST" enctype="multipart/form-data" id="deal-form">
        <input type="hidden" name="id" id="deal-id" value="<?= $deal['id'] ?>">
        <input type="hidden" name="existing_image" id="existing-image" value="<?= $deal['image'] ?>">

        <label for="productname">Product Name:</label>
        <input type="text" name="productname" id="productname" value="<?= $deal['product_name'] ?>" required>

        <label for="aboutproduct">About Product:</label>
        <textarea name="aboutproduct" id="aboutproduct" required><?= $deal['description'] ?></textarea>

        <label for="image">Image:</label>
        <input type="file" name="image" id="image">
        <?php if ($edit && $deal['image'])
            echo "<img src='uploads/{$deal['image']}' id='preview-image' height='80'>"; ?>

        <label for="netprice">Net Price (₹):</label>
        <input type="number" name="netprice" id="netprice" value="<?= $deal['netprice'] ?>" step="0.01" required>

        <label for="discount">Discount (%):</label>
        <input type="number" name="discount" id="discount" value="<?= $deal['discount'] ?>" step="0.01" required>

        <label for="sellingprice">Selling Price (₹):</label>
        <input type="number" name="sellingprice" id="sellingprice" value="<?= $deal['sale_price'] ?>" step="0.01"
            readonly>

        <button type="submit" id="submit-btn"><?= $edit ? 'Update' : 'Add' ?> Deal</button>
    </form>

    <hr id="section-divider">
    <h2 id="deal-list-title">All Deals of the Day</h2>

    <div id="deal-list">
        <?php
        $res = $conn->query("SELECT * FROM dealsoftheday ORDER BY id DESC");
        while ($row = $res->fetch_assoc()) {
            echo "
    <div class='deal-card' id='deal-{$row['id']}'>
        <img src='uploads/{$row['image']}' class='deal-image'>
        <h3 class='deal-title'>{$row['product_name']}</h3>
        <p class='deal-description'>{$row['description']}</p>
        <p class='deal-net'><strong>Net Price:</strong> ₹{$row['netprice']}</p>
        <p class='deal-discount'><strong>Discount:</strong> {$row['discount']}%</p>
        <p class='deal-sale'><strong>Selling Price:</strong> ₹{$row['sale_price']}</p>
        <a href='dealsoftheday.php?edit={$row['id']}' class='edit-link'>✏️ Edit</a> | 
        <a href='dealsoftheday.php?delete={$row['id']}' class='delete-link' onclick='return confirm(\"Are you sure?\")'>❌ Delete</a>
    </div>
    ";
        }
        ?>
    </div>
</div>

<script>
    document.getElementById('netprice').addEventListener('input', calculateSellingPrice);
    document.getElementById('discount').addEventListener('input', calculateSellingPrice);

    function calculateSellingPrice() {
        const net = parseFloat(document.getElementById('netprice').value) || 0;
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const discountAmount = (net * discount) / 100;
        const sellingPrice = net - discountAmount;
        document.getElementById('sellingprice').value = sellingPrice.toFixed(2);
    }
</script>

</div> <!-- close main-content -->
</body>

</html>
<style>
    /* Form Styling */
    #container{
        margin-left: 20%;
    }
    #deal-form {
        max-width: 600px;
        margin-bottom: 40px;
        background: #fff;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    #deal-form label {
        display: block;
        font-weight: bold;
        margin-top: 15px;
        margin-bottom: 5px;
    }

    #deal-form input[type="text"],
    #deal-form input[type="number"],
    #deal-form textarea,
    #deal-form input[type="file"],
    #deal-form input[readonly] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 14px;
    }

    #deal-form input[readonly] {
        background-color: #eee;
    }

    #deal-form button {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    #deal-form button:hover {
        background-color: #0056b3;
    }

    #preview-image {
        margin-top: 10px;
        height: 80px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    /* Divider */
    #section-divider {
        margin: 40px 0;
        border: none;
        border-top: 1px solid #ccc;
    }

    /* Deal Cards */
    #deal-list {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .deal-card {
        width: 300px;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
    }

    .deal-card:hover {
        transform: translateY(-3px);
    }

    .deal-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .deal-title {
        font-size: 18px;
        margin: 0 0 5px;
    }

    .deal-description {
        font-size: 14px;
        color: #555;
        margin-bottom: 10px;
    }

    .deal-net,
    .deal-discount,
    .deal-sale {
        font-size: 14px;
        margin: 4px 0;
    }

    .edit-link,
    .delete-link {
        font-size: 14px;
        text-decoration: none;
        color: #007bff;
        margin-right: 10px;
    }

    .delete-link {
        color: #dc3545;
    }

    .edit-link:hover,
    .delete-link:hover {
        text-decoration: underline;
    }
</style>