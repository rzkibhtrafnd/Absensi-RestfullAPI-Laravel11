<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiSetting extends Model
{
    protected $table = 'attendance_settings'; // sesuai nama tabel di migration
    protected $fillable = [
        'check_in_start',
        'check_in_end',
        'check_out_time',
        'radius_meters',
        'late_tolerance',
        'office_address',
        'office_latitude',
        'office_longitude',
    ];
}
