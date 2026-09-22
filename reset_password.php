<?php
require_once 'config/db.php';
require_once 'includes/csrf.php';
require_once 'includes/functions.php';

$page_title = 'Reset Password';
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$tokenHash = $token !== '' ? hash('sha256', $token) : '';
$error = '';
$success = false;
$validToken = false;

if ($tokenHash !== '') {
    $stmt = $pdo->prepare('SELECT reset_id, user_id FROM password_resets WHERE token_hash = ? AND expires_at > NOW() LIMIT 1');
    $stmt->execute([$tokenHash]);
    $validToken = (bool)$stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $error = 'Invalid or expired session. Please try again.';
    } elseif (!$validToken) {
        $error = 'This reset link is invalid or has expired. Please request a new one.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirmation = $_POST['confirm_password'] ?? '';
        if (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirmation) {
            $error = 'Passwords do not match.';
        } else {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare('SELECT user_id FROM password_resets WHERE token_hash = ? AND expires_at > NOW() LIMIT 1 FOR UPDATE');
                $stmt->execute([$tokenHash]);
                $reset = $stmt->fetch();
                if (!$reset) {
                    throw new RuntimeException('expired');
                }
                $pdo->prepare('UPDATE users SET password_hash = ? WHERE user_id = ? AND status = \'active\'')->execute([password_hash($password, PASSWORD_DEFAULT), (int)$reset['user_id']]);
                $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([(int)$reset['user_id']]);
                $pdo->commit();
                $success = true;
                $validToken = false;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $error = 'This reset link is invalid or has expired. Please request a new one.';
            }
        }
    }
}

require_once 'includes/header.php';
?>
<style>
.auth-wrap { min-height:calc(100vh - 60px); display:flex; align-items:center; justify-content:center; padding:40px 16px; background:linear-gradient(rgba(5,27,54,.80),rgba(7,38,75,.88)),url('gt_pic/library.jpg') center/cover no-repeat; }
.auth-box { background:rgba(255,255,255,.98); border:1px solid rgba(255,255,255,.15); border-radius:14px; width:100%; max-width:420px; padding:36px; box-shadow:0 18px 48px rgba(2,12,24,.26); }
.auth-logo { text-align:center; margin-bottom:24px; }.auth-logo img { height:50px; border-radius:6px; }.auth-logo h2 { font-size:18px; font-weight:700; margin-top:10px; color:#1e293b; }.auth-logo p { font-size:13px; color:#64748b; margin-top:4px; }.link-row { text-align:center; font-size:13px; color:#64748b; margin-top:16px; }.link-row a { color:#004AAD; font-weight:600; }
</style>
<div class="auth-wrap"><div class="auth-box">
    <div style="margin-bottom:16px; text-align:left;"><button type="button" class="back-button auth" onclick="goBackSafely();"><i class="fas fa-arrow-left"></i> Back</button></div>
    <div class="auth-logo"><img src="gt_pic/logo.jpg" alt="GCTU Logo"><h2>Choose a new password</h2><p>Use at least 8 characters</p></div>
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> Your password has been reset successfully.</div><p class="link-row"><a href="login.php">Sign in with your new password</a></p>
    <?php elseif (!$validToken): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo h($error ?: 'This reset link is invalid or has expired.'); ?></div><p class="link-row"><a href="forgot_password.php">Request a new reset link</a></p>
    <?php else: ?>
        <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo h($error); ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo h(generate_csrf_token()); ?>"><input type="hidden" name="token" value="<?php echo h($token); ?>">
            <div class="form-group"><label class="form-label" for="reset-password">New password</label><div class="password-field"><input id="reset-password" type="password" name="password" class="form-control" required autocomplete="new-password"><button type="button" class="password-toggle" aria-controls="reset-password" aria-label="Show password" title="Show password" onclick="togglePasswordVisibility(this)"><i class="fas fa-eye" aria-hidden="true"></i></button></div></div>
            <div class="form-group"><label class="form-label" for="reset-confirm-password">Confirm new password</label><div class="password-field"><input id="reset-confirm-password" type="password" name="confirm_password" class="form-control" required autocomplete="new-password"><button type="button" class="password-toggle" aria-controls="reset-confirm-password" aria-label="Show password" title="Show password" onclick="togglePasswordVisibility(this)"><i class="fas fa-eye" aria-hidden="true"></i></button></div></div>
            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:12px;"><i class="fas fa-key"></i> Reset password</button>
        </form>
    <?php endif; ?>
</div></div>
<?php require_once 'includes/footer.php'; ?>