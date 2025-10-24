<?php
include 'db_connection.php';

// Fetch all events from database (for demo purposes, show all events)
// In production, you might want: WHERE event_date >= CURDATE()
$events = $conn->query("SELECT * FROM EVENT ORDER BY event_date DESC, event_time DESC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Events — GoGreenTogether</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/navigation.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main class="container">
    <h1>Upcoming Events</h1>
    
    <!-- Google Calendar Embed -->
    <div class="calendar-container">
      <iframe 
        src="https://calendar.google.com/calendar/embed?height=600&wkst=1&ctz=Asia%2FKuala_Lumpur&showPrint=0&src=Y2Y0NWM1OThiM2U0MjJmZjE2OTllMGI4ZTc2MDQzOTY2NDRjMWQ0N2E3NzVlMWExNzAwNGUwZTgwYjEyMjM1NkBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&color=%230b8043" 
        style="border:solid 1px #777" 
        width="100%" 
        height="600" 
        frameborder="0" 
        scrolling="no">
      </iframe>
    </div>

    <h2>Featured Events</h2>
    <ul class="event-list">
      <?php if ($events && $events->num_rows > 0): ?>
        <?php while ($event = $events->fetch_assoc()): ?>
          <li class="event-card">
            <h3 class="event-title"><?php echo htmlspecialchars($event['event_title']); ?></h3>
            <div class="meta">
              📅 <?php echo date('Y-m-d H:i', strtotime($event['event_date'] . ' ' . $event['event_time'])); ?>  
              ·  📍 <?php echo htmlspecialchars($event['event_location']); ?>
            </div>
            <p class="desc"><?php echo htmlspecialchars($event['event_description']); ?></p>
            <div class="event-actions">
              <button class="btn" onclick="addToCalendar(
                '<?php echo addslashes($event['event_title']); ?>', 
                '<?php echo addslashes($event['event_description']); ?>', 
                '<?php echo date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_time'])); ?>', 
                '<?php echo date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_time']) + 3600); ?>', 
                '<?php echo addslashes($event['event_location']); ?>')">
                Add to Calendar
              </button>
            </div>
          </li>
        <?php endwhile; ?>
      <?php else: ?>
        <li class="event-card">
          <h3 class="event-title">No Upcoming Events</h3>
          <p class="desc">Check back soon for new eco-friendly events!</p>
        </li>
      <?php endif; ?>
    </ul>
    </ul>
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

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="../js/main.js"></script>
  <script>
    function addToCalendar(title, description, startTime, endTime, location) {
      const baseUrl = 'https://calendar.google.com/calendar/render';
      const event = {
        action: 'TEMPLATE',
        text: title,
        details: description,
        location: location,
        dates: startTime.replace(/[-:]/g, '') + '/' + endTime.replace(/[-:]/g, '')
      };
      
      const params = new URLSearchParams(event);
      window.open(baseUrl + '?' + params.toString(), '_blank');
    }
  </script>
</body>
</html>
