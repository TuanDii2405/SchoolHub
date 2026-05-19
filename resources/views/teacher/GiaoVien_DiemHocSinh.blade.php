<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giáo viên – Điểm học sinh</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .score-high  { color:#198754;font-weight:700; }
        .score-mid   { color:#fd7e14;font-weight:700; }
        .score-low   { color:#dc3545;font-weight:700; }
    </style>
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ GIÁO VIÊN</h2></div>
        <div class="content-box">
            <div class="section-title blue">Điểm học sinh</div>

            <div class="action-bar">
                <select class="search-input" id="filterLop" onchange="filterTable()" style="width:200px">
                    <option value="">— Tất cả lớp —</option>
                    @foreach ($lopHocs as $lop)
                        <option value="{{ $lop->TenLopHoc }}">{{ $lop->TenLopHoc }}</option>
                    @endforeach
                </select>
                <input class="search-input" type="text" id="searchInput"
                       placeholder="Tìm học sinh, kỳ thi..." oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="tbl" style="table-layout:fixed">
                    <colgroup>
                        <col style="width:50px"><col style="width:170px"><col>
                        <col style="width:110px"><col style="width:90px"><col style="width:90px">
                        <col style="width:90px"><col style="width:100px"><col style="width:130px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>STT</th><th>Học sinh</th><th>Kỳ thi</th>
                            <th>Lớp</th><th>4PA</th><th>Đúng/Sai</th>
                            <th>Trả lời ngắn</th><th>Tổng điểm</th><th>Thời gian nộp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($diemSos as $i => $ds)
                        @php $tong = (float) $ds->TongDiem_DiemSo; @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td style="text-align:left">{{ $ds->ten_hoc_sinh }}</td>
                            <td style="text-align:left;word-break:break-word">{{ $ds->Ten_KyThi }}</td>
                            <td>{{ $ds->TenLopHoc }}</td>
                            <td>{{ number_format($ds->DiemPhanTracNghiem4PhuongAn_DiemSo, 2) }}</td>
                            <td>{{ number_format($ds->DiemPhanTracNghiemDungSai_DiemSo, 2) }}</td>
                            <td>{{ number_format($ds->DiemPhanTracNghiemTraLoiNgan_DiemSo, 2) }}</td>
                            <td>
                                <span class="{{ $tong >= 8 ? 'score-high' : ($tong >= 5 ? 'score-mid' : 'score-low') }}">
                                    {{ number_format($tong, 2) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($ds->ThoiGianKetThuc_DiemSo)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="empty-notice">Chưa có dữ liệu điểm nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script>
    window.PAGE_ROLE   = 'giaovien';
    window.PAGE_ACTIVE = 'diem-hoc-sinh';

    function filterTable() {
        const q    = document.getElementById('searchInput').value.toLowerCase();
        const lop  = document.getElementById('filterLop').value.toLowerCase();
        document.querySelectorAll('#tbl tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = (text.includes(q) && (!lop || text.includes(lop))) ? '' : 'none';
        });
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
