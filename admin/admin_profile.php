<?php
include("../includes/conn.php");
include("header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --text-color: #34495e;
            --border-radius: 12px;
            --box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .profile-container {
            background: #fff;
            width: 100%;
            max-width: 800px;
            margin: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 2fr;
            transition: var(--transition);
        }
        
        .profile-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .profile-sidebar {
            background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
            padding: 40px 30px;
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: var(--transition);
        }
        
        .profile-image:hover {
            transform: scale(1.05);
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .profile-title {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 20px;
        }
        
        .profile-social {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: var(--transition);
        }
        
        .social-icon:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-3px);
        }
        
        .profile-content {
            padding: 40px;
        }
        
        .section-title {
            font-size: 1.2rem;
            color: var(--dark-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: var(--primary-color);
        }
        
        .info-item {
            margin-bottom: 15px;
            display: flex;
        }
        
        .info-label {
            font-weight: 500;
            color: var(--dark-color);
            min-width: 100px;
        }
        
        .info-value {
            flex: 1;
        }
        
        .edit-btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            margin-top: 20px;
            font-weight: 500;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .edit-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }
        
        .edit-btn i {
            margin-right: 8px;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
            
            .profile-sidebar {
                padding: 30px 20px;
            }
            
            .profile-content {
                padding: 30px;
            }
        }
    </style>
</head>
<body>

<?php
// Fetch the admin data (change id as needed)
$admin_id = 1; // or use WHERE username = 'admin' if preferred

$query = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<div class="profile-container">
    <div class="profile-sidebar">
        <img src="<?= htmlspecialchars($admin['profile_image']) ?>" alt="Admin Photo" class="profile-image">
        <h2 class="profile-name"><?= htmlspecialchars($admin['username']) ?></h2>
        <p class="profile-title">System Administrator</p>

        <div class="profile-social">
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
            <a href="mailto:<?= htmlspecialchars($admin['email']) ?>" class="social-icon"><i class="fas fa-envelope"></i></a>
        </div>
    </div>

    <div class="profile-content">
        <h3 class="section-title"><i class="fas fa-user-circle"></i> Personal Information</h3>

        <div class="info-item">
            <span class="info-label">Email:</span>
            <span class="info-value"><?= htmlspecialchars($admin['email']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">Phone:</span>
            <span class="info-value"><?= htmlspecialchars($admin['phone']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">Member Since:</span>
            <span class="info-value"><?= date("F j, Y", strtotime($admin['created_at'])) ?></span>
        </div>

        <h3 class="section-title" style="margin-top: 30px;"><i class="fas fa-info-circle"></i> About</h3>

        <div class="info-item">
            <span class="info-label">Bio:</span>
            <span class="info-value"><?= nl2br(htmlspecialchars($admin['bio'])) ?></span>
        </div>

        <a href="edit_profile.php?id=<?= $admin['id'] ?>" class="edit-btn"><i class="fas fa-edit"></i> Edit Profile</a>
    </div>
</div>


</body>
</html>

