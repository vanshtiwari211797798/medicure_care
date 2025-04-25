<?php
session_start();
include("includes/conn.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userEmail = $_SESSION['user'];
$query = "SELECT * FROM admin_users WHERE email='$userEmail'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Initialize feedback message
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);

    if (!empty($phone) && !empty($location) && !empty($dob)) {
         $insert = "INSERT INTO profile_edits (user_email, phone, location, dob, updated_at) 
        VALUES ('$userEmail', '$phone', '$location', '$dob', NOW())";

        if (mysqli_query($conn, $insert)) {
            $message = "Profile updated successfully.";
        } else {
            $message = "Failed to update profile: " . mysqli_error($conn);
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
</head>
<body>
<h2>Edit Profile</h2>

<?php if ($message != ""): ?>
    <p style="color: red;"><?= $message ?></p>
<?php endif; ?>

<form method="POST">
    <label>Phone:</label><br>
    <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" required><br><br>

    <label>Location:</label><br>
    <input type="text" name="location" value="<?= htmlspecialchars($row['location'] ?? '') ?>" required><br><br>

    <label>Date of Birth:</label><br>
    <input type="date" name="dob" value="<?= htmlspecialchars($row['dob'] ?? '') ?>" required><br><br>

    <button type="submit">Save Changes</button>
</form>
</body>
</html>
