<?php
declare(strict_types=1);
require_once __DIR__ . '/../../guards/role.php';
requireRole('admin');
include __DIR__ . '/../../views/admin/Admin_QuanLyHocSinh.html';




