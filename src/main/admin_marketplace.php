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

// Handle Delete Item
if (isset($_GET['delete'])) {
    $itemId = intval($_GET['delete']);
    
    // Get item image to delete
    $item = $conn->query("SELECT item_image FROM ITEM WHERE item_id = $itemId")->fetch_assoc();
    if ($item && !empty($item['item_image']) && file_exists('../media/items/' . $item['item_image'])) {
        unlink('../media/items/' . $item['item_image']);
    }
    
    // Delete related swap requests
    $conn->query("DELETE FROM SWAP WHERE swap_sender_item_id = $itemId OR swap_receiver_item_id = $itemId");
    
    if ($conn->query("DELETE FROM ITEM WHERE item_id = $itemId")) {
        $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                     VALUES ('admin', 'DELETE', 'ITEM', $itemId, 'Deleted item ID: $itemId')");
        $message = "Item deleted successfully!";
    } else {
        $error = "Error deleting item.";
    }
}

// Handle Edit Item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $itemId = intval($_POST['item_id']);
    $itemName = trim($_POST['item_name']);
    $itemDescription = trim($_POST['item_description']);
    
    $stmt = $conn->prepare("UPDATE ITEM SET item_name = ?, item_description = ? WHERE item_id = ?");
    $stmt->bind_param("ssi", $itemName, $itemDescription, $itemId);
    
    if ($stmt->execute()) {
        $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                     VALUES ('admin', 'UPDATE', 'ITEM', $itemId, 'Updated item: $itemName')");
        $message = "Item updated successfully!";
    } else {
        $error = "Error updating item.";
    }
}

// Get all marketplace items with user info
$items = $conn->query("SELECT i.*, u.user_name FROM ITEM i 
                       LEFT JOIN USER u ON i.user_id = u.user_id 
                       ORDER BY i.item_id DESC");

// Get item for editing if requested
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editItem = $conn->query("SELECT * FROM ITEM WHERE item_id = $editId")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Marketplace - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Manage Marketplace Items</h1>
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

                <?php if ($editItem): ?>
                    <!-- Edit Item Form -->
                    <div class="admin-form">
                        <h2>Edit Marketplace Item</h2>
                        <form method="POST" action="">
                            <input type="hidden" name="item_id" value="<?php echo $editItem['item_id']; ?>">
                            
                            <div class="form-grid">
                                <div class="form-group-admin">
                                    <label>Item Name *</label>
                                    <input type="text" name="item_name" value="<?php echo htmlspecialchars($editItem['item_name']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group-admin" style="margin-top: 1rem;">
                                <label>Description *</label>
                                <textarea name="item_description" required><?php echo htmlspecialchars($editItem['item_description']); ?></textarea>
                            </div>
                            
                            <?php if ($editItem['item_image']): ?>
                                <div class="form-group-admin" style="margin-top: 1rem;">
                                    <label>Current Image</label>
                                    <img src="../media/items/<?php echo htmlspecialchars($editItem['item_image']); ?>" 
                                         style="max-width: 200px; border-radius: 8px; margin-top: 0.5rem;">
                                </div>
                            <?php endif; ?>
                            
                            <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                                <button type="submit" class="btn-admin btn-primary-admin">
                                    <i class="fas fa-save"></i> Update Item
                                </button>
                                <a href="admin_marketplace.php" class="btn-admin btn-secondary-admin">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Items Table -->
                <div class="admin-table-container" style="margin-top: 2rem;">
                    <div class="admin-table-header">
                        <h2><i class="fas fa-store"></i> All Marketplace Items (<?php echo $items->num_rows; ?>)</h2>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Item Name</th>
                                <th>Owner</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($items->num_rows > 0): ?>
                                <?php while ($item = $items->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $item['item_id']; ?></td>
                                    <td>
                                        <?php if ($item['item_image']): ?>
                                            <img src="../media/items/<?php echo htmlspecialchars($item['item_image']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: var(--admin-bg); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-box" style="color: var(--admin-text-light);"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['item_name']); ?></strong><br>
                                        <small style="color: var(--admin-text-light);">
                                            <?php echo htmlspecialchars(substr($item['item_description'], 0, 60)); ?>...
                                        </small>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['user_name'] ?? 'Unknown'); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $item['item_id']; ?>" class="btn-admin btn-secondary-admin btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $item['item_id']; ?>" 
                                           onclick="return confirm('Are you sure? This will also delete related swap requests.')"
                                           class="btn-admin btn-danger-admin btn-sm">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2rem; color: #666;">No marketplace items found.</td>
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
