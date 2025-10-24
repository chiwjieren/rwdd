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

// Handle Delete Event
if (isset($_GET['delete'])) {
    $eventId = intval($_GET['delete']);
    
    if ($conn->query("DELETE FROM EVENT WHERE event_id = $eventId")) {
        $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                     VALUES ('admin', 'DELETE', 'EVENT', $eventId, 'Deleted event ID: $eventId')");
        $message = "Event deleted successfully!";
    } else {
        $error = "Error deleting event.";
    }
}

// Handle Create/Edit Event
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eventTitle = trim($_POST['event_title']);
    $eventDescription = trim($_POST['event_description']);
    $eventDate = $_POST['event_date'];
    $eventTime = $_POST['event_time'];
    $eventLocation = trim($_POST['event_location']);
    
    if (isset($_POST['event_id']) && !empty($_POST['event_id'])) {
        // Update existing event
        $eventId = intval($_POST['event_id']);
        $stmt = $conn->prepare("UPDATE EVENT SET event_title = ?, event_description = ?, event_date = ?, event_time = ?, event_location = ? WHERE event_id = ?");
        $stmt->bind_param("sssssi", $eventTitle, $eventDescription, $eventDate, $eventTime, $eventLocation, $eventId);
        
        if ($stmt->execute()) {
            $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                         VALUES ('admin', 'UPDATE', 'EVENT', $eventId, 'Updated event: $eventTitle')");
            $message = "Event updated successfully!";
        } else {
            $error = "Error updating event.";
        }
    } else {
        // Create new event
        $stmt = $conn->prepare("INSERT INTO EVENT (event_title, event_description, event_date, event_time, event_location) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $eventTitle, $eventDescription, $eventDate, $eventTime, $eventLocation);
        
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                         VALUES ('admin', 'CREATE', 'EVENT', $newId, 'Created new event: $eventTitle')");
            $message = "Event created successfully!";
        } else {
            $error = "Error creating event.";
        }
    }
}

// Get all events
$events = $conn->query("SELECT * FROM EVENT ORDER BY event_date DESC");

// Get event for editing if requested
$editEvent = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editEvent = $conn->query("SELECT * FROM EVENT WHERE event_id = $editId")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Manage Events</h1>
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

                <!-- Add/Edit Event Form -->
                <div class="admin-form">
                    <h2><?php echo $editEvent ? 'Edit Event' : 'Add New Event'; ?></h2>
                    <form method="POST" action="">
                        <?php if ($editEvent): ?>
                            <input type="hidden" name="event_id" value="<?php echo $editEvent['event_id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-grid">
                            <div class="form-group-admin">
                                <label>Event Title *</label>
                                <input type="text" name="event_title" value="<?php echo $editEvent['event_title'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group-admin">
                                <label>Location *</label>
                                <input type="text" name="event_location" value="<?php echo $editEvent['event_location'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group-admin">
                                <label>Date *</label>
                                <input type="date" name="event_date" value="<?php echo $editEvent['event_date'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group-admin">
                                <label>Time *</label>
                                <input type="time" name="event_time" value="<?php echo $editEvent['event_time'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group-admin" style="margin-top: 1rem;">
                            <label>Description *</label>
                            <textarea name="event_description" required><?php echo $editEvent['event_description'] ?? ''; ?></textarea>
                        </div>
                        
                        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                            <button type="submit" class="btn-admin btn-primary-admin">
                                <i class="fas fa-save"></i>
                                <?php echo $editEvent ? 'Update Event' : 'Create Event'; ?>
                            </button>
                            <?php if ($editEvent): ?>
                                <a href="admin_events.php" class="btn-admin btn-secondary-admin">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Events Table -->
                <div class="admin-table-container" style="margin-top: 2rem;">
                    <div class="admin-table-header">
                        <h2><i class="fas fa-calendar"></i> All Events (<?php echo $events->num_rows; ?>)</h2>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Date & Time</th>
                                <th>Location</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($event = $events->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $event['event_id']; ?></td>
                                    <td><?php echo htmlspecialchars($event['event_title']); ?></td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($event['event_date'])); ?><br>
                                        <small style="color: var(--admin-text-light);"><?php echo date('h:i A', strtotime($event['event_time'])); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($event['event_location']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($event['created_at'])); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $event['event_id']; ?>" class="btn-admin btn-secondary-admin btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $event['event_id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this event?')"
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
