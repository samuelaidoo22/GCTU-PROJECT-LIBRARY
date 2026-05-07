<?php
require_once '../config/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin') { header("Location: ../login.php"); exit(); }

if (isset($_POST['action'], $_POST['project_id'])) {
    $status = $_POST['action']==='approve' ? 'approved' : 'rejected';
    $pdo->prepare("UPDATE projects SET approval_status=? WHERE project_id=?")->execute([$status,(int)$_POST['project_id']]);
    header("Location: dashboard.php"); exit();
}

$current_page = 'admin'; $page_title = 'Admin Dashboard';

$stats = [
    'total'     => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'approved'  => $pdo->query("SELECT COUNT(*) FROM projects WHERE approval_status='approved'")->fetchColumn(),
    'pending'   => $pdo->query("SELECT COUNT(*) FROM projects WHERE approval_status='pending'")->fetchColumn(),
    'users'     => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'students'  => $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn(),
    'downloads' => $pdo->query("SELECT COUNT(*) FROM access_logs WHERE access_type='download_full'")->fetchColumn(),
];

$pending = $pdo->query("SELECT p.*, d.department_name, u.username FROM projects p JOIN departments d ON p.department_id=d.department_id JOIN users u ON p.uploader_id=u.user_id WHERE p.approval_status='pending' ORDER BY p.upload_date DESC LIMIT 10")->fetchAll();
$logs    = $pdo->query("SELECT l.*, u.username, p.title FROM access_logs l JOIN users u ON l.user_id=u.user_id JOIN projects p ON l.project_id=p.project_id ORDER BY l.access_date DESC LIMIT 6")->fetchAll();
$depts   = $pdo->query("SELECT d.department_code, COUNT(p.project_id) as cnt FROM departments d LEFT JOIN projects p ON d.department_id=p.department_id GROUP BY d.department_id")->fetchAll();
$max = max(array_column($depts,'cnt') ?: [1]);

require_once '../includes/header.php';
?>
<style>
.admin-wrap { display:grid; grid-template-columns:220px 1fr; min-height:calc(100vh - 60px); }
.admin-sidebar { background:#fff; border-right:1px solid #e2e8f0; padding:16px 0; }
.sidebar-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8; padding:12px 16px 6px; }
.sidebar-link { display:flex; align-items:center; gap:10px; padding:9px 16px; font-size:13px; font-weight:500; color:#475569; text-decoration:none; transition:background 0.15s; border-left:3px solid transparent; }
.sidebar-link i { width:16px; text-align:center; }
.sidebar-link:hover, .sidebar-link.active { background:#eff6ff; color:#004AAD; border-left-color:#004AAD; }
.sidebar-badge { margin-left:auto; background:#fee2e2; color:#b91c1c; font-size:10px; font-weight:700; padding:1px 6px; border-radius:8px; }
.admin-main { background:#f0f4f8; padding:28px; }
.stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
.stat-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:18px; border-top:3px solid; }
.stat-card .lbl { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#64748b; margin-bottom:8px; }
.stat-card .val { font-size:28px; font-weight:700; color:#1e293b; }
.stat-card .sub { font-size:12px; color:#94a3b8; margin-top:4px; }
.panel { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:20px; }
.panel-head { font-size:15px; font-weight:700; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; }
.bar-chart { display:flex; align-items:flex-end; gap:8px; height:120px; margin-top:8px; }
.bar-col { flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; }
.bar-fill { width:100%; background:linear-gradient(to top,#004AAD,#3b82f6); border-radius:3px 3px 0 0; min-height:3px; }
.bar-label { font-size:10px; color:#94a3b8; font-weight:600; }
.bar-val   { font-size:11px; color:#475569; font-weight:700; }
.log-row { display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid #f1f5f9; }
.log-row:last-child { border-bottom:none; }
.log-icon { width:30px; height:30px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0; }
.log-icon.dl  { background:#dbeafe; color:#1d4ed8; }
.log-icon.vw  { background:#f1f5f9; color:#64748b; }
.log-text strong { font-size:13px; display:block; color:#1e293b; }
.log-text span   { font-size:11px; color:#94a3b8; }
.log-time { font-size:11px; color:#94a3b8; margin-left:auto; white-space:nowrap; }

@media (max-width: 768px) {
    .admin-wrap { grid-template-columns: 1fr; }
    .admin-sidebar { position: static; height: auto; padding: 0 16px 16px; border-right: none; border-bottom: 1px solid #e2e8f0; display: flex; flex-wrap: wrap; gap: 8px; }
    .sidebar-label { width: 100%; padding: 12px 0 4px; }
    .sidebar-link { padding: 6px 10px; border-left: none; border-bottom: 2px solid transparent; }
    .sidebar-link:hover, .sidebar-link.active { border-left-color: transparent; border-bottom-color: #004AAD; }
    .stat-grid { grid-template-columns: 1fr 1fr; }
    .admin-main > div:nth-child(3) { grid-template-columns: 1fr !important; }
}
</style>

<div class="admin-wrap">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-label">Overview</div>
        <a href="dashboard.php" class="sidebar-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

        <div class="sidebar-label">Content</div>
        <a href="dashboard.php" class="sidebar-link">
            <i class="fas fa-clock"></i> Pending Approvals
            <?php if ($stats['pending'] > 0): ?><span class="sidebar-badge"><?php echo $stats['pending']; ?></span><?php endif; ?>
        </a>
        <a href="../browse.php" class="sidebar-link"><i class="fas fa-book-open"></i> All Projects</a>

        <div class="sidebar-label">Users</div>
        <a href="#" class="sidebar-link"><i class="fas fa-users"></i> Manage Users</a>

        <div class="sidebar-label">System</div>
        <a href="#" class="sidebar-link"><i class="fas fa-scroll"></i> Activity Logs</a>
        <a href="../logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>

        <div style="margin:20px 12px 0; padding:12px; background:#f0f4f8; border-radius:8px; font-size:12px;">
            <div style="font-weight:700; color:#004AAD; margin-bottom:4px;">Logged in as</div>
            <div style="color:#374151;"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
            <div style="color:#94a3b8; margin-top:2px;">Administrator</div>
        </div>
    </aside>

    <!-- Main -->
    <div class="admin-main">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
            <div>
                <h1 style="font-size:22px; font-weight:700;">Admin Dashboard</h1>
                <p class="text-muted text-sm"><?php echo date('l, d F Y'); ?></p>
            </div>
            <a href="../upload.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Project</a>
        </div>

        <!-- Stats -->
        <div class="stat-grid">
            <div class="stat-card" style="border-top-color:#004AAD;">
                <div class="lbl">Total Projects</div>
                <div class="val"><?php echo $stats['total']; ?></div>
                <div class="sub"><?php echo $stats['approved']; ?> approved</div>
            </div>
            <div class="stat-card" style="border-top-color:#f59e0b;">
                <div class="lbl">Pending Review</div>
                <div class="val" style="color:#d97706;"><?php echo $stats['pending']; ?></div>
                <div class="sub">Awaiting approval</div>
            </div>
            <div class="stat-card" style="border-top-color:#16a34a;">
                <div class="lbl">Registered Users</div>
                <div class="val"><?php echo $stats['users']; ?></div>
                <div class="sub"><?php echo $stats['students']; ?> students</div>
            </div>
            <div class="stat-card" style="border-top-color:#7c3aed;">
                <div class="lbl">Total Downloads</div>
                <div class="val"><?php echo $stats['downloads']; ?></div>
                <div class="sub">Full PDF downloads</div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 280px; gap:20px; margin-bottom:20px;">
            <!-- Bar chart -->
            <div class="panel">
                <div class="panel-head">Projects by Department</div>
                <div class="bar-chart">
                    <?php foreach ($depts as $d):
                        $h = $max > 0 ? round(($d['cnt']/$max)*100) : 3;
                    ?>
                        <div class="bar-col">
                            <div class="bar-val"><?php echo $d['cnt']; ?></div>
                            <div class="bar-fill" style="height:<?php echo max($h,3); ?>px;"></div>
                            <div class="bar-label"><?php echo htmlspecialchars($d['department_code']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Activity -->
            <div class="panel">
                <div class="panel-head">Recent Activity</div>
                <?php if (empty($logs)): ?>
                    <p class="text-muted text-sm">No activity yet.</p>
                <?php else: ?>
                    <?php foreach ($logs as $l): $dl = $l['access_type']==='download_full'; ?>
                        <div class="log-row">
                            <div class="log-icon <?php echo $dl?'dl':'vw'; ?>">
                                <i class="fas <?php echo $dl?'fa-download':'fa-eye'; ?>"></i>
                            </div>
                            <div class="log-text">
                                <strong><?php echo htmlspecialchars($l['username']); ?> <?php echo $dl?'downloaded':'viewed'; ?></strong>
                                <span><?php echo substr(htmlspecialchars($l['title']), 0, 35).'…'; ?></span>
                            </div>
                            <span class="log-time"><?php echo date('H:i', strtotime($l['access_date'])); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pending table -->
        <div class="panel">
            <div class="panel-head">
                Pending Approvals
                <span class="badge <?php echo $stats['pending']>0?'badge-gold':'badge-green'; ?>"><?php echo $stats['pending']; ?> pending</span>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pending)): ?>
                        <tr><td colspan="5" style="text-align:center; color:#64748b; padding:30px;"><i class="fas fa-check-circle" style="color:#16a34a; margin-right:6px;"></i>All clear — nothing pending.</td></tr>
                    <?php else: foreach ($pending as $p): ?>
                        <tr>
                            <td style="max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($p['title']); ?></td>
                            <td><span class="badge badge-blue"><?php echo htmlspecialchars($p['department_name']); ?></span></td>
                            <td><?php echo htmlspecialchars($p['username']); ?></td>
                            <td><?php echo date('d M Y', strtotime($p['upload_date'])); ?></td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="project_id" value="<?php echo $p['project_id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Approve</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="project_id" value="<?php echo $p['project_id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
