<?php include 'header.php'; ?>
<?php include '../includes/conn.php'; ?>

<?php
// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM featuredbrands WHERE id = $id");
    header("Location: FeaturedBrands.php");
    exit;
}

// Handle Form Submit (Insert or Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Handle Image Upload
    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "uploads/" . $image);
    } else {
        $image = $_POST['existing_image'];
    }

    if ($id) {
        $conn->query("UPDATE featuredbrands SET image='$image' WHERE id=$id");
    } else {
        $conn->query("INSERT INTO featuredbrands (image) VALUES ('$image')");
    }

    header("Location: FeaturedBrands.php");
    exit;
}

// Edit Mode
$edit = false;
$brand = ['id' => '', 'image' => ''];

if (isset($_GET['edit'])) {
    $edit = true;
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM featuredbrands WHERE id=$id");
    $brand = $res->fetch_assoc();
}
?>
<div id="container">
    <h2><?= $edit ? 'Edit Brand Image' : 'Add New Brand Image' ?></h2>

    <form method="POST" enctype="multipart/form-data" style="max-width: 400px; margin-bottom: 40px;">
        <input type="hidden" name="id" value="<?= $brand['id'] ?>">
        <input type="hidden" name="existing_image" value="<?= $brand['image'] ?>">

        <label>Brand Image:</label><br>
        <input type="file" name="image"><br>
        <?php if ($edit && $brand['image'])
            echo "<img src='uploads/{$brand['image']}' height='80' style='margin-top:10px;'>"; ?><br><br>

        <button type="submit"><?= $edit ? 'Update' : 'Add' ?> Brand</button>
    </form>

    <hr style="margin: 40px 0;">

    <h2>All Featured Brands</h2>

    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
        <?php
        $res = $conn->query("SELECT * FROM featuredbrands ORDER BY id DESC");
        while ($row = $res->fetch_assoc()) {
            echo "
    <div style='border: 1px solid #ccc; padding: 15px; border-radius: 8px;'>
        <img src='uploads/{$row['image']}' height='100' style='object-fit: contain;'><br><br>
        <a href='FeaturedBrands.php?edit={$row['id']}'>✏️ Edit</a> | 
        <a href='FeaturedBrands.php?delete={$row['id']}' onclick='return confirm(\"Are you sure?\")'>❌ Delete</a>
    </div>
    ";
        }
        ?>
    </div>

</div> <!-- close main-content -->

</div>
</body>

</html>

<style>
    #container{
        margin-left: 20%;
    }
</style>