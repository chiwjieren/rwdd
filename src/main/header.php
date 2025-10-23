<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get unread notification count if user is logged in
$unreadNotifications = 0;
if (isLoggedIn()) {
    $notifConn = new mysqli('localhost', 'root', '', 'rwdd_assignment');
    if (!$notifConn->connect_error) {
        $userId = $_SESSION['user_id'];
        $notifQuery = $notifConn->prepare("SELECT COUNT(*) as unread_count FROM NOTIFICATION WHERE user_id = ? AND is_read = FALSE");
        $notifQuery->bind_param("i", $userId);
        $notifQuery->execute();
        $unreadNotifications = $notifQuery->get_result()->fetch_assoc()['unread_count'];
        $notifConn->close();
    }
}
?>

<nav class="nav">
    <a href="index.php" class="brand">GoGreenTogether</a>
    
    <?php if (isLoggedIn()): ?>
        <!-- Checkbox for mobile menu toggle (progressive enhancement) -->
        <input type="checkbox" id="nav-toggle" class="nav-toggle" aria-label="Toggle navigation menu">
        <label for="nav-toggle" class="hamburger">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </label>
    <?php endif; ?>
    
    <div class="nav-links">
        <div class="nav-menu">
            <a href="aboutus.php">About</a>
            <a href="event.php">Events</a>
            <a href="marketplace.php">Marketplace</a>
            <a href="tips.php">Tips</a>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <!-- Mobile Profile Section (only visible on mobile) -->
            <div class="mobile-profile-section">
                <div class="mobile-profile-header">
                    <img src="<?php echo !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] : '../media/default-avatar.png'; ?>" alt="Profile">
                    <span><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                </div>
                <div class="mobile-profile-links">
                    <a href="profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <a href="notifications.php">
                        <i class="fas fa-bell"></i> Notifications
                        <?php if ($unreadNotifications > 0): ?>
                            <span class="mobile-badge"><?php echo $unreadNotifications; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="myswaps.php">
                        <i class="fas fa-exchange-alt"></i> My Swaps
                    </a>
                    <a href="inventory.php">
                        <i class="fas fa-boxes"></i> Inventory
                    </a>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="nav-auth">
        <?php if (isLoggedIn()): ?>
            <a href="notifications.php" class="notification-btn">
                <i class="fas fa-bell"></i>
                <?php if ($unreadNotifications > 0): ?>
                    <span class="notification-badge"><?php echo $unreadNotifications; ?></span>
                <?php endif; ?>
            </a>
            
            <div class="profile-section">
                <div class="profile-trigger">
                    <img src="<?php echo !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] : '../media/default-avatar.png'; ?>" alt="Profile" class="profile-img">
                    <span class="profile-name"><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                </div>
                <div class="dropdown-content">
                    <a href="profile.php" class="dropdown-item">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <a href="notifications.php" class="dropdown-item">
                        <i class="fas fa-bell"></i> Notifications
                        <?php if ($unreadNotifications > 0): ?>
                            <span class="request-badge"><?php echo $unreadNotifications; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="myswaps.php" class="dropdown-item">
                        <i class="fas fa-exchange-alt"></i> My Swaps
                    </a>
                    <a href="inventory.php" class="dropdown-item">
                        <i class="fas fa-boxes"></i> Inventory
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="dropdown-item">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php" class="btn btn-primary login-btn">Login</a>
        <?php endif; ?>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('nav-toggle');
    const navLinks = document.querySelectorAll('.nav-menu a, .mobile-profile-links a');
    
    // Close mobile menu when clicking a link (progressive enhancement)
    if (navToggle) {
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    navToggle.checked = false;
                }
            });
        });
    }
    
    // Desktop dropdown hover functionality
    const profileSection = document.querySelector('.profile-section');
    const dropdownContent = document.querySelector('.dropdown-content');
    let hideTimeout;

    if (profileSection && dropdownContent) {
        function isDesktop() {
            return window.innerWidth > 768;
        }

        // Only apply hover effects on desktop
        profileSection.addEventListener('mouseenter', function() {
            if (isDesktop()) {
                clearTimeout(hideTimeout);
            }
        });

        profileSection.addEventListener('mouseleave', function() {
            if (isDesktop()) {
                hideTimeout = setTimeout(function() {
                    // CSS handles the hiding
                }, 500);
            }
        });

        dropdownContent.addEventListener('mouseenter', function() {
            if (isDesktop()) {
                clearTimeout(hideTimeout);
            }
        });

        dropdownContent.addEventListener('mouseleave', function() {
            if (isDesktop()) {
                hideTimeout = setTimeout(function() {
                    // CSS handles the hiding
                }, 500);
            }
        });
    }
});
</script>
