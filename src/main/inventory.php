<?php
// Start session only if not already started
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

// Handle item upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_item'])) {
    $itemName = trim($_POST['item_name']);
    $itemDescription = trim($_POST['item_description']);
    $itemImage = null;
    
    // Validate input
    if (empty($itemName)) {
        $error = "Item name is required!";
    } else {
        // Handle image upload
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (in_array($_FILES['item_image']['type'], $allowedTypes) && $_FILES['item_image']['size'] <= $maxSize) {
                $extension = pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION);
                $fileName = 'item_' . $userId . '_' . time() . '.' . $extension;
                $uploadPath = '../media/items/' . $fileName;
                
                if (move_uploaded_file($_FILES['item_image']['tmp_name'], $uploadPath)) {
                    $itemImage = $fileName;
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Invalid image file. Please upload JPG, PNG, or GIF under 5MB.";
            }
        }
        
        // Insert item if no errors
        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO ITEM (item_name, item_description, item_image, user_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $itemName, $itemDescription, $itemImage, $userId);
            
            if ($stmt->execute()) {
                $message = "Item added successfully!";
            } else {
                $error = "Error adding item: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Handle item deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $itemId = intval($_GET['delete']);
    
    // Check if item is involved in any swap requests
    $swapCheck = $conn->prepare("SELECT COUNT(*) as swap_count FROM SWAP WHERE (swap_sender_item_id = ? OR swap_receiver_item_id = ?) AND swap_status IN ('pending', 'approved')");
    $swapCheck->bind_param("ii", $itemId, $itemId);
    $swapCheck->execute();
    $swapResult = $swapCheck->get_result()->fetch_assoc();
    
    if ($swapResult['swap_count'] > 0) {
        $error = "Cannot delete this item. It is involved in active swap requests. Please cancel or complete those swaps first.";
    } else {
        // Get item details to delete image file
        $stmt = $conn->prepare("SELECT item_image FROM ITEM WHERE item_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $itemId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $item = $result->fetch_assoc();
            
            // Delete the item from database
            $deleteStmt = $conn->prepare("DELETE FROM ITEM WHERE item_id = ? AND user_id = ?");
            $deleteStmt->bind_param("ii", $itemId, $userId);
            
            if ($deleteStmt->execute()) {
                // Delete image file if exists
                if (!empty($item['item_image']) && file_exists('../media/items/' . $item['item_image'])) {
                    unlink('../media/items/' . $item['item_image']);
                }
                $message = "Item deleted successfully!";
            } else {
                $error = "Error deleting item.";
            }
            $deleteStmt->close();
        } else {
            $error = "Item not found or you don't have permission to delete it.";
        }
        $stmt->close();
    }
    $swapCheck->close();
}

// Get all user's items
$itemsQuery = $conn->prepare("SELECT item_id, item_name, item_description, item_image FROM ITEM WHERE user_id = ? ORDER BY item_id DESC");
$itemsQuery->bind_param("i", $userId);
$itemsQuery->execute();
$itemsResult = $itemsQuery->get_result();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Inventory — GoGreenTogether</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="../css/inventory.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .upload-section {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    
    .upload-section h2 {
      margin-top: 0;
      color: #333;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }
    
    .form-group input[type="text"],
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
      font-family: inherit;
    }
    
    .form-group textarea {
      min-height: 100px;
      resize: vertical;
    }
    
    .image-upload {
      border: 2px dashed #ddd;
      border-radius: 5px;
      padding: 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .image-upload:hover {
      border-color: #28a745;
      background: #f9f9f9;
    }
    
    .image-upload input[type="file"] {
      display: none;
    }
    
    .image-preview {
      max-width: 200px;
      max-height: 200px;
      margin: 10px auto;
      display: none;
    }
    
    .image-preview img {
      max-width: 100%;
      border-radius: 5px;
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
    
    .inventory-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }
    
    .item-card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    
    .item-card:hover {
      transform: translateY(-5px);
    }
    
    .item-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      background: #f0f0f0;
    }
    
    .item-content {
      padding: 20px;
    }
    
    .item-content h3 {
      margin: 0 0 10px 0;
      color: #333;
      font-size: 18px;
    }
    
    .item-content p {
      margin: 0 0 15px 0;
      color: #666;
      font-size: 14px;
    }
    
    .item-actions {
      display: flex;
      gap: 10px;
    }
    
    .btn-delete {
      background-color: #dc3545;
      color: white;
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      text-decoration: none;
      display: inline-block;
    }
    
    .btn-delete:hover {
      background-color: #c82333;
    }
    
    .no-items {
      text-align: center;
      padding: 60px 20px;
      color: #999;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .no-items i {
      font-size: 60px;
      margin-bottom: 20px;
      color: #ddd;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .upload-section {
        padding: 20px 15px;
      }
      
      .inventory-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .item-card {
        max-width: 100%;
      }
      
      .form-group input[type="text"],
      .form-group textarea {
        font-size: 16px; /* Prevent zoom on iOS */
      }
      
      .btn-primary {
        width: 100%;
        padding: 15px;
      }
      
      .item-actions {
        flex-direction: column;
      }
      
      .btn-delete {
        width: 100%;
        text-align: center;
      }
    }
    
    @media (min-width: 769px) and (max-width: 1024px) {
      .inventory-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  
  <main class="container">
    <h1>Your Inventory</h1>
    
    <!-- Upload Section -->
    <div class="upload-section">
      <h2>Add New Item</h2>
      
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
      
      <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
          <label for="item_name">Item Name *</label>
          <input type="text" id="item_name" name="item_name" required placeholder="Enter item name">
        </div>
        
        <div class="form-group">
          <label for="item_description">Description</label>
          <textarea id="item_description" name="item_description" placeholder="Describe your item (optional)"></textarea>
        </div>
        
        <div class="form-group">
          <label>Item Image (Optional)</label>
          <div class="image-upload" onclick="document.getElementById('item_image').click()">
            <i class="fas fa-cloud-upload-alt" style="font-size: 40px; color: #28a745;"></i>
            <p>Click to upload image</p>
            <p style="font-size: 12px; color: #999;">JPG, PNG, or GIF (Max 5MB)</p>
            <input type="file" id="item_image" name="item_image" accept="image/*" onchange="previewImage(this)">
          </div>
          <div class="image-preview" id="imagePreview">
            <img id="preview" src="" alt="Preview">
          </div>
        </div>
        
        <button type="submit" name="upload_item" class="btn-primary">
          <i class="fas fa-plus"></i> Add Item
        </button>
      </form>
    </div>
    
    <!-- Items Display -->
    <h2>My Items</h2>
    
    <?php if ($itemsResult->num_rows > 0): ?>
      <div class="inventory-grid">
        <?php while ($item = $itemsResult->fetch_assoc()): ?>
          <div class="item-card">
            <?php if (!empty($item['item_image'])): ?>
              <img src="../media/items/<?php echo htmlspecialchars($item['item_image']); ?>" 
                   alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                   class="item-image">
            <?php else: ?>
              <div class="item-image" style="display: flex; align-items: center; justify-content: center; background: #f0f0f0;">
                <i class="fas fa-box" style="font-size: 60px; color: #ccc;"></i>
              </div>
            <?php endif; ?>
            
            <div class="item-content">
              <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
              <p><?php echo !empty($item['item_description']) ? htmlspecialchars($item['item_description']) : '<em>No description</em>'; ?></p>
              
              <div class="item-actions">
                <a href="?delete=<?php echo $item['item_id']; ?>" 
                   class="btn-delete" 
                   onclick="return confirm('Are you sure you want to delete this item?')">
                  <i class="fas fa-trash"></i> Delete
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="no-items">
        <i class="fas fa-box-open"></i>
        <h3>No Items Yet</h3>
        <p>Start adding items to your inventory using the form above.</p>
      </div>
    <?php endif; ?>
  </main>
  
  <script>
    function previewImage(input) {
      const preview = document.getElementById('imagePreview');
      const previewImg = document.getElementById('preview');
      
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          previewImg.src = e.target.result;
          preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
      }
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
$itemsQuery->close();
$conn->close();
?>
