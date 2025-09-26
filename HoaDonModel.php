<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    protected $table = 'hoa_dons';
    protected $fillable = [ 'ma_hoa_don',
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
        'email',
        'sdt',
        'tinh_trang',
    ];
     const TAM_TAT   = 0;
    const HOAT_DONG     = 1;
}
