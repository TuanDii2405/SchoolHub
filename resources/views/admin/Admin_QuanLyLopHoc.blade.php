<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Quản lý lớp học</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ ADMIN</h2></div>
        <div class="content-box">
            <div class="section-title">Quản lý lớp học</div>

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
                <button class="action-btn" onclick="openAdd()">+ Thêm lớp mới</button>
                <input class="search-input" type="text" id="searchInput"
                       placeholder="Tìm tên lớp, giáo viên, môn..." oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="lopHocTable">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên lớp</th>
                            <th>Năm học</th>
                            <th>Khối</th>
                            <th>Môn học</th>
                            <th>Giáo viên phụ trách</th>
                            <th>Số học sinh</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lopHocs as $i => $lh)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $lh->TenLopHoc }}</td>
                            <td>{{ $lh->NamHoc }}</td>
                            <td>{{ $lh->Ten_KhoiLop }}</td>
                            <td>{{ $lh->Ten_MonHoc }}</td>
                            <td>{{ $lh->ten_giao_vien }}</td>
                            <td>
                                <button class="tbl-link"
                                        onclick="xemThanhVien({{ $lh->ID_LopHoc }}, '{{ addslashes($lh->TenLopHoc) }}')">
                                    {{ $lh->so_hoc_sinh }} học sinh
                                </button>
                            </td>
                            <td>
                                <button class="btn-edit" onclick="openEdit(
                                    {{ $lh->ID_LopHoc }},
                                    '{{ addslashes($lh->TenLopHoc) }}',
                                    '{{ $lh->NamHoc }}',
                                    {{ $lh->ID_KhoiLop }},
                                    {{ $lh->ID_MonHoc }},
                                    {{ $lh->ID_Teacher }}
                                )">Sửa</button>

                                <form method="POST"
                                      action="{{ route('admin.lop-hoc.destroy', $lh->ID_LopHoc) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Xóa lớp {{ addslashes($lh->TenLopHoc) }}?\nToàn bộ danh sách thành viên sẽ bị xóa theo.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="empty-notice">Chưa có lớp học nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

{{-- ========== MODAL THÊM / SỬA LỚP HỌC ========== --}}
<div id="modalOverlay" class="modal-overlay" style="display:none" onclick="closeOnBackdrop(event)">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-header-title" id="modalTitle">Thêm lớp học mới</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="modalForm" method="POST" action="{{ route('admin.lop-hoc.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tên lớp học <span class="required">*</span></label>
                        <input class="form-input" type="text" name="TenLopHoc" id="f_ten"
                               placeholder="VD: Lớp 10A1" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Năm học <span class="required">*</span></label>
                        <input class="form-input" type="text" name="NamHoc" id="f_namhoc"
                               placeholder="VD: 2024-2025" required maxlength="20">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Khối lớp <span class="required">*</span></label>
                        <select class="form-select" name="ID_KhoiLop" id="f_khoi" required>
                            <option value="">— Chọn khối —</option>
                            @foreach ($khoiLops as $kl)
                                <option value="{{ $kl->ID_KhoiLop }}">{{ $kl->Ten_KhoiLop }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Môn học <span class="required">*</span></label>
                        <select class="form-select" name="ID_MonHoc" id="f_mon" required>
                            <option value="">— Chọn môn —</option>
                            @foreach ($monHocs as $mh)
                                <option value="{{ $mh->ID_MonHoc }}">{{ $mh->Ten_MonHoc }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Giáo viên phụ trách <span class="required">*</span></label>
                    <select class="form-select" name="ID_Teacher" id="f_giaovien" required>
                        <option value="">— Chọn giáo viên —</option>
                        @foreach ($giaoViens as $gv)
                            <option value="{{ $gv->ID_User }}">{{ $gv->HoVaTen_User }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button type="submit" class="action-btn" id="submitBtn">Thêm</button>
            </div>
        </form>
    </div>
</div>

{{-- ========== MODAL XEM DANH SÁCH HỌC SINH ========== --}}
<div id="modalThanhVien" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeThanhVien()">
    <div class="modal-box" style="width:600px">
        <div class="modal-header">
            <span class="modal-header-title" id="tvTitle">Danh sách học sinh</span>
            <button class="modal-close" onclick="closeThanhVien()">×</button>
        </div>
        <div style="padding:16px 20px">
            <div id="tvLoading" style="text-align:center;color:var(--text-soft);padding:20px">Đang tải...</div>
            <div id="tvContent" style="display:none">
                <div class="table-wrap" style="max-height:300px;overflow-y:auto">
                    <table class="tbl">
                        <thead>
                            <tr><th>STT</th><th>Họ và tên</th><th>Email</th><th>Ngày tham gia</th><th></th></tr>
                        </thead>
                        <tbody id="tvBody"></tbody>
                    </table>
                </div>
            </div>

            {{-- Thêm học sinh vào lớp --}}
            <div id="tvAddSection" style="margin-top:14px;border-top:1px solid var(--border);padding-top:14px">
                <div class="form-label" style="margin-bottom:6px;font-weight:600">Thêm học sinh vào lớp</div>
                <form id="tvAddForm" method="POST" style="display:flex;gap:8px;align-items:flex-end">
                    @csrf
                    <select class="form-select" name="ID_Student" id="tv_student_select"
                            required style="flex:1">
                        <option value="">— Chọn học sinh —</option>
                    </select>
                    <button type="submit" class="action-btn" style="white-space:nowrap;height:38px">+ Thêm</button>
                </form>
            </div>
            <div id="tvNoAvailable" style="display:none;margin-top:14px;border-top:1px solid var(--border);
                 padding-top:12px;color:var(--text-soft);font-size:.9em">
                Tất cả học sinh đã có trong lớp này.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeThanhVien()">Đóng</button>
        </div>
    </div>
</div>

{{-- Dữ liệu thành viên nhúng sẵn để không cần AJAX --}}
<script>
    window.PAGE_ROLE   = 'admin';
    window.PAGE_ACTIVE = 'quanly-lophoc';

    // Dữ liệu thành viên từng lớp (nhúng từ server)
    const thanhVienData = {
        @foreach ($lopHocs as $lh)
        @php
            $members = \Illuminate\Support\Facades\DB::select(
                "SELECT u.ID_User, u.HoVaTen_User, u.EmailCaNhan_User, lv.NgayThamGia
                 FROM Lop_hoc_ThanhVien lv
                 JOIN `User` u ON lv.ID_Student = u.ID_User
                 WHERE lv.ID_LopHoc = ?
                 ORDER BY u.HoVaTen_User",
                [$lh->ID_LopHoc]
            );
            $available = \Illuminate\Support\Facades\DB::select(
                "SELECT u.ID_User, u.HoVaTen_User
                 FROM `User` u
                 WHERE u.PhanQuyen_User = 'student'
                   AND u.TrangThaiHoatDong_User = 'active'
                   AND u.ID_User NOT IN (
                       SELECT ID_Student FROM Lop_hoc_ThanhVien WHERE ID_LopHoc = ?
                   )
                 ORDER BY u.HoVaTen_User",
                [$lh->ID_LopHoc]
            );
        @endphp
        {{ $lh->ID_LopHoc }}: {
            ten: '{{ addslashes($lh->TenLopHoc) }}',
            hocSinhs: [
                @foreach ($members as $m)
                {
                    id: {{ $m->ID_User }},
                    ten: '{{ addslashes($m->HoVaTen_User) }}',
                    email: '{{ addslashes($m->EmailCaNhan_User ?? '') }}',
                    ngay: '{{ $m->NgayThamGia ? \Carbon\Carbon::parse($m->NgayThamGia)->format('d/m/Y') : '—' }}'
                },
                @endforeach
            ],
            hocSinhCoThe: [
                @foreach ($available as $s)
                { id: {{ $s->ID_User }}, ten: '{{ addslashes($s->HoVaTen_User) }}' },
                @endforeach
            ]
        },
        @endforeach
    };

    const storeUrl    = "{{ route('admin.lop-hoc.store') }}";
    const updateBase  = "{{ url('admin/lop-hoc') }}";
    const csrfToken   = document.querySelector('meta[name="csrf-token"]')?.content
                     || '{{ csrf_token() }}';

    function openAdd() {
        document.getElementById('modalTitle').textContent  = 'Thêm lớp học mới';
        document.getElementById('modalForm').action        = storeUrl;
        document.getElementById('formMethod').value        = 'POST';
        document.getElementById('submitBtn').textContent   = 'Thêm';
        document.getElementById('f_ten').value             = '';
        document.getElementById('f_namhoc').value          = '';
        document.getElementById('f_khoi').value            = '';
        document.getElementById('f_mon').value             = '';
        document.getElementById('f_giaovien').value        = '';
        document.getElementById('modalOverlay').style.display = 'flex';
    }

    function openEdit(id, ten, namhoc, khoi, mon, gv) {
        document.getElementById('modalTitle').textContent  = 'Sửa thông tin lớp học';
        document.getElementById('modalForm').action        = updateBase + '/' + id;
        document.getElementById('formMethod').value        = 'PUT';
        document.getElementById('submitBtn').textContent   = 'Cập nhật';
        document.getElementById('f_ten').value             = ten;
        document.getElementById('f_namhoc').value          = namhoc;
        document.getElementById('f_khoi').value            = khoi;
        document.getElementById('f_mon').value             = mon;
        document.getElementById('f_giaovien').value        = gv;
        document.getElementById('modalOverlay').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('modalOverlay').style.display = 'none';
    }

    function closeOnBackdrop(e) {
        if (e.target === document.getElementById('modalOverlay')) closeModal();
    }

    function xemThanhVien(id, tenLop) {
        const data = thanhVienData[id];
        document.getElementById('tvTitle').textContent          = 'Học sinh – ' + tenLop;
        document.getElementById('tvLoading').style.display      = 'none';
        document.getElementById('tvContent').style.display      = '';

        // Danh sách học sinh hiện tại
        const tbody = document.getElementById('tvBody');
        if (!data || data.hocSinhs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="empty-notice">Chưa có học sinh nào trong lớp</td></tr>';
        } else {
            tbody.innerHTML = data.hocSinhs.map((hs, i) => {
                const removeUrl = updateBase + '/' + id + '/hoc-sinh/' + hs.id;
                return `<tr>
                    <td>${i + 1}</td>
                    <td>${hs.ten}</td>
                    <td>${hs.email || '—'}</td>
                    <td>${hs.ngay}</td>
                    <td>
                        <form method="POST" action="${removeUrl}" style="margin:0"
                              onsubmit="return confirm('Xóa ${hs.ten} khỏi lớp?')">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn-danger" style="padding:2px 8px;font-size:.8em">Xóa</button>
                        </form>
                    </td>
                </tr>`;
            }).join('');
        }

        // Form thêm học sinh
        const addForm = document.getElementById('tvAddForm');
        addForm.action = updateBase + '/' + id + '/them-hoc-sinh';

        const sel = document.getElementById('tv_student_select');
        sel.innerHTML = '<option value="">— Chọn học sinh —</option>';

        if (data && data.hocSinhCoThe.length > 0) {
            data.hocSinhCoThe.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.ten;
                sel.appendChild(opt);
            });
            document.getElementById('tvAddSection').style.display   = '';
            document.getElementById('tvNoAvailable').style.display  = 'none';
        } else {
            document.getElementById('tvAddSection').style.display   = 'none';
            document.getElementById('tvNoAvailable').style.display  = '';
        }

        document.getElementById('modalThanhVien').style.display = 'flex';
    }

    function closeThanhVien() {
        document.getElementById('modalThanhVien').style.display = 'none';
    }

    function filterTable() {
        const q    = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#lopHocTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
