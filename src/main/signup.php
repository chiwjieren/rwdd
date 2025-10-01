<?php
// Placeholder for actual authentication logic
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - GoGreenTogether</title>
    <link rel="stylesheet" href="../css/styles.css">
    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
            <h1>Join GoGreenTogether</h1>
            <p class="auth-description">Create your account and start making a difference in your community.</p>
            
            <form class="auth-form" action="#" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
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
                
                <div class="form-group password-group">
                    <label for="confirm-password">Confirm Password</label>
                    <div class="password-input-group">
                        <input type="password" id="confirm-password" name="confirm-password" required>
                        <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div>
                
                <button type="submit" class="btn btn-primary">Sign Up</button>
                
                <p class="auth-alternative">
                    Already have an account? <a href="login.php">Log In</a>
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
