<?php 
include 'header.php';
include '../includes/conn.php';

// Handle banner upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    if ($image) {
        $uniqueName = uniqid() . '-' . $image;
        move_uploaded_file($tmp, "uploads/" . $uniqueName);

        $conn->query("INSERT INTO banner (image) VALUES ('$uniqueName')");
        echo "<p class='success-msg'>Banner uploaded successfully!</p>";
    }
}

// Handle delete banner
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT image FROM banner WHERE id = $id");
    $row = $result->fetch_assoc();

    if (!empty($row['image']) && file_exists("uploads/" . $row['image'])) {
        unlink("uploads/" . $row['image']);
    }

    $conn->query("DELETE FROM banner WHERE id = $id");
    echo "<p class='error-msg'>Banner deleted successfully!</p>";
}
?>

<div class="container">
    <h1 class="page-title">📢 Manage Banners</h1>

    <!-- Upload Form -->
    <form class="upload-form" action="" method="POST" enctype="multipart/form-data">
        <label for="image">Select Banner Image:</label>
        <input type="file" name="image" id="image" required>
        <button type="submit">Upload</button>
    </form>

    <!-- Display All Banners -->
    <h3 class="section-title">🖼 Current Banners</h3>
    <div class="banner-list">
        <?php
        $result = $conn->query("SELECT * FROM banner ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            echo "
            <div class='banner-card'>
                <img src='uploads/{$row['image']}' alt='Banner'>
                <a class='delete-btn' href='?delete={$row['id']}' onclick=\"return confirm('Delete this banner?');\">Delete</a>
            </div>
            ";
        }
        ?>
    </div>
</div>
</body>
</html>


<style>
    /* General Container */
.container {
    padding: 30px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f9f9f9;
    margin-left: 17%;
}

/* Titles */
.page-title {
    color: #333;
    font-size: 28px;
    margin-bottom: 25px;
}

.section-title {
    margin-top: 40px;
    margin-bottom: 20px;
    color: #444;
}

/* Success & Error Messages */
.success-msg {
    color: #28a745;
    background: #eaf9ed;
    padding: 10px 15px;
    border-left: 4px solid #28a745;
    margin-bottom: 20px;
    border-radius: 5px;
}

.error-msg {
    color: #dc3545;
    background: #fdecec;
    padding: 10px 15px;
    border-left: 4px solid #dc3545;
    margin-bottom: 20px;
    border-radius: 5px;
}

/* Upload Form */
.upload-form {
    max-width: 400px;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.upload-form label {
    display: block;
    margin-bottom: 10px;
    color: #555;
}

.upload-form input[type="file"] {
    margin-bottom: 20px;
}

.upload-form button {
    background: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.upload-form button:hover {
    background: #0056b3;
}

/* Banner Display Cards */
.banner-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.banner-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.1);
    padding: 15px;
    width: 250px;
    text-align: center;
    position: relative;
}

.banner-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 6px;
}

.delete-btn {
    display: inline-block;
    margin-top: 10px;
    color: #dc3545;
    text-decoration: none;
    font-weight: bold;
}

.delete-btn:hover {
    text-decoration: underline;
}

</style>