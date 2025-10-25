<?php
include 'db_connection.php';

$newsletterMessage = '';
$newsletterError = '';

// Handle newsletter subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsletter_email'])) {
    $email = trim($_POST['newsletter_email']);
    
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            // Check if email already exists
            $checkStmt = $conn->prepare("SELECT subscriber_id FROM NEWSLETTER_SUBSCRIBER WHERE subscriber_email = ?");
            
            if (!$checkStmt) {
                $newsletterError = "Database error: " . $conn->error . " - Please check if NEWSLETTER_SUBSCRIBER table exists.";
            } else {
                $checkStmt->bind_param("s", $email);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                
                if ($result->num_rows > 0) {
                    $newsletterError = "This email is already subscribed!";
                } else {
                    // Insert new subscriber
                    $insertStmt = $conn->prepare("INSERT INTO NEWSLETTER_SUBSCRIBER (subscriber_email) VALUES (?)");
                    
                    if (!$insertStmt) {
                        $newsletterError = "Database error: " . $conn->error;
                    } else {
                        $insertStmt->bind_param("s", $email);
                        
                        if ($insertStmt->execute()) {
                            $newsletterMessage = "Successfully subscribed to our newsletter!";
                        } else {
                            $newsletterError = "Error subscribing: " . $insertStmt->error;
                        }
                        $insertStmt->close();
                    }
                }
                $checkStmt->close();
            }
        } catch (Exception $e) {
            $newsletterError = "Error: " . $e->getMessage();
        }
    } else {
        $newsletterError = "Please enter a valid email address.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>GoGreenTogether — Home</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    <section class= "partners-section">
      <h2>Meet Our Partners</h2>
        <div class="marquee-content">
          <div class="partner-card">
            <img src="../media/partner1.jpeg" alt="Partner Company 1">
            <h4>Jadon Wong</h4>
            <p>Co-Founder, EcoGrow</p>
          </div>
          <div class="partner-card">
            <img src="../media/partner2.jpeg" alt="Partner Company 2">
            <h4>Amelia Tan</h4>
            <p>Project Director, GreenTech Innovations</p>
          </div>
          <div class="partner-card">
            <img src="../media/partner3.jpeg" alt="Partner Company 3">
            <h4>Angela Law</h4>
            <p>Operations Manager, RecycleHub</p>
          </div>
          <div class="partner-card">
            <img src="../media/partner4.jpeg" alt="Partner Company 4">
            <h4>Aaron Lee</h4>
            <p>CEO, EcoCycle Solutions</p>
          </div>
          <div class="partner-card">
            <img src="../media/partner5.jpeg" alt="Partner Company 5">
            <h4>Adlen Chan</h4>
            <p>Head of Partnership, GoGreen Inc.</p>
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
    <section class="newsletter-section" id="newsletter-section">
      <div class="container">
        <div class="newsletter-content">
          <h2>Stay Updated</h2>
          <p>Subscribe to receive project updates, news, and insights</p>
          
          <?php if ($newsletterMessage): ?>
            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #c3e6cb;">
              ✓ <?php echo $newsletterMessage; ?>
            </div>
          <?php endif; ?>
          
          <?php if ($newsletterError): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #f5c6cb;">
              ✗ <?php echo $newsletterError; ?>
            </div>
          <?php endif; ?>
          
          <form class="newsletter-form" action="#newsletter-section" method="POST">
            <div class="form-group">
              <input type="email" name="newsletter_email" placeholder="Enter your email address" required>
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
          <li><a href="#newsletter-section">Newsletter</a></li>
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

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="../js/main.js"></script>
</body>
</html>
