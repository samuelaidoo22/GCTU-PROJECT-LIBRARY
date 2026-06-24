<?php
// download.php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

if (!isset($_GET['id'])) {
    header("Location: browse.php");
    exit();
}

$project_id = (int)$_GET['id'];

// Fetch project file path
$stmt = $pdo->prepare("SELECT file_path, title FROM projects WHERE project_id = ? AND approval_status = 'approved'");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if ($project && !empty($project['file_path'])) {
    $normalizedFile = trim(str_replace('\\', '/', $project['file_path']));
    $normalizedFile = preg_replace('#^' . preg_quote(UPLOAD_URL, '#') . '/#', '', $normalizedFile);
    $file = resolve_upload_path(basename($normalizedFile));

    $log_stmt = $pdo->prepare("INSERT INTO access_logs (user_id, project_id, access_type) VALUES (?, ?, 'download_full')");
    $log_stmt->execute([$_SESSION['user_id'], $project_id]);
    safe_file_download($file, preg_replace('/[^A-Za-z0-9_-]+/', '_', $project['title']) . '.pdf');
} else {
    http_response_code(404);
    echo 'File not found or access denied.';
}
?>
