<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin – Trang chủ</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <style>
        .thongbao-list   { display:flex;flex-direction:column;gap:10px;margin-top:12px; }
        .thongbao-item   { border:1px solid var(--cerulean-200);border-radius:10px;
                           padding:12px 16px;background:#fff; }
        .thongbao-header { display:flex;justify-content:space-between;align-items:center;
                           flex-wrap:wrap;gap:6px;margin-bottom:8px; }
        .thongbao-scope  { font-size:12px;font-weight:700;color:var(--cerulean);
                           display:flex;align-items:center;gap:5px; }
        .scope-all       { color:var(--jasper); }
        .thongbao-date   { font-size:11px;color:var(--text-soft);display:flex;align-items:center;gap:4px; }
        .thongbao-content{ font-size:14px;color:var(--text-main);line-height:1.6;
                           white-space:pre-wrap;word-break:break-word;margin-bottom:10px; }
        .thongbao-footer { display:flex;justify-content:space-between;align-items:center; }
        .thongbao-author { font-size:12px;color:var(--text-soft);display:flex;align-items:center;gap:4px; }
        .thongbao-actions{ display:flex;gap:6px; }

        .filter-bar { display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:8px; }
        .filter-bar select { padding:5px 10px;border:1.5px solid var(--cerulean-300);
                             border-radius:8px;font-size:13px;color:var(--text-main);
                             background:#fff;cursor:pointer; }
    </style>
  </head>
  <body>
    <div id="app-header"></div>
    <div class="layout">
      <div id="app-sidebar"></div>
      <main class="main-content">
        <div class="role-title-box"><h2>VAI TRÒ ADMIN</h2></div>
        <div class="content-box">
          <div class="section-title">Thông báo hệ thống</div>

          @if (session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if ($errors->any())
              <div class="alert alert-danger">
                  @foreach ($errors->all() as $e) {{ $e }}<br> @endforeach
              </div>
          @endif

          <div class="action-bar">
            <button class="action-btn" onclick="openCreate()">Tạo thông báo</button>
            <button class="action-btn" id="btnFilter" onclick="toggleFilter()">Lọc thông báo</button>
            <button class="action-btn" onclick="location.reload()">Làm mới</button>
          </div>

          {{-- Filter panel --}}
          <div id="filterPanel" style="display:none;margin-top:8px;padding:12px 14px;
               background:#f4f8fd;border:1.5px solid var(--cerulean-200);border-radius:10px;">
            <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
              <div>
                <div style="font-size:11px;color:var(--text-soft);margin-bottom:4px">Khối lớp</div>
                <select id="filterKhoi" class="form-select" onchange="applyFilter()" style="min-width:130px">
                  <option value="">Tất cả khối</option>
                  @foreach ($khoiLops as $kl)
                      <option value="{{ $kl->Ten_KhoiLop }}">{{ $kl->Ten_KhoiLop }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <div style="font-size:11px;color:var(--text-soft);margin-bottom:4px">Môn học</div>
                <select id="filterMon" class="form-select" onchange="applyFilter()" style="min-width:130px">
                  <option value="">Tất cả môn</option>
                  @foreach ($monHocs as $mh)
                      <option value="{{ $mh->Ten_MonHoc }}">{{ $mh->Ten_MonHoc }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <div style="font-size:11px;color:var(--text-soft);margin-bottom:4px">Phạm vi</div>
                <select id="filterScope" class="form-select" onchange="applyFilter()" style="min-width:140px">
                  <option value="">Tất cả phạm vi</option>
                  <option value="toan-he-thong">Toàn hệ thống</option>
                  <option value="co-pham-vi">Có phạm vi</option>
                </select>
              </div>
              <button class="btn-danger" onclick="resetFilter()" style="margin-bottom:1px">Xóa lọc</button>
            </div>
          </div>

          @if(count($thongBaos) > 0)
            <div class="thongbao-list" id="thongbaoList">
              @foreach($thongBaos as $tb)
                <div class="thongbao-item"
                     data-khoi="{{ $tb->Ten_KhoiLop ?? '' }}"
                     data-mon="{{ $tb->Ten_MonHoc ?? '' }}"
                     data-scope="{{ (!$tb->Ten_KhoiLop && !$tb->Ten_MonHoc) ? 'toan-he-thong' : 'co-pham-vi' }}">
                  <div class="thongbao-header">
                    <span class="thongbao-scope {{ !$tb->Ten_KhoiLop && !$tb->Ten_MonHoc ? 'scope-all' : '' }}">
                      @if(!$tb->Ten_KhoiLop && !$tb->Ten_MonHoc)
                        <i class="bi bi-globe2"></i> Toàn hệ thống
                      @elseif($tb->Ten_KhoiLop && $tb->Ten_MonHoc)
                        <i class="bi bi-bookmark"></i> {{ $tb->Ten_KhoiLop }} – {{ $tb->Ten_MonHoc }}
                      @elseif($tb->Ten_KhoiLop)
                        <i class="bi bi-layers"></i> {{ $tb->Ten_KhoiLop }}
                      @else
                        <i class="bi bi-book"></i> {{ $tb->Ten_MonHoc }}
                      @endif
                    </span>
                    <span class="thongbao-date">
                      <i class="bi bi-clock"></i>
                      {{ \Carbon\Carbon::parse($tb->NgayTao_ThongBao)->format('d/m/Y H:i') }}
                    </span>
                  </div>
                  <div class="thongbao-content">{{ $tb->NoiDung_ThongBao }}</div>
                  <div class="thongbao-footer">
                    <span class="thongbao-author">
                        <i class="bi bi-person"></i> {{ $tb->ten_nguoi_gui }}
                    </span>
                    <div class="thongbao-actions">
                        <button class="btn-edit" onclick='openEdit(@json($tb))'>Sửa</button>
                        <form method="POST"
                              action="{{ route('admin.thong-bao.destroy', $tb->ID_ThongBao) }}"
                              style="display:inline"
                              onsubmit="return confirm('Xóa thông báo này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger">Xóa</button>
                        </form>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="empty-notice" id="thongbaoList">
              Hiện tại không có thông báo nào trên hệ thống
            </div>
          @endif
        </div>
      </main>
    </div>

    {{-- MODAL --}}
    <div id="modalTB" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeModal()">
        <div class="modal-box" style="width:520px">
            <div class="modal-header">
                <span class="modal-header-title" id="modalTitle">Tạo thông báo</span>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            <form id="formTB" method="POST" action="{{ route('admin.thong-bao.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nội dung <span class="required">*</span></label>
                        <textarea class="form-input" name="NoiDung_ThongBao" id="f_noidung"
                                  rows="5" required maxlength="2000"
                                  placeholder="Nhập nội dung thông báo..."></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Khối lớp <span style="color:var(--text-soft);font-weight:400">(tùy chọn)</span></label>
                            <select class="form-select" name="ID_KhoiLop" id="f_khoi">
                                <option value="">— Toàn hệ thống —</option>
                                @foreach ($khoiLops as $kl)
                                    <option value="{{ $kl->ID_KhoiLop }}">{{ $kl->Ten_KhoiLop }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Môn học <span style="color:var(--text-soft);font-weight:400">(tùy chọn)</span></label>
                            <select class="form-select" name="ID_MonHoc" id="f_mon">
                                <option value="">— Toàn hệ thống —</option>
                                @foreach ($monHocs as $mh)
                                    <option value="{{ $mh->ID_MonHoc }}">{{ $mh->Ten_MonHoc }}</option>
                                @endforeach
                            </select>
                        </div>
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
      window.PAGE_ACTIVE = '';

      const storeUrl   = "{{ route('admin.thong-bao.store') }}";
      const updateBase = "{{ url('admin/thong-bao') }}";

      function openCreate() {
          document.getElementById('modalTitle').textContent   = 'Tạo thông báo';
          document.getElementById('formTB').action            = storeUrl;
          document.getElementById('formMethod').value         = 'POST';
          document.getElementById('submitBtn').textContent    = 'Tạo';
          document.getElementById('f_noidung').value          = '';
          document.getElementById('f_khoi').value             = '';
          document.getElementById('f_mon').value              = '';
          document.getElementById('modalTB').style.display    = 'flex';
      }

      function openEdit(tb) {
          document.getElementById('modalTitle').textContent   = 'Sửa thông báo';
          document.getElementById('formTB').action            = updateBase + '/' + tb.ID_ThongBao;
          document.getElementById('formMethod').value         = 'PUT';
          document.getElementById('submitBtn').textContent    = 'Cập nhật';
          document.getElementById('f_noidung').value          = tb.NoiDung_ThongBao || '';
          document.getElementById('f_khoi').value             = tb.ID_KhoiLop || '';
          document.getElementById('f_mon').value              = tb.ID_MonHoc  || '';
          document.getElementById('modalTB').style.display    = 'flex';
      }

      function closeModal() {
          document.getElementById('modalTB').style.display = 'none';
      }

      function toggleFilter() {
          var panel = document.getElementById('filterPanel');
          var isOpen = panel.style.display !== 'none';
          panel.style.display = isOpen ? 'none' : 'block';
          if (isOpen) resetFilter();
      }

      function resetFilter() {
          ['filterKhoi','filterMon','filterScope'].forEach(function(id) {
              document.getElementById(id).value = '';
          });
          applyFilter();
      }

      function applyFilter() {
          const khoi  = document.getElementById('filterKhoi').value;
          const mon   = document.getElementById('filterMon').value;
          const scope = document.getElementById('filterScope').value;
          document.querySelectorAll('#thongbaoList .thongbao-item').forEach(function(el) {
              const ok =
                  (!khoi  || el.dataset.khoi  === khoi)  &&
                  (!mon   || el.dataset.mon   === mon)   &&
                  (!scope || el.dataset.scope === scope);
              el.style.display = ok ? '' : 'none';
          });
      }
    </script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
  </body>
</html>
