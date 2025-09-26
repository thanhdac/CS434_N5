<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HoaDonController
{
    // App\Http\Controllers\Admin\DonHangController.php

    public function locChuaDuyet(Request $request)
    {
        $donHangs = DB::table('hoa_dons')
            ->where('tinh_trang', 0)
            ->select(
                'ma_hoa_don',
                'ma_kh',
                'ho_ten',
                'email',
                'dia_chi',
                'sdt',
                'ma_sp',
                'ten_sp',
                'don_gia',
                'hinh',
                'so_luong',
                'ghi_chu',
                'tinh_trang',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($donHangs);
    }
    public function locDaDuyet(Request $request)
    {
        $donHangs = DB::table('hoa_dons')
            ->where('tinh_trang', 1)
            ->select(
                'ma_hoa_don',
                'ma_kh',
                'ho_ten',
                'email',
                'dia_chi',
                'sdt',
                'ma_sp',
                'ten_sp',
                'don_gia',
                'hinh',
                'so_luong',
                'ghi_chu',
                'tinh_trang',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($donHangs);
    }
    public function thongKeHoaDon(Request $request)
    {
        $begin = $request->begin;
        $end = $request->end;

        $data = DB::table('hoa_dons')
            ->select(DB::raw('DATE(created_at) as ngay_dat'), DB::raw('COUNT(*) as so_luong_hoa_don'))
            ->whereBetween(DB::raw('DATE(created_at)'), [$begin, $end])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('ngay_dat')
            ->get();

        $labels = $data->pluck('ngay_dat');
        $so_luong = $data->pluck('so_luong_hoa_don');

        return response()->json([
            'status' => true,
            'data' => $data,
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Số lượng hóa đơn',
                    'data' => $so_luong,
                    'borderColor' => '#36A2EB',
                    'fill' => false,
                ]
            ]
        ]);
    }

    public function locHoaDonChiTiet(Request $request)
    {
        $begin = $request->begin;
        $end = $request->end;

        $data = DB::table('hoa_dons')
            ->join('khach_hangs', 'hoa_dons.ma_kh', '=', 'khach_hangs.ma_kh')
            ->select(
                'hoa_dons.*',
                'khach_hangs.ho_ten as ten_khach_hang',
                'khach_hangs.email'
            )
            ->whereBetween(DB::raw('DATE(hoa_dons.created_at)'), [$begin, $end])
            ->orderBy('hoa_dons.created_at', 'desc')
            ->get();

        return response()->json($data);
    }


    public function getData()
    {
        $data = HoaDon::get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function addData(Request $request)
    {
        HoaDon::create([
            'trang_thai'     => $request->trang_thai,
            'trang_thai'     => $request->trang_thai,
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'Thêm HoaDon ' . $request->ten_sp . ' thành công',
        ]);
    }

    public function update(Request $request)
    {
        HoaDon::where('id', $request->id)->update([
            'ma_sp'       => $request->ma_sp,
            'ten_sp'      => $request->ten_sp,
            'don_gia'     => $request->don_gia,
            'tinh_trang' => $request->tinh_trang,
            'gia_cu'      => $request->gia_cu,
            'so_luong'    => $request->so_luong,
            'hinh'        => $request->hinh,
            'ma_dm'       => $request->ma_dm,
            'mo_ta'       => $request->mo_ta,
            'trailer'     => $request->trailer,
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'Cập nhật HoaDon ' . $request->ten_sp . ' thành công',
        ]);
    }

    public function destroy(Request $request)
    {
        HoaDon::where('id', $request->id)->delete();

        return response()->json([
            'status'    => true,
            'message'   => 'Xóa HoaDon thành công',
        ]);
    }

    // public function changeStatus(Request $request)
    // {
    //     $HoaDon = HoaDon::where('id', $request->id)->first();
    //     // Đảo trạng thái: nếu đang hoạt động (1) thì chuyển thành tạm tắt (0), ngược lại thì chuyển thành hoạt động
    //     $HoaDon->tinh_trang = ($HoaDon->tinh_trang == HoaDon::HOAT_DONG) ? HoaDon::TAM_TAT : HoaDon::HOAT_DONG;

    //     $HoaDon->save();

    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Thay đổi trạng thái HoaDon thành công',
    //     ]);
    // }
    public function changeStatus(Request $request)
    {
        $ma = $request->ma_hoa_don;

        if (!$ma) {
            return response()->json([
                'status' => false,
                'message' => 'Thiếu mã hóa đơn'
            ], 400);
        }

        $HoaDon = HoaDon::where('ma_hoa_don', $ma)->first();

        if (!$HoaDon) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy hóa đơn để cập nhật'
            ], 404);
        }

        $HoaDon->tinh_trang = ($HoaDon->tinh_trang == 1) ? 0 : 1;
        $HoaDon->save();

        return response()->json([
            'status'  => true,
            'message' => 'Thay đổi trạng thái hóa đơn thành công',
        ]);
    }



    public function getDataClientHoaDon()
    {
        $data = HoaDon::where('trang_thai', '>', 0)->get();

        return response()->json([
            'status'    => true,
            'data'      => $data,
        ]);
    }




    public function datHang(Request $request)
    {
        $dsSanPham = $request->input('san_pham');

        if (empty($dsSanPham)) {
            return response()->json([
                'status' => false,
                'message' => 'Không có sản phẩm nào được chọn.'
            ]);
        }

        // Thông tin khách hàng
        $ma_kh   = $request->input('ma_kh') ?? null;
        $ho_ten  = $request->input('ho_ten') ?? 'Khách lẻ';
        $email   = $request->input('email') ?? 'no@email.com';
        $dia_chi = $request->input('dia_chi') ?? 'Chưa cung cấp';
        $sdt     = $request->input('sdt') ?? '0000000000';

        DB::beginTransaction();
        try {
            // === Tạo mã hóa đơn tự động ===
            $lastHD = DB::table('hoa_dons')
                ->orderByDesc('id')
                ->value('ma_hoa_don');

            if ($lastHD) {
                $soCuoi = (int) str_replace('HD', '', $lastHD); // Lấy số cuối
                $soMoi = $soCuoi + 1;
            } else {
                $soMoi = 1;
            }

            $ma_hoa_don = 'HD' . str_pad($soMoi, 2, '0', STR_PAD_LEFT); // Ví dụ: HD01, HD02

            // === Lưu từng sản phẩm kèm chung mã hóa đơn ===
            foreach ($dsSanPham as $sp) {
                DB::table('hoa_dons')->insert([
                    'ma_hoa_don' => $ma_hoa_don,
                    'ma_kh'      => $ma_kh,
                    'ho_ten'     => $ho_ten,
                    'email'      => $email,
                    'dia_chi'    => $dia_chi,
                    'sdt'        => $sdt,
                    'ma_sp'      => $sp['ma_sp'],
                    'ten_sp'     => $sp['ten_sp'],
                    'don_gia'    => $sp['don_gia'],
                    'hinh'       => $sp['hinh'],
                    'so_luong'   => $sp['so_luong'],
                    'ghi_chu'    => $sp['ghi_chu'] ?? null,
                    'tinh_trang' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Đặt hàng thành công!',
                'ma_hoa_don' => $ma_hoa_don
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Lỗi đặt hàng: ' . $e->getMessage(),
            ]);
        }
    }
}
