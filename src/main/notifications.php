<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'rwdd_assignment');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];
$message = "";
$error = "";

// Handle swap request approval
if (isset($_POST['approve_swap'])) {
    $swapRequestId = intval($_POST['swap_request_id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get swap details first
        $swapQuery = $conn->prepare("SELECT s.swap_sender_id, s.swap_receiver_id, s.swap_sender_item_id, s.swap_receiver_item_id, u.user_name as sender_name, i1.item_name as sender_item, i2.item_name as receiver_item FROM SWAP s JOIN USER u ON s.swap_sender_id = u.user_id JOIN ITEM i1 ON s.swap_sender_item_id = i1.item_id JOIN ITEM i2 ON s.swap_receiver_item_id = i2.item_id WHERE s.swap_request_id = ? AND s.swap_receiver_id = ?");
        $swapQuery->bind_param("ii", $swapRequestId, $userId);
        $swapQuery->execute();
        $swapData = $swapQuery->get_result()->fetch_assoc();
        
        if (!$swapData) {
            throw new Exception("Swap request not found or you don't have permission.");
        }
        
        // Transfer item ownership
        // Item from sender goes to receiver
        $transferItem1 = $conn->prepare("UPDATE ITEM SET user_id = ? WHERE item_id = ?");
        $transferItem1->bind_param("ii", $swapData['swap_receiver_id'], $swapData['swap_sender_item_id']);
        $transferItem1->execute();
        
        // Item from receiver goes to sender
        $transferItem2 = $conn->prepare("UPDATE ITEM SET user_id = ? WHERE item_id = ?");
        $transferItem2->bind_param("ii", $swapData['swap_sender_id'], $swapData['swap_receiver_item_id']);
        $transferItem2->execute();
        
        // Update swap status to approved
        $updateSwap = $conn->prepare("UPDATE SWAP SET swap_status = 'approved' WHERE swap_request_id = ?");
        $updateSwap->bind_param("i", $swapRequestId);
        $updateSwap->execute();
        
        // Create notification for sender (request approved)
        $notificationMessage = "Your swap request has been approved! You now have '{$swapData['receiver_item']}' and they received your '{$swapData['sender_item']}'.";
        $insertNotification = $conn->prepare("INSERT INTO NOTIFICATION (user_id, notification_type, swap_request_id, notification_message) VALUES (?, 'swap_approved', ?, ?)");
        $insertNotification->bind_param("iis", $swapData['swap_sender_id'], $swapRequestId, $notificationMessage);
        $insertNotification->execute();
        
        // Mark the original notification as read
        $markRead = $conn->prepare("UPDATE NOTIFICATION SET is_read = TRUE WHERE swap_request_id = ? AND user_id = ?");
        $markRead->bind_param("ii", $swapRequestId, $userId);
        $markRead->execute();
        
        $conn->commit();
        $message = "Swap approved! Items have been exchanged successfully.";
        
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error approving swap request: " . $e->getMessage();
    }
}

// Handle swap request rejection
if (isset($_POST['reject_swap'])) {
    $swapRequestId = intval($_POST['swap_request_id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update swap status to rejected
        $updateSwap = $conn->prepare("UPDATE SWAP SET swap_status = 'rejected' WHERE swap_request_id = ? AND swap_receiver_id = ?");
        $updateSwap->bind_param("ii", $swapRequestId, $userId);
        $updateSwap->execute();
        
        // Get swap details
        $swapQuery = $conn->prepare("SELECT s.swap_sender_id, i1.item_name as sender_item, i2.item_name as receiver_item FROM SWAP s JOIN ITEM i1 ON s.swap_sender_item_id = i1.item_id JOIN ITEM i2 ON s.swap_receiver_item_id = i2.item_id WHERE s.swap_request_id = ?");
        $swapQuery->bind_param("i", $swapRequestId);
        $swapQuery->execute();
        $swapData = $swapQuery->get_result()->fetch_assoc();
        
        // Create notification for sender (request rejected)
        $notificationMessage = "Your swap request for '{$swapData['receiver_item']}' has been declined.";
        $insertNotification = $conn->prepare("INSERT INTO NOTIFICATION (user_id, notification_type, swap_request_id, notification_message) VALUES (?, 'swap_rejected', ?, ?)");
        $insertNotification->bind_param("iis", $swapData['swap_sender_id'], $swapRequestId, $notificationMessage);
        $insertNotification->execute();
        
        // Mark the original notification as read
        $markRead = $conn->prepare("UPDATE NOTIFICATION SET is_read = TRUE WHERE swap_request_id = ? AND user_id = ?");
        $markRead->bind_param("ii", $swapRequestId, $userId);
        $markRead->execute();
        
        $conn->commit();
        $message = "Swap request rejected.";
        
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error rejecting swap request: " . $e->getMessage();
    }
}

// Mark notification as read
if (isset($_GET['mark_read'])) {
    $notificationId = intval($_GET['mark_read']);
    $markRead = $conn->prepare("UPDATE NOTIFICATION SET is_read = TRUE WHERE notification_id = ? AND user_id = ?");
    $markRead->bind_param("ii", $notificationId, $userId);
    $markRead->execute();
}

// Get all notifications for the user
$notificationsQuery = $conn->prepare("
    SELECT n.notification_id, n.notification_type, n.notification_message, n.is_read, n.notification_created_at,
           s.swap_request_id, s.swap_status, s.swap_sender_id, s.swap_receiver_id,
           i1.item_name as sender_item, i1.item_image as sender_item_image,
           i2.item_name as receiver_item, i2.item_image as receiver_item_image,
           u.user_name as sender_name
    FROM NOTIFICATION n
    LEFT JOIN SWAP s ON n.swap_request_id = s.swap_request_id
    LEFT JOIN ITEM i1 ON s.swap_sender_item_id = i1.item_id
    LEFT JOIN ITEM i2 ON s.swap_receiver_item_id = i2.item_id
    LEFT JOIN USER u ON s.swap_sender_id = u.user_id
    WHERE n.user_id = ?
    ORDER BY n.notification_created_at DESC
");
$notificationsQuery->bind_param("i", $userId);
$notificationsQuery->execute();
$notifications = $notificationsQuery->get_result();

// Count unread notifications
$unreadQuery = $conn->prepare("SELECT COUNT(*) as unread_count FROM NOTIFICATION WHERE user_id = ? AND is_read = FALSE");
$unreadQuery->bind_param("i", $userId);
$unreadQuery->execute();
$unreadCount = $unreadQuery->get_result()->fetch_assoc()['unread_count'];
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Notifications — GoGreenTogether</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .notifications-container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .notifications-header {
      background: white;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .notification-card {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 15px;
      border-left: 4px solid #28a745;
    }
    
    .notification-card.unread {
      background: #f0f9f0;
      border-left-color: #28a745;
    }
    
    .notification-card.read {
      border-left-color: #ccc;
      opacity: 0.8;
    }
    
    .notification-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 15px;
    }
    
    .notification-type {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .notification-type i {
      font-size: 24px;
    }
    
    .notification-type.swap_request i {
      color: #007bff;
    }
    
    .notification-type.swap_approved i {
      color: #28a745;
    }
    
    .notification-type.swap_rejected i {
      color: #dc3545;
    }
    
    .notification-time {
      font-size: 12px;
      color: #999;
    }
    
    .notification-message {
      font-size: 16px;
      color: #333;
      margin-bottom: 15px;
    }
    
    .swap-details {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      gap: 20px;
      align-items: center;
      background: #f9f9f9;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    
    .swap-item {
      text-align: center;
    }
    
    .swap-item img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 10px;
    }
    
    .swap-item .no-image {
      width: 120px;
      height: 120px;
      background: #e0e0e0;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 10px;
    }
    
    .swap-arrow {
      font-size: 30px;
      color: #28a745;
    }
    
    .notification-actions {
      display: flex;
      gap: 10px;
    }
    
    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    
    .btn-approve {
      background-color: #28a745;
      color: white;
    }
    
    .btn-approve:hover {
      background-color: #218838;
    }
    
    .btn-reject {
      background-color: #dc3545;
      color: white;
    }
    
    .btn-reject:hover {
      background-color: #c82333;
    }
    
    .btn-read {
      background-color: #6c757d;
      color: white;
    }
    
    .btn-read:hover {
      background-color: #5a6268;
    }
    
    .status-badge {
      display: inline-block;
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
    }
    
    .status-badge.pending {
      background: #fff3cd;
      color: #856404;
    }
    
    .status-badge.approved {
      background: #d4edda;
      color: #155724;
    }
    
    .status-badge.rejected {
      background: #f8d7da;
      color: #721c24;
    }
    
    .no-notifications {
      text-align: center;
      padding: 60px 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .no-notifications i {
      font-size: 60px;
      color: #ddd;
      margin-bottom: 20px;
    }
    
    .alert {
      padding: 12px 20px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  
  <main class="notifications-container">
    <div class="notifications-header">
      <div>
        <h1 style="margin: 0;">Notifications</h1>
        <p style="margin: 5px 0 0 0; color: #666;"><?php echo $unreadCount; ?> unread notification<?php echo $unreadCount != 1 ? 's' : ''; ?></p>
      </div>
    </div>
    
    <?php if (!empty($error)): ?>
      <div class="alert alert-error">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>
    
    <?php if (!empty($message)): ?>
      <div class="alert alert-success">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>
    
    <?php if ($notifications->num_rows > 0): ?>
      <?php while ($notification = $notifications->fetch_assoc()): ?>
        <div class="notification-card <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
          <div class="notification-header">
            <div class="notification-type <?php echo $notification['notification_type']; ?>">
              <?php if ($notification['notification_type'] == 'swap_request'): ?>
                <i class="fas fa-envelope"></i>
              <?php elseif ($notification['notification_type'] == 'swap_approved'): ?>
                <i class="fas fa-check-circle"></i>
              <?php else: ?>
                <i class="fas fa-times-circle"></i>
              <?php endif; ?>
              <span>
                <?php 
                  if ($notification['notification_type'] == 'swap_request') echo 'New Swap Request';
                  elseif ($notification['notification_type'] == 'swap_approved') echo 'Request Approved';
                  else echo 'Request Declined';
                ?>
              </span>
            </div>
            <div class="notification-time">
              <?php echo date('M d, Y g:i A', strtotime($notification['notification_created_at'])); ?>
            </div>
          </div>
          
          <div class="notification-message">
            <?php echo htmlspecialchars($notification['notification_message']); ?>
          </div>
          
          <?php if ($notification['notification_type'] == 'swap_request' && $notification['swap_status'] == 'pending'): ?>
            <div class="swap-details">
              <div class="swap-item">
                <p style="margin: 0 0 10px 0; color: #666; font-size: 12px;">They offer:</p>
                <?php if (!empty($notification['sender_item_image'])): ?>
                  <img src="../media/items/<?php echo htmlspecialchars($notification['sender_item_image']); ?>" alt="<?php echo htmlspecialchars($notification['sender_item']); ?>">
                <?php else: ?>
                  <div class="no-image">
                    <i class="fas fa-box" style="font-size: 40px; color: #ccc;"></i>
                  </div>
                <?php endif; ?>
                <p style="font-weight: 600; margin: 0;"><?php echo htmlspecialchars($notification['sender_item']); ?></p>
                <p style="font-size: 12px; color: #666; margin: 5px 0 0 0;">by <?php echo htmlspecialchars($notification['sender_name']); ?></p>
              </div>
              
              <div class="swap-arrow">
                <i class="fas fa-exchange-alt"></i>
              </div>
              
              <div class="swap-item">
                <p style="margin: 0 0 10px 0; color: #666; font-size: 12px;">For your:</p>
                <?php if (!empty($notification['receiver_item_image'])): ?>
                  <img src="../media/items/<?php echo htmlspecialchars($notification['receiver_item_image']); ?>" alt="<?php echo htmlspecialchars($notification['receiver_item']); ?>">
                <?php else: ?>
                  <div class="no-image">
                    <i class="fas fa-box" style="font-size: 40px; color: #ccc;"></i>
                  </div>
                <?php endif; ?>
                <p style="font-weight: 600; margin: 0;"><?php echo htmlspecialchars($notification['receiver_item']); ?></p>
              </div>
            </div>
            
            <form method="POST" action="" class="notification-actions">
              <input type="hidden" name="swap_request_id" value="<?php echo $notification['swap_request_id']; ?>">
              <button type="submit" name="approve_swap" class="btn btn-approve" onclick="return confirm('Are you sure you want to approve this swap?')">
                <i class="fas fa-check"></i> Approve Swap
              </button>
              <button type="submit" name="reject_swap" class="btn btn-reject" onclick="return confirm('Are you sure you want to reject this swap?')">
                <i class="fas fa-times"></i> Decline
              </button>
            </form>
          <?php else: ?>
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span class="status-badge <?php echo $notification['swap_status']; ?>">
                <?php echo ucfirst($notification['swap_status']); ?>
              </span>
              <?php if (!$notification['is_read']): ?>
                <a href="?mark_read=<?php echo $notification['notification_id']; ?>" class="btn btn-read">
                  <i class="fas fa-check"></i> Mark as Read
                </a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="no-notifications">
        <i class="fas fa-bell-slash"></i>
        <h3>No Notifications</h3>
        <p>You don't have any notifications yet.</p>
      </div>
    <?php endif; ?>
  </main>

  <footer class="footer">
    <div class="footer-content">
      <div class="footer-section">
        <h3>GoGreenTogether</h3>
        <p>Making sustainability accessible and engaging for everyone. Join our community and make a difference.</p>
        <div class="social-links">
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
        </div>
      </div>
      
      <div class="footer-section">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="aboutus.php">About Us</a></li>
          <li><a href="event.php">Events</a></li>
          <li><a href="marketplace.php">Marketplace</a></li>
          <li><a href="tips.php">Eco Tips</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4>Get Involved</h4>
        <ul>
          <li><a href="#">Volunteer</a></li>
          <li><a href="#">Partner With Us</a></li>
          <li><a href="#">Share Your Story</a></li>
          <li><a href="#">Newsletter</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4>Contact Us</h4>
        <ul class="contact-info">
          <li><i class="fas fa-envelope"></i> info@gogreentogether.org</li>
          <li><i class="fas fa-phone"></i> +60 12-345-6789</li>
          <li><i class="fas fa-map-marker-alt"></i> Asia Pacific University</li>
        </ul>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; 2025 GoGreenTogether. All rights reserved.</p>
      <div class="footer-bottom-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Cookie Policy</a>
      </div>
    </div>
  </footer>
</body>
</html>
<?php
$conn->close();
?>
