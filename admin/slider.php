<?php 
include 'header.php'; 
include '../includes/conn.php';

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slider'])) {
    $caption = $_POST['slider_title'];
    $image = $_FILES['slider_image'];

    $imageName = uniqid() . '-' . basename($image['name']);
    $targetDir = "uploads/";
    $targetFile = $targetDir . $imageName;

    // Ensure folder exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($image['tmp_name'], $targetFile)) {
        $insert = "INSERT INTO topbanner (caption, image) VALUES ('$caption', '$imageName')";
        mysqli_query($conn, $insert);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Get the image name to delete the file too
    $res = mysqli_query($conn, "SELECT image FROM topbanner WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
    $imagePath = 'uploads/' . $row['image'];

    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    mysqli_query($conn, "DELETE FROM topbanner WHERE id = $id");
    header("Location: slider.php");
    exit();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_slider'])) {
    $id = $_POST['slider_id'];
    $caption = $_POST['slider_title'];

    if (!empty($_FILES['slider_image']['name'])) {
        $image = $_FILES['slider_image'];
        $imageName = uniqid() . '-' . basename($image['name']);
        $targetDir = "uploads/";
        $targetFile = $targetDir . $imageName;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            // Delete old image
            $res = mysqli_query($conn, "SELECT image FROM topbanner WHERE id = $id");
            $row = mysqli_fetch_assoc($res);
            unlink($targetDir . $row['image']);

            $update = "UPDATE topbanner SET caption='$caption', image='$imageName' WHERE id=$id";
            mysqli_query($conn, $update);
        }
    } else {
        // Only caption update
        mysqli_query($conn, "UPDATE topbanner SET caption='$caption' WHERE id=$id");
    }

    header("Location: slider.php");
    exit();
}
?>

<div id="slider-container" style="padding: 20px;">
    <h1 id="slider-heading">Manage Sliders</h1>

    <!-- Add Slider Form -->
    <h3 id="add-slider-heading">Add New Slider</h3>
    <form id="add-slider-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="add_slider" value="1">

        <div id="form-group-title">
            <label for="slider_title">Slider Title:</label>
            <input type="text" name="slider_title" id="slider_title" required>
        </div>

        <div id="form-group-image">
            <label for="slider_image">Slider Image:</label>
            <input type="file" name="slider_image" id="slider_image" accept="image/*" required>
        </div>

        <button type="submit" id="add-slider-button">Add Slider</button>
    </form>

    <!-- List of Sliders -->
    <h3 id="existing-sliders-heading">Existing Sliders</h3>
    <table id="slider-table" border="1" cellpadding="10" cellspacing="0">
        <thead id="slider-table-head">
            <tr>
                <th>ID</th>
                <th>Caption</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="slider-table-body">
            <?php
            $res = mysqli_query($conn, "SELECT * FROM topbanner ORDER BY id DESC");
            while ($row = mysqli_fetch_assoc($res)) {
            ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['caption'] ?></td>
                    <td><img src="uploads/<?= $row['image'] ?>" width="100" height="50"></td>
                    <td>
                        <form class="update-slider-form" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="update_slider" value="1">
                            <input type="hidden" name="slider_id" value="<?= $row['id'] ?>">
                            <input type="text" name="slider_title" value="<?= $row['caption'] ?>" required>
                            <input type="file" name="slider_image" accept="image/*">
                            <button type="submit">Update</button>
                        </form>
                        <a href="slider.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this slider?')" class="delete-link">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

