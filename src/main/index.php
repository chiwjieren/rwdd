<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>GoGreenTogether — Home</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <!-- Hero Section with Carousel -->
  <div class="hero-carousel">
    <div class="carousel-container">
      <div class="carousel-slide active">
        <img src="../media/communitygarden.jpeg" alt="Community garden project">
        <div class="carousel-content">
          <h2>"Building a Greener Future, Together"</h2>
          <p>Join our community in making sustainable living accessible to everyone</p>
        </div>
      </div>
      <div class="carousel-slide">
        <img src="../media/recyclingworkshop.jpeg" alt="Recycling workshop">
        <div class="carousel-content">
          <h2>"Small Actions, Big Impact"</h2>
          <p>Learn how your daily choices can create positive environmental change</p>
        </div>
      </div>
      <div class="carousel-slide">
        <img src="../media/Communitycleanup.jpeg" alt="Community cleanup">
        <div class="carousel-content">
          <h2>"Together for a Cleaner Tomorrow"</h2>
          <p>Participate in our community events and make a difference</p>
        </div>
      </div>
      <button class="carousel-btn prev" aria-label="Previous slide">❮</button>
      <button class="carousel-btn next" aria-label="Next slide">❯</button>
      <div class="carousel-dots"></div>
    </div>
  </div>

  <main>
    <!-- Partner Companies Marquee -->
    <section class="partners-section">
      <h2>Our Partners</h2>
      <div class="partner-marquee">
        <div class="marquee-content">
          <img src="../media/partner1.png" alt="Partner Company 1">
          <img src="../media/partner2.png" alt="Partner Company 2">
          <img src="../media/partner3.png" alt="Partner Company 3">
          <img src="../media/partner4.png" alt="Partner Company 4">
          <img src="../media/partner5.png" alt="Partner Company 5">
        </div>
      </div>
    </section>

    <!-- Community Highlights -->
    <section class="community-section container">
      <h2>Our Flourishing Community</h2>
      <div class="community-grid">
        <div class="community-card">
          <img src="../media/communityevent.jpeg" alt="Community Events">
          <h3>Events</h3>
          <p>Join our workshops, cleanups, and eco-friendly activities</p>
        </div>
        <div class="community-card">
          <img src="../media/communityproject.jpeg" alt="Community Projects">
          <h3>Projects</h3>
          <p>Participate in ongoing community sustainability projects</p>
        </div>
        <div class="community-card">
          <img src="../media/communityimpact.jpeg" alt="Community Impact">
          <h3>Impact</h3>
          <p>See the difference we're making together</p>
        </div>
      </div>
      <div class="cta-container">
        <a href="signup.php" class="btn btn-primary">Get Started</a>
        <p class="cta-subtext">Join our community and make a difference today!</p>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
      <div class="container">
        <div class="newsletter-content">
          <h2>Stay Updated</h2>
          <p>Subscribe to receive project updates, news, and insights</p>
          <form class="newsletter-form" action="#" method="POST">
            <div class="form-group">
              <input type="email" name="email" placeholder="Enter your email address" required>
              <button type="submit" class="btn">Subscribe</button>
            </div>
            <p class="form-notice">By subscribing, you agree to receive our newsletter and updates.</p>
          </form>
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
</body>
</html>
