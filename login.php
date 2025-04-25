<?php
session_start(); 
include("includes/conn.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin_users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['email']; // ✅ Store email
            header("Location: profile.php"); // ✅ Redirect before any output
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}

// ✅ Only after header logic is done, include HTML
include("includes/header.php");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - MediCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary: #4CAF50;
            --primary-dark: #388E3C;
            --primary-light: #C8E6C9;
            --secondary: #2196F3;
            --white: #ffffff;
            --light: #f5f5f5;
            --dark: #2c3e50;
            --gray: #95a5a6;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-light), var(--white));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: var(--dark);
            overflow-x: hidden;
        }
        
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-box {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .login-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            z-index: 2;
        }
        
        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--dark);
            font-size: 28px;
            font-weight: 600;
            position: relative;
        }
        
        .login-box h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }
        
        .login-box input[type="email"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 15px 20px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            background-color: var(--light);
            transition: all 0.3s ease;
            color: var(--dark);
        }
        
        .login-box input:focus {
            border-color: var(--primary);
            background-color: var(--white);
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            transition: all 0.3s ease;
        }
        
        .login-box input:focus + .input-icon {
            color: var(--primary);
        }
        
        .login-box button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .login-box button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }
        
        .login-box button:active {
            transform: translateY(0);
        }
        
        .login-box button::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255,255,255,0.1), rgba(255,255,255,0.3));
            transform: translateX(-100%);
            transition: transform 0.4s ease;
        }
        
        .login-box button:hover::after {
            transform: translateX(100%);
        }
        
        .login-box .error {
            margin: 20px 0;
            color: #b30000;
            background: #ffdddd;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            animation: shake 0.5s ease-in-out;
        }
        
        .login-box p {
            text-align: center;
            margin-top: 25px;
            color: var(--gray);
        }
        
        .login-box a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }
        
        .login-box a:hover {
            color: var(--primary-dark);
        }
        
        .login-box a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }
        
        .login-box a:hover::after {
            width: 100%;
        }
        
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(76, 175, 80, 0.1);
            animation: float 15s infinite linear;
        }
        
        .shape:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 60%;
            left: 80%;
            animation-delay: 3s;
            animation-duration: 20s;
        }
        
        .shape:nth-child(3) {
            width: 70px;
            height: 70px;
            top: 80%;
            left: 20%;
            animation-delay: 5s;
            animation-duration: 25s;
        }
        
        .shape:nth-child(4) {
            width: 120px;
            height: 120px;
            top: 30%;
            left: 70%;
            animation-delay: 7s;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        @media (max-width: 480px) {
            .login-box {
                padding: 30px 20px;
                margin: 20px;
            }
            
            .login-box h2 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="login-container">
        <div class="login-box animate__animated animate__fadeIn">
            <h2>Welcome Back</h2>
            <form method="POST">
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email Address" required>
                    <span class="input-icon">✉️</span>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                    <span class="input-icon">🔒</span>
                </div>
                <button type="submit" class="animate__animated animate__pulse animate__infinite animate__slower">Login</button>
            </form>

            <?php if ($error): ?>
                <div class="error animate__animated animate__shakeX"><?= $error ?></div>
            <?php endif; ?>

            <p>Don't have an account? <a href="register.php">Create one</a></p>
            <p><a href="forgot-password.php">Forgot password?</a></p>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>
    
    <script>
        // Add animation to input fields on focus
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', (e) => {
                e.target.parentNode.classList.add('animate__animated', 'animate__pulse');
                setTimeout(() => {
                    e.target.parentNode.classList.remove('animate__animated', 'animate__pulse');
                }, 1000);
            });
        });
    </script>
</body>
</html>
