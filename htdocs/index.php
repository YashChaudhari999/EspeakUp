<?php
session_start();

// Check if user is already logged in, redirect to another page if true
if (isset($_SESSION['email'])) {
  header("Location: ./gitup/pages/card.php");
  exit(); // Stop executing further code
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <title>Home Page</title>
  <link rel="stylesheet" href="./gitup/css/style.css">

  <link rel="shortcut icon" href="./gitup/assets/favicon.png" type="image/x-icon">
</head>

<body>

  <div class="logo">
    <img src="./gitup/assets/logo.png" alt="">
  </div>
  <header>
    <nav>

      <a href="./gitup/pages/contact.php">Contact</a>
      <a href="./gitup/pages/about.html">About</a>
      <button>
        <a href="./gitup/assets/espeakup.apk" download>
          <!-- <a href="#" > -->
          <i class="fa-brands fa-android"></i>Get App
        </a>
      </button>
    </nav>
  </header>

  <!-- carousel -->
  <div class="carousel">
    <!-- list item -->
    <div class="list">
      <div class="item">
        <img src="./gitup/assets/russia.jpg">
        <div class="content">
          <div class="author">ESPEAK UP</div>
          <div class="title">The Motherland</div>
          <div class="topic">RUSSIA</div>
          <div class="des">
            🇷🇺 Russian, the language of the steppes and snow-capped landscapes. With its Cyrillic script dancing
            across the page, Russian is a tapestry of rich literary tradition, from the epic novels of Tolstoy to the
            poignant poetry of Pushkin. It's a language that whispers tales of czars and revolutionaries, carrying the
            echoes of a tumultuous history. With every word, Russian invites you into a world of depth and emotion,
            where every syllable is imbued with passion and resilience.
          </div>
          <div class="buttons">
            <button><a href="./gitup/pages/card.php">EXPLORE NOW</a></button>
            <button><a href="./gitup/pages/login.php">SIGN IN</a></button>
          </div>
        </div>
      </div>
      <div class="item">
        <img src="./gitup/assets/spain.jpg">
        <div class="content">
          <div class="author">ESPEAK UP</div>
          <div class="title">Land of Cervantes</div>
          <div class="topic">SPAIN</div>
          <div class="des">
            🇪🇸 Spanish, the language of passion and rhythm. From the sun-drenched streets of Madrid to the vibrant
            markets of Mexico City, Spanish fills the air with its melodic cadence. It's a language of poets and
            conquerors, weaving tales of love and adventure across the globe. Spanish is the heartbeat of fiestas and
            flamenco, where every word is a celebration of life and culture. With its warmth and vitality, Spanish
            embraces you like an old friend, inviting you to dance to the beat of its soulful melodies.
          </div>
          <div class="buttons">
            <button><a href="./gitup/pages/card.php">EXPLORE NOW</a></button>
            <button><a href="./gitup/pages/login.php">SIGN IN</a></button>
          </div>
        </div>
      </div>
      <div class="item">
        <img src="./gitup/assets/japan.jpg">
        <div class="content">
          <div class="author">ESPEAK UP</div>
          <div class="title">Land of Anime</div>
          <div class="topic">JAPAN</div>
          <div class="des">
            🇯🇵 Japanese, the language of cherry blossoms and ancient traditions. With its intricate writing system
            blending kanji, hiragana, and katakana, Japanese paints a picture of reverence for nature and respect for
            the past. From the serene gardens of Kyoto to the bustling streets of Tokyo, Japanese whispers secrets of
            honor and harmony. It's a language of zen and zenkai, where every character tells a story of balance and
            enlightenment. Japanese is a journey into the heart of tradition, where the past and present dance in
            perfect harmony.
          </div>
          <div class="buttons">
            <button><a href="./gitup/pages/card.php">EXPLORE NOW</a></button>
            <button><a href="./gitup/pages/login.php">SIGN IN</a></button>
          </div>
        </div>
      </div>
      <div class="item">
        <img src="./gitup/assets/france.jpg">
        <div class="content">
          <div class="author">ESPEAK UP</div>
          <div class="title">Land Of The Lights</div>
          <div class="topic">FRANCE</div>
          <div class="des">
            🇫🇷 French, the language of romance and refinement. Like a fine wine, French rolls off the tongue with
            elegance and sophistication. From the cobblestone alleys of Paris to the sun-kissed vineyards of Bordeaux,
            French is a symphony of culture and class, inspiring poets and philosophers for centuries. It's the language
            of amour and art de vivre, where every phrase is a brushstroke on the canvas of life. French is a passport
            to the world of haute couture and haute cuisine, where every word is a nod to elegance and taste.
          </div>
          <div class="buttons">
            <button><a href="./gitup/pages/card.php">EXPLORE NOW</a></button>
            <button><a href="./gitup/pages/login.php">SIGN IN</a></button>
          </div>
        </div>
      </div>
      <div class="item">
        <img src="./gitup/assets/germany.jpg">
        <div class="content">
          <div class="author">ESPEAK UP</div>
          <div class="title">The Land of Ideas</div>
          <div class="topic">GERMANY</div>
          <div class="des">
            🇩🇪 German, the language of precision and innovation. With its complex grammar and robust vocabulary,
            German is a symmetrical masterpiece of logic and order. From the fairy-tale forests of the Black Forest to
            the cutting-edge technology of Berlin, German is a language of intellect and ingenuity, shaping the world
            with its words. It's the language of philosophers and pioneers, where every sentence is a quest for truth
            and discovery. German is a symphony of efficiency and excellence, where every umlaut and eszett is a
            testament to precision and ingenuity.
          </div>
          <div class="buttons">
            <button><a href="./gitup/pages/login.php">EXPLORE NOW</a></button>
            <button><a href="./gitup/pages/login.php">SIGN IN</a></button>
          </div>
        </div>
      </div>
    </div>
    <!-- list thumnail -->
    <div class="thumbnail">
      <div class="item">
        <img src="./gitup/assets/russia.jpg">
        <div class="content">
          <div class="title">
            RUSSIAN
          </div>
          <!-- <div class="description">
                        Description
                    </div> -->
        </div>
      </div>
      <div class="item">
        <img src="./gitup/assets/spain.jpg">
        <div class="content">
          <div class="title">
            SPANISH
          </div>
          <!-- <div class="description">
                        Description
                    </div> -->
        </div>
      </div>
      <div class="item">
        <img src="./gitup/assets/japan.jpg">
        <div class="content">
          <div class="title">
            JAPANESE
          </div>
          <!-- <div class="description">
                        Description
                    </div> -->
        </div>
      </div>
      <div class="item">
        <img src="./gitup/assets/france.jpg">
        <div class="content">
          <div class="title">
            FRENCH
          </div>
          <!-- <div class="description">
                        Description
                    </div> -->
        </div>
      </div>
      <div class="item">
        <img src="./gitup/assets/germany.jpg">
        <div class="content">
          <div class="title">
            GERMAN
          </div>
          <!-- <div class="description">
                        Description
                    </div> -->
        </div>
      </div>
    </div>
    <!-- next prev -->

    <div class="arrows">
      <button id="prev">
        < </button>
          <button id="next">></button>
    </div>
    <!-- time running -->
    <div class="time"></div>
  </div>


  <script src="./gitup/script/app.js"></script>
</body>

</html>