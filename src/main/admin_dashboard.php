<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

// Get statistics
$result = $conn->query("SELECT COUNT(*) as count FROM USER");
$totalUsers = $result ? $result->fetch_assoc()['count'] : 0;

$result = $conn->query("SELECT COUNT(*) as count FROM EVENT");
$totalEvents = $result ? $result->fetch_assoc()['count'] : 0;

$result = $conn->query("SELECT COUNT(*) as count FROM TIP");
$totalTips = $result ? $result->fetch_assoc()['count'] : 0;

$result = $conn->query("SELECT COUNT(*) as count FROM ITEM");
$totalItems = $result ? $result->fetch_assoc()['count'] : 0;

$result = $conn->query("SELECT COUNT(*) as count FROM SWAP");
$totalSwaps = $result ? $result->fetch_assoc()['count'] : 0;

$result = $conn->query("SELECT COUNT(*) as count FROM NEWSLETTER_SUBSCRIBER");
$totalSubscribers = $result ? $result->fetch_assoc()['count'] : 0;

$result = $conn->query("SELECT COUNT(*) as count FROM QUIZ_QUESTION");
$totalQuizQuestions = $result ? $result->fetch_assoc()['count'] : 0;

// Get recent activity
$recentLogs = $conn->query("SELECT * FROM ADMIN_LOG ORDER BY created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GoGreenTogether</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'admin_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1>Dashboard Overview</h1>
                <div class="admin-user">
                    <i class="fas fa-user-shield"></i>
                    <span>Welcome, Admin</span>
                </div>
            </header>

            <div class="admin-content">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #4CAF50;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalUsers; ?></h3>
                            <p>Total Users</p>
                        </div>
                        <a href="admin_users.php" class="stat-link">View All <i class="fas fa-arrow-right"></i></a>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #2196F3;">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalEvents; ?></h3>
                            <p>Total Events</p>
                        </div>
                        <a href="admin_events.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #FF9800;">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalTips; ?></h3>
                            <p>Tips Submitted</p>
                        </div>
                        <a href="admin_tips.php" class="stat-link">View All <i class="fas fa-arrow-right"></i></a>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #9C27B0;">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalItems; ?></h3>
                            <p>Marketplace Items</p>
                        </div>
                        <a href="admin_marketplace.php" class="stat-link">View All <i class="fas fa-arrow-right"></i></a>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #f44336;">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalSwaps; ?></h3>
                            <p>Swap Requests</p>
                        </div>
                        <a href="admin_swaps.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #00BCD4;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalSubscribers; ?></h3>
                            <p>Newsletter Subscribers</p>
                        </div>
                        <a href="admin_newsletter.php" class="stat-link">View All <i class="fas fa-arrow-right"></i></a>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: #607D8B;">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalQuizQuestions; ?></h3>
                            <p>Quiz Questions</p>
                        </div>
                        <a href="admin_quiz.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="activity-section">
                    <h2><i class="fas fa-history"></i> Recent Admin Activity</h2>
                    <div class="activity-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Table</th>
                                    <th>Details</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recentLogs->num_rows > 0): ?>
                                    <?php while ($log = $recentLogs->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <span class="action-badge action-<?php echo strtolower($log['action_type']); ?>">
                                                    <?php echo $log['action_type']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($log['table_name']); ?></td>
                                            <td><?php echo htmlspecialchars($log['action_details']); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($log['created_at'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 2rem; color: #999;">
                                            No activity logs yet
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
