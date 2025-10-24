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

// Handle Delete User
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    
    // Delete related records first
    $conn->query("DELETE FROM ITEM WHERE user_id = $userId");
    $conn->query("DELETE FROM TIP WHERE user_id = $userId");
    $conn->query("DELETE FROM SWAP WHERE swap_sender_id = $userId OR swap_receiver_id = $userId");
    $conn->query("DELETE FROM NOTIFICATION WHERE user_id = $userId");
    
    // Delete user
    if ($conn->query("DELETE FROM USER WHERE user_id = $userId")) {
        // Log action
        $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                     VALUES ('admin', 'DELETE', 'USER', $userId, 'Deleted user ID: $userId')");
        $message = "User deleted successfully!";
    } else {
        $error = "Error deleting user.";
    }
}

// Handle Create/Edit User
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userName = trim($_POST['user_name']);
    $userEmail = trim($_POST['user_email']);
    $userPassword = $_POST['user_password'];
    
    if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        // Update existing user
        $userId = intval($_POST['user_id']);
        
        if (!empty($userPassword)) {
            $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE USER SET user_name = ?, user_email = ?, user_password = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $userName, $userEmail, $hashedPassword, $userId);
        } else {
            $stmt = $conn->prepare("UPDATE USER SET user_name = ?, user_email = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $userName, $userEmail, $userId);
        }
        
        if ($stmt->execute()) {
            $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                         VALUES ('admin', 'UPDATE', 'USER', $userId, 'Updated user: $userName')");
            $message = "User updated successfully!";
        } else {
            $error = "Error updating user.";
        }
    } else {
        // Create new user
        $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO USER (user_name, user_email, user_password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $userName, $userEmail, $hashedPassword);
        
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                         VALUES ('admin', 'CREATE', 'USER', $newId, 'Created new user: $userName')");
            $message = "User created successfully!";
        } else {
            $error = "Error creating user. Email might already exist.";
        }
    }
}

// Get all users
$users = $conn->query("SELECT * FROM USER ORDER BY user_id DESC");

// Get user for editing if requested
$editUser = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editUser = $conn->query("SELECT * FROM USER WHERE user_id = $editId")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Manage Users</h1>
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

                <!-- Add/Edit User Form -->
                <div class="admin-form">
                    <h2><?php echo $editUser ? 'Edit User' : 'Add New User'; ?></h2>
                    <form method="POST" action="">
                        <?php if ($editUser): ?>
                            <input type="hidden" name="user_id" value="<?php echo $editUser['user_id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-grid">
                            <div class="form-group-admin">
                                <label>Full Name *</label>
                                <input type="text" name="user_name" value="<?php echo $editUser['user_name'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group-admin">
                                <label>Email *</label>
                                <input type="email" name="user_email" value="<?php echo $editUser['user_email'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group-admin">
                                <label>Password <?php echo $editUser ? '(Leave blank to keep current)' : '*'; ?></label>
                                <input type="password" name="user_password" <?php echo !$editUser ? 'required' : ''; ?>>
                            </div>
                        </div>
                        
                        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                            <button type="submit" class="btn-admin btn-primary-admin">
                                <i class="fas fa-save"></i>
                                <?php echo $editUser ? 'Update User' : 'Create User'; ?>
                            </button>
                            <?php if ($editUser): ?>
                                <a href="admin_users.php" class="btn-admin btn-secondary-admin">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Users Table -->
                <div class="admin-table-container" style="margin-top: 2rem;">
                    <div class="admin-table-header">
                        <h2><i class="fas fa-users"></i> All Users (<?php echo $users->num_rows; ?>)</h2>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subscribed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users->num_rows > 0): ?>
                                <?php while ($user = $users->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $user['user_id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                                        <td>
                                            <?php if ($user['user_subscribe']): ?>
                                                <span class="badge badge-success">Yes</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="?edit=<?php echo $user['user_id']; ?>" class="btn-admin btn-secondary-admin btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?delete=<?php echo $user['user_id']; ?>" 
                                               onclick="return confirm('Are you sure? This will delete all user data including items and swaps.')"
                                               class="btn-admin btn-danger-admin btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2rem; color: #666;">No users found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
