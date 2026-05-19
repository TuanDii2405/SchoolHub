<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giáo viên – Đơn xin nghỉ</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .badge-pending  { background:#fd7e14;color:#fff;padding:2px 10px;border-radius:10px;font-size:12px; }
        .badge-approved { background:#198754;color:#fff;padding:2px 10px;border-radius:10px;font-size:12px; }
        .badge-rejected { background:#dc3545;color:#fff;padding:2px 10px;border-radius:10px;font-size:12px; }
    </style>
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ GIÁO VIÊN</h2></div>
        <div class="content-box">
            <div class="section-title blue">Duyệt đơn xin nghỉ</div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="action-bar">
                <select class="search-input" id="filterTT" onchange="filterTable()" style="width:180px">
                    <option value="">— Tất cả trạng thái —</option>
                    <option value="pending">Chờ duyệt</option>
                    <option value="approved">Đã duyệt</option>
                    <option value="rejected">Đã từ chối</option>
                </select>
                <input class="search-input" type="text" id="searchInput"
                       placeholder="Tìm học sinh, lớp..." oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="tbl" style="table-layout:fixed">
                    <colgroup>
                        <col style="width:50px"><col style="width:160px"><col style="width:120px">
                        <col style="width:100px"><col><col style="width:110px"><col style="width:130px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>STT</th><th>Học sinh</th><th>Lớp</th>
                            <th>Ngày xin nghỉ</th><th>Lý do</th><th>Trạng thái</th><th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($donNghis as $i => $don)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td style="text-align:left">{{ $don->ten_hoc_sinh }}</td>
                            <td>{{ $don->TenLopHoc }}</td>
                            <td>{{ \Carbon\Carbon::parse($don->NgayHoc_DiemDanh)->format('d/m/Y') }}</td>
                            <td style="text-align:left;word-break:break-word">{{ $don->NoiDung_DonXinNghi }}</td>
                            <td>
                                <span class="badge-{{ $don->TrangThai_DonXinNghi }}">
                                    {{ ['pending'=>'Chờ duyệt','approved'=>'Đã duyệt','rejected'=>'Từ chối'][$don->TrangThai_DonXinNghi] ?? $don->TrangThai_DonXinNghi }}
                                </span>
                            </td>
                            <td style="white-space:nowrap">
                                @if ($don->TrangThai_DonXinNghi === 'pending')
                                <form method="POST"
                                      action="{{ route('teacher.don-xin-nghi.update', $don->ID_DonXinNghi) }}"
                                      style="display:inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="TrangThai_DonXinNghi" value="approved">
                                    <button type="submit" class="btn-edit"
                                            onclick="return confirm('Duyệt đơn xin nghỉ này?')">Duyệt</button>
                                </form>
                                <form method="POST"
                                      action="{{ route('teacher.don-xin-nghi.update', $don->ID_DonXinNghi) }}"
                                      style="display:inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="TrangThai_DonXinNghi" value="rejected">
                                    <button type="submit" class="btn-danger"
                                            onclick="return confirm('Từ chối đơn xin nghỉ này?')">Từ chối</button>
                                </form>
                                @else
                                <span style="color:var(--text-soft);font-size:13px">Đã xử lý</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="empty-notice">Chưa có đơn xin nghỉ nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script>
    window.PAGE_ROLE   = 'giaovien';
    window.PAGE_ACTIVE = 'don-xin-nghi';

    function filterTable() {
        const q  = document.getElementById('searchInput').value.toLowerCase();
        const tt = document.getElementById('filterTT').value.toLowerCase();
        document.querySelectorAll('#tbl tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = (text.includes(q) && (!tt || text.includes(
                {'pending':'chờ duyệt','approved':'đã duyệt','rejected':'từ chối'}[tt] || tt
            ))) ? '' : 'none';
        });
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
