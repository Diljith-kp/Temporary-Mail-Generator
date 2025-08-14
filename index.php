<?php
error_reporting(0);
session_start();

// Simplified functions for API communication
function RandomNumber($length) {
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= mt_rand(0, 9);
    }
    return $str;
}

function rando($length) {
    $characters = "1234567890abcdefghijklmnopqrstuvwxyz";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function makeCurlRequest($url, $headers) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$headers = ["Content-Type:application/x-www-form-urlencoded", "Host: 10minutemail.net", "Connection: Keep-Alive", "Accept-Encoding: gzip", "User-Agent: okhttp/3.12.1"];

if (isset($_GET["action"])) {
    if ($_GET["action"] == "new" || !isset($_SESSION['email'])) {
        $i8 = RandomNumber(8);
        $imei = rando(16);
        $b = RandomNumber(3);
        $l = time();
        $hh = "21b6e" . $imei . $i8 . $b;
        $url = "https://10minutemail.net/address.api.php?new=1&sessionid=$hh&_=$l";
        $one = makeCurlRequest($url, $headers);
        $json = json_decode($one, true);
        if ($json && isset($json['mail_get_mail']) && isset($json['session_id'])) {
            $_SESSION['email'] = $json["mail_get_mail"];
            $_SESSION['sessionId'] = $json["session_id"];
            $_SESSION['expiryTime'] = time() + (10 * 60);
        }
    }
    header("Location: index.php");
    exit();
}

$emails = [];
$emailContent = null;
if (isset($_SESSION['sessionId'])) {
    $hh = $_SESSION['sessionId'];
    if (isset($_GET["mailid"])) {
        $mid = $_GET["mailid"];
        $url = "https://10minutemail.net/mail.api.php?mailid=$mid&sessionid=$hh";
        $one = makeCurlRequest($url, $headers);
        $json = json_decode($one, true);
        if ($json && isset($json["body"][0]["body"])) {
            $emailContent = $json["body"][0]["body"];
            $emailFrom = $json["from"];
            $emailSubject = $json["subject"];
        }
    } else {
        $url = "https://10minutemail.net/address.api.php?sessionid=$hh&_=" . time();
        $one = makeCurlRequest($url, $headers);
        $json = json_decode($one, true);
        if ($json && isset($json["mail_list"])) {
            $emails = $json["mail_list"];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temp Mail Generator</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="<?php echo isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'true' ? 'dark-mode' : ''; ?>">
    <header class="header">
        <div class="header-container">
            <h1 class="header-logo">Temp Mail Generator</h1>
            <div class="header-actions">
                <a href="https://diljith.in" class="connect-btn" target="_blank">Connect with me <i class="fas fa-external-link-alt"></i></a>
                <a href="https://diljith.in" class="connect-icon-mobile" target="_blank">
                    <i class="fas fa-user-circle"></i>
                </a>
                <button id="theme-toggle" class="theme-toggle-btn">
                    <i class="fas fa-sun"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="content-card">
                <?php if (isset($_SESSION['email'])): ?>
                    <p class="status-message">Your Temporary Email Address:</p>
                    <div class="email-display">
                        <input type="text" id="temp-email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
                        <button onclick="copyEmail()" class="btn btn-secondary"><i class="fas fa-copy"></i></button>
                    </div>
                    <p class="countdown-timer" id="timer"></p>
                    <div class="button-group-desktop">
                        <a href="index.php" class="btn btn-primary">Refresh Inbox</a>
                        <a href="index.php?action=new" class="btn btn-secondary">New Mail</a>
                    </div>
                <?php else: ?>
                    <p class="status-message">Generate a new temporary email address to get started.</p>
                    <div class="button-group-desktop">
                        <a href="index.php?action=new" class="btn btn-primary main-action-btn">Generate Mail</a>
                    </div>
                <?php endif; ?>
            </div>
    
            <?php if ($emailContent): ?>
                <div class="content-card">
                    <h2 class="section-heading">Email Details</h2>
                    <p><strong>From:</strong> <?php echo htmlspecialchars($emailFrom); ?></p>
                    <p><strong>Subject:</strong> <?php echo htmlspecialchars($emailSubject); ?></p>
                    <hr class="separator">
                    <div class="email-body"><?php echo nl2br(htmlspecialchars($emailContent)); ?></div>
                    <div class="button-group-desktop">
                        <a href="index.php" class="btn btn-primary">Back to Inbox</a>
                    </div>
                </div>
            <?php elseif (!empty($emails)): ?>
                <div class="content-card">
                    <h2 class="section-heading">Inbox</h2>
                    <div class="inbox-list">
                        <?php 
                        $hasEmails = false;
                        if (!empty($emails)) {
                            foreach ($emails as $mail) {
                                // Check if the email is not the welcome message
                                if ($mail['from'] !== 'no-reply@10minutemail.net' || strpos($mail['subject'], 'Welcome to 10 Minute Mail') === false) {
                                    $hasEmails = true;
                                    echo '<a href="index.php?mailid=' . htmlspecialchars($mail['mail_id']) . '" class="inbox-item">';
                                    echo '<div class="inbox-from">' . htmlspecialchars($mail['from']) . '</div>';
                                    echo '<div class="inbox-subject">' . htmlspecialchars($mail['subject']) . '</div>';
                                    echo '<div class="inbox-time">' . htmlspecialchars($mail['datetime2']) . '</div>';
                                    echo '</a>';
                                }
                            }
                        }
                        if (!$hasEmails) {
                            echo '<p>Your inbox is empty.</p>';
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> All rights reserved. Made with ❤️ by Diljith KP.</p>
        </div>
    </footer>

    <script>
        <?php if (isset($_SESSION['expiryTime'])): ?>
            const expiryTime = <?php echo $_SESSION['expiryTime']; ?>;
            const timerElement = document.getElementById('timer');
            function updateTimer() {
                const now = Math.floor(Date.now() / 1000);
                let timeLeft = expiryTime - now;
                if (timeLeft < 0) {
                    timerElement.textContent = "Your email has expired. Generating new mail...";
                    setTimeout(() => window.location.href = 'index.php?action=new', 2000);
                    return;
                }
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `Expires in: ${minutes}m ${seconds}s`;
                setTimeout(updateTimer, 1000);
            }
            updateTimer();
        <?php endif; ?>

        function copyEmail() {
            const emailInput = document.getElementById("temp-email");
            emailInput.select();
            emailInput.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("Email address copied to clipboard!");
        }

        const themeToggleBtn = document.getElementById('theme-toggle');
        themeToggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            document.cookie = `darkMode=${isDarkMode}; path=/; max-age=${60 * 60 * 24 * 365}`;
            themeToggleBtn.querySelector('i').className = isDarkMode ? 'fas fa-sun' : 'fas fa-moon';
        });

        document.addEventListener('DOMContentLoaded', () => {
            const isDarkMode = document.body.classList.contains('dark-mode');
            themeToggleBtn.querySelector('i').className = isDarkMode ? 'fas fa-sun' : 'fas fa-moon';
        });
    </script>
</body>
</html>