<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function requireRole(string $requiredRole): void
{
    requireLogin();

    $role = $_SESSION['auth']['role'] ?? null;
    if ($role !== $requiredRole) {
        header('HTTP/1.1 403 Forbidden');
        echo '<h2>403 - Bạn không có quyền truy cập trang này.</h2>';
        echo '<p><a href="/PHP_FinalExam/pages/auth/dangnhap.php">Quay về trang đăng nhập</a></p>';
        exit;
    }
}
