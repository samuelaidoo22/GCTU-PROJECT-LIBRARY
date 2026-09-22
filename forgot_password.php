<?php
require_once 'config/db.php';
require_once 'includes/csrf.php';
require_once 'includes/functions.php';

$page_title = 'Forgot Password';
$sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $error = 'Invalid or expired session. Please try again.';
    } else {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $sent = true;

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $pdo->prepare("SELECT user_id, email FROM users WHERE email = ? AND status = 'active' LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);
                $pdo->prepare('DELETE FROM password_resets WHERE user_id = ? OR expires_at < NOW()')->execute([(int)$user['user_id']]);
                $pdo->prepare('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))')->execute([(int)$user['user_id'], $tokenHash]);

                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/.');
                $resetUrl = $scheme . '://' . $host . $basePath . '/reset_password.php?token=' . urlencode($token);
                $message = "Hello,\n\nWe received a request to reset your GCTU Project Library password.\n\nOpen this link within 1 hour to choose a new password:\n{$resetUrl}\n\nIf you did not request this, you can ignore this email. Your password will remain unchanged.\n\nGCTU Project Library";
                send_notification_email($user['email'], 'GCTU Project Library: Reset your password', $message);
            }
        }
    }
}

require_once 'includes/header.php';
?>
<style>
.auth-wrap { min-height: calc(100vh - 60px); display:flex; align-items:center; justify-content:center; padding:40px 16px; background:linear-gradient(rgba(5,27,54,.80),rgba(7,38,75,.88)),url('gt_pic/library.jpg') center/cover no-repeat; }
.auth-box { background:rgba(255,255,255,.98); border:1px solid rgba(255,255,255,.15); border-radius:14px; width:100%; max-width:420px; padding:36px; box-shadow:0 18px 48px rgba(2,12,24,.26); }
.auth-logo { text-align:center; margin-bottom:24px; }.auth-logo img { height:50px; border-radius:6px; }.auth-logo h2 { font-size:18px; font-weight:700; margin-top:10px; color:#1e293b; }.auth-logo p { font-size:13px; color:#64748b; margin-top:4px; }.link-row { text-align:center; font-size:13px; color:#64748b; margin-top:16px; }.link-row a { color:#004AAD; font-weight:600; }
</style>
<div class="auth-wrap">
    <div class="auth-box">
        <div style="margin-bottom:16px; text-align:left;"><button type="button" class="back-button auth" onclick="goBackSafely();"><i class="fas fa-arrow-left"></i> Back</button></div>
        <div class="auth-logo"><img src="gt_pic/logo.jpg" alt="GCTU Logo"><h2>Reset your password</h2><p>Enter the email linked to your account</p></div>
        <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo h($error); ?></div><?php endif; ?>
        <?php if ($sent && !$error): ?>
            <div class="alert alert-success"><i class="fas fa-envelope"></i> If an active account uses that email, a password reset link has been sent.</div>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo h(generate_csrf_token()); ?>">
                <div class="form-group"><label class="form-label" for="email">Email address</label><input id="email" type="email" name="email" class="form-control" placeholder="you@example.com" required autocomplete="email"></div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:12px;"><i class="fas fa-paper-plane"></i> Send reset link</button>
            </form>
        <?php endif; ?>
        <p class="link-row"><a href="login.php">Return to sign in</a></p>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>