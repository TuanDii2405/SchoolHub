<?php
declare(strict_types=1);

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (!is_string($uriPath) || $uriPath === '') {
    $uriPath = '/';
}

$documentRoot = rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\');
if ($documentRoot === '') {
    $documentRoot = __DIR__ . '/..';
}

$requestedFile = $documentRoot . $uriPath;
if (is_file($requestedFile)) {
    return false;
}

$projectBase = '/PHP_FinalExam';
if (strpos($uriPath, $projectBase . '/') !== 0) {
    return false;
}

$relativePath = substr($uriPath, strlen($projectBase) + 1);
if (!is_string($relativePath) || $relativePath === '') {
    return false;
}

$targetRelative = null;
if ($relativePath === 'dangnhap.php') {
    $targetRelative = 'pages/auth/dangnhap.php';
} elseif (preg_match('/^Admin_.+\.php$/', $relativePath) === 1) {
    $targetRelative = 'pages/admin/' . $relativePath;
} elseif (preg_match('/^GiaoVien_.+\.php$/', $relativePath) === 1) {
    $targetRelative = 'pages/teacher/' . $relativePath;
} elseif (preg_match('/^HocSinh_.+\.php$/', $relativePath) === 1) {
    $targetRelative = 'pages/student/' . $relativePath;
}

if ($targetRelative === null) {
    return false;
}

$targetFile = __DIR__ . '/' . $targetRelative;
if (!is_file($targetFile)) {
    http_response_code(404);
    echo 'Not Found';
    return true;
}

require $targetFile;
return true;
