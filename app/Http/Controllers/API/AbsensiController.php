<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\QrCodeToken;
use App\Models\Absensi;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function getLatestQr()
    {
        $latest = QrCodeToken::where('expired_at', '>', now('Asia/Jakarta'))
                             ->latest()
                             ->first();
    
        if (!$latest) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada QR Code tersedia saat ini'
            ], 404);
        }
    
        $qrImage = \QrCode::format('svg')->size(300)->generate($latest->token);
    
        return response()->json([
            'success' => true,
            'qr_image' => 'data:image/svg+xml;base64,' . base64_encode($qrImage),
            'token' => $latest->token,
            'expired_at' => $latest->expired_at
        ]);
    }
    

    public function scanQr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $qr = QrCodeToken::where('token', $request->token)
            ->where('expired_at', '>', now('Asia/Jakarta'))
            ->first();

        if (!$qr) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau telah kedaluwarsa'
            ], 403);
        }

        $officeLat = config('absensi.office_latitude', -6.200000);
        $officeLon = config('absensi.office_longitude', 106.816666);
        $allowedRadius = config('absensi.allowed_radius_km', 0.1);

        $distance = $this->calculateDistance($officeLat, $officeLon, $request->latitude, $request->longitude);

        if ($distance > $allowedRadius) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius absensi',
                'distance' => round($distance, 2) . ' km'
            ], 403);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $today = now('Asia/Jakarta')->toDateString();

        $absensi = Absensi::firstOrNew([
            'pegawai_id' => $user->id,
            'tanggal' => $today
        ]);

        if (!$absensi->exists) {
            $jamMasuk = now('Asia/Jakarta')->toTimeString();
            $status = (now('Asia/Jakarta')->format('H:i') > '08:00') ? 'Terlambat' : 'Hadir';
        
            $absensi->fill([
                'jam_masuk' => $jamMasuk,
                'status' => $status,
                'lokasi_masuk' => $request->latitude . ',' . $request->longitude
            ])->save();
        
            return response()->json([
                'success' => true,
                'message' => $status === 'Terlambat' ? 'Check-in berhasil tapi Anda terlambat' : 'Check-in berhasil',
                'type' => 'checkin',
                'absensi' => $absensi
            ]);
        }
        
        if (!$absensi->jam_keluar) {
            $absensi->update([
                'jam_keluar' => now('Asia/Jakarta')->toTimeString(),
                'lokasi_keluar' => $request->latitude . ',' . $request->longitude
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil',
                'type' => 'checkout',
                'absensi' => $absensi
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Anda sudah melakukan absensi hari ini'
        ], 409);
    }

    public function ajukanAbsensi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date|before_or_equal:today',
            'status' => 'required|in:Izin,Sakit,Cuti',
            'alasan' => 'required|string|max:500',
            'lampiran' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $tanggal = Carbon::parse($request->tanggal, 'Asia/Jakarta')->toDateString();

        if (Absensi::where('pegawai_id', $user->id)->where('tanggal', $tanggal)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki data absensi untuk tanggal ini'
            ], 409);
        }

        $file = $request->file('lampiran');
        $filename = 'absensi_' . $user->id . '_' . str_replace('-', '', $tanggal) . '.' . $file->extension();
        $file->storeAs('absensi/lampiran', $filename, 'public');

        $absensi = Absensi::create([
            'pegawai_id' => $user->id,
            'tanggal' => $tanggal,
            'status' => $request->status,
            'alasan' => $request->alasan,
            'lampiran' => $filename,
            'approved_by' => null,
            'approval_status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan absensi berhasil dikirim',
            'absensi' => $absensi
        ]);
    }

    public function riwayatAbsensi(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $riwayat = Absensi::where('pegawai_id', $user->id)->orderByDesc('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat absensi ditemukan',
            'riwayat' => $riwayat
        ]);
    }

    public function listAbsensi()
    {
        $user = auth()->user();
        if ($user->role === 'hr') {
            $semua = Absensi::with('pegawai')->orderByDesc('tanggal')->get();
            $pribadi = Absensi::where('pegawai_id', $user->id)->orderByDesc('tanggal')->get();

            return response()->json([
                'success' => true,
                'semua_pegawai' => $semua,
                'riwayat_pribadi' => $pribadi
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
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
