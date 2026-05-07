<?php
// download.php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: browse.php");
    exit();
}

$project_id = (int)$_GET['id'];

// Fetch project file path
$stmt = $pdo->prepare("SELECT file_path, title FROM projects WHERE project_id = ? AND approval_status = 'approved'");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if ($project && !empty($project['file_path']) && file_exists($project['file_path'])) {
    // Log download
    $log_stmt = $pdo->prepare("INSERT INTO access_logs (user_id, project_id, access_type) VALUES (?, ?, 'download_full')");
    $log_stmt->execute([$_SESSION['user_id'], $project_id]);

    // Serve file
    $file = $project['file_path'];
    $filename = str_replace(' ', '_', $project['title']) . '.pdf';

    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    
    readfile($file);
    exit;
} else {
    echo "File not found or access denied.";
}
?>
