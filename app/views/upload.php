<div class="page-banner">
    <div class="container">
        <div style="margin-bottom:10px;">
            <a href="javascript:history.back()" style="font-size:13px; color:#fff; text-decoration:none;"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <div class="breadcrumb"><a href="index.php">Home</a> &rsaquo; Upload Project</div>
        <h1>Submit Project</h1>
        <p>Fill in the form below to submit your research project for review.</p>
    </div>
</div>

<div class="container" style="padding:32px 0 64px;">
    <div class="grid" style="grid-template-columns: 1fr 280px; gap:24px; align-items:start;">
        <div class="card">
            <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div><?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="section-title">Project Details</div>

                <div class="form-group">
                    <label class="form-label">Full Project Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="Enter the complete project title" required value="<?php echo htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE); ?>">
                </div>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Department *</label>
                        <select name="department_id" class="form-control" required>
                            <option value="" disabled>Select department</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?php echo $d['department_id']; ?>" <?php echo ($department_id === (int)$d['department_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['department_code'] . ' — ' . $d['department_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="" disabled>Select category</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['category_id']; ?>" <?php echo ($category_id === (int)$c['category_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Abstract *</label>
                    <textarea name="abstract" class="form-control" rows="5" placeholder="Provide a detailed summary of your research…" required><?php echo htmlspecialchars($abstract, ENT_QUOTES | ENT_SUBSTITUTE); ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Keywords</label>
                    <input type="text" name="keywords" class="form-control" placeholder="e.g. AI, Machine Learning, Ghana" value="<?php echo htmlspecialchars($keywords, ENT_QUOTES | ENT_SUBSTITUTE); ?>">
                </div>

                <div class="section-title">Author(s)</div>
                <div class="form-group" id="author-list">
                    <?php foreach ($authors as $idx => $author): ?>
                        <input type="text" name="authors[]" class="form-control" placeholder="Full Name of Author <?php echo $idx + 1; ?><?php echo $idx === 0 ? '' : ' (optional)'; ?>" value="<?php echo htmlspecialchars($author, ENT_QUOTES | ENT_SUBSTITUTE); ?>" <?php echo $idx === 0 ? 'required' : ''; ?> style="margin-bottom:12px;">
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-author" class="btn btn-outline btn-sm" style="margin-bottom:20px;">
                    <i class="fas fa-plus"></i> Add Author
                </button>

                <div class="section-title">Project File</div>
                <div class="form-group">
                    <label class="form-label">Upload PDF *</label>
                    <input type="file" name="project_file" class="form-control" accept=".pdf" required>
                    <p class="caption" style="margin-top:8px;">PDF only. Maximum file size: 20MB.</p>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">Submit for Review</button>
            </form>
        </div>

        <aside>
            <div class="card">
                <h3 class="section-title">Submission Guide</h3>
                <ul class="caption" style="list-style:none; padding:0; color:#475569; line-height:1.9;">
                    <li><strong>PDF Format Only</strong><br>Export your document as a standard PDF, max 20MB.</li>
                    <li><strong>Detailed Abstract</strong><br>A clear summary improves search relevance.</li>
                    <li><strong>Use Keywords</strong><br>Add relevant keywords separated by commas.</li>
                    <li><strong>Review Process</strong><br>Submitted projects are reviewed by department staff.</li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<script>
document.getElementById('add-author').addEventListener('click', function() {
    const list = document.getElementById('author-list');
    const count = list.querySelectorAll('input').length + 1;
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'authors[]';
    input.className = 'form-control';
    input.placeholder = 'Full Name of Author ' + count + ' (optional)';
    input.style.marginBottom = '12px';
    list.appendChild(input);
});
</script>
