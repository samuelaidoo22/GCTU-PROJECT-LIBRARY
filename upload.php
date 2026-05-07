<?php
require_once 'config/db.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$current_page = 'upload'; $page_title = 'Upload Project';
$error = ''; $success = '';
$categories  = $pdo->query("SELECT * FROM categories")->fetchAll();
$departments = $pdo->query("SELECT * FROM departments")->fetchAll();
if ($_SERVER['REQUEST_METHOD']=='POST') {
    if (isset($_FILES['project_file']) && $_FILES['project_file']['error']==0) {
        $ext = strtolower(pathinfo($_FILES['project_file']['name'], PATHINFO_EXTENSION));
        if ($ext==='pdf') {
            $fname = 'GCTU_'.uniqid().'.pdf';
            $path  = 'uploads/'.$fname;
            if (move_uploaded_file($_FILES['project_file']['tmp_name'], $path)) {
                try {
                    $pdo->beginTransaction();
                    $pdo->prepare("INSERT INTO projects (title,abstract,keywords,category_id,department_id,file_path,upload_date,uploader_id,approval_status) VALUES (?,?,?,?,?,?,CURDATE(),?,'pending')")
                        ->execute([trim($_POST['title']),trim($_POST['abstract']),trim($_POST['keywords']),(int)$_POST['category_id'],(int)$_POST['department_id'],$path,$_SESSION['user_id']]);
                    $pid = $pdo->lastInsertId();
                    $as  = $pdo->prepare("INSERT INTO authors (project_id,author_name) VALUES (?,?)");
                    foreach ($_POST['authors'] as $a) { if (trim($a)) $as->execute([$pid,trim($a)]); }
                    $pdo->commit();
                    $success = "Project submitted successfully! It is pending department review.";
                } catch (Exception $e) { $pdo->rollBack(); $error = "Database error. Try again."; }
            } else { $error = "Could not save file. Check upload folder permissions."; }
        } else { $error = "Only PDF files are accepted."; }
    } else { $error = "Please attach a PDF file."; }
}
require_once 'includes/header.php';
?>
<div class="page-banner">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a> &rsaquo; Upload Project</div>
        <h1>Submit Project</h1>
        <p>Fill in the form below to submit your research project for review.</p>
    </div>
</div>
<div class="container" style="padding:32px 24px 64px;">
    <div class="responsive-grid" style="display:grid; grid-template-columns:1fr 280px; gap:24px; align-items:start;">

        <!-- Form -->
        <div class="card">
            <?php if ($error):   ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div><?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#004AAD; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #e2e8f0;">Project Details</div>

                <div class="form-group">
                    <label class="form-label">Full Project Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="Enter the complete project title" required>
                </div>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Department *</label>
                        <select name="department_id" class="form-control" required>
                            <option value="" disabled selected>Select department</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?php echo $d['department_id']; ?>" <?php echo (isset($_SESSION['dept_id'])&&$_SESSION['dept_id']==$d['department_id'])?'selected':''; ?>>
                                    <?php echo $d['department_code'].' — '.$d['department_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="" disabled selected>Select category</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Abstract *</label>
                    <textarea name="abstract" class="form-control" rows="5" placeholder="Provide a detailed summary of your research…" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Keywords</label>
                    <input type="text" name="keywords" class="form-control" placeholder="e.g. AI, Machine Learning, Ghana (comma-separated)">
                </div>

                <div style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#004AAD; margin:20px 0 14px; padding-bottom:10px; border-bottom:1px solid #e2e8f0;">Author(s)</div>
                <div id="author-list" class="form-group">
                    <input type="text" name="authors[]" class="form-control" placeholder="Full Name of Author 1" required style="margin-bottom:8px;">
                    <input type="text" name="authors[]" class="form-control" placeholder="Full Name of Author 2 (optional)">
                </div>
                <button type="button" id="add-author" class="btn btn-outline btn-sm mb-2">
                    <i class="fas fa-plus"></i> Add Author
                </button>

                <div style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#004AAD; margin:20px 0 14px; padding-bottom:10px; border-bottom:1px solid #e2e8f0;">Project File</div>
                <div class="form-group">
                    <label class="form-label">Upload PDF *</label>
                    <input type="file" name="project_file" class="form-control" accept=".pdf" required id="file-input">
                    <p style="font-size:12px; color:#64748b; margin-top:6px;"><i class="fas fa-info-circle"></i> PDF format only. Maximum file size: 20MB.</p>
                </div>

                <button type="submit" class="btn btn-primary" style="padding:12px 32px;">
                    <i class="fas fa-paper-plane"></i> Submit for Review
                </button>
            </form>
        </div>

        <!-- Guide -->
        <aside>
            <div class="card">
                <div style="font-size:15px; font-weight:700; color:#004AAD; margin-bottom:16px;"><i class="fas fa-info-circle"></i> Submission Guide</div>
                <ul style="list-style:none; font-size:13px; color:#475569; line-height:1.8;">
                    <li style="margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">
                        <strong style="display:block; color:#1e293b;">PDF Format Only</strong>
                        Export your document as a standard PDF, max 20MB.
                    </li>
                    <li style="margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">
                        <strong style="display:block; color:#1e293b;">Detailed Abstract</strong>
                        A clear abstract improves discoverability in searches.
                    </li>
                    <li style="margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">
                        <strong style="display:block; color:#1e293b;">Use Keywords</strong>
                        Add 4–6 relevant keywords separated by commas.
                    </li>
                    <li>
                        <strong style="display:block; color:#1e293b;">Review Process</strong>
                        Submitted projects are reviewed by department heads within 1–3 working days.
                    </li>
                </ul>
            </div>
        </aside>
    </div>
</div>
<script>
document.getElementById('add-author').addEventListener('click', function() {
    const n = document.querySelectorAll('#author-list input').length + 1;
    const i = document.createElement('input');
    i.type='text'; i.name='authors[]'; i.className='form-control';
    i.placeholder='Full Name of Author '+n+' (optional)'; i.style.marginTop='8px';
    document.getElementById('author-list').appendChild(i);
});
</script>
<?php require_once 'includes/footer.php'; ?>
