<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\AbsensiSetting;
use App\Models\QrCodeToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AbsensiService
{
    public function getLatestQr()
    {
        $latest = QrCodeToken::where('expired_at', '>', now('Asia/Jakarta'))
                             ->latest()
                             ->first();
    
        if (!$latest) {
            return [
                'success' => false,
                'message' => 'Belum ada QR Code tersedia saat ini'
            ];
        }
    
        $qrImage = QrCode::format('svg')->size(300)->generate($latest->token);
    
        return [
            'success'    => true,
            'qr_image'   => 'data:image/svg+xml;base64,' . base64_encode($qrImage),
            'token'      => $latest->token,
            'expired_at' => $latest->expired_at
        ];
    }

    public function processQrScan(array $data)
    {
        $qr = QrCodeToken::where('token', $data['token'])
                         ->where('expired_at', '>', now('Asia/Jakarta'))
                         ->first();
    
        if (!$qr) {
            return [
                'success' => false,
                'message' => 'QR Code tidak valid atau telah kedaluwarsa'
            ];
        }
    
        $setting = AbsensiSetting::first();
        if ($setting) {
            $officeLat = $setting->office_latitude;
            $officeLon = $setting->office_longitude;
            $allowedRadius = $setting->radius_meters / 1000;
        } else {
            $officeLat = config('absensi.office_latitude', -6.200000);
            $officeLon = config('absensi.office_longitude', 106.816666);
            $allowedRadius = config('absensi.allowed_radius_km', 0.1);
        }
    
        $distance = $this->calculateDistance($officeLat, $officeLon, $data['latitude'], $data['longitude']);
    
        if ($distance > $allowedRadius) {
            return [
                'success'  => false,
                'message'  => 'Anda berada di luar radius absensi',
                'distance' => round($distance, 2) . ' km'
            ];
        }
    
        $today = now('Asia/Jakarta')->toDateString();
    
        $absensi = Absensi::firstOrNew([
            'pegawai_id' => $data['user_id'],
            'tanggal'    => $today
        ]);
    
        if (!$absensi->exists) {
            $jamMasuk = now('Asia/Jakarta')->toTimeString();
            $status   = (now('Asia/Jakarta')->format('H:i') > '08:00') ? 'Terlambat' : 'Hadir';
    
            $absensi->fill([
                'jam_masuk'       => $jamMasuk,
                'status'          => $status,
                'lokasi_masuk'    => $data['latitude'] . ',' . $data['longitude'],
                'approval_status' => 'approved'
            ])->save();
    
            return [
                'success' => true,
                'message' => ($status === 'Terlambat') ? 'Check-in berhasil tapi Anda terlambat' : 'Check-in berhasil',
                'type'    => 'checkin',
                'absensi' => $absensi
            ];
        }
    
        if (!$absensi->jam_keluar) {
            $absensi->update([
                'jam_keluar'    => now('Asia/Jakarta')->toTimeString(),
                'lokasi_keluar' => $data['latitude'] . ',' . $data['longitude']
            ]);
    
            return [
                'success' => true,
                'message' => 'Check-out berhasil',
                'type'    => 'checkout',
                'absensi' => $absensi
            ];
        }
    
        return [
            'success' => false,
            'message' => 'Anda sudah melakukan absensi hari ini'
        ];
    }

    public function ajukanAbsensi(array $data)
    {
        $tanggal = Carbon::parse($data['tanggal'], 'Asia/Jakarta')->toDateString();
    
        if (Absensi::where('pegawai_id', $data['user_id'])->where('tanggal', $tanggal)->exists()) {
            return [
                'success' => false,
                'message' => 'Anda sudah memiliki data absensi untuk tanggal ini'
            ];
        }
    
        $file = $data['lampiran'];
        $filename = 'absensi_' . $data['user_id'] . '_' . str_replace('-', '', $tanggal) . '.' . $file->extension();
        $file->storeAs('absensi/lampiran', $filename, 'public');
    
        $absensi = Absensi::create([
            'pegawai_id'      => $data['user_id'],
            'tanggal'         => $tanggal,
            'status'          => $data['status'],
            'alasan'          => $data['alasan'],
            'lampiran'        => $filename,
            'approved_by'     => null,
            'approval_status' => 'pending'
        ]);
    
        return [
            'success' => true,
            'message' => 'Pengajuan absensi berhasil dikirim',
            'absensi' => $absensi
        ];
    }

    public function getRiwayatAbsensi($userId)
    {
        return Absensi::where('pegawai_id', $userId)
                      ->orderByDesc('tanggal')
                      ->get();
    }

    public function getRiwayatPengajuan($userId, $role, $filter = null)
    {
        $query = Absensi::query();
        $query->whereIn('status', ['Izin', 'Sakit', 'Cuti']);
    
        if ($role === 'pegawai') {
            $query->where('pegawai_id', $userId);
        }
    
        if ($filter) {
            $query->where('approval_status', $filter);
        }
    
        return $query->orderByDesc('tanggal')->get();
    }

    public function approveAbsensi($id, $approverId)
    {
        $absensi = Absensi::find($id);
    
        if (!$absensi) {
            return ['message' => 'Data absensi tidak ditemukan.'];
        }
    
        $absensi->update([
            'approval_status' => 'approved',
            'approved_by' => $approverId,
            'keterangan_approval' => 'Absensi disetujui oleh ' . Auth::user()->name
        ]);
    
        return ['message' => 'Pengajuan absensi disetujui.'];
    }

    public function rejectAbsensi($id, $approverId, $alasan)
    {
        $absensi = Absensi::find($id);
    
        if (!$absensi) {
            return ['message' => 'Data absensi tidak ditemukan.'];
        }
    
        $absensi->update([
            'approval_status' => 'rejected',
            'approved_by' => $approverId,
            'keterangan_approval' => $alasan ?? 'Tidak ada alasan'
        ]);
    
        return ['message' => 'Pengajuan absensi ditolak.'];
    }

    public function getListAbsensi($userId, $role)
    {
        if ($role !== 'hr') {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        return [
            'success' => true,
            'semua_pegawai' => Absensi::with('pegawai')->orderByDesc('tanggal')->get(),
            'riwayat_pribadi' => Absensi::where('pegawai_id', $userId)->orderByDesc('tanggal')->get()
        ];
    }

    public function generateAlphaForAll()
    {
        $today = Carbon::now('Asia/Jakarta');
        $dayName = $today->format('l');
    
        if (in_array($dayName, ['Saturday', 'Sunday'])) {
            return [
                'success' => true,
                'message' => 'Hari libur, tidak perlu mencatat Alpha.'
            ];
        }
    
        $pegawaiList = User::where('role', 'pegawai')->get();
        $generated = [];
    
        foreach ($pegawaiList as $pegawai) {
            $sudahAbsensi = Absensi::where('pegawai_id', $pegawai->id)
                                ->whereDate('tanggal', $today->toDateString())
                                ->exists();
    
            if (!$sudahAbsensi) {
                Absensi::create([
                    'pegawai_id'    => $pegawai->id,
                    'tanggal'       => $today->toDateString(),
                    'status'        => 'Alpha',
                    'jam_masuk'     => null,
                    'jam_keluar'    => null,
                    'lokasi_masuk'  => null,
                    'lokasi_keluar' => null,
                ]);
                $generated[] = $pegawai->id;
            }
        }
    
        return [
            'success'       => true,
            'message'       => 'Alpha berhasil dicatat.',
            'total_alpha'   => count($generated),
            'pegawai_alpha' => $generated
        ];
    }

    public function getSettings()
    {
        $settings = AbsensiSetting::first();
        if (!$settings) {
            return [
                'success' => false,
                'message' => 'Pengaturan absensi tidak ditemukan.'
            ];
        }
        return [
            'success'  => true,
            'settings' => $settings
        ];
    }

    public function updateSettings(array $data)
    {
        $settings = AbsensiSetting::first();
    
        if (!$settings) {
            $settings = AbsensiSetting::create($data);
        } else {
            $settings->update($data);
        }
    
        return [
            'success'  => true,
            'message'  => 'Pengaturan absensi berhasil diperbarui.',
            'settings' => $settings
        ];
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}