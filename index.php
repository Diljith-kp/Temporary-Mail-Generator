<?php include 'core.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="Free Temporary Mail Generator"/>
  <title>TemporaryMail - Get a Disposable Email Now</title>
  <link rel="icon" href="https://i.ibb.co/QYDmh44/image-search-1664006558163.png" type="image/png">
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
<header class="header">
  <div class="header-content container">
    <div class="logo">Temporary Mail</div>
    <nav class="nav">
      <div class="hamburger-menu" onclick="toggleMenu()">
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
    <line x1="3" y1="6" x2="21" y2="6"/>
    <line x1="3" y1="12" x2="21" y2="12"/>
    <line x1="3" y1="18" x2="21" y2="18"/>
</svg>
      </div>
      <ul class="nav-links" id="navLinks">
        <li><a href="#">Home</a></li>
        <li><a href="service.html">Services</a></li>
        <li><a href="about.html">About Us</a></li>
        <li><a href="privacy.html">Terms & Privacy</a></li>
      </ul>
    </nav>
    <a href="https://diljith.in/" class="btn btn-header">Let's Connect</a>
  </div>
</header>

  <main class="main-content">
    <div class="container">
      
      <div class="generator-section">
        <div class="generator-content">
          <div class="text-content">
            <h1 class="main-heading">📬 Temporary Mail</h1>
            <p class="sub-heading">Your disposable, secure, and private email address.</p>

            <?php if (isset($e) && !isset($_REQUEST["mid"]) && !isset($_REQUEST["hh"])) : ?>
              <div class="generator-active">
                <p class="status-message">Mail ID Generated Successfully</p>
                <input type="text" id="myInput" readonly value="<?= htmlspecialchars($e) ?>">
                <p id="countdown" class="countdown-timer">Expires in: 10:00</p>
                <div class="button-group">
                  <button onclick="copyEmail()" id="copy-btn" class="btn btn-primary">Copy Mail ID</button>
                  <button onclick="viewInbox()" class="btn btn-secondary">View Inbox</button>
                  <form method="POST" action="" class="form-group">
                    <button type="submit" class="btn btn-danger">🔁 New Mail</button>
                  </form>
                </div>
              </div>
            <?php else: ?>
              <div class="generator-inactive">
                <form method="POST" action="">
                  <button type="submit" name="generate_new" class="btn btn-primary main-action-btn">Generate a Temporary Email</button>
                </form>
              </div>
            <?php endif; ?>
          </div>
          <div class="illustration-content">
            <img src="https://res.cloudinary.com/dptj37ebu/image/upload//f_auto/v1754512426/temp-removebg-preview_ww2dxm.png" alt="A girl holding a message" class="illustration">
          </div>
        </div>
      </div>

      <div id="inbox-container" class="inbox-viewer">
        <?php if(isset($_REQUEST["mid"])): ?>
          <div class='email-details'>
            <h2 class="section-heading">Email Details</h2>
            <p><strong>From:</strong> <?= htmlspecialchars($id) ?></p>
            <p><strong>Subject:</strong> <?= htmlspecialchars($sub) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($time) ?></p>
            <hr class="separator">
            <p class="email-body"><?= htmlspecialchars($e) ?></p>
          </div>
        <?php elseif(isset($_REQUEST["hh"])): ?>
          <div class="inbox">
            <h2 class="section-heading">Inbox</h2>
            <?php if ($z == 1): ?>
              <div class='inbox-item no-mails'>
                No Mails Received
              </div>
            <?php else: ?>
              <?php
              $a = 0;
              while($z > $a) {
                $id1 = $c[$a]['from'];
                $sub1 = $c[$a]['subject'];
                $time1 = $c[$a]['datetime2'];
                $mid = $c[$a]['mail_id'];
              ?>
              <div class="inbox-item">
                <p class="truncate"><strong>From:</strong> <?= htmlspecialchars($id1) ?></p>
                <p class="truncate"><strong>Subject:</strong> <?= htmlspecialchars($sub1) ?></p>
                <p class="inbox-item-time"><?= htmlspecialchars($time1) ?></p>
                <button onclick="viewEmail('<?= htmlspecialchars($mid) ?>')" class="btn btn-primary">
                    View Email
                </button>
              </div>
              <?php $a++; $z--; } ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <footer class="footer">
    <div class="container">
<div class="footer-links hide-on-mobile">
  <a href="privacy.html">Terms & Privacy</a>
  <a href="service.html">Services</a>
  <a href="https://diljith.in/">Let's Connect</a>
</div>
      <p class="footer-copy">&copy; 2025 Diljith. All rights reserved.</p>
    </div>
  </footer>

  <script>
    // Toggle mobile navigation menu
    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }

    // Copy email to clipboard and show toast notification
    function copyEmail() {
      const copyText = document.getElementById("myInput");
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      document.execCommand("copy");

      const toast = document.getElementById("toast");
      toast.classList.remove("hidden");
      setTimeout(() => {
          toast.classList.add("hidden");
      }, 2000);
    }
    
    // Countdown timer
    let minutes = 10;
    let seconds = 0;
    const countdown = document.getElementById('countdown');
    const interval = setInterval(() => {
        if (!countdown) return;
        if (seconds === 0) {
            if (minutes === 0) {
                countdown.innerText = "⏳ Mail expired.";
                clearInterval(interval);
                return;
            }
            minutes--;
            seconds = 59;
        } else {
            seconds--;
        }
        countdown.innerText = `Expires in: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }, 1000);

    // Fetch and display inbox content without a full page reload
    function viewInbox() {
      const hh = "<?= htmlspecialchars($hh) ?>";
      fetch(`?hh=${encodeURIComponent(hh)}`)
          .then(response => response.text())
          .then(html => {
              const inbox = document.createElement("html");
              inbox.innerHTML = html;
              const updated = inbox.querySelector("#inbox-container");
              if (updated) {
                  document.getElementById("inbox-container").innerHTML = updated.innerHTML;
              }
          });
    }

    // Fetch and display a specific email without a full page reload
    function viewEmail(mid) {
      const hh = "<?= htmlspecialchars($hh) ?>";
      fetch(`?hh=${encodeURIComponent(hh)}&mid=${encodeURIComponent(mid)}`)
          .then(response => response.text())
          .then(html => {
              const inbox = document.createElement("html");
              inbox.innerHTML = html;
              const updated = inbox.querySelector("#inbox-container");
              if (updated) {
                  document.getElementById("inbox-container").innerHTML = updated.innerHTML;
              }
          });
    }
    
// Toggle mobile menu
  function toggleMenu() {
    const nav = document.querySelector('.nav');
    nav.classList.toggle('active');
  }
    // Auto-refresh inbox
    setInterval(() => {
      const hh = "<?= htmlspecialchars($hh) ?>";
      if (!hh) return;

      fetch(`?hh=${encodeURIComponent(hh)}`)
          .then(response => response.text())
          .then(html => {
              const inbox = document.createElement("html");
              inbox.innerHTML = html;
              const updated = inbox.querySelector("#inbox-container");
              if (updated) {
                  document.getElementById("inbox-container").innerHTML = updated.innerHTML;
              }
          });
    }, 15000);
  </script>
</body>
</html>