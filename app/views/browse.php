<div class="page-banner">
    <div class="container">
        <div style="margin-bottom:10px;">
            <a href="javascript:history.back()" style="font-size:13px; color:#fff; text-decoration:none;"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <div class="breadcrumb"><a href="index.php">Home</a> &rsaquo; Browse Projects</div>
        <h1>Browse Projects</h1>
        <p><?php echo count($projects); ?> project<?php echo count($projects) !== 1 ? 's' : ''; ?> found</p>
    </div>
</div>

<div class="container" style="padding:32px 0 64px;">
    <div class="grid" style="grid-template-columns: 1fr 320px; gap: 32px; align-items: start;">
        <div>
            <div class="card mb-3">
                <form method="GET" action="browse.php" class="grid" style="gap:16px;">
                    <div>
                        <label class="form-label">Department</label>
                        <select name="dept" class="form-control" onchange="this.form.submit()">
                            <option value="0">All Departments</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?php echo $d['department_id']; ?>" <?php echo $dept_filter == $d['department_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['department_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Category</label>
                        <select name="cat" class="form-control" onchange="this.form.submit()">
                            <option value="0">All Categories</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['category_id']; ?>" <?php echo $cat_filter == $c['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Year</label>
                        <select name="year" class="form-control" onchange="this.form.submit()">
                            <option value="0">All Years</option>
                            <?php foreach ([date('Y'), date('Y') - 1, date('Y') - 2, date('Y') - 3] as $y): ?>
                                <option value="<?php echo $y; ?>" <?php echo $year_filter == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if ($dept_filter || $cat_filter || $year_filter): ?>
                        <div>
                            <a href="browse.php" class="btn btn-outline">Reset filters</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <div class="card">
                <div class="flex-between mb-3">
                    <div>
                        <h2 class="section-title">All Projects</h2>
                    </div>
                    <span class="text-muted"><?php echo count($projects); ?> results</span>
                </div>

                <?php if (empty($projects)): ?>
                    <div class="card" style="text-align:center;">
                        <p class="text-muted">No projects found. Try a different filter.</p>
                    </div>
                <?php else: ?>
                    <div class="grid" style="gap:14px;">
                        <?php foreach ($projects as $p): ?>
                            <a href="project.php?id=<?php echo $p['project_id']; ?>" class="card" style="display:block;">
                                <span class="badge badge-blue"><?php echo htmlspecialchars($p['department_name']); ?></span>
                                <h3><?php echo htmlspecialchars($p['title']); ?></h3>
                                <p class="caption" style="margin:10px 0 14px; line-height:1.6;"><?php echo htmlspecialchars($p['abstract']); ?></p>
                                <div class="flex" style="flex-wrap:wrap; gap:12px; color:#64748b; font-size:13px;">
                                    <span><i class="fas fa-calendar-alt"></i> <?php echo date('M Y', strtotime($p['upload_date'])); ?></span>
                                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($p['category_name']); ?></span>
                                    <span><i class="fas fa-eye"></i> <?php echo $p['view_count']; ?> views</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <aside>
            <div class="card">
                <h3 class="section-title">Browse Tips</h3>
                <p class="text-muted">Use filters to narrow projects by department, category, or year. Click a project card to view full details and download content.</p>
            </div>
        </aside>
    </div>
</div>
