<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        flash('error', 'Please enter a valid email address.');
        header('Location: login.php');
        exit;
    }

    $result = send_otp($email);
    $_SESSION['otp_email'] = $email;
    if ($result['sent']) {
        flash('success', 'OTP sent to your email. If email is unavailable, use the code below for local testing.');
    } else {
        flash('warning', 'Email sending is not available in this environment. Use the one-time code shown below.');
    }
    $_SESSION['debug_otp'] = $result['code'];
    header('Location: verify_otp.php');
    exit;
}

render_header('Login');
?>
<section class="auth-card">
    <div class="auth-copy">
        <h2>Access your learning dashboard</h2>
        <p>Enter your email to receive a 6-digit verification code. Admin access is available with <strong>admin@capstone.local</strong>.</p>
        <ul class="auth-highlights">
            <li>Secure one-time verification</li>
            <li>Instant course and job access</li>
            <li>Track your progress in one place</li>
        </ul>
    </div>
    <div class="form-panel">
        <h2>Email OTP Login</h2>
        <p>Enter your email to receive a 6-digit verification code.</p>
        <form method="post" autocomplete="off">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" required>
            <button type="submit">Send OTP</button>
        </form>
    </div>
</section>
<?php render_footer(); ?>