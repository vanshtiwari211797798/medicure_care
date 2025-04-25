<?php
include("includes/header.php");
?>
<div class="container mt-5">
    <div class="alert alert-danger text-center">
        <h2>Order Processing Error</h2>
        <p><?= htmlspecialchars($_GET['message'] ?? 'An unknown error occurred') ?></p>
        <a href="cart.php" class="btn btn-primary">Return to Cart</a>
        <a href="index.php" class="btn btn-secondary">Return to Home</a>
    </div>
</div>
<?php
include("includes/footer.php");
?>