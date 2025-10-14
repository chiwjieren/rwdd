<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Tips — GoGreenTogether</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="../css/tips.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main class="container">
    <div class="tips-header">
      <h1>Eco-Friendly Tips & Quiz</h1>
      <p>Learn and test your knowledge about sustainable living</p>
    </div>

    <div class="tips-nav">
      <button class="active" data-section="green-tips">Green Tips</button>
      <button data-section="energy-tips">Energy Saving</button>
      <button data-section="eco-quiz">Take the Quiz</button>
    </div>

    <!-- Share a Tip Form -->
    <div class="share-tip-container">
      <button class="btn btn-primary" id="shareNewTip">Share Your Tip</button>
      
      <div class="share-tip-modal" id="shareTipModal">
        <div class="modal-content">
          <div class="modal-header">
            <h2>Share Your Eco-Friendly Tip</h2>
            <button class="close-modal">&times;</button>
          </div>
          <form id="shareTipForm" class="share-tip-form">
            <div class="form-group">
              <label for="tipTitle">Tip Title</label>
              <input type="text" id="tipTitle" required placeholder="Enter a clear, concise title">
            </div>
            <div class="form-group">
              <label for="tipContent">Your Tip</label>
              <textarea id="tipContent" required placeholder="Share your eco-friendly tip..."></textarea>
            </div>
            <div class="form-group">
              <label for="tipCategory">Category</label>
              <select id="tipCategory" required>
                <option value="">Select a category</option>
                <option value="green">Green Tips</option>
                <option value="energy">Energy Saving</option>
              </select>
            </div>
            <div class="form-footer">
              <button type="button" class="btn btn-secondary" onclick="closeShareTipModal()">Cancel</button>
              <button type="submit" class="btn btn-primary">Share Tip</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Green Tips Section -->
    <section id="green-tips" class="tips-section active">
      <div class="tip-card">
        <h3>Reduce Plastic Waste</h3>
        <p class="tip-content">Use reusable bags, water bottles, and containers. Say no to single-use plastics and opt for sustainable alternatives.</p>
        <div class="tip-meta">
          <span class="tip-author">By: EcoWarrior</span>
          <span class="tip-category">Category: Daily Habits</span>
        </div>
      </div>

      <div class="tip-card">
        <h3>Start Composting</h3>
        <p class="tip-content">Turn your food scraps and yard waste into nutrient-rich soil. It's easy to start with a small bin in your backyard.</p>
        <div class="tip-meta">
          <span class="tip-author">By: GreenThumb</span>
          <span class="tip-category">Category: Gardening</span>
        </div>
      </div>

      <div class="tip-card">
        <h3>Sustainable Shopping</h3>
        <p class="tip-content">Buy local, choose products with minimal packaging, and support eco-friendly brands.</p>
        <div class="tip-meta">
          <span class="tip-author">By: SustainableShopper</span>
          <span class="tip-category">Category: Lifestyle</span>
        </div>
      </div>
    </section>

    <!-- Energy Saving Tips Section -->
    <section id="energy-tips" class="tips-section">
      <div class="tip-card">
        <h3>Smart Temperature Control</h3>
        <p class="tip-content">Set your thermostat to 68°F (20°C) in winter and 78°F (26°C) in summer. Each degree of adjustment can save on energy costs.</p>
        <div class="tip-meta">
          <span class="tip-author">By: EnergyExpert</span>
          <span class="tip-category">Category: Home Energy</span>
        </div>
      </div>

      <div class="tip-card">
        <h3>LED Lighting</h3>
        <p class="tip-content">Replace traditional bulbs with LED lights. They use up to 75% less energy and last 25 times longer.</p>
        <div class="tip-meta">
          <span class="tip-author">By: BrightIdeas</span>
          <span class="tip-category">Category: Lighting</span>
        </div>
      </div>

      <div class="tip-card">
        <h3>Standby Power Management</h3>
        <p class="tip-content">Unplug electronics when not in use or use a power strip to eliminate standby power consumption.</p>
        <div class="tip-meta">
          <span class="tip-author">By: PowerSaver</span>
          <span class="tip-category">Category: Electronics</span>
        </div>
      </div>
    </section>

    <!-- Quiz Section -->
    <section id="eco-quiz" class="tips-section">
      <div class="quiz-container">
        <div class="quiz-card">
          <div class="question">Which of these actions saves the most energy in your home?</div>
          <div class="options">
            <div class="option">Using LED light bulbs</div>
            <div class="option">Proper insulation of walls and roof</div>
            <div class="option">Unplugging unused devices</div>
            <div class="option">Using a smart thermostat</div>
          </div>
        </div>

        <div class="quiz-progress">
          <p>Question 1 of 5</p>
          <div class="progress-bar">
            <div class="progress-fill" style="width: 20%"></div>
          </div>
          <button class="btn btn-primary" onclick="nextQuestion()">Next Question</button>
        </div>
      </div>
    </section>
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
  <script src="../js/tips.js"></script>
</body>
</html>
