<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Quản lý môn học</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ ADMIN</h2></div>
        <div class="content-box">
            <div class="section-title">Quản lý môn học</div>

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
                <button class="action-btn" onclick="openAdd()">+ Thêm mới</button>
                <input class="search-input" type="text" id="searchInput"
                       placeholder="Tìm tên môn học..." oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="monHocTable">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên môn học</th>
                            <th>Số lớp học</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($monHocs as $i => $mh)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $mh->Ten_MonHoc }}</td>
                            <td>{{ $mh->so_lop }}</td>
                            <td>
                                <button class="btn-edit"
                                        onclick="openEdit({{ $mh->ID_MonHoc }}, '{{ addslashes($mh->Ten_MonHoc) }}')">
                                    Sửa
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.mon-hoc.destroy', $mh->ID_MonHoc) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Xóa {{ addslashes($mh->Ten_MonHoc) }}?\nMôn học đang có lớp học sẽ không thể xóa.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger"
                                            {{ $mh->so_lop > 0 ? 'disabled title=Đang có lớp học' : '' }}>
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="empty-notice">Chưa có môn học nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

{{-- ========== MODAL THÊM / SỬA ========== --}}
<div id="modalOverlay" class="modal-overlay" style="display:none" onclick="closeOnBackdrop(event)">
    <div class="modal-box" style="width:360px">
        <div class="modal-header">
            <span class="modal-header-title" id="modalTitle">Thêm môn học mới</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="modalForm" method="POST" action="{{ route('admin.mon-hoc.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tên môn học <span class="required">*</span></label>
                    <input class="form-input" type="text" name="Ten_MonHoc" id="f_ten"
                           placeholder="VD: Toán học" required maxlength="100">
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
    window.PAGE_ACTIVE = 'quanly-monhoc';

    const storeUrl   = "{{ route('admin.mon-hoc.store') }}";
    const updateBase = "{{ url('admin/mon-hoc') }}";

    function openAdd() {
        document.getElementById('modalTitle').textContent = 'Thêm môn học mới';
        document.getElementById('modalForm').action       = storeUrl;
        document.getElementById('formMethod').value       = 'POST';
        document.getElementById('submitBtn').textContent  = 'Thêm';
        document.getElementById('f_ten').value            = '';
        document.getElementById('modalOverlay').style.display = 'flex';
        document.getElementById('f_ten').focus();
    }

    function openEdit(id, ten) {
        document.getElementById('modalTitle').textContent = 'Sửa tên môn học';
        document.getElementById('modalForm').action       = updateBase + '/' + id;
        document.getElementById('formMethod').value       = 'PUT';
        document.getElementById('submitBtn').textContent  = 'Cập nhật';
        document.getElementById('f_ten').value            = ten;
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
        const rows = document.querySelectorAll('#monHocTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
