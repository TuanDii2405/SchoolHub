<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Student\StudentController;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', [AuthController::class, 'redirectHome']);

// Auth
Route::get('/login',        [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',       [AuthController::class, 'login']);
Route::get('/logout',       [AuthController::class, 'logout'])->name('logout');
Route::get('/dang-ky',      [AuthController::class, 'showRegister'])->name('register');
Route::post('/dang-ky',     [AuthController::class, 'register']);
Route::get('/doi-mat-khau', [AuthController::class, 'showDoiMatKhau'])->name('doi-mat-khau');
Route::post('/doi-mat-khau',[AuthController::class, 'doiMatKhau']);

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth.check', 'role:admin'])->group(function () {
    Route::get('/',           [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/thong-bao',         [AdminController::class, 'thongBaoStore'])->name('thong-bao.store');
    Route::put('/thong-bao/{id}',     [AdminController::class, 'thongBaoUpdate'])->name('thong-bao.update');
    Route::delete('/thong-bao/{id}',  [AdminController::class, 'thongBaoDestroy'])->name('thong-bao.destroy');
    Route::get('/hoc-sinh',          [AdminController::class, 'hocSinh'])->name('hoc-sinh');
    Route::post('/hoc-sinh',         [AdminController::class, 'hocSinhStore'])->name('hoc-sinh.store');
    Route::put('/hoc-sinh/{id}',     [AdminController::class, 'hocSinhUpdate'])->name('hoc-sinh.update');
    Route::delete('/hoc-sinh/{id}',  [AdminController::class, 'hocSinhDestroy'])->name('hoc-sinh.destroy');
    Route::get('/giao-vien',         [AdminController::class, 'giaoVien'])->name('giao-vien');
    Route::post('/giao-vien',        [AdminController::class, 'giaoVienStore'])->name('giao-vien.store');
    Route::put('/giao-vien/{id}',    [AdminController::class, 'giaoVienUpdate'])->name('giao-vien.update');
    Route::delete('/giao-vien/{id}', [AdminController::class, 'giaoVienDestroy'])->name('giao-vien.destroy');
    Route::get('/khoi-lop',          [AdminController::class, 'khoiLop'])->name('khoi-lop');
    Route::post('/khoi-lop',         [AdminController::class, 'khoiLopStore'])->name('khoi-lop.store');
    Route::put('/khoi-lop/{id}',     [AdminController::class, 'khoiLopUpdate'])->name('khoi-lop.update');
    Route::delete('/khoi-lop/{id}',  [AdminController::class, 'khoiLopDestroy'])->name('khoi-lop.destroy');
    Route::get('/mon-hoc',           [AdminController::class, 'monHoc'])->name('mon-hoc');
    Route::post('/mon-hoc',          [AdminController::class, 'monHocStore'])->name('mon-hoc.store');
    Route::put('/mon-hoc/{id}',      [AdminController::class, 'monHocUpdate'])->name('mon-hoc.update');
    Route::delete('/mon-hoc/{id}',   [AdminController::class, 'monHocDestroy'])->name('mon-hoc.destroy');
    Route::get('/lop-hoc',          [AdminController::class, 'lopHoc'])->name('lop-hoc');
    Route::post('/lop-hoc',                              [AdminController::class, 'lopHocStore'])->name('lop-hoc.store');
    Route::put('/lop-hoc/{id}',                          [AdminController::class, 'lopHocUpdate'])->name('lop-hoc.update');
    Route::delete('/lop-hoc/{id}',                       [AdminController::class, 'lopHocDestroy'])->name('lop-hoc.destroy');
    Route::post('/lop-hoc/{id}/them-hoc-sinh',           [AdminController::class, 'lopHocThemHocSinh'])->name('lop-hoc.them-hoc-sinh');
    Route::delete('/lop-hoc/{lopId}/hoc-sinh/{stuId}',   [AdminController::class, 'lopHocXoaHocSinh'])->name('lop-hoc.xoa-hoc-sinh');
    Route::get('/ky-thi',          [AdminController::class, 'kyThi'])->name('ky-thi');
    Route::post('/ky-thi',         [AdminController::class, 'kyThiStore'])->name('ky-thi.store');
    Route::put('/ky-thi/{id}',     [AdminController::class, 'kyThiUpdate'])->name('ky-thi.update');
    Route::delete('/ky-thi/{id}',  [AdminController::class, 'kyThiDestroy'])->name('ky-thi.destroy');
    Route::get('/de-thi',                                   [AdminController::class, 'deThi'])->name('de-thi');
    Route::post('/de-thi',                                  [AdminController::class, 'deThiStore'])->name('de-thi.store');
    Route::put('/de-thi/{id}',                              [AdminController::class, 'deThiUpdate'])->name('de-thi.update');
    Route::delete('/de-thi/{id}',                           [AdminController::class, 'deThiDestroy'])->name('de-thi.destroy');
    Route::get('/de-thi/{id}/cau-hoi',                      [AdminController::class, 'deThiCauHoi'])->name('de-thi.cau-hoi');
    Route::post('/de-thi/{id}/cau-hoi',                     [AdminController::class, 'deThiCauHoiStore'])->name('de-thi.cau-hoi.store');
    Route::delete('/de-thi/{id}/cau-hoi/{chiTietId}',       [AdminController::class, 'deThiCauHoiDestroy'])->name('de-thi.cau-hoi.destroy');
    Route::get('/de-thi/{id}/dem-cau-hoi',                  [AdminController::class, 'deThiDemCauHoi'])->name('de-thi.dem-cau-hoi');
    Route::get('/chu-de',          [AdminController::class, 'chuDe'])->name('chu-de');
    Route::post('/chu-de',         [AdminController::class, 'chuDeStore'])->name('chu-de.store');
    Route::put('/chu-de/{id}',     [AdminController::class, 'chuDeUpdate'])->name('chu-de.update');
    Route::delete('/chu-de/{id}',  [AdminController::class, 'chuDeDestroy'])->name('chu-de.destroy');
    Route::get('/cau-hoi',                      [AdminController::class, 'cauHoi'])->name('cau-hoi');
    Route::post('/cau-hoi/4pa',                 [AdminController::class, 'cauHoi4PAStore'])->name('cau-hoi.4pa.store');
    Route::put('/cau-hoi/4pa/{id}',             [AdminController::class, 'cauHoi4PAUpdate'])->name('cau-hoi.4pa.update');
    Route::delete('/cau-hoi/4pa/{id}',          [AdminController::class, 'cauHoi4PADestroy'])->name('cau-hoi.4pa.destroy');
    Route::post('/cau-hoi/dung-sai',            [AdminController::class, 'cauHoiDSStore'])->name('cau-hoi.ds.store');
    Route::put('/cau-hoi/dung-sai/{id}',        [AdminController::class, 'cauHoiDSUpdate'])->name('cau-hoi.ds.update');
    Route::delete('/cau-hoi/dung-sai/{id}',     [AdminController::class, 'cauHoiDSDestroy'])->name('cau-hoi.ds.destroy');
    Route::post('/cau-hoi/tra-loi-ngan',        [AdminController::class, 'cauHoiNganStore'])->name('cau-hoi.ngan.store');
    Route::put('/cau-hoi/tra-loi-ngan/{id}',    [AdminController::class, 'cauHoiNganUpdate'])->name('cau-hoi.ngan.update');
    Route::delete('/cau-hoi/tra-loi-ngan/{id}', [AdminController::class, 'cauHoiNganDestroy'])->name('cau-hoi.ngan.destroy');
    Route::post('/impersonate/{id}',            [AdminController::class, 'impersonate'])->name('impersonate');
});

Route::get('/admin/impersonate-back', [AdminController::class, 'impersonateBack'])
    ->name('admin.impersonate.back')
    ->middleware('auth.check');

// Teacher
Route::prefix('giao-vien')->name('teacher.')->middleware(['auth.check', 'role:teacher'])->group(function () {
    Route::get('/',                    [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::post('/thong-bao',          [TeacherController::class, 'thongBaoStore'])->name('thong-bao.store');
    Route::put('/thong-bao/{id}',      [TeacherController::class, 'thongBaoUpdate'])->name('thong-bao.update');
    Route::delete('/thong-bao/{id}',   [TeacherController::class, 'thongBaoDestroy'])->name('thong-bao.destroy');
    Route::get('/lop-hoc',             [TeacherController::class, 'lopHoc'])->name('lop-hoc');
    Route::get('/de-thi',                                   [TeacherController::class, 'deThi'])->name('de-thi');
    Route::post('/de-thi',                                  [TeacherController::class, 'deThiStore'])->name('de-thi.store');
    Route::put('/de-thi/{id}',                              [TeacherController::class, 'deThiUpdate'])->name('de-thi.update');
    Route::delete('/de-thi/{id}',                           [TeacherController::class, 'deThiDestroy'])->name('de-thi.destroy');
    Route::get('/de-thi/{id}/cau-hoi',                      [TeacherController::class, 'deThiCauHoi'])->name('de-thi.cau-hoi');
    Route::post('/de-thi/{id}/cau-hoi',                     [TeacherController::class, 'deThiCauHoiStore'])->name('de-thi.cau-hoi.store');
    Route::delete('/de-thi/{id}/cau-hoi/{chiTietId}',       [TeacherController::class, 'deThiCauHoiDestroy'])->name('de-thi.cau-hoi.destroy');
    Route::get('/de-thi/{id}/dem-cau-hoi',                  [TeacherController::class, 'deThiDemCauHoi'])->name('de-thi.dem-cau-hoi');
    Route::get('/4pa',                    [TeacherController::class, 'tracNghiem4PA'])->name('4pa');
    Route::post('/4pa',                   [TeacherController::class, 'cauHoi4PAStore'])->name('4pa.store');
    Route::put('/4pa/{id}',               [TeacherController::class, 'cauHoi4PAUpdate'])->name('4pa.update');
    Route::delete('/4pa/{id}',            [TeacherController::class, 'cauHoi4PADestroy'])->name('4pa.destroy');
    Route::get('/dung-sai',               [TeacherController::class, 'tracNghiemDungSai'])->name('dung-sai');
    Route::post('/dung-sai',              [TeacherController::class, 'cauHoiDSStore'])->name('dung-sai.store');
    Route::put('/dung-sai/{id}',          [TeacherController::class, 'cauHoiDSUpdate'])->name('dung-sai.update');
    Route::delete('/dung-sai/{id}',       [TeacherController::class, 'cauHoiDSDestroy'])->name('dung-sai.destroy');
    Route::get('/tra-loi-ngan',           [TeacherController::class, 'tracNghiemTraLoiNgan'])->name('tra-loi-ngan');
    Route::post('/tra-loi-ngan',          [TeacherController::class, 'cauHoiNganStore'])->name('tra-loi-ngan.store');
    Route::put('/tra-loi-ngan/{id}',      [TeacherController::class, 'cauHoiNganUpdate'])->name('tra-loi-ngan.update');
    Route::delete('/tra-loi-ngan/{id}',   [TeacherController::class, 'cauHoiNganDestroy'])->name('tra-loi-ngan.destroy');
    Route::get('/chu-de',            [TeacherController::class, 'chuDe'])->name('chu-de');
    Route::post('/chu-de',           [TeacherController::class, 'chuDeStore'])->name('chu-de.store');
    Route::put('/chu-de/{id}',       [TeacherController::class, 'chuDeUpdate'])->name('chu-de.update');
    Route::delete('/chu-de/{id}',    [TeacherController::class, 'chuDeDestroy'])->name('chu-de.destroy');
    Route::get('/diem-danh',         [TeacherController::class, 'diemDanh'])->name('diem-danh');
    Route::post('/diem-danh',        [TeacherController::class, 'diemDanhStore'])->name('diem-danh.store');
    Route::put('/diem-danh/{id}',    [TeacherController::class, 'diemDanhUpdate'])->name('diem-danh.update');
    Route::delete('/diem-danh/{id}', [TeacherController::class, 'diemDanhDestroy'])->name('diem-danh.destroy');
    Route::get('/ky-thi',            [TeacherController::class, 'kyThi'])->name('ky-thi');
    Route::post('/ky-thi',           [TeacherController::class, 'kyThiStore'])->name('ky-thi.store');
    Route::put('/ky-thi/{id}',       [TeacherController::class, 'kyThiUpdate'])->name('ky-thi.update');
    Route::delete('/ky-thi/{id}',    [TeacherController::class, 'kyThiDestroy'])->name('ky-thi.destroy');
    Route::get('/diem-hoc-sinh',     [TeacherController::class, 'diemHocSinh'])->name('diem-hoc-sinh');
    Route::get('/don-xin-nghi',      [TeacherController::class, 'donXinNghi'])->name('don-xin-nghi');
    Route::put('/don-xin-nghi/{id}', [TeacherController::class, 'donXinNghiUpdate'])->name('don-xin-nghi.update');
    Route::get('/thong-tin',         [TeacherController::class, 'thongTin'])->name('thong-tin');
    Route::put('/thong-tin',         [TeacherController::class, 'thongTinUpdate'])->name('thong-tin.update');
    Route::get('/doi-mat-khau',      [TeacherController::class, 'doiMatKhau'])->name('doi-mat-khau');
    Route::post('/doi-mat-khau',     [TeacherController::class, 'doiMatKhauUpdate'])->name('doi-mat-khau.update');
});

// Student
Route::prefix('hoc-sinh')->name('student.')->middleware(['auth.check', 'role:student'])->group(function () {
    Route::get('/',            [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/lop-hoc',     [StudentController::class, 'lopHoc'])->name('lop-hoc');
    Route::get('/ky-thi',      [StudentController::class, 'kyThi'])->name('ky-thi');
    Route::get('/lich-su-bai', [StudentController::class, 'lichSuLamBai'])->name('lich-su-bai');
    Route::get('/diem-danh',   [StudentController::class, 'diemDanh'])->name('diem-danh');
    Route::get('/thong-tin',   [StudentController::class, 'thongTin'])->name('thong-tin');
    Route::get('/xep-hang',    [StudentController::class, 'xepHang'])->name('xep-hang');
});
