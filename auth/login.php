<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/session.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /PHP_FinalExam/pages/auth/dangnhap.php');
    exit;
}

$loginInput = strtolower(trim((string)($_POST['username'] ?? '')));
$passwordInput = trim((string)($_POST['password'] ?? ''));

if ($loginInput === '' || $passwordInput === '') {
    setFlash('error', 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.');
    header('Location: /PHP_FinalExam/pages/auth/dangnhap.php');
    exit;
}

try {
    $pdo = createDatabaseConnection();

    $roleAlias = [
        'admin' => 'admin',
        'giaovien' => 'teacher',
        'hocsinh' => 'student',
    ];

    $user = null;
    if (isset($roleAlias[$loginInput])) {
        $stmt = $pdo->prepare(
            'SELECT ID_User, Pass_User, PhanQuyen_User, HoVaTen_User, TrangThaiHoatDong_User
             FROM `User`
             WHERE PhanQuyen_User = :role
             ORDER BY ID_User ASC
             LIMIT 1'
        );
        $stmt->execute(['role' => $roleAlias[$loginInput]]);
        $user = $stmt->fetch();
    } else {
        $stmt = $pdo->prepare(
            'SELECT ID_User, Pass_User, PhanQuyen_User, HoVaTen_User, TrangThaiHoatDong_User
             FROM `User`
             WHERE LOWER(EmailCaNhan_User) = :login OR SoDienThoai_User = :phone
             ORDER BY ID_User ASC
             LIMIT 1'
        );
        $stmt->execute(['login' => $loginInput, 'phone' => $loginInput]);
        $user = $stmt->fetch();
    }

    if (!$user) {
        setFlash('error', 'Sai tài khoản hoặc mật khẩu.');
        header('Location: /PHP_FinalExam/pages/auth/dangnhap.php');
        exit;
    }

    if (($user['TrangThaiHoatDong_User'] ?? '') !== 'active') {
        setFlash('error', 'Tài khoản hiện không hoạt động.');
        header('Location: /PHP_FinalExam/pages/auth/dangnhap.php');
        exit;
    }

    $storedPassword = (string)$user['Pass_User'];
    $isValidPassword = false;

    if (password_get_info($storedPassword)['algo'] !== null) {
        $isValidPassword = password_verify($passwordInput, $storedPassword);
    } else {
        $isValidPassword = hash_equals($storedPassword, $passwordInput);
    }

    if (!$isValidPassword) {
        setFlash('error', 'Sai tài khoản hoặc mật khẩu.');
        header('Location: /PHP_FinalExam/pages/auth/dangnhap.php');
        exit;
    }

    $_SESSION['auth'] = [
        'id' => (int)$user['ID_User'],
        'name' => (string)$user['HoVaTen_User'],
        'role' => (string)$user['PhanQuyen_User'],
        'logged_at' => date('c'),
    ];

    $redirectByRole = [
        'admin' => '/PHP_FinalExam/pages/admin/Admin_TrangChu.php',
        'teacher' => '/PHP_FinalExam/pages/teacher/GiaoVien_TrangChu.php',
        'student' => '/PHP_FinalExam/pages/student/HocSinh_TrangChu.php',
    ];

    $target = $redirectByRole[$_SESSION['auth']['role']] ?? '/PHP_FinalExam/pages/auth/dangnhap.php';
    header('Location: ' . $target);
    exit;
} catch (Throwable $e) {
    setFlash('error', 'Hệ thống tạm thời gián đoạn kết nối CSDL.');
    header('Location: /PHP_FinalExam/pages/auth/dangnhap.php');
    exit;
}
