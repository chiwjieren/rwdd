<?php
// Start session before any output
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

// Initialize variables
$message = "";
$error = "";


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Get form data and sanitize
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    $confirmPassword = mysqli_real_escape_string($conn, trim($_POST['confirm-password']));
    
    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "Please fill in all required fields!";
    } 
    
    else if ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } 
    
    else {  
        // Hash the password before storing (IMPORTANT for security!)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // SQL query to insert data using prepared statement (prevents SQL injection)
        $stmt = $conn->prepare("INSERT INTO USER (user_name, user_subscribe, user_password, user_email) VALUES (?, 0, ?, ?)");
        $stmt->bind_param("sss", $name, $hashedPassword, $email);
        
        if ($stmt->execute()) {
            // Get the newly created user's ID
            $userId = $conn->insert_id;
            
            // Create session for the user (auto-login)
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $name;
            $_SESSION['user_email'] = $email;
            
            // Redirect to home page
            header("Location: index.php");
            exit();
        } else {
            // Check if email already exists
            if (mysqli_errno($conn) == 1062) {
                $error = "Email already registered. Please use a different email or <a href='login.php'>login here</a>.";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
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
    <title>Sign Up - GoGreenTogether</title>
    <link rel="stylesheet" href="../css/styles.css">
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
                
                <button type="submit" name="submit" class="btn btn-primary">Sign Up</button>
                
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
</body>
</html>
