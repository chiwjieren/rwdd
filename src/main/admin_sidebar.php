<aside class="admin-sidebar">
    <div class="admin-logo">
        <i class="fas fa-leaf"></i>
        <h2>GoGreen Admin</h2>
    </div>
    <nav class="admin-nav">
        <a href="admin_dashboard.php" class="admin-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="admin_users.php" class="admin-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="admin_events.php" class="admin-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_events.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar"></i> Events
        </a>
        <a href="admin_tips.php" class="admin-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_tips.php' ? 'active' : ''; ?>">
            <i class="fas fa-lightbulb"></i> Tips
        </a>
        <a href="admin_marketplace.php" class="admin-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_marketplace.php' ? 'active' : ''; ?>">
            <i class="fas fa-store"></i> Marketplace
        </a>
        <a href="admin_swaps.php" class="admin-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_swaps.php' ? 'active' : ''; ?>">
            <i class="fas fa-exchange-alt"></i> Swap Requests
        </a>
        <a href="admin_newsletter.php" class="admin-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_newsletter.php' ? 'active' : ''; ?>">
            <i class="fas fa-envelope"></i> Newsletter
        </a>
        <a href="admin_quiz.php" class="admin-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_quiz.php' ? 'active' : ''; ?>">
            <i class="fas fa-question-circle"></i> Quiz Questions
        </a>
        <div class="admin-nav-divider"></div>
        <a href="index.php" class="admin-nav-item">
            <i class="fas fa-home"></i> View Site
        </a>
        <a href="logout.php" class="admin-nav-item">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</aside>
