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
        // Get user from database
        $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_password FROM USER WHERE user_email = ?");
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
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
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
                <h3>GoGreen Together</h3>
                <p>Join us in creating a sustainable future through community-driven initiatives and eco-friendly practices.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="aboutus.php">About Us</a></li>
                    <li><a href="event.php">Events</a></li>
                    <li><a href="tips.php">Tips</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Community</h4>
                <ul>
                    <li><a href="marketplace.php">Marketplace</a></li>
                    <li><a href="inventory.php">My Inventory</a></li>
                    <li><a href="myswaps.php">My Swaps</a></li>
                    <li><a href="notifications.php">Notifications</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contact Us</h4>
                <ul>
                    <li><i class="fas fa-envelope"></i> info@gogreen.com</li>
                    <li><i class="fas fa-phone"></i> +60 12-345 6789</li>
                    <li><i class="fas fa-map-marker-alt"></i> Kuala Lumpur, Malaysia</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 GoGreen Together. All rights reserved.</p>
        </div>
    </footer>

    <!-- <script src="../js/main.js"></script> -->
</body>
</html>
