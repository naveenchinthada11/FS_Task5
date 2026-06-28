<?php
require_once 'functions.php';

$email = $_SESSION['otp_email'] ?? null;
if (!$email) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['otp'] ?? '');
    if (!$code || !ctype_digit($code)) {
        flash('error', 'Enter the 6-digit OTP code.');
        header('Location: verify_otp.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT * FROM otps WHERE email = ? AND code = ? AND expires_at > NOW()');
    $stmt->execute([$email, $code]);
    $otp = $stmt->fetch();
    if (!$otp) {
        flash('error', 'OTP is invalid or expired. Request a new one.');
        header('Location: login.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        $stmt = $pdo->prepare('INSERT INTO users (email, role, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$email, 'student']);
        $userId = $pdo->lastInsertId();
    } else {
        $userId = $user['id'];
    }

    $stmt = $pdo->prepare('DELETE FROM otps WHERE id = ?');
    $stmt->execute([$otp['id']]);

    $_SESSION['user_id'] = $userId;
    unset($_SESSION['otp_email'], $_SESSION['debug_otp']);
    flash('success', 'You are logged in successfully.');
    header('Location: dashboard.php');
    exit;
}

render_header('Verify OTP');
?>
<section class="form-panel">
    <h2>Enter OTP</h2>
    <p>An OTP was sent to <strong><?php echo h($email); ?></strong>. It expires in 10 minutes.</p>
    <form method="post" autocomplete="off">
        <label for="otp">OTP Code</label>
        <input id="otp" name="otp" type="text" maxlength="6" required>
        <button type="submit">Verify</button>
    </form>
    <?php if (!empty($_SESSION['debug_otp'])): ?>
        <div class="debug-note">
            Local test OTP: <strong><?php echo h($_SESSION['debug_otp']); ?></strong>
        </div>
    <?php endif; ?>
</section>
<?php render_footer(); ?>