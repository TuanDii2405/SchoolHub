<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Câu hỏi đề thi</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .dethi-info {
            display: flex; gap: 24px; align-items: center; flex-wrap: wrap;
            background: var(--cerulean-50); border: 1px solid var(--cerulean-200);
            border-radius: 10px; padding: 12px 18px; margin-bottom: 16px;
        }
        .dethi-info-name { font-size: 15px; font-weight: 700; color: var(--cerulean-dark); }
        .dethi-info-meta { font-size: 13px; color: var(--text-soft); }
        .count-chip {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--cerulean-100); border: 1px solid var(--cerulean-300);
            border-radius: 20px; padding: 3px 12px; font-size: 13px; font-weight: 600;
            color: var(--cerulean-dark);
        }
        /* ── Split layout ── */
        .q-split {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            align-items: start;
        }
        .q-panel {
            border: 1.5px solid var(--cerulean-200);
            border-radius: 10px;
            overflow: hidden;
        }
        .q-panel-header {
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
            background: var(--cerulean-50);
            padding: 8px 14px;
            font-size: 12px; font-weight: 700; color: var(--cerulean-dark);
            border-bottom: 1.5px solid var(--cerulean-200);
        }
        .q-panel-header.green { background: #f0faf2; border-bottom-color: #a8d5ac; color: #256029; }
        .q-panel-body {
            max-height: 420px;
            overflow-y: auto;
        }
        .q-panel-body .tbl { margin: 0; border-radius: 0; }
        .q-panel-body .tbl td, .q-panel-body .tbl th { font-size: 12px; padding: 6px 10px; }
        .tbl td.q-text-cell { text-align: left; white-space: normal; word-break: break-word; }
        .da-badge { display: inline-block; padding: 1px 7px; border-radius: 12px;
                    font-size: 11px; font-weight: 700; background: var(--cerulean); color: #fff; }
        .btn-sm { padding: 3px 10px; font-size: 12px; border: 1.5px solid var(--cerulean-300);
                  border-radius: 6px; background: var(--cerulean-50); color: var(--cerulean-dark);
                  cursor: pointer; font-family: inherit; white-space: nowrap; }
        .btn-sm:hover { background: var(--cerulean); color: #fff; border-color: var(--cerulean); }
        .btn-danger-sm { padding: 3px 10px; font-size: 12px;
                         border: 1.5px solid var(--jasper-200,#f5b8ba);
                         border-radius: 6px; background: var(--jasper-100); color: var(--jasper-dark);
                         cursor: pointer; font-family: inherit; }
        .btn-danger-sm:hover { background: var(--jasper); color: #fff; }
        .empty-bank { font-size: 12px; color: var(--text-soft); font-style: italic;
                      padding: 14px 16px; text-align: center; }
        .check-bar { display: flex; align-items: center; gap: 8px;
                     padding: 6px 10px; border-bottom: 1px solid var(--cerulean-100);
                     background: #fff; font-size: 12px; color: var(--text-soft); }
    </style>
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ ADMIN</h2></div>
        <div class="content-box">

            <div style="margin-bottom:10px;font-size:13px">
                <a href="{{ route('admin.de-thi') }}" style="color:var(--cerulean)">← Quay lại danh sách đề thi</a>
            </div>

            <div class="section-title">Quản lý câu hỏi trong đề thi</div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- De_Thi info --}}
            <div class="dethi-info">
                <div>
                    <div class="dethi-info-name">{{ $deThi->TenDeThi }}</div>
                    <div class="dethi-info-meta">{{ $deThi->Ten_MonHoc }} &nbsp;|&nbsp; {{ $deThi->Ten_KhoiLop }}</div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <span class="count-chip"><i class="bi bi-list-check"></i> 4PA: {{ count($in4PA) }}</span>
                    <span class="count-chip"><i class="bi bi-check2-square"></i> DS: {{ count($inDS) }}</span>
                    <span class="count-chip"><i class="bi bi-input-cursor-text"></i> Ngắn: {{ count($inNgan) }}</span>
                </div>
            </div>

            {{-- TABS --}}
            <div class="tab-bar">
                <button class="tab-btn active" id="tab-btn-4pa" onclick="switchTab('4pa')">
                    4 Phương án
                    <span class="tab-count">{{ count($in4PA) }}/{{ count($in4PA)+count($avail4PA) }}</span>
                </button>
                <button class="tab-btn" id="tab-btn-ds" onclick="switchTab('ds')">
                    Đúng / Sai
                    <span class="tab-count">{{ count($inDS) }}/{{ count($inDS)+count($availDS) }}</span>
                </button>
                <button class="tab-btn" id="tab-btn-ngan" onclick="switchTab('ngan')">
                    Trả lời ngắn
                    <span class="tab-count">{{ count($inNgan) }}/{{ count($inNgan)+count($availNgan) }}</span>
                </button>
            </div>

            {{-- ===== TAB 4PA ===== --}}
            <div id="tab-4pa">
                <div class="q-split">
                    {{-- Panel trái: đã có trong đề --}}
                    <div class="q-panel">
                        <div class="q-panel-header green">
                            <span><i class="bi bi-check-circle-fill"></i> Đã có trong đề thi</span>
                            <span class="count-chip" style="background:#e6f4ea;border-color:#a8d5ac;color:#256029">
                                {{ count($in4PA) }} câu
                            </span>
                        </div>
                        <div class="q-panel-body">
                            @if (count($in4PA) > 0)
                            <table class="tbl">
                                <thead>
                                    <tr><th>Nội dung câu hỏi</th><th>ĐA</th><th></th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($in4PA as $q)
                                    <tr>
                                        <td class="q-text-cell" title="{{ $q->NoiDungCauHoi_TracNghiem4PhuongAn }}">
                                            {{ $q->NoiDungCauHoi_TracNghiem4PhuongAn }}
                                        </td>
                                        <td><span class="da-badge">{{ $q->DapAn_TracNghiem4PhuongAn }}</span></td>
                                        <td>
                                            <form method="POST"
                                                  action="{{ route('admin.de-thi.cau-hoi.destroy', [$deThi->ID_MaDeThi, $q->ID_DeThiChiTiet]) }}"
                                                  onsubmit="return confirm('Xóa câu này khỏi đề thi?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-danger-sm">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                                <div class="empty-bank">Chưa có câu nào trong đề</div>
                            @endif
                        </div>
                    </div>

                    {{-- Panel phải: ngân hàng chưa vào --}}
                    <div class="q-panel">
                        <div class="q-panel-header">
                            <span><i class="bi bi-bank"></i> Ngân hàng (chưa có trong đề)</span>
                            <span class="count-chip">{{ count($avail4PA) }} câu</span>
                        </div>
                        <div class="q-panel-body">
                            @if (count($avail4PA) > 0)
                            <form method="POST" action="{{ route('admin.de-thi.cau-hoi.store', $deThi->ID_MaDeThi) }}">
                                @csrf
                                <input type="hidden" name="type" value="4pa">
                                <div class="check-bar">
                                    <input type="checkbox" id="chk-all-4pa" onchange="toggleAll('chk-4pa',this.checked)">
                                    <label for="chk-all-4pa">Chọn tất cả</label>
                                    <button type="submit" class="btn-sm" style="margin-left:auto">
                                        <i class="bi bi-plus-circle"></i> Thêm đã chọn
                                    </button>
                                </div>
                                <table class="tbl">
                                    <thead>
                                        <tr><th style="width:32px"></th><th>Nội dung câu hỏi</th><th>ĐA</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($avail4PA as $q)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="chk-4pa"
                                                       name="question_ids[]"
                                                       value="{{ $q->ID_TracNghiem4PhuongAn }}">
                                            </td>
                                            <td class="q-text-cell" title="{{ $q->NoiDungCauHoi_TracNghiem4PhuongAn }}">
                                                {{ $q->NoiDungCauHoi_TracNghiem4PhuongAn }}
                                            </td>
                                            <td><span class="da-badge">{{ $q->DapAn_TracNghiem4PhuongAn }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
                            @else
                                <div class="empty-bank">Không còn câu nào cho môn/khối này trong ngân hàng</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== TAB DS ===== --}}
            <div id="tab-ds" style="display:none">
                <div class="q-split">
                    <div class="q-panel">
                        <div class="q-panel-header green">
                            <span><i class="bi bi-check-circle-fill"></i> Đã có trong đề thi</span>
                            <span class="count-chip" style="background:#e6f4ea;border-color:#a8d5ac;color:#256029">
                                {{ count($inDS) }} câu
                            </span>
                        </div>
                        <div class="q-panel-body">
                            @if (count($inDS) > 0)
                            <table class="tbl">
                                <thead>
                                    <tr><th>Nội dung câu hỏi</th><th>ĐA</th><th></th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($inDS as $q)
                                    <tr>
                                        <td class="q-text-cell" title="{{ $q->NoiDungCauHoi_TracNghiemDungSai }}">
                                            {{ $q->NoiDungCauHoi_TracNghiemDungSai }}
                                        </td>
                                        <td><span class="da-badge" style="letter-spacing:2px">{{ $q->DapAn }}</span></td>
                                        <td>
                                            <form method="POST"
                                                  action="{{ route('admin.de-thi.cau-hoi.destroy', [$deThi->ID_MaDeThi, $q->ID_DeThiChiTiet]) }}"
                                                  onsubmit="return confirm('Xóa câu này khỏi đề thi?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-danger-sm">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                                <div class="empty-bank">Chưa có câu nào trong đề</div>
                            @endif
                        </div>
                    </div>

                    <div class="q-panel">
                        <div class="q-panel-header">
                            <span><i class="bi bi-bank"></i> Ngân hàng (chưa có trong đề)</span>
                            <span class="count-chip">{{ count($availDS) }} câu</span>
                        </div>
                        <div class="q-panel-body">
                            @if (count($availDS) > 0)
                            <form method="POST" action="{{ route('admin.de-thi.cau-hoi.store', $deThi->ID_MaDeThi) }}">
                                @csrf
                                <input type="hidden" name="type" value="ds">
                                <div class="check-bar">
                                    <input type="checkbox" id="chk-all-ds" onchange="toggleAll('chk-ds',this.checked)">
                                    <label for="chk-all-ds">Chọn tất cả</label>
                                    <button type="submit" class="btn-sm" style="margin-left:auto">
                                        <i class="bi bi-plus-circle"></i> Thêm đã chọn
                                    </button>
                                </div>
                                <table class="tbl">
                                    <thead>
                                        <tr><th style="width:32px"></th><th>Nội dung câu hỏi</th><th>ĐA</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($availDS as $q)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="chk-ds"
                                                       name="question_ids[]"
                                                       value="{{ $q->ID_TracNghiemDungSai }}">
                                            </td>
                                            <td class="q-text-cell" title="{{ $q->NoiDungCauHoi_TracNghiemDungSai }}">
                                                {{ $q->NoiDungCauHoi_TracNghiemDungSai }}
                                            </td>
                                            <td><span class="da-badge" style="letter-spacing:2px">{{ $q->DapAn }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
                            @else
                                <div class="empty-bank">Không còn câu nào cho môn/khối này trong ngân hàng</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== TAB NGẮN ===== --}}
            <div id="tab-ngan" style="display:none">
                <div class="q-split">
                    <div class="q-panel">
                        <div class="q-panel-header green">
                            <span><i class="bi bi-check-circle-fill"></i> Đã có trong đề thi</span>
                            <span class="count-chip" style="background:#e6f4ea;border-color:#a8d5ac;color:#256029">
                                {{ count($inNgan) }} câu
                            </span>
                        </div>
                        <div class="q-panel-body">
                            @if (count($inNgan) > 0)
                            <table class="tbl">
                                <thead>
                                    <tr><th>Nội dung câu hỏi</th><th>ĐA</th><th></th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($inNgan as $q)
                                    <tr>
                                        <td class="q-text-cell" title="{{ $q->NoiDungCauHoi_TracNghiemTraLoiNgan }}">
                                            {{ $q->NoiDungCauHoi_TracNghiemTraLoiNgan }}
                                        </td>
                                        <td><span class="da-badge" style="letter-spacing:3px">{{ $q->DapAn }}</span></td>
                                        <td>
                                            <form method="POST"
                                                  action="{{ route('admin.de-thi.cau-hoi.destroy', [$deThi->ID_MaDeThi, $q->ID_DeThiChiTiet]) }}"
                                                  onsubmit="return confirm('Xóa câu này khỏi đề thi?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-danger-sm">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                                <div class="empty-bank">Chưa có câu nào trong đề</div>
                            @endif
                        </div>
                    </div>

                    <div class="q-panel">
                        <div class="q-panel-header">
                            <span><i class="bi bi-bank"></i> Ngân hàng (chưa có trong đề)</span>
                            <span class="count-chip">{{ count($availNgan) }} câu</span>
                        </div>
                        <div class="q-panel-body">
                            @if (count($availNgan) > 0)
                            <form method="POST" action="{{ route('admin.de-thi.cau-hoi.store', $deThi->ID_MaDeThi) }}">
                                @csrf
                                <input type="hidden" name="type" value="ngan">
                                <div class="check-bar">
                                    <input type="checkbox" id="chk-all-ngan" onchange="toggleAll('chk-ngan',this.checked)">
                                    <label for="chk-all-ngan">Chọn tất cả</label>
                                    <button type="submit" class="btn-sm" style="margin-left:auto">
                                        <i class="bi bi-plus-circle"></i> Thêm đã chọn
                                    </button>
                                </div>
                                <table class="tbl">
                                    <thead>
                                        <tr><th style="width:32px"></th><th>Nội dung câu hỏi</th><th>ĐA</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($availNgan as $q)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="chk-ngan"
                                                       name="question_ids[]"
                                                       value="{{ $q->ID_TracNghiemTraLoiNgan }}">
                                            </td>
                                            <td class="q-text-cell" title="{{ $q->NoiDungCauHoi_TracNghiemTraLoiNgan }}">
                                                {{ $q->NoiDungCauHoi_TracNghiemTraLoiNgan }}
                                            </td>
                                            <td><span class="da-badge" style="letter-spacing:3px">{{ $q->DapAn }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
                            @else
                                <div class="empty-bank">Không còn câu nào cho môn/khối này trong ngân hàng</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    window.PAGE_ROLE   = 'admin';
    window.PAGE_ACTIVE = 'quanly-dethi';

    function switchTab(name) {
        ['4pa','ds','ngan'].forEach(t => {
            document.getElementById('tab-' + t).style.display       = t === name ? '' : 'none';
            document.getElementById('tab-btn-' + t).classList.toggle('active', t === name);
        });
    }

    function toggleAll(cls, checked) {
        document.querySelectorAll('.' + cls).forEach(cb => cb.checked = checked);
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
