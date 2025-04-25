<?php
include('../includes/conn.php');
include("header.php");

// Update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $hours = $_POST['hours'];

    $sql = "UPDATE contactinfo SET 
                email = '$email',
                phone = '$phone',
                address = '$address',
                hours = '$hours'
            WHERE id = 1";
    mysqli_query($conn, $sql);
    $message = "Contact information updated successfully!";
}

// Fetch data
$result = mysqli_query($conn, "SELECT * FROM contactinfo WHERE id = 1");
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Contact Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-5" style="margin-left: 20%;">
        <h2>Edit Contact Information</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>">
            </div>

            <div class="mb-3">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($data['phone']) ?>">
            </div>

            <div class="mb-3">
                <label>Address (use &lt;br&gt; for new lines)</label>
                <textarea name="address" class="form-control"><?= htmlspecialchars($data['address']) ?></textarea>
            </div>

            <div class="mb-3">
                <label>Business Hours (use &lt;br&gt; for new lines)</label>
                <textarea name="hours" class="form-control"><?= htmlspecialchars($data['hours']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Contact Info</button>
        </form>
    </div>
</body>

</html>