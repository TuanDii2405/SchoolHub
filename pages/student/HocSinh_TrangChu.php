<?php
declare(strict_types=1);
require_once __DIR__ . '/../../guards/role.php';
requireRole('student');
include __DIR__ . '/../../views/student/HocSinh_TrangChu.html';




