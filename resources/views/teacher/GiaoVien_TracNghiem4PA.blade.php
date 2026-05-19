<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giáo viên – Câu hỏi 4 phương án</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .q-section { font-size:12px;font-weight:700;color:var(--cerulean);text-transform:uppercase;
                     letter-spacing:.5px;padding:6px 0 2px;border-bottom:1px solid var(--cerulean-200);margin-bottom:4px; }
        .ans-grid   { display:grid;grid-template-columns:auto 1fr;align-items:center;gap:6px 10px;margin-top:4px; }
        .ans-label  { font-size:13px;font-weight:700;color:var(--cerulean-dark);width:28px;text-align:center; }
        .tbl td.q-text { text-align:left;white-space:normal;word-break:break-word; }
        .da-badge   { display:inline-block;padding:1px 8px;border-radius:12px;font-size:12px;font-weight:700;
                      background:var(--cerulean);color:#fff; }
    </style>
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ GIÁO VIÊN</h2></div>
        <div class="content-box">
            <div class="section-title blue">Ngân hàng câu hỏi – Trắc nghiệm 4 phương án</div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $e) {{ $e }}<br> @endforeach
                </div>
            @endif

            <div class="action-bar">
                <button class="action-btn" onclick="open4PA()">+ Thêm câu hỏi</button>
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
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="q-text">{{ $q->NoiDungCauHoi_TracNghiem4PhuongAn }}</td>
                            <td>{{ $q->NoiDung_ChuDe }}</td>
                            <td>{{ $q->Ten_MonHoc }}</td>
                            <td>{{ $q->Ten_KhoiLop }}</td>
                            <td><span class="da-badge">{{ $q->DapAn_TracNghiem4PhuongAn }}</span></td>
                            <td style="white-space:nowrap">
                                <button class="btn-edit" onclick='edit4PA(@json($q))'>Sửa</button>
                                <form method="POST"
                                      action="{{ route('teacher.4pa.destroy', $q->ID_TracNghiem4PhuongAn) }}"
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
<div id="modal4PA" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeModal()">
    <div class="modal-box" style="width:560px">
        <div class="modal-header">
            <span class="modal-header-title" id="modalTitle">Thêm câu hỏi 4 phương án</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="form4PA" method="POST" action="{{ route('teacher.4pa.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
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
                <div class="q-section" style="margin-top:8px">Nội dung</div>
                <div class="form-group">
                    <label class="form-label">Câu hỏi <span class="required">*</span></label>
                    <textarea class="form-input" name="NoiDungCauHoi_TracNghiem4PhuongAn" id="f_cauhoi"
                              rows="3" required placeholder="Nhập nội dung câu hỏi..."></textarea>
                </div>
                <div class="q-section" style="margin-top:8px">Đáp án</div>
                <div class="ans-grid">
                    @foreach (['A','B','C','D'] as $letter)
                    <span class="ans-label">{{ $letter }}</span>
                    <input class="form-input" type="text"
                           name="NoiDungCauTraLoi{{ $letter === 'A' ? 1 : ($letter === 'B' ? 2 : ($letter === 'C' ? 3 : 4)) }}_TracNghiem4PhuongAn"
                           id="f_ans{{ $letter }}" required maxlength="255"
                           placeholder="Nội dung đáp án {{ $letter }}">
                    @endforeach
                </div>
                <div class="form-group" style="margin-top:10px">
                    <label class="form-label">Đáp án đúng <span class="required">*</span></label>
                    <select class="form-select" name="DapAn_TracNghiem4PhuongAn" id="f_dapan" required style="width:100px">
                        <option value="">—</option>
                        <option value="A">A</option><option value="B">B</option>
                        <option value="C">C</option><option value="D">D</option>
                    </select>
                </div>
                <div class="form-group" style="margin-top:8px">
                    <label class="form-label">Hướng dẫn giải <span style="color:var(--text-soft);font-weight:400">(tùy chọn)</span></label>
                    <textarea class="form-input" name="HuongDanGiai_TracNghiem4PhuongAn" id="f_hdg"
                              rows="2" placeholder="Hướng dẫn giải..."></textarea>
                </div>
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
    window.PAGE_ACTIVE = 'tn4pa';

    const storeUrl   = "{{ route('teacher.4pa.store') }}";
    const updateBase = "{{ url('giao-vien/4pa') }}";
    const allChuDe   = @json($chuDesAll);

    function filterChude() {
        const monId  = parseInt(document.getElementById('f_mon').value)  || 0;
        const khoiId = parseInt(document.getElementById('f_khoi').value) || 0;
        const sel    = document.getElementById('f_chude');
        const cur    = sel.value;
        sel.innerHTML = '<option value="">— Chọn chủ đề —</option>';
        allChuDe.filter(c =>
            (!monId  || c.ID_MonHoc   == monId) &&
            (!khoiId || c.ID_KhoiLop  == khoiId)
        ).forEach(c => {
            const o = document.createElement('option');
            o.value = c.ID_ChuDe; o.textContent = c.NoiDung_ChuDe;
            if (String(o.value) === String(cur)) o.selected = true;
            sel.appendChild(o);
        });
    }

    function open4PA() {
        document.getElementById('modalTitle').textContent = 'Thêm câu hỏi 4 phương án';
        document.getElementById('form4PA').action = storeUrl;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('submitBtn').textContent = 'Thêm';
        ['f_cauhoi','f_ansA','f_ansB','f_ansC','f_ansD','f_hdg'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('f_mon').value = '';
        document.getElementById('f_khoi').value = '';
        document.getElementById('f_dapan').value = '';
        filterChude();
        document.getElementById('modal4PA').style.display = 'flex';
    }

    function edit4PA(q) {
        document.getElementById('modalTitle').textContent = 'Sửa câu hỏi 4 phương án';
        document.getElementById('form4PA').action = updateBase + '/' + q.ID_TracNghiem4PhuongAn;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('submitBtn').textContent = 'Cập nhật';
        document.getElementById('f_mon').value  = q.ID_MonHoc  || '';
        document.getElementById('f_khoi').value = q.ID_KhoiLop || '';
        filterChude();
        document.getElementById('f_chude').value = q.ID_ChuDe || '';
        document.getElementById('f_cauhoi').value = q.NoiDungCauHoi_TracNghiem4PhuongAn || '';
        document.getElementById('f_ansA').value  = q.NoiDungCauTraLoi1_TracNghiem4PhuongAn || '';
        document.getElementById('f_ansB').value  = q.NoiDungCauTraLoi2_TracNghiem4PhuongAn || '';
        document.getElementById('f_ansC').value  = q.NoiDungCauTraLoi3_TracNghiem4PhuongAn || '';
        document.getElementById('f_ansD').value  = q.NoiDungCauTraLoi4_TracNghiem4PhuongAn || '';
        document.getElementById('f_dapan').value = q.DapAn_TracNghiem4PhuongAn || '';
        document.getElementById('f_hdg').value   = q.HuongDanGiai_TracNghiem4PhuongAn || '';
        document.getElementById('modal4PA').style.display = 'flex';
    }

    function closeModal() { document.getElementById('modal4PA').style.display = 'none'; }

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
