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

// Handle Delete Tip
if (isset($_GET['delete'])) {
    $tipId = intval($_GET['delete']);
    
    if ($conn->query("DELETE FROM TIP WHERE tip_id = $tipId")) {
        $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                     VALUES ('admin', 'DELETE', 'TIP', $tipId, 'Deleted tip ID: $tipId')");
        $message = "Tip deleted successfully!";
    } else {
        $error = "Error deleting tip.";
    }
}

// Handle Edit Tip
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tip_id'])) {
    $tipId = intval($_POST['tip_id']);
    $tipTitle = trim($_POST['tip_title']);
    $tipContent = trim($_POST['tip_content']);
    $tipCategory = trim($_POST['tip_category']);
    
    $stmt = $conn->prepare("UPDATE TIP SET tip_title = ?, tip_content = ?, tip_category = ? WHERE tip_id = ?");
    $stmt->bind_param("sssi", $tipTitle, $tipContent, $tipCategory, $tipId);
    
    if ($stmt->execute()) {
        $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                     VALUES ('admin', 'UPDATE', 'TIP', $tipId, 'Updated tip: $tipTitle')");
        $message = "Tip updated successfully!";
    } else {
        $error = "Error updating tip.";
    }
}

// Get all tips with user info
$tips = $conn->query("SELECT t.*, u.user_name FROM TIP t 
                      LEFT JOIN USER u ON t.user_id = u.user_id 
                      ORDER BY t.created_at DESC");

// Get tip for editing if requested
$editTip = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editTip = $conn->query("SELECT * FROM TIP WHERE tip_id = $editId")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tips - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Manage Tips</h1>
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

                <?php if ($editTip): ?>
                    <!-- Edit Tip Form -->
                    <div class="admin-form">
                        <h2>Edit Tip</h2>
                        <form method="POST" action="">
                            <input type="hidden" name="tip_id" value="<?php echo $editTip['tip_id']; ?>">
                            
                            <div class="form-grid">
                                <div class="form-group-admin">
                                    <label>Tip Title *</label>
                                    <input type="text" name="tip_title" value="<?php echo htmlspecialchars($editTip['tip_title']); ?>" required>
                                </div>
                                <div class="form-group-admin">
                                    <label>Category *</label>
                                    <select name="tip_category" required>
                                        <option value="energy" <?php echo $editTip['tip_category'] == 'energy' ? 'selected' : ''; ?>>Energy Saving</option>
                                        <option value="water" <?php echo $editTip['tip_category'] == 'water' ? 'selected' : ''; ?>>Water Conservation</option>
                                        <option value="waste" <?php echo $editTip['tip_category'] == 'waste' ? 'selected' : ''; ?>>Waste Reduction</option>
                                        <option value="transportation" <?php echo $editTip['tip_category'] == 'transportation' ? 'selected' : ''; ?>>Transportation</option>
                                        <option value="food" <?php echo $editTip['tip_category'] == 'food' ? 'selected' : ''; ?>>Food & Diet</option>
                                        <option value="other" <?php echo $editTip['tip_category'] == 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group-admin" style="margin-top: 1rem;">
                                <label>Tip Content *</label>
                                <textarea name="tip_content" required><?php echo htmlspecialchars($editTip['tip_content']); ?></textarea>
                            </div>
                            
                            <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                                <button type="submit" class="btn-admin btn-primary-admin">
                                    <i class="fas fa-save"></i> Update Tip
                                </button>
                                <a href="admin_tips.php" class="btn-admin btn-secondary-admin">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Tips Table -->
                <div class="admin-table-container" style="margin-top: 2rem;">
                    <div class="admin-table-header">
                        <h2><i class="fas fa-lightbulb"></i> All Tips (<?php echo $tips->num_rows; ?>)</h2>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Submitted By</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($tip = $tips->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $tip['tip_id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($tip['tip_title']); ?></strong><br>
                                        <small style="color: var(--admin-text-light);">
                                            <?php echo htmlspecialchars(substr($tip['tip_content'], 0, 80)); ?>...
                                        </small>
                                    </td>
                                    <td>
                                        <span class="action-badge" style="background: #dbeafe; color: #1e40af;">
                                            <?php echo ucfirst($tip['tip_category']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($tip['user_name'] ?? 'Unknown'); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($tip['created_at'])); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $tip['tip_id']; ?>" class="btn-admin btn-secondary-admin btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $tip['tip_id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this tip?')"
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
