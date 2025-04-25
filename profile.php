<?php
session_start();
include("includes/conn.php");
include("includes/header.php");

// Step 1: Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Step 2: Ab maan lein ki $_SESSION['user'] me sirf email string hai
$userEmail = $_SESSION['user']; // ✅ safe, no array use

// Step 3: Database query
$query = "SELECT * FROM admin_users WHERE email='$userEmail' AND role=0";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Step 4: Debugging (optional)
// echo "<pre>";
// var_dump($row);
// echo "</pre>";

// Step 5: Ab user data access kar sakte ho safely
?>


<style>
    :root {
        --primary-color: #4361ee;
        --primary-light: #eef2ff;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --danger-color: #f72585;
        --success-color: #4cc9f0;
        --light-color: #f8f9fa;
        --light-gray: #e9ecef;
        --medium-gray: #adb5bd;
        --dark-color: #212529;
        --text-muted: #6c757d;
        --border-radius: 12px;
        --border-radius-sm: 6px;
        --box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --box-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: #f9fafb;
        color: var(--dark-color);
        line-height: 1.6;
        margin: 0;
        padding: 0;
    }

    .profile-container {
        max-width: 900px;
        margin: 60px auto;
        background: #ffffff;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        position: relative;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        color: white;
        padding: 40px 50px 80px;
        position: relative;
        text-align: center;
    }

    .profile-header::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 50%;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: var(--box-shadow);
        margin-bottom: 20px;
        background-color: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: var(--primary-color);
        font-size: 48px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .profile-header h2 {
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 8px;
        letter-spacing: -0.5px;
    }

    .profile-header p {
        font-size: 16px;
        opacity: 0.9;
        margin: 0;
        font-weight: 400;
    }

    .profile-badge {
        position: absolute;
        top: 30px;
        right: 30px;
        background: white;
        color: var(--primary-color);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: var(--box-shadow-sm);
    }

    .profile-content {
        padding: 50px;
        margin-top: -30px;
    }

    .profile-section {
        margin-bottom: 40px;
    }

    .profile-section h3 {
        font-size: 18px;
        font-weight: 600;
        color: var(--dark-color);
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--light-gray);
        display: flex;
        align-items: center;
    }

    .profile-section h3 i {
        margin-right: 10px;
        color: var(--primary-color);
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .detail-card {
        background: var(--light-color);
        border-radius: var(--border-radius-sm);
        padding: 20px;
        transition: var(--transition);
    }

    .detail-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--box-shadow-sm);
    }

    .detail-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .detail-label i {
        margin-right: 8px;
        font-size: 14px;
    }

    .detail-value {
        font-size: 16px;
        font-weight: 500;
        color: var(--dark-color);
    }

    .profile-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-top: 40px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 14px 20px;
        background-color: var(--primary-color);
        color: white;
        border-radius: var(--border-radius-sm);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        text-align: center;
        border: none;
        cursor: pointer;
        font-size: 15px;
    }

    .action-btn:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
    }

    .action-btn i {
        margin-right: 10px;
        font-size: 16px;
    }

    .logout-btn {
        background-color: white;
        color: var(--danger-color);
        border: 1px solid var(--danger-color);
    }

    .logout-btn:hover {
        background-color: var(--danger-color);
        color: white;
        box-shadow: 0 4px 12px rgba(247, 37, 133, 0.2);
    }

    .edit-btn {
        position: absolute;
        top: 30px;
        left: 30px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
    }

    .edit-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(15deg);
    }

    /* Stats Section */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: var(--border-radius-sm);
        padding: 20px;
        text-align: center;
        box-shadow: var(--box-shadow-sm);
        transition: var(--transition);
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .profile-container {
            margin: 20px;
            border-radius: var(--border-radius-sm);
        }

        .profile-header {
            padding: 30px 20px 60px;
        }

        .profile-content {
            padding: 30px 20px;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            font-size: 36px;
        }

        .profile-header h2 {
            font-size: 24px;
        }

        .profile-actions {
            grid-template-columns: 1fr;
        }

        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .profile-header {
            padding: 25px 15px 50px;
        }

        .profile-badge, .edit-btn {
            top: 15px;
            right: 15px;
            font-size: 10px;
        }

        .edit-btn {
            left: 15px;
            width: 30px;
            height: 30px;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .stats-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-container">
    <div class="profile-header">
        <button class="edit-btn" title="Edit Profile">
            <i class="fas fa-pencil-alt"></i>
        </button>
        <span class="profile-badge">Active</span>
        
        <div class="profile-avatar">
            <?= strtoupper(substr($row['username'], 0, 1)) ?>
        </div>
        
        <h2><?= htmlspecialchars($row['username']) ?></h2>
        <p>Member since June 2023</p>
    </div>

    <div class="profile-content">
        <?php
        // Get total orders count
        $ordersQuery = "SELECT COUNT(*) as total_orders FROM orders WHERE email='$userEmail'";
        $ordersResult = mysqli_query($conn, $ordersQuery);
        $ordersCount = mysqli_fetch_assoc($ordersResult)['total_orders'];

        // Get total amount spent
        $spentQuery = "SELECT SUM(price * quantity) as total_spent FROM orders WHERE email='$userEmail'";
        $spentResult = mysqli_query($conn, $spentQuery);
        $totalSpent = mysqli_fetch_assoc($spentResult)['total_spent'] ?? 0;

        // Get wishlist count
        // $wishlistQuery = "SELECT COUNT(*) as wishlist_count FROM wishlist WHERE user_email='$userEmail'";
        // $wishlistResult = mysqli_query($conn, $wishlistQuery);
        // $wishlistCount = ($wishlistResult) ? mysqli_fetch_assoc($wishlistResult)['wishlist_count'] : 0;
        ?>
        <!-- <div class="stats-container">
            <div class="stat-card">
            <div class="stat-value"><?= $ordersCount ?></div>
            <div class="stat-label">Orders</div>
            </div>
            <div class="stat-card">
            <div class="stat-value">₹<?= number_format($totalSpent, 2) ?></div>
            <div class="stat-label">Total Spent</div>
            </div>
            <div class="stat-card">
            <div class="stat-value"><?= $wishlistCount ?></div>
            <div class="stat-label">Wishlist</div>
            </div>
        </div> -->

        <div class="profile-section">
            <h3><i class="fas fa-user-circle"></i> Personal Information</h3>
            <div class="detail-grid">
                <div class="detail-card">
                    <div class="detail-label"><i class="fas fa-envelope"></i> Email Address</div>
                    <div class="detail-value"><?= htmlspecialchars($row['email']) ?></div>
                </div>
                <div class="detail-card">
                    <div class="detail-label"><i class="fas fa-phone"></i> Contact Number</div>
                    <div class="detail-value"><?= htmlspecialchars($row['phone']) ?></div>
                </div>
                <div class="detail-card">
                    <div class="detail-label"><i class="fas fa-map-marker-alt"></i> Location</div>
                    <div class="detail-value"><?= htmlspecialchars($row['location']) ?></div>
                </div>
                
            </div>
        </div>

        <!-- <div class="profile-section">
            <h3><i class="fas fa-shield-alt"></i> Account Security</h3>
            <div class="detail-grid">
                <div class="detail-card">
                    <div class="detail-label"><i class="fas fa-lock"></i> Password</div>
                    <div class="detail-value">••••••••</div>
                </div>
                <div class="detail-card">
                    <div class="detail-label"><i class="fas fa-mobile-alt"></i> Two-Factor Auth</div>
                    <div class="detail-value">Not Enabled</div>
                </div>
                <div class="detail-card">
                    <div class="detail-label"><i class="fas fa-clock"></i> Last Login</div>
                    <div class="detail-value">2 hours ago</div>
                </div>
                <div class="detail-card">
                    <div class="detail-label"><i class="fas fa-globe"></i> Active Sessions</div>
                    <div class="detail-value">1 device</div>
                </div>
            </div>
        </div> -->

        <div class="profile-actions">
            <a class="action-btn" href="myorders.php">
                <i class="fas fa-shopping-bag"></i> My Orders
            </a>
            <a class="action-btn" href="viewcart.php">
                <i class="fas fa-shopping-cart"></i> My Cart
            </a>
            <!-- <a class="action-btn" href="#">
                <i class="fas fa-heart"></i> Wishlist
            </a> -->
            <a class="action-btn logout-btn" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>