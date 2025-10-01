<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Marketplace — GoGreenTogether</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/marketplace.css">
</head>
<body>
  <?php 
    include 'header.php';
    if (!isLoggedIn()) {
      header("Location: login.php");
      exit();
    }
  ?>

  <main class="container">
    <div class="marketplace-header">
      <h1>Green Marketplace</h1>
      <p>Swap eco-friendly items with other community members.</p>
      <button class="btn btn-primary" id="addItemBtn">+ Add Your Item</button>
    </div>

    <div class="marketplace-filters">
      <div class="search-bar">
        <input type="text" placeholder="Search items...">
        <button class="btn-icon"><i class="fas fa-search"></i></button>
      </div>
      <div class="filter-options">
        <select>
          <option value="">All Categories</option>
          <option value="garden">Garden</option>
          <option value="household">Household</option>
          <option value="electronics">Electronics</option>
          <option value="books">Books</option>
        </select>
      </div>
    </div>

    <div class="grid products">
      <article class="product" data-id="1">
        <div class="product-image">
          <img src="../media/product1.jpg" alt="Eco-friendly Garden Tools">
          <span class="status-badge available">Available</span>
        </div>
        <div class="product-info">
          <h3>Eco-friendly Garden Tools</h3>
          <p class="description">Set of gardening tools made from recycled materials.</p>
          <p class="owner">Owner: GreenThumb</p>
          <button class="btn btn-swap" onclick="openSwapModal(1)">Swap Item</button>
        </div>
      </article>

      <article class="product" data-id="2">
        <div class="product-image">
          <img src="../media/product2.jpg" alt="Reusable Food Containers">
          <span class="status-badge available">Available</span>
        </div>
        <div class="product-info">
          <h3>Reusable Food Containers</h3>
          <p class="description">Set of 5 glass containers with bamboo lids.</p>
          <p class="owner">Owner: EcoKitchen</p>
          <button class="btn btn-swap" onclick="openSwapModal(2)">Swap Item</button>
        </div>
      </article>
    </div>

    <!-- Swap Modal -->
    <div id="swapModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Swap Items</h2>
          <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="swap-container">
            <div class="item-to-receive">
              <h3>Item You'll Receive</h3>
              <div class="selected-item">
                <!-- Selected item details will be populated here -->
              </div>
            </div>
            <div class="items-to-offer">
              <h3>Your Items to Offer</h3>
              <div class="your-items grid">
                <!-- User's items will be populated here -->
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeSwapModal()">Cancel</button>
          <button class="btn btn-primary" onclick="proposeSwap()">Propose Swap</button>
        </div>
      </div>
    </div>

    <!-- Swap Requests Modal -->
    <div id="swapRequestsModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Swap Requests</h2>
          <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="requests-container">
            <!-- Incoming Requests Tab -->
            <div class="requests-section">
              <h3>Incoming Requests</h3>
              <div class="request-card">
                <div class="request-items">
                  <div class="item-exchange">
                    <div class="requested-item">
                      <img src="../media/product1.jpg" alt="Your Item">
                      <div class="item-details">
                        <h4>Your Item: Eco-friendly Garden Tools</h4>
                      </div>
                    </div>
                    <i class="fas fa-exchange-alt exchange-icon"></i>
                    <div class="offered-item">
                      <img src="../media/product2.jpg" alt="Their Item">
                      <div class="item-details">
                        <h4>Their Item: Reusable Food Containers</h4>
                      </div>
                    </div>
                  </div>
                  <div class="request-info">
                    <p>From: GreenUser123</p>
                    <p>Requested: 2 hours ago</p>
                  </div>
                  <div class="request-actions">
                    <button class="btn btn-approve" onclick="handleSwapRequest('approve', 1)">
                      <i class="fas fa-check"></i> Approve
                    </button>
                    <button class="btn btn-reject" onclick="handleSwapRequest('reject', 1)">
                      <i class="fas fa-times"></i> Reject
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Outgoing Requests Tab -->
            <div class="requests-section">
              <h3>Outgoing Requests</h3>
              <div class="request-card pending">
                <div class="request-items">
                  <div class="item-exchange">
                    <div class="offered-item">
                      <img src="../media/product3.jpg" alt="Your Item">
                      <div class="item-details">
                        <h4>Your Item: Bamboo Utensils</h4>
                      </div>
                    </div>
                    <i class="fas fa-exchange-alt exchange-icon"></i>
                    <div class="requested-item">
                      <img src="../media/product4.jpg" alt="Their Item">
                      <div class="item-details">
                        <h4>Their Item: Solar Lamp</h4>
                      </div>
                    </div>
                  </div>
                  <div class="request-info">
                    <p>To: EcoTrader</p>
                    <p>Status: Pending</p>
                  </div>
                  <div class="request-actions">
                    <button class="btn btn-cancel" onclick="handleSwapRequest('cancel', 2)">
                      Cancel Request
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Item Modal -->
    <div id="addItemModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Add New Item</h2>
          <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
          <form id="addItemForm" class="add-item-form">
            <div class="form-group">
              <label for="itemName">Item Name</label>
              <input type="text" id="itemName" required>
            </div>
            <div class="form-group">
              <label for="itemDescription">Description</label>
              <textarea id="itemDescription" required></textarea>
            </div>
            <div class="form-group">
              <label for="itemCategory">Category</label>
              <select id="itemCategory" required>
                <option value="garden">Garden</option>
                <option value="household">Household</option>
                <option value="electronics">Electronics</option>
                <option value="books">Books</option>
              </select>
            </div>
            <div class="form-group">
              <label for="itemImage">Upload Image</label>
              <input type="file" id="itemImage" accept="image/*" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeAddItemModal()">Cancel</button>
          <button class="btn btn-primary" onclick="submitNewItem()">Add Item</button>
        </div>
      </div>
    </div>
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

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="../js/main.js"></script>
  <script src="../js/marketplace.js"></script>
</body>
</html>
