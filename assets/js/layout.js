/**
 * layout.js – Shared header + accordion sidebar
 * Mỗi trang set:  window.PAGE_ROLE   = 'admin' | 'giaovien' | 'hocsinh'
 *                 window.PAGE_ACTIVE = '<item-id>'   (id của sub-item đang active)
 */
(function () {
  /* ── Logo thật ĐHSP TP.HCM ── */
  var LOGO_SVG =
    '<img class="header-logo-img" src="https://i.ibb.co/s9YdMrTJ/Logo-HCMUE-Gia-tri-cot-loi-1-co-vien.png" alt="Logo Trường ĐHSP TP.HCM" />';
  var LOGO_LINK =
    '<a href="https://i.ibb.co/s9YdMrTJ/Logo-HCMUE-Gia-tri-cot-loi-1-co-vien.png" target="_blank" rel="noopener noreferrer" aria-label="Logo HCMUE">' +
    LOGO_SVG +
    "</a>";

  var MENU_ICONS = {
    taikhoan: "bi-person-gear",
    giaovu: "bi-buildings",
    thicu: "bi-ui-checks-grid",
    nganhangde: "bi-collection",
    lophoc: "bi-people",
    kythi: "bi-calendar3",
    nganhangcauhoi: "bi-patch-question",

    "quanly-giaovien": "bi-person-video3",
    "quanly-hocsinh": "bi-mortarboard",
    "quanly-khoilop": "bi-diagram-3",
    "quanly-monhoc": "bi-journal-bookmark",
    "quanly-lophoc": "bi-people",
    "quanly-kythi": "bi-calendar-check",
    "quanly-dethi": "bi-file-earmark-text",
    "quanly-chude": "bi-bookmarks",
    "quanly-cauhoi": "bi-patch-question",

    "ds-lophoc": "bi-people",
    diemdanh: "bi-clipboard2-check",
    "ds-chude": "bi-bookmarks",
    "ds-dethi": "bi-file-earmark-text",
    tn4pa: "bi-ui-radios-grid",
    tnds: "bi-check2-square",
    tntraloigan: "bi-pencil-square",
    "gv-thongtin": "bi-person-badge",
    "gv-doimatkhau": "bi-key",

    "hs-thongtin": "bi-person-badge",
    "hs-ds-kythi": "bi-calendar3",
    "hs-ds-lophoc": "bi-people",
    "hs-lichsu-lamdai": "bi-clock-history",
    "hs-xephang": "bi-bar-chart-line",
    "hs-diemdanh": "bi-clipboard2-check",
  };

  function decorateMenuLabel(id, label) {
    var icon = MENU_ICONS[id] || "bi-dot";
    return (
      '<span class="nav-icon" aria-hidden="true">' +
      '<i class="bi ' +
      icon +
      '"></i>' +
      '</span><span class="nav-text">' +
      label +
      "</span>"
    );
  }

  /* ── Cấu trúc menu theo role ── */
  var MENUS = {
    admin: [
      {
        id: "taikhoan",
        label: "Quản lý tài khoản",
        items: [
          {
            id: "quanly-giaovien",
            label: "Quản lý giáo viên",
            href: "Admin_QuanLyGiaoVien.php",
          },
          {
            id: "quanly-hocsinh",
            label: "Quản lý học sinh",
            href: "Admin_QuanLyHocSinh.php",
          },
        ],
      },
      {
        id: "giaovu",
        label: "Quản lý giáo vụ",
        items: [
          {
            id: "quanly-khoilop",
            label: "Quản lý khối lớp",
            href: "Admin_QuanLyKhoiLop.php",
          },
          {
            id: "quanly-monhoc",
            label: "Quản lý môn học",
            href: "Admin_QuanLyMonHoc.php",
          },
          {
            id: "quanly-lophoc",
            label: "Quản lý lớp học",
            href: "Admin_QuanLyLopHoc.php",
          },
        ],
      },
      {
        id: "thicu",
        label: "Quản lý thi cử",
        items: [
          {
            id: "quanly-kythi",
            label: "Quản lý kỳ thi",
            href: "Admin_QuanLyKyThi.php",
          },
          {
            id: "quanly-dethi",
            label: "Quản lý đề thi",
            href: "Admin_QuanLyDeThi.php",
          },
        ],
      },
      {
        id: "nganhangde",
        label: "Quản lý ngân hàng đề",
        items: [
          {
            id: "quanly-chude",
            label: "Quản lý chủ đề",
            href: "Admin_QuanLyChuDe.php",
          },
          {
            id: "quanly-cauhoi",
            label: "Quản lý câu hỏi",
            href: "Admin_QuanLyCauHoi.php",
          },
        ],
      },
    ],

    giaovien: [
      {
        id: "lophoc",
        label: "Quản lý lớp học",
        items: [
          {
            id: "ds-lophoc",
            label: "Danh sách lớp học",
            href: "GiaoVien_DanhSachLopHoc.php",
          },
          {
            id: "diemdanh",
            label: "Quản lý điểm danh",
            href: "GiaoVien_QuanLyDiemDanh.php",
          },
        ],
      },
      {
        id: "kythi",
        label: "Quản lý kỳ thi",
        items: [
          {
            id: "ds-chude",
            label: "Danh sách chủ đề",
            href: "GiaoVien_DanhSachChuDe.php",
          },
          {
            id: "ds-dethi",
            label: "Danh sách đề thi",
            href: "GiaoVien_DanhSachDeThi.php",
          },
        ],
      },
      {
        id: "nganhangcauhoi",
        label: "Quản lý ngân hàng câu hỏi",
        items: [
          {
            id: "tn4pa",
            label: "Trắc nghiệm 4 phương án",
            href: "GiaoVien_TracNghiem4PA.php",
          },
          {
            id: "tnds",
            label: "Trắc nghiệm đúng sai",
            href: "GiaoVien_TracNghiemDungSai.php",
          },
          {
            id: "tntraloigan",
            label: "Trắc nghiệm trả lời ngắn",
            href: "GiaoVien_TracNghiemTraLoiNgan.php",
          },
        ],
      },
      {
        id: "taikhoan",
        label: "Quản lý tài khoản",
        items: [
          {
            id: "gv-thongtin",
            label: "Thông tin cá nhân",
            href: "GiaoVien_ThongTinCaNhan.php",
          },
          {
            id: "gv-doimatkhau",
            label: "Đổi mật khẩu",
            href: "GiaoVien_DoiMatKhau.php",
          },
        ],
      },
    ],

    hocsinh: [
      {
        id: "hs-thongtin",
        label: "Thông tin cá nhân",
        href: "HocSinh_ThongTinCaNhan.php",
      },
      {
        id: "hs-ds-kythi",
        label: "Danh sách kỳ thi",
        href: "HocSinh_DanhSachKyThi.php",
      },
      {
        id: "hs-ds-lophoc",
        label: "Danh sách lớp học",
        href: "HocSinh_DanhSachLopHoc.php",
      },
      {
        id: "hs-lichsu-lamdai",
        label: "Lịch sử làm bài",
        href: "HocSinh_LichSuLamBai.php",
      },
      { id: "hs-xephang", label: "Xếp hạng", href: "HocSinh_XepHang.php" },
      {
        id: "hs-diemdanh",
        label: "Lịch sử điểm danh",
        href: "HocSinh_LichSuDiemDanh.php",
      },
    ],
  };

  /* ── Build header HTML ── */
  function buildHeader() {
    return (
      '<header class="header">' +
      '<div class="header-left">' +
      LOGO_LINK +
      "</div>" +
      '<button class="btn-logout" onclick="handleLogout()">Đăng xuất</button>' +
      "</header>"
    );
  }

  /* ── Build sidebar HTML ── */
  function buildSidebar(role, activeItem) {
    var menu = MENUS[role];
    if (!menu) return '<aside class="sidebar"></aside>';

    var html =
      '<aside class="sidebar">' +
      '<p class="greeting">Chào, Võ Tấn Duy</p>' +
      '<p class="nav-title">Thanh điều hướng chức năng</p>';

    if (role === "hocsinh") {
      /* Flat list – mỗi item là link trực tiếp */
      menu.forEach(function (item) {
        var active = item.id === activeItem ? " active" : "";
        html +=
          '<a class="nav-btn' +
          active +
          '" href="' +
          item.href +
          '">' +
          decorateMenuLabel(item.id, item.label) +
          "</a>";
      });
    } else {
      /* Accordion – group có sub-items */
      menu.forEach(function (group) {
        var groupOpen = group.items.some(function (it) {
          return it.id === activeItem;
        });
        var arrow = groupOpen ? "▲" : "▼";
        html +=
          '<button class="nav-btn nav-group-btn' +
          (groupOpen ? " active" : "") +
          '" onclick="toggleGroup(\'' +
          group.id +
          "')\">" +
          decorateMenuLabel(group.id, group.label) +
          ' <span class="nav-arrow">' +
          arrow +
          "</span></button>";
        html +=
          '<div class="nav-sub-list' +
          (groupOpen ? " open" : "") +
          '" id="group-' +
          group.id +
          '">';
        group.items.forEach(function (item) {
          var active = item.id === activeItem ? " active" : "";
          html +=
            '<a class="nav-sub-link' +
            active +
            '" href="' +
            item.href +
            '">' +
            decorateMenuLabel(item.id, item.label) +
            "</a>";
        });
        html += "</div>";
      });
    }

    html += "</aside>";
    return html;
  }

  /* ── Public: initLayout(role, activeItem) ── */
  window.initLayout = function (role, activeItem) {
    var headerSlot = document.getElementById("app-header");
    if (headerSlot) {
      var tmp = document.createElement("div");
      tmp.innerHTML = buildHeader();
      headerSlot.replaceWith(tmp.firstChild);
    }

    var sidebarSlot = document.getElementById("app-sidebar");
    if (sidebarSlot) {
      var tmp2 = document.createElement("div");
      tmp2.innerHTML = buildSidebar(role, activeItem);
      sidebarSlot.replaceWith(tmp2.firstChild);
    }
  };

  /* ── Public: toggleGroup(groupId) ── */
  window.toggleGroup = function (groupId) {
    var sub = document.getElementById("group-" + groupId);
    if (!sub) return;
    var isOpen = sub.classList.contains("open");

    /* Đóng tất cả */
    document.querySelectorAll(".nav-sub-list").forEach(function (el) {
      el.classList.remove("open");
    });
    document.querySelectorAll(".nav-group-btn").forEach(function (btn) {
      btn.classList.remove("active");
      var arrow = btn.querySelector(".nav-arrow");
      if (arrow) arrow.textContent = "▼";
    });

    /* Mở cái vừa click (nếu đang đóng) */
    if (!isOpen) {
      sub.classList.add("open");
      var parentBtn = sub.previousElementSibling;
      if (parentBtn) {
        parentBtn.classList.add("active");
        var a = parentBtn.querySelector(".nav-arrow");
        if (a) a.textContent = "▲";
      }
    }
  };

  /* ── Public: handleLogout ── */
  window.handleLogout = function () {
    if (confirm("Bạn có chắc muốn đăng xuất không?")) {
      window.location.href = "/PHP_FinalExam/auth/logout.php";
    }
  };

  function findIconByText(text) {
    var t = (text || "").toLowerCase();
    if (t.indexOf("đăng xuất") >= 0) return "bi-box-arrow-right";
    if (t.indexOf("đăng nhập") >= 0) return "bi-box-arrow-in-right";
    if (t.indexOf("đăng ký") >= 0) return "bi-person-plus";
    if (t.indexOf("đổi mật khẩu") >= 0 || t.indexOf("mật khẩu") >= 0)
      return "bi-key";
    if (t.indexOf("quay lại") >= 0) return "bi-arrow-return-left";
    if (t.indexOf("làm mới") >= 0) return "bi-arrow-clockwise";
    if (t.indexOf("lọc") >= 0) return "bi-funnel";
    if (t.indexOf("thêm") >= 0 || t.indexOf("tạo") >= 0)
      return "bi-plus-circle";
    if (t.indexOf("sửa") >= 0 || t.indexOf("cập nhật") >= 0)
      return "bi-pencil-square";
    if (t.indexOf("xóa") >= 0) return "bi-trash";
    if (t.indexOf("chi tiết") >= 0 || t.indexOf("xem") >= 0) return "bi-search";
    if (t.indexOf("lưu") >= 0) return "bi-save";
    return "bi-dot";
  }

  function decorateActionElement(el) {
    if (!el) return;
    if (el.querySelector && el.querySelector(".ui-icon")) return;
    var rawText = (el.textContent || "").trim();
    if (!rawText) return;
    var iconClass = findIconByText(rawText);
    var label = rawText.replace(
      /^\s*[\u2190-\u27BF\uE000-\uF8FF\u2600-\u26FF\uD83C-\uDBFF\uDC00-\uDFFF]+\s*/,
      "",
    );
    el.innerHTML =
      '<span class="ui-icon" aria-hidden="true">' +
      '<i class="bi ' +
      iconClass +
      '"></i>' +
      '</span><span class="ui-label">' +
      label +
      "</span>";
  }

  function applySemanticIcons() {
    var selectors = [
      ".btn-logout",
      ".action-btn",
      ".sub-btn",
      ".btn-primary",
      ".btn-edit",
      ".btn-danger",
      ".btn-submit",
      ".btn-back",
      ".tbl-link",
    ];
    document
      .querySelectorAll(selectors.join(","))
      .forEach(decorateActionElement);
  }

  /* ── Auto-init khi trang load xong ── */
  document.addEventListener("DOMContentLoaded", function () {
    if (window.PAGE_ROLE) {
      window.initLayout(window.PAGE_ROLE, window.PAGE_ACTIVE || "");
    }
    applySemanticIcons();
  });
})();
