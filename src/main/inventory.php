<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Inventory — GoGreenTogether</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/inventory.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="container">
    <h1>Your Inventory</h1>
    <div class="inventory-grid">
      <?php
      // Database connection
      $conn = new mysqli('localhost', 'root', '', 'rwdd_assignment');
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
      $userId = $_SESSION['user_id'];
      ?>
    </div>
  </main>
</body>
</html>
