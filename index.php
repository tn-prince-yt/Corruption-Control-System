<?php
include 'includes/config.php';
include 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Corruption Control System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/navbar.php'; ?>

        <div class="main-content" style="margin: 60px 0; text-align: center;">
            <h1 style="color: var(--primary-color); font-size: 42px; margin-bottom: 20px;">Corruption Control System</h1>
            <p style="font-size: 18px; color: #666; margin-bottom: 40px;">
                A transparent online platform for reporting corruption complaints and tracking investigations
            </p>

            <div class="section" style="margin-bottom: 50px;">
                <h2 style="color: var(--primary-color); margin-bottom: 30px;">How to Get Started</h2>
                <div class="stats-grid" style="margin-bottom: 30px;">
                    <div class="stat-card" style="text-align: left; padding: 30px;">
                        <h3 style="font-size: 24px;">Citizens</h3>
                        <p style="margin-bottom: 20px;">Report corruption complaints and track their status</p>
                        <a href="auth/register.php" class="btn btn-primary">Register Now</a>
                        <a href="auth/login.php" class="btn btn-secondary" style="margin-left: 10px;">Login</a>
                    </div>
                    <div class="stat-card" style="text-align: left; padding: 30px;">
                        <h3 style="font-size: 24px;">Admin</h3>
                        <p style="margin-bottom: 20px;">Review complaints and assign investigation officers</p>
                        <a href="auth/login.php" class="btn btn-primary">Login as Admin</a>
                    </div>
                    <div class="stat-card" style="text-align: left; padding: 30px;">
                        <h3 style="font-size: 24px;">Officers</h3>
                        <p style="margin-bottom: 20px;">Investigate assigned complaints and submit reports</p>
                        <a href="auth/login.php" class="btn btn-primary">Login as Officer</a>
                    </div>
                </div>
            </div>

            <div class="info-box" style="max-width: 600px; margin: 0 auto; text-align: left;">
                <h3>Features</h3>
                <ul style="margin-left: 20px; color: var(--text-color);">
                    <li>Online complaint submission with evidence uploads</li>
                    <li>Real-time complaint status tracking</li>
                    <li>Admin review and officer assignment system</li>
                    <li>Investigation report submission</li>
                    <li>Comprehensive audit logging</li>
                    <li>Secure login authentication</li>
                    <li>Responsive mobile-friendly interface</li>
                </ul>
            </div>

            <div style="margin-top: 50px; padding: 30px; background-color: #f0f8ff; border-radius: 8px;">
                <h3 style="color: var(--primary-color); margin-bottom: 15px;">Demo Credentials</h3>
                <p style="font-size: 14px; margin-bottom: 10px;">
                    <strong>Admin Email:</strong> admin@corruption.com | <strong>Password:</strong> admin123
                </p>
                <p style="font-size: 14px;">
                    <strong>Officer Email:</strong> officer@corruption.com | <strong>Password:</strong> officer123
                </p>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
