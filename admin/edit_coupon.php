<?php
// filepath: c:\xampp\htdocs\medicare\admin\edit_coupon.php
include '../includes/conn.php';
include 'header.php';

// Initialize variables
$coupon_id = $_GET['id'] ?? 0;
$coupon = [];
$error = '';
$success = '';

// Fetch coupon details
if ($coupon_id > 0) {
    $query = "SELECT * FROM coupons WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $coupon_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $coupon = $result->fetch_assoc();
    } else {
        $error = "Coupon not found.";
    }
    $stmt->close();
} else {
    $error = "Invalid coupon ID.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $discount = trim($_POST['discount']);
    $expiry_date = trim($_POST['expiry_date']);
    $is_active = trim($_POST['is_active']);

    if (!empty($code) && !empty($discount) && !empty($expiry_date)) {
        $update_query = "UPDATE coupons SET code = ?, discount = ?, expiry_date = ?, is_active = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sdsii", $code, $discount, $expiry_date, $is_active, $coupon_id);

        if ($stmt->execute()) {
            // Redirect after successful update
            header("Location: CouponCode.php");
            exit();
        } else {
            $error = "Failed to update coupon. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "All fields are required.";
    }
}
?>

<div class="container mt-5" style="margin-left: 20%;">
    <h1>Edit Coupon</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($coupon)): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="code">Coupon Code</label>
                <input type="text" id="code" name="code" class="form-control" value="<?php echo htmlspecialchars($coupon['code']); ?>" required>
            </div>

            <div class="form-group">
                <label for="discount">Discount</label>
                <input type="number" id="discount" name="discount" class="form-control" value="<?php echo htmlspecialchars($coupon['discount']); ?>" required>
            </div>

            <div class="form-group">
                <label for="expiry_date">Expiry Date</label>
                <input type="date" id="expiry_date" name="expiry_date" class="form-control" value="<?php echo htmlspecialchars($coupon['expiry_date']); ?>" required>
                <script>
                    document.getElementById('expiry_date').min = new Date().toISOString().split('T')[0];
                </script>
            </div>

            <div class="form-group">
                <label for="is_active">Status</label>
                <select id="is_active" name="is_active" class="form-control" required>
                    <option value="1" <?php echo ($coupon['is_active'] == 1) ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo ($coupon['is_active'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Coupon</button>
        </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
