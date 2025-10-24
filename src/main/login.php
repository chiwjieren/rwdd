<?php
// Start session before any output
session_start();

// Enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

$error = "";
$message = "";

// Check for signup success message
if (isset($_GET['signup']) && $_GET['signup'] == 'success') {
    $message = "Registration successful! Please log in.";
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields!";
    } else {
        // Check for admin credentials first
        if ($email === "admin" && $password === "admin123") {
            // Admin login
            $_SESSION['user_id'] = 0;
            $_SESSION['username'] = 'admin';
            $_SESSION['user_email'] = 'admin';
            $_SESSION['is_admin'] = true;
            $_SESSION['profile_image'] = '../media/default-avatar.png';
            
            // Redirect to admin dashboard
            header("Location: admin_dashboard.php");
            exit();
        }
        
        // Regular user login
        $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_password, user_profile_image FROM USER WHERE user_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['user_password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['user_name'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['is_admin'] = false;
                $_SESSION['profile_image'] = !empty($user['user_profile_image']) ? '../media/profiles/' . $user['user_profile_image'] : '../media/default-avatar.png';
                
                // Redirect to home page
                header("Location: index.php");
                exit();
            } 
            
            else {
                $error = "Invalid email or password!";
            }
        } 
        
        else {
            $error = "Invalid email or password!";
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - GoGreenTogether</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
        include 'header.php';
        // If user is already logged in, redirect to home page
        if (isLoggedIn()) {
            header("Location: index.php");
            exit();
        }
    ?>

    <main class="container auth-container">
        <div class="auth-box">
            <h1>Welcome Back</h1>
            <p class="auth-description">Log in to your account to continue your green journey.</p>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error" style="background-color: #fee; color: #c33; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-success" style="background-color: #efe; color: #3c3; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form class="auth-form" action="#" method="POST">
                <div class="form-group">
                    <label for="email">Email / Username</label>
                    <input type="text" id="email" name="email" required>
                </div>
                
                <div class="form-group password-group">
                    <label for="password">Password</label>
                    <div class="password-input-group">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-extras">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                </div>

                <!-- <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div> -->
                
                <button type="submit" name="login" class="btn btn-primary">Log In</button>

                <p class="auth-alternative">
                    Don't have an account? <a href="signup.php">Sign Up</a>
                </p>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>GoGreenTogether</h3>
                <p>Making sustainability accessible and engaging for everyone. Join our community and make a difference.</p>
                <div class="social-links">
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.linkedin.com" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="aboutus.php">About Us</a></li>
                    <li><a href="event.php">Events</a></li>
                    <li><a href="marketplace.php">Marketplace</a></li>
                    <li><a href="tips.php">Tips</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Get Involved</h4>
                <ul>
                    <li><a href="https://wa.me/60123456789" target="_blank">Volunteer</a></li>
                    <li><a href="https://wa.me/60123456789" target="_blank">Partner With Us</a></li>
                    <li><a href="https://wa.me/60123456789" target="_blank">Share Your Story</a></li>
                    <li><a href="index.php#newsletter-section">Newsletter</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contact Us</h4>
                <ul class="contact-info">
                    <li><a href="mailto:info@gogreentogether.org"><i class="fas fa-envelope"></i> info@gogreentogether.org</a></li>
                    <li><a href="https://wa.me/60123456789" target="_blank"><i class="fas fa-phone"></i> +60 12-345-6789</a></li>
                    <li><a href="https://www.google.com/maps/search/Asia+Pacific+University,+Kuala+Lumpur" target="_blank"><i class="fas fa-map-marker-alt"></i> Asia Pacific University</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 GoGreenTogether. All rights reserved.</p>
        </div>
    </footer>

    <!-- <script src="../js/main.js"></script> -->
</body>
</html>
