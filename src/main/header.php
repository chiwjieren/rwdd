<?php
session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
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
        <div class="nav-auth">
            <?php if (isLoggedIn()): ?>
                <div class="profile-section">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <?php if (isset($_SESSION['notifications']) && count($_SESSION['notifications']) > 0): ?>
                            <span class="notification-badge"><?php echo count($_SESSION['notifications']); ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="profile-dropdown">
                        <button class="profile-btn">
                            <img src="<?php echo $_SESSION['profile_image'] ?? '../media/default-avatar.png'; ?>" alt="Profile" class="profile-img">
                            <span class="profile-name"><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                        </button>
                        <div class="dropdown-content">
                            <a href="profile.php" class="dropdown-item">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <a href="myswaps.php" class="dropdown-item">
                                <i class="fas fa-exchange-alt"></i> My Swaps
                                <?php if (isset($_SESSION['pending_requests']) && $_SESSION['pending_requests'] > 0): ?>
                                    <span class="request-badge"><?php echo $_SESSION['pending_requests']; ?></span>
                                <?php endif; ?>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary login-btn">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hamburger menu toggle
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    
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
});
</script>
