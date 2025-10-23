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

$senderId = $_SESSION['user_id'];
$message = "";
$error = "";

// Handle swap request submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_request'])) {
    $senderItemId = intval($_POST['sender_item_id']);
    $receiverItemId = intval($_POST['receiver_item_id']);
    
    // Get receiver's user_id from the item
    $receiverQuery = $conn->prepare("SELECT user_id FROM ITEM WHERE item_id = ?");
    $receiverQuery->bind_param("i", $receiverItemId);
    $receiverQuery->execute();
    $receiverResult = $receiverQuery->get_result();
    
    if ($receiverResult->num_rows > 0) {
        $receiverData = $receiverResult->fetch_assoc();
        $receiverId = $receiverData['user_id'];
        
        // Validate that sender owns the sender item
        $validateQuery = $conn->prepare("SELECT item_id FROM ITEM WHERE item_id = ? AND user_id = ?");
        $validateQuery->bind_param("ii", $senderItemId, $senderId);
        $validateQuery->execute();
        $validateResult = $validateQuery->get_result();
        
        if ($validateResult->num_rows > 0) {
            // Check if a pending request already exists
            $checkQuery = $conn->prepare("SELECT swap_request_id FROM SWAP WHERE swap_sender_id = ? AND swap_receiver_id = ? AND swap_sender_item_id = ? AND swap_receiver_item_id = ? AND swap_status = 'pending'");
            $checkQuery->bind_param("iiii", $senderId, $receiverId, $senderItemId, $receiverItemId);
            $checkQuery->execute();
            $checkResult = $checkQuery->get_result();
            
            if ($checkResult->num_rows > 0) {
                $error = "You already have a pending swap request for these items.";
            } else {
                // Insert swap request
                $insertSwap = $conn->prepare("INSERT INTO SWAP (swap_sender_id, swap_receiver_id, swap_sender_item_id, swap_receiver_item_id) VALUES (?, ?, ?, ?)");
                $insertSwap->bind_param("iiii", $senderId, $receiverId, $senderItemId, $receiverItemId);
                
                if ($insertSwap->execute()) {
                    $swapRequestId = $conn->insert_id;
                    
                    // Get sender's name and item names
                    $senderName = $_SESSION['username'];
                    $itemQuery = $conn->prepare("SELECT item_name FROM ITEM WHERE item_id = ?");
                    $itemQuery->bind_param("i", $senderItemId);
                    $itemQuery->execute();
                    $senderItemName = $itemQuery->get_result()->fetch_assoc()['item_name'];
                    
                    $itemQuery->bind_param("i", $receiverItemId);
                    $itemQuery->execute();
                    $receiverItemName = $itemQuery->get_result()->fetch_assoc()['item_name'];
                    
                    // Create notification for receiver
                    $notificationMessage = "$senderName wants to swap their '$senderItemName' for your '$receiverItemName'";
                    $insertNotification = $conn->prepare("INSERT INTO NOTIFICATION (user_id, notification_type, swap_request_id, notification_message) VALUES (?, 'swap_request', ?, ?)");
                    $insertNotification->bind_param("iis", $receiverId, $swapRequestId, $notificationMessage);
                    $insertNotification->execute();
                    
                    $message = "Swap request sent successfully!";
                    header("Location: marketplace.php?success=request_sent");
                    exit();
                } else {
                    $error = "Error sending swap request: " . $conn->error;
                }
            }
        } else {
            $error = "You don't own the item you're trying to swap.";
        }
    } else {
        $error = "Invalid item selected.";
    }
}

// Get receiver item details if provided
$receiverItemId = isset($_GET['receiver_item_id']) ? intval($_GET['receiver_item_id']) : 0;
$receiverItem = null;

if ($receiverItemId > 0) {
    $itemQuery = $conn->prepare("SELECT i.item_id, i.item_name, i.item_description, i.item_image, u.user_name FROM ITEM i JOIN USER u ON i.user_id = u.user_id WHERE i.item_id = ?");
    $itemQuery->bind_param("i", $receiverItemId);
    $itemQuery->execute();
    $result = $itemQuery->get_result();
    
    if ($result->num_rows > 0) {
        $receiverItem = $result->fetch_assoc();
    }
}

// Get sender's items
$senderItemsQuery = $conn->prepare("SELECT item_id, item_name, item_description, item_image FROM ITEM WHERE user_id = ?");
$senderItemsQuery->bind_param("i", $senderId);
$senderItemsQuery->execute();
$senderItems = $senderItemsQuery->get_result();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Send Swap Request — GoGreenTogether</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .swap-container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .swap-section {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    
    .swap-preview {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      gap: 20px;
      align-items: center;
      margin-bottom: 30px;
    }
    
    .item-preview {
      background: #f9f9f9;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
    }
    
    .item-preview img {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 15px;
    }
    
    .item-preview .no-image {
      width: 200px;
      height: 200px;
      background: #e0e0e0;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
    }
    
    .swap-icon {
      font-size: 40px;
      color: #28a745;
    }
    
    .item-preview h3 {
      margin: 0 0 10px 0;
      color: #333;
    }
    
    .item-preview p {
      color: #666;
      font-size: 14px;
      margin: 5px 0;
    }
    
    .your-items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    
    .selectable-item {
      background: white;
      border: 2px solid #ddd;
      border-radius: 10px;
      padding: 15px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .selectable-item:hover {
      border-color: #28a745;
      transform: translateY(-2px);
    }
    
    .selectable-item.selected {
      border-color: #28a745;
      background: #f0f9f0;
    }
    
    .selectable-item img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 10px;
    }
    
    .selectable-item .no-image {
      width: 100%;
      height: 150px;
      background: #e0e0e0;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 10px;
    }
    
    .btn-primary {
      background-color: #28a745;
      color: white;
      padding: 12px 30px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
    }
    
    .btn-primary:hover {
      background-color: #218838;
    }
    
    .btn-primary:disabled {
      background-color: #ccc;
      cursor: not-allowed;
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
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .swap-container {
        margin: 20px auto;
        padding: 10px;
      }
      
      .swap-section {
        padding: 20px 15px;
      }
      
      .swap-preview {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .swap-icon {
        transform: rotate(90deg);
        font-size: 30px;
      }
      
      .item-preview img,
      .item-preview .no-image {
        width: 150px;
        height: 150px;
      }
      
      .your-items-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .selectable-item img,
      .selectable-item .no-image {
        height: 200px;
      }
      
      .btn-primary {
        width: 100%;
        padding: 15px;
      }
    }
    
    @media (min-width: 769px) and (max-width: 1024px) {
      .your-items-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .item-preview img,
      .item-preview .no-image {
        width: 180px;
        height: 180px;
      }
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  
  <main class="swap-container">
    <h1>Send Swap Request</h1>
    
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
    
    <?php if ($receiverItem): ?>
      <div class="swap-section">
        <h2>Swap Preview</h2>
        <div class="swap-preview">
          <div class="item-preview">
            <h4>You Offer:</h4>
            <div id="selectedItemPreview">
              <p style="color: #999;">Select your item below</p>
            </div>
          </div>
          
          <div class="swap-icon">
            <i class="fas fa-exchange-alt"></i>
          </div>
          
          <div class="item-preview">
            <h4>You Receive:</h4>
            <?php if (!empty($receiverItem['item_image'])): ?>
              <img src="../media/items/<?php echo htmlspecialchars($receiverItem['item_image']); ?>" alt="<?php echo htmlspecialchars($receiverItem['item_name']); ?>">
            <?php else: ?>
              <div class="no-image">
                <i class="fas fa-box" style="font-size: 60px; color: #ccc;"></i>
              </div>
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($receiverItem['item_name']); ?></h3>
            <p><strong>Owner:</strong> <?php echo htmlspecialchars($receiverItem['user_name']); ?></p>
            <p><?php echo htmlspecialchars($receiverItem['item_description']); ?></p>
          </div>
        </div>
        
        <form method="POST" action="" id="swapForm">
          <input type="hidden" name="receiver_item_id" value="<?php echo $receiverItemId; ?>">
          <input type="hidden" name="sender_item_id" id="senderItemId" value="">
          
          <h3>Select Your Item to Swap</h3>
          
          <?php if ($senderItems->num_rows > 0): ?>
            <div class="your-items-grid">
              <?php while ($item = $senderItems->fetch_assoc()): ?>
                <div class="selectable-item" onclick="selectItem(<?php echo $item['item_id']; ?>, '<?php echo htmlspecialchars($item['item_name'], ENT_QUOTES); ?>', '<?php echo !empty($item['item_image']) ? htmlspecialchars($item['item_image'], ENT_QUOTES) : ''; ?>')">
                  <?php if (!empty($item['item_image'])): ?>
                    <img src="../media/items/<?php echo htmlspecialchars($item['item_image']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                  <?php else: ?>
                    <div class="no-image">
                      <i class="fas fa-box" style="font-size: 40px; color: #ccc;"></i>
                    </div>
                  <?php endif; ?>
                  <h4><?php echo htmlspecialchars($item['item_name']); ?></h4>
                  <p><?php echo htmlspecialchars($item['item_description']); ?></p>
                </div>
              <?php endwhile; ?>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
              <button type="submit" name="send_request" class="btn-primary" id="submitBtn" disabled>
                <i class="fas fa-paper-plane"></i> Send Swap Request
              </button>
            </div>
          <?php else: ?>
            <p style="text-align: center; color: #999; padding: 40px 0;">
              You don't have any items to swap. <a href="inventory.php">Add items to your inventory</a> first.
            </p>
          <?php endif; ?>
        </form>
      </div>
    <?php else: ?>
      <div class="swap-section">
        <p style="text-align: center; color: #999;">Invalid item selected. <a href="marketplace.php">Browse marketplace</a></p>
      </div>
    <?php endif; ?>
  </main>
  
  <script>
    let selectedItemId = null;
    
    function selectItem(itemId, itemName, itemImage) {
      // Remove previous selection
      document.querySelectorAll('.selectable-item').forEach(item => {
        item.classList.remove('selected');
      });
      
      // Add selection to clicked item
      event.currentTarget.classList.add('selected');
      
      // Update hidden input
      selectedItemId = itemId;
      document.getElementById('senderItemId').value = itemId;
      
      // Update preview
      const preview = document.getElementById('selectedItemPreview');
      if (itemImage) {
        preview.innerHTML = `
          <img src="../media/items/${itemImage}" alt="${itemName}" style="width: 200px; height: 200px; object-fit: cover; border-radius: 10px; margin-bottom: 15px;">
          <h3>${itemName}</h3>
        `;
      } else {
        preview.innerHTML = `
          <div class="no-image" style="width: 200px; height: 200px; margin: 0 auto 15px;">
            <i class="fas fa-box" style="font-size: 60px; color: #ccc;"></i>
          </div>
          <h3>${itemName}</h3>
        `;
      }
      
      // Enable submit button
      document.getElementById('submitBtn').disabled = false;
    }
  </script>

  <footer class="footer">
    <div class="footer-content">
      <div class="footer-section">
        <h3>GoGreenTogether</h3>
        <p>Making sustainability accessible and engaging for everyone. Join our community and make a difference.</p>
        <div class="social-links">
          <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
          <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="https://www.linkedin.com" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
        </div>
      </div>
      
      <div class="footer-section">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="aboutus.php">About Us</a></li>
          <li><a href="event.php">Events</a></li>
          <li><a href="marketplace.php">Marketplace</a></li>
          <li><a href="tips.php">Tips</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4>Get Involved</h4>
        <ul>
          <li><a href="https://wa.me/60123456789" target="_blank">Volunteer</a></li>
          <li><a href="https://wa.me/60123456789" target="_blank">Partner With Us</a></li>
          <li><a href="https://wa.me/60123456789" target="_blank">Share Your Story</a></li>
          <li><a href="index.php#newsletter-section">Newsletter</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4>Contact Us</h4>
        <ul class="contact-info">
          <li><a href="mailto:info@gogreentogether.org"><i class="fas fa-envelope"></i> info@gogreentogether.org</a></li>
          <li><a href="https://wa.me/60123456789" target="_blank"><i class="fas fa-phone"></i> +60 12-345-6789</a></li>
          <li><a href="https://www.google.com/maps/search/Asia+Pacific+University,+Kuala+Lumpur" target="_blank"><i class="fas fa-map-marker-alt"></i> Asia Pacific University</a></li>
        </ul>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; 2025 GoGreenTogether. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
<?php
$conn->close();
?>
