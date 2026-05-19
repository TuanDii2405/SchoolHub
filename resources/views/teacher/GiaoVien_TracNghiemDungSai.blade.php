<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giáo viên – Câu hỏi Đúng / Sai</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .q-section { font-size:12px;font-weight:700;color:var(--cerulean);text-transform:uppercase;
                     letter-spacing:.5px;padding:6px 0 2px;border-bottom:1px solid var(--cerulean-200);margin-bottom:4px; }
        .tbl td.q-text { text-align:left;white-space:normal;word-break:break-word; }
        .da-badge   { display:inline-block;padding:1px 6px;border-radius:12px;font-size:12px;font-weight:700;
                      background:var(--cerulean);color:#fff;margin:1px; }
        .ds-row     { display:grid;grid-template-columns:1fr auto;gap:6px;align-items:center;margin-bottom:6px; }
        .ds-toggle  { display:flex;gap:4px; }
        .ds-toggle label { padding:3px 10px;border-radius:20px;border:1.5px solid var(--cerulean-300);
                           font-size:12px;cursor:pointer;color:var(--text-soft); }
        .ds-toggle input:checked + label { background:var(--cerulean);color:#fff;border-color:var(--cerulean); }
        .ds-toggle input { display:none; }
    </style>
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ GIÁO VIÊN</h2></div>
        <div class="content-box">
            <div class="section-title blue">Ngân hàng câu hỏi – Trắc nghiệm Đúng / Sai</div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $e) {{ $e }}<br> @endforeach
                </div>
            @endif

            <div class="action-bar">
                <button class="action-btn" onclick="openDS()">+ Thêm câu hỏi</button>
                <input class="search-input" type="text" id="searchInput"
                       placeholder="Tìm nội dung, chủ đề..." oninput="filterTbl()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="tbl" style="table-layout:fixed">
                    <colgroup>
                        <col style="width:50px"><col><col style="width:270px">
                        <col style="width:90px"><col style="width:80px"><col style="width:130px"><col style="width:200px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>STT</th><th>Nội dung câu hỏi</th><th>Chủ đề</th>
                            <th>Môn</th><th>Khối</th><th>Đáp án</th><th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cauHois as $i => $q)
                        @php $da = str_pad($q->DapAn_TracNghiem4PhuongAn ?? 'FFFF', 4, 'F'); @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="q-text">{{ $q->NoiDungCauHoi_TracNghiemDungSai }}</td>
                            <td>{{ $q->NoiDung_ChuDe }}</td>
                            <td>{{ $q->Ten_MonHoc }}</td>
                            <td>{{ $q->Ten_KhoiLop }}</td>
                            <td style="white-space:nowrap">
                                @for ($k = 0; $k < 4; $k++)
                                    <span class="da-badge" style="{{ $da[$k]==='T' ? '' : 'background:var(--jasper)' }}">{{ $da[$k]==='T' ? 'Đ' : 'S' }}</span>
                                @endfor
                            </td>
                            <td style="white-space:nowrap">
                                <button class="btn-edit" onclick='editDS(@json($q))'>Sửa</button>
                                <form method="POST"
                                      action="{{ route('teacher.dung-sai.destroy', $q->ID_TracNghiemDungSai) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Xóa câu hỏi này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="empty-notice">Chưa có câu hỏi nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

{{-- MODAL --}}
<div id="modalDS" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeModal()">
    <div class="modal-box" style="width:580px">
        <div class="modal-header">
            <span class="modal-header-title" id="modalTitle">Thêm câu hỏi đúng sai</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="formDS" method="POST" action="{{ route('teacher.dung-sai.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="DapAn_TracNghiem4PhuongAn" id="ds_dapan_hidden">
            <div class="modal-body">
                <div class="q-section">Phân loại</div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Môn học <span class="required">*</span></label>
                        <select class="form-select" name="ID_MonHoc" id="f_mon" required onchange="filterChude()">
                            <option value="">— Chọn môn —</option>
                            @foreach ($monHocs as $mh)
                                <option value="{{ $mh->ID_MonHoc }}">{{ $mh->Ten_MonHoc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Khối lớp <span class="required">*</span></label>
                        <select class="form-select" name="ID_KhoiLop" id="f_khoi" required onchange="filterChude()">
                            <option value="">— Chọn khối —</option>
                            @foreach ($khoiLops as $kl)
                                <option value="{{ $kl->ID_KhoiLop }}">{{ $kl->Ten_KhoiLop }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Chủ đề <span class="required">*</span></label>
                    <select class="form-select" name="ID_ChuDe" id="f_chude" required>
                        <option value="">— Chọn chủ đề —</option>
                    </select>
                </div>
                <div class="q-section" style="margin-top:8px">Nội dung câu hỏi</div>
                <div class="form-group">
                    <textarea class="form-input" name="NoiDungCauHoi_TracNghiemDungSai" id="f_cauhoi"
                              rows="2" required placeholder="Nhập nội dung câu hỏi..."></textarea>
                </div>
                <div class="q-section" style="margin-top:8px">Mệnh đề & Đáp án</div>
                @foreach ([1,2,3,4] as $n)
                <div class="ds-row">
                    <input class="form-input" type="text"
                           name="NoiDungMenhDe{{ $n }}_TracNghiemDungSai" id="f_md{{ $n }}"
                           required maxlength="255" placeholder="Mệnh đề {{ $n }}">
                    <div class="ds-toggle">
                        <input type="radio" name="ds_toggle_{{ $n }}" id="ds_d{{ $n }}" value="T">
                        <label for="ds_d{{ $n }}">Đúng</label>
                        <input type="radio" name="ds_toggle_{{ $n }}" id="ds_s{{ $n }}" value="F" checked>
                        <label for="ds_s{{ $n }}">Sai</label>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:10px">
                    <input class="form-input" type="text"
                           name="HuongDanGiaiMenhDe{{ $n }}_TracNghiemDungSai" id="f_hdg{{ $n }}"
                           maxlength="500" placeholder="Hướng dẫn mệnh đề {{ $n }} (tùy chọn)">
                </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button type="submit" class="action-btn" id="submitBtn">Thêm</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.PAGE_ROLE   = 'giaovien';
    window.PAGE_ACTIVE = 'tnds';

    const storeUrl   = "{{ route('teacher.dung-sai.store') }}";
    const updateBase = "{{ url('giao-vien/dung-sai') }}";
    const allChuDe   = @json($chuDesAll);

    function filterChude() {
        const monId  = parseInt(document.getElementById('f_mon').value)  || 0;
        const khoiId = parseInt(document.getElementById('f_khoi').value) || 0;
        const sel    = document.getElementById('f_chude');
        const cur    = sel.value;
        sel.innerHTML = '<option value="">— Chọn chủ đề —</option>';
        allChuDe.filter(c =>
            (!monId  || c.ID_MonHoc  == monId) &&
            (!khoiId || c.ID_KhoiLop == khoiId)
        ).forEach(c => {
            const o = document.createElement('option');
            o.value = c.ID_ChuDe; o.textContent = c.NoiDung_ChuDe;
            if (String(o.value) === String(cur)) o.selected = true;
            sel.appendChild(o);
        });
    }

    function openDS() {
        document.getElementById('modalTitle').textContent = 'Thêm câu hỏi đúng sai';
        document.getElementById('formDS').action = storeUrl;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('submitBtn').textContent = 'Thêm';
        document.getElementById('f_cauhoi').value = '';
        document.getElementById('f_mon').value  = '';
        document.getElementById('f_khoi').value = '';
        [1,2,3,4].forEach(n => {
            document.getElementById('f_md' + n).value  = '';
            document.getElementById('ds_s'  + n).checked = true;
            document.getElementById('f_hdg' + n).value  = '';
        });
        filterChude();
        document.getElementById('modalDS').style.display = 'flex';
    }

    function editDS(q) {
        document.getElementById('modalTitle').textContent = 'Sửa câu hỏi đúng sai';
        document.getElementById('formDS').action = updateBase + '/' + q.ID_TracNghiemDungSai;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('submitBtn').textContent = 'Cập nhật';
        document.getElementById('f_mon').value  = q.ID_MonHoc  || '';
        document.getElementById('f_khoi').value = q.ID_KhoiLop || '';
        filterChude();
        document.getElementById('f_chude').value  = q.ID_ChuDe || '';
        document.getElementById('f_cauhoi').value = q.NoiDungCauHoi_TracNghiemDungSai || '';
        document.getElementById('f_md1').value    = q.NoiDungMenhDe1_TracNghiemDungSai || '';
        document.getElementById('f_md2').value    = q.NoiDungMenhDe2_TracNghiemDungSai || '';
        document.getElementById('f_md3').value    = q.NoiDungMenhDe3_TracNghiemDungSai || '';
        document.getElementById('f_md4').value    = q.NoiDungMenhDe4_TracNghiemDungSai || '';
        document.getElementById('f_hdg1').value   = q.HuongDanGiaiMenhDe1_TracNghiemDungSai || '';
        document.getElementById('f_hdg2').value   = q.HuongDanGiaiMenhDe2_TracNghiemDungSai || '';
        document.getElementById('f_hdg3').value   = q.HuongDanGiaiMenhDe3_TracNghiemDungSai || '';
        document.getElementById('f_hdg4').value   = q.HuongDanGiaiMenhDe4_TracNghiemDungSai || '';
        const da = (q.DapAn_TracNghiem4PhuongAn || 'FFFF').padEnd(4, 'F');
        [1,2,3,4].forEach(n => {
            const isTrue = da[n-1] === 'T';
            document.getElementById(isTrue ? 'ds_d'+n : 'ds_s'+n).checked = true;
        });
        document.getElementById('modalDS').style.display = 'flex';
    }

    function buildDSDapAn() {
        document.getElementById('ds_dapan_hidden').value =
            [1,2,3,4].map(n => document.getElementById('ds_d'+n).checked ? 'T' : 'F').join('');
    }
    document.getElementById('formDS').addEventListener('submit', buildDSDapAn);

    function closeModal() { document.getElementById('modalDS').style.display = 'none'; }

    function filterTbl() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('#tbl tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
