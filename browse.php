<?php
require_once 'config/db.php';
$current_page = 'browse';
$page_title   = 'Browse Projects';

$dept_filter = isset($_GET['dept']) ? (int)$_GET['dept'] : 0;
$cat_filter  = isset($_GET['cat'])  ? (int)$_GET['cat']  : 0;
$year_filter = isset($_GET['year']) ? (int)$_GET['year'] : 0;

$conds = ["p.approval_status='approved'"]; $params = [];
if ($dept_filter) { $conds[] = "p.department_id=?"; $params[] = $dept_filter; }
if ($cat_filter)  { $conds[] = "p.category_id=?";  $params[] = $cat_filter; }
if ($year_filter) { $conds[] = "YEAR(p.upload_date)=?"; $params[] = $year_filter; }
$where = implode(' AND ', $conds);

$stmt = $pdo->prepare("SELECT p.*, d.department_name, c.category_name FROM projects p JOIN departments d ON p.department_id=d.department_id JOIN categories c ON p.category_id=c.category_id WHERE $where ORDER BY p.upload_date DESC");
$stmt->execute($params);
$projects = $stmt->fetchAll();

$departments = $pdo->query("SELECT * FROM departments")->fetchAll();
$categories  = $pdo->query("SELECT * FROM categories")->fetchAll();

require_once 'includes/header.php';
?>

<style>
.browse-layout {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 24px;
    padding: 32px 0 64px;
    align-items: start;
}
.filter-panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    position: sticky;
    top: 72px;
}
.filter-panel-head {
    background: #004AAD;
    color: #fff;
    padding: 12px 16px;
    font-size: 13px;
    font-weight: 700;
}
.filter-section { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; }
.filter-section h4 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 10px; }
.filter-link {
    display: flex; justify-content: space-between; align-items: center;
    padding: 6px 8px; border-radius: 5px; font-size: 13px; color: #374151;
    text-decoration: none; margin-bottom: 2px; transition: background 0.1s;
}
.filter-link:hover, .filter-link.active {
    background: #eff6ff; color: #004AAD;
}
.filter-link.active { font-weight: 600; }
.filter-link .cnt  { font-size: 11px; color: #94a3b8; background: #f1f5f9; padding: 1px 6px; border-radius: 10px; }
.filter-link.active .cnt { background: #dbeafe; color: #1d4ed8; }

.results-bar {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
}
.results-bar h2 { font-size: 16px; font-weight: 700; }
.results-bar span { font-size: 13px; color: #64748b; }

.proj-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 12px;
    display: block;
    text-decoration: none;
    color: inherit;
    transition: box-shadow 0.2s, border-color 0.2s;
}
.proj-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.07); border-color: #93c5fd; }
.proj-card h3 { font-size: 15px; font-weight: 600; color: #1e293b; margin: 8px 0 8px; line-height: 1.4; }
.proj-card .abstract { font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 14px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.proj-card .card-meta { display: flex; gap: 16px; font-size: 12px; color: #94a3b8; align-items: center; }
.proj-card .card-meta span { display: flex; align-items: center; gap: 4px; }

.empty-state { text-align: center; padding: 60px 20px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; }
.empty-state i { font-size: 40px; color: #cbd5e1; margin-bottom: 12px; display: block; }
.empty-state h3 { font-weight: 600; color: #64748b; margin-bottom: 6px; }
.empty-state p  { font-size: 13px; color: #94a3b8; }

@media (max-width: 768px) {
    .browse-layout { grid-template-columns: 1fr; gap: 16px; padding: 20px 0 40px; }
    .filter-panel { position: static; margin-bottom: 16px; }
}
</style>

<div class="page-banner">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a> &rsaquo; Browse Projects</div>
        <h1>Browse Projects</h1>
        <p><?php echo count($projects); ?> project<?php echo count($projects)!=1?'s':''; ?> found</p>
    </div>
</div>

<div class="container">
    <div class="browse-layout">

        <!-- Filter Sidebar -->
        <aside>
            <div class="filter-panel">
                <div class="filter-panel-head"><i class="fas fa-filter"></i> &nbsp;Filter</div>

                <div class="filter-section">
                    <h4>Department</h4>
                    <a href="browse.php" class="filter-link <?php echo !$dept_filter?'active':''; ?>">
                        All <span class="cnt"><?php echo count($projects); ?></span>
                    </a>
                    <?php foreach ($departments as $d): ?>
                        <a href="browse.php?dept=<?php echo $d['department_id']; ?><?php echo $cat_filter?"&cat=$cat_filter":''; ?>"
                           class="filter-link <?php echo $dept_filter==$d['department_id']?'active':''; ?>">
                            <?php echo htmlspecialchars($d['department_code']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="filter-section">
                    <h4>Category</h4>
                    <a href="browse.php<?php echo $dept_filter?"?dept=$dept_filter":''; ?>"
                       class="filter-link <?php echo !$cat_filter?'active':''; ?>">All</a>
                    <?php foreach ($categories as $c): ?>
                        <a href="browse.php?cat=<?php echo $c['category_id']; ?><?php echo $dept_filter?"&dept=$dept_filter":''; ?>"
                           class="filter-link <?php echo $cat_filter==$c['category_id']?'active':''; ?>">
                            <?php echo htmlspecialchars($c['category_name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="filter-section" style="border-bottom:none;">
                    <h4>Year</h4>
                    <?php foreach ([2024,2023,2022,2021] as $y): ?>
                        <a href="browse.php?year=<?php echo $y; ?><?php echo $dept_filter?"&dept=$dept_filter":''; ?>"
                           class="filter-link <?php echo $year_filter==$y?'active':''; ?>">
                            <?php echo $y; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>

        <!-- Results -->
        <div>
            <div class="results-bar">
                <h2>All Projects</h2>
                <span><?php echo count($projects); ?> results</span>
            </div>

            <?php if (empty($projects)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>No projects found</h3>
                    <p>Try a different filter combination.</p>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $p): ?>
                    <a href="project.php?id=<?php echo $p['project_id']; ?>" class="proj-card">
                        <span class="badge badge-blue"><?php echo htmlspecialchars($p['department_name']); ?></span>
                        <h3><?php echo htmlspecialchars($p['title']); ?></h3>
                        <p class="abstract"><?php echo htmlspecialchars($p['abstract']); ?></p>
                        <div class="card-meta">
                            <span><i class="fas fa-calendar-alt"></i> <?php echo date('M Y', strtotime($p['upload_date'])); ?></span>
                            <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($p['category_name']); ?></span>
                            <span><i class="fas fa-eye"></i> <?php echo $p['view_count']; ?> views</span>
                            <span style="margin-left:auto; color:#004AAD; font-weight:600; font-size:12px;">View &rarr;</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
