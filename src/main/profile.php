<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'rwdd_assignment');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];
$message = "";
$error = "";

// Check for success message from redirect
if (isset($_GET['success'])) {
    $message = "Profile image updated successfully!";
}

// Get user details
$userQuery = $conn->prepare("SELECT user_name, user_email, user_profile_image FROM USER WHERE user_id = ?");
$userQuery->bind_param("i", $userId);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();
$userQuery->close();

// Handle profile image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_profile'])) {
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (in_array($_FILES['profile_image']['type'], $allowedTypes) && $_FILES['profile_image']['size'] <= $maxSize) {
            $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $fileName = 'profile_' . $userId . '_' . time() . '.' . $extension;
            $uploadDir = '../media/profiles/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadPath)) {
                // Delete old profile image if exists
                if (!empty($user['user_profile_image']) && file_exists('../media/profiles/' . $user['user_profile_image'])) {
                    unlink('../media/profiles/' . $user['user_profile_image']);
                }
                
                // Update database
                $stmt = $conn->prepare("UPDATE USER SET user_profile_image = ? WHERE user_id = ?");
                $stmt->bind_param("si", $fileName, $userId);
                
                if ($stmt->execute()) {
                    $_SESSION['profile_image'] = '../media/profiles/' . $fileName;
                    $message = "Profile image updated successfully!";
                    $user['user_profile_image'] = $fileName;
                    // Refresh the page to show new image
                    header("Location: profile.php?success=1");
                    exit();
                } else {
                    $error = "Error updating profile image in database.";
                }
                $stmt->close();
            } else {
                $error = "Failed to upload image. Please check folder permissions.";
            }
        } else {
            if (!in_array($_FILES['profile_image']['type'], $allowedTypes)) {
                $error = "Invalid file type. Please upload JPG, PNG, or GIF only.";
            } else {
                $error = "File too large. Maximum size is 5MB.";
            }
        }
    } else {
        if ($_FILES['profile_image']['error'] != 0) {
            $error = "Upload error: " . $_FILES['profile_image']['error'];
        } else {
            $error = "Please select an image to upload.";
        }
    }
}

// Handle profile image removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_profile'])) {
    // Delete profile image file if exists
    if (!empty($user['user_profile_image']) && file_exists('../media/profiles/' . $user['user_profile_image'])) {
        unlink('../media/profiles/' . $user['user_profile_image']);
    }
    
    // Update database to remove image reference
    $stmt = $conn->prepare("UPDATE USER SET user_profile_image = NULL WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        unset($_SESSION['profile_image']);
        $message = "Profile image removed successfully!";
        $user['user_profile_image'] = null;
    } else {
        $error = "Error removing profile image.";
    }
    $stmt->close();
}

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = trim($_POST['user_name']);
    
    if (!empty($name)) {
        $stmt = $conn->prepare("UPDATE USER SET user_name = ? WHERE user_id = ?");
        $stmt->bind_param("si", $name, $userId);
        
        if ($stmt->execute()) {
            $_SESSION['username'] = $name;
            $user['user_name'] = $name;
            $message = "Profile updated successfully!";
        } else {
            $error = "Error updating profile.";
        }
        $stmt->close();
    } else {
        $error = "Name cannot be empty.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - GoGreenTogether</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        
        .profile-content-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .profile-image-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            overflow: hidden;
            border-radius: 50%;
        }
        
        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            border: 4px solid #28a745;
            border-radius: 50%;
            display: block;
        }
        
        .default-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: #999;
            border: 4px solid #28a745;
        }
        
        .upload-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #28a745;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .upload-overlay:hover {
            background: #218838;
        }
        
        .upload-overlay input[type="file"] {
            display: none;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input[type="text"],
        .form-group input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group input[type="email"] {
            background: #f5f5f5;
            cursor: not-allowed;
        }
        
        .btn-primary {
            background-color: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: #218838;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .section-title {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }
        
        .btn-remove {
            background: #dc3545;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            display: inline-block;
            margin-top: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .btn-remove:hover {
            background: #c82333;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .profile-actions {
            margin-top: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .profile-actions h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-container {
                margin: 20px auto;
                padding: 10px;
            }
            
            .profile-content-section {
                padding: 20px 15px;
            }
            
            .profile-image-container,
            .default-avatar {
                width: 120px;
                height: 120px;
            }
            
            .default-avatar {
                font-size: 50px;
            }
            
            .upload-overlay {
                width: 35px;
                height: 35px;
            }
            
            .form-group input[type="text"],
            .form-group input[type="email"] {
                font-size: 16px; /* Prevent zoom on iOS */
            }
            
            .btn-primary,
            .btn-remove {
                width: 100%;
                padding: 15px;
            }
            
            .profile-actions {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="profile-container">
        <h1>My Profile</h1>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Profile Image Section -->
        <div class="profile-content-section">
            <div class="profile-header">
                <div class="profile-image-container">
                    <?php if (!empty($user['user_profile_image'])): ?>
                        <img src="../media/profiles/<?php echo htmlspecialchars($user['user_profile_image']); ?>" 
                             alt="Profile" class="profile-image" id="profilePreview">
                    <?php else: ?>
                        <div class="default-avatar" id="profilePreview">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" id="profileImageForm">
                        <div class="upload-overlay" onclick="document.getElementById('profile_image_input').click()">
                            <i class="fas fa-camera"></i>
                            <input type="file" id="profile_image_input" name="profile_image" 
                                   accept="image/*" onchange="uploadProfileImage(this)">
                        </div>
                        <input type="hidden" name="upload_profile" value="1">
                    </form>
                </div>
                <h2><?php echo htmlspecialchars($user['user_name']); ?></h2>
                <p style="color: #666;"><?php echo htmlspecialchars($user['user_email']); ?></p>
            </div>
            
            <!-- Profile Actions -->
            <?php if (!empty($user['user_profile_image'])): ?>
                <div class="profile-actions">
                    <h3>Profile Picture Actions</h3>
                    <form method="POST" style="display: inline-block;">
                        <button type="submit" name="remove_profile" 
                                class="btn-remove"
                                onclick="return confirm('Are you sure you want to remove your profile picture?')">
                            <i class="fas fa-trash"></i> Remove Profile Picture
                        </button>
                    </form>
                    <p style="margin: 10px 0 0 0; font-size: 13px; color: #999;">
                        <i class="fas fa-info-circle"></i> This will delete your current profile picture and restore the default avatar.
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Profile Information Section -->
        <div class="profile-content-section">
            <h2 class="section-title">Profile Information</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="user_name">Full Name</label>
                    <input type="text" id="user_name" name="user_name" 
                           value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="user_email">Email</label>
                    <input type="email" id="user_email" name="user_email" 
                           value="<?php echo htmlspecialchars($user['user_email']); ?>" readonly>
                    <small style="color: #999;">Email cannot be changed</small>
                </div>
                
                <button type="submit" name="update_profile" class="btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
        </div>
    </main>
    
    <script>
        function uploadProfileImage(input) {
            if (input.files && input.files[0]) {
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profilePreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="profile-image" alt="Profile">';
                }
                reader.readAsDataURL(input.files[0]);
                
                // Auto-submit form
                document.getElementById('profileImageForm').submit();
            }
        }
    </script>

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
</body>
</html>
