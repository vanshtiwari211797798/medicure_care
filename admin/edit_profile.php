<?php
include('../includes/conn.php');
include("header.php");


$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $bio      = $_POST['bio'];
    $profile_image = $_POST['old_image'];

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
        $profile_image = $target_file;
    }

    $query = "UPDATE admins SET username=?, email=?, phone=?, bio=?, profile_image=?, updated_at=NOW() WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $username, $email, $phone, $bio, $profile_image, $id);
    $stmt->execute();

    header("Location: admin_profile.php");
    exit();
}

$query = "SELECT * FROM admins WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Admin Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .edit-profile-container {
            max-width: 700px;
            background: #fff;
            margin: 50px auto;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .edit-profile-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: 600;
            color: #444;
        }

        input[type="text"],
        input[type="email"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .current-image {
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .current-image img {
            width: 100px;
            height: auto;
            border-radius: 8px;
        }

        input[type="submit"] {
            background: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="edit-profile-container">
        <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($admin['phone']) ?>">

            <label>Bio:</label>
            <textarea name="bio"><?= htmlspecialchars($admin['bio']) ?></textarea>

            <label>Current Image:</label>
            <div class="current-image">
                <img src="<?= htmlspecialchars($admin['profile_image']) ?>" alt="Current Image">
            </div>

            <label>Upload New Image:</label>
            <input type="file" name="profile_image">

            <input type="hidden" name="old_image" value="<?= htmlspecialchars($admin['profile_image']) ?>">

            <input type="submit" value="Update Profile">
        </form>
    </div>
</body>
</html>
