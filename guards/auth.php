<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/session.php';

function requireLogin(): void
{
    if (empty($_SESSION['auth'])) {
        setFlash('error', 'Vui lòng đăng nhập để tiếp tục.');
        header('Location: /PHP_FinalExam/pages/auth/dangnhap.php');
        exit;
    }
}

function currentUser(): ?array
{
    return $_SESSION['auth'] ?? null;
}
