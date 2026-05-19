<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giáo viên – Quản lý điểm danh</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .badge-scheduled  { background:#6c757d;color:#fff;padding:2px 8px;border-radius:10px;font-size:12px; }
        .badge-in_progress{ background:#fd7e14;color:#fff;padding:2px 8px;border-radius:10px;font-size:12px; }
        .badge-completed  { background:#198754;color:#fff;padding:2px 8px;border-radius:10px;font-size:12px; }
        .badge-cancelled  { background:#dc3545;color:#fff;padding:2px 8px;border-radius:10px;font-size:12px; }
        .status-present { color:#198754;font-weight:600; }
        .status-absent  { color:#dc3545;font-weight:600; }
        .status-late    { color:#fd7e14;font-weight:600; }
        .status-excused { color:#6c757d;font-weight:600; }
        .student-row    { display:grid;grid-template-columns:1fr 180px;align-items:center;gap:8px;
                          padding:6px 0;border-bottom:1px solid var(--border-color); }
    </style>
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ GIÁO VIÊN</h2></div>
        <div class="content-box">
            <div class="section-title blue">Quản lý điểm danh</div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="action-bar">
                <button class="action-btn" onclick="openCreate()">+ Tạo buổi điểm danh</button>
                <input class="search-input" type="text" id="searchInput"
                       placeholder="Tìm lớp, ngày..." oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="tbl">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Lớp học</th>
                            <th>Môn học</th>
                            <th>Ngày học</th>
                            <th>Bắt đầu</th>
                            <th>Kết thúc</th>
                            <th>Sĩ số</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sessions as $i => $dd)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $dd->TenLopHoc }}</td>
                            <td>{{ $dd->Ten_MonHoc }}</td>
                            <td>{{ \Carbon\Carbon::parse($dd->NgayHoc_DiemDanh)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($dd->ThoiGianBatDau_DiemDanh)->format('H:i') }}</td>
                            <td>{{ $dd->ThoiGianKetThuc_DiemDanh ? \Carbon\Carbon::parse($dd->ThoiGianKetThuc_DiemDanh)->format('H:i') : '—' }}</td>
                            <td>{{ $dd->so_hoc_sinh }}</td>
                            <td>
                                <span class="badge-{{ $dd->TrangThaiBuoiHoc_DiemDanh }}">
                                    {{ ['scheduled'=>'Đã lên lịch','in_progress'=>'Đang diễn ra',
                                        'completed'=>'Hoàn thành','cancelled'=>'Đã hủy'][$dd->TrangThaiBuoiHoc_DiemDanh] ?? $dd->TrangThaiBuoiHoc_DiemDanh }}
                                </span>
                            </td>
                            <td style="white-space:nowrap">
                                <button class="btn-edit"
                                        onclick='openMark(@json($dd))'>Điểm danh</button>
                                <form method="POST"
                                      action="{{ route('teacher.diem-danh.destroy', $dd->ID_DiemDanh) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Xóa buổi điểm danh này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="empty-notice">Chưa có buổi điểm danh nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

{{-- Modal tạo buổi --}}
<div id="modalCreate" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeCreate()">
    <div class="modal-box" style="width:460px">
        <div class="modal-header">
            <span class="modal-header-title">Tạo buổi điểm danh</span>
            <button class="modal-close" onclick="closeCreate()">×</button>
        </div>
        <form method="POST" action="{{ route('teacher.diem-danh.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Lớp học <span class="required">*</span></label>
                    <select class="form-select" name="ID_LopHoc" required>
                        <option value="">— Chọn lớp —</option>
                        @foreach ($lopHocs as $lop)
                            <option value="{{ $lop->ID_LopHoc }}">{{ $lop->TenLopHoc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ngày học <span class="required">*</span></label>
                    <input class="form-input" type="date" name="NgayHoc_DiemDanh" required
                           value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Giờ bắt đầu <span class="required">*</span></label>
                        <input class="form-input" type="time" name="ThoiGianBatDau_DiemDanh" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Giờ kết thúc</label>
                        <input class="form-input" type="time" name="ThoiGianKetThuc_DiemDanh">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Trạng thái <span class="required">*</span></label>
                    <select class="form-select" name="TrangThaiBuoiHoc_DiemDanh" required>
                        <option value="scheduled">Đã lên lịch</option>
                        <option value="in_progress">Đang diễn ra</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeCreate()">Hủy</button>
                <button type="submit" class="action-btn">Tạo buổi</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal điểm danh học sinh --}}
<div id="modalMark" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeMark()">
    <div class="modal-box" style="width:520px;max-height:80vh;display:flex;flex-direction:column">
        <div class="modal-header">
            <span class="modal-header-title" id="markTitle">Điểm danh buổi học</span>
            <button class="modal-close" onclick="closeMark()">×</button>
        </div>
        <form id="markForm" method="POST" action="">
            @csrf @method('PUT')
            <div class="modal-body" style="overflow-y:auto;flex:1">
                <div class="form-group" style="margin-bottom:12px">
                    <label class="form-label">Trạng thái buổi học</label>
                    <select class="form-select" name="TrangThaiBuoiHoc_DiemDanh" id="markTrangThai">
                        <option value="scheduled">Đã lên lịch</option>
                        <option value="in_progress">Đang diễn ra</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
                <div style="font-size:13px;font-weight:700;color:var(--cerulean);margin-bottom:8px">
                    Điểm danh học sinh
                </div>
                <div id="studentList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeMark()">Hủy</button>
                <button type="submit" class="action-btn">Lưu điểm danh</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.PAGE_ROLE   = 'giaovien';
    window.PAGE_ACTIVE = 'diemdanh';

    const updateBase    = "{{ url('giao-vien/diem-danh') }}";
    const allStudents   = @json($studentsPerClass);

    function openCreate() { document.getElementById('modalCreate').style.display = 'flex'; }
    function closeCreate() { document.getElementById('modalCreate').style.display = 'none'; }

    function openMark(dd) {
        const lopId    = dd.lop_id || dd.ID_LopHoc;
        const students = allStudents[lopId] || [];
        const existing = dd.ChiTietDiemDanh_DiemDanh
            ? JSON.parse(dd.ChiTietDiemDanh_DiemDanh) : {};

        document.getElementById('markTitle').textContent =
            'Điểm danh: ' + dd.TenLopHoc + ' – ' +
            new Date(dd.NgayHoc_DiemDanh).toLocaleDateString('vi-VN');
        document.getElementById('markForm').action = updateBase + '/' + dd.ID_DiemDanh;
        document.getElementById('markTrangThai').value = dd.TrangThaiBuoiHoc_DiemDanh;

        const list   = document.getElementById('studentList');
        const labels = { present:'Có mặt', absent:'Vắng mặt', late:'Đi trễ', excused:'Có phép' };

        if (students.length === 0) {
            list.innerHTML = '<p style="color:var(--text-soft);font-size:13px">Lớp chưa có học sinh nào.</p>';
        } else {
            list.innerHTML = students.map(s => {
                const cur = existing[s.ID_User] || 'present';
                const opts = Object.entries(labels).map(([v, lbl]) =>
                    `<option value="${v}"${v === cur ? ' selected' : ''}>${lbl}</option>`
                ).join('');
                return `<div class="student-row">
                    <span style="font-size:13px">${s.HoVaTen_User}</span>
                    <select class="form-select" name="chi_tiet[${s.ID_User}]" style="padding:4px 8px;font-size:13px">${opts}</select>
                </div>`;
            }).join('');
        }

        document.getElementById('modalMark').style.display = 'flex';
    }

    function closeMark() { document.getElementById('modalMark').style.display = 'none'; }

    function filterTable() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('#tbl tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
