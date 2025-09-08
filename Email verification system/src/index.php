<?php
require_once 'functions.php';
session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Subscription Request
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $file = __DIR__ . '/registered_emails.txt';
            $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

            if (in_array($email, $emails)) {
                $message = "⚠️ Email already registered.";
            } else {
                $_SESSION['register_email'] = $email;
                $_SESSION['register_code'] = generateVerificationCode();
                sendVerificationEmail($email, $_SESSION['register_code'], 'subscribe');
                $message = "✅ Verification code sent to $email.";
            }
        } else {
            $message = "❌ Invalid email address.";
        }
    }

    // Subscription Verification
    if (isset($_POST['verification_code'])) {
        if ($_POST['verification_code'] === ($_SESSION['register_code'] ?? '')) {
            registerEmail($_SESSION['register_email']);
            $message = "🎉 Registration complete.";
            unset($_SESSION['register_email'], $_SESSION['register_code']);
        } else {
            $message = "❌ Incorrect verification code.";
        }
    }

    // Unsubscribe Request
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $file = __DIR__ . '/registered_emails.txt';
            $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

            if (!in_array($email, $emails)) {
                $message = "⚠️ Email not found in subscription list.";
            } else {
                $_SESSION['unsubscribe_email'] = $email;
                $_SESSION['unsubscribe_code'] = generateVerificationCode();
                sendVerificationEmail($email, $_SESSION['unsubscribe_code'], 'unsubscribe');
                $message = "🔒 Unsubscribe verification code sent to $email.";
            }
        } else {
            $message = "❌ Invalid email address.";
        }
    }

    // Unsubscribe Verification
    if (isset($_POST['unsubscribe_verification_code'])) {
        if ($_POST['unsubscribe_verification_code'] === ($_SESSION['unsubscribe_code'] ?? '')) {
            if (unsubscribeEmail($_SESSION['unsubscribe_email'])) {
                $message = "✅ You have been unsubscribed.";
            } else {
                $message = "⚠️ Email not found.";
            }
            unset($_SESSION['unsubscribe_email'], $_SESSION['unsubscribe_code']);
        } else {
            $message = "❌ Incorrect unsubscribe code.";
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>GitHub Timeline Subscription</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        form { margin-bottom: 20px; }
        input { padding: 6px; margin-right: 10px; }
        button { padding: 6px 12px; }
        .section { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>GitHub Timeline Subscription</h1>

    <div class="section">
        <h3>Subscribe</h3>
        <form method="post">
            <input type="email" name="email" required placeholder="Enter your email">
            <button id="submit-email">Submit</button>
        </form>

        <form method="post">
            <input type="text" name="verification_code" maxlength="6" required placeholder="Enter verification code">
            <button id="submit-verification">Verify</button>
        </form>
    </div>

    <div class="section">
        <h3>Unsubscribe</h3>
        <form method="post">
            <input type="email" name="unsubscribe_email" required placeholder="Enter your email">
            <button id="submit-unsubscribe">Unsubscribe</button>
        </form>

        <form method="post">
            <input type="text" name="unsubscribe_verification_code" placeholder="Enter verification code">
            <button id="verify-unsubscribe">Verify</button>
        </form>
    </div>

    <?php if (!empty($message)) : ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>
</body>
</html>
