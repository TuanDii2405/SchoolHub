<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Quản lý kỳ thi</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .modal-section {
            font-size: 12px;
            font-weight: 700;
            color: var(--cerulean);
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 6px 0 2px;
            border-bottom: 1px solid var(--cerulean-200);
            margin-bottom: 4px;
        }
        .form-input-sm { padding: 6px 9px; font-size: 13px; }
        .score-hint { font-size: 11px; color: var(--text-soft); margin-top: 2px; }
        #totalDiem { font-weight: 700; }
        #totalDiem.ok  { color: #256029; }
        #totalDiem.err { color: var(--jasper); }
    </style>
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ ADMIN</h2></div>
        <div class="content-box">
            <div class="section-title">Quản lý kỳ thi</div>

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
                <button class="action-btn" onclick="openAdd()">+ Tạo kỳ thi</button>
                <input class="search-input" type="text" id="searchInput"
                       placeholder="Tìm tên kỳ thi, môn, lớp..." oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="kyThiTable">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên kỳ thi</th>
                            <th>Lớp học</th>
                            <th>Môn</th>
                            <th>Chủ đề</th>
                            <th>Bắt đầu</th>
                            <th>TG (phút)</th>
                            <th>Số câu (4PA|DS|Ngắn)</th>
                            <th>Điểm (4PA|DS|Ngắn)</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kyThis as $i => $kt)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td style="text-align:left">{{ $kt->Ten_KyThi }}</td>
                            <td>{{ $kt->TenLopHoc }}</td>
                            <td>{{ $kt->Ten_MonHoc }}</td>
                            <td style="text-align:left;max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                                title="{{ $kt->NoiDung_ChuDe }}">{{ $kt->NoiDung_ChuDe }}</td>
                            <td>{{ $kt->ThoiGianBatDau_KyThi ? \Carbon\Carbon::parse($kt->ThoiGianBatDau_KyThi)->format('d/m/Y H:i') : '—' }}</td>
                            <td>{{ $kt->ThoiGianLamBai_KyThi }}</td>
                            <td>{{ $kt->SoCauHoiTracNghiem4PhuongAn_KyThi }}|{{ $kt->SoCauHoiTracNghiemDungSai_KyThi }}|{{ $kt->SoCauHoiTracNghiemTraLoiNgan_KyThi }}</td>
                            <td>{{ $kt->PhanBoDiemTracNghiem4PhuongAn_KyThi }}|{{ $kt->PhanBoDiemTracNghiemDungSai_KyThi }}|{{ $kt->PhanBoDiemTracNghiemTraLoiNgan_KyThi }}</td>
                            <td>
                                <button class="btn-edit" onclick='openEdit(@json($kt))'>Sửa</button>

                                <form method="POST"
                                      action="{{ route('admin.ky-thi.destroy', $kt->ID_KyThi) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Xóa kỳ thi {{ addslashes($kt->Ten_KyThi) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="empty-notice">Chưa có kỳ thi nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

{{-- ========== MODAL THÊM / SỬA KỲ THI ========== --}}
<div id="modalOverlay" class="modal-overlay" style="display:none" onclick="closeOnBackdrop(event)">
    <div class="modal-box" style="width:580px">
        <div class="modal-header">
            <span class="modal-header-title" id="modalTitle">Tạo kỳ thi mới</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="modalForm" method="POST" action="{{ route('admin.ky-thi.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body">
                {{-- Thông tin cơ bản --}}
                <div class="modal-section">Thông tin cơ bản</div>

                <div class="form-group">
                    <label class="form-label">Tên kỳ thi <span class="required">*</span></label>
                    <input class="form-input" type="text" name="Ten_KyThi" id="f_ten"
                           placeholder="VD: Kiểm tra học kỳ 1 – Toán 10A1" required maxlength="150">
                </div>

                <div class="form-group">
                    <label class="form-label">Mô tả</label>
                    <input class="form-input" type="text" name="MoTa_KyThi" id="f_mota"
                           placeholder="Ghi chú thêm về kỳ thi" maxlength="255">
                </div>

                {{-- Phân loại --}}
                <div class="modal-section" style="margin-top:8px">Phân loại</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Khối lớp <span class="required">*</span></label>
                        <select class="form-select" name="ID_KhoiLop" id="f_khoi" required onchange="cascadeFilter()">
                            <option value="">— Chọn khối —</option>
                            @foreach ($khoiLops as $kl)
                                <option value="{{ $kl->ID_KhoiLop }}">{{ $kl->Ten_KhoiLop }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Môn học <span class="required">*</span></label>
                        <select class="form-select" name="ID_MonHoc" id="f_mon" required onchange="cascadeFilter()">
                            <option value="">— Chọn môn —</option>
                            @foreach ($monHocs as $mh)
                                <option value="{{ $mh->ID_MonHoc }}">{{ $mh->Ten_MonHoc }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Lớp học <span class="required">*</span></label>
                        <select class="form-select" name="ID_LopHoc" id="f_lophoc" required>
                            <option value="">— Chọn lớp —</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Chủ đề <span class="required">*</span></label>
                        <select class="form-select" name="ID_ChuDe" id="f_chude" required>
                            <option value="">— Chọn chủ đề —</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Đề thi <span class="required">*</span></label>
                    <select class="form-select" name="ID_MaDeThi" id="f_dethi" required onchange="loadDeThiCount()">
                        <option value="">— Chọn đề thi —</option>
                    </select>
                    <div id="dethi-count-hint" style="margin-top:4px;font-size:12px;color:var(--text-soft);display:none">
                        Số câu trong đề thi:
                        <strong>4PA: <span id="hint-4pa">0</span></strong> &nbsp;|&nbsp;
                        <strong>DS: <span id="hint-ds">0</span></strong> &nbsp;|&nbsp;
                        <strong>Ngắn: <span id="hint-ngan">0</span></strong>
                        &nbsp;<span style="color:var(--jasper);font-style:italic" id="hint-warn"></span>
                    </div>
                </div>

                {{-- Thời gian --}}
                <div class="modal-section" style="margin-top:8px">Thời gian</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Thời gian bắt đầu</label>
                        <input class="form-input" type="datetime-local" name="ThoiGianBatDau_KyThi" id="f_batdau">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Thời gian kết thúc</label>
                        <input class="form-input" type="datetime-local" name="ThoiGianKetThuc_KyThi" id="f_ketthuc">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Thời gian làm bài (phút) <span class="required">*</span></label>
                    <input class="form-input" type="number" name="ThoiGianLamBai_KyThi" id="f_thoigian"
                           min="1" max="300" placeholder="VD: 45" required>
                </div>

                {{-- Số câu hỏi --}}
                <div class="modal-section" style="margin-top:8px">Số câu hỏi</div>

                <div class="form-row" style="grid-template-columns:1fr 1fr 1fr">
                    <div class="form-group">
                        <label class="form-label">4 phương án <span class="required">*</span></label>
                        <input class="form-input" type="number" name="SoCauHoiTracNghiem4PhuongAn_KyThi"
                               id="f_so4pa" min="0" value="0" required oninput="calcDiem();recheck()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Đúng / Sai <span class="required">*</span></label>
                        <input class="form-input" type="number" name="SoCauHoiTracNghiemDungSai_KyThi"
                               id="f_sods" min="0" value="0" required oninput="calcDiem();recheck()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Trả lời ngắn <span class="required">*</span></label>
                        <input class="form-input" type="number" name="SoCauHoiTracNghiemTraLoiNgan_KyThi"
                               id="f_songan" min="0" value="0" required oninput="calcDiem();recheck()">
                    </div>
                </div>

                {{-- Phân bổ điểm --}}
                <div class="modal-section" style="margin-top:8px">Phân bổ điểm (tổng = 10)</div>

                <div class="form-row" style="grid-template-columns:1fr 1fr 1fr">
                    <div class="form-group">
                        <label class="form-label">4 phương án <span class="required">*</span></label>
                        <input class="form-input" type="number" name="PhanBoDiemTracNghiem4PhuongAn_KyThi"
                               id="f_d4pa" min="0" max="10" step="0.25" value="0" required oninput="calcDiem()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Đúng / Sai <span class="required">*</span></label>
                        <input class="form-input" type="number" name="PhanBoDiemTracNghiemDungSai_KyThi"
                               id="f_dds" min="0" max="10" step="0.25" value="0" required oninput="calcDiem()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Trả lời ngắn <span class="required">*</span></label>
                        <input class="form-input" type="number" name="PhanBoDiemTracNghiemTraLoiNgan_KyThi"
                               id="f_dngan" min="0" max="10" step="0.25" value="0" required oninput="calcDiem()">
                    </div>
                </div>
                <div class="score-hint">Tổng điểm hiện tại:
                    <span id="totalDiem" class="err">0</span> / 10
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button type="submit" class="action-btn" id="submitBtn">Tạo</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.PAGE_ROLE   = 'admin';
    window.PAGE_ACTIVE = 'quanly-kythi';

    const storeUrl   = "{{ route('admin.ky-thi.store') }}";
    const updateBase = "{{ url('admin/ky-thi') }}";

    // Dữ liệu cascade từ server
    const allChuDe  = @json($chuDesAll);
    const allLopHoc = @json($lopHocsAll);
    const allDeThi  = @json($deThisAll);

    function cascadeFilter() {
        const khoi = parseInt(document.getElementById('f_khoi').value) || 0;
        const mon  = parseInt(document.getElementById('f_mon').value)  || 0;

        fillSelect('f_lophoc', allLopHoc,
            l => (!khoi || l.ID_KhoiLop === khoi) && (!mon || l.ID_MonHoc === mon),
            l => l.ID_LopHoc, l => l.TenLopHoc, '— Chọn lớp —');

        fillSelect('f_chude', allChuDe,
            c => (!khoi || c.ID_KhoiLop === khoi) && (!mon || c.ID_MonHoc === mon),
            c => c.ID_ChuDe, c => c.NoiDung_ChuDe, '— Chọn chủ đề —');

        fillSelect('f_dethi', allDeThi,
            d => (!khoi || d.ID_MaKhoi === khoi) && (!mon || d.ID_MaMon === mon),
            d => d.ID_MaDeThi, d => d.TenDeThi, '— Chọn đề thi —');
    }

    function fillSelect(id, data, filterFn, valFn, labelFn, placeholder) {
        const sel = document.getElementById(id);
        const cur = sel.value;
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        data.filter(filterFn).forEach(item => {
            const opt = document.createElement('option');
            opt.value = valFn(item);
            opt.textContent = labelFn(item);
            if (String(opt.value) === String(cur)) opt.selected = true;
            sel.appendChild(opt);
        });
    }

    function calcDiem() {
        const total = (parseFloat(document.getElementById('f_d4pa').value)  || 0)
                    + (parseFloat(document.getElementById('f_dds').value)   || 0)
                    + (parseFloat(document.getElementById('f_dngan').value) || 0);
        const el = document.getElementById('totalDiem');
        el.textContent = Math.round(total * 100) / 100;
        el.className   = Math.abs(total - 10) < 0.001 ? 'ok' : 'err';
    }

    function resetForm() {
        ['f_ten','f_mota','f_batdau','f_ketthuc'].forEach(id => document.getElementById(id).value = '');
        ['f_khoi','f_mon','f_lophoc','f_chude','f_dethi'].forEach(id => document.getElementById(id).value = '');
        ['f_so4pa','f_sods','f_songan','f_d4pa','f_dds','f_dngan'].forEach(id => document.getElementById(id).value = 0);
        document.getElementById('f_thoigian').value = '';
        cascadeFilter();
        calcDiem();
    }

    function openAdd() {
        document.getElementById('modalTitle').textContent = 'Tạo kỳ thi mới';
        document.getElementById('modalForm').action       = storeUrl;
        document.getElementById('formMethod').value       = 'POST';
        document.getElementById('submitBtn').textContent  = 'Tạo';
        resetForm();
        document.getElementById('modalOverlay').style.display = 'flex';
        document.getElementById('f_ten').focus();
    }

    function openEdit(kt) {
        document.getElementById('modalTitle').textContent = 'Sửa kỳ thi';
        document.getElementById('modalForm').action       = updateBase + '/' + kt.ID_KyThi;
        document.getElementById('formMethod').value       = 'PUT';
        document.getElementById('submitBtn').textContent  = 'Cập nhật';

        document.getElementById('f_ten').value     = kt.Ten_KyThi   || '';
        document.getElementById('f_mota').value    = kt.MoTa_KyThi  || '';
        document.getElementById('f_thoigian').value= kt.ThoiGianLamBai_KyThi || '';
        document.getElementById('f_so4pa').value   = kt.SoCauHoiTracNghiem4PhuongAn_KyThi   || 0;
        document.getElementById('f_sods').value    = kt.SoCauHoiTracNghiemDungSai_KyThi     || 0;
        document.getElementById('f_songan').value  = kt.SoCauHoiTracNghiemTraLoiNgan_KyThi  || 0;
        document.getElementById('f_d4pa').value    = kt.PhanBoDiemTracNghiem4PhuongAn_KyThi  || 0;
        document.getElementById('f_dds').value     = kt.PhanBoDiemTracNghiemDungSai_KyThi    || 0;
        document.getElementById('f_dngan').value   = kt.PhanBoDiemTracNghiemTraLoiNgan_KyThi || 0;

        // datetime-local cần format YYYY-MM-DDTHH:MM
        document.getElementById('f_batdau').value  = kt.ThoiGianBatDau_KyThi
            ? kt.ThoiGianBatDau_KyThi.replace(' ', 'T').substring(0, 16) : '';
        document.getElementById('f_ketthuc').value = kt.ThoiGianKetThuc_KyThi
            ? kt.ThoiGianKetThuc_KyThi.replace(' ', 'T').substring(0, 16) : '';

        // Set khối + môn trước để cascade lọc đúng
        document.getElementById('f_khoi').value = kt.ID_KhoiLop || '';
        document.getElementById('f_mon').value  = kt.ID_MonHoc  || '';
        cascadeFilter();

        // Sau khi cascade, set lại giá trị đã chọn
        document.getElementById('f_lophoc').value = kt.ID_LopHoc  || '';
        document.getElementById('f_chude').value  = kt.ID_ChuDe   || '';
        document.getElementById('f_dethi').value  = kt.ID_MaDeThi || '';

        calcDiem();
        loadDeThiCount();
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
        const rows = document.querySelectorAll('#kyThiTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    const demCauHoiBase = "{{ url('admin/de-thi') }}";

    async function loadDeThiCount() {
        const id = document.getElementById('f_dethi').value;
        const hint = document.getElementById('dethi-count-hint');
        if (!id) { hint.style.display = 'none'; return; }
        try {
            const res = await fetch(`${demCauHoiBase}/${id}/dem-cau-hoi`);
            const d   = await res.json();
            document.getElementById('hint-4pa').textContent  = d.so_4pa;
            document.getElementById('hint-ds').textContent   = d.so_ds;
            document.getElementById('hint-ngan').textContent = d.so_ngan;
            hint.style.display = '';
            checkHints(d);
        } catch { hint.style.display = 'none'; }
    }

    let _lastCounts = null;
    function recheck() { if (_lastCounts) checkHints(_lastCounts); }

    function checkHints(d) {
        _lastCounts = d;
        const so4pa  = parseInt(document.getElementById('f_so4pa').value)  || 0;
        const sods   = parseInt(document.getElementById('f_sods').value)   || 0;
        const songan = parseInt(document.getElementById('f_songan').value) || 0;
        const warns  = [];
        if (so4pa  > d.so_4pa)  warns.push(`4PA yêu cầu ${so4pa} > có ${d.so_4pa}`);
        if (sods   > d.so_ds)   warns.push(`DS yêu cầu ${sods} > có ${d.so_ds}`);
        if (songan > d.so_ngan) warns.push(`Ngắn yêu cầu ${songan} > có ${d.so_ngan}`);
        document.getElementById('hint-warn').textContent = warns.length ? '⚠ ' + warns.join(', ') : '';
    }

    // Khởi tạo cascade khi load trang
    cascadeFilter();
    calcDiem();
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
