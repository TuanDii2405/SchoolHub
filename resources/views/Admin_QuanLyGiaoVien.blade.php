<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Quản lý giáo viên</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ ADMIN</h2></div>
        <div class="content-box">
            <div class="section-title">Quản lý giáo viên</div>

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
                       placeholder="Tìm theo tên, email, môn..."
                       oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="giaoVienTable">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Họ và tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Môn phụ trách</th>
                            <th>Khối phụ trách</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($giaoViens as $i => $gv)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $gv->HoVaTen_User }}</td>
                            <td>{{ $gv->EmailCaNhan_User ?? '—' }}</td>
                            <td>{{ $gv->SoDienThoai_User ?? '—' }}</td>
                            <td>{{ $gv->Ten_MonHoc ?? '—' }}</td>
                            <td>{{ $gv->Ten_KhoiLop ?? '—' }}</td>
                            <td>
                                @if ($gv->TrangThaiHoatDong_User === 'active')
                                    <span class="badge badge-active">Hoạt động</span>
                                @elseif ($gv->TrangThaiHoatDong_User === 'locked')
                                    <span class="badge badge-locked">Bị khóa</span>
                                @else
                                    <span class="badge badge-inactive">Không HĐ</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap">
                                <form method="POST"
                                      action="{{ route('admin.impersonate', $gv->ID_User) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Đăng nhập vào tài khoản của {{ addslashes($gv->HoVaTen_User) }}?')">
                                    @csrf
                                    <button type="submit" class="btn-edit">Xem</button>
                                </form>

                                <button class="btn-edit" onclick="openEdit(
                                    {{ $gv->ID_User }},
                                    '{{ addslashes($gv->HoVaTen_User) }}',
                                    '{{ $gv->EmailCaNhan_User ?? '' }}',
                                    '{{ $gv->SoDienThoai_User ?? '' }}',
                                    '{{ $gv->NgayThangNamSinh_User ?? '' }}',
                                    '{{ $gv->TrangThaiHoatDong_User }}',
                                    '{{ $gv->PhuTrachMon_User ?? '' }}',
                                    '{{ $gv->PhuTrachKhoi_User ?? '' }}'
                                )">Sửa</button>

                                <form method="POST"
                                      action="{{ route('admin.giao-vien.destroy', $gv->ID_User) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Xóa giáo viên {{ addslashes($gv->HoVaTen_User) }}?\nGiáo viên đang phụ trách lớp học sẽ không thể xóa.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="empty-notice">Chưa có giáo viên nào</td></tr>
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
            <span class="modal-header-title" id="modalTitle">Thêm giáo viên mới</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="modalForm" method="POST" action="{{ route('admin.giao-vien.store') }}">
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

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Môn phụ trách</label>
                        <select class="form-select" name="PhuTrachMon_User" id="f_mon">
                            <option value="">— Chưa phân công —</option>
                            @foreach ($monHocs as $mon)
                                <option value="{{ $mon->ID_MonHoc }}">{{ $mon->Ten_MonHoc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Khối phụ trách</label>
                        <select class="form-select" name="PhuTrachKhoi_User" id="f_khoi">
                            <option value="">— Chưa phân công —</option>
                            @foreach ($khoiLops as $khoi)
                                <option value="{{ $khoi->ID_KhoiLop }}">{{ $khoi->Ten_KhoiLop }}</option>
                            @endforeach
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
    window.PAGE_ACTIVE = 'quanly-giaovien';

    const storeUrl   = "{{ route('admin.giao-vien.store') }}";
    const updateBase = "{{ url('admin/giao-vien') }}";

    function openAdd() {
        document.getElementById('modalTitle').textContent   = 'Thêm giáo viên mới';
        document.getElementById('modalForm').action         = storeUrl;
        document.getElementById('formMethod').value         = 'POST';
        document.getElementById('submitBtn').textContent    = 'Thêm';
        document.getElementById('f_ten').value              = '';
        document.getElementById('f_email').value            = '';
        document.getElementById('f_sdt').value              = '';
        document.getElementById('f_ngaysinh').value         = '';
        document.getElementById('f_trangthai').value        = 'active';
        document.getElementById('f_mon').value              = '';
        document.getElementById('f_khoi').value             = '';
        document.getElementById('f_matkhau').value          = '';
        document.getElementById('f_matkhau').required       = true;
        document.getElementById('pwRequired').style.display = '';
        document.getElementById('pwHint').style.display     = 'none';
        document.getElementById('modalOverlay').style.display = 'flex';
    }

    function openEdit(id, ten, email, sdt, ngaysinh, trangthai, mon, khoi) {
        document.getElementById('modalTitle').textContent   = 'Sửa thông tin giáo viên';
        document.getElementById('modalForm').action         = updateBase + '/' + id;
        document.getElementById('formMethod').value         = 'PUT';
        document.getElementById('submitBtn').textContent    = 'Cập nhật';
        document.getElementById('f_ten').value              = ten;
        document.getElementById('f_email').value            = email;
        document.getElementById('f_sdt').value              = sdt;
        document.getElementById('f_ngaysinh').value         = ngaysinh;
        document.getElementById('f_trangthai').value        = trangthai;
        document.getElementById('f_mon').value              = mon;
        document.getElementById('f_khoi').value             = khoi;
        document.getElementById('f_matkhau').value          = '';
        document.getElementById('f_matkhau').required       = false;
        document.getElementById('pwRequired').style.display = 'none';
        document.getElementById('pwHint').style.display     = '';
        document.getElementById('modalOverlay').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('modalOverlay').style.display = 'none';
    }

    function closeOnBackdrop(e) {
        if (e.target === document.getElementById('modalOverlay')) closeModal();
    }

    function filterTable() {
        const q    = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#giaoVienTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
