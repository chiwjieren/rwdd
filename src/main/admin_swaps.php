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

// Handle Delete Swap
if (isset($_GET['delete'])) {
    $swapId = intval($_GET['delete']);
    
    if ($conn->query("DELETE FROM SWAP WHERE swap_id = $swapId")) {
        $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                     VALUES ('admin', 'DELETE', 'SWAP', $swapId, 'Deleted swap request ID: $swapId')");
        $message = "Swap request deleted successfully!";
    } else {
        $error = "Error deleting swap request.";
    }
}

// Handle Update Status
if (isset($_GET['approve'])) {
    $swapId = intval($_GET['approve']);
    
    if ($conn->query("UPDATE SWAP SET swap_status = 'approved' WHERE swap_id = $swapId")) {
        $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                     VALUES ('admin', 'UPDATE', 'SWAP', $swapId, 'Approved swap request ID: $swapId')");
        $message = "Swap request approved!";
    } else {
        $error = "Error approving swap request.";
    }
}

if (isset($_GET['reject'])) {
    $swapId = intval($_GET['reject']);
    
    if ($conn->query("UPDATE SWAP SET swap_status = 'rejected' WHERE swap_id = $swapId")) {
        $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                     VALUES ('admin', 'UPDATE', 'SWAP', $swapId, 'Rejected swap request ID: $swapId')");
        $message = "Swap request rejected!";
    } else {
        $error = "Error rejecting swap request.";
    }
}

// Get all swap requests with details
$swaps = $conn->query("SELECT s.*, 
                       u1.user_name as sender_name, 
                       u2.user_name as receiver_name,
                       i1.item_name as sender_item_name,
                       i2.item_name as receiver_item_name
                       FROM SWAP s
                       LEFT JOIN USER u1 ON s.swap_sender_id = u1.user_id
                       LEFT JOIN USER u2 ON s.swap_receiver_id = u2.user_id
                       LEFT JOIN ITEM i1 ON s.swap_sender_item_id = i1.item_id
                       LEFT JOIN ITEM i2 ON s.swap_receiver_item_id = i2.item_id
                       ORDER BY s.swap_created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Swap Requests - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Manage Swap Requests</h1>
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

                <!-- Swaps Table -->
                <div class="admin-table-container">
                    <div class="admin-table-header">
                        <h2><i class="fas fa-exchange-alt"></i> All Swap Requests (<?php echo $swaps->num_rows; ?>)</h2>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sender</th>
                                <th>Sender Item</th>
                                <th>⇄</th>
                                <th>Receiver</th>
                                <th>Receiver Item</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($swap = $swaps->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $swap['swap_id']; ?></td>
                                    <td><?php echo htmlspecialchars($swap['sender_name']); ?></td>
                                    <td><?php echo htmlspecialchars($swap['sender_item_name']); ?></td>
                                    <td style="text-align: center; font-size: 1.2rem; color: var(--admin-primary);">
                                        <i class="fas fa-exchange-alt"></i>
                                    </td>
                                    <td><?php echo htmlspecialchars($swap['receiver_name']); ?></td>
                                    <td><?php echo htmlspecialchars($swap['receiver_item_name']); ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'background: #fff3cd; color: #856404;',
                                            'approved' => 'background: #d1fae5; color: #065f46;',
                                            'rejected' => 'background: #fee2e2; color: #991b1b;'
                                        ];
                                        $statusColor = $statusColors[$swap['swap_status']] ?? '';
                                        ?>
                                        <span class="action-badge" style="<?php echo $statusColor; ?>">
                                            <?php echo ucfirst($swap['swap_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($swap['created_at'])); ?></td>
                                    <td>
                                        <?php if ($swap['swap_status'] == 'pending'): ?>
                                            <a href="?approve=<?php echo $swap['swap_id']; ?>" 
                                               onclick="return confirm('Approve this swap request?')"
                                               class="btn-admin btn-primary-admin btn-sm">
                                                <i class="fas fa-check"></i> Approve
                                            </a>
                                            <a href="?reject=<?php echo $swap['swap_id']; ?>" 
                                               onclick="return confirm('Reject this swap request?')"
                                               class="btn-admin btn-danger-admin btn-sm">
                                                <i class="fas fa-times"></i> Reject
                                            </a>
                                        <?php endif; ?>
                                        <a href="?delete=<?php echo $swap['swap_id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this swap request?')"
                                           class="btn-admin btn-danger-admin btn-sm">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
