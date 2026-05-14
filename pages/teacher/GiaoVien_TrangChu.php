<?php
declare(strict_types=1);
require_once __DIR__ . '/../../guards/role.php';
requireRole('teacher');
include __DIR__ . '/../../views/teacher/GiaoVien_TrangChu.html';




