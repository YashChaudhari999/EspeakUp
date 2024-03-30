<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Languages</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../css/card.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">
</head>

<body>

  <div class="hidden">
    <h1>hi</h1>
  </div>


  <div id="nav">
    <div id="nleft">
      <a href="./card.php"><img src="../assets/logo.png" alt=""></a>
    </div>
    <div id="nright">
      <a href="./dash.php" class="btn btn-outline-light" id="sign-out-btn">Dashboard</a>
      </a>
    </div>
  </div>

  <main>
    <div class="card-container">
      <!-- Card 1 -->
      <div class="card card1">
        <div class="info">
          <h1 class="title">French</h1>
          <p class="description">
            "Bonjour! Explore the beauty of the French language, known for its elegance and rich cultural heritage. Dive
            into the world of art, literature, and cuisine as you embark on your French language journey."
            <br>
            <br>
            <a
              href="<?php echo isset($_SESSION['email']) ? 'https://mediafiles.botpress.cloud/d645c9e5-dd3d-4ad6-865b-33e77521f1a8/webchat/bot.html' : 'login.php'; ?>">Start
              Learning</a>
          </p>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="card card2">
        <div class="info">
          <h1 class="title">German</h1>
          <p class="description">
            Welcome to Germany, where history and modernity collide,
            We greet you with open arms, so come and enjoy the ride.
            From the bustling streets of Berlin to the serene Bavarian Alps,
            We hope your time here is filled with unforgettable moments and hallo!
            <br>
            <br>
            <a
              href="<?php echo isset($_SESSION['email']) ? 'https://mediafiles.botpress.cloud/b488be33-44e4-4ea8-8bec-127dbba4cfc1/webchat/bot.html' : 'login.php'; ?>">Start
              Learning</a>
          </p>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="card card3">
        <div class="info">
          <h1 class="title">Japanese</h1>
          <p class="description">
            "こんにちは! Step into Japan's unique blend of tradition and modernity. From graceful tea ceremonies to the
            cutting-edge vibe of Tokyo, explore a language that mirrors the nation's rich cultural tapestry"
            <br>
            <br>

            <a
              href="<?php echo isset($_SESSION['email']) ? 'https://mediafiles.botpress.cloud/9e2238e0-af40-4f50-877a-727759bbfa78/webchat/bot.html' : 'login.php'; ?>">Start
              Learning</a>
          </p>

        </div>
      </div>

      <!-- Card 4 -->
      <div class="card card4">
        <div class="info">
          <h1 class="title">Russian</h1>
          <p class="description">
            "Привет! Uncover the mysteries of the Cyrillic alphabet. Immerse in Russian's rich history, literature, and
            traditions. Your linguistic journey awaits, promising a fascinating exploration of a diverse culture."
            <br>
            <br>
            <a
              href="<?php echo isset($_SESSION['email']) ? 'https://mediafiles.botpress.cloud/c65d922a-18fd-4b29-b783-4df9ab1eccf6/webchat/bot.html' : 'login.php'; ?>">Start
              Learning</a>
          </p>
        </div>
      </div>

      <!-- Card 5 -->
      <div class="card card5">
        <div class="info">
          <h1 class="title">Spanish</h1>
          <p class="description">
            "Hola! Embark on a colorful journey through Spanish. From passionate flamenco rhythms to the savory delights
            of paella, experience the warmth and energy woven into this captivating language and culture."
            <br>
            <br>
            <a href="<?php echo isset($_SESSION['email']) ? 'https://mediafiles.botpress.cloud/69f8ea84-c571-4f2b-9bde-0148a4a615ef/webchat/bot.html' : 'login.php'; ?>">Start Learning</a>
          </p>
        </div>
      </div>
    </div>
  </main>

  <footer class="footer">
    <div class="container-fo">
      <div class="row-fo">
        <div class="footer-col">
          <h4>company</h4>
          <ul>
            <li><a href="./about.html">about us</a></li>
            <li><a href="./contact.php">Contact US</a></li>
            <li><a href="./terms.html">Terms and Conditions</a></li>


          </ul>
        </div>
        <div class="footer-col">
          <h4>get help</h4>
          <ul>
            <li><a href="#">FAQ</a></li>

          </ul>
        </div>

        <div class="footer-col">
          <h4>follow us</h4>
          <div class="social-links">
            <a href="#"><i class="fa-brands fa-facebook"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>

          </div>
        </div>
      </div>
    </div>
  </footer>

</body>

</html>