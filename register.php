<?php
require_once 'config/db.php';
$current_page = 'register';
$page_title   = 'Register';
$error = ''; $success = '';
$departments = $pdo->query("SELECT * FROM departments")->fetchAll();
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $u = trim($_POST['username']); $e = trim($_POST['email']);
    $p = $_POST['password'];       $c = $_POST['confirm_password'];
    $d = (int)$_POST['department_id'];
    if ($p !== $c) { $error = "Passwords do not match."; }
    elseif (strlen($p) < 8) { $error = "Password must be at least 8 characters."; }
    else {
        $chk = $pdo->prepare("SELECT user_id FROM users WHERE username=? OR email=?");
        $chk->execute([$u,$e]);
        if ($chk->fetch()) { $error = "Username or email already taken."; }
        else {
            $hash = password_hash($p, PASSWORD_BCRYPT);
            $pdo->prepare("INSERT INTO users (username,email,password_hash,role,department_id,status) VALUES (?,?,?,'student',?,'active')")->execute([$u,$e,$hash,$d]);
            $success = "Account created successfully!";
        }
    }
}
require_once 'includes/header.php';
?>
<style>
.auth-wrap { min-height:calc(100vh - 60px); display:flex; align-items:center; justify-content:center; padding:40px 16px; background: linear-gradient(rgba(0, 74, 173, 0.75), rgba(0, 33, 71, 0.9)), url('gt_pic/library.jpg') center/cover no-repeat; }
.auth-box { background:#fff; border:none; border-radius:12px; width:100%; max-width:480px; padding:36px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25); }
.auth-logo { text-align:center; margin-bottom:24px; }
.auth-logo img { height:48px; border-radius:6px; }
.auth-logo h2 { font-size:18px; font-weight:700; margin-top:10px; }
.auth-logo p  { font-size:13px; color:#64748b; margin-top:4px; }
.grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:0 16px; }
hr.divider { border:none; border-top:1px solid #f1f5f9; margin:20px 0; }
.link-row { text-align:center; font-size:13px; color:#64748b; margin-top:16px; }
.link-row a { color:#004AAD; font-weight:600; }
</style>
<div class="auth-wrap">
    <div class="auth-box">
        <div class="auth-logo">
            <img src="gt_pic/logo.jpg" alt="GCTU">
            <h2>Create Account</h2>
            <p>Join the GCTU Project Library</p>
        </div>
        <?php if ($error):   ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?> <a href="login.php">Sign in &rarr;</a></div><?php endif; ?>
        <form method="POST">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Username *</label>
                    <input type="text" name="username" class="form-control" placeholder="e.g. kwame.asante" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Department *</label>
                    <select name="department_id" class="form-control" required>
                        <option value="" disabled selected>Select…</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?php echo $d['department_id']; ?>"><?php echo $d['department_code']; ?> — <?php echo $d['department_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" placeholder="you@student.gctu.edu.gh" required>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:12px;">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>
        <hr class="divider">
        <p class="link-row">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
