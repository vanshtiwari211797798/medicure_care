<?php
include("includes/conn.php");
include("includes/header.php");

$message = '';
$messageColor = 'green';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $location = $_POST['location'];
    // $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    $check = mysqli_query($conn, "SELECT * FROM admin_users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Email already exists.";
        $messageColor = 'red';
    } else {
        $sql = "INSERT INTO admin_users (username, phone, email, password, location, dob, gender) 
                VALUES ('$username', '$phone', '$email', '$password', '$location', '$dob', '$gender')";
        if (mysqli_query($conn, $sql)) {
            $message = "Registration successful! <a href='login.php'>Login now</a>";
        } else {
            $message = "Something went wrong: " . mysqli_error($conn);
            $messageColor = 'red';
        }
    }
}
?>

<!-- HTML Registration Form -->
<title>Register - MediCare</title>

<style>
    #body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to right, #e3f2fd, #ffffff);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .register-box {
        background: #ffffff;
        padding: 40px 30px;
        border-radius: 15px;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .register-box h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #2c3e50;
        font-size: 24px;
    }

    .register-box input[type="text"],
    .register-box input[type="email"],
    .register-box input[type="password"],
    .register-box input[type="date"],
    .register-box select {
        width: 100%;
        padding: 12px 14px;
        margin-bottom: 18px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        background-color: #f9f9f9;
        transition: 0.2s ease;
    }

    .register-box input:focus,
    .register-box select:focus {
        border-color: #4CAF50;
        background-color: #fff;
        outline: none;
    }

    .register-box button {
        width: 100%;
        padding: 12px;
        background: #4CAF50;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .register-box button:hover {
        background: #45a049;
    }

    .message {
        margin-top: 20px;
        text-align: center;
        padding: 12px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 14px;
    }

    .message a {
        color: #fff;
        text-decoration: underline;
    }

    .form-row {
        display: flex;
        gap: 15px;
    }

    .form-row>div {
        flex: 1;
    }

    @media (max-width: 480px) {
        .register-box {
            padding: 30px 20px;
            margin: 20px;
        }

        .form-row {
            flex-direction: column;
            gap: 0;
        }
    }
</style>


<div id="body">
    <div class="register-box">
        <h2>Create Your Account</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Full Name" required>

            <div class="form-row">
                <div>
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>
                <div>
                    <input type="text" name="phone" placeholder="Phone Number" required>
                </div>
            </div>

            <input type="password" name="password" placeholder="Password" required>

            <div class="form-row">
                <div>
                    <input type="text" name="location" placeholder="City, Country" required>
                </div>
                <div>
                    <!-- <input type="date" name="dob" placeholder="Date of Birth" required> -->
                    <select name="gender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                        <option value="Prefer not to say">Prefer not to say</option>
                    </select>
                </div>
            </div>



            <button type="submit">Register</button>
            <p style="text-align: center; margin-top: 15px;">Already have an account? <a href="login.php"
                    style="color: #4CAF50; text-decoration: none; font-weight: 500;">Login here</a></p>
        </form>
        <?php if (!empty($message)): ?>
            <div class="message" style="background: <?= $messageColor === 'red' ? '#ffe5e5' : '#4CAF50'; ?>; 
                    color: <?= $messageColor === 'red' ? '#b30000' : '#fff'; ?>;">
                <?= $message ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>