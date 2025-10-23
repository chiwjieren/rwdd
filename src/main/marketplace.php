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

$currentUserId = $_SESSION['user_id'];
$message = "";

// Check for success message
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'request_sent') {
        $message = "Swap request sent successfully!";
    }
}

// Search functionality
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get all items from other users (not current user's items)
if (!empty($searchQuery)) {
    $itemsQuery = $conn->prepare("
        SELECT i.item_id, i.item_name, i.item_description, i.item_image, i.user_id, u.user_name 
        FROM ITEM i 
        JOIN USER u ON i.user_id = u.user_id 
        WHERE i.user_id != ? AND (i.item_name LIKE ? OR i.item_description LIKE ?)
        ORDER BY i.item_id DESC
    ");
    $searchParam = '%' . $searchQuery . '%';
    $itemsQuery->bind_param("iss", $currentUserId, $searchParam, $searchParam);
} else {
    $itemsQuery = $conn->prepare("
        SELECT i.item_id, i.item_name, i.item_description, i.item_image, i.user_id, u.user_name 
        FROM ITEM i 
        JOIN USER u ON i.user_id = u.user_id 
        WHERE i.user_id != ?
        ORDER BY i.item_id DESC
    ");
    $itemsQuery->bind_param("i", $currentUserId);
}

$itemsQuery->execute();
$items = $itemsQuery->get_result();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Marketplace — GoGreenTogether</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .marketplace-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .marketplace-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .marketplace-header h1 {
      color: #333;
      margin: 0 0 10px 0;
    }
    
    .marketplace-header p {
      color: #666;
      font-size: 16px;
    }
    
    .search-section {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    
    .search-bar {
      display: flex;
      gap: 10px;
    }
    
    .search-bar input {
      flex: 1;
      padding: 12px 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
    }
    
    .btn-search {
      background-color: #28a745;
      color: white;
      padding: 12px 30px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
    }
    
    .btn-search:hover {
      background-color: #218838;
    }
    
    .items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 25px;
      margin-top: 20px;
    }
    
    .item-card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .item-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .item-image {
      width: 100%;
      height: 250px;
      object-fit: cover;
      background: #f0f0f0;
    }
    
    .item-image-placeholder {
      width: 100%;
      height: 250px;
      background: #e0e0e0;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .item-content {
      padding: 20px;
    }
    
    .item-content h3 {
      margin: 0 0 10px 0;
      color: #333;
      font-size: 20px;
    }
    
    .item-content p {
      color: #666;
      font-size: 14px;
      margin: 0 0 10px 0;
      line-height: 1.5;
    }
    
    .item-owner {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 15px;
      padding-top: 10px;
      border-top: 1px solid #eee;
      font-size: 14px;
      color: #999;
    }
    
    .btn-swap {
      width: 100%;
      background-color: #28a745;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background-color 0.3s;
    }
    
    .btn-swap:hover {
      background-color: #218838;
    }
    
    .no-items {
      text-align: center;
      padding: 80px 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .no-items i {
      font-size: 80px;
      color: #ddd;
      margin-bottom: 20px;
    }
    
    .no-items h3 {
      color: #666;
      margin: 0 0 10px 0;
    }
    
    .no-items p {
      color: #999;
    }
    
    .alert {
      padding: 15px 20px;
      border-radius: 5px;
      margin-bottom: 20px;
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  
  <main class="marketplace-container">
    <div class="marketplace-header">
      <h1>Green Marketplace</h1>
      <p>Discover and swap eco-friendly items with the community</p>
    </div>
    
    <?php if (!empty($message)): ?>
      <div class="alert">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>
    
    <div class="search-section">
      <form method="GET" action="" class="search-bar">
        <input type="text" name="search" placeholder="Search for items..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit" class="btn-search">
          <i class="fas fa-search"></i> Search
        </button>
      </form>
    </div>
    
    <?php if ($items->num_rows > 0): ?>
      <div class="items-grid">
        <?php while ($item = $items->fetch_assoc()): ?>
          <div class="item-card">
            <?php if (!empty($item['item_image'])): ?>
              <img src="../media/items/<?php echo htmlspecialchars($item['item_image']); ?>" 
                   alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                   class="item-image">
            <?php else: ?>
              <div class="item-image-placeholder">
                <i class="fas fa-box" style="font-size: 80px; color: #ccc;"></i>
              </div>
            <?php endif; ?>
            
            <div class="item-content">
              <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
              <p><?php echo !empty($item['item_description']) ? htmlspecialchars($item['item_description']) : '<em>No description available</em>'; ?></p>
              
              <div class="item-owner">
                <i class="fas fa-user"></i>
                <span><?php echo htmlspecialchars($item['user_name']); ?></span>
              </div>
              
              <a href="send_swap_request.php?receiver_item_id=<?php echo $item['item_id']; ?>" class="btn-swap">
                <i class="fas fa-exchange-alt"></i> Request Swap
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="no-items">
        <i class="fas fa-box-open"></i>
        <h3>No Items Available</h3>
        <?php if (!empty($searchQuery)): ?>
          <p>No items found matching "<?php echo htmlspecialchars($searchQuery); ?>"</p>
          <p><a href="marketplace.php" style="color: #28a745; text-decoration: none; font-weight: 600;">View all items</a></p>
        <?php else: ?>
          <p>There are currently no items available in the marketplace.</p>
          <p>Check back later or <a href="inventory.php" style="color: #28a745; text-decoration: none; font-weight: 600;">add your own items</a>!</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </main>

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
