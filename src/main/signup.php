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
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </div>
                
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
