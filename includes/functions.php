<?php
/**
 * Shared utility helpers.
 */

function h($value) {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function safe_redirect($url) {
    $url = str_replace(["\r", "\n"], '', $url);
    header('Location: ' . $url);
    exit();
}

function filter_int($value, $default = 0) {
    $value = filter_var($value, FILTER_VALIDATE_INT);
    return $value === false ? $default : $value;
}

function request_get($key, $default = '') {
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

function request_post($key, $default = '') {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

function request_array($key, array $default = []): array {
    return isset($_REQUEST[$key]) && is_array($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
}

function old($key, $default = '') {
    return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $default;
}

function normalize_keywords(string $keywords): string {
    $parts = array_filter(array_map('trim', explode(',', $keywords)), 'strlen');
    $parts = array_unique($parts);
    return implode(', ', $parts);
}

function normalize_authors(array $authors): array {
    $result = [];
    foreach ($authors as $author) {
        $author = trim($author);
        if ($author !== '') {
            $result[] = preg_replace('/\s+/', ' ', $author);
        }
    }
    return $result;
}

function validate_pdf_upload(array $file): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        return [false, 'Invalid upload request.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, 'File upload failed.'];
    }

    if ($file['size'] > 20 * 1024 * 1024) {
        return [false, 'File size exceeds the 20MB limit.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        return [false, 'Only PDF files are accepted.'];
    }

    $mime = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
    } elseif (function_exists('mime_content_type')) {
        $mime = mime_content_type($file['tmp_name']);
    } else {
        $handle = fopen($file['tmp_name'], 'rb');
        $header = $handle ? fread($handle, 4) : '';
        if ($handle) {
            fclose($handle);
        }
        if ($header !== '%PDF') {
            return [false, 'Only genuine PDF files are accepted.'];
        }
        return [true, ''];
    }

    if ($mime !== 'application/pdf' && $mime !== 'application/x-pdf') {
        return [false, 'Only genuine PDF files are accepted.'];
    }

    return [true, ''];
}

function generate_upload_filename(string $prefix = 'GCTU_'): string {
    return $prefix . bin2hex(random_bytes(16)) . '.pdf';
}

function resolve_upload_path(string $filename): string {
    return UPLOAD_DIR . '/' . basename($filename);
}

function ensure_upload_directory(): void {
    if (!defined('UPLOAD_DIR')) {
        throw new RuntimeException('UPLOAD_DIR is not configured.');
    }

    $path = UPLOAD_DIR;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function safe_file_download(string $filePath, string $downloadName): void {
    if (!file_exists($filePath)) {
        http_response_code(404);
        echo 'File not found.';
        exit();
    }

    $realUploadDir = realpath(UPLOAD_DIR);
    if ($realUploadDir === false) {
        http_response_code(500);
        echo 'Download directory is unavailable.';
        exit();
    }

    $realPath = realpath($filePath);
    if ($realPath === false || strpos($realPath, $realUploadDir) !== 0) {
        http_response_code(403);
        echo 'Access denied.';
        exit();
    }

    if (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . rawurlencode(basename($downloadName)) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($realPath));
    readfile($realPath);
    exit();
}
