<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Marketplace — GoGreenTogether</title>
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
    <h1>Green Marketplace</h1>
    <p>Swap, buy, or list eco-friendly items. Each product has owner, name, description, price and status in the DB model.</p>

    <div class="grid products">
      <article class="product">
        <img src="assets/images/placeholder.png" alt="product image">
        <h3>Rubber Duck (recycled)</h3>
        <p class="price">RM 10</p>
        <p class="small">Owner: user123 · Status: Available</p>
        <button class="btn">Contact Owner</button>
      </article>

      <article class="product">
        <img src="assets/images/placeholder.png" alt="product image">
        <h3>Used Garden Tools</h3>
        <p class="price">RM 40</p>
        <p class="small">Owner: green_shop · Status: Available</p>
        <button class="btn">Contact Owner</button>
      </article>
    </div>
  </main>

  <footer class="footer">
    <small><a href="index.php">← Back</a></small>
  </footer>

  <script src="assets/js/main.js"></script>
</body>
</html>
