<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'alasan',
        'lampiran',
    ];

    // Relasi ke user (pegawai)
    public function pegawai()
    {
        return $this->belongsTo(User::class, 'pegawai_id');
    }
}
