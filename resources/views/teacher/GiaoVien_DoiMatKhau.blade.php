<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giáo viên – Đổi mật khẩu</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ GIÁO VIÊN</h2></div>
        <div class="content-box">
            <div class="section-title blue">Đổi mật khẩu</div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $e) {{ $e }}<br> @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('teacher.doi-mat-khau.update') }}" style="max-width:440px">
                @csrf
                <div class="form-group-inline">
                    <label>Mật khẩu hiện tại</label>
                    <input type="password" name="mat_khau_cu"
                           placeholder="Nhập mật khẩu hiện tại..." required>
                </div>
                <div class="form-group-inline">
                    <label>Mật khẩu mới</label>
                    <input type="password" name="mat_khau_moi"
                           placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)..." required>
                </div>
                <div class="form-group-inline">
                    <label>Xác nhận mật khẩu mới</label>
                    <input type="password" name="xac_nhan"
                           placeholder="Nhập lại mật khẩu mới..." required>
                </div>
                <button type="submit" class="btn-primary">Xác nhận đổi mật khẩu</button>
            </form>
        </div>
    </main>
</div>
<script>
    window.PAGE_ROLE   = 'giaovien';
    window.PAGE_ACTIVE = 'gv-doimatkhau';
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
