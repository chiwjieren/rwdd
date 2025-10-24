<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

$message = "";
$error = "";

// Handle Delete Subscriber
if (isset($_GET['delete'])) {
    $subscriberId = intval($_GET['delete']);
    
    if ($conn->query("DELETE FROM NEWSLETTER_SUBSCRIBER WHERE subscriber_id = $subscriberId")) {
        $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                     VALUES ('admin', 'DELETE', 'NEWSLETTER_SUBSCRIBER', $subscriberId, 'Deleted subscriber ID: $subscriberId')");
        $message = "Subscriber deleted successfully!";
    } else {
        $error = "Error deleting subscriber.";
    }
}

// Get all newsletter subscribers
$subscribers = $conn->query("SELECT * FROM NEWSLETTER_SUBSCRIBER ORDER BY subscribed_at DESC");

// Get subscriber count
$totalSubscribers = $subscribers->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Newsletter - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Manage Newsletter Subscribers</h1>
                <div class="admin-user">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </div>
            </header>

            <div class="admin-content">
                <?php if ($message): ?>
                    <div class="alert-admin alert-success-admin">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert-admin alert-error-admin">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Statistics Card -->
                <div class="stats-grid" style="margin-bottom: 2rem;">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #00BCD4;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalSubscribers; ?></h3>
                            <p>Total Subscribers</p>
                        </div>
                    </div>
                </div>

                <!-- Subscribers Table -->
                <div class="admin-table-container">
                    <div class="admin-table-header">
                        <h2><i class="fas fa-users"></i> All Newsletter Subscribers</h2>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email Address</th>
                                <th>Subscribed Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($totalSubscribers > 0): ?>
                                <?php while ($subscriber = $subscribers->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $subscriber['subscriber_id']; ?></td>
                                        <td>
                                            <i class="fas fa-envelope" style="color: var(--admin-text-light); margin-right: 0.5rem;"></i>
                                            <?php echo htmlspecialchars($subscriber['subscriber_email']); ?>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($subscriber['subscribed_at'])); ?></td>
                                        <td>
                                            <a href="?delete=<?php echo $subscriber['subscriber_id']; ?>" 
                                               onclick="return confirm('Are you sure you want to unsubscribe this email?')"
                                               class="btn-admin btn-danger-admin btn-sm">
                                                <i class="fas fa-trash"></i> Remove
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 3rem; color: var(--admin-text-light);">
                                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                        No newsletter subscribers yet
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Export Section -->
                <?php if ($totalSubscribers > 0): ?>
                    <div class="admin-form" style="margin-top: 2rem;">
                        <h2><i class="fas fa-download"></i> Export Subscriber Emails</h2>
                        <p style="color: var(--admin-text-light); margin-bottom: 1rem;">
                            Copy all subscriber emails for use in your email marketing platform:
                        </p>
                        <textarea 
                            readonly 
                            style="width: 100%; min-height: 150px; padding: 1rem; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace; font-size: 0.9rem;"
                            onclick="this.select();"
                        ><?php 
                            $subscribers->data_seek(0);
                            $emails = [];
                            while ($sub = $subscribers->fetch_assoc()) {
                                $emails[] = $sub['subscriber_email'];
                            }
                            echo implode(', ', $emails);
                        ?></textarea>
                        <button 
                            onclick="navigator.clipboard.writeText(this.previousElementSibling.value); alert('Emails copied to clipboard!');" 
                            class="btn-admin btn-primary-admin" 
                            style="margin-top: 1rem;">
                            <i class="fas fa-copy"></i> Copy to Clipboard
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
