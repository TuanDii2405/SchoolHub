<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Quản lý câu hỏi</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .q-section { font-size:12px;font-weight:700;color:var(--cerulean);text-transform:uppercase;
                     letter-spacing:.5px;padding:6px 0 2px;border-bottom:1px solid var(--cerulean-200);margin-bottom:4px; }
        .ans-grid   { display:grid;grid-template-columns:auto 1fr;align-items:center;gap:6px 10px;margin-top:4px; }
        .ans-label  { font-size:13px;font-weight:700;color:var(--cerulean-dark);width:28px;text-align:center; }
        .tbl td.q-text { text-align:left; white-space:normal; word-break:break-word; }
        .da-badge   { display:inline-block;padding:1px 8px;border-radius:12px;font-size:12px;font-weight:700;
                      background:var(--cerulean);color:#fff; }
        .ds-row     { display:grid;grid-template-columns:1fr auto;gap:6px;align-items:center;margin-bottom:6px; }
        .ds-toggle  { display:flex;gap:4px; }
        .ds-toggle label { padding:3px 10px;border-radius:20px;border:1.5px solid var(--cerulean-300);
                           font-size:12px;cursor:pointer;color:var(--text-soft); }
        .ds-toggle input:checked + label { background:var(--cerulean);color:#fff;border-color:var(--cerulean); }
        .ds-toggle input { display:none; }
        .char-input { width:42px;text-align:center;font-size:15px;font-weight:700;letter-spacing:1px; }
    </style>
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ ADMIN</h2></div>
        <div class="content-box">
            <div class="section-title">Quản lý câu hỏi</div>

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

            {{-- ===== TAB BAR ===== --}}
            <div class="tab-bar">
                <button class="tab-btn" id="tab-btn-4pa" onclick="switchTab('4pa')">
                    Trắc nghiệm 4 phương án
                    <span class="tab-count">{{ count($cau4PA) }}</span>
                </button>
                <button class="tab-btn" id="tab-btn-dung-sai" onclick="switchTab('dung-sai')">
                    Trắc nghiệm đúng sai
                    <span class="tab-count">{{ count($cauDS) }}</span>
                </button>
                <button class="tab-btn" id="tab-btn-tra-loi-ngan" onclick="switchTab('tra-loi-ngan')">
                    Trả lời ngắn
                    <span class="tab-count">{{ count($cauNgan) }}</span>
                </button>
            </div>

            {{-- ===== TAB 1: 4 PHƯƠNG ÁN ===== --}}
            <div id="tab-4pa">
                <div class="action-bar">
                    <button class="action-btn" onclick="open4PA()">+ Thêm câu hỏi</button>
                    <input class="search-input" type="text" id="search4pa"
                           placeholder="Tìm nội dung, chủ đề..." oninput="filterTbl('tbl4pa','search4pa')">
                </div>
                <div class="table-wrap">
                    <table class="tbl" id="tbl4pa" style="table-layout:fixed">
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
                            @forelse ($cau4PA as $i => $q)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="q-text">{{ $q->NoiDungCauHoi_TracNghiem4PhuongAn }}</td>
                                <td>{{ $q->chu_de }}</td>
                                <td>{{ $q->Ten_MonHoc }}</td>
                                <td>{{ $q->Ten_KhoiLop }}</td>
                                <td><span class="da-badge">{{ $q->DapAn_TracNghiem4PhuongAn }}</span></td>
                                <td style="white-space:nowrap">
                                    <button class="btn-edit" onclick='edit4PA(@json($q))'>Sửa</button>
                                    <form method="POST"
                                          action="{{ route('admin.cau-hoi.4pa.destroy', $q->ID_TracNghiem4PhuongAn) }}"
                                          style="display:inline"
                                          onsubmit="return confirm('Xóa câu hỏi này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="empty-notice">Chưa có câu hỏi 4 phương án nào</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ===== TAB 2: ĐÚNG SAI ===== --}}
            <div id="tab-dung-sai" style="display:none">
                <div class="action-bar">
                    <button class="action-btn" onclick="openDS()">+ Thêm câu hỏi</button>
                    <input class="search-input" type="text" id="searchDS"
                           placeholder="Tìm nội dung, chủ đề..." oninput="filterTbl('tblDS','searchDS')">
                </div>
                <div class="table-wrap">
                    <table class="tbl" id="tblDS" style="table-layout:fixed">
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
                            @forelse ($cauDS as $i => $q)
                            @php
                                $da = str_pad($q->DapAn_TracNghiem4PhuongAn ?? 'FFFF', 4, 'F');
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="q-text">{{ $q->NoiDungCauHoi_TracNghiemDungSai }}</td>
                                <td>{{ $q->chu_de }}</td>
                                <td>{{ $q->Ten_MonHoc }}</td>
                                <td>{{ $q->Ten_KhoiLop }}</td>
                                <td style="white-space:nowrap">
                                    @for ($k = 0; $k < 4; $k++)
                                        <span class="da-badge" style="{{ $da[$k]==='T' ? '' : 'background:var(--jasper)' }};padding:1px 6px;margin:1px">{{ $da[$k]==='T' ? 'Đ' : 'S' }}</span>
                                    @endfor
                                </td>
                                <td style="white-space:nowrap">
                                    <button class="btn-edit" onclick='editDS(@json($q))'>Sửa</button>
                                    <form method="POST"
                                          action="{{ route('admin.cau-hoi.ds.destroy', $q->ID_TracNghiemDungSai) }}"
                                          style="display:inline"
                                          onsubmit="return confirm('Xóa câu hỏi này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="empty-notice">Chưa có câu hỏi đúng sai nào</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ===== TAB 3: TRẢ LỜI NGẮN ===== --}}
            <div id="tab-tra-loi-ngan" style="display:none">
                <div class="action-bar">
                    <button class="action-btn" onclick="openNgan()">+ Thêm câu hỏi</button>
                    <input class="search-input" type="text" id="searchNgan"
                           placeholder="Tìm nội dung, chủ đề..." oninput="filterTbl('tblNgan','searchNgan')">
                </div>
                <div class="table-wrap">
                    <table class="tbl" id="tblNgan" style="table-layout:fixed">
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
                            @forelse ($cauNgan as $i => $q)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="q-text">{{ $q->NoiDungCauHoi_TracNghiemTraLoiNgan }}</td>
                                <td>{{ $q->chu_de }}</td>
                                <td>{{ $q->Ten_MonHoc }}</td>
                                <td>{{ $q->Ten_KhoiLop }}</td>
                                <td>
                                    <span class="da-badge" style="letter-spacing:3px;font-size:13px">
                                        {{ $q->KiTuThu1CuaDapAn_TracNghiemTraLoiNgan }}{{ $q->KiTuThu2CuaDapAn_TracNghiemTraLoiNgan }}{{ $q->KiTuThu3CuaDapAn_TracNghiemTraLoiNgan }}{{ $q->KiTuThu4CuaDapAn_TracNghiemTraLoiNgan }}
                                    </span>
                                </td>
                                <td style="white-space:nowrap">
                                    <button class="btn-edit" onclick='editNgan(@json($q))'>Sửa</button>
                                    <form method="POST"
                                          action="{{ route('admin.cau-hoi.ngan.destroy', $q->ID_TracNghiemTraLoiNgan) }}"
                                          style="display:inline"
                                          onsubmit="return confirm('Xóa câu hỏi này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="empty-notice">Chưa có câu hỏi trả lời ngắn nào</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

{{-- ===================================================
     MODAL 1: 4 PHƯƠNG ÁN
     =================================================== --}}
<div id="modal4PA" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeModal('modal4PA')">
    <div class="modal-box" style="width:560px">
        <div class="modal-header">
            <span class="modal-header-title" id="title4PA">Thêm câu hỏi 4 phương án</span>
            <button class="modal-close" onclick="closeModal('modal4PA')">×</button>
        </div>
        <form id="form4PA" method="POST" action="{{ route('admin.cau-hoi.4pa.store') }}">
            @csrf
            <input type="hidden" name="_method" id="method4PA" value="POST">
            <div class="modal-body">
                <div class="q-section">Phân loại</div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Môn học <span class="required">*</span></label>
                        <select class="form-select" name="ID_MonHoc" id="4pa_mon" required onchange="filterChude('4pa')">
                            <option value="">— Chọn môn —</option>
                            @foreach ($monHocs as $mh)
                                <option value="{{ $mh->ID_MonHoc }}">{{ $mh->Ten_MonHoc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Khối lớp <span class="required">*</span></label>
                        <select class="form-select" name="ID_KhoiLop" id="4pa_khoi" required onchange="filterChude('4pa')">
                            <option value="">— Chọn khối —</option>
                            @foreach ($khoiLops as $kl)
                                <option value="{{ $kl->ID_KhoiLop }}">{{ $kl->Ten_KhoiLop }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Chủ đề <span class="required">*</span></label>
                    <select class="form-select" name="ID_ChuDe" id="4pa_chude" required>
                        <option value="">— Chọn chủ đề —</option>
                    </select>
                </div>

                <div class="q-section" style="margin-top:8px">Nội dung</div>
                <div class="form-group">
                    <label class="form-label">Câu hỏi <span class="required">*</span></label>
                    <textarea class="form-input" name="NoiDungCauHoi_TracNghiem4PhuongAn" id="4pa_cauhoi"
                              rows="3" required placeholder="Nhập nội dung câu hỏi..."></textarea>
                </div>

                <div class="q-section" style="margin-top:8px">Đáp án</div>
                <div class="ans-grid">
                    @foreach (['A','B','C','D'] as $letter)
                    <span class="ans-label">{{ $letter }}</span>
                    <input class="form-input" type="text"
                           name="NoiDungCauTraLoi{{ $letter === 'A' ? 1 : ($letter === 'B' ? 2 : ($letter === 'C' ? 3 : 4)) }}_TracNghiem4PhuongAn"
                           id="4pa_ans{{ $letter }}" required maxlength="255"
                           placeholder="Nội dung đáp án {{ $letter }}">
                    @endforeach
                </div>
                <div class="form-group" style="margin-top:10px">
                    <label class="form-label">Đáp án đúng <span class="required">*</span></label>
                    <select class="form-select" name="DapAn_TracNghiem4PhuongAn" id="4pa_dapan" required style="width:100px">
                        <option value="">—</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top:8px">
                    <label class="form-label">Hướng dẫn giải <span style="color:var(--text-soft);font-weight:400">(tùy chọn)</span></label>
                    <textarea class="form-input" name="HuongDanGiai_TracNghiem4PhuongAn" id="4pa_hdg"
                              rows="2" placeholder="Hướng dẫn giải chi tiết..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modal4PA')">Hủy</button>
                <button type="submit" class="action-btn" id="submit4PA">Thêm</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================================================
     MODAL 2: ĐÚNG SAI
     =================================================== --}}
<div id="modalDS" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeModal('modalDS')">
    <div class="modal-box" style="width:580px">
        <div class="modal-header">
            <span class="modal-header-title" id="titleDS">Thêm câu hỏi đúng sai</span>
            <button class="modal-close" onclick="closeModal('modalDS')">×</button>
        </div>
        <form id="formDS" method="POST" action="{{ route('admin.cau-hoi.ds.store') }}">
            @csrf
            <input type="hidden" name="_method" id="methodDS" value="POST">
            <input type="hidden" name="DapAn_TracNghiem4PhuongAn" id="ds_dapan_hidden">
            <div class="modal-body">
                <div class="q-section">Phân loại</div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Môn học <span class="required">*</span></label>
                        <select class="form-select" name="ID_MonHoc" id="ds_mon" required onchange="filterChude('ds')">
                            <option value="">— Chọn môn —</option>
                            @foreach ($monHocs as $mh)
                                <option value="{{ $mh->ID_MonHoc }}">{{ $mh->Ten_MonHoc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Khối lớp <span class="required">*</span></label>
                        <select class="form-select" name="ID_KhoiLop" id="ds_khoi" required onchange="filterChude('ds')">
                            <option value="">— Chọn khối —</option>
                            @foreach ($khoiLops as $kl)
                                <option value="{{ $kl->ID_KhoiLop }}">{{ $kl->Ten_KhoiLop }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Chủ đề <span class="required">*</span></label>
                    <select class="form-select" name="ID_ChuDe" id="ds_chude" required>
                        <option value="">— Chọn chủ đề —</option>
                    </select>
                </div>

                <div class="q-section" style="margin-top:8px">Nội dung câu hỏi</div>
                <div class="form-group">
                    <textarea class="form-input" name="NoiDungCauHoi_TracNghiemDungSai" id="ds_cauhoi"
                              rows="2" required placeholder="Nhập nội dung câu hỏi..."></textarea>
                </div>

                <div class="q-section" style="margin-top:8px">Mệnh đề & Đáp án</div>
                @foreach ([1,2,3,4] as $n)
                <div class="ds-row">
                    <input class="form-input" type="text"
                           name="NoiDungMenhDe{{ $n }}_TracNghiemDungSai" id="ds_md{{ $n }}"
                           required maxlength="255" placeholder="Mệnh đề {{ $n }}">
                    <div class="ds-toggle">
                        <input type="radio" name="ds_toggle_{{ $n }}" id="ds_d{{ $n }}" value="T">
                        <label for="ds_d{{ $n }}">Đúng</label>
                        <input type="radio" name="ds_toggle_{{ $n }}" id="ds_s{{ $n }}" value="F" checked>
                        <label for="ds_s{{ $n }}">Sai</label>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:10px">
                    <label class="form-label" style="font-weight:400;color:var(--text-soft)">
                        Hướng dẫn mệnh đề {{ $n }} (tùy chọn)
                    </label>
                    <input class="form-input" type="text"
                           name="HuongDanGiaiMenhDe{{ $n }}_TracNghiemDungSai" id="ds_hdg{{ $n }}"
                           maxlength="500" placeholder="Giải thích...">
                </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modalDS')">Hủy</button>
                <button type="submit" class="action-btn" id="submitDS" onclick="buildDSDapAn()">Thêm</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================================================
     MODAL 3: TRẢ LỜI NGẮN
     =================================================== --}}
<div id="modalNgan" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeModal('modalNgan')">
    <div class="modal-box" style="width:500px">
        <div class="modal-header">
            <span class="modal-header-title" id="titleNgan">Thêm câu hỏi trả lời ngắn</span>
            <button class="modal-close" onclick="closeModal('modalNgan')">×</button>
        </div>
        <form id="formNgan" method="POST" action="{{ route('admin.cau-hoi.ngan.store') }}">
            @csrf
            <input type="hidden" name="_method" id="methodNgan" value="POST">
            <div class="modal-body">
                <div class="q-section">Phân loại</div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Môn học <span class="required">*</span></label>
                        <select class="form-select" name="ID_MonHoc" id="ng_mon" required onchange="filterChude('ng')">
                            <option value="">— Chọn môn —</option>
                            @foreach ($monHocs as $mh)
                                <option value="{{ $mh->ID_MonHoc }}">{{ $mh->Ten_MonHoc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Khối lớp <span class="required">*</span></label>
                        <select class="form-select" name="ID_KhoiLop" id="ng_khoi" required onchange="filterChude('ng')">
                            <option value="">— Chọn khối —</option>
                            @foreach ($khoiLops as $kl)
                                <option value="{{ $kl->ID_KhoiLop }}">{{ $kl->Ten_KhoiLop }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Chủ đề <span class="required">*</span></label>
                    <select class="form-select" name="ID_ChuDe" id="ng_chude" required>
                        <option value="">— Chọn chủ đề —</option>
                    </select>
                </div>

                <div class="q-section" style="margin-top:8px">Nội dung câu hỏi</div>
                <div class="form-group">
                    <textarea class="form-input" name="NoiDungCauHoi_TracNghiemTraLoiNgan" id="ng_cauhoi"
                              rows="3" required placeholder="Nhập nội dung câu hỏi..."></textarea>
                </div>

                <div class="q-section" style="margin-top:8px">Đáp án (4 ký tự)</div>
                <div style="display:flex;gap:10px;align-items:center;margin-bottom:4px">
                    @foreach ([1,2,3,4] as $k)
                    <div style="text-align:center">
                        <div style="font-size:11px;color:var(--text-soft);margin-bottom:3px">Ký tự {{ $k }}</div>
                        <input class="form-input char-input"
                               type="text" maxlength="1"
                               name="KiTuThu{{ $k }}CuaDapAn_TracNghiemTraLoiNgan"
                               id="ng_kt{{ $k }}" required
                               oninput="this.value=this.value.toUpperCase();nextKiTu(this,{{ $k }})">
                    </div>
                    @endforeach
                </div>
                <div style="font-size:11px;color:var(--text-soft)">
                    Nhập từng ký tự đáp án (viết hoa tự động). VD: đáp án "12AB" → nhập 1, 2, A, B
                </div>

                <div class="form-group" style="margin-top:10px">
                    <label class="form-label">Hướng dẫn giải <span style="color:var(--text-soft);font-weight:400">(tùy chọn)</span></label>
                    <textarea class="form-input" name="HuongDanGiai_TracNghiemTraLoiNgan" id="ng_hdg"
                              rows="2" placeholder="Hướng dẫn giải..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modalNgan')">Hủy</button>
                <button type="submit" class="action-btn" id="submitNgan">Thêm</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.PAGE_ROLE   = 'admin';
    window.PAGE_ACTIVE = 'quanly-cauhoi';

    const allChuDe     = @json($chuDesAll);
    const store4PA     = "{{ route('admin.cau-hoi.4pa.store') }}";
    const storeDS      = "{{ route('admin.cau-hoi.ds.store') }}";
    const storeNgan    = "{{ route('admin.cau-hoi.ngan.store') }}";
    const updateBase4PA  = "{{ url('admin/cau-hoi/4pa') }}";
    const updateBaseDS   = "{{ url('admin/cau-hoi/dung-sai') }}";
    const updateBaseNgan = "{{ url('admin/cau-hoi/tra-loi-ngan') }}";

    // ── Tab switching ─────────────────────────────────────────
    function switchTab(tab) {
        ['4pa','dung-sai','tra-loi-ngan'].forEach(t => {
            document.getElementById('tab-' + t).style.display   = t === tab ? '' : 'none';
            document.getElementById('tab-btn-' + t).classList.toggle('active', t === tab);
        });
    }

    // ── Cascade chủ đề ───────────────────────────────────────
    function filterChude(prefix) {
        const monId  = parseInt(document.getElementById(prefix + '_mon').value)  || 0;
        const khoiId = parseInt(document.getElementById(prefix + '_khoi').value) || 0;
        const sel    = document.getElementById(prefix + '_chude');
        const cur    = sel.value;
        sel.innerHTML = '<option value="">— Chọn chủ đề —</option>';
        allChuDe.filter(c =>
            (!monId  || c.ID_MonHoc  === monId) &&
            (!khoiId || c.ID_KhoiLop === khoiId)
        ).forEach(c => {
            const o = document.createElement('option');
            o.value = c.ID_ChuDe;
            o.textContent = c.NoiDung_ChuDe;
            if (String(o.value) === String(cur)) o.selected = true;
            sel.appendChild(o);
        });
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // ── Modal 4PA ────────────────────────────────────────────
    function open4PA() {
        document.getElementById('title4PA').textContent = 'Thêm câu hỏi 4 phương án';
        document.getElementById('form4PA').action = store4PA;
        document.getElementById('method4PA').value = 'POST';
        document.getElementById('submit4PA').textContent = 'Thêm';
        ['4pa_cauhoi','4pa_ansA','4pa_ansB','4pa_ansC','4pa_ansD','4pa_hdg'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('4pa_mon').value = '';
        document.getElementById('4pa_khoi').value = '';
        document.getElementById('4pa_dapan').value = '';
        filterChude('4pa');
        document.getElementById('modal4PA').style.display = 'flex';
    }

    function edit4PA(q) {
        document.getElementById('title4PA').textContent = 'Sửa câu hỏi 4 phương án';
        document.getElementById('form4PA').action = updateBase4PA + '/' + q.ID_TracNghiem4PhuongAn;
        document.getElementById('method4PA').value = 'PUT';
        document.getElementById('submit4PA').textContent = 'Cập nhật';
        document.getElementById('4pa_mon').value   = q.ID_MonHoc   || '';
        document.getElementById('4pa_khoi').value  = q.ID_KhoiLop  || '';
        filterChude('4pa');
        document.getElementById('4pa_chude').value = q.ID_ChuDe    || '';
        document.getElementById('4pa_cauhoi').value = q.NoiDungCauHoi_TracNghiem4PhuongAn || '';
        document.getElementById('4pa_ansA').value  = q.NoiDungCauTraLoi1_TracNghiem4PhuongAn || '';
        document.getElementById('4pa_ansB').value  = q.NoiDungCauTraLoi2_TracNghiem4PhuongAn || '';
        document.getElementById('4pa_ansC').value  = q.NoiDungCauTraLoi3_TracNghiem4PhuongAn || '';
        document.getElementById('4pa_ansD').value  = q.NoiDungCauTraLoi4_TracNghiem4PhuongAn || '';
        document.getElementById('4pa_dapan').value = q.DapAn_TracNghiem4PhuongAn || '';
        document.getElementById('4pa_hdg').value   = q.HuongDanGiai_TracNghiem4PhuongAn || '';
        document.getElementById('modal4PA').style.display = 'flex';
    }

    // ── Modal Đúng Sai ────────────────────────────────────────
    function openDS() {
        document.getElementById('titleDS').textContent = 'Thêm câu hỏi đúng sai';
        document.getElementById('formDS').action = storeDS;
        document.getElementById('methodDS').value = 'POST';
        document.getElementById('submitDS').textContent = 'Thêm';
        document.getElementById('ds_cauhoi').value = '';
        document.getElementById('ds_mon').value  = '';
        document.getElementById('ds_khoi').value = '';
        [1,2,3,4].forEach(n => {
            document.getElementById('ds_md' + n).value  = '';
            document.getElementById('ds_s'  + n).checked = true;
            document.getElementById('ds_hdg'+ n).value  = '';
        });
        filterChude('ds');
        document.getElementById('modalDS').style.display = 'flex';
    }

    function editDS(q) {
        document.getElementById('titleDS').textContent = 'Sửa câu hỏi đúng sai';
        document.getElementById('formDS').action = updateBaseDS + '/' + q.ID_TracNghiemDungSai;
        document.getElementById('methodDS').value = 'PUT';
        document.getElementById('submitDS').textContent = 'Cập nhật';
        document.getElementById('ds_mon').value  = q.ID_MonHoc  || '';
        document.getElementById('ds_khoi').value = q.ID_KhoiLop || '';
        filterChude('ds');
        document.getElementById('ds_chude').value  = q.ID_ChuDe || '';
        document.getElementById('ds_cauhoi').value = q.NoiDungCauHoi_TracNghiemDungSai || '';
        document.getElementById('ds_md1').value    = q.NoiDungMenhDe1_TracNghiemDungSai || '';
        document.getElementById('ds_md2').value    = q.NoiDungMenhDe2_TracNghiemDungSai || '';
        document.getElementById('ds_md3').value    = q.NoiDungMenhDe3_TracNghiemDungSai || '';
        document.getElementById('ds_md4').value    = q.NoiDungMenhDe4_TracNghiemDungSai || '';
        document.getElementById('ds_hdg1').value   = q.HuongDanGiaiMenhDe1_TracNghiemDungSai || '';
        document.getElementById('ds_hdg2').value   = q.HuongDanGiaiMenhDe2_TracNghiemDungSai || '';
        document.getElementById('ds_hdg3').value   = q.HuongDanGiaiMenhDe3_TracNghiemDungSai || '';
        document.getElementById('ds_hdg4').value   = q.HuongDanGiaiMenhDe4_TracNghiemDungSai || '';
        const da = (q.DapAn_TracNghiem4PhuongAn || 'FFFF').padEnd(4, 'F');
        [1,2,3,4].forEach(n => {
            const isTrue = da[n-1] === 'T';
            document.getElementById(isTrue ? 'ds_d'+n : 'ds_s'+n).checked = true;
        });
        document.getElementById('modalDS').style.display = 'flex';
    }

    function buildDSDapAn() {
        const da = [1,2,3,4].map(n =>
            document.getElementById('ds_d'+n).checked ? 'T' : 'F'
        ).join('');
        document.getElementById('ds_dapan_hidden').value = da;
    }
    document.getElementById('formDS').addEventListener('submit', buildDSDapAn);

    // ── Modal Trả lời ngắn ────────────────────────────────────
    function openNgan() {
        document.getElementById('titleNgan').textContent = 'Thêm câu hỏi trả lời ngắn';
        document.getElementById('formNgan').action = storeNgan;
        document.getElementById('methodNgan').value = 'POST';
        document.getElementById('submitNgan').textContent = 'Thêm';
        document.getElementById('ng_cauhoi').value = '';
        document.getElementById('ng_mon').value  = '';
        document.getElementById('ng_khoi').value = '';
        document.getElementById('ng_hdg').value  = '';
        [1,2,3,4].forEach(k => document.getElementById('ng_kt'+k).value = '');
        filterChude('ng');
        document.getElementById('modalNgan').style.display = 'flex';
    }

    function editNgan(q) {
        document.getElementById('titleNgan').textContent = 'Sửa câu hỏi trả lời ngắn';
        document.getElementById('formNgan').action = updateBaseNgan + '/' + q.ID_TracNghiemTraLoiNgan;
        document.getElementById('methodNgan').value = 'PUT';
        document.getElementById('submitNgan').textContent = 'Cập nhật';
        document.getElementById('ng_mon').value   = q.ID_MonHoc  || '';
        document.getElementById('ng_khoi').value  = q.ID_KhoiLop || '';
        filterChude('ng');
        document.getElementById('ng_chude').value  = q.ID_ChuDe  || '';
        document.getElementById('ng_cauhoi').value = q.NoiDungCauHoi_TracNghiemTraLoiNgan || '';
        document.getElementById('ng_kt1').value = q.KiTuThu1CuaDapAn_TracNghiemTraLoiNgan || '';
        document.getElementById('ng_kt2').value = q.KiTuThu2CuaDapAn_TracNghiemTraLoiNgan || '';
        document.getElementById('ng_kt3').value = q.KiTuThu3CuaDapAn_TracNghiemTraLoiNgan || '';
        document.getElementById('ng_kt4').value = q.KiTuThu4CuaDapAn_TracNghiemTraLoiNgan || '';
        document.getElementById('ng_hdg').value = q.HuongDanGiai_TracNghiemTraLoiNgan || '';
        document.getElementById('modalNgan').style.display = 'flex';
    }

    function nextKiTu(el, n) {
        if (el.value && n < 4) document.getElementById('ng_kt'+(n+1)).focus();
    }

    // ── Filter table ─────────────────────────────────────────
    function filterTbl(tblId, searchId) {
        const q = document.getElementById(searchId).value.toLowerCase();
        document.querySelectorAll('#' + tblId + ' tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    // Khởi tạo tab active từ session
    switchTab('{{ session("active_tab", "4pa") }}');
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
