-- =========================================================
-- Database: school_exam_db
-- Muc tieu: He thong thi truc tuyen (admin, teacher, student)
-- Compatible: MySQL 8+ / MariaDB 10.4+ (XAMPP)
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS school_exam_db;
CREATE DATABASE school_exam_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school_exam_db;

-- =========================================================
-- 1) Danh muc co ban
-- =========================================================

CREATE TABLE Khoi_lop (
    ID_KhoiLop INT AUTO_INCREMENT PRIMARY KEY,
    Ten_KhoiLop VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE Mon_Hoc (
    ID_MonHoc INT AUTO_INCREMENT PRIMARY KEY,
    Ten_MonHoc VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE `User` (
    ID_User INT AUTO_INCREMENT PRIMARY KEY,
    Pass_User VARCHAR(255) NOT NULL,
    NgayTaoTaiKhoan_User DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PhanQuyen_User ENUM('admin', 'teacher', 'student') NOT NULL,
    HoVaTen_User VARCHAR(150) NOT NULL,
    TrangThaiHoatDong_User ENUM('active', 'inactive', 'locked') NOT NULL DEFAULT 'active',
    SoDienThoai_User VARCHAR(20) NULL,
    NgayThangNamSinh_User DATE NULL,
    EmailCaNhan_User VARCHAR(150) NULL UNIQUE,
    PhuTrachKhoi_User INT NULL,
    PhuTrachMon_User INT NULL,
    INDEX idx_user_role (PhanQuyen_User),
    CONSTRAINT fk_user_khoi FOREIGN KEY (PhuTrachKhoi_User) REFERENCES Khoi_lop(ID_KhoiLop),
    CONSTRAINT fk_user_mon FOREIGN KEY (PhuTrachMon_User) REFERENCES Mon_Hoc(ID_MonHoc)
) ENGINE=InnoDB;

CREATE TABLE QuanLy (
    ID_QuanLy INT AUTO_INCREMENT PRIMARY KEY,
    ID_User INT NOT NULL UNIQUE,
    CapQuanLy ENUM('super_admin', 'admin') NOT NULL DEFAULT 'admin',
    GhiChu_QuanLy VARCHAR(255) NULL,
    CONSTRAINT fk_quanly_user FOREIGN KEY (ID_User) REFERENCES `User`(ID_User)
) ENGINE=InnoDB;

CREATE TABLE Lop_hoc (
    ID_LopHoc INT AUTO_INCREMENT PRIMARY KEY,
    ID_KhoiLop INT NOT NULL,
    ID_MonHoc INT NOT NULL,
    ID_Teacher INT NOT NULL,
    ID_Student INT NULL,
    TenLopHoc VARCHAR(100) NOT NULL,
    NamHoc VARCHAR(20) NOT NULL,
    UNIQUE KEY uq_lophoc (TenLopHoc, NamHoc, ID_MonHoc),
    CONSTRAINT fk_lophoc_khoi FOREIGN KEY (ID_KhoiLop) REFERENCES Khoi_lop(ID_KhoiLop),
    CONSTRAINT fk_lophoc_mon FOREIGN KEY (ID_MonHoc) REFERENCES Mon_Hoc(ID_MonHoc),
    CONSTRAINT fk_lophoc_teacher FOREIGN KEY (ID_Teacher) REFERENCES `User`(ID_User),
    CONSTRAINT fk_lophoc_student FOREIGN KEY (ID_Student) REFERENCES `User`(ID_User)
) ENGINE=InnoDB;

-- Bo sung de phan lop nhieu-hoc-sinh (khong pha vo cau truc cu)
CREATE TABLE Lop_hoc_ThanhVien (
    ID_LopHoc INT NOT NULL,
    ID_Student INT NOT NULL,
    NgayThamGia DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ID_LopHoc, ID_Student),
    CONSTRAINT fk_lhtv_lophoc FOREIGN KEY (ID_LopHoc) REFERENCES Lop_hoc(ID_LopHoc),
    CONSTRAINT fk_lhtv_student FOREIGN KEY (ID_Student) REFERENCES `User`(ID_User)
) ENGINE=InnoDB;

CREATE TABLE Thong_bao (
    ID_ThongBao INT AUTO_INCREMENT PRIMARY KEY,
    ID_User INT NOT NULL,
    ID_KhoiLop INT NULL,
    ID_MonHoc INT NULL,
    NoiDung_ThongBao TEXT NOT NULL,
    NgayTao_ThongBao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_thongbao_user FOREIGN KEY (ID_User) REFERENCES `User`(ID_User),
    CONSTRAINT fk_thongbao_khoi FOREIGN KEY (ID_KhoiLop) REFERENCES Khoi_lop(ID_KhoiLop),
    CONSTRAINT fk_thongbao_mon FOREIGN KEY (ID_MonHoc) REFERENCES Mon_Hoc(ID_MonHoc)
) ENGINE=InnoDB;

CREATE TABLE Chu_De (
    ID_ChuDe INT AUTO_INCREMENT PRIMARY KEY,
    ID_MonHoc INT NOT NULL,
    ID_KhoiLop INT NOT NULL,
    NoiDung_ChuDe VARCHAR(255) NOT NULL,
    ID_NguoiTao INT NOT NULL,
    CONSTRAINT fk_chude_mon FOREIGN KEY (ID_MonHoc) REFERENCES Mon_Hoc(ID_MonHoc),
    CONSTRAINT fk_chude_khoi FOREIGN KEY (ID_KhoiLop) REFERENCES Khoi_lop(ID_KhoiLop),
    CONSTRAINT fk_chude_nguoitao FOREIGN KEY (ID_NguoiTao) REFERENCES `User`(ID_User)
) ENGINE=InnoDB;

CREATE TABLE Cau_hoi_trac_nghiem_4_phuong_an (
    ID_TracNghiem4PhuongAn INT AUTO_INCREMENT PRIMARY KEY,
    ID_ChuDe INT NOT NULL,
    ID_MonHoc INT NOT NULL,
    ID_KhoiLop INT NOT NULL,
    NoiDungCauHoi_TracNghiem4PhuongAn TEXT NOT NULL,
    NoiDungCauTraLoi1_TracNghiem4PhuongAn VARCHAR(255) NOT NULL,
    NoiDungCauTraLoi2_TracNghiem4PhuongAn VARCHAR(255) NOT NULL,
    NoiDungCauTraLoi3_TracNghiem4PhuongAn VARCHAR(255) NOT NULL,
    NoiDungCauTraLoi4_TracNghiem4PhuongAn VARCHAR(255) NOT NULL,
    DapAn_TracNghiem4PhuongAn CHAR(1) NOT NULL,
    HuongDanGiai_TracNghiem4PhuongAn TEXT NULL,
    CONSTRAINT fk_q4pa_chude FOREIGN KEY (ID_ChuDe) REFERENCES Chu_De(ID_ChuDe),
    CONSTRAINT fk_q4pa_mon FOREIGN KEY (ID_MonHoc) REFERENCES Mon_Hoc(ID_MonHoc),
    CONSTRAINT fk_q4pa_khoi FOREIGN KEY (ID_KhoiLop) REFERENCES Khoi_lop(ID_KhoiLop),
    CONSTRAINT ck_q4pa_dapan CHECK (DapAn_TracNghiem4PhuongAn IN ('A', 'B', 'C', 'D'))
) ENGINE=InnoDB;

CREATE TABLE Cau_hoi_trac_nghiem_dung_sai (
    ID_TracNghiemDungSai INT AUTO_INCREMENT PRIMARY KEY,
    ID_ChuDe INT NOT NULL,
    ID_MonHoc INT NOT NULL,
    ID_KhoiLop INT NOT NULL,
    NoiDungCauHoi_TracNghiemDungSai TEXT NOT NULL,
    NoiDungMenhDe1_TracNghiemDungSai VARCHAR(255) NOT NULL,
    NoiDungMenhDe2_TracNghiemDungSai VARCHAR(255) NOT NULL,
    NoiDungMenhDe3_TracNghiemDungSai VARCHAR(255) NOT NULL,
    NoiDungMenhDe4_TracNghiemDungSai VARCHAR(255) NOT NULL,
    DapAn_TracNghiem4PhuongAn VARCHAR(20) NOT NULL,
    HuongDanGiaiMenhDe1_TracNghiemDungSai TEXT NULL,
    HuongDanGiaiMenhDe2_TracNghiemDungSai TEXT NULL,
    HuongDanGiaiMenhDe3_TracNghiemDungSai TEXT NULL,
    HuongDanGiaiMenhDe4_TracNghiemDungSai TEXT NULL,
    CONSTRAINT fk_qds_chude FOREIGN KEY (ID_ChuDe) REFERENCES Chu_De(ID_ChuDe),
    CONSTRAINT fk_qds_mon FOREIGN KEY (ID_MonHoc) REFERENCES Mon_Hoc(ID_MonHoc),
    CONSTRAINT fk_qds_khoi FOREIGN KEY (ID_KhoiLop) REFERENCES Khoi_lop(ID_KhoiLop)
) ENGINE=InnoDB;

CREATE TABLE Cau_hoi_tra_loi_ngan (
    ID_TracNghiemTraLoiNgan INT AUTO_INCREMENT PRIMARY KEY,
    ID_ChuDe INT NOT NULL,
    ID_MonHoc INT NOT NULL,
    ID_KhoiLop INT NOT NULL,
    NoiDungCauHoi_TracNghiemTraLoiNgan TEXT NOT NULL,
    KiTuThu1CuaDapAn_TracNghiemTraLoiNgan CHAR(1) NOT NULL,
    KiTuThu2CuaDapAn_TracNghiemTraLoiNgan CHAR(1) NOT NULL,
    KiTuThu3CuaDapAn_TracNghiemTraLoiNgan CHAR(1) NOT NULL,
    KiTuThu4CuaDapAn_TracNghiemTraLoiNgan CHAR(1) NOT NULL,
    HuongDanGiai_TracNghiemTraLoiNgan TEXT NULL,
    CONSTRAINT fk_qngan_chude FOREIGN KEY (ID_ChuDe) REFERENCES Chu_De(ID_ChuDe),
    CONSTRAINT fk_qngan_mon FOREIGN KEY (ID_MonHoc) REFERENCES Mon_Hoc(ID_MonHoc),
    CONSTRAINT fk_qngan_khoi FOREIGN KEY (ID_KhoiLop) REFERENCES Khoi_lop(ID_KhoiLop)
) ENGINE=InnoDB;

CREATE TABLE De_Thi (
    ID_MaDeThi INT AUTO_INCREMENT PRIMARY KEY,
    TenDeThi VARCHAR(150) NOT NULL,
    ID_NguoiTao INT NOT NULL,
    ID_MaMon INT NOT NULL,
    ID_MaKhoi INT NOT NULL,
    NgayTao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    MoTa VARCHAR(255) NULL,
    CONSTRAINT fk_dethi_nguoitao FOREIGN KEY (ID_NguoiTao) REFERENCES `User`(ID_User),
    CONSTRAINT fk_dethi_mon FOREIGN KEY (ID_MaMon) REFERENCES Mon_Hoc(ID_MonHoc),
    CONSTRAINT fk_dethi_khoi FOREIGN KEY (ID_MaKhoi) REFERENCES Khoi_lop(ID_KhoiLop)
) ENGINE=InnoDB;

CREATE TABLE De_Thi_Chi_Tiet (
    ID_DeThiChiTiet INT AUTO_INCREMENT PRIMARY KEY,
    ID_MaDeThi INT NOT NULL,
    ID_NguoiTao INT NOT NULL,
    ID_MaMon INT NOT NULL,
    ID_MaKhoi INT NOT NULL,
    ID_TracNghiem4PhuongAn INT NULL,
    ID_TracNghiemDungSai INT NULL,
    ID_TracNghiemTraLoiNgan INT NULL,
    CONSTRAINT fk_detchitiet_dethi FOREIGN KEY (ID_MaDeThi) REFERENCES De_Thi(ID_MaDeThi),
    CONSTRAINT fk_detchitiet_nguoitao FOREIGN KEY (ID_NguoiTao) REFERENCES `User`(ID_User),
    CONSTRAINT fk_detchitiet_mon FOREIGN KEY (ID_MaMon) REFERENCES Mon_Hoc(ID_MonHoc),
    CONSTRAINT fk_detchitiet_khoi FOREIGN KEY (ID_MaKhoi) REFERENCES Khoi_lop(ID_KhoiLop),
    CONSTRAINT fk_detchitiet_4pa FOREIGN KEY (ID_TracNghiem4PhuongAn) REFERENCES Cau_hoi_trac_nghiem_4_phuong_an(ID_TracNghiem4PhuongAn),
    CONSTRAINT fk_detchitiet_ds FOREIGN KEY (ID_TracNghiemDungSai) REFERENCES Cau_hoi_trac_nghiem_dung_sai(ID_TracNghiemDungSai),
    CONSTRAINT fk_detchitiet_ngan FOREIGN KEY (ID_TracNghiemTraLoiNgan) REFERENCES Cau_hoi_tra_loi_ngan(ID_TracNghiemTraLoiNgan)
) ENGINE=InnoDB;

CREATE TABLE Ky_thi (
    ID_KyThi INT AUTO_INCREMENT PRIMARY KEY,
    ID_KhoiLop INT NOT NULL,
    ID_MonHoc INT NOT NULL,
    ID_ChuDe INT NOT NULL,
    ID_LopHoc INT NOT NULL,
    Ten_KyThi VARCHAR(150) NOT NULL,
    MoTa_KyThi VARCHAR(255) NULL,
    ThoiGianLamBai_KyThi INT NOT NULL,
    PhanBoDiemTracNghiem4PhuongAn_KyThi DECIMAL(5,2) NOT NULL,
    PhanBoDiemTracNghiemDungSai_KyThi DECIMAL(5,2) NOT NULL,
    PhanBoDiemTracNghiemTraLoiNgan_KyThi DECIMAL(5,2) NOT NULL,
    SoCauHoiTracNghiem4PhuongAn_KyThi INT NOT NULL,
    SoCauHoiTracNghiemDungSai_KyThi INT NOT NULL,
    SoCauHoiTracNghiemTraLoiNgan_KyThi INT NOT NULL,
    ID_MaDeThi INT NOT NULL,
    ThoiGianBatDau_KyThi DATETIME NULL,
    ThoiGianKetThuc_KyThi DATETIME NULL,
    CONSTRAINT fk_kythi_khoi FOREIGN KEY (ID_KhoiLop) REFERENCES Khoi_lop(ID_KhoiLop),
    CONSTRAINT fk_kythi_mon FOREIGN KEY (ID_MonHoc) REFERENCES Mon_Hoc(ID_MonHoc),
    CONSTRAINT fk_kythi_chude FOREIGN KEY (ID_ChuDe) REFERENCES Chu_De(ID_ChuDe),
    CONSTRAINT fk_kythi_lophoc FOREIGN KEY (ID_LopHoc) REFERENCES Lop_hoc(ID_LopHoc),
    CONSTRAINT fk_kythi_dethi FOREIGN KEY (ID_MaDeThi) REFERENCES De_Thi(ID_MaDeThi)
) ENGINE=InnoDB;

CREATE TABLE Diem_danh (
    ID_DiemDanh INT AUTO_INCREMENT PRIMARY KEY,
    ID_LopHoc INT NOT NULL,
    NgayHoc_DiemDanh DATE NOT NULL,
    ThoiGianBatDau_DiemDanh DATETIME NOT NULL,
    ThoiGianKetThuc_DiemDanh DATETIME NULL,
    ChiTietDiemDanh_DiemDanh TEXT NULL,
    TrangThaiBuoiHoc_DiemDanh ENUM('scheduled', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    CONSTRAINT fk_diemdanh_lophoc FOREIGN KEY (ID_LopHoc) REFERENCES Lop_hoc(ID_LopHoc)
) ENGINE=InnoDB;

CREATE TABLE Don_xin_nghi (
    ID_DonXinNghi INT AUTO_INCREMENT PRIMARY KEY,
    ID_LopHoc INT NOT NULL,
    ID_User INT NOT NULL,
    ID_DiemDanh INT NOT NULL,
    ThoiGianGui_DonXinNghi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    NoiDung_DonXinNghi TEXT NOT NULL,
    TrangThai_DonXinNghi ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    CONSTRAINT fk_donnghi_lophoc FOREIGN KEY (ID_LopHoc) REFERENCES Lop_hoc(ID_LopHoc),
    CONSTRAINT fk_donnghi_user FOREIGN KEY (ID_User) REFERENCES `User`(ID_User),
    CONSTRAINT fk_donnghi_diemdanh FOREIGN KEY (ID_DiemDanh) REFERENCES Diem_danh(ID_DiemDanh)
) ENGINE=InnoDB;

CREATE TABLE Diem_so (
    ID_DiemSo INT AUTO_INCREMENT PRIMARY KEY,
    ID_User INT NOT NULL,
    ID_MaKyThi INT NOT NULL,
    ID_MaDeThi INT NOT NULL,
    DiemPhanTracNghiem4PhuongAn_DiemSo DECIMAL(5,2) NOT NULL,
    DiemPhanTracNghiemDungSai_DiemSo DECIMAL(5,2) NOT NULL,
    DiemPhanTracNghiemTraLoiNgan_DiemSo DECIMAL(5,2) NOT NULL,
    TongDiem_DiemSo DECIMAL(5,2) NOT NULL,
    ThoiGianBatDau_DiemSo DATETIME NOT NULL,
    ThoiGianKetThuc_DiemSo DATETIME NOT NULL,
    ThoiGianLamBai_DiemSo INT NOT NULL,
    LichSuLamBai TEXT NULL,
    CONSTRAINT fk_diemso_user FOREIGN KEY (ID_User) REFERENCES `User`(ID_User),
    CONSTRAINT fk_diemso_kythi FOREIGN KEY (ID_MaKyThi) REFERENCES Ky_thi(ID_KyThi),
    CONSTRAINT fk_diemso_dethi FOREIGN KEY (ID_MaDeThi) REFERENCES De_Thi(ID_MaDeThi)
) ENGINE=InnoDB;

-- =========================================================
-- 2) Seed data mau
-- =========================================================

INSERT INTO Khoi_lop (Ten_KhoiLop)
VALUES ('Khoi 10'), ('Khoi 11'), ('Khoi 12');

INSERT INTO Mon_Hoc (Ten_MonHoc)
VALUES ('Toan'), ('Ngu van'), ('Tieng Anh'), ('Vat ly'), ('Hoa hoc'), ('Sinh hoc');

-- Tai khoan mau dang nhap nhanh
-- admin/admin123 | giaovien/gv123 | hocsinh/hs123
INSERT INTO `User`
(Pass_User, PhanQuyen_User, HoVaTen_User, TrangThaiHoatDong_User, SoDienThoai_User, NgayThangNamSinh_User, EmailCaNhan_User, PhuTrachKhoi_User, PhuTrachMon_User)
VALUES
('admin123', 'admin', 'Nguyen Van Quan Tri', 'active', '0901000001', '1988-04-15', 'admin@school.local', NULL, NULL),
('gv123', 'teacher', 'Tran Thi Minh Chau', 'active', '0901000002', '1990-06-10', 'giaovien@school.local', 3, 1),
('gv123', 'teacher', 'Le Quoc Bao', 'active', '0901000003', '1989-01-22', 'lequocbao@school.local', 2, 3),
('gv123', 'teacher', 'Pham Thu Ha', 'active', '0901000004', '1992-09-30', 'phamthuha@school.local', 1, 2),
('gv123', 'teacher', 'Vo Dinh Nam', 'active', '0901000005', '1991-11-11', 'vodinhnam@school.local', 3, 4),
('gv123', 'teacher', 'Duong Khanh Linh', 'active', '0901000006', '1993-12-01', 'duongkhanhlinh@school.local', 2, 5),
('gv123', 'teacher', 'Bui Gia Huy', 'active', '0901000007', '1994-07-08', 'buigiahuy@school.local', 1, 6),
('hs123', 'student', 'Nguyen Thi A', 'active', '0912000001', '2008-01-10', 'hsa01@school.local', NULL, NULL),
('hs123', 'student', 'Tran Van B', 'active', '0912000002', '2008-02-12', 'hsb02@school.local', NULL, NULL),
('hs123', 'student', 'Le Thi C', 'active', '0912000003', '2008-03-13', 'hsc03@school.local', NULL, NULL),
('hs123', 'student', 'Pham Van D', 'active', '0912000004', '2008-04-14', 'hsd04@school.local', NULL, NULL),
('hs123', 'student', 'Vo Thi E', 'active', '0912000005', '2008-05-15', 'hse05@school.local', NULL, NULL),
('hs123', 'student', 'Dang Van F', 'active', '0912000006', '2008-06-16', 'hsf06@school.local', NULL, NULL),
('hs123', 'student', 'Hoang Thi G', 'active', '0912000007', '2008-07-17', 'hsg07@school.local', NULL, NULL),
('hs123', 'student', 'Do Van H', 'active', '0912000008', '2008-08-18', 'hsh08@school.local', NULL, NULL),
('hs123', 'student', 'Phan Thi I', 'active', '0912000009', '2007-09-19', 'hsi09@school.local', NULL, NULL),
('hs123', 'student', 'Nguyen Van K', 'active', '0912000010', '2007-10-20', 'hsk10@school.local', NULL, NULL),
('hs123', 'student', 'Le Thi L', 'active', '0912000011', '2007-11-21', 'hsl11@school.local', NULL, NULL),
('hs123', 'student', 'Tran Van M', 'active', '0912000012', '2007-12-22', 'hsm12@school.local', NULL, NULL),
('hs123', 'student', 'Vo Thi N', 'active', '0912000013', '2006-01-23', 'hsn13@school.local', NULL, NULL),
('hs123', 'student', 'Pham Van O', 'active', '0912000014', '2006-02-24', 'hso14@school.local', NULL, NULL),
('hs123', 'student', 'Dang Thi P', 'active', '0912000015', '2006-03-25', 'hsp15@school.local', NULL, NULL),
('hs123', 'student', 'Bui Van Q', 'active', '0912000016', '2006-04-26', 'hsq16@school.local', NULL, NULL),
('hs123', 'student', 'Mai Thi R', 'active', '0912000017', '2006-05-27', 'hsr17@school.local', NULL, NULL),
('hs123', 'student', 'Trinh Van S', 'active', '0912000018', '2006-06-28', 'hss18@school.local', NULL, NULL),
('hs123', 'student', 'Nguyen Thi T', 'active', '0912000019', '2008-09-05', 'hst19@school.local', NULL, NULL),
('hs123', 'student', 'Tran Thi U', 'active', '0912000020', '2008-09-15', 'hsu20@school.local', NULL, NULL),
('hs123', 'student', 'Le Van V', 'active', '0912000021', '2008-10-01', 'hsv21@school.local', NULL, NULL),
('hs123', 'student', 'Pham Thi W', 'active', '0912000022', '2008-10-11', 'hsw22@school.local', NULL, NULL),
('hs123', 'student', 'Vo Van X', 'active', '0912000023', '2007-01-09', 'hsx23@school.local', NULL, NULL),
('hs123', 'student', 'Dang Thi Y', 'active', '0912000024', '2007-02-19', 'hsy24@school.local', NULL, NULL),
('hs123', 'student', 'Bui Van Z', 'active', '0912000025', '2007-03-29', 'hsz25@school.local', NULL, NULL),
('hs123', 'student', 'Ho Thi AA', 'active', '0912000026', '2007-04-05', 'hsaa26@school.local', NULL, NULL),
('hs123', 'student', 'Le Van AB', 'active', '0912000027', '2007-05-16', 'hsab27@school.local', NULL, NULL),
('hs123', 'student', 'Tran Thi AC', 'active', '0912000028', '2007-06-26', 'hsac28@school.local', NULL, NULL),
('hs123', 'student', 'Nguyen Van AD', 'active', '0912000029', '2006-07-03', 'hsad29@school.local', NULL, NULL),
('hs123', 'student', 'Pham Thi AE', 'active', '0912000030', '2006-08-13', 'hsae30@school.local', NULL, NULL);

INSERT INTO QuanLy (ID_User, CapQuanLy, GhiChu_QuanLy)
VALUES (1, 'super_admin', 'Tai khoan quan tri tong');

INSERT INTO Lop_hoc
(ID_KhoiLop, ID_MonHoc, ID_Teacher, ID_Student, TenLopHoc, NamHoc)
VALUES
(1, 2, 4, 8, '10A1 Van', '2025-2026'),
(1, 6, 7, 9, '10A1 Sinh', '2025-2026'),
(2, 3, 3, 17, '11A2 Anh', '2025-2026'),
(2, 5, 6, 18, '11A2 Hoa', '2025-2026'),
(3, 1, 2, 26, '12A3 Toan', '2025-2026'),
(3, 4, 5, 27, '12A3 Ly', '2025-2026');

INSERT INTO Lop_hoc_ThanhVien (ID_LopHoc, ID_Student)
VALUES
(1, 8),(1, 9),(1, 10),(1, 11),(1, 12),(1, 13),(1, 14),(1, 15),
(2, 8),(2, 9),(2, 10),(2, 11),(2, 12),(2, 13),(2, 14),(2, 15),
(3, 16),(3, 17),(3, 18),(3, 19),(3, 20),(3, 21),(3, 22),(3, 23),
(4, 16),(4, 17),(4, 18),(4, 19),(4, 20),(4, 21),(4, 22),(4, 23),
(5, 24),(5, 25),(5, 26),(5, 27),(5, 28),(5, 29),(5, 30),(5, 31),
(6, 24),(6, 25),(6, 26),(6, 27),(6, 28),(6, 29),(6, 30),(6, 31);

INSERT INTO Thong_bao (ID_User, ID_KhoiLop, ID_MonHoc, NoiDung_ThongBao, NgayTao_ThongBao)
VALUES
(1, NULL, NULL, 'Chao mung hoc ky II nam hoc 2025-2026. Tat ca lop hoc hoc truc tuyen on dinh.', '2026-01-03 07:30:00'),
(2, 3, 1, 'Khoi 12 on tap chuyen de Ham so va ung dung vao thu 2.', '2026-01-05 08:00:00'),
(3, 2, 3, 'Lop 11A2 Anh co bai kiem tra tu vung vao tuan sau.', '2026-01-06 09:20:00'),
(4, 1, 2, 'Hoc sinh 10A1 nop bai phan tich doan van truoc 20:00 Chu Nhat.', '2026-01-07 10:15:00'),
(5, 3, 4, 'Buoi hoc Vat ly bo sung he dao dong dien ra toi Thu 5.', '2026-01-08 14:10:00'),
(6, 2, 5, 'Phong thi ao da mo de hoc sinh 11A2 luyen de Hoa hoc.', '2026-01-09 16:40:00');

INSERT INTO Chu_De (ID_MonHoc, ID_KhoiLop, NoiDung_ChuDe, ID_NguoiTao)
VALUES
(1, 3, 'Ham so va ung dung', 2),
(1, 3, 'Mu logarit', 2),
(4, 3, 'Dao dong co', 5),
(3, 2, 'Reading comprehension', 3),
(5, 2, 'Este - Lipit', 6),
(2, 1, 'Van hoc trung dai', 4),
(6, 1, 'Te bao hoc', 7),
(3, 2, 'Grammar in use', 3);

INSERT INTO Cau_hoi_trac_nghiem_4_phuong_an
(ID_ChuDe, ID_MonHoc, ID_KhoiLop, NoiDungCauHoi_TracNghiem4PhuongAn,
 NoiDungCauTraLoi1_TracNghiem4PhuongAn, NoiDungCauTraLoi2_TracNghiem4PhuongAn,
 NoiDungCauTraLoi3_TracNghiem4PhuongAn, NoiDungCauTraLoi4_TracNghiem4PhuongAn,
 DapAn_TracNghiem4PhuongAn, HuongDanGiai_TracNghiem4PhuongAn)
VALUES
(1,1,3,'Ham so y = x^2 co dinh tai diem nao?','(0,0)','(1,0)','(0,1)','(-1,1)','A','Dinh parabol y=x^2 la goc toa do.'),
(1,1,3,'Dao ham cua y = 3x^2 la','6x','3x','x^2','2x','A','Ap dung cong thuc dao ham x^n.'),
(2,1,3,'log_a(a^k) bang','k','a*k','k/a','a^k','A','Theo dinh nghia logarit.'),
(2,1,3,'Phuong trinh 2^x = 8 co nghiem','x=2','x=3','x=4','x=1','B','8=2^3 nen x=3.'),
(3,4,3,'Dai luong dac trung cho dao dong la','Bien do','Khoi luong','Nhiet do','Cong suat','A','Bien do la do lech cuc dai.'),
(3,4,3,'Chu ky T cua dao dong dieu hoa co don vi','s','m','kg','N','A','Chu ky do bang giay.'),
(4,3,2,'Choose the correct word: She ___ to school every day.','go','goes','gone','going','B','Chu ngu ngoi thu 3 so it + V-s/es.'),
(4,3,2,'Synonym of "rapid" is','slow','quick','late','silent','B','Rapid = quick.'),
(5,5,2,'Cong thuc tong quat cua este no don chuc la','CnH2nO2','CnH2n+2','CnH2nO','CnH2n-2','A','Este don chuc no co dang CnH2nO2.'),
(5,5,2,'Phan ung xa phong hoa xay ra voi','Este cua glixerol','Ankan','Anken','Ankin','A','Chat beo la trieste cua glixerol.'),
(6,2,1,'Tac gia Truyen Kieu la','Nguyen Du','Nguyen Trai','Ho Xuan Huong','Nam Cao','A','Tac gia la dai thi hao Nguyen Du.'),
(7,6,1,'Bao quan thong tin di truyen nam o','Nhan te bao','Mang te bao','Ti the','Ribosome','A','Nhan chua ADN.');

INSERT INTO Cau_hoi_trac_nghiem_dung_sai
(ID_ChuDe, ID_MonHoc, ID_KhoiLop, NoiDungCauHoi_TracNghiemDungSai,
 NoiDungMenhDe1_TracNghiemDungSai, NoiDungMenhDe2_TracNghiemDungSai,
 NoiDungMenhDe3_TracNghiemDungSai, NoiDungMenhDe4_TracNghiemDungSai,
 DapAn_TracNghiem4PhuongAn,
 HuongDanGiaiMenhDe1_TracNghiemDungSai, HuongDanGiaiMenhDe2_TracNghiemDungSai,
 HuongDanGiaiMenhDe3_TracNghiemDungSai, HuongDanGiaiMenhDe4_TracNghiemDungSai)
VALUES
(1,1,3,'Xet tinh dung/sai ve ham so bac hai',
 'Do thi ham so bac hai la parabol',
 'Ham so bac hai luon dong bien tren R',
 'He so a quyet dinh chieu mo parabol',
 'Parabol co mot dinh',
 '1:T,2:F,3:T,4:T',
 'Dung','Sai vi con phu thuoc khoang','Dung','Dung'),
(3,4,3,'Xet tinh dung/sai ve dao dong dieu hoa',
 'Van toc doi pha voi gia toc',
 'Gia toc luon huong ve vi tri can bang',
 'Bien do thay doi theo thoi gian',
 'Tan so la nghich dao chu ky',
 '1:F,2:T,3:F,4:T',
 'Sai','Dung','Sai','Dung'),
(4,3,2,'True/False about reading skill',
 'Skimming means reading for gist',
 'Scanning is for finding specific info',
 'You must translate every word to understand text',
 'Context can help infer unknown words',
 '1:T,2:T,3:F,4:T',
 'True','True','False','True'),
(5,5,2,'Xet tinh dung/sai ve este',
 'Este co mui thom dac trung',
 'Tat ca este tan tot trong nuoc',
 'Este co the bi thuy phan trong moi truong kiem',
 'Chat beo la mot loai este',
 '1:T,2:F,3:T,4:T',
 'Dung','Sai','Dung','Dung'),
(6,2,1,'Xet tinh dung/sai ve van hoc trung dai',
 'Van hoc trung dai thuong dung chu Han Nom',
 'Chi co mot the loai truyen',
 'Noi dung thuong de cap dao ly nhan nghia',
 'Ngon ngu giau tinh uoc le',
 '1:T,2:F,3:T,4:T',
 'Dung','Sai','Dung','Dung'),
(7,6,1,'Xet tinh dung/sai ve te bao',
 'Mang sinh chat giup trao doi chat',
 'Ribosome la noi tong hop protein',
 'Nhan te bao khong chua vat chat di truyen',
 'Ti the lien quan ho hap te bao',
 '1:T,2:T,3:F,4:T',
 'Dung','Dung','Sai','Dung');

INSERT INTO Cau_hoi_tra_loi_ngan
(ID_ChuDe, ID_MonHoc, ID_KhoiLop, NoiDungCauHoi_TracNghiemTraLoiNgan,
 KiTuThu1CuaDapAn_TracNghiemTraLoiNgan,
 KiTuThu2CuaDapAn_TracNghiemTraLoiNgan,
 KiTuThu3CuaDapAn_TracNghiemTraLoiNgan,
 KiTuThu4CuaDapAn_TracNghiemTraLoiNgan,
 HuongDanGiai_TracNghiemTraLoiNgan)
VALUES
(1,1,3,'Gia tri cua pi lam tron 4 ky tu dau la?', '3','.', '1','4','Pi xap xi 3.14159.'),
(2,1,3,'2 mu 5 bang bao nhieu (viet 4 ky tu, them khoang trong neu can)?', '3','2',' ',' ','2^5 = 32.'),
(3,4,3,'Don vi cua tan so?', 'H','z',' ',' ','Tan so co don vi Hz.'),
(4,3,2,'Fill in: "I ___ a student" (4 ky tu)', 'a','m',' ',' ','To be voi I la am.'),
(5,5,2,'Ky hieu hoa hoc cua nuoc?', 'H','2','O',' ','Nuoc co cong thuc H2O.'),
(6,2,1,'Tac gia cua Truyen Kieu (4 ky tu dau)', 'N','g','u','y','Nguyen Du.'),
(7,6,1,'Bao quan ADN nam chu yeu o dau? (4 ky tu dau)', 'N','h','a','n','ADN chu yeu o nhan te bao.'),
(8,3,2,'Past tense cua go la gi? (4 ky tu dau)', 'w','e','n','t','Dong tu bat quy tac: go-went-gone.');

INSERT INTO De_Thi (TenDeThi, ID_NguoiTao, ID_MaMon, ID_MaKhoi, NgayTao, MoTa)
VALUES
('De Toan 12 - Giua ky 2 - Ma 001', 2, 1, 3, '2026-03-01 09:00:00', 'De tong hop Ham so va Mu logarit'),
('De Ly 12 - Kiem tra chuong dao dong - Ma 002', 5, 4, 3, '2026-03-02 10:00:00', 'Danh gia kien thuc dao dong co ban'),
('De Anh 11 - Reading + Grammar - Ma 003', 3, 3, 2, '2026-03-03 11:00:00', 'Kiem tra ky nang doc hieu va ngu phap'),
('De Hoa 11 - Este Lipit - Ma 004', 6, 5, 2, '2026-03-04 14:00:00', 'Kiem tra chu de este va chat beo'),
('De Van 10 - Van hoc trung dai - Ma 005', 4, 2, 1, '2026-03-05 15:00:00', 'Danh gia kien thuc tac gia tac pham'),
('De Sinh 10 - Te bao hoc - Ma 006', 7, 6, 1, '2026-03-05 16:00:00', 'Kiem tra kien thuc te bao hoc co ban');

-- Moi dong la mot lien ket de-thi -> cau hoi (theo tung loai)
INSERT INTO De_Thi_Chi_Tiet
(ID_MaDeThi, ID_NguoiTao, ID_MaMon, ID_MaKhoi, ID_TracNghiem4PhuongAn, ID_TracNghiemDungSai, ID_TracNghiemTraLoiNgan)
VALUES
(1,2,1,3,1,NULL,NULL),
(1,2,1,3,2,NULL,NULL),
(1,2,1,3,3,NULL,NULL),
(1,2,1,3,4,NULL,NULL),
(1,2,1,3,NULL,1,NULL),
(1,2,1,3,NULL,NULL,1),
(1,2,1,3,NULL,NULL,2),

(2,5,4,3,5,NULL,NULL),
(2,5,4,3,6,NULL,NULL),
(2,5,4,3,NULL,2,NULL),
(2,5,4,3,NULL,NULL,3),

(3,3,3,2,7,NULL,NULL),
(3,3,3,2,8,NULL,NULL),
(3,3,3,2,NULL,3,NULL),
(3,3,3,2,NULL,NULL,4),
(3,3,3,2,NULL,NULL,8),

(4,6,5,2,9,NULL,NULL),
(4,6,5,2,10,NULL,NULL),
(4,6,5,2,NULL,4,NULL),
(4,6,5,2,NULL,NULL,5),

(5,4,2,1,11,NULL,NULL),
(5,4,2,1,NULL,5,NULL),
(5,4,2,1,NULL,NULL,6),

(6,7,6,1,12,NULL,NULL),
(6,7,6,1,NULL,6,NULL),
(6,7,6,1,NULL,NULL,7);

INSERT INTO Ky_thi
(ID_KhoiLop, ID_MonHoc, ID_ChuDe, ID_LopHoc, Ten_KyThi, MoTa_KyThi, ThoiGianLamBai_KyThi,
 PhanBoDiemTracNghiem4PhuongAn_KyThi, PhanBoDiemTracNghiemDungSai_KyThi, PhanBoDiemTracNghiemTraLoiNgan_KyThi,
 SoCauHoiTracNghiem4PhuongAn_KyThi, SoCauHoiTracNghiemDungSai_KyThi, SoCauHoiTracNghiemTraLoiNgan_KyThi,
 ID_MaDeThi, ThoiGianBatDau_KyThi, ThoiGianKetThuc_KyThi)
VALUES
(3,1,1,5,'Thi giua ky Toan 12A3','Kiem tra giua ky 2 Toan khoi 12',45,6.00,2.00,2.00,4,1,2,1,'2026-03-10 07:30:00','2026-03-10 08:15:00'),
(3,4,3,6,'Thi chuong Dao dong 12A3','Bai thi nhanh danh gia dao dong',30,5.00,3.00,2.00,2,1,1,2,'2026-03-12 09:00:00','2026-03-12 09:30:00'),
(2,3,4,3,'Thi Reading 11A2','Bai thi doc hieu va ngu phap',40,6.50,1.50,2.00,2,1,2,3,'2026-03-14 13:30:00','2026-03-14 14:10:00'),
(2,5,5,4,'Thi chu de Este 11A2','Kiem tra cuoi chuong Hoa hoc',35,6.00,2.00,2.00,2,1,1,4,'2026-03-16 15:00:00','2026-03-16 15:35:00');

INSERT INTO Diem_danh
(ID_LopHoc, NgayHoc_DiemDanh, ThoiGianBatDau_DiemDanh, ThoiGianKetThuc_DiemDanh, ChiTietDiemDanh_DiemDanh, TrangThaiBuoiHoc_DiemDanh)
VALUES
(5, '2026-03-08', '2026-03-08 07:00:00', '2026-03-08 07:45:00', 'Si so 8/8, hoc on tap ham so.', 'completed'),
(5, '2026-03-09', '2026-03-09 07:00:00', '2026-03-09 07:45:00', 'Si so 7/8, 1 ban xin nghi co phep.', 'completed'),
(3, '2026-03-11', '2026-03-11 13:00:00', '2026-03-11 13:45:00', 'Si so 8/8, luyen reading.', 'completed'),
(4, '2026-03-13', '2026-03-13 14:00:00', '2026-03-13 14:45:00', 'Si so 7/8, 1 ban den muon.', 'completed'),
(1, '2026-03-15', '2026-03-15 08:00:00', NULL, 'Dang dien ra tiet hoc van hoc trung dai.', 'in_progress');

INSERT INTO Don_xin_nghi
(ID_LopHoc, ID_User, ID_DiemDanh, ThoiGianGui_DonXinNghi, NoiDung_DonXinNghi, TrangThai_DonXinNghi)
VALUES
(5, 24, 2, '2026-03-09 06:20:00', 'Em bi sot, xin phep nghi buoi on tap ngay 09/03.', 'approved'),
(4, 21, 4, '2026-03-13 12:10:00', 'Em co viec gia dinh, xin den tre 20 phut.', 'approved'),
(1, 13, 5, '2026-03-15 07:30:00', 'Em bi dau bung, xin nghi tiet hoc hom nay.', 'pending');

INSERT INTO Diem_so
(ID_User, ID_MaKyThi, ID_MaDeThi,
 DiemPhanTracNghiem4PhuongAn_DiemSo,
 DiemPhanTracNghiemDungSai_DiemSo,
 DiemPhanTracNghiemTraLoiNgan_DiemSo,
 TongDiem_DiemSo,
 ThoiGianBatDau_DiemSo, ThoiGianKetThuc_DiemSo, ThoiGianLamBai_DiemSo, LichSuLamBai)
VALUES
(24, 1, 1, 5.00, 1.50, 1.50, 8.00, '2026-03-10 07:31:00', '2026-03-10 08:12:00', 41, 'Sai cau 3, bo qua cau tra loi ngan so 2'),
(25, 1, 1, 6.00, 1.50, 1.80, 9.30, '2026-03-10 07:30:30', '2026-03-10 08:09:10', 39, 'Hoan thanh toan bo, sai 1 menh de dung/sai'),
(26, 1, 1, 4.50, 1.00, 1.00, 6.50, '2026-03-10 07:33:00', '2026-03-10 08:14:20', 41, 'Gap kho o phan logarit'),
(16, 3, 3, 5.50, 1.00, 1.50, 8.00, '2026-03-14 13:31:00', '2026-03-14 14:08:00', 37, 'Lam tot reading, sai 1 cau grammar'),
(17, 3, 3, 6.00, 1.50, 1.80, 9.30, '2026-03-14 13:30:30', '2026-03-14 14:05:30', 35, 'Diem cao nhat lop'),
(18, 3, 3, 4.00, 1.00, 1.00, 6.00, '2026-03-14 13:32:10', '2026-03-14 14:09:40', 37, 'Can luyen them tu vung'),
(19, 4, 4, 5.50, 1.50, 1.50, 8.50, '2026-03-16 15:01:00', '2026-03-16 15:33:00', 32, 'Nho bai este kha tot'),
(20, 4, 4, 4.00, 1.00, 1.20, 6.20, '2026-03-16 15:02:00', '2026-03-16 15:34:20', 32, 'Sai cau xa phong hoa');

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- 3) Goi y truy van nhanh de test sau import
-- =========================================================
-- SELECT COUNT(*) AS so_user FROM `User`;
-- SELECT COUNT(*) AS so_cauhoi_4pa FROM Cau_hoi_trac_nghiem_4_phuong_an;
-- SELECT ID_KyThi, Ten_KyThi FROM Ky_thi ORDER BY ID_KyThi;
-- SELECT HoVaTen_User, TongDiem_DiemSo FROM Diem_so ds JOIN `User` u ON u.ID_User = ds.ID_User ORDER BY TongDiem_DiemSo DESC;
