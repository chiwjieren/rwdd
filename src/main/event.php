<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Events — GoGreenTogether</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <nav class="nav">
    <a href="index.php" class="brand">GoGreenTogether</a>
    <div class="nav-links">
      <a href="aboutus.php">About</a>
      <a href="event.php">Events</a>
      <a href="marketplace.php">Marketplace</a>
      <a href="tips.php">Tips</a>
    </div>
  </nav>

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
      <li class="event-card">
        <h3 class="event-title">Go Green 2025</h3>
        <div class="meta">📅 2025-01-01 08:00 — 2025-01-01 09:00  ·  📍 Community Hall</div>
        <p class="desc">This event aims to educate the community about protecting the environment.</p>
        <div class="event-actions">
          <button class="btn" onclick="addToCalendar('Go Green 2025', 'This event aims to educate the community about protecting the environment.', '2025-01-01T08:00:00', '2025-01-01T09:00:00', 'Community Hall')">Add to Calendar</button>
          <button class="btn btn-secondary">Register</button>
        </div>
      </li>

      <li class="event-card">
        <h3 class="event-title">Recycling Workshop</h3>
        <div class="meta">📅 2025-02-15 10:00 — 2025-02-15 12:00  ·  📍 City Library</div>
        <p class="desc">Hand-on session on sorting and upcycling household waste.</p>
        <div class="event-actions">
          <button class="btn" onclick="addToCalendar('Recycling Workshop', 'Hand-on session on sorting and upcycling household waste.', '2025-02-15T10:00:00', '2025-02-15T12:00:00', 'City Library')">Add to Calendar</button>
          <button class="btn btn-secondary">Register</button>
        </div>
      </li>
    </ul>
  </main>

  <footer class="footer">
    <small><a href="index.php">← Back</a></small>
  </footer>

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
