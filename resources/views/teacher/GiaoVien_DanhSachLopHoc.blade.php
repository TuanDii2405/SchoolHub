<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giáo viên – Danh sách lớp học</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .student-row { display:grid;grid-template-columns:30px 1fr 1fr;gap:6px 12px;
                       padding:6px 0;border-bottom:1px solid var(--border-color);font-size:13px;align-items:center; }
        .student-row:last-child { border-bottom:none; }
        .student-num { color:var(--text-soft);text-align:center; }
        .detail-meta { display:flex;gap:24px;flex-wrap:wrap;margin-bottom:14px; }
        .detail-meta span { font-size:13px;color:var(--text-soft); }
        .detail-meta strong { color:var(--text-main); }
    </style>
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ GIÁO VIÊN</h2></div>
        <div class="content-box">
            <div class="section-title blue">Danh sách lớp học</div>
            <div class="action-bar">
                <input class="search-input" type="text" id="searchInput"
                       placeholder="Tìm tên lớp, môn, khối..." oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>
            <div class="table-wrap">
                <table class="tbl" id="tbl">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên lớp học</th>
                            <th>Khối lớp</th>
                            <th>Môn học</th>
                            <th>Năm học</th>
                            <th>Số học sinh</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lopHocs as $i => $lh)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $lh->TenLopHoc }}</td>
                            <td>{{ $lh->Ten_KhoiLop }}</td>
                            <td>{{ $lh->Ten_MonHoc }}</td>
                            <td>{{ $lh->NamHoc }}</td>
                            <td>{{ $lh->so_hoc_sinh }}</td>
                            <td>
                                <button class="tbl-link"
                                        onclick='openDetail(@json($lh))'>Xem chi tiết</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="empty-notice">Bạn chưa phụ trách lớp học nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

{{-- Modal chi tiết lớp --}}
<div id="modalDetail" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeDetail()">
    <div class="modal-box" style="width:580px;max-height:80vh;display:flex;flex-direction:column">
        <div class="modal-header">
            <span class="modal-header-title" id="detailTitle">Chi tiết lớp học</span>
            <button class="modal-close" onclick="closeDetail()">×</button>
        </div>
        <div class="modal-body" style="overflow-y:auto;flex:1">
            <div class="detail-meta" id="detailMeta"></div>
            <div style="font-size:13px;font-weight:700;color:var(--cerulean);margin-bottom:8px">
                Danh sách học sinh
            </div>
            <div id="detailStudents"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeDetail()">Đóng</button>
        </div>
    </div>
</div>

<script>
    window.PAGE_ROLE   = 'giaovien';
    window.PAGE_ACTIVE = 'ds-lophoc';

    const allStudents = @json($studentsPerClass);

    function openDetail(lh) {
        document.getElementById('detailTitle').textContent =
            'Chi tiết lớp: ' + lh.TenLopHoc;

        document.getElementById('detailMeta').innerHTML =
            `<span><strong>Môn:</strong> ${lh.Ten_MonHoc}</span>
             <span><strong>Khối:</strong> ${lh.Ten_KhoiLop}</span>
             <span><strong>Năm học:</strong> ${lh.NamHoc}</span>
             <span><strong>Sĩ số:</strong> ${lh.so_hoc_sinh} học sinh</span>`;

        const students = allStudents[lh.ID_LopHoc] || [];
        const box = document.getElementById('detailStudents');

        if (students.length === 0) {
            box.innerHTML = '<p style="color:var(--text-soft);font-size:13px">Lớp chưa có học sinh nào.</p>';
        } else {
            box.innerHTML = students.map((s, i) => {
                const dob = s.NgayThangNamSinh_User
                    ? new Date(s.NgayThangNamSinh_User).toLocaleDateString('vi-VN') : '—';
                const email = s.EmailCaNhan_User || '—';
                return `<div class="student-row">
                    <span class="student-num">${i + 1}</span>
                    <span><strong>${s.HoVaTen_User}</strong><br>
                        <span style="color:var(--text-soft)">${email}</span></span>
                    <span style="color:var(--text-soft)">Sinh: ${dob}<br>
                        Tham gia: ${new Date(s.NgayThamGia).toLocaleDateString('vi-VN')}</span>
                </div>`;
            }).join('');
        }

        document.getElementById('modalDetail').style.display = 'flex';
    }

    function closeDetail() {
        document.getElementById('modalDetail').style.display = 'none';
    }

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
