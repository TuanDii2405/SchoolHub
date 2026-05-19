<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Quản lý chủ đề</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ ADMIN</h2></div>
        <div class="content-box">
            <div class="section-title">Quản lý chủ đề</div>

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
                <button class="action-btn" onclick="openAdd()">+ Thêm chủ đề</button>
                <input class="search-input" type="text" id="searchInput"
                       placeholder="Tìm chủ đề, môn, khối..." oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="chuDeTable">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Nội dung chủ đề</th>
                            <th>Môn học</th>
                            <th>Khối lớp</th>
                            <th>Người tạo</th>
                            <th>Số câu hỏi</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($chuDes as $i => $cd)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td style="text-align:left">{{ $cd->NoiDung_ChuDe }}</td>
                            <td>{{ $cd->Ten_MonHoc }}</td>
                            <td>{{ $cd->Ten_KhoiLop }}</td>
                            <td>{{ $cd->ten_nguoi_tao }}</td>
                            <td>{{ $cd->tong_cau_hoi }}</td>
                            <td>
                                <button class="btn-edit" onclick="openEdit(
                                    {{ $cd->ID_ChuDe }},
                                    '{{ addslashes($cd->NoiDung_ChuDe) }}',
                                    {{ $cd->ID_MonHoc }},
                                    {{ $cd->ID_KhoiLop }}
                                )">Sửa</button>

                                <form method="POST"
                                      action="{{ route('admin.chu-de.destroy', $cd->ID_ChuDe) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Xóa chủ đề này?\nCác câu hỏi trong chủ đề sẽ không thể xóa nếu đã có kỳ thi liên quan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger"
                                            {{ $cd->tong_cau_hoi > 0 ? 'title=Đang có câu hỏi' : '' }}>
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="empty-notice">Chưa có chủ đề nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

{{-- ========== MODAL THÊM / SỬA ========== --}}
<div id="modalOverlay" class="modal-overlay" style="display:none" onclick="closeOnBackdrop(event)">
    <div class="modal-box" style="width:440px">
        <div class="modal-header">
            <span class="modal-header-title" id="modalTitle">Thêm chủ đề mới</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="modalForm" method="POST" action="{{ route('admin.chu-de.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nội dung chủ đề <span class="required">*</span></label>
                    <input class="form-input" type="text" name="NoiDung_ChuDe" id="f_noidung"
                           placeholder="VD: Hàm số và đồ thị" required maxlength="255">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Môn học <span class="required">*</span></label>
                        <select class="form-select" name="ID_MonHoc" id="f_mon" required>
                            <option value="">— Chọn môn —</option>
                            @foreach ($monHocs as $mh)
                                <option value="{{ $mh->ID_MonHoc }}">{{ $mh->Ten_MonHoc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Khối lớp <span class="required">*</span></label>
                        <select class="form-select" name="ID_KhoiLop" id="f_khoi" required>
                            <option value="">— Chọn khối —</option>
                            @foreach ($khoiLops as $kl)
                                <option value="{{ $kl->ID_KhoiLop }}">{{ $kl->Ten_KhoiLop }}</option>
                            @endforeach
                        </select>
                    </div>
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
    window.PAGE_ROLE   = 'admin';
    window.PAGE_ACTIVE = 'quanly-chude';

    const storeUrl   = "{{ route('admin.chu-de.store') }}";
    const updateBase = "{{ url('admin/chu-de') }}";

    function openAdd() {
        document.getElementById('modalTitle').textContent  = 'Thêm chủ đề mới';
        document.getElementById('modalForm').action        = storeUrl;
        document.getElementById('formMethod').value        = 'POST';
        document.getElementById('submitBtn').textContent   = 'Thêm';
        document.getElementById('f_noidung').value         = '';
        document.getElementById('f_mon').value             = '';
        document.getElementById('f_khoi').value            = '';
        document.getElementById('modalOverlay').style.display = 'flex';
        document.getElementById('f_noidung').focus();
    }

    function openEdit(id, noidung, mon, khoi) {
        document.getElementById('modalTitle').textContent  = 'Sửa chủ đề';
        document.getElementById('modalForm').action        = updateBase + '/' + id;
        document.getElementById('formMethod').value        = 'PUT';
        document.getElementById('submitBtn').textContent   = 'Cập nhật';
        document.getElementById('f_noidung').value         = noidung;
        document.getElementById('f_mon').value             = mon;
        document.getElementById('f_khoi').value            = khoi;
        document.getElementById('modalOverlay').style.display = 'flex';
        document.getElementById('f_noidung').focus();
    }

    function closeModal() {
        document.getElementById('modalOverlay').style.display = 'none';
    }

    function closeOnBackdrop(e) {
        if (e.target === document.getElementById('modalOverlay')) closeModal();
    }

    function filterTable() {
        const q    = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#chuDeTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
