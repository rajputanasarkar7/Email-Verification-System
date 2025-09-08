<?php
/**
 * Generate a 6-digit numeric verification code.
*/
function generateVerificationCode(): string {
  return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}
/*Send a verification code to an email.*/
function sendVerificationEmail(string $email, string $code, string $type = 'subscribe'): bool {
    // Set subject & message based on type
    if ($type === 'subscribe') {
        $subject = "Your Verification Code"; // ✅ required subject
        $message = "<p>Your verification code is: <strong>$code</strong></p>"; // ✅ required body
    } elseif ($type === 'unsubscribe') {
        $subject = "Confirm Unsubscription"; // ✅ required subject
        $message = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>"; // ✅ required body
    } else {
        return false; // Unknown type
    }

    // Common headers
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    // Mailpit (local) setup
    ini_set("SMTP", "127.0.0.1");
    ini_set("smtp_port", "1025");
    ini_set("sendmail_from", "no-reply@example.com");

    // Send email
    return mail($email, $subject, $message, $headers);
}


/**
 * Register an email by storing it in a file.
 */
function registerEmail(string $email): bool {
  $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
        return true;
        }
        return false;
}

/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail(string $email): bool {
  $file = __DIR__ . '/registered_emails.txt';
  if (!file_exists($file)) return false;

  $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  $filtered = array_filter($emails, fn($line) => trim($line) !== trim($email));

  if (count($emails) === count($filtered)) {
      return false; // Email not found
  }

  file_put_contents($file, implode(PHP_EOL, $filtered) . PHP_EOL);
  return true;
}

/**
 * Fetch GitHub timeline.
 */
function fetchGitHubTimeline() {
  $url = 'https://api.github.com/events';
  $context = stream_context_create([
      'http' => [
          'header' => "User-Agent: PHP"
      ]
  ]);

  $response = file_get_contents($url, false, $context);
  return json_decode($response, true);
}

/**
 * Format GitHub timeline data. Returns a valid HTML sting.
 */
function formatGitHubData(array $data): string {
    // Simulate GitHub event data formatting
    $html = '<h2>GitHub Timeline Updates</h2>';
    $html .= '<table border="1"><tr><th>Event</th><th>User</th></tr>';

    // Example dummy data; replace this with real parsed data if needed
    foreach ($data as $item) {
        $event = htmlspecialchars($item['event'] ?? 'Push');
        $user = htmlspecialchars($item['user'] ?? 'testuser');
        $html .= "<tr><td>$event</td><td>$user</td></tr>";
    }

    $html .= '</table>';
    $html .= '<p><a href="http://localhost/GH-timeline/src/unsubscribe.php" id="unsubscribe-button">Unsubscribe</a></p>';

    return $html;
}
/**
 * Send the formatted GitHub updates to registered emails.
 */
function sendGitHubUpdatesToSubscribers(): void {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Fetch GitHub timeline
    $rawData = fetchGitHubTimeline();

    // Fallback dummy data
    $sampleData = [['event' => 'Push', 'user' => 'testuser']];

    // Parse API JSON response if successful
    $eventData = [];
    if ($rawData !== false) {
        $parsed = json_decode($rawData, true);
        if (is_array($parsed)) {
            foreach ($parsed as $item) {
                $eventData[] = [
                    'event' => $item['type'] ?? 'Unknown',
                    'user' => $item['actor']['login'] ?? 'Anonymous'
                ];
            }
        } else {
            $eventData = $sampleData;
        }
    } else {
        $eventData = $sampleData;
    }

    // Format HTML content
    $htmlContent = formatGitHubData($eventData);

    // Prepare email headers
    $subject = "Latest GitHub Updates";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";

    // Optional: for local Mailpit setup
    ini_set("SMTP", "127.0.0.1");
    ini_set("smtp_port", "1025");

    // Send to all subscribers
    foreach ($emails as $email) {
        mail($email, $subject, $htmlContent, $headers);
    }
}

