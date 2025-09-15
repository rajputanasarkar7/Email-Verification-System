
# GH-timeline

This project is a PHP-based email verification system where users register using their email, receive a verification code, and subscribe to GitHub timeline updates. A CRON job fetches the latest GitHub timeline every 5 minutes and sends updates to registered users via email.

 

✅ Implement all required functions in `functions.php`.  

✅ Implement a form in `index.php` to take email input and verify via code.  

✅ Implement a CRON job to send GitHub timeline updates every 5 minutes.  

✅ Implement an unsubscribe feature where users can opt out via email verification.

✅ Implement `unsubscribe.php` to handle email unsubscription.



## ⚠️ Important Notes

I use [Mailpit](https://mailpit.axllent.org/) for local testing of email functionality.

**I used PHP version: 8.3**

---

## 📌 Features to Implement

### 1️⃣ **Email Verification**
- Users enter their email in a form.
- A **6-digit numeric code** is generated and emailed to them.
- Users enter the code in the form to verify and register.
- Store the verified email in `registered_emails.txt`.

### 2️⃣ **Unsubscribe Mechanism**
- Emails should include an **unsubscribe link**.
- Clicking it will take user to the unsubscribe page.
- Users enter their email in a form.
- A **6-digit numeric code** is generated and emailed to them.
- Users enter the code to confirm unsubscription.

### 3️⃣ **GitHub Timeline Fetch**
- Every 5 minutes, a CRON job should:
  - Fetch data from `https://www.github.com/timeline`
  - Format it as **HTML (not JSON)**.
  - Send it via email to all registered users.

---

## 📜 File Details & Function Stubs

**I  implement the following functions inside `functions.php`:**

```php
function generateVerificationCode() {
    // Generate and return a 6-digit numeric code
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    // Save verified email to registered_emails.txt
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    // Remove email from registered_emails.txt
}

function sendVerificationEmail($email, $code) {
    // Send an email containing the verification code
}

function fetchGitHubTimeline() {
    // Fetch latest data from https://www.github.com/timeline
}

function formatGitHubData($data) {
    // Convert fetched data into formatted HTML
}

function sendGitHubUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    // Send formatted GitHub timeline to registered users
}
```
## 🔄 CRON Job Implementation

📌 You must implement a **CRON job** that runs `cron.php` every 5 minutes.  
📌 **Do not just write instructions**—provide an actual **setup_cron.sh** script inside `src/`.  
📌 **Your script should automatically configure the CRON job on execution.**  

---

### 🛠 Required Files

- **`setup_cron.sh`** (Must configure the CRON job)
- **`cron.php`** (Must handle sending GitHub updates via email)

---

### 🚀 How It Should Work

- The `setup_cron.sh` script should register a **CRON job** that executes `cron.php` every 5 minutes.
- The CRON job **must be automatically added** when the script runs.
- The `cron.php` file should actually **fetch GitHub timeline data** and **send emails** to registered users.

---

## 📩 Email Handling

✅ The email content must be in **HTML format** (not JSON).  
✅ Use **PHP's `mail()` function** for sending emails.  
✅ Each email should include an **unsubscribe link**.  
✅ Unsubscribing should trigger a **confirmation code** before removal.  
✅ Store emails in `registered_emails.txt` (**Do not use a database**).  

---

.  

---
## 📌 Input & Button Formatting 

### 📧 Email Input & Submission Button:
- The email input field  `name="email"`.
- The submit button `id="submit-email"`.

#### ✅ Example:
```html
<input type="email" name="email" required>
<button id="submit-email">Submit</button>
```
---
### 🔢 Verification Code Input & Submission Button:

- The verification input field  `name="verification_code"`.  
- The submit button 
`id="submit-verification"`.  

#### ✅ Example:
```html
<input type="text" name="verification_code" maxlength="6" required>
<button id="submit-verification">Verify</button>
```
---
### 🚫 Unsubscribe Email & Submission Button
- The unsubscribe input field `name="unsubscribe_email"`.
- The submit button  `id="submit-unsubscribe"`.
#### ✅ Example:
```html
<input type="email" name="unsubscribe_email" required>
<button id="submit-unsubscribe">Unsubscribe</button>
```
---
### 🚫 Unsubscribe Code Input & Submission Button
- The unsubscribe code input field  `name="unsubscribe_verification_code"`.
- The submit button  `id="verify-unsubscribe"`.
#### ✅ Example:
```html
<input type="text" name="unsubscribe_verification_code">
<button id="verify-unsubscribe">Verify</button>
```
---

## 📩 Email Content 
#### ✅ Verification Email:
- **Subject:** `Your Verification Code`
- **Body Format:**
```html
<p>Your verification code is: <strong>123456</strong></p>
```
- Sender: no-reply@example.com
---
.

#### ✅ GitHub Updates Email:
- **Subject:** `Latest GitHub Updates`
- **Body Format:**
```html
<h2>GitHub Timeline Updates</h2>
<table border="1">
  <tr><th>Event</th><th>User</th></tr>
  <tr><td>Push</td><td>testuser</td></tr>
</table>
<p><a href="unsubscribe_url" id="unsubscribe-button">Unsubscribe</a></p>
```
---
### ✅ Unsubscribe Confirmation Email:
- **Subject:** `Confirm Unsubscription`
- **Body Format:**
```html
<p>To confirm unsubscription, use this code: <strong>654321</strong></p>
```
---
