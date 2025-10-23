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
    <button class="hamburger" aria-label="Toggle Menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <div class="nav-links">
        <div class="nav-menu">
            <a href="aboutus.php">About</a>
            <a href="event.php">Events</a>
            <a href="marketplace.php">Marketplace</a>
            <a href="tips.php">Tips</a>
        </div>
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
    // Hamburger menu toggle
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.nav')) {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
    }

    // Dropdown delay functionality
    const profileSection = document.querySelector('.profile-section');
    const dropdownContent = document.querySelector('.dropdown-content');
    let hideTimeout;

    if (profileSection && dropdownContent) {
        // Show dropdown on hover
        profileSection.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
            dropdownContent.classList.add('show');
        });

        // Delay hiding dropdown when mouse leaves
        profileSection.addEventListener('mouseleave', function() {
            hideTimeout = setTimeout(function() {
                dropdownContent.classList.remove('show');
            }, 500); // 500ms delay before hiding
        });

        // Keep dropdown open when hovering over it
        dropdownContent.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
            dropdownContent.classList.add('show');
        });

        // Hide dropdown with delay when leaving dropdown
        dropdownContent.addEventListener('mouseleave', function() {
            hideTimeout = setTimeout(function() {
                dropdownContent.classList.remove('show');
            }, 500); // 500ms delay before hiding
        });
    }
});
</script>
