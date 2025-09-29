<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Tips — GoGreenTogether</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <nav class="nav">
    <a href="index.php" class="brand">GoGreenTogether</a>
    <button class="hamburger" aria-label="Toggle Menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
    <div class="nav-links">
      <a href="aboutus.php">About</a>
      <a href="event.php">Events</a>
      <a href="marketplace.php">Marketplace</a>
      <a href="tips.php">Tips</a>
    </div>
  </nav>

  <main class="container">
    <h1>Eco Tips</h1>
    <p class="muted">Short tips with a status and optional author (from data dictionary). :contentReference[oaicite:4]{index=4}</p>

    <ul class="tips-list">
      <li>Carpooling saves fuel and cuts emissions.</li>
      <li>Use reusable bags and bottles to reduce single-use plastic.</li>
      <li>Compost food scraps to enrich soil for gardening.</li>
    </ul>

    <h2>Share a tip</h2>
    <form id="tipForm">
      <textarea name="tip" rows="3" placeholder="Share a short eco tip..." required></textarea>
      <button class="btn" type="submit">Submit</button>
    </form>

    <div id="tipFeedback" class="muted" aria-live="polite"></div>
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
</body>
</html>
