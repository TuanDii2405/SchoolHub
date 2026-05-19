<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Quản lý khối lớp</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div id="app-header"></div>
<div class="layout">
    <div id="app-sidebar"></div>
    <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ ADMIN</h2></div>
        <div class="content-box">
            <div class="section-title">Quản lý khối lớp</div>

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
                       placeholder="Tìm tên khối..." oninput="filterTable()">
                <button class="action-btn" onclick="location.reload()">Làm mới</button>
            </div>

            <div class="table-wrap">
                <table class="tbl" id="khoiLopTable">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên khối lớp</th>
                            <th>Số lớp học</th>
                            <th>Số môn học</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($khoiLops as $i => $kl)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $kl->Ten_KhoiLop }}</td>
                            <td>{{ $kl->so_lop }}</td>
                            <td>{{ $kl->so_mon }}</td>
                            <td>
                                <button class="btn-edit"
                                        onclick="openEdit({{ $kl->ID_KhoiLop }}, '{{ addslashes($kl->Ten_KhoiLop) }}')">
                                    Sửa
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.khoi-lop.destroy', $kl->ID_KhoiLop) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Xóa {{ addslashes($kl->Ten_KhoiLop) }}?\nKhối lớp đang có lớp học sẽ không thể xóa.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger"
                                            {{ $kl->so_lop > 0 ? 'disabled title=Đang có lớp học' : '' }}>
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="empty-notice">Chưa có khối lớp nào</td></tr>
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
            <span class="modal-header-title" id="modalTitle">Thêm khối lớp mới</span>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="modalForm" method="POST" action="{{ route('admin.khoi-lop.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tên khối lớp <span class="required">*</span></label>
                    <input class="form-input" type="text" name="Ten_KhoiLop" id="f_ten"
                           placeholder="VD: Khối 10" required maxlength="50">
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
    window.PAGE_ACTIVE = 'quanly-khoilop';

    const storeUrl   = "{{ route('admin.khoi-lop.store') }}";
    const updateBase = "{{ url('admin/khoi-lop') }}";

    function openAdd() {
        document.getElementById('modalTitle').textContent = 'Thêm khối lớp mới';
        document.getElementById('modalForm').action       = storeUrl;
        document.getElementById('formMethod').value       = 'POST';
        document.getElementById('submitBtn').textContent  = 'Thêm';
        document.getElementById('f_ten').value            = '';
        document.getElementById('modalOverlay').style.display = 'flex';
        document.getElementById('f_ten').focus();
    }

    function openEdit(id, ten) {
        document.getElementById('modalTitle').textContent = 'Sửa tên khối lớp';
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
        const rows = document.querySelectorAll('#khoiLopTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
</script>
<script src="{{ asset('assets/js/layout.js') }}"></script>
</body>
</html>
