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

// Get all swap requests (sent and received)
$swapsQuery = $conn->prepare("
    SELECT s.swap_request_id, s.swap_status, s.swap_created_at, s.swap_sender_id, s.swap_receiver_id,
           i1.item_name as sender_item, i1.item_image as sender_item_image,
           i2.item_name as receiver_item, i2.item_image as receiver_item_image,
           u1.user_name as sender_name,
           u2.user_name as receiver_name
    FROM SWAP s
    JOIN ITEM i1 ON s.swap_sender_item_id = i1.item_id
    JOIN ITEM i2 ON s.swap_receiver_item_id = i2.item_id
    JOIN USER u1 ON s.swap_sender_id = u1.user_id
    JOIN USER u2 ON s.swap_receiver_id = u2.user_id
    WHERE s.swap_sender_id = ? OR s.swap_receiver_id = ?
    ORDER BY s.swap_created_at DESC
");
$swapsQuery->bind_param("ii", $userId, $userId);
$swapsQuery->execute();
$swaps = $swapsQuery->get_result();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>My Swaps — GoGreenTogether</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .swaps-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .tabs {
      display: flex;
      gap: 10px;
      margin-bottom: 30px;
      border-bottom: 2px solid #ddd;
    }
    
    .tab {
      padding: 15px 30px;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
      color: #666;
      position: relative;
    }
    
    .tab.active {
      color: #28a745;
    }
    
    .tab.active::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      right: 0;
      height: 2px;
      background: #28a745;
    }
    
    .swap-card {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    
    .swap-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
    }
    
    .swap-type {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
      color: #333;
    }
    
    .swap-type.sent {
      color: #007bff;
    }
    
    .swap-type.received {
      color: #28a745;
    }
    
    .swap-details {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      gap: 30px;
      align-items: center;
    }
    
    .swap-item {
      text-align: center;
    }
    
    .swap-item img {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 15px;
    }
    
    .swap-item .no-image {
      width: 150px;
      height: 150px;
      background: #e0e0e0;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
    }
    
    .swap-item h4 {
      margin: 0 0 5px 0;
      color: #333;
    }
    
    .swap-item p {
      margin: 0;
      font-size: 14px;
      color: #666;
    }
    
    .swap-arrow {
      font-size: 40px;
      color: #28a745;
    }
    
    .status-badge {
      display: inline-block;
      padding: 8px 20px;
      border-radius: 20px;
      font-size: 13px;
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
    
    .swap-date {
      font-size: 13px;
      color: #999;
    }
    
    .no-swaps {
      text-align: center;
      padding: 80px 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .no-swaps i {
      font-size: 80px;
      color: #ddd;
      margin-bottom: 20px;
    }
    
    .no-swaps h3 {
      color: #666;
      margin: 0 0 10px 0;
    }
    
    .no-swaps p {
      color: #999;
    }
    
    .no-swaps a {
      color: #28a745;
      text-decoration: none;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  
  <main class="swaps-container">
    <h1>My Swaps</h1>
    
    <div class="tabs">
      <button class="tab active" onclick="filterSwaps('all')">All Swaps</button>
      <button class="tab" onclick="filterSwaps('pending')">Pending</button>
      <button class="tab" onclick="filterSwaps('approved')">Approved</button>
      <button class="tab" onclick="filterSwaps('rejected')">Rejected</button>
    </div>
    
    <?php if ($swaps->num_rows > 0): ?>
      <div id="swaps-list">
        <?php while ($swap = $swaps->fetch_assoc()): ?>
          <div class="swap-card" data-status="<?php echo $swap['swap_status']; ?>">
            <div class="swap-header">
              <div class="swap-type <?php echo $swap['swap_sender_id'] == $userId ? 'sent' : 'received'; ?>">
                <i class="fas <?php echo $swap['swap_sender_id'] == $userId ? 'fa-paper-plane' : 'fa-inbox'; ?>"></i>
                <span><?php echo $swap['swap_sender_id'] == $userId ? 'Sent to ' . htmlspecialchars($swap['receiver_name']) : 'Received from ' . htmlspecialchars($swap['sender_name']); ?></span>
              </div>
              <div>
                <span class="status-badge <?php echo $swap['swap_status']; ?>">
                  <?php echo ucfirst($swap['swap_status']); ?>
                </span>
                <div class="swap-date" style="text-align: right; margin-top: 5px;">
                  <?php echo date('M d, Y', strtotime($swap['swap_created_at'])); ?>
                </div>
              </div>
            </div>
            
            <div class="swap-details">
              <div class="swap-item">
                <p style="margin-bottom: 10px; font-weight: 600; color: #666;">
                  <?php echo $swap['swap_sender_id'] == $userId ? 'You offered:' : 'They offered:'; ?>
                </p>
                <?php if (!empty($swap['sender_item_image'])): ?>
                  <img src="../media/items/<?php echo htmlspecialchars($swap['sender_item_image']); ?>" alt="<?php echo htmlspecialchars($swap['sender_item']); ?>">
                <?php else: ?>
                  <div class="no-image">
                    <i class="fas fa-box" style="font-size: 50px; color: #ccc;"></i>
                  </div>
                <?php endif; ?>
                <h4><?php echo htmlspecialchars($swap['sender_item']); ?></h4>
                <p>by <?php echo htmlspecialchars($swap['sender_name']); ?></p>
              </div>
              
              <div class="swap-arrow">
                <i class="fas fa-exchange-alt"></i>
              </div>
              
              <div class="swap-item">
                <p style="margin-bottom: 10px; font-weight: 600; color: #666;">
                  <?php echo $swap['swap_receiver_id'] == $userId ? 'You offered:' : 'They wanted:'; ?>
                </p>
                <?php if (!empty($swap['receiver_item_image'])): ?>
                  <img src="../media/items/<?php echo htmlspecialchars($swap['receiver_item_image']); ?>" alt="<?php echo htmlspecialchars($swap['receiver_item']); ?>">
                <?php else: ?>
                  <div class="no-image">
                    <i class="fas fa-box" style="font-size: 50px; color: #ccc;"></i>
                  </div>
                <?php endif; ?>
                <h4><?php echo htmlspecialchars($swap['receiver_item']); ?></h4>
                <p>by <?php echo htmlspecialchars($swap['receiver_name']); ?></p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="no-swaps">
        <i class="fas fa-exchange-alt"></i>
        <h3>No Swap Requests Yet</h3>
        <p>You haven't sent or received any swap requests.</p>
        <p><a href="marketplace.php">Browse the marketplace</a> to start swapping items!</p>
      </div>
    <?php endif; ?>
  </main>
  
  <script>
    function filterSwaps(status) {
      // Update active tab
      document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
      });
      event.currentTarget.classList.add('active');
      
      // Filter swaps
      const swapCards = document.querySelectorAll('.swap-card');
      swapCards.forEach(card => {
        if (status === 'all' || card.dataset.status === status) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }
  </script>
</body>
</html>
<?php
$conn->close();
?>
