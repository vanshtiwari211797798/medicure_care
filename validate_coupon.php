<?php
include("includes/conn.php");

header('Content-Type: application/json');

$coupon_code = $_POST['coupon_code'] ?? '';
$amount = floatval($_POST['amount'] ?? 0);

// Validate coupon exists and is applicable
$query = "SELECT id, code, discount, max_discount, discount_type, min_order_amount 
            FROM coupons 
            WHERE code = ? 
            AND is_active = 1 
            AND expiry_date >= CURDATE() 
            AND (min_order_amount IS NULL OR min_order_amount <= ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "sd", $coupon_code, $amount);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($coupon = mysqli_fetch_assoc($result)) {
    $response = [
        'success' => true,
        'message' => 'Coupon applied successfully!',
        'coupon_id' => $coupon['id'],
        'discount' => floatval($coupon['discount']),
        'max_discount' => floatval($coupon['max_discount'] ?? 0),
        'discount_type' => $coupon['discount_type'] ?? 'percentage'
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid coupon or minimum order amount not met'
    ];
}

echo json_encode($response);
?>