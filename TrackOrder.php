<?php
ob_start();
session_start();
include 'includes/conn.php';

// Check if order_id is provided and is a valid integer
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid Order ID.");
}

$order_id = intval($_GET['order_id']); // Ensure the order_id is an integer

// SQL query to fetch tracking details for the given order ID
$sql = "SELECT delivered_by, status FROM orders WHERE id = $order_id";
$result = $conn->query($sql);

if (!$result) {
    die("SQL query failed: {$conn->error}");
}

// Check if the order exists
if ($result->num_rows === 0) {
    die("Order not found.");
}

$row = $result->fetch_assoc();
$delivered_by = strtolower(trim($row['delivered_by'])); // sanitize
$order_status = $row['status']; // Get order status from the 'status' column

// If 'delivered_by' is empty or not assigned and the order is in processing
if (empty($delivered_by) && $order_status === 'Processing') {
    // Show JavaScript alert for processing status and redirect to 'myorders.php'
    echo '<script>
            alert("Your order is currently being processed. Please contact support or wait until the order is shipped.");
            window.location.href = "/myorders.php"; // Redirect to the myorders page
          </script>';
    exit(); // Stop further execution
}

// Redirect based on 'delivered_by' tracking service
switch ($delivered_by) {
    case 'track on':
    case 'trackon':
        header("Location: https://uat.trackon.in/Tracking/t2/MultipleTracking");
        break;

    case 'dtdc':
        header("Location: https://www.dtdc.in/tracking.asp");
        break;

    case 'bluedart':
        header("Location: https://www.bluedart.com/tracking");
        break;

    case 'delhivery':
        header("Location: https://www.delhivery.com/tracking");
        break;

    case 'india post':
        header("Location: https://www.indiapost.gov.in/_layouts/15/dop.portal.tracking/trackconsignment.aspx");
        break;

    default:
        // Show an alert and redirect to 'myorders.php' if the tracking service is not assigned
        echo '<script>
                alert("Your order in the process.");
                window.location.href = "myorders.php"; // Redirect to the myorders page
                </script>';
        break;
}

exit();
