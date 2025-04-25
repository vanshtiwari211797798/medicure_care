<?php

session_start();
include 'includes/conn.php'; 
// Initialize variables
$email = '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        // Check if the email exists in the database
        $query = "SELECT * FROM admin_users WHERE email = ?";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error preparing statement: $conn->error");
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate a 6-digit OTP
            $otp = rand(100000, 999999);

            // Store the OTP in the session for verification
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;

            // Send the OTP to the user's email
            $to = $email;
            $subject = "Password Reset OTP";
            $message = "Your OTP for password reset is: $otp";
            $headers = "From: no-reply@medicare.com";

            if (mail($to, $subject, $message, $headers)) {
                $success = "An OTP has been sent to your email. Please check your inbox.";
            } else {
                $error = "Failed to send OTP. Please try again later.";
            }
        } else {
            $error = "No account found with this email address.";
        }

        $stmt->close();
    } else {
        $error = "Please enter your email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Account Recovery</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --error-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #adb5bd;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 24px;
            font-size: 28px;
            font-weight: 600;
        }

        .description {
            color: var(--gray-color);
            margin-bottom: 30px;
            font-size: 15px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
            font-size: 14px;
        }

        input[type="email"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input[type="email"]:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .success {
            background-color: rgba(76, 201, 240, 0.1);
            color: #1e96fc;
            padding: 14px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid var(--success-color);
            text-align: left;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }

        .error {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--error-color);
            padding: 14px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid var(--error-color);
            text-align: left;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .back-to-login {
            margin-top: 25px;
            font-size: 14px;
            color: var(--gray-color);
        }

        .back-to-login a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .back-to-login a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .illustration {
            margin-bottom: 30px;
        }

        .illustration svg {
            width: 180px;
            height: auto;
        }

        @media (max-width: 576px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="illustration">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
        </div>
        
        <h1>Forgot Password?</h1>
        <p class="description">Enter your email address and we'll send you a one-time password (OTP) to reset your password.</p>
        
        <form method="POST" action="forgot-password.php">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="your@email.com">
            </div>
            <button type="submit">Send OTP</button>
        </form>

        <?php if (!empty($success)): ?>
            <div class="success">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="error">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <div class="back-to-login">
            Remember your password? <a href="login.php">Sign in</a>
        </div>
    </div>
</body>
</html>