# PHP FinalExam — Hệ thống thi trực tuyến

Dự án Laravel 12 · PHP 8.2 · MySQL · Tailwind CSS 4 · Vite

---

## Mục lục

1. [Yêu cầu hệ thống](#1-yêu-cầu-hệ-thống)
2. [Cài đặt phần mềm cần thiết](#2-cài-đặt-phần-mềm-cần-thiết)
3. [Lấy mã nguồn](#3-lấy-mã-nguồn)
4. [Cài đặt dự án](#4-cài-đặt-dự-án)
5. [Cấu hình môi trường (.env)](#5-cấu-hình-môi-trường-env)
6. [Nhập cơ sở dữ liệu](#6-nhập-cơ-sở-dữ-liệu)
7. [Build giao diện (Vite)](#7-build-giao-diện-vite)
8. [Chạy ứng dụng](#8-chạy-ứng-dụng)
9. [Tài khoản mặc định](#9-tài-khoản-mặc-định)
10. [Xử lý lỗi thường gặp](#10-xử-lý-lỗi-thường-gặp)

---

## 1. Yêu cầu hệ thống

| Phần mềm | Phiên bản tối thiểu | Ghi chú                        |
| -------- | ------------------- | ------------------------------ |
| Windows  | 10 / 11             |                                |
| XAMPP    | 8.2.x               | Bao gồm PHP 8.2, Apache, MySQL |
| Composer | 2.x                 | Quản lý package PHP            |
| Node.js  | 18 LTS hoặc mới hơn | Bao gồm npm                    |
| Git      | bất kỳ              | Để clone mã nguồn              |

> VS Code đã có sẵn. Các phần mềm còn lại sẽ được hướng dẫn cài đặt ở bước 2.

---

## 2. Cài đặt phần mềm cần thiết

### 2.1 XAMPP (PHP 8.2 + Apache + MySQL)

1. Tải XAMPP tại: https://www.apachefriends.org/download.html  
   Chọn phiên bản **PHP 8.2.x** (Windows installer `.exe`).

2. Chạy file cài đặt, giữ mặc định, cài vào `C:\xampp`.

3. Sau khi cài xong, mở **XAMPP Control Panel**:
   - Nhấn **Start** bên cạnh **Apache**.
   - Nhấn **Start** bên cạnh **MySQL**.
   - Cả hai phải hiện chữ **Running** (nền xanh lá).

4. Thêm PHP vào PATH của Windows để dùng trong terminal:
   - Nhấn `Win + S` → gõ **"environment variables"** → chọn **"Edit the system environment variables"**.
   - Nhấn **Environment Variables...** → tìm dòng **Path** trong **System variables** → nhấn **Edit**.
   - Nhấn **New** → nhập `C:\xampp\php` → nhấn **OK** tất cả.
   - Mở terminal mới và kiểm tra: `php -v` → phải hiện `PHP 8.2.x`.

### 2.2 Composer

1. Tải Composer tại: https://getcomposer.org/Composer-Setup.exe

2. Chạy file `.exe`, nhấn **Next** liên tục.  
   Khi hỏi đường dẫn PHP, chọn `C:\xampp\php\php.exe`.

3. Kiểm tra sau cài đặt (mở terminal mới):
   ```
   composer --version
   ```
   Phải hiện `Composer version 2.x.x`.

### 2.3 Node.js

1. Tải Node.js tại: https://nodejs.org/  
   Chọn phiên bản **LTS** (ví dụ: 20.x hoặc 22.x).

2. Chạy file `.msi`, nhấn **Next** liên tục, giữ mặc định.

3. Kiểm tra sau cài đặt (mở terminal mới):
   ```
   node -v
   npm -v
   ```
   Phải hiện version hợp lệ.

### 2.4 Git

1. Tải Git tại: https://git-scm.com/download/win

2. Chạy file cài đặt, giữ mặc định, nhấn **Next** liên tục.

3. Kiểm tra:
   ```
   git --version
   ```

---

## 3. Lấy mã nguồn

Mở **Terminal** (PowerShell hoặc Command Prompt) và chạy:

```powershell
cd C:\xampp\htdocs
git clone <URL_REPO> PHP_FinalExam
cd PHP_FinalExam
```

> Nếu đã có thư mục `PHP_FinalExam` rồi thì bỏ qua bước clone, chỉ cần `cd C:\xampp\htdocs\PHP_FinalExam`.

---

## 4. Cài đặt dự án

Đảm bảo đang ở trong thư mục `C:\xampp\htdocs\PHP_FinalExam`, sau đó chạy tuần tự:

```powershell
# Cài PHP dependencies
composer install

# Cài Node dependencies
npm install
```

> Bước `composer install` có thể mất 1-3 phút. Bước `npm install` mất khoảng 30 giây.

---

## 5. Cấu hình môi trường (.env)

### 5.1 Tạo file .env

```powershell
copy .env.example .env
```

### 5.2 Chỉnh sửa .env

Mở file `.env` trong VS Code và sửa các dòng sau:

```env
APP_NAME="PHP FinalExam"
APP_URL=http://localhost/PHP_FinalExam/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_exam_db
DB_USERNAME=root
DB_PASSWORD=
```

> Mật khẩu MySQL của XAMPP mặc định là **trống** (để trống `DB_PASSWORD=`).

### 5.3 Sinh Application Key

```powershell
php artisan key:generate
```

Lệnh này tự ghi `APP_KEY=base64:...` vào file `.env`.

### 5.4 Tạo bảng Session

Dự án dùng `SESSION_DRIVER=database`. Chạy migration để tạo bảng session:

```powershell
php artisan migrate
```

> Lệnh này tạo các bảng `sessions`, `cache`, `jobs` trong database. **Không** tạo bảng nghiệp vụ (bảng đó đã có trong file SQL ở bước 6).

---

## 6. Nhập cơ sở dữ liệu

### 6.1 Mở phpMyAdmin

Mở trình duyệt và truy cập: `http://localhost/phpmyadmin`

### 6.2 Import file SQL

1. Trong phpMyAdmin, nhấn tab **Import** ở thanh trên cùng.
2. Nhấn **Choose File** → điều hướng đến thư mục dự án → chọn file `database_school_exam.sql`.
3. Nhấn **Import** (nút màu xám phía dưới).
4. Chờ vài giây → phpMyAdmin hiện thông báo thành công màu xanh lá.

> File SQL sẽ tự tạo database `school_exam_db` và toàn bộ bảng + dữ liệu mẫu.

### 6.3 Kiểm tra

Trong phpMyAdmin, bên trái sẽ xuất hiện database `school_exam_db` với các bảng:  
`User`, `Lop_Hoc`, `Mon_Hoc`, `Cau_Hoi`, `De_Thi`, `Ky_Thi`, v.v.

---

## 7. Build giao diện (Vite)

```powershell
npm run build
```

> Lệnh này compile CSS (Tailwind) và JS, xuất ra thư mục `public/build/`. Cần chạy lại mỗi khi thay đổi file CSS/JS.

---

## 8. Chạy ứng dụng

### Cách 1: Dùng XAMPP (Khuyến nghị cho bài nộp)

1. Đảm bảo **Apache** và **MySQL** đang **Running** trong XAMPP Control Panel.
2. Mở trình duyệt, truy cập:
   ```
   http://localhost/PHP_FinalExam/public
   ```

### Cách 2: Dùng PHP built-in server (Phát triển nhanh)

```powershell
php artisan serve
```

Mở trình duyệt, truy cập: `http://127.0.0.1:8000`

> Cách này không cần Apache nhưng MySQL trong XAMPP vẫn phải đang chạy.

---

## 9. Tài khoản mặc định

| Vai trò   | Tên đăng nhập | Mật khẩu   |
| --------- | ------------- | ---------- |
| Admin     | `admin`       | `admin123` |
| Giáo viên | `giaovien`    | `gv123`    |
| Học sinh  | `hocsinh`     | `hs123`    |

> Có thể đăng nhập bằng **email** hoặc **số điện thoại** của bất kỳ tài khoản nào trong database.

---

## 10. Xử lý lỗi thường gặp

### Lỗi: `php: command not found` hoặc `'php' is not recognized`

PHP chưa được thêm vào PATH. Thực hiện lại bước 2.1 (thêm `C:\xampp\php` vào PATH) rồi mở lại terminal.

### Lỗi: `composer: command not found`

Khởi động lại máy sau khi cài Composer, hoặc thêm `C:\ProgramData\ComposerSetup\bin` vào PATH thủ công.

### Lỗi: `SQLSTATE[HY000] [1045] Access denied for user 'root'`

Mật khẩu MySQL sai. Kiểm tra lại `DB_PASSWORD` trong `.env`. Với XAMPP mặc định, để trống.

### Lỗi: `SQLSTATE[HY000] [2002] Connection refused`

MySQL chưa chạy. Mở XAMPP Control Panel → nhấn **Start** bên cạnh **MySQL**.

### Lỗi: `No application encryption key has been specified`

Chưa sinh APP_KEY. Chạy: `php artisan key:generate`

### Lỗi: `Table 'school_exam_db.sessions' doesn't exist`

Chưa chạy migration. Chạy: `php artisan migrate`

### Trang trắng hoặc lỗi 500 sau khi import SQL

1. Kiểm tra file `.env` đã cấu hình đúng chưa.
2. Xóa cache: `php artisan config:clear && php artisan cache:clear`
3. Kiểm tra `storage/logs/laravel.log` để xem lỗi chi tiết.

### CSS/JS không load (trang không có giao diện)

Chưa build Vite. Chạy: `npm run build`

---

## Tóm tắt lệnh (chạy lần đầu từ đầu đến cuối)

```powershell
# 1. Vào thư mục dự án
cd C:\xampp\htdocs\PHP_FinalExam

# 2. Cài dependencies
composer install
npm install

# 3. Tạo và sửa .env (xem chi tiết mục 5)
copy .env.example .env
# → Mở .env, đổi DB_CONNECTION=mysql, thêm DB_DATABASE=school_exam_db

# 4. Sinh key + tạo bảng session
php artisan key:generate
php artisan migrate

# 5. Import database_school_exam.sql qua phpMyAdmin (xem mục 6)

# 6. Build giao diện
npm run build

# 7. Mở trình duyệt
# XAMPP: http://localhost/PHP_FinalExam/public
# hoặc chạy: php artisan serve  →  http://127.0.0.1:8000
```
