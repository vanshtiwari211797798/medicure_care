<?php
include '../includes/conn.php';
include 'header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coupon_code = $_POST['coupon_code'] ?? '';
    $discount = $_POST['discount'] ?? '';
    $type = $_POST['type'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';

    // Validate inputs
    if (!empty($coupon_code) && !empty($discount) && !empty($expiry_date)) {
        // Insert coupon into database (without created_at)
        $sql = "INSERT INTO coupons (code, discount,type, expiry_date, is_active) 
                VALUES (?, ?, ?,?, 1)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sdss", $coupon_code, $discount,$type, $expiry_date);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Coupon added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error adding coupon.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error preparing statement: {$conn->error}</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Coupon</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5" style="margin-left: 20%;">
        <h2>Add New Coupon</h2>
        <form method="POST">
            <div class="form-group">
                <input type="text" class="form-control" name="coupon_code" placeholder="Enter Coupon Code" required>
            </div>
            <div class="form-group">
                <input type="number" class="form-control" name="discount" placeholder="Enter Discount" required>
            </div>
            <div class="form-group">
                <select name="type" id="type">
                    <option value="">Select Coupon Type</option>
                    <option value="regular">Regular</option>
                    <option value="special">Special</option>
                </select>
            </div>
            <div class="form-group">
                <input type="date" class="form-control" name="expiry_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Coupon</button>
        </form>

        <hr>
        <h2>All Coupons</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Coupon Code</th>
                    <th>Discount</th>
                    <th>Type</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM coupons ORDER BY id DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status = $row['is_active'] ? 'Active' : 'Inactive';
                        $statusClass = $row['is_active'] ? 'text-success' : 'text-danger';
                        echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['code']}</td>
                        <td>{$row['discount']}</td>
                        <td>{$row['type']}</td>
                        <td>{$row['expiry_date']}</td>
                        <td class='{$statusClass}'>{$status}</td>
                        <td>
                            <a href='edit_coupon.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
                            <a href='delete_coupon.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                        </td>
                    </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No coupons found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>



</body>

</html>

<?php include 'footer.php'; ?>