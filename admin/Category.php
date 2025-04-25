<?php 
include 'header.php';
include '../includes/conn.php';

// Handle Add / Update Category
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoryName = $_POST['category_name'];
    $imageUploaded = isset($_FILES['category_image']) && $_FILES['category_image']['error'] === 0;
    $imageName = $imageUploaded ? uniqid() . '-' . $_FILES['category_image']['name'] : null;
    $imageTmp = $imageUploaded ? $_FILES['category_image']['tmp_name'] : null;
    $uploadPath = 'uploads/' . $imageName;

    if (isset($_POST['update_id'])) {
        // Update logic
        $id = $_POST['update_id'];

        if ($imageUploaded && move_uploaded_file($imageTmp, $uploadPath)) {
            // Update with new image
            $stmt = $conn->prepare("UPDATE categories SET name = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssi", $categoryName, $imageName, $id);
        } else {
            // Update without new image
            $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $categoryName, $id);
        }

        $stmt->execute();
        $stmt->close();
        echo "<div class='alert success'>Category updated successfully!</div>";
    } else {
        // Add logic
        if ($imageUploaded && move_uploaded_file($imageTmp, $uploadPath)) {
            $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
            $stmt->bind_param("ss", $categoryName, $imageName);
            $stmt->execute();
            $stmt->close();
            echo "<div class='alert success' style='margin-left: 20%; margin-top: 2%;'>Category added successfully!</div>";
        } else {
            echo "<div class='alert error' style='margin-left: 20%; margin-top: 2%; '>Failed to upload image.</div>";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    // Optional: remove image file too
    $imageResult = $conn->query("SELECT image FROM categories WHERE id = $deleteId");
    $img = $imageResult->fetch_assoc();
    if ($img && file_exists('uploads/' . $img['image'])) {
        unlink('uploads/' . $img['image']);
    }
    $conn->query("DELETE FROM categories WHERE id = $deleteId");
    echo "<div class='alert error' style='margin-left: 20%; margin-top: 2%; '>Category deleted successfully!</div>";
}

// Edit Mode (fetch data to fill the form)
$editData = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $editQuery = $conn->query("SELECT * FROM categories WHERE id = $editId");
    $editData = $editQuery->fetch_assoc();
}
?>



<div class="category-container">
    <h1 class="page-title">Manage Categories</h1>

    <!-- Add / Edit Form -->
    <form method="POST" enctype="multipart/form-data" class="category-form">
        <div class="form-group">
            <label class="form-label">Category Name:</label>
            <input type="text" name="category_name" value="<?= $editData['name'] ?? '' ?>" class="form-input" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Category Image:</label>
            <input type="file" name="category_image" accept="image/*" class="form-input">
        </div>
        
        <div class="form-group">
            <?php if ($editData): ?>
                <input type="hidden" name="update_id" value="<?= $editData['id'] ?>">
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="category.php" class="cancel-link">Cancel</a>
            <?php else: ?>
                <button type="submit" class="btn btn-primary">Add Category</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Show Categories in Cards -->
    <div class="categories-grid">
        <?php
        $result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
        
        while ($row = $result->fetch_assoc()) {
            echo "
            <div class='category-card'>
                <img src='uploads/{$row['image']}' class='category-image'>
                <div class='category-name'>{$row['name']}</div>
                <div class='category-actions'>
                    <a href='?edit={$row['id']}' class='action-link'>Edit</a>
                    <a href='?delete={$row['id']}' onclick='return confirm(\"Are you sure?\")' class='action-link delete-link'>Delete</a>
                </div>
            </div>";
        }
        ?>
    </div>
</div>