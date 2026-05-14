<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/session.php';

if (!empty($_SESSION['auth']['role'])) {
    $role = $_SESSION['auth']['role'];
    if ($role === 'admin') {
        header('Location: /PHP_FinalExam/pages/admin/Admin_TrangChu.php');
        exit;
    }
    if ($role === 'teacher') {
        header('Location: /PHP_FinalExam/pages/teacher/GiaoVien_TrangChu.php');
        exit;
    }
    if ($role === 'student') {
        header('Location: /PHP_FinalExam/pages/student/HocSinh_TrangChu.php');
        exit;
    }
}

header('Location: /PHP_FinalExam/pages/auth/dangnhap.php');
exit;
