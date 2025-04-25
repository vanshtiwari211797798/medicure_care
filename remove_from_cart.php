<?php
session_start();
include("includes/conn.php");

if (!isset($_SESSION['user']) || !isset($_GET['id'])) {
    header("Location: viewcart.php");
    exit;
}

$id = intval($_GET['id']);
$sql = "DELETE FROM cart WHERE id = $id";
mysqli_query($conn, $sql);

header("Location: viewcart.php");
?>
