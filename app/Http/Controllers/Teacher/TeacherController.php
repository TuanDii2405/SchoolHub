<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function dashboard(): View
    {
        $teacherId = session('auth.id');
        $teacher   = DB::table('User')->where('ID_User', $teacherId)->first();

        $thongBaos = DB::select(
            "SELECT tb.*, u.HoVaTen_User as ten_nguoi_gui,
                    k.Ten_KhoiLop, m.Ten_MonHoc
             FROM Thong_bao tb
             JOIN `User` u ON tb.ID_User = u.ID_User
             LEFT JOIN Khoi_lop k ON tb.ID_KhoiLop = k.ID_KhoiLop
             LEFT JOIN Mon_Hoc m ON tb.ID_MonHoc = m.ID_MonHoc
             WHERE (tb.ID_KhoiLop IS NULL AND tb.ID_MonHoc IS NULL)
                OR tb.ID_User = ?
                OR tb.ID_KhoiLop = ?
                OR tb.ID_MonHoc = ?
             ORDER BY tb.NgayTao_ThongBao DESC",
            [
                $teacherId,
                $teacher->PhuTrachKhoi_User ?? 0,
                $teacher->PhuTrachMon_User  ?? 0,
            ]
        );

        $khoiLops = DB::select("SELECT ID_KhoiLop, Ten_KhoiLop FROM Khoi_lop ORDER BY ID_KhoiLop");
        $monHocs  = DB::select("SELECT ID_MonHoc, Ten_MonHoc FROM Mon_Hoc ORDER BY Ten_MonHoc");

        return view('teacher.GiaoVien_TrangChu', compact('thongBaos', 'khoiLops', 'monHocs', 'teacher'));
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
            'ID_MonHoc'        => $data['ID_MonHoc']  ?? null,
            'NgayTao_ThongBao' => now(),
        ]);
        return redirect()->route('teacher.dashboard')->with('success', 'Đã tạo thông báo!');
    }

    public function thongBaoUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'NoiDung_ThongBao' => 'required|string|max:2000',
            'ID_KhoiLop'       => 'nullable|integer|exists:Khoi_lop,ID_KhoiLop',
            'ID_MonHoc'        => 'nullable|integer|exists:Mon_Hoc,ID_MonHoc',
        ]);
        DB::table('Thong_bao')
            ->where('ID_ThongBao', $id)
            ->where('ID_User', session('auth.id'))
            ->update([
                'NoiDung_ThongBao' => $data['NoiDung_ThongBao'],
                'ID_KhoiLop'       => $data['ID_KhoiLop'] ?? null,
                'ID_MonHoc'        => $data['ID_MonHoc']  ?? null,
            ]);
        return redirect()->route('teacher.dashboard')->with('success', 'Đã cập nhật thông báo!');
    }

    public function thongBaoDestroy(int $id): RedirectResponse
    {
        DB::table('Thong_bao')
            ->where('ID_ThongBao', $id)
            ->where('ID_User', session('auth.id'))
            ->delete();
        return redirect()->route('teacher.dashboard')->with('success', 'Đã xóa thông báo!');
    }

    public function lopHoc(Request $request): View
    {
        $teacherId = $request->session()->get('auth.id');
        $lopHocs = DB::select(
            "SELECT l.ID_LopHoc, l.TenLopHoc, l.NamHoc,
                    l.ID_KhoiLop, l.ID_MonHoc, l.ID_Teacher, l.ID_Student,
                    k.Ten_KhoiLop, m.Ten_MonHoc,
                    COUNT(lv.ID_Student) as so_hoc_sinh
             FROM Lop_hoc l
             JOIN Khoi_lop k ON l.ID_KhoiLop = k.ID_KhoiLop
             JOIN Mon_Hoc m ON l.ID_MonHoc = m.ID_MonHoc
             LEFT JOIN Lop_hoc_ThanhVien lv ON l.ID_LopHoc = lv.ID_LopHoc
             WHERE l.ID_Teacher = ?
             GROUP BY l.ID_LopHoc, l.TenLopHoc, l.NamHoc,
                      l.ID_KhoiLop, l.ID_MonHoc, l.ID_Teacher, l.ID_Student,
                      k.Ten_KhoiLop, m.Ten_MonHoc
             ORDER BY l.ID_LopHoc",
            [$teacherId]
        );

        $studentsPerClass = [];
        foreach ($lopHocs as $lop) {
            $studentsPerClass[$lop->ID_LopHoc] = DB::select(
                "SELECT u.ID_User, u.HoVaTen_User, u.EmailCaNhan_User,
                        u.SoDienThoai_User, u.NgayThangNamSinh_User, lv.NgayThamGia
                 FROM Lop_hoc_ThanhVien lv
                 JOIN `User` u ON lv.ID_Student = u.ID_User
                 WHERE lv.ID_LopHoc = ?
                 ORDER BY u.HoVaTen_User",
                [$lop->ID_LopHoc]
            );
        }

        return view('teacher.GiaoVien_DanhSachLopHoc', compact('lopHocs', 'studentsPerClass'));
    }

    public function chuDe(Request $request): View
    {
        $teacherId = $request->session()->get('auth.id');
        $chuDes = DB::select(
            "SELECT cd.*, m.Ten_MonHoc, k.Ten_KhoiLop,
                    (SELECT COUNT(*) FROM Cau_hoi_trac_nghiem_4_phuong_an WHERE ID_ChuDe = cd.ID_ChuDe)
                  + (SELECT COUNT(*) FROM Cau_hoi_trac_nghiem_dung_sai WHERE ID_ChuDe = cd.ID_ChuDe)
                  + (SELECT COUNT(*) FROM Cau_hoi_tra_loi_ngan WHERE ID_ChuDe = cd.ID_ChuDe) as tong_cau_hoi
             FROM Chu_De cd
             JOIN Mon_Hoc m ON cd.ID_MonHoc = m.ID_MonHoc
             JOIN Khoi_lop k ON cd.ID_KhoiLop = k.ID_KhoiLop
             WHERE cd.ID_NguoiTao = ?
             ORDER BY cd.ID_ChuDe",
            [$teacherId]
        );
        $monHocs  = DB::select("SELECT ID_MonHoc, Ten_MonHoc FROM Mon_Hoc ORDER BY Ten_MonHoc");
        $khoiLops = DB::select("SELECT ID_KhoiLop, Ten_KhoiLop FROM Khoi_lop ORDER BY ID_KhoiLop");
        return view('teacher.GiaoVien_DanhSachChuDe', compact('chuDes', 'monHocs', 'khoiLops'));
    }

    public function deThi(Request $request): View
    {
        $teacherId = $request->session()->get('auth.id');
        $deThis = DB::select(
            "SELECT dt.*, m.Ten_MonHoc, k.Ten_KhoiLop,
                    (SELECT COUNT(*) FROM De_Thi_Chi_Tiet WHERE ID_MaDeThi = dt.ID_MaDeThi) as tong_cau_hoi
             FROM De_Thi dt
             JOIN Mon_Hoc m ON dt.ID_MaMon = m.ID_MonHoc
             JOIN Khoi_lop k ON dt.ID_MaKhoi = k.ID_KhoiLop
             WHERE dt.ID_NguoiTao = ?
             ORDER BY dt.ID_MaDeThi",
            [$teacherId]
        );
        $monHocs  = DB::select("SELECT ID_MonHoc, Ten_MonHoc FROM Mon_Hoc ORDER BY ID_MonHoc");
        $khoiLops = DB::select("SELECT ID_KhoiLop, Ten_KhoiLop FROM Khoi_lop ORDER BY ID_KhoiLop");
        return view('teacher.GiaoVien_DanhSachDeThi', compact('deThis', 'monHocs', 'khoiLops'));
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
            'TenDeThi'    => $data['TenDeThi'],
            'ID_MaMon'    => $data['ID_MaMon'],
            'ID_MaKhoi'   => $data['ID_MaKhoi'],
            'MoTa'        => $data['MoTa'] ?? null,
            'ID_NguoiTao' => session('auth.id'),
            'NgayTao'     => now(),
        ]);
        return redirect()->route('teacher.de-thi')->with('success', 'Tạo đề thi thành công!');
    }

    public function deThiUpdate(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'TenDeThi'  => 'required|string|max:150',
            'ID_MaMon'  => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_MaKhoi' => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
            'MoTa'      => 'nullable|string|max:255',
        ]);
        DB::table('De_Thi')
            ->where('ID_MaDeThi', $id)
            ->where('ID_NguoiTao', session('auth.id'))
            ->update([
                'TenDeThi'  => $data['TenDeThi'],
                'ID_MaMon'  => $data['ID_MaMon'],
                'ID_MaKhoi' => $data['ID_MaKhoi'],
                'MoTa'      => $data['MoTa'] ?? null,
            ]);
        return redirect()->route('teacher.de-thi')->with('success', 'Cập nhật đề thi thành công!');
    }

    public function deThiDestroy(int $id): RedirectResponse
    {
        try {
            DB::table('De_Thi_Chi_Tiet')->where('ID_MaDeThi', $id)->delete();
            DB::table('De_Thi')
                ->where('ID_MaDeThi', $id)
                ->where('ID_NguoiTao', session('auth.id'))
                ->delete();
            return redirect()->route('teacher.de-thi')->with('success', 'Đã xóa đề thi!');
        } catch (\Exception) {
            return redirect()->route('teacher.de-thi')->with('error', 'Không thể xóa: đề thi đang được sử dụng trong kỳ thi.');
        }
    }

    public function deThiCauHoi(int $id): View
    {
        $deThi = DB::table('De_Thi')
            ->join('Mon_Hoc', 'De_Thi.ID_MaMon', '=', 'Mon_Hoc.ID_MonHoc')
            ->join('Khoi_lop', 'De_Thi.ID_MaKhoi', '=', 'Khoi_lop.ID_KhoiLop')
            ->where('De_Thi.ID_MaDeThi', $id)
            ->where('De_Thi.ID_NguoiTao', session('auth.id'))
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

        return view('teacher.GiaoVien_QuanLyDeThiCauHoi', compact(
            'deThi', 'in4PA', 'inDS', 'inNgan', 'avail4PA', 'availDS', 'availNgan'
        ));
    }

    public function deThiCauHoiStore(Request $request, int $id): RedirectResponse
    {
        $deThi = DB::table('De_Thi')
            ->where('ID_MaDeThi', $id)
            ->where('ID_NguoiTao', session('auth.id'))
            ->first();
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

    public function tracNghiem4PA(Request $request): View
    {
        $teacherId = $request->session()->get('auth.id');
        $cauHois = DB::select(
            "SELECT q.*, cd.NoiDung_ChuDe, m.Ten_MonHoc, k.Ten_KhoiLop
             FROM Cau_hoi_trac_nghiem_4_phuong_an q
             JOIN Chu_De cd ON q.ID_ChuDe = cd.ID_ChuDe
             JOIN Mon_Hoc m ON q.ID_MonHoc = m.ID_MonHoc
             JOIN Khoi_lop k ON q.ID_KhoiLop = k.ID_KhoiLop
             WHERE cd.ID_NguoiTao = ?
             ORDER BY q.ID_TracNghiem4PhuongAn",
            [$teacherId]
        );
        $monHocs   = DB::select("SELECT * FROM Mon_Hoc ORDER BY Ten_MonHoc");
        $khoiLops  = DB::select("SELECT * FROM Khoi_lop ORDER BY Ten_KhoiLop");
        $chuDesAll = DB::select(
            "SELECT cd.* FROM Chu_De cd WHERE cd.ID_NguoiTao = ? ORDER BY cd.NoiDung_ChuDe",
            [$teacherId]
        );
        return view('teacher.GiaoVien_TracNghiem4PA', compact('cauHois', 'monHocs', 'khoiLops', 'chuDesAll'));
    }

    public function cauHoi4PAStore(Request $request): RedirectResponse
    {
        $teacherId = session('auth.id');
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
        $cd = DB::selectOne("SELECT ID_ChuDe FROM Chu_De WHERE ID_ChuDe = ? AND ID_NguoiTao = ?", [$data['ID_ChuDe'], $teacherId]);
        if (!$cd) abort(403);
        DB::table('Cau_hoi_trac_nghiem_4_phuong_an')->insert($data);
        return redirect()->route('teacher.4pa')->with('success', 'Thêm câu hỏi 4PA thành công!');
    }

    public function cauHoi4PAUpdate(Request $request, int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
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
        $q = DB::selectOne(
            "SELECT q.ID_TracNghiem4PhuongAn FROM Cau_hoi_trac_nghiem_4_phuong_an q
             JOIN Chu_De cd ON q.ID_ChuDe = cd.ID_ChuDe
             WHERE q.ID_TracNghiem4PhuongAn = ? AND cd.ID_NguoiTao = ?", [$id, $teacherId]
        );
        if (!$q) abort(403);
        DB::table('Cau_hoi_trac_nghiem_4_phuong_an')->where('ID_TracNghiem4PhuongAn', $id)->update($data);
        return redirect()->route('teacher.4pa')->with('success', 'Cập nhật câu hỏi 4PA thành công!');
    }

    public function cauHoi4PADestroy(int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
        $q = DB::selectOne(
            "SELECT q.ID_TracNghiem4PhuongAn FROM Cau_hoi_trac_nghiem_4_phuong_an q
             JOIN Chu_De cd ON q.ID_ChuDe = cd.ID_ChuDe
             WHERE q.ID_TracNghiem4PhuongAn = ? AND cd.ID_NguoiTao = ?", [$id, $teacherId]
        );
        if (!$q) abort(403);
        DB::table('De_Thi_Chi_Tiet')->where('ID_TracNghiem4PhuongAn', $id)->delete();
        DB::table('Cau_hoi_trac_nghiem_4_phuong_an')->where('ID_TracNghiem4PhuongAn', $id)->delete();
        return redirect()->route('teacher.4pa')->with('success', 'Đã xóa câu hỏi!');
    }

    public function tracNghiemDungSai(Request $request): View
    {
        $teacherId = $request->session()->get('auth.id');
        $cauHois = DB::select(
            "SELECT q.*, cd.NoiDung_ChuDe, m.Ten_MonHoc, k.Ten_KhoiLop
             FROM Cau_hoi_trac_nghiem_dung_sai q
             JOIN Chu_De cd ON q.ID_ChuDe = cd.ID_ChuDe
             JOIN Mon_Hoc m ON q.ID_MonHoc = m.ID_MonHoc
             JOIN Khoi_lop k ON q.ID_KhoiLop = k.ID_KhoiLop
             WHERE cd.ID_NguoiTao = ?
             ORDER BY q.ID_TracNghiemDungSai",
            [$teacherId]
        );
        $monHocs   = DB::select("SELECT * FROM Mon_Hoc ORDER BY Ten_MonHoc");
        $khoiLops  = DB::select("SELECT * FROM Khoi_lop ORDER BY Ten_KhoiLop");
        $chuDesAll = DB::select(
            "SELECT cd.* FROM Chu_De cd WHERE cd.ID_NguoiTao = ? ORDER BY cd.NoiDung_ChuDe",
            [$teacherId]
        );
        return view('teacher.GiaoVien_TracNghiemDungSai', compact('cauHois', 'monHocs', 'khoiLops', 'chuDesAll'));
    }

    public function cauHoiDSStore(Request $request): RedirectResponse
    {
        $teacherId = session('auth.id');
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
        $cd = DB::selectOne("SELECT ID_ChuDe FROM Chu_De WHERE ID_ChuDe = ? AND ID_NguoiTao = ?", [$data['ID_ChuDe'], $teacherId]);
        if (!$cd) abort(403);
        DB::table('Cau_hoi_trac_nghiem_dung_sai')->insert($data);
        return redirect()->route('teacher.dung-sai')->with('success', 'Thêm câu hỏi Đúng/Sai thành công!');
    }

    public function cauHoiDSUpdate(Request $request, int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
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
        $q = DB::selectOne(
            "SELECT q.ID_TracNghiemDungSai FROM Cau_hoi_trac_nghiem_dung_sai q
             JOIN Chu_De cd ON q.ID_ChuDe = cd.ID_ChuDe
             WHERE q.ID_TracNghiemDungSai = ? AND cd.ID_NguoiTao = ?", [$id, $teacherId]
        );
        if (!$q) abort(403);
        DB::table('Cau_hoi_trac_nghiem_dung_sai')->where('ID_TracNghiemDungSai', $id)->update($data);
        return redirect()->route('teacher.dung-sai')->with('success', 'Cập nhật câu hỏi Đúng/Sai thành công!');
    }

    public function cauHoiDSDestroy(int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
        $q = DB::selectOne(
            "SELECT q.ID_TracNghiemDungSai FROM Cau_hoi_trac_nghiem_dung_sai q
             JOIN Chu_De cd ON q.ID_ChuDe = cd.ID_ChuDe
             WHERE q.ID_TracNghiemDungSai = ? AND cd.ID_NguoiTao = ?", [$id, $teacherId]
        );
        if (!$q) abort(403);
        DB::table('De_Thi_Chi_Tiet')->where('ID_TracNghiemDungSai', $id)->delete();
        DB::table('Cau_hoi_trac_nghiem_dung_sai')->where('ID_TracNghiemDungSai', $id)->delete();
        return redirect()->route('teacher.dung-sai')->with('success', 'Đã xóa câu hỏi!');
    }

    public function tracNghiemTraLoiNgan(Request $request): View
    {
        $teacherId = $request->session()->get('auth.id');
        $cauHois = DB::select(
            "SELECT q.*, cd.NoiDung_ChuDe, m.Ten_MonHoc, k.Ten_KhoiLop
             FROM Cau_hoi_tra_loi_ngan q
             JOIN Chu_De cd ON q.ID_ChuDe = cd.ID_ChuDe
             JOIN Mon_Hoc m ON q.ID_MonHoc = m.ID_MonHoc
             JOIN Khoi_lop k ON q.ID_KhoiLop = k.ID_KhoiLop
             WHERE cd.ID_NguoiTao = ?
             ORDER BY q.ID_TracNghiemTraLoiNgan",
            [$teacherId]
        );
        $monHocs   = DB::select("SELECT * FROM Mon_Hoc ORDER BY Ten_MonHoc");
        $khoiLops  = DB::select("SELECT * FROM Khoi_lop ORDER BY Ten_KhoiLop");
        $chuDesAll = DB::select(
            "SELECT cd.* FROM Chu_De cd WHERE cd.ID_NguoiTao = ? ORDER BY cd.NoiDung_ChuDe",
            [$teacherId]
        );
        return view('teacher.GiaoVien_TracNghiemTraLoiNgan', compact('cauHois', 'monHocs', 'khoiLops', 'chuDesAll'));
    }

    public function cauHoiNganStore(Request $request): RedirectResponse
    {
        $teacherId = session('auth.id');
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
        $cd = DB::selectOne("SELECT ID_ChuDe FROM Chu_De WHERE ID_ChuDe = ? AND ID_NguoiTao = ?", [$data['ID_ChuDe'], $teacherId]);
        if (!$cd) abort(403);
        DB::table('Cau_hoi_tra_loi_ngan')->insert($data);
        return redirect()->route('teacher.tra-loi-ngan')->with('success', 'Thêm câu hỏi trả lời ngắn thành công!');
    }

    public function cauHoiNganUpdate(Request $request, int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
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
        $q = DB::selectOne(
            "SELECT q.ID_TracNghiemTraLoiNgan FROM Cau_hoi_tra_loi_ngan q
             JOIN Chu_De cd ON q.ID_ChuDe = cd.ID_ChuDe
             WHERE q.ID_TracNghiemTraLoiNgan = ? AND cd.ID_NguoiTao = ?", [$id, $teacherId]
        );
        if (!$q) abort(403);
        DB::table('Cau_hoi_tra_loi_ngan')->where('ID_TracNghiemTraLoiNgan', $id)->update($data);
        return redirect()->route('teacher.tra-loi-ngan')->with('success', 'Cập nhật câu hỏi trả lời ngắn thành công!');
    }

    public function cauHoiNganDestroy(int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
        $q = DB::selectOne(
            "SELECT q.ID_TracNghiemTraLoiNgan FROM Cau_hoi_tra_loi_ngan q
             JOIN Chu_De cd ON q.ID_ChuDe = cd.ID_ChuDe
             WHERE q.ID_TracNghiemTraLoiNgan = ? AND cd.ID_NguoiTao = ?", [$id, $teacherId]
        );
        if (!$q) abort(403);
        DB::table('De_Thi_Chi_Tiet')->where('ID_TracNghiemTraLoiNgan', $id)->delete();
        DB::table('Cau_hoi_tra_loi_ngan')->where('ID_TracNghiemTraLoiNgan', $id)->delete();
        return redirect()->route('teacher.tra-loi-ngan')->with('success', 'Đã xóa câu hỏi!');
    }

    public function diemDanh(Request $request): View
    {
        $teacherId = $request->session()->get('auth.id');

        $sessions = DB::select(
            "SELECT dd.*, l.TenLopHoc, m.Ten_MonHoc, l.ID_LopHoc as lop_id,
                    COUNT(lv.ID_Student) as so_hoc_sinh
             FROM Diem_danh dd
             JOIN Lop_hoc l ON dd.ID_LopHoc = l.ID_LopHoc
             JOIN Mon_Hoc m ON l.ID_MonHoc  = m.ID_MonHoc
             LEFT JOIN Lop_hoc_ThanhVien lv ON l.ID_LopHoc = lv.ID_LopHoc
             WHERE l.ID_Teacher = ?
             GROUP BY dd.ID_DiemDanh, dd.ID_LopHoc, dd.NgayHoc_DiemDanh,
                      dd.ThoiGianBatDau_DiemDanh, dd.ThoiGianKetThuc_DiemDanh,
                      dd.ChiTietDiemDanh_DiemDanh, dd.TrangThaiBuoiHoc_DiemDanh,
                      l.TenLopHoc, m.Ten_MonHoc, l.ID_LopHoc
             ORDER BY dd.NgayHoc_DiemDanh DESC",
            [$teacherId]
        );

        $lopHocs = DB::select(
            "SELECT ID_LopHoc, TenLopHoc FROM Lop_hoc WHERE ID_Teacher = ? ORDER BY TenLopHoc",
            [$teacherId]
        );

        $studentsPerClass = [];
        foreach ($lopHocs as $lop) {
            $studentsPerClass[$lop->ID_LopHoc] = DB::select(
                "SELECT u.ID_User, u.HoVaTen_User
                 FROM Lop_hoc_ThanhVien lv
                 JOIN `User` u ON lv.ID_Student = u.ID_User
                 WHERE lv.ID_LopHoc = ?
                 ORDER BY u.HoVaTen_User",
                [$lop->ID_LopHoc]
            );
        }

        return view('teacher.GiaoVien_QuanLyDiemDanh', compact('sessions', 'lopHocs', 'studentsPerClass'));
    }

    public function thongTin(Request $request): View
    {
        $teacherId = $request->session()->get('auth.id');
        $teacher = DB::selectOne(
            "SELECT u.ID_User, u.HoVaTen_User, u.EmailCaNhan_User, u.SoDienThoai_User,
                    u.NgayThangNamSinh_User, u.PhanQuyen_User, u.TrangThaiHoatDong_User,
                    u.PhuTrachKhoi_User, u.PhuTrachMon_User, u.NgayTaoTaiKhoan_User,
                    m.Ten_MonHoc, k.Ten_KhoiLop,
                    COUNT(DISTINCT l.ID_LopHoc) as so_lop
             FROM `User` u
             LEFT JOIN Mon_Hoc m ON u.PhuTrachMon_User = m.ID_MonHoc
             LEFT JOIN Khoi_lop k ON u.PhuTrachKhoi_User = k.ID_KhoiLop
             LEFT JOIN Lop_hoc l ON l.ID_Teacher = u.ID_User
             WHERE u.ID_User = ?
             GROUP BY u.ID_User, u.HoVaTen_User, u.EmailCaNhan_User, u.SoDienThoai_User,
                      u.NgayThangNamSinh_User, u.PhanQuyen_User, u.TrangThaiHoatDong_User,
                      u.PhuTrachKhoi_User, u.PhuTrachMon_User, u.NgayTaoTaiKhoan_User,
                      m.Ten_MonHoc, k.Ten_KhoiLop",
            [$teacherId]
        );
        return view('teacher.GiaoVien_ThongTinCaNhan', compact('teacher'));
    }

    public function thongTinUpdate(Request $request): RedirectResponse
    {
        $teacherId = session('auth.id');
        $data = $request->validate([
            'HoVaTen_User'           => 'required|string|max:150',
            'EmailCaNhan_User'       => 'nullable|email|max:150',
            'SoDienThoai_User'       => 'nullable|string|max:20',
            'NgayThangNamSinh_User'  => 'nullable|date',
        ]);
        DB::table('User')->where('ID_User', $teacherId)->update($data);
        return redirect()->route('teacher.thong-tin')->with('success', 'Cập nhật thông tin thành công!');
    }

    public function chuDeStore(Request $request): RedirectResponse
    {
        $teacherId = session('auth.id');
        $data = $request->validate([
            'NoiDung_ChuDe' => 'required|string|max:255',
            'ID_MonHoc'     => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_KhoiLop'    => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
        ]);
        DB::table('Chu_De')->insert([
            'NoiDung_ChuDe' => $data['NoiDung_ChuDe'],
            'ID_MonHoc'     => $data['ID_MonHoc'],
            'ID_KhoiLop'    => $data['ID_KhoiLop'],
            'ID_NguoiTao'   => $teacherId,
        ]);
        return redirect()->route('teacher.chu-de')->with('success', 'Tạo chủ đề thành công!');
    }

    public function chuDeUpdate(Request $request, int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
        $data = $request->validate([
            'NoiDung_ChuDe' => 'required|string|max:255',
            'ID_MonHoc'     => 'required|integer|exists:Mon_Hoc,ID_MonHoc',
            'ID_KhoiLop'    => 'required|integer|exists:Khoi_lop,ID_KhoiLop',
        ]);
        DB::table('Chu_De')
            ->where('ID_ChuDe', $id)
            ->where('ID_NguoiTao', $teacherId)
            ->update($data);
        return redirect()->route('teacher.chu-de')->with('success', 'Cập nhật chủ đề thành công!');
    }

    public function chuDeDestroy(int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
        try {
            DB::table('Chu_De')
                ->where('ID_ChuDe', $id)
                ->where('ID_NguoiTao', $teacherId)
                ->delete();
            return redirect()->route('teacher.chu-de')->with('success', 'Đã xóa chủ đề!');
        } catch (\Exception) {
            return redirect()->route('teacher.chu-de')->with('error', 'Không thể xóa: chủ đề đang có câu hỏi liên kết.');
        }
    }

    public function diemDanhStore(Request $request): RedirectResponse
    {
        $teacherId = session('auth.id');
        $data = $request->validate([
            'ID_LopHoc'                    => 'required|integer|exists:Lop_hoc,ID_LopHoc',
            'NgayHoc_DiemDanh'             => 'required|date',
            'ThoiGianBatDau_DiemDanh'      => 'required|date_format:H:i',
            'ThoiGianKetThuc_DiemDanh'     => 'nullable|date_format:H:i',
            'TrangThaiBuoiHoc_DiemDanh'    => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $lop = DB::table('Lop_hoc')
            ->where('ID_LopHoc', $data['ID_LopHoc'])
            ->where('ID_Teacher', $teacherId)
            ->first();
        abort_if(!$lop, 403);

        $ngay = $data['NgayHoc_DiemDanh'];
        DB::table('Diem_danh')->insert([
            'ID_LopHoc'                    => $data['ID_LopHoc'],
            'NgayHoc_DiemDanh'             => $ngay,
            'ThoiGianBatDau_DiemDanh'      => $ngay . ' ' . $data['ThoiGianBatDau_DiemDanh'] . ':00',
            'ThoiGianKetThuc_DiemDanh'     => $data['ThoiGianKetThuc_DiemDanh']
                ? $ngay . ' ' . $data['ThoiGianKetThuc_DiemDanh'] . ':00'
                : null,
            'TrangThaiBuoiHoc_DiemDanh'    => $data['TrangThaiBuoiHoc_DiemDanh'],
            'ChiTietDiemDanh_DiemDanh'     => null,
        ]);
        return redirect()->route('teacher.diem-danh')->with('success', 'Tạo buổi điểm danh thành công!');
    }

    public function diemDanhUpdate(Request $request, int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
        $dd = DB::selectOne(
            "SELECT dd.* FROM Diem_danh dd
             JOIN Lop_hoc l ON dd.ID_LopHoc = l.ID_LopHoc
             WHERE dd.ID_DiemDanh = ? AND l.ID_Teacher = ?",
            [$id, $teacherId]
        );
        abort_if(!$dd, 403);

        $trangThai  = $request->input('TrangThaiBuoiHoc_DiemDanh');
        $chiTietRaw = $request->input('chi_tiet', []);

        $chiTiet = [];
        foreach ($chiTietRaw as $stuId => $status) {
            if (\in_array($status, ['present', 'absent', 'late', 'excused'])) {
                $chiTiet[(int) $stuId] = $status;
            }
        }

        DB::table('Diem_danh')->where('ID_DiemDanh', $id)->update([
            'TrangThaiBuoiHoc_DiemDanh'  => in_array($trangThai, ['scheduled', 'in_progress', 'completed', 'cancelled'])
                ? $trangThai : $dd->TrangThaiBuoiHoc_DiemDanh,
            'ChiTietDiemDanh_DiemDanh'   => empty($chiTiet) ? $dd->ChiTietDiemDanh_DiemDanh : json_encode($chiTiet),
        ]);
        return redirect()->route('teacher.diem-danh')->with('success', 'Đã cập nhật điểm danh!');
    }

    public function diemDanhDestroy(int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
        $dd = DB::selectOne(
            "SELECT dd.ID_DiemDanh FROM Diem_danh dd
             JOIN Lop_hoc l ON dd.ID_LopHoc = l.ID_LopHoc
             WHERE dd.ID_DiemDanh = ? AND l.ID_Teacher = ?",
            [$id, $teacherId]
        );
        abort_if(!$dd, 403);

        try {
            DB::table('Don_xin_nghi')->where('ID_DiemDanh', $id)->delete();
            DB::table('Diem_danh')->where('ID_DiemDanh', $id)->delete();
            return redirect()->route('teacher.diem-danh')->with('success', 'Đã xóa buổi điểm danh!');
        } catch (\Exception) {
            return redirect()->route('teacher.diem-danh')->with('error', 'Không thể xóa buổi điểm danh.');
        }
    }

    public function kyThi(Request $request): View
    {
        $teacherId = session('auth.id');
        $kyThis = DB::select(
            "SELECT kt.*, k.Ten_KhoiLop, m.Ten_MonHoc,
                    l.TenLopHoc, dt.TenDeThi, cd.NoiDung_ChuDe
             FROM Ky_thi kt
             JOIN Khoi_lop k ON kt.ID_KhoiLop = k.ID_KhoiLop
             JOIN Mon_Hoc m  ON kt.ID_MonHoc  = m.ID_MonHoc
             JOIN Lop_hoc l  ON kt.ID_LopHoc  = l.ID_LopHoc
             JOIN De_Thi dt  ON kt.ID_MaDeThi = dt.ID_MaDeThi
             JOIN Chu_De cd  ON kt.ID_ChuDe   = cd.ID_ChuDe
             WHERE l.ID_Teacher = ?
             ORDER BY kt.ID_KyThi DESC",
            [$teacherId]
        );
        $lopHocsAll = DB::select(
            "SELECT ID_LopHoc, TenLopHoc, ID_MonHoc, ID_KhoiLop
             FROM Lop_hoc WHERE ID_Teacher = ? ORDER BY TenLopHoc",
            [$teacherId]
        );
        $chuDesAll = DB::select(
            "SELECT ID_ChuDe, NoiDung_ChuDe, ID_MonHoc, ID_KhoiLop
             FROM Chu_De WHERE ID_NguoiTao = ? ORDER BY NoiDung_ChuDe",
            [$teacherId]
        );
        $deThisAll = DB::select(
            "SELECT ID_MaDeThi, TenDeThi, ID_MaMon, ID_MaKhoi
             FROM De_Thi WHERE ID_NguoiTao = ? ORDER BY TenDeThi",
            [$teacherId]
        );
        $khoiLops = DB::select(
            "SELECT DISTINCT k.ID_KhoiLop, k.Ten_KhoiLop
             FROM Lop_hoc l JOIN Khoi_lop k ON l.ID_KhoiLop = k.ID_KhoiLop
             WHERE l.ID_Teacher = ? ORDER BY k.Ten_KhoiLop",
            [$teacherId]
        );
        $monHocs = DB::select(
            "SELECT DISTINCT m.ID_MonHoc, m.Ten_MonHoc
             FROM Lop_hoc l JOIN Mon_Hoc m ON l.ID_MonHoc = m.ID_MonHoc
             WHERE l.ID_Teacher = ? ORDER BY m.Ten_MonHoc",
            [$teacherId]
        );
        return view('teacher.GiaoVien_DanhSachKyThi', compact('kyThis', 'lopHocsAll', 'chuDesAll', 'deThisAll', 'khoiLops', 'monHocs'));
    }

    public function kyThiStore(Request $request): RedirectResponse
    {
        $teacherId = session('auth.id');
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

        $lop = DB::table('Lop_hoc')
            ->where('ID_LopHoc', $data['ID_LopHoc'])
            ->where('ID_Teacher', $teacherId)
            ->first();
        abort_if(!$lop, 403);

        $countErrors = $this->checkDeThiSoCau((int) $data['ID_MaDeThi'], $data);
        if ($countErrors) {
            return back()->withErrors($countErrors)->withInput();
        }

        DB::table('Ky_thi')->insert($data);
        return redirect()->route('teacher.ky-thi')->with('success', 'Tạo kỳ thi thành công!');
    }

    public function kyThiUpdate(Request $request, int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
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

        $kt = DB::selectOne(
            "SELECT kt.ID_KyThi FROM Ky_thi kt
             JOIN Lop_hoc l ON kt.ID_LopHoc = l.ID_LopHoc
             WHERE kt.ID_KyThi = ? AND l.ID_Teacher = ?",
            [$id, $teacherId]
        );
        abort_if(!$kt, 403);

        $countErrors = $this->checkDeThiSoCau((int) $data['ID_MaDeThi'], $data);
        if ($countErrors) {
            return back()->withErrors($countErrors)->withInput();
        }

        DB::table('Ky_thi')->where('ID_KyThi', $id)->update($data);
        return redirect()->route('teacher.ky-thi')->with('success', 'Cập nhật kỳ thi thành công!');
    }

    public function kyThiDestroy(int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
        $kt = DB::selectOne(
            "SELECT kt.ID_KyThi FROM Ky_thi kt
             JOIN Lop_hoc l ON kt.ID_LopHoc = l.ID_LopHoc
             WHERE kt.ID_KyThi = ? AND l.ID_Teacher = ?",
            [$id, $teacherId]
        );
        abort_if(!$kt, 403);

        try {
            DB::table('Ky_thi')->where('ID_KyThi', $id)->delete();
            return redirect()->route('teacher.ky-thi')->with('success', 'Đã xóa kỳ thi!');
        } catch (\Exception) {
            return redirect()->route('teacher.ky-thi')->with('error', 'Không thể xóa: kỳ thi đã có học sinh làm bài.');
        }
    }

    public function diemHocSinh(Request $request): View
    {
        $teacherId = session('auth.id');
        $diemSos = DB::select(
            "SELECT ds.*, u.HoVaTen_User as ten_hoc_sinh,
                    kt.Ten_KyThi, l.TenLopHoc, m.Ten_MonHoc
             FROM Diem_so ds
             JOIN `User` u   ON ds.ID_User     = u.ID_User
             JOIN Ky_thi kt  ON ds.ID_MaKyThi  = kt.ID_KyThi
             JOIN Lop_hoc l  ON kt.ID_LopHoc   = l.ID_LopHoc
             JOIN Mon_Hoc m  ON kt.ID_MonHoc   = m.ID_MonHoc
             WHERE l.ID_Teacher = ?
             ORDER BY ds.ThoiGianKetThuc_DiemSo DESC",
            [$teacherId]
        );
        $lopHocs = DB::select(
            "SELECT ID_LopHoc, TenLopHoc FROM Lop_hoc WHERE ID_Teacher = ? ORDER BY TenLopHoc",
            [$teacherId]
        );
        return view('teacher.GiaoVien_DiemHocSinh', compact('diemSos', 'lopHocs'));
    }

    public function donXinNghi(Request $request): View
    {
        $teacherId = session('auth.id');
        $donNghis = DB::select(
            "SELECT dxn.*, u.HoVaTen_User as ten_hoc_sinh,
                    l.TenLopHoc, dd.NgayHoc_DiemDanh
             FROM Don_xin_nghi dxn
             JOIN `User` u      ON dxn.ID_User     = u.ID_User
             JOIN Lop_hoc l     ON dxn.ID_LopHoc   = l.ID_LopHoc
             JOIN Diem_danh dd  ON dxn.ID_DiemDanh = dd.ID_DiemDanh
             WHERE l.ID_Teacher = ?
             ORDER BY dxn.ThoiGianGui_DonXinNghi DESC",
            [$teacherId]
        );
        return view('teacher.GiaoVien_DonXinNghi', compact('donNghis'));
    }

    public function donXinNghiUpdate(Request $request, int $id): RedirectResponse
    {
        $teacherId = session('auth.id');
        $don = DB::selectOne(
            "SELECT dxn.ID_DonXinNghi FROM Don_xin_nghi dxn
             JOIN Lop_hoc l ON dxn.ID_LopHoc = l.ID_LopHoc
             WHERE dxn.ID_DonXinNghi = ? AND l.ID_Teacher = ?",
            [$id, $teacherId]
        );
        abort_if(!$don, 403);

        $trangThai = $request->input('TrangThai_DonXinNghi');
        if (!\in_array($trangThai, ['approved', 'rejected'])) {
            return back()->with('error', 'Trạng thái không hợp lệ.');
        }

        DB::table('Don_xin_nghi')
            ->where('ID_DonXinNghi', $id)
            ->update(['TrangThai_DonXinNghi' => $trangThai]);

        $msg = $trangThai === 'approved' ? 'Đã duyệt đơn xin nghỉ!' : 'Đã từ chối đơn xin nghỉ.';
        return redirect()->route('teacher.don-xin-nghi')->with('success', $msg);
    }

    public function doiMatKhau(): View
    {
        return view('teacher.GiaoVien_DoiMatKhau');
    }

    public function doiMatKhauUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'mat_khau_cu'   => 'required|string',
            'mat_khau_moi'  => 'required|string|min:6',
            'xac_nhan'      => 'required|string|same:mat_khau_moi',
        ]);

        $teacherId = session('auth.id');
        $user = DB::table('User')->where('ID_User', $teacherId)->first();

        if (!$user || md5($request->mat_khau_cu) !== $user->Pass_User) {
            return back()->withErrors(['mat_khau_cu' => 'Mật khẩu hiện tại không đúng.'])->withInput();
        }

        if ($request->mat_khau_cu === $request->mat_khau_moi) {
            return back()->withErrors(['mat_khau_moi' => 'Mật khẩu mới không được trùng mật khẩu cũ.'])->withInput();
        }

        DB::table('User')
            ->where('ID_User', $teacherId)
            ->update(['Pass_User' => md5($request->mat_khau_moi)]);

        return redirect()->route('teacher.doi-mat-khau')->with('success', 'Đổi mật khẩu thành công!');
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
            $errs[] = "Đề thi chỉ có {$c4} câu 4 phương án (yêu cầu {$data['SoCauHoiTracNghiem4PhuongAn_KyThi']} câu).";
        if ($data['SoCauHoiTracNghiemDungSai_KyThi'] > $cds)
            $errs[] = "Đề thi chỉ có {$cds} câu đúng/sai (yêu cầu {$data['SoCauHoiTracNghiemDungSai_KyThi']} câu).";
        if ($data['SoCauHoiTracNghiemTraLoiNgan_KyThi'] > $cng)
            $errs[] = "Đề thi chỉ có {$cng} câu trả lời ngắn (yêu cầu {$data['SoCauHoiTracNghiemTraLoiNgan_KyThi']} câu).";
        return $errs;
    }
}
