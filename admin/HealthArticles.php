<?php include 'header.php'; ?>
<?php include '../includes/conn.php'; ?>

<?php
// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM healtharticles WHERE id = $id");
    header("Location: HealthArticles.php");
    exit;
}

// Handle form submit (Insert or Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $heading = $_POST['heading'];
    $para = $_POST['para'];

    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "uploads/" . $image);
    } else {
        $image = $_POST['existing_image'];
    }

    if ($id) {
        $conn->query("UPDATE healtharticles SET heading='$heading', para='$para', image='$image' WHERE id=$id");
    } else {
        $conn->query("INSERT INTO healtharticles (heading, para, image) VALUES ('$heading', '$para', '$image')");
    }

    header("Location: HealthArticles.php");
    exit;
}

$edit = false;
$article = ['id' => '', 'heading' => '', 'para' => '', 'image' => ''];

if (isset($_GET['edit'])) {
    $edit = true;
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM healtharticles WHERE id=$id");
    $article = $res->fetch_assoc();
}
?>

<!-- Style -->
<style>
    #container{        
        margin-left:20%;
    }
    
    #article-form-container {
        max-width: 600px;
        margin-bottom: 40px;
        background: #fff;
        border: 1px solid #ddd;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0 6px rgba(0, 0, 0, 0.05);
    }

    #article-form-container label {
        font-weight: bold;
        color: #444;
    }

    #article-form-container input[type="text"],
    #article-form-container textarea,
    #article-form-container input[type="file"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #bbb;
        border-radius: 6px;
    }

    #article-form-container button {
        margin-top: 10px;
        background-color: #007bff;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
    }

    #article-form-container button:hover {
        background-color: #0056b3;
    }

    #preview-image {
        margin-top: 10px;
        border-radius: 6px;
    }

    #article-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .article-card {
        width: 300px;
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
    }

    .article-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }

    .article-card h3 {
        margin: 10px 0 5px;
        font-size: 18px;
        color: #333;
    }

    .article-card p {
        font-size: 14px;
        color: #555;
    }

    .article-card a {
        text-decoration: none;
        margin-right: 10px;
        color: #007bff;
        font-weight: bold;
    }

    .article-card a:hover {
        color: #dc3545;
    }

    #divider {
        margin: 40px 0;
        border: 0;
        height: 1px;
        background: #ccc;
    }
</style>

<!-- close main-content -->
<div id="container">
    <!-- Title -->
    <h2 id="form-heading"><?= $edit ? 'Edit Article' : 'Add New Health Article' ?></h2>

    <!-- Form -->
    <form id="article-form-container" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="article-id" value="<?= $article['id'] ?>">
        <input type="hidden" name="existing_image" id="existing-image" value="<?= $article['image'] ?>">

        <label for="heading-input">Heading:</label><br>
        <input type="text" id="heading-input" name="heading" value="<?= $article['heading'] ?>" required><br><br>

        <label for="para-input">Paragraph:</label><br>
        <textarea id="para-input" name="para" required><?= $article['para'] ?></textarea><br><br>

        <label for="image-upload">Image:</label><br>
        <input type="file" id="image-upload" name="image"><br>
        <?php if ($edit && $article['image'])
            echo "<img id='preview-image' src='uploads/{$article['image']}' height='80'>"; ?><br><br>

        <button type="submit" id="submit-btn"><?= $edit ? 'Update' : 'Add' ?> Article</button>
    </form>

    <hr id="divider">

    <!-- Title -->
    <h2 id="articles-title">All Health Articles</h2>

    <!-- Article Cards -->
    <div id="article-cards">
        <?php
        $res = $conn->query("SELECT * FROM healtharticles ORDER BY id DESC");
        while ($row = $res->fetch_assoc()) {
            echo "
    <div class='article-card'>
        <img src='uploads/{$row['image']}' alt='Health Image'>
        <h3>{$row['heading']}</h3>
        <p>{$row['para']}</p>
        <a href='HealthArticles.php?edit={$row['id']}'>✏️ Edit</a> | 
        <a href='HealthArticles.php?delete={$row['id']}' onclick='return confirm(\"Are you sure?\")'>❌ Delete</a>
    </div>";
        }
        ?>
    </div>

</div>
</div>
</body>

</html>