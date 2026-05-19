<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Quản lý đề thi</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ ADMIN</h2></div>
        <div class="content-box">
            <div class="section-title">Quản lý đề thi</div>

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
                <button class="action-btn" onclick="openAdd()">+ Tạo đề thi</button>
                <input class="search-input" type="text" id="searchInput"
                       placeholder="Tìm tên đề thi, môn, khối..." oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="deThiTable" style="table-layout:fixed">
                    <colgroup>
                        <col style="width:50px"><col><col style="width:110px">
                        <col style="width:85px"><col style="width:200px"><col style="width:110px">
                        <col style="width:90px"><col style="width:70px"><col style="width:280px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên đề thi</th>
                            <th>Môn học</th>
                            <th>Khối lớp</th>
                            <th>Mô tả</th>
                            <th>Người tạo</th>
                            <th>Ngày tạo</th>
                            <th>Tổng câu</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deThis as $i => $dt)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td style="text-align:left;white-space:normal;word-break:break-word">{{ $dt->TenDeThi }}</td>
                            <td>{{ $dt->Ten_MonHoc }}</td>
                            <td>{{ $dt->Ten_KhoiLop }}</td>
                            <td style="text-align:left;white-space:normal;word-break:break-word">{{ $dt->MoTa ?? '—' }}</td>
                            <td>{{ $dt->ten_nguoi_tao }}</td>
                            <td>{{ \Carbon\Carbon::parse($dt->NgayTao)->format('d/m/Y') }}</td>
                            <td>{{ $dt->tong_cau_hoi }}</td>
                            <td style="white-space:nowrap">
                                <a href="{{ route('admin.de-thi.cau-hoi', $dt->ID_MaDeThi) }}"
                                   class="btn-edit">Xem</a>

                                <button class="btn-edit" onclick="openEdit(
                                    {{ $dt->ID_MaDeThi }},
                                    '{{ addslashes($dt->TenDeThi) }}',
                                    {{ $dt->ID_MaMon }},
                                    {{ $dt->ID_MaKhoi }},
                                    '{{ addslashes($dt->MoTa ?? '') }}'
                                )">Sửa</button>

                                <form method="POST"
                                      action="{{ route('admin.de-thi.destroy', $dt->ID_MaDeThi) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Xóa đề thi {{ addslashes($dt->TenDeThi) }}?\nCác câu hỏi trong đề thi cũng sẽ bị xóa.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="empty-notice">Chưa có đề thi nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

{{-- ========== MODAL THÊM / SỬA ========== --}}
<div id="modalOverlay" class="modal-overlay" style="display:none" onclick="closeOnBackdrop(event)">
    <div class="modal-box" style="width:460px">
        <div class="modal-header">
            <span class="modal-header-title" id="modalTitle">Tạo đề thi mới</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="modalForm" method="POST" action="{{ route('admin.de-thi.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tên đề thi <span class="required">*</span></label>
                    <input class="form-input" type="text" name="TenDeThi" id="f_ten"
                           placeholder="VD: Đề thi học kỳ 1 - Toán 10" required maxlength="150">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Môn học <span class="required">*</span></label>
                        <select class="form-select" name="ID_MaMon" id="f_mon" required>
                            <option value="">— Chọn môn —</option>
                            @foreach ($monHocs as $mh)
                                <option value="{{ $mh->ID_MonHoc }}">{{ $mh->Ten_MonHoc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Khối lớp <span class="required">*</span></label>
                        <select class="form-select" name="ID_MaKhoi" id="f_khoi" required>
                            <option value="">— Chọn khối —</option>
                            @foreach ($khoiLops as $kl)
                                <option value="{{ $kl->ID_KhoiLop }}">{{ $kl->Ten_KhoiLop }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Mô tả</label>
                    <input class="form-input" type="text" name="MoTa" id="f_mota"
                           placeholder="Mô tả ngắn về đề thi" maxlength="255">
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
    window.PAGE_ACTIVE = 'quanly-dethi';

    const storeUrl   = "{{ route('admin.de-thi.store') }}";
    const updateBase = "{{ url('admin/de-thi') }}";

    function openAdd() {
        document.getElementById('modalTitle').textContent  = 'Tạo đề thi mới';
        document.getElementById('modalForm').action        = storeUrl;
        document.getElementById('formMethod').value        = 'POST';
        document.getElementById('submitBtn').textContent   = 'Tạo';
        document.getElementById('f_ten').value             = '';
        document.getElementById('f_mon').value             = '';
        document.getElementById('f_khoi').value            = '';
        document.getElementById('f_mota').value            = '';
        document.getElementById('modalOverlay').style.display = 'flex';
        document.getElementById('f_ten').focus();
    }

    function openEdit(id, ten, mon, khoi, mota) {
        document.getElementById('modalTitle').textContent  = 'Sửa thông tin đề thi';
        document.getElementById('modalForm').action        = updateBase + '/' + id;
        document.getElementById('formMethod').value        = 'PUT';
        document.getElementById('submitBtn').textContent   = 'Cập nhật';
        document.getElementById('f_ten').value             = ten;
        document.getElementById('f_mon').value             = mon;
        document.getElementById('f_khoi').value            = khoi;
        document.getElementById('f_mota').value            = mota;
        document.getElementById('modalOverlay').style.display = 'flex';
        document.getElementById('f_ten').focus();
    }

    function closeModal() {
        document.getElementById('modalOverlay').style.display = 'none';
    }

    function closeOnBackdrop(e) {
        if (e.target === document.getElementById('modalOverlay')) closeModal();
    }

    function filterTable() {
        const q    = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#deThiTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
