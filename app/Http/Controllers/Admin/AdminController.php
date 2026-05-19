<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $thongBaos = DB::select(
            "SELECT tb.*, u.HoVaTen_User as ten_nguoi_gui,
                    k.Ten_KhoiLop, m.Ten_MonHoc
             FROM Thong_bao tb
             JOIN `User` u ON tb.ID_User = u.ID_User
             LEFT JOIN Khoi_lop k ON tb.ID_KhoiLop = k.ID_KhoiLop
             LEFT JOIN Mon_Hoc m ON tb.ID_MonHoc = m.ID_MonHoc
             ORDER BY tb.NgayTao_ThongBao DESC"
        );
        $monHocs  = DB::select("SELECT ID_MonHoc, Ten_MonHoc FROM Mon_Hoc ORDER BY ID_MonHoc");
        $khoiLops = DB::select("SELECT ID_KhoiLop, Ten_KhoiLop FROM Khoi_lop ORDER BY ID_KhoiLop");
        return view('admin.Admin_TrangChu', compact('thongBaos', 'monHocs', 'khoiLops'));
    }

    public function thongBaoStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'NoiDung_ThongBao' => 'required|string|max:2000',
            'ID_KhoiLop'       => 'nullable|integer|exists:Khoi_lop,ID_KhoiLop',
            'ID_MonHoc'        => 'nullable|integer|exists:Mon_Hoc,ID_MonHoc',
        ]);

        DB::table('Thong_bao')->insert([
            'ID_User'          => session('auth.id'),
            'NoiDung_ThongBao' => $data['NoiDung_ThongBao'],
            'ID_KhoiLop'       => $data['ID_KhoiLop'] ?? null,
            'ID_MonHoc'        => $data['ID_MonHoc'] ?? null,
            'NgayTao_ThongBao' => now(),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Đã tạo thông báo!');
    }

    public function thongBaoUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'NoiDung_ThongBao' => 'required|string|max:2000',
            'ID_KhoiLop'       => 'nullable|integer|exists:Khoi_lop,ID_KhoiLop',
            'ID_MonHoc'        => 'nullable|integer|exists:Mon_Hoc,ID_MonHoc',
        ]);

        DB::table('Thong_bao')->where('ID_ThongBao', $id)->update([
            'NoiDung_ThongBao' => $data['NoiDung_ThongBao'],
            'ID_KhoiLop'       => $data['ID_KhoiLop'] ?? null,
            'ID_MonHoc'        => $data['ID_MonHoc'] ?? null,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Đã cập nhật thông báo!');
    }

    public function thongBaoDestroy(int $id): RedirectResponse
    {
        DB::table('Thong_bao')->where('ID_ThongBao', $id)->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Đã xóa thông báo!');
    }

    public function hocSinh(): View
    {
        $hocSinhs = DB::select(
            "SELECT u.*, k.Ten_KhoiLop
             FROM `User` u
             LEFT JOIN Khoi_lop k ON u.PhuTrachKhoi_User = k.ID_KhoiLop
             WHERE u.PhanQuyen_User = 'student'
             ORDER BY u.ID_User"
        );
        return view('admin.Admin_QuanLyHocSinh', compact('hocSinhs'));
    }

    public function giaoVien(): View
    {
        $giaoViens = DB::select(
            "SELECT u.*, m.Ten_MonHoc, k.Ten_KhoiLop
             FROM `User` u
             LEFT JOIN Mon_Hoc m ON u.PhuTrachMon_User = m.ID_MonHoc
             LEFT JOIN Khoi_lop k ON u.PhuTrachKhoi_User = k.ID_KhoiLop
             WHERE u.PhanQuyen_User = 'teacher'
             ORDER BY u.ID_User"
        );
        $monHocs  = DB::select("SELECT ID_MonHoc, Ten_MonHoc FROM Mon_Hoc ORDER BY ID_MonHoc");
        $khoiLops = DB::select("SELECT ID_KhoiLop, Ten_KhoiLop FROM Khoi_lop ORDER BY ID_KhoiLop");
        return view('admin.Admin_QuanLyGiaoVien', compact('giaoViens', 'monHocs', 'khoiLops'));
    }

    public function giaoVienStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'HoVaTen_User'           => 'required|string|max:150',
            'EmailCaNhan_User'       => 'nullable|email|max:150|unique:User,EmailCaNhan_User',
            'SoDienThoai_User'       => 'nullable|string|max:20',
            'NgayThangNamSinh_User'  => 'nullable|date',
            'TrangThaiHoatDong_User' => 'required|in:active,inactive,locked',
            'PhuTrachMon_User'       => 'nullable|integer|exists:Mon_Hoc,ID_MonHoc',
            'PhuTrachKhoi_User'      => 'nullable|integer|exists:Khoi_lop,ID_KhoiLop',
            'mat_khau'               => 'required|string|min:6',
        ]);

        DB::table('User')->insert([
            'HoVaTen_User'           => $data['HoVaTen_User'],
            'EmailCaNhan_User'       => $data['EmailCaNhan_User'] ?? null,
            'SoDienThoai_User'       => $data['SoDienThoai_User'] ?? null,
            'NgayThangNamSinh_User'  => $data['NgayThangNamSinh_User'] ?? null,
            'TrangThaiHoatDong_User' => $data['TrangThaiHoatDong_User'],
            'PhuTrachMon_User'       => $data['PhuTrachMon_User'] ?? null,
            'PhuTrachKhoi_User'      => $data['PhuTrachKhoi_User'] ?? null,
            'PhanQuyen_User'         => 'teacher',
            'Pass_User'              => md5($data['mat_khau']),
            'NgayTaoTaiKhoan_User'   => now(),
        ]);

        return redirect()->route('admin.giao-vien')->with('success', 'Thêm giáo viên thành công!');
    }

    public function giaoVienUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'HoVaTen_User'           => 'required|string|max:150',
            'EmailCaNhan_User'       => "nullable|email|max:150|unique:User,EmailCaNhan_User,{$id},ID_User",
            'SoDienThoai_User'       => 'nullable|string|max:20',
            'NgayThangNamSinh_User'  => 'nullable|date',
            'TrangThaiHoatDong_User' => 'required|in:active,inactive,locked',
            'PhuTrachMon_User'       => 'nullable|integer|exists:Mon_Hoc,ID_MonHoc',
            'PhuTrachKhoi_User'      => 'nullable|integer|exists:Khoi_lop,ID_KhoiLop',
            'mat_khau'               => 'nullable|string|min:6',
        ]);

        $update = [
            'HoVaTen_User'           => $data['HoVaTen_User'],
            'EmailCaNhan_User'       => $data['EmailCaNhan_User'] ?? null,
            'SoDienThoai_User'       => $data['SoDienThoai_User'] ?? null,
            'NgayThangNamSinh_User'  => $data['NgayThangNamSinh_User'] ?? null,
            'TrangThaiHoatDong_User' => $data['TrangThaiHoatDong_User'],
            'PhuTrachMon_User'       => $data['PhuTrachMon_User'] ?? null,
            'PhuTrachKhoi_User'      => $data['PhuTrachKhoi_User'] ?? null,
        ];

        if (!empty($data['mat_khau'])) {
            $update['Pass_User'] = md5($data['mat_khau']);
        }

        DB::table('User')
            ->where('ID_User', $id)
            ->where('PhanQuyen_User', 'teacher')
            ->update($update);

        return redirect()->route('admin.giao-vien')->with('success', 'Cập nhật giáo viên thành công!');
    }

    public function giaoVienDestroy(int $id): RedirectResponse
    {
        try {
            DB::table('User')
                ->where('ID_User', $id)
                ->where('PhanQuyen_User', 'teacher')
                ->delete();
            return redirect()->route('admin.giao-vien')->with('success', 'Đã xóa giáo viên!');
        } catch (\Exception) {
            return redirect()->route('admin.giao-vien')->with('error', 'Không thể xóa: giáo viên đang phụ trách lớp học hoặc có dữ liệu liên quan.');
        }
    }

    public function khoiLop(): View
    {
        $khoiLops = DB::select(
            "SELECT k.ID_KhoiLop, k.Ten_KhoiLop,
                    COUNT(DISTINCT l.ID_LopHoc) as so_lop,
                    COUNT(DISTINCT l.ID_MonHoc) as so_mon
             FROM Khoi_lop k
             LEFT JOIN Lop_hoc l ON l.ID_KhoiLop = k.ID_KhoiLop
             GROUP BY k.ID_KhoiLop, k.Ten_KhoiLop
             ORDER BY k.ID_KhoiLop"
        );
        return view('admin.Admin_QuanLyKhoiLop', compact('khoiLops'));
    }

    public function monHoc(): View
    {
        $monHocs = DB::select(
            "SELECT m.ID_MonHoc, m.Ten_MonHoc,
                    COUNT(DISTINCT l.ID_LopHoc) as so_lop
             FROM Mon_Hoc m
             LEFT JOIN Lop_hoc l ON l.ID_MonHoc = m.ID_MonHoc
             GROUP BY m.ID_MonHoc, m.Ten_MonHoc
             ORDER BY m.ID_MonHoc"
        );
        return view('admin.Admin_QuanLyMonHoc', compact('monHocs'));
    }

    public function khoiLopStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'Ten_KhoiLop' => 'required|string|max:50|unique:Khoi_lop,Ten_KhoiLop',
        ]);
        DB::table('Khoi_lop')->insert(['Ten_KhoiLop' => $validated['Ten_KhoiLop']]);
        return redirect()->route('admin.khoi-lop')->with('success', 'Thêm khối lớp thành công!');
    }

    public function khoiLopUpdate(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'Ten_KhoiLop' => "required|string|max:50|unique:Khoi_lop,Ten_KhoiLop,{$id},ID_KhoiLop",
        ]);
        DB::table('Khoi_lop')->where('ID_KhoiLop', $id)->update(['Ten_KhoiLop' => $validated['Ten_KhoiLop']]);
        return redirect()->route('admin.khoi-lop')->with('success', 'Cập nhật khối lớp thành công!');
    }

    public function khoiLopDestroy(int $id): RedirectResponse
    {
        try {
            DB::table('Khoi_lop')->where('ID_KhoiLop', $id)->delete();
            return redirect()->route('admin.khoi-lop')->with('success', 'Đã xóa khối lớp!');
        } catch (\Exception) {
            return redirect()->route('admin.khoi-lop')->with('error', 'Không thể xóa: khối lớp đang có lớp học, câu hỏi hoặc kỳ thi liên quan.');
        }
    }

    public function monHocStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'Ten_MonHoc' => 'required|string|max:100|unique:Mon_Hoc,Ten_MonHoc',
        ]);
        DB::table('Mon_Hoc')->insert(['Ten_MonHoc' => $validated['Ten_MonHoc']]);
        return redirect()->route('admin.mon-hoc')->with('success', 'Thêm môn học thành công!');
    }

    public function monHocUpdate(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'Ten_MonHoc' => "required|string|max:100|unique:Mon_Hoc,Ten_MonHoc,{$id},ID_MonHoc",
        ]);
        DB::table('Mon_Hoc')->where('ID_MonHoc', $id)->update(['Ten_MonHoc' => $validated['Ten_MonHoc']]);
        return redirect()->route('admin.mon-hoc')->with('success', 'Cập nhật môn học thành công!');
    }

    public function monHocDestroy(int $id): RedirectResponse
    {
        try {
            DB::table('Mon_Hoc')->where('ID_MonHoc', $id)->delete();
            return redirect()->route('admin.mon-hoc')->with('success', 'Đã xóa môn học!');
        } catch (\Exception) {
            return redirect()->route('admin.mon-hoc')->with('error', 'Không thể xóa: môn học đang có lớp học, câu hỏi hoặc kỳ thi liên quan.');
        }
    }

    public function lopHoc(): View
    {
        $lopHocs = DB::select(
            "SELECT l.ID_LopHoc, l.TenLopHoc, l.NamHoc,
                    l.ID_KhoiLop, l.ID_MonHoc, l.ID_Teacher,
                    k.Ten_KhoiLop, m.Ten_MonHoc, u.HoVaTen_User as ten_giao_vien,
                    COUNT(lv.ID_Student) as so_hoc_sinh
             FROM Lop_hoc l
             JOIN Khoi_lop k ON l.ID_KhoiLop = k.ID_KhoiLop
             JOIN Mon_Hoc m ON l.ID_MonHoc = m.ID_MonHoc
             JOIN `User` u ON l.ID_Teacher = u.ID_User
             LEFT JOIN Lop_hoc_ThanhVien lv ON l.ID_LopHoc = lv.ID_LopHoc
             GROUP BY l.ID_LopHoc, l.TenLopHoc, l.NamHoc,
                      l.ID_KhoiLop, l.ID_MonHoc, l.ID_Teacher,
                      k.Ten_KhoiLop, m.Ten_MonHoc, u.HoVaTen_User
             ORDER BY l.NamHoc DESC, l.ID_KhoiLop, l.ID_LopHoc"
        );
        $khoiLops  = DB::select("SELECT ID_KhoiLop, Ten_KhoiLop FROM Khoi_lop ORDER BY ID_KhoiLop");
        $monHocs   = DB::select("SELECT ID_MonHoc, Ten_MonHoc FROM Mon_Hoc ORDER BY ID_MonHoc");
        $giaoViens = DB::select(
            "SELECT ID_User, HoVaTen_User FROM `User`
             WHERE PhanQuyen_User = 'teacher' AND TrangThaiHoatDong_User = 'active'
             ORDER BY HoVaTen_User"
        );
        return view('admin.Admin_QuanLyLopHoc', compact('lopHocs', 'khoiLops', 'monHocs', 'giaoViens'));
    }

    public function lopHocStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'TenLopHoc'   => 'required|string|max:100',
            'NamHoc'      => 'required|string|max:20',
            'ID_KhoiLop'  => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'ID_MonHoc'   => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_Teacher'  => 'required|integer|exists:User,ID_User',
        ]);

        $exists = DB::table('Lop_hoc')
            ->where('TenLopHoc', $data['TenLopHoc'])
            ->where('NamHoc', $data['NamHoc'])
            ->where('ID_MonHoc', $data['ID_MonHoc'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['TenLopHoc' => 'Lớp học này đã tồn tại trong năm học và môn học đã chọn.'])->withInput();
        }

        DB::table('Lop_hoc')->insert([
            'TenLopHoc'  => $data['TenLopHoc'],
            'NamHoc'     => $data['NamHoc'],
            'ID_KhoiLop' => $data['ID_KhoiLop'],
            'ID_MonHoc'  => $data['ID_MonHoc'],
            'ID_Teacher' => $data['ID_Teacher'],
        ]);

        return redirect()->route('admin.lop-hoc')->with('success', 'Thêm lớp học thành công!');
    }

    public function lopHocUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'TenLopHoc'   => 'required|string|max:100',
            'NamHoc'      => 'required|string|max:20',
            'ID_KhoiLop'  => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'ID_MonHoc'   => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_Teacher'  => 'required|integer|exists:User,ID_User',
        ]);

        $exists = DB::table('Lop_hoc')
            ->where('TenLopHoc', $data['TenLopHoc'])
            ->where('NamHoc', $data['NamHoc'])
            ->where('ID_MonHoc', $data['ID_MonHoc'])
            ->where('ID_LopHoc', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['TenLopHoc' => 'Lớp học này đã tồn tại trong năm học và môn học đã chọn.'])->withInput();
        }

        DB::table('Lop_hoc')->where('ID_LopHoc', $id)->update([
            'TenLopHoc'  => $data['TenLopHoc'],
            'NamHoc'     => $data['NamHoc'],
            'ID_KhoiLop' => $data['ID_KhoiLop'],
            'ID_MonHoc'  => $data['ID_MonHoc'],
            'ID_Teacher' => $data['ID_Teacher'],
        ]);

        return redirect()->route('admin.lop-hoc')->with('success', 'Cập nhật lớp học thành công!');
    }

    public function lopHocDestroy(int $id): RedirectResponse
    {
        try {
            DB::table('Lop_hoc_ThanhVien')->where('ID_LopHoc', $id)->delete();
            DB::table('Lop_hoc')->where('ID_LopHoc', $id)->delete();
            return redirect()->route('admin.lop-hoc')->with('success', 'Đã xóa lớp học!');
        } catch (\Exception) {
            return redirect()->route('admin.lop-hoc')->with('error', 'Không thể xóa: lớp học đang có kỳ thi hoặc dữ liệu điểm danh liên quan.');
        }
    }

    public function lopHocThemHocSinh(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'ID_Student' => 'required|integer|exists:User,ID_User',
        ]);

        $exists = DB::table('Lop_hoc_ThanhVien')
            ->where('ID_LopHoc', $id)
            ->where('ID_Student', $data['ID_Student'])
            ->exists();

        if ($exists) {
            return redirect()->route('admin.lop-hoc')->with('error', 'Học sinh này đã có trong lớp!');
        }

        DB::table('Lop_hoc_ThanhVien')->insert([
            'ID_LopHoc'   => $id,
            'ID_Student'  => $data['ID_Student'],
            'NgayThamGia' => now(),
        ]);

        return redirect()->route('admin.lop-hoc')->with('success', 'Đã thêm học sinh vào lớp!');
    }

    public function lopHocXoaHocSinh(int $lopId, int $stuId): RedirectResponse
    {
        DB::table('Lop_hoc_ThanhVien')
            ->where('ID_LopHoc', $lopId)
            ->where('ID_Student', $stuId)
            ->delete();

        return redirect()->route('admin.lop-hoc')->with('success', 'Đã xóa học sinh khỏi lớp!');
    }

    public function kyThi(): View
    {
        $kyThis = DB::select(
            "SELECT kt.*, k.Ten_KhoiLop, m.Ten_MonHoc,
                    l.TenLopHoc, dt.TenDeThi, cd.NoiDung_ChuDe
             FROM Ky_thi kt
             JOIN Khoi_lop k  ON kt.ID_KhoiLop = k.ID_KhoiLop
             JOIN Mon_Hoc m   ON kt.ID_MonHoc  = m.ID_MonHoc
             JOIN Lop_hoc l   ON kt.ID_LopHoc  = l.ID_LopHoc
             JOIN De_Thi dt   ON kt.ID_MaDeThi = dt.ID_MaDeThi
             JOIN Chu_De cd   ON kt.ID_ChuDe   = cd.ID_ChuDe
             ORDER BY kt.ID_KyThi DESC"
        );
        $khoiLops   = DB::select("SELECT ID_KhoiLop, Ten_KhoiLop FROM Khoi_lop ORDER BY ID_KhoiLop");
        $monHocs    = DB::select("SELECT ID_MonHoc, Ten_MonHoc FROM Mon_Hoc ORDER BY ID_MonHoc");
        $chuDesAll  = DB::select("SELECT ID_ChuDe, NoiDung_ChuDe, ID_MonHoc, ID_KhoiLop FROM Chu_De ORDER BY ID_ChuDe");
        $lopHocsAll = DB::select("SELECT ID_LopHoc, TenLopHoc, ID_MonHoc, ID_KhoiLop FROM Lop_hoc ORDER BY TenLopHoc");
        $deThisAll  = DB::select("SELECT ID_MaDeThi, TenDeThi, ID_MaMon, ID_MaKhoi FROM De_Thi ORDER BY TenDeThi");
        return view('admin.Admin_QuanLyKyThi', compact(
            'kyThis', 'khoiLops', 'monHocs', 'chuDesAll', 'lopHocsAll', 'deThisAll'
        ));
    }

    public function kyThiStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'Ten_KyThi'                              => 'required|string|max:150',
            'MoTa_KyThi'                             => 'nullable|string|max:255',
            'ID_KhoiLop'                             => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'ID_MonHoc'                              => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_ChuDe'                               => 'required|integer|exists:Chu_De,ID_ChuDe',
            'ID_LopHoc'                              => 'required|integer|exists:Lop_hoc,ID_LopHoc',
            'ID_MaDeThi'                             => 'required|integer|exists:De_Thi,ID_MaDeThi',
            'ThoiGianLamBai_KyThi'                   => 'required|integer|min:1',
            'ThoiGianBatDau_KyThi'                   => 'nullable|date',
            'ThoiGianKetThuc_KyThi'                  => 'nullable|date|after_or_equal:ThoiGianBatDau_KyThi',
            'SoCauHoiTracNghiem4PhuongAn_KyThi'      => 'required|integer|min:0',
            'SoCauHoiTracNghiemDungSai_KyThi'        => 'required|integer|min:0',
            'SoCauHoiTracNghiemTraLoiNgan_KyThi'     => 'required|integer|min:0',
            'PhanBoDiemTracNghiem4PhuongAn_KyThi'    => 'required|numeric|min:0',
            'PhanBoDiemTracNghiemDungSai_KyThi'      => 'required|numeric|min:0',
            'PhanBoDiemTracNghiemTraLoiNgan_KyThi'   => 'required|numeric|min:0',
        ]);

        $countErrors = $this->checkDeThiSoCau((int) $data['ID_MaDeThi'], $data);
        if ($countErrors) {
            return back()->withErrors($countErrors)->withInput();
        }

        DB::table('Ky_thi')->insert($data);
        return redirect()->route('admin.ky-thi')->with('success', 'Tạo kỳ thi thành công!');
    }

    public function kyThiUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'Ten_KyThi'                              => 'required|string|max:150',
            'MoTa_KyThi'                             => 'nullable|string|max:255',
            'ID_KhoiLop'                             => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'ID_MonHoc'                              => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_ChuDe'                               => 'required|integer|exists:Chu_De,ID_ChuDe',
            'ID_LopHoc'                              => 'required|integer|exists:Lop_hoc,ID_LopHoc',
            'ID_MaDeThi'                             => 'required|integer|exists:De_Thi,ID_MaDeThi',
            'ThoiGianLamBai_KyThi'                   => 'required|integer|min:1',
            'ThoiGianBatDau_KyThi'                   => 'nullable|date',
            'ThoiGianKetThuc_KyThi'                  => 'nullable|date|after_or_equal:ThoiGianBatDau_KyThi',
            'SoCauHoiTracNghiem4PhuongAn_KyThi'      => 'required|integer|min:0',
            'SoCauHoiTracNghiemDungSai_KyThi'        => 'required|integer|min:0',
            'SoCauHoiTracNghiemTraLoiNgan_KyThi'     => 'required|integer|min:0',
            'PhanBoDiemTracNghiem4PhuongAn_KyThi'    => 'required|numeric|min:0',
            'PhanBoDiemTracNghiemDungSai_KyThi'      => 'required|numeric|min:0',
            'PhanBoDiemTracNghiemTraLoiNgan_KyThi'   => 'required|numeric|min:0',
        ]);

        $countErrors = $this->checkDeThiSoCau((int) $data['ID_MaDeThi'], $data);
        if ($countErrors) {
            return back()->withErrors($countErrors)->withInput();
        }

        DB::table('Ky_thi')->where('ID_KyThi', $id)->update($data);
        return redirect()->route('admin.ky-thi')->with('success', 'Cập nhật kỳ thi thành công!');
    }

    public function kyThiDestroy(int $id): RedirectResponse
    {
        try {
            DB::table('Ky_thi')->where('ID_KyThi', $id)->delete();
            return redirect()->route('admin.ky-thi')->with('success', 'Đã xóa kỳ thi!');
        } catch (\Exception) {
            return redirect()->route('admin.ky-thi')->with('error', 'Không thể xóa: kỳ thi đã có học sinh làm bài (dữ liệu điểm số liên quan).');
        }
    }

    public function deThi(): View
    {
        $deThis = DB::select(
            "SELECT dt.*, m.Ten_MonHoc, k.Ten_KhoiLop, u.HoVaTen_User as ten_nguoi_tao,
                    (SELECT COUNT(*) FROM De_Thi_Chi_Tiet WHERE ID_MaDeThi = dt.ID_MaDeThi) as tong_cau_hoi
             FROM De_Thi dt
             JOIN Mon_Hoc m  ON dt.ID_MaMon    = m.ID_MonHoc
             JOIN Khoi_lop k ON dt.ID_MaKhoi   = k.ID_KhoiLop
             JOIN `User` u   ON dt.ID_NguoiTao = u.ID_User
             ORDER BY dt.NgayTao DESC"
        );
        $monHocs  = DB::select("SELECT ID_MonHoc, Ten_MonHoc FROM Mon_Hoc ORDER BY ID_MonHoc");
        $khoiLops = DB::select("SELECT ID_KhoiLop, Ten_KhoiLop FROM Khoi_lop ORDER BY ID_KhoiLop");
        return view('admin.Admin_QuanLyDeThi', compact('deThis', 'monHocs', 'khoiLops'));
    }

    public function deThiStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'TenDeThi'  => 'required|string|max:150',
            'ID_MaMon'  => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_MaKhoi' => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'MoTa'      => 'nullable|string|max:255',
        ]);

        DB::table('De_Thi')->insert([
            'TenDeThi'     => $data['TenDeThi'],
            'ID_MaMon'     => $data['ID_MaMon'],
            'ID_MaKhoi'    => $data['ID_MaKhoi'],
            'MoTa'         => $data['MoTa'] ?? null,
            'ID_NguoiTao'  => session('auth.id'),
            'NgayTao'      => now(),
        ]);

        return redirect()->route('admin.de-thi')->with('success', 'Tạo đề thi thành công!');
    }

    public function deThiUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'TenDeThi'  => 'required|string|max:150',
            'ID_MaMon'  => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_MaKhoi' => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'MoTa'      => 'nullable|string|max:255',
        ]);

        DB::table('De_Thi')->where('ID_MaDeThi', $id)->update([
            'TenDeThi'  => $data['TenDeThi'],
            'ID_MaMon'  => $data['ID_MaMon'],
            'ID_MaKhoi' => $data['ID_MaKhoi'],
            'MoTa'      => $data['MoTa'] ?? null,
        ]);

        return redirect()->route('admin.de-thi')->with('success', 'Cập nhật đề thi thành công!');
    }

    public function deThiDestroy(int $id): RedirectResponse
    {
        try {
            DB::table('De_Thi_Chi_Tiet')->where('ID_MaDeThi', $id)->delete();
            DB::table('De_Thi')->where('ID_MaDeThi', $id)->delete();
            return redirect()->route('admin.de-thi')->with('success', 'Đã xóa đề thi!');
        } catch (\Exception) {
            return redirect()->route('admin.de-thi')->with('error', 'Không thể xóa: đề thi đang được sử dụng trong kỳ thi.');
        }
    }

    public function deThiCauHoi(int $id): View
    {
        $deThi = DB::table('De_Thi')
            ->join('Mon_Hoc', 'De_Thi.ID_MaMon', '=', 'Mon_Hoc.ID_MonHoc')
            ->join('Khoi_lop', 'De_Thi.ID_MaKhoi', '=', 'Khoi_lop.ID_KhoiLop')
            ->where('De_Thi.ID_MaDeThi', $id)
            ->select('De_Thi.*', 'Mon_Hoc.Ten_MonHoc', 'Khoi_lop.Ten_KhoiLop')
            ->first();
        abort_if(!$deThi, 404);

        $in4PA = DB::select(
            "SELECT dtct.ID_DeThiChiTiet, q.ID_TracNghiem4PhuongAn,
                    q.NoiDungCauHoi_TracNghiem4PhuongAn, q.DapAn_TracNghiem4PhuongAn
             FROM De_Thi_Chi_Tiet dtct
             JOIN Cau_hoi_trac_nghiem_4_phuong_an q
               ON dtct.ID_TracNghiem4PhuongAn = q.ID_TracNghiem4PhuongAn
             WHERE dtct.ID_MaDeThi = ?
             ORDER BY q.ID_TracNghiem4PhuongAn", [$id]);

        $inDS = DB::select(
            "SELECT dtct.ID_DeThiChiTiet, q.ID_TracNghiemDungSai,
                    q.NoiDungCauHoi_TracNghiemDungSai,
                    q.DapAn_TracNghiem4PhuongAn as DapAn
             FROM De_Thi_Chi_Tiet dtct
             JOIN Cau_hoi_trac_nghiem_dung_sai q
               ON dtct.ID_TracNghiemDungSai = q.ID_TracNghiemDungSai
             WHERE dtct.ID_MaDeThi = ?
             ORDER BY q.ID_TracNghiemDungSai", [$id]);

        $inNgan = DB::select(
            "SELECT dtct.ID_DeThiChiTiet, q.ID_TracNghiemTraLoiNgan,
                    q.NoiDungCauHoi_TracNghiemTraLoiNgan,
                    CONCAT(q.KiTuThu1CuaDapAn_TracNghiemTraLoiNgan,
                           q.KiTuThu2CuaDapAn_TracNghiemTraLoiNgan,
                           q.KiTuThu3CuaDapAn_TracNghiemTraLoiNgan,
                           q.KiTuThu4CuaDapAn_TracNghiemTraLoiNgan) as DapAn
             FROM De_Thi_Chi_Tiet dtct
             JOIN Cau_hoi_tra_loi_ngan q
               ON dtct.ID_TracNghiemTraLoiNgan = q.ID_TracNghiemTraLoiNgan
             WHERE dtct.ID_MaDeThi = ?
             ORDER BY q.ID_TracNghiemTraLoiNgan", [$id]);

        $avail4PA = DB::select(
            "SELECT q.ID_TracNghiem4PhuongAn, q.NoiDungCauHoi_TracNghiem4PhuongAn,
                    q.DapAn_TracNghiem4PhuongAn
             FROM Cau_hoi_trac_nghiem_4_phuong_an q
             WHERE q.ID_MonHoc = ? AND q.ID_KhoiLop = ?
               AND q.ID_TracNghiem4PhuongAn NOT IN (
                   SELECT ID_TracNghiem4PhuongAn FROM De_Thi_Chi_Tiet
                   WHERE ID_MaDeThi = ? AND ID_TracNghiem4PhuongAn IS NOT NULL
               )
             ORDER BY q.ID_TracNghiem4PhuongAn",
            [$deThi->ID_MaMon, $deThi->ID_MaKhoi, $id]);

        $availDS = DB::select(
            "SELECT q.ID_TracNghiemDungSai, q.NoiDungCauHoi_TracNghiemDungSai,
                    q.DapAn_TracNghiem4PhuongAn as DapAn
             FROM Cau_hoi_trac_nghiem_dung_sai q
             WHERE q.ID_MonHoc = ? AND q.ID_KhoiLop = ?
               AND q.ID_TracNghiemDungSai NOT IN (
                   SELECT ID_TracNghiemDungSai FROM De_Thi_Chi_Tiet
                   WHERE ID_MaDeThi = ? AND ID_TracNghiemDungSai IS NOT NULL
               )
             ORDER BY q.ID_TracNghiemDungSai",
            [$deThi->ID_MaMon, $deThi->ID_MaKhoi, $id]);

        $availNgan = DB::select(
            "SELECT q.ID_TracNghiemTraLoiNgan, q.NoiDungCauHoi_TracNghiemTraLoiNgan,
                    CONCAT(q.KiTuThu1CuaDapAn_TracNghiemTraLoiNgan,
                           q.KiTuThu2CuaDapAn_TracNghiemTraLoiNgan,
                           q.KiTuThu3CuaDapAn_TracNghiemTraLoiNgan,
                           q.KiTuThu4CuaDapAn_TracNghiemTraLoiNgan) as DapAn
             FROM Cau_hoi_tra_loi_ngan q
             WHERE q.ID_MonHoc = ? AND q.ID_KhoiLop = ?
               AND q.ID_TracNghiemTraLoiNgan NOT IN (
                   SELECT ID_TracNghiemTraLoiNgan FROM De_Thi_Chi_Tiet
                   WHERE ID_MaDeThi = ? AND ID_TracNghiemTraLoiNgan IS NOT NULL
               )
             ORDER BY q.ID_TracNghiemTraLoiNgan",
            [$deThi->ID_MaMon, $deThi->ID_MaKhoi, $id]);

        return view('admin.Admin_QuanLyDeThiCauHoi', compact(
            'deThi', 'in4PA', 'inDS', 'inNgan', 'avail4PA', 'availDS', 'availNgan'
        ));
    }

    public function deThiCauHoiStore(Request $request, int $id): RedirectResponse
    {
        $deThi = DB::table('De_Thi')->where('ID_MaDeThi', $id)->first();
        abort_if(!$deThi, 404);

        $type        = $request->input('type');
        $questionIds = array_filter(array_map('intval', (array) $request->input('question_ids', [])));

        if (empty($questionIds) || !in_array($type, ['4pa', 'ds', 'ngan'])) {
            return back()->with('error', 'Vui lòng chọn ít nhất một câu hỏi.');
        }

        $col = match($type) {
            '4pa'  => 'ID_TracNghiem4PhuongAn',
            'ds'   => 'ID_TracNghiemDungSai',
            'ngan' => 'ID_TracNghiemTraLoiNgan',
        };

        $existing = DB::table('De_Thi_Chi_Tiet')
            ->where('ID_MaDeThi', $id)
            ->whereNotNull($col)
            ->pluck($col)
            ->toArray();

        $added = 0;
        foreach ($questionIds as $qid) {
            if (!in_array($qid, $existing)) {
                DB::table('De_Thi_Chi_Tiet')->insert([
                    'ID_MaDeThi'              => $id,
                    'ID_NguoiTao'             => session('auth.id'),
                    'ID_MaMon'                => $deThi->ID_MaMon,
                    'ID_MaKhoi'               => $deThi->ID_MaKhoi,
                    'ID_TracNghiem4PhuongAn'  => $type === '4pa'  ? $qid : null,
                    'ID_TracNghiemDungSai'    => $type === 'ds'   ? $qid : null,
                    'ID_TracNghiemTraLoiNgan' => $type === 'ngan' ? $qid : null,
                ]);
                $added++;
            }
        }

        return back()->with('success', "Đã thêm {$added} câu hỏi vào đề thi.");
    }

    public function deThiCauHoiDestroy(int $id, int $chiTietId): RedirectResponse
    {
        DB::table('De_Thi_Chi_Tiet')
            ->where('ID_DeThiChiTiet', $chiTietId)
            ->where('ID_MaDeThi', $id)
            ->delete();
        return back()->with('success', 'Đã xóa câu hỏi khỏi đề thi.');
    }

    public function deThiDemCauHoi(int $id): JsonResponse
    {
        $counts = DB::selectOne(
            "SELECT
                SUM(CASE WHEN ID_TracNghiem4PhuongAn IS NOT NULL THEN 1 ELSE 0 END) as so_4pa,
                SUM(CASE WHEN ID_TracNghiemDungSai IS NOT NULL THEN 1 ELSE 0 END) as so_ds,
                SUM(CASE WHEN ID_TracNghiemTraLoiNgan IS NOT NULL THEN 1 ELSE 0 END) as so_ngan
             FROM De_Thi_Chi_Tiet WHERE ID_MaDeThi = ?",
            [$id]
        );
        return response()->json([
            'so_4pa'  => (int) ($counts->so_4pa  ?? 0),
            'so_ds'   => (int) ($counts->so_ds   ?? 0),
            'so_ngan' => (int) ($counts->so_ngan ?? 0),
        ]);
    }

    private function checkDeThiSoCau(int $deThiId, array $data): array
    {
        $counts = DB::selectOne(
            "SELECT
                SUM(CASE WHEN ID_TracNghiem4PhuongAn IS NOT NULL THEN 1 ELSE 0 END) as so_4pa,
                SUM(CASE WHEN ID_TracNghiemDungSai IS NOT NULL THEN 1 ELSE 0 END) as so_ds,
                SUM(CASE WHEN ID_TracNghiemTraLoiNgan IS NOT NULL THEN 1 ELSE 0 END) as so_ngan
             FROM De_Thi_Chi_Tiet WHERE ID_MaDeThi = ?",
            [$deThiId]
        );
        $errs = [];
        $c4   = (int) ($counts->so_4pa  ?? 0);
        $cds  = (int) ($counts->so_ds   ?? 0);
        $cng  = (int) ($counts->so_ngan ?? 0);
        if ($data['SoCauHoiTracNghiem4PhuongAn_KyThi'] > $c4)
            $errs[] = "Đề thi chỉ có {$c4} câu 4 phương án (kỳ thi yêu cầu {$data['SoCauHoiTracNghiem4PhuongAn_KyThi']} câu).";
        if ($data['SoCauHoiTracNghiemDungSai_KyThi'] > $cds)
            $errs[] = "Đề thi chỉ có {$cds} câu đúng/sai (kỳ thi yêu cầu {$data['SoCauHoiTracNghiemDungSai_KyThi']} câu).";
        if ($data['SoCauHoiTracNghiemTraLoiNgan_KyThi'] > $cng)
            $errs[] = "Đề thi chỉ có {$cng} câu trả lời ngắn (kỳ thi yêu cầu {$data['SoCauHoiTracNghiemTraLoiNgan_KyThi']} câu).";
        return $errs;
    }

    public function chuDe(): View
    {
        $chuDes = DB::select(
            "SELECT cd.*, m.Ten_MonHoc, k.Ten_KhoiLop, u.HoVaTen_User as ten_nguoi_tao,
                    (SELECT COUNT(*) FROM Cau_hoi_trac_nghiem_4_phuong_an WHERE ID_ChuDe = cd.ID_ChuDe)
                  + (SELECT COUNT(*) FROM Cau_hoi_trac_nghiem_dung_sai     WHERE ID_ChuDe = cd.ID_ChuDe)
                  + (SELECT COUNT(*) FROM Cau_hoi_tra_loi_ngan              WHERE ID_ChuDe = cd.ID_ChuDe) as tong_cau_hoi
             FROM Chu_De cd
             JOIN Mon_Hoc m  ON cd.ID_MonHoc   = m.ID_MonHoc
             JOIN Khoi_lop k ON cd.ID_KhoiLop  = k.ID_KhoiLop
             JOIN `User` u   ON cd.ID_NguoiTao = u.ID_User
             ORDER BY cd.ID_ChuDe"
        );
        $monHocs  = DB::select("SELECT ID_MonHoc, Ten_MonHoc FROM Mon_Hoc ORDER BY ID_MonHoc");
        $khoiLops = DB::select("SELECT ID_KhoiLop, Ten_KhoiLop FROM Khoi_lop ORDER BY ID_KhoiLop");
        return view('admin.Admin_QuanLyChuDe', compact('chuDes', 'monHocs', 'khoiLops'));
    }

    public function chuDeStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'NoiDung_ChuDe' => 'required|string|max:255',
            'ID_MonHoc'     => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_KhoiLop'    => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
        ]);

        DB::table('Chu_De')->insert([
            'NoiDung_ChuDe' => $data['NoiDung_ChuDe'],
            'ID_MonHoc'     => $data['ID_MonHoc'],
            'ID_KhoiLop'    => $data['ID_KhoiLop'],
            'ID_NguoiTao'   => session('auth.id'),
        ]);

        return redirect()->route('admin.chu-de')->with('success', 'Thêm chủ đề thành công!');
    }

    public function chuDeUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'NoiDung_ChuDe' => 'required|string|max:255',
            'ID_MonHoc'     => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_KhoiLop'    => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
        ]);

        DB::table('Chu_De')->where('ID_ChuDe', $id)->update($data);
        return redirect()->route('admin.chu-de')->with('success', 'Cập nhật chủ đề thành công!');
    }

    public function chuDeDestroy(int $id): RedirectResponse
    {
        try {
            DB::table('Chu_De')->where('ID_ChuDe', $id)->delete();
            return redirect()->route('admin.chu-de')->with('success', 'Đã xóa chủ đề!');
        } catch (\Exception) {
            return redirect()->route('admin.chu-de')->with('error', 'Không thể xóa: chủ đề đang có câu hỏi hoặc kỳ thi liên quan.');
        }
    }

    public function cauHoi(): View
    {
        $cau4PA = DB::select(
            "SELECT q.*, cd.NoiDung_ChuDe as chu_de, m.Ten_MonHoc, k.Ten_KhoiLop
             FROM Cau_hoi_trac_nghiem_4_phuong_an q
             JOIN Chu_De cd  ON q.ID_ChuDe   = cd.ID_ChuDe
             JOIN Mon_Hoc m  ON q.ID_MonHoc  = m.ID_MonHoc
             JOIN Khoi_lop k ON q.ID_KhoiLop = k.ID_KhoiLop
             ORDER BY q.ID_TracNghiem4PhuongAn DESC"
        );
        $cauDS = DB::select(
            "SELECT q.*, cd.NoiDung_ChuDe as chu_de, m.Ten_MonHoc, k.Ten_KhoiLop
             FROM Cau_hoi_trac_nghiem_dung_sai q
             JOIN Chu_De cd  ON q.ID_ChuDe   = cd.ID_ChuDe
             JOIN Mon_Hoc m  ON q.ID_MonHoc  = m.ID_MonHoc
             JOIN Khoi_lop k ON q.ID_KhoiLop = k.ID_KhoiLop
             ORDER BY q.ID_TracNghiemDungSai DESC"
        );
        $cauNgan = DB::select(
            "SELECT q.*, cd.NoiDung_ChuDe as chu_de, m.Ten_MonHoc, k.Ten_KhoiLop
             FROM Cau_hoi_tra_loi_ngan q
             JOIN Chu_De cd  ON q.ID_ChuDe   = cd.ID_ChuDe
             JOIN Mon_Hoc m  ON q.ID_MonHoc  = m.ID_MonHoc
             JOIN Khoi_lop k ON q.ID_KhoiLop = k.ID_KhoiLop
             ORDER BY q.ID_TracNghiemTraLoiNgan DESC"
        );
        $chuDesAll = DB::select("SELECT ID_ChuDe, NoiDung_ChuDe, ID_MonHoc, ID_KhoiLop FROM Chu_De ORDER BY ID_ChuDe");
        $monHocs   = DB::select("SELECT ID_MonHoc, Ten_MonHoc FROM Mon_Hoc ORDER BY ID_MonHoc");
        $khoiLops  = DB::select("SELECT ID_KhoiLop, Ten_KhoiLop FROM Khoi_lop ORDER BY ID_KhoiLop");
        return view('admin.Admin_QuanLyCauHoi', compact('cau4PA', 'cauDS', 'cauNgan', 'chuDesAll', 'monHocs', 'khoiLops'));
    }

    // ── 4 Phương Án ──────────────────────────────────────────
    public function cauHoi4PAStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ID_ChuDe'   => 'required|integer|exists:Chu_De,ID_ChuDe',
            'ID_MonHoc'  => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_KhoiLop' => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'NoiDungCauHoi_TracNghiem4PhuongAn'     => 'required|string',
            'NoiDungCauTraLoi1_TracNghiem4PhuongAn' => 'required|string|max:255',
            'NoiDungCauTraLoi2_TracNghiem4PhuongAn' => 'required|string|max:255',
            'NoiDungCauTraLoi3_TracNghiem4PhuongAn' => 'required|string|max:255',
            'NoiDungCauTraLoi4_TracNghiem4PhuongAn' => 'required|string|max:255',
            'DapAn_TracNghiem4PhuongAn'             => 'required|in:A,B,C,D',
            'HuongDanGiai_TracNghiem4PhuongAn'      => 'nullable|string',
        ]);
        DB::table('Cau_hoi_trac_nghiem_4_phuong_an')->insert($data);
        return redirect()->route('admin.cau-hoi')->with('success', 'Thêm câu hỏi 4PA thành công!')->with('active_tab', '4pa');
    }

    public function cauHoi4PAUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'ID_ChuDe'   => 'required|integer|exists:Chu_De,ID_ChuDe',
            'ID_MonHoc'  => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_KhoiLop' => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'NoiDungCauHoi_TracNghiem4PhuongAn'     => 'required|string',
            'NoiDungCauTraLoi1_TracNghiem4PhuongAn' => 'required|string|max:255',
            'NoiDungCauTraLoi2_TracNghiem4PhuongAn' => 'required|string|max:255',
            'NoiDungCauTraLoi3_TracNghiem4PhuongAn' => 'required|string|max:255',
            'NoiDungCauTraLoi4_TracNghiem4PhuongAn' => 'required|string|max:255',
            'DapAn_TracNghiem4PhuongAn'             => 'required|in:A,B,C,D',
            'HuongDanGiai_TracNghiem4PhuongAn'      => 'nullable|string',
        ]);
        DB::table('Cau_hoi_trac_nghiem_4_phuong_an')->where('ID_TracNghiem4PhuongAn', $id)->update($data);
        return redirect()->route('admin.cau-hoi')->with('success', 'Cập nhật câu hỏi 4PA thành công!')->with('active_tab', '4pa');
    }

    public function cauHoi4PADestroy(int $id): RedirectResponse
    {
        DB::table('De_Thi_Chi_Tiet')->where('ID_TracNghiem4PhuongAn', $id)->delete();
        DB::table('Cau_hoi_trac_nghiem_4_phuong_an')->where('ID_TracNghiem4PhuongAn', $id)->delete();
        return redirect()->route('admin.cau-hoi')->with('success', 'Đã xóa câu hỏi!')->with('active_tab', '4pa');
    }

    // ── Đúng Sai ─────────────────────────────────────────────
    public function cauHoiDSStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ID_ChuDe'   => 'required|integer|exists:Chu_De,ID_ChuDe',
            'ID_MonHoc'  => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_KhoiLop' => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'NoiDungCauHoi_TracNghiemDungSai'    => 'required|string',
            'NoiDungMenhDe1_TracNghiemDungSai'   => 'required|string|max:255',
            'NoiDungMenhDe2_TracNghiemDungSai'   => 'required|string|max:255',
            'NoiDungMenhDe3_TracNghiemDungSai'   => 'required|string|max:255',
            'NoiDungMenhDe4_TracNghiemDungSai'   => 'required|string|max:255',
            'DapAn_TracNghiem4PhuongAn'          => ['required', 'regex:/^[TF]{4}$/'],
            'HuongDanGiaiMenhDe1_TracNghiemDungSai' => 'nullable|string',
            'HuongDanGiaiMenhDe2_TracNghiemDungSai' => 'nullable|string',
            'HuongDanGiaiMenhDe3_TracNghiemDungSai' => 'nullable|string',
            'HuongDanGiaiMenhDe4_TracNghiemDungSai' => 'nullable|string',
        ]);
        DB::table('Cau_hoi_trac_nghiem_dung_sai')->insert($data);
        return redirect()->route('admin.cau-hoi')->with('success', 'Thêm câu hỏi Đúng/Sai thành công!')->with('active_tab', 'dung-sai');
    }

    public function cauHoiDSUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'ID_ChuDe'   => 'required|integer|exists:Chu_De,ID_ChuDe',
            'ID_MonHoc'  => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_KhoiLop' => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'NoiDungCauHoi_TracNghiemDungSai'    => 'required|string',
            'NoiDungMenhDe1_TracNghiemDungSai'   => 'required|string|max:255',
            'NoiDungMenhDe2_TracNghiemDungSai'   => 'required|string|max:255',
            'NoiDungMenhDe3_TracNghiemDungSai'   => 'required|string|max:255',
            'NoiDungMenhDe4_TracNghiemDungSai'   => 'required|string|max:255',
            'DapAn_TracNghiem4PhuongAn'          => ['required', 'regex:/^[TF]{4}$/'],
            'HuongDanGiaiMenhDe1_TracNghiemDungSai' => 'nullable|string',
            'HuongDanGiaiMenhDe2_TracNghiemDungSai' => 'nullable|string',
            'HuongDanGiaiMenhDe3_TracNghiemDungSai' => 'nullable|string',
            'HuongDanGiaiMenhDe4_TracNghiemDungSai' => 'nullable|string',
        ]);
        DB::table('Cau_hoi_trac_nghiem_dung_sai')->where('ID_TracNghiemDungSai', $id)->update($data);
        return redirect()->route('admin.cau-hoi')->with('success', 'Cập nhật câu hỏi Đúng/Sai thành công!')->with('active_tab', 'dung-sai');
    }

    public function cauHoiDSDestroy(int $id): RedirectResponse
    {
        DB::table('De_Thi_Chi_Tiet')->where('ID_TracNghiemDungSai', $id)->delete();
        DB::table('Cau_hoi_trac_nghiem_dung_sai')->where('ID_TracNghiemDungSai', $id)->delete();
        return redirect()->route('admin.cau-hoi')->with('success', 'Đã xóa câu hỏi!')->with('active_tab', 'dung-sai');
    }

    // ── Trả lời ngắn ─────────────────────────────────────────
    public function cauHoiNganStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ID_ChuDe'   => 'required|integer|exists:Chu_De,ID_ChuDe',
            'ID_MonHoc'  => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_KhoiLop' => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'NoiDungCauHoi_TracNghiemTraLoiNgan'       => 'required|string',
            'KiTuThu1CuaDapAn_TracNghiemTraLoiNgan'    => 'required|string|size:1',
            'KiTuThu2CuaDapAn_TracNghiemTraLoiNgan'    => 'required|string|size:1',
            'KiTuThu3CuaDapAn_TracNghiemTraLoiNgan'    => 'required|string|size:1',
            'KiTuThu4CuaDapAn_TracNghiemTraLoiNgan'    => 'required|string|size:1',
            'HuongDanGiai_TracNghiemTraLoiNgan'        => 'nullable|string',
        ]);
        DB::table('Cau_hoi_tra_loi_ngan')->insert($data);
        return redirect()->route('admin.cau-hoi')->with('success', 'Thêm câu hỏi trả lời ngắn thành công!')->with('active_tab', 'tra-loi-ngan');
    }

    public function cauHoiNganUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'ID_ChuDe'   => 'required|integer|exists:Chu_De,ID_ChuDe',
            'ID_MonHoc'  => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_KhoiLop' => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'NoiDungCauHoi_TracNghiemTraLoiNgan'       => 'required|string',
            'KiTuThu1CuaDapAn_TracNghiemTraLoiNgan'    => 'required|string|size:1',
            'KiTuThu2CuaDapAn_TracNghiemTraLoiNgan'    => 'required|string|size:1',
            'KiTuThu3CuaDapAn_TracNghiemTraLoiNgan'    => 'required|string|size:1',
            'KiTuThu4CuaDapAn_TracNghiemTraLoiNgan'    => 'required|string|size:1',
            'HuongDanGiai_TracNghiemTraLoiNgan'        => 'nullable|string',
        ]);
        DB::table('Cau_hoi_tra_loi_ngan')->where('ID_TracNghiemTraLoiNgan', $id)->update($data);
        return redirect()->route('admin.cau-hoi')->with('success', 'Cập nhật câu hỏi trả lời ngắn thành công!')->with('active_tab', 'tra-loi-ngan');
    }

    public function cauHoiNganDestroy(int $id): RedirectResponse
    {
        DB::table('De_Thi_Chi_Tiet')->where('ID_TracNghiemTraLoiNgan', $id)->delete();
        DB::table('Cau_hoi_tra_loi_ngan')->where('ID_TracNghiemTraLoiNgan', $id)->delete();
        return redirect()->route('admin.cau-hoi')->with('success', 'Đã xóa câu hỏi!')->with('active_tab', 'tra-loi-ngan');
    }

    public function hocSinhStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'HoVaTen_User'           => 'required|string|max:150',
            'EmailCaNhan_User'       => 'nullable|email|max:150|unique:User,EmailCaNhan_User',
            'SoDienThoai_User'       => 'nullable|string|max:20',
            'NgayThangNamSinh_User'  => 'nullable|date',
            'TrangThaiHoatDong_User' => 'required|in:active,inactive,locked',
            'mat_khau'               => 'required|string|min:6',
        ]);

        DB::table('User')->insert([
            'HoVaTen_User'           => $data['HoVaTen_User'],
            'EmailCaNhan_User'       => $data['EmailCaNhan_User'] ?? null,
            'SoDienThoai_User'       => $data['SoDienThoai_User'] ?? null,
            'NgayThangNamSinh_User'  => $data['NgayThangNamSinh_User'] ?? null,
            'TrangThaiHoatDong_User' => $data['TrangThaiHoatDong_User'],
            'PhanQuyen_User'         => 'student',
            'Pass_User'              => md5($data['mat_khau']),
            'NgayTaoTaiKhoan_User'   => now(),
        ]);

        return redirect()->route('admin.hoc-sinh')->with('success', 'Thêm học sinh thành công!');
    }

    public function hocSinhUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'HoVaTen_User'           => 'required|string|max:150',
            'EmailCaNhan_User'       => "nullable|email|max:150|unique:User,EmailCaNhan_User,{$id},ID_User",
            'SoDienThoai_User'       => 'nullable|string|max:20',
            'NgayThangNamSinh_User'  => 'nullable|date',
            'TrangThaiHoatDong_User' => 'required|in:active,inactive,locked',
            'mat_khau'               => 'nullable|string|min:6',
        ]);

        $update = [
            'HoVaTen_User'           => $data['HoVaTen_User'],
            'EmailCaNhan_User'       => $data['EmailCaNhan_User'] ?? null,
            'SoDienThoai_User'       => $data['SoDienThoai_User'] ?? null,
            'NgayThangNamSinh_User'  => $data['NgayThangNamSinh_User'] ?? null,
            'TrangThaiHoatDong_User' => $data['TrangThaiHoatDong_User'],
        ];

        if (!empty($data['mat_khau'])) {
            $update['Pass_User'] = md5($data['mat_khau']);
        }

        DB::table('User')
            ->where('ID_User', $id)
            ->where('PhanQuyen_User', 'student')
            ->update($update);

        return redirect()->route('admin.hoc-sinh')->with('success', 'Cập nhật học sinh thành công!');
    }

    public function hocSinhDestroy(int $id): RedirectResponse
    {
        try {
            DB::table('Lop_hoc_ThanhVien')->where('ID_Student', $id)->delete();
            DB::table('User')
                ->where('ID_User', $id)
                ->where('PhanQuyen_User', 'student')
                ->delete();
            return redirect()->route('admin.hoc-sinh')->with('success', 'Đã xóa học sinh!');
        } catch (\Exception) {
            return redirect()->route('admin.hoc-sinh')->with('error', 'Không thể xóa: học sinh có dữ liệu liên quan (điểm thi, đơn xin nghỉ...).');
        }
    }

    public function impersonate(Request $request, int $id): RedirectResponse
    {
        $user = DB::selectOne(
            "SELECT ID_User, HoVaTen_User, PhanQuyen_User, TrangThaiHoatDong_User
             FROM `User` WHERE ID_User = ? AND PhanQuyen_User IN ('teacher','student')",
            [$id]
        );

        if (!$user) abort(404);

        $request->session()->put('impersonator', $request->session()->get('auth'));
        $request->session()->put('auth', [
            'id'        => (int) $user->ID_User,
            'name'      => (string) $user->HoVaTen_User,
            'role'      => (string) $user->PhanQuyen_User,
            'logged_at' => now()->toIso8601String(),
        ]);

        $cookie = \Illuminate\Support\Facades\Cookie::make(
            'admin_impersonating',
            (string) $user->HoVaTen_User,
            120, '/', null, false, false
        );

        $redirect = $user->PhanQuyen_User === 'teacher'
            ? route('teacher.dashboard')
            : route('student.dashboard');

        return redirect($redirect)->withCookie($cookie);
    }

    public function impersonateBack(Request $request): RedirectResponse
    {
        $impersonator = $request->session()->get('impersonator');
        if (!$impersonator) {
            return redirect()->route('admin.dashboard');
        }

        $request->session()->put('auth', $impersonator);
        $request->session()->forget('impersonator');

        $expiredCookie = \Illuminate\Support\Facades\Cookie::make(
            'admin_impersonating', '', -1, '/', null, false, false
        );

        return redirect()->route('admin.dashboard')->withCookie($expiredCookie);
    }
}
