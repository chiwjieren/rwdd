<?php
// Placeholder for actual authentication logic - to be implemented later
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - GoGreenTogether</title>
    <link rel="stylesheet" href="../css/styles.css">
    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <nav class="nav">
        <a href="index.php" class="brand">GoGreenTogether</a>
        <button class="hamburger" aria-label="Toggle Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="nav-links">
            <a href="aboutus.php">About</a>
            <a href="event.php">Events</a>
            <a href="marketplace.php">Marketplace</a>
            <a href="tips.php">Tips</a>
        </div>
    </nav>

    <main class="container auth-container">
        <div class="auth-box">
            <h1>Welcome Back</h1>
            <p class="auth-description">Log in to your account to continue your green journey.</p>
            
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
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>

                <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div>
                
                <button type="submit" class="btn btn-primary">Log In</button>

                <p class="auth-alternative">
                    Don't have an account? <a href="signup.php">Sign Up</a>
                </p>
            </form>
        </div>
    </main>

    <footer class="footer">
        <!-- Footer content -->
    </footer>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../js/main.js"></script>
</body>
</html>
