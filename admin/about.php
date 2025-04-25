<?php
include('../includes/conn.php');
session_start();

$message = "";
$imagePath = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escape user inputs
    $title1 = mysqli_real_escape_string($conn, $_POST['title1']);
    $content1 = mysqli_real_escape_string($conn, $_POST['content1']);
    $title2 = mysqli_real_escape_string($conn, $_POST['title2']);
    $list1 = mysqli_real_escape_string($conn, $_POST['list_item1']);
    $list2 = mysqli_real_escape_string($conn, $_POST['list_item2']);
    $list3 = mysqli_real_escape_string($conn, $_POST['list_item3']);
    $list4 = mysqli_real_escape_string($conn, $_POST['list_item4']);
    $list5 = mysqli_real_escape_string($conn, $_POST['list_item5']);
    $title3 = mysqli_real_escape_string($conn, $_POST['title3']);
    $content2 = mysqli_real_escape_string($conn, $_POST['content2']);

    // Check for image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['image']['name']);
        $targetDir = "uploads/";

        // Create folder if not exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $newFilename = time() . "_" . $filename;
        $targetFile = $targetDir . $newFilename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        } else {
            $message = "Error uploading image.";
        }
    }

    // If no new image uploaded, fetch current image
    if (empty($imagePath)) {
        $getImage = mysqli_query($conn, "SELECT image FROM aboutus WHERE id = 1");
        $imgData = mysqli_fetch_assoc($getImage);
        $imagePath = $imgData['image'];
    }

    // Update the data
    $sql = "UPDATE aboutus SET 
                image = '$imagePath',
                title1 = '$title1',
                content1 = '$content1',
                title2 = '$title2',
                list_item1 = '$list1',
                list_item2 = '$list2',
                list_item3 = '$list3',
                list_item4 = '$list4',
                list_item5 = '$list5',
                title3 = '$title3',
                content2 = '$content2'
            WHERE id = 1";

    if (mysqli_query($conn, $sql)) {
        $message = "Content updated successfully!";
    } else {
        $message = "Update failed: " . mysqli_error($conn);
    }
}

// Fetch updated data
$result = mysqli_query($conn, "SELECT * FROM aboutus WHERE id = 1");
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit About Us Page</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Current Image</label><br>
            <?php if (!empty($data['image'])): ?>
                <img src="<?= htmlspecialchars($data['image']) ?>" width="150" class="mb-2"><br>
            <?php endif; ?>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label>Title 1</label>
            <input type="text" name="title1" class="form-control" value="<?= htmlspecialchars($data['title1']) ?>">
        </div>

        <div class="mb-3">
            <label>Content 1</label>
            <textarea name="content1" class="form-control"><?= htmlspecialchars($data['content1']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Title 2</label>
            <input type="text" name="title2" class="form-control" value="<?= htmlspecialchars($data['title2']) ?>">
        </div>

        <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="mb-3">
                <label>List Item <?= $i ?></label>
                <input type="text" name="list_item<?= $i ?>" class="form-control" value="<?= htmlspecialchars($data["list_item$i"]) ?>">
            </div>
        <?php endfor; ?>

        <div class="mb-3">
            <label>Title 3</label>
            <input type="text" name="title3" class="form-control" value="<?= htmlspecialchars($data['title3']) ?>">
        </div>

        <div class="mb-3">
            <label>Content 2</label>
            <textarea name="content2" class="form-control"><?= htmlspecialchars($data['content2']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
</body>
</html>
