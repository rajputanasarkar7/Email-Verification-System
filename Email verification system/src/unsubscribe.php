<?php
require_once 'functions.php';

session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Email form submitted for unsubscribe
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['unsubscribe_email'] = $email;
            $_SESSION['unsubscribe_code'] = generateVerificationCode();

            // Custom HTML format for unsubscribe verification email
            $subject = "Confirm Unsubscription"; // ✅ Required subject
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= "From: no-reply@example.com\r\n";
            ini_set("SMTP", "127.0.0.1");
            ini_set("smtp_port", "1025");
            ini_set("sendmail_from", "no-reply@example.com");

            $body = "<p>To confirm unsubscription, use this code: <strong>{$_SESSION['unsubscribe_code']}</strong></p>";

            if (mail($email, $subject, $body, $headers)) {
                $message = "Verification code sent to $email.";
            } else {
                $message = "Failed to send email. Check Mailpit.";
            }
        } else {
            $message = "Invalid email address.";
        }
    }

    // Step 2: Verification code form submitted
    if (isset($_POST['unsubscribe_verification_code'])) {
        $inputCode = trim($_POST['unsubscribe_verification_code']);
        if (
            isset($_SESSION['unsubscribe_code'], $_SESSION['unsubscribe_email']) &&
            $inputCode === $_SESSION['unsubscribe_code']
        ) {
            if (unsubscribeEmail($_SESSION['unsubscribe_email'])) {
                $message = "You have been unsubscribed.";
            } else {
                $message = "Email not found or already unsubscribed.";
            }

            unset($_SESSION['unsubscribe_email'], $_SESSION['unsubscribe_code']);
        } else {
            $message = "Incorrect verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        input, button { padding: 8px; margin: 4px 0; }
    </style>
</head>
<body>
    <h2>Unsubscribe from GitHub Updates</h2>

    <form method="post">
        <input type="email" name="unsubscribe_email" required placeholder="Enter your email">
        <button type="submit" id="submit-unsubscribe">Send Code</button>
    </form>

    <form method="post">
        <input type="text" name="unsubscribe_verification_code" maxlength="6" placeholder="Enter verification code">
        <button type="submit" id="verify-unsubscribe">Verify</button>
    </form>

    <?php if (!empty($message)): ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>
</body>
</html>
