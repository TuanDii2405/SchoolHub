# PHP FinalExam - Báo cáo kiểm tra hệ thống

Ngày cập nhật: 2026-05-14

## 1) Mục tiêu dự án

Xây dựng hệ thống thi trực tuyến cho 3 nhóm người dùng:

1. Admin
2. Teacher
3. Student

Mục tiêu nghiệp vụ:

1. Quản lý tài khoản, lớp, môn, khối, kỳ thi.
2. Quản lý ngân hàng câu hỏi và đề thi.
3. Học sinh tham gia thi và theo dõi kết quả.

## 2) Cấu trúc hiện tại (đã chuẩn hóa)

Thư mục gốc hiện gọn, tách rõ từng lớp:

1. pages: toàn bộ trang PHP theo role.
2. views: template HTML giao diện theo role.
3. auth: xử lý đăng nhập/đăng xuất.
4. guards: kiểm tra đăng nhập và phân quyền.
5. config: cấu hình kết nối CSDL.
6. bootstrap: session + helper dùng chung.
7. assets: CSS/JS/ảnh.
8. router.php: router cho PHP built-in server.
9. run.bat: script chạy hệ thống local.

Chi tiết pages:

1. pages/admin: 10 trang.
2. pages/teacher: 10 trang.
3. pages/student: 7 trang.
4. pages/auth: 1 trang đăng nhập.

## 3) Kết quả kiểm tra từ trên xuống

### 3.1 EntryPoint và Routing

Đã kiểm tra:

1. index.php định tuyến theo role session.
2. .htaccess map URL cũ sang cấu trúc pages mới.
3. router.php map URL cũ khi chạy bằng PHP built-in server.
4. run.bat chạy local với 2 chế độ:
   - Apache/XAMPP nếu có.
   - Built-in server nếu không có Apache.

Kết quả:

1. Truy cập dangnhap.php thành công (HTTP 200).
2. Truy cập Admin_TrangChu.php thành công (HTTP 200).
3. Truy cập GiaoVien_TrangChu.php thành công (HTTP 200).
4. Truy cập HocSinh_TrangChu.php thành công (HTTP 200).

### 3.2 Auth, Session, Guard

Đã kiểm tra:

1. auth/login.php xác thực bằng email/số điện thoại hoặc alias role.
2. auth/logout.php hủy session đúng chuẩn.
3. guards/auth.php chặn khi chưa đăng nhập.
4. guards/role.php chặn sai quyền và trả 403.

Kết quả:

1. Luồng phân quyền role đã hoạt động.
2. Chuyển hướng theo role sau login đã cấu hình đủ.

### 3.3 Database và dữ liệu mẫu

Đã kiểm tra:

1. createDatabaseConnection trong config/database.php.
2. Cơ chế chọn DB chỉ nhận DB có bảng User.
3. Kết nối thực tế và đếm dữ liệu User.

Kết quả:

1. DB đang dùng: school_exam_db.
2. Tổng User: 37.
3. Admin: 1.
4. Teacher: 6.
5. Student: 30.

### 3.4 Chất lượng mã hiện tại

Đã kiểm tra:

1. Syntax/Problems toàn workspace.
2. Đồng bộ đường dẫn sau khi dọn thư mục.
3. Encoding UTF-8 cho các file PHP quan trọng.

Kết quả:

1. Không phát hiện lỗi cú pháp.
2. Hệ thống chạy được trên local.

## 4) Những gì đã đạt được

1. Hoàn tất nền tảng đăng nhập + session + role guard.
2. Hoàn tất bộ trang role theo cấu trúc pages + views tách biệt.
3. Hoàn tất route tương thích ngược URL cũ, không gãy link.
4. Hoàn tất script run local linh hoạt (Apache hoặc built-in).
5. Hoàn tất cơ chế chống chọn nhầm DB không đúng schema User.
6. Hoàn tất dữ liệu mẫu đủ để test 3 vai trò.

## 5) Những gì cần làm tiếp theo (ưu tiên)

### Ưu tiên cao (nên làm ngay)

1. Chuyển mật khẩu mẫu sang hash bằng password_hash.
2. Bổ sung CSRF token cho form đăng nhập và form thay đổi dữ liệu.
3. Bổ sung log lỗi hệ thống ra file để debug (không lộ cho người dùng).
4. Thống nhất charset UTF-8 cho toàn bộ file còn lại.

### Ưu tiên trung bình

1. Chuyển các trang HTML tĩnh trong views thành trang động gắn DB thật.
2. Hoàn thiện CRUD thật cho:
   - Quản lý giáo viên/học sinh.
   - Quản lý lớp học/khối/môn.
   - Chủ đề/câu hỏi/đề thi/kỳ thi.
3. Hoàn thiện nghiệp vụ làm bài thi, chấm điểm, lưu lịch sử.
4. Hoàn thiện nghiệp vụ điểm danh và đơn xin nghỉ.

### Ưu tiên cải tiến

1. Bổ sung test tối thiểu cho login, guard, DB connection.
2. Chuẩn hóa logging và cấu hình môi trường dev/prod.
3. Viết tài liệu API/luồng xử lý cho từng nhóm chức năng.

## 6) Cách chạy nhanh

1. Chạy run.bat tại thư mục dự án.
2. Mở URL được script in ra (có thể là cổng 8080 hoặc 8081).
3. Đăng nhập để test theo vai trò.

## 7) Tài khoản test nhanh

1. Admin: admin / admin123
2. Giáo viên: giaovien / gv123
3. Học sinh: hocsinh / hs123

Ghi chú:

1. Có thể đăng nhập bằng email hoặc số điện thoại của User trong DB.
2. Dữ liệu mẫu hiện dùng cho mục đích phát triển/local test.
