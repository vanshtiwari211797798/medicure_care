<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --success: #4cc9f0;
            --text-dark: #2b2d42;
            --text-medium: #4a4e69;
            --bg-light: #f8f9fa;
            --bg-white: #ffffff;
            --radius-lg: 16px;
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            color: var(--text-dark);
            line-height: 1.6;
        }

        .confirmation-container {
            max-width: 600px;
            width: 100%;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .confirmation-card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 60px 40px;
            text-align: center;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .confirmation-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
        }

        .confetti {
            position: absolute;
            width: 15px;
            height: 15px;
            background-color: var(--primary-light);
            opacity: 0;
        }

        .checkmark {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            position: relative;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(76, 201, 240, 0.1);
            animation: scaleIn 0.5s ease-out;
        }

        .checkmark::before {
            content: '';
            display: block;
            width: 30px;
            height: 60px;
            border: solid var(--success);
            border-width: 0 5px 5px 0;
            transform: rotate(45deg) scale(0);
            position: absolute;
            top: 8px;
            animation: checkmark 0.5s cubic-bezier(0.42, 0, 0.27, 1.55) 0.5s forwards;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 700;
            color: var(--primary);
            animation: fadeIn 0.8s ease-out 0.3s forwards;
            opacity: 0;
        }

        .order-details {
            margin: 30px 0;
            padding: 20px;
            background: var(--bg-light);
            border-radius: 12px;
            animation: fadeIn 0.8s ease-out 0.6s forwards;
            opacity: 0;
        }

        p {
            font-size: 1.1rem;
            color: var(--text-medium);
            margin-bottom: 15px;
            animation: fadeIn 0.8s ease-out 0.4s forwards;
            opacity: 0;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 16px 32px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-top: 30px;
            box-shadow: 0 4px 6px rgba(67, 97, 238, 0.15);
            animation: fadeIn 0.8s ease-out 0.8s forwards;
            opacity: 0;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(67, 97, 238, 0.2);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary svg {
            margin-right: 10px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        @keyframes checkmark {
            0% {
                transform: rotate(45deg) scale(0);
            }
            50% {
                transform: rotate(45deg) scale(1.2);
            }
            100% {
                transform: rotate(45deg) scale(1);
            }
        }

        @media (max-width: 768px) {
            .confirmation-card {
                padding: 40px 20px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .checkmark {
                width: 60px;
                height: 60px;
            }
            
            .checkmark::before {
                width: 20px;
                height: 40px;
                top: 6px;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-card">
            <!-- Confetti elements will be added by JavaScript -->
            <div class="checkmark"></div>
            <h1>Order Confirmed!</h1>
            <p>Thank you for your purchase. Your order has been successfully placed.</p>
            <div class="order-details">
                <p>We've sent a confirmation email with your order details.</p>
                <p>Your order will arrive in 3-5 business days.</p>
            </div>
            <a href="index.php" class="btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Back to Home
            </a>
        </div>
    </div>

    <script>
        // Create confetti effect
        function createConfetti() {
            const colors = ['#4361ee', '#4895ef', '#4cc9f0', '#3f37c9', '#f72585'];
            const card = document.querySelector('.confirmation-card');
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.top = -20 + 'px';
                confetti.style.transform = `rotate(${Math.random() * 360}deg)`;
                
                const size = Math.random() * 10 + 5;
                confetti.style.width = size + 'px';
                confetti.style.height = size + 'px';
                
                card.appendChild(confetti);
                
                // Animate each confetti piece
                const animationDuration = Math.random() * 3 + 2;
                confetti.style.animation = `fall ${animationDuration}s linear forwards`;
                
                // Start animation with slight delay
                setTimeout(() => {
                    confetti.style.opacity = '1';
                }, Math.random() * 500);
            }
        }

        // Add animation for confetti
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fall {
                0% {
                    transform: translateY(0) rotate(0deg);
                    opacity: 1;
                }
                100% {
                    transform: translateY(calc(100vh + 20px)) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Start confetti after a short delay
        setTimeout(createConfetti, 800);
    </script>
</body>
</html>