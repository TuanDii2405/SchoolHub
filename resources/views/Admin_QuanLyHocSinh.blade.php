<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Quản lý học sinh</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ ADMIN</h2></div>
        <div class="content-box">
            <div class="section-title">Quản lý học sinh</div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $err) {{ $err }}<br> @endforeach
                </div>
            @endif

            <div class="action-bar">
                <button class="action-btn" onclick="openAdd()">+ Thêm mới</button>
                <input class="search-input" type="text" id="searchInput"
                       placeholder="Tìm theo tên hoặc email..."
                       oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="hocSinhTable">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Họ và tên</th>
                            <th>Email</th>
                            <th>Ngày sinh</th>
                            <th>Số điện thoại</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hocSinhs as $i => $hs)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $hs->HoVaTen_User }}</td>
                            <td>{{ $hs->EmailCaNhan_User ?? '—' }}</td>
                            <td>{{ $hs->NgayThangNamSinh_User ? \Carbon\Carbon::parse($hs->NgayThangNamSinh_User)->format('d/m/Y') : '—' }}</td>
                            <td>{{ $hs->SoDienThoai_User ?? '—' }}</td>
                            <td>
                                @if ($hs->TrangThaiHoatDong_User === 'active')
                                    <span class="badge badge-active">Hoạt động</span>
                                @elseif ($hs->TrangThaiHoatDong_User === 'locked')
                                    <span class="badge badge-locked">Bị khóa</span>
                                @else
                                    <span class="badge badge-inactive">Không HĐ</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap">
                                <form method="POST"
                                      action="{{ route('admin.impersonate', $hs->ID_User) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Đăng nhập vào tài khoản của {{ addslashes($hs->HoVaTen_User) }}?')">
                                    @csrf
                                    <button type="submit" class="btn-edit">Xem</button>
                                </form>

                                <button class="btn-edit" onclick="openEdit(
                                    {{ $hs->ID_User }},
                                    '{{ addslashes($hs->HoVaTen_User) }}',
                                    '{{ $hs->EmailCaNhan_User ?? '' }}',
                                    '{{ $hs->SoDienThoai_User ?? '' }}',
                                    '{{ $hs->NgayThangNamSinh_User ?? '' }}',
                                    '{{ $hs->TrangThaiHoatDong_User }}'
                                )">Sửa</button>

                                <form method="POST"
                                      action="{{ route('admin.hoc-sinh.destroy', $hs->ID_User) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Xóa học sinh {{ addslashes($hs->HoVaTen_User) }}?\nLưu ý: dữ liệu thành viên lớp học sẽ bị xóa theo.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="empty-notice">Chưa có học sinh nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

{{-- ========== MODAL THÊM / SỬA ========== --}}
<div id="modalOverlay" class="modal-overlay" style="display:none" onclick="closeOnBackdrop(event)">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-header-title" id="modalTitle">Thêm học sinh mới</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="modalForm" method="POST" action="{{ route('admin.hoc-sinh.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Họ và tên <span class="required">*</span></label>
                    <input class="form-input" type="text" name="HoVaTen_User" id="f_ten"
                           placeholder="Nhập họ và tên" required maxlength="150">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input class="form-input" type="email" name="EmailCaNhan_User" id="f_email"
                               placeholder="email@school.edu.vn" maxlength="150">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số điện thoại</label>
                        <input class="form-input" type="text" name="SoDienThoai_User" id="f_sdt"
                               placeholder="09xxxxxxxx" maxlength="20">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Ngày sinh</label>
                        <input class="form-input" type="date" name="NgayThangNamSinh_User" id="f_ngaysinh">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Trạng thái <span class="required">*</span></label>
                        <select class="form-select" name="TrangThaiHoatDong_User" id="f_trangthai" required>
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Không hoạt động</option>
                            <option value="locked">Bị khóa</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Mật khẩu <span class="required" id="pwRequired">*</span>
                        <span id="pwHint" style="font-weight:400;color:var(--text-soft);display:none">
                            (để trống = giữ nguyên)
                        </span>
                    </label>
                    <input class="form-input" type="password" name="mat_khau" id="f_matkhau"
                           placeholder="Tối thiểu 6 ký tự" minlength="6">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button type="submit" class="action-btn" id="submitBtn">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.PAGE_ROLE   = 'admin';
    window.PAGE_ACTIVE = 'quanly-hocsinh';

    const storeUrl  = "{{ route('admin.hoc-sinh.store') }}";
    const updateBase = "{{ url('admin/hoc-sinh') }}";

    function openAdd() {
        document.getElementById('modalTitle').textContent  = 'Thêm học sinh mới';
        document.getElementById('modalForm').action        = storeUrl;
        document.getElementById('formMethod').value        = 'POST';
        document.getElementById('submitBtn').textContent   = 'Thêm';
        document.getElementById('f_ten').value             = '';
        document.getElementById('f_email').value           = '';
        document.getElementById('f_sdt').value             = '';
        document.getElementById('f_ngaysinh').value        = '';
        document.getElementById('f_trangthai').value       = 'active';
        document.getElementById('f_matkhau').value         = '';
        document.getElementById('f_matkhau').required      = true;
        document.getElementById('pwRequired').style.display = '';
        document.getElementById('pwHint').style.display    = 'none';
        document.getElementById('modalOverlay').style.display = 'flex';
    }

    function openEdit(id, ten, email, sdt, ngaysinh, trangthai) {
        document.getElementById('modalTitle').textContent  = 'Sửa thông tin học sinh';
        document.getElementById('modalForm').action        = updateBase + '/' + id;
        document.getElementById('formMethod').value        = 'PUT';
        document.getElementById('submitBtn').textContent   = 'Cập nhật';
        document.getElementById('f_ten').value             = ten;
        document.getElementById('f_email').value           = email;
        document.getElementById('f_sdt').value             = sdt;
        document.getElementById('f_ngaysinh').value        = ngaysinh;
        document.getElementById('f_trangthai').value       = trangthai;
        document.getElementById('f_matkhau').value         = '';
        document.getElementById('f_matkhau').required      = false;
        document.getElementById('pwRequired').style.display = 'none';
        document.getElementById('pwHint').style.display    = '';
        document.getElementById('modalOverlay').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('modalOverlay').style.display = 'none';
    }

    function closeOnBackdrop(e) {
        if (e.target === document.getElementById('modalOverlay')) closeModal();
    }

    function filterTable() {
        const q   = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#hocSinhTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
