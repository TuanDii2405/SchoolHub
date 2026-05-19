<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giáo viên – Thông tin cá nhân</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ GIÁO VIÊN</h2></div>
        <div class="content-box">
            <div class="section-title blue">Thông tin cá nhân</div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $e) {{ $e }}<br> @endforeach
                </div>
            @endif

            @if ($teacher)
            <table class="info-table" style="max-width:600px">
                <tr>
                    <td class="info-label">Họ và tên</td>
                    <td><span class="info-value">{{ $teacher->HoVaTen_User }}</span></td>
                </tr>
                <tr>
                    <td class="info-label">Ngày sinh</td>
                    <td><span class="info-value">{{ $teacher->NgayThangNamSinh_User ? \Carbon\Carbon::parse($teacher->NgayThangNamSinh_User)->format('d/m/Y') : '—' }}</span></td>
                </tr>
                <tr>
                    <td class="info-label">Môn phụ trách</td>
                    <td><span class="info-value">{{ $teacher->Ten_MonHoc ?? '—' }}</span></td>
                </tr>
                <tr>
                    <td class="info-label">Khối phụ trách</td>
                    <td><span class="info-value">{{ $teacher->Ten_KhoiLop ?? '—' }}</span></td>
                </tr>
                <tr>
                    <td class="info-label">Email</td>
                    <td><span class="info-value">{{ $teacher->EmailCaNhan_User ?? '—' }}</span></td>
                </tr>
                <tr>
                    <td class="info-label">Số điện thoại</td>
                    <td><span class="info-value">{{ $teacher->SoDienThoai_User ?? '—' }}</span></td>
                </tr>
                <tr>
                    <td class="info-label">Số lớp đang dạy</td>
                    <td><span class="info-value">{{ $teacher->so_lop }} lớp</span></td>
                </tr>
                <tr>
                    <td class="info-label">Ngày tham gia</td>
                    <td><span class="info-value">{{ \Carbon\Carbon::parse($teacher->NgayTaoTaiKhoan_User)->format('d/m/Y') }}</span></td>
                </tr>
            </table>
            <br>
            <button class="btn-primary" onclick="openEdit()">Cập nhật thông tin</button>
            @else
            <div class="empty-notice">Không tìm thấy thông tin tài khoản.</div>
            @endif
        </div>
    </main>
</div>

{{-- MODAL cập nhật thông tin --}}
<div id="modalEdit" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeEdit()">
    <div class="modal-box" style="width:480px">
        <div class="modal-header">
            <span class="modal-header-title">Cập nhật thông tin cá nhân</span>
            <button class="modal-close" onclick="closeEdit()">×</button>
        </div>
        <form method="POST" action="{{ route('teacher.thong-tin.update') }}">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Họ và tên <span class="required">*</span></label>
                    <input class="form-input" type="text" name="HoVaTen_User" id="f_hoten"
                           required maxlength="150">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input class="form-input" type="email" name="EmailCaNhan_User" id="f_email" maxlength="150">
                </div>
                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input class="form-input" type="text" name="SoDienThoai_User" id="f_sdt" maxlength="20">
                </div>
                <div class="form-group">
                    <label class="form-label">Ngày sinh</label>
                    <input class="form-input" type="date" name="NgayThangNamSinh_User" id="f_ngaysinh">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEdit()">Hủy</button>
                <button type="submit" class="action-btn">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.PAGE_ROLE   = 'giaovien';
    window.PAGE_ACTIVE = 'gv-thongtin';

    @if ($teacher)
    const teacherData = @json($teacher);
    @endif

    function openEdit() {
        document.getElementById('f_hoten').value    = teacherData.HoVaTen_User    || '';
        document.getElementById('f_email').value    = teacherData.EmailCaNhan_User || '';
        document.getElementById('f_sdt').value      = teacherData.SoDienThoai_User || '';
        const dob = teacherData.NgayThangNamSinh_User
            ? teacherData.NgayThangNamSinh_User.substring(0, 10) : '';
        document.getElementById('f_ngaysinh').value = dob;
        document.getElementById('modalEdit').style.display = 'flex';
        document.getElementById('f_hoten').focus();
    }

    function closeEdit() {
        document.getElementById('modalEdit').style.display = 'none';
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
