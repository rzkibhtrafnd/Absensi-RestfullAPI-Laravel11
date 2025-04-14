<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\QrCodeToken;
use App\Models\Absensi;
use App\Models\User;
use App\Models\AbsensiSetting;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // -------------------------
    // FUNGSI ABSENSI UTAMA
    // -------------------------
    
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
    
        $qrImage = QrCode::format('svg')->size(300)->generate($latest->token);
    
        return response()->json([
            'success'    => true,
            'qr_image'   => 'data:image/svg+xml;base64,' . base64_encode($qrImage),
            'token'      => $latest->token,
            'expired_at' => $latest->expired_at
        ]);
    }
    

    public function scanQr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'     => 'required|string',
            'latitude'  => 'required|numeric',
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
    
        $setting = AbsensiSetting::first();
        if ($setting) {
            $officeLat = $setting->office_latitude;
            $officeLon = $setting->office_longitude;
            $allowedRadius = $setting->radius_meters / 1000;
        } else {
            // fallback ke nilai default
            $officeLat = config('absensi.office_latitude', -6.200000);
            $officeLon = config('absensi.office_longitude', 106.816666);
            $allowedRadius = config('absensi.allowed_radius_km', 0.1);
        }
    
        $distance = $this->calculateDistance($officeLat, $officeLon, $request->latitude, $request->longitude);
    
        if ($distance > $allowedRadius) {
            return response()->json([
                'success'  => false,
                'message'  => 'Anda berada di luar radius absensi',
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
            'tanggal'    => $today
        ]);
    
        if (!$absensi->exists) {
            $jamMasuk = now('Asia/Jakarta')->toTimeString();
            $status   = (now('Asia/Jakarta')->format('H:i') > '08:00') ? 'Terlambat' : 'Hadir';
    
            $absensi->fill([
                'jam_masuk'       => $jamMasuk,
                'status'          => $status,
                'lokasi_masuk'    => $request->latitude . ',' . $request->longitude,
                'approval_status' => 'approved'
            ])->save();
    
            return response()->json([
                'success' => true,
                'message' => ($status === 'Terlambat') ? 'Check-in berhasil tapi Anda terlambat' : 'Check-in berhasil',
                'type'    => 'checkin',
                'absensi' => $absensi
            ]);
        }
    
        if (!$absensi->jam_keluar) {
            $absensi->update([
                'jam_keluar'    => now('Asia/Jakarta')->toTimeString(),
                'lokasi_keluar' => $request->latitude . ',' . $request->longitude
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil',
                'type'    => 'checkout',
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
            'tanggal'  => 'required|date|before_or_equal:today',
            'status'   => 'required|in:Izin,Sakit,Cuti',
            'alasan'   => 'required|string|max:500',
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
            'pegawai_id'      => $user->id,
            'tanggal'         => $tanggal,
            'status'          => $request->status, // Izin, Sakit, Cuti
            'alasan'          => $request->alasan,
            'lampiran'        => $filename,
            'approved_by'     => null,
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
    
        $riwayat = Absensi::where('pegawai_id', $user->id)
                          ->orderByDesc('tanggal')
                          ->get();
    
        return response()->json([
            'success' => true,
            'message' => 'Riwayat absensi ditemukan',
            'riwayat' => $riwayat
        ]);
    }
    
    /**
     * Menampilkan riwayat pengajuan absensi.
     * Hanya menampilkan data pengajuan (Izin, Sakit, Cuti). Data dengan status Hadir atau Terlambat tidak ditampilkan.
     * Endpoint: GET /absensi/riwayat-pengajuan?filter=<pending|approved|rejected>
     */
    public function riwayatPengajuan(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
    
        // Ambil hanya data pengajuan: Izin, Sakit, Cuti
        $query = Absensi::query();
        $query->whereIn('status', ['Izin', 'Sakit', 'Cuti']);
    
        if ($user->role === 'pegawai') {
            $query->where('pegawai_id', $user->id);
        }
    
        if ($request->has('filter')) {
            $filter = $request->input('filter');
            $query->where('approval_status', $filter);
        }
    
        $pengajuan = $query->orderByDesc('tanggal')->get();
    
        return response()->json([
            'success' => true,
            'message' => 'Riwayat pengajuan absensi ditemukan.',
            'pengajuan' => $pengajuan
        ]);
    }

    public function approveAbsensi($id)
    {
        $absensi = Absensi::find($id);
    
        if (!$absensi) {
            return response()->json(['message' => 'Data absensi tidak ditemukan.'], 404);
        }
    
        // Mengubah status approval menjadi approved
        $absensi->approval_status = 'approved';
    
        // Menambahkan informasi tentang siapa yang menyetujui (ID pengguna yang melakukan approval)
        $absensi->approved_by = auth()->user()->id;
    
        // Menambahkan keterangan approval jika diperlukan (optional)
        $absensi->keterangan_approval = 'Absensi disetujui oleh ' . auth()->user()->name;
    
        $absensi->save();
    
        return response()->json(['message' => 'Pengajuan absensi disetujui.']);
    }
    
    public function rejectAbsensi(Request $request, $id)
    {
        $absensi = Absensi::find($id);
    
        if (!$absensi) {
            return response()->json(['message' => 'Data absensi tidak ditemukan.'], 404);
        }
    
        // Mengubah status approval menjadi rejected
        $absensi->approval_status = 'rejected';
    
        // Menambahkan informasi tentang siapa yang menolak (ID pengguna yang melakukan penolakan)
        $absensi->approved_by = auth()->user()->id;
    
        // Menambahkan alasan penolakan dari request
        $absensi->keterangan_approval = $request->input('alasan_ditolak', 'Tidak ada alasan');
    
        $absensi->save();
    
        return response()->json(['message' => 'Pengajuan absensi ditolak.']);
    }
    

    public function listAbsensi()
    {
        $user = auth()->user();
        if ($user->role === 'hr') {
            $semua = Absensi::with('pegawai')
                            ->orderByDesc('tanggal')
                            ->get();
            $pribadi = Absensi::where('pegawai_id', $user->id)
                              ->orderByDesc('tanggal')
                              ->get();
    
            return response()->json([
                'success'        => true,
                'semua_pegawai'  => $semua,
                'riwayat_pribadi'=> $pribadi
            ]);
        }
    
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }
    
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Haversine Formula
        $earthRadius = 6371; // radius bumi dalam kilometer

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance; // dalam kilometer
    }

    
    public function generateAlphaForAll(Request $request)
    {
        $today = Carbon::now('Asia/Jakarta');
        $dayName = $today->format('l');
    
        if (in_array($dayName, ['Saturday', 'Sunday'])) {
            return response()->json([
                'success' => true,
                'message' => 'Hari libur, tidak perlu mencatat Alpha.'
            ]);
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
    
        return response()->json([
            'success'       => true,
            'message'       => 'Alpha berhasil dicatat.',
            'total_alpha'   => count($generated),
            'pegawai_alpha' => $generated
        ]);
    }
    
    // -----------------------------
    // PENGATURAN ABSENSI
    // -----------------------------
    
    /**
     * Mengambil pengaturan absensi saat ini.
     * Endpoint: GET /absensi/settings
     */
    public function getSettings()
    {
        $settings = AbsensiSetting::first();
        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan absensi tidak ditemukan.'
            ], 404);
        }
        return response()->json([
            'success'  => true,
            'settings' => $settings
        ]);
    }
    
    /**
     * Memperbarui pengaturan absensi.
     * Endpoint: PUT /absensi/settings
     *
     * Parameter (JSON):
     * - check_in_start (format H:i)
     * - check_in_end (format H:i)
     * - check_out_time (format H:i)
     * - radius_meters (integer)
     * - late_tolerance (format H:i)
     * - office_address (string)
     * - office_latitude (numeric)
     * - office_longitude (numeric)
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_in_start'    => 'required|date_format:H:i',
            'check_in_end'      => 'required|date_format:H:i',
            'check_out_time'    => 'required|date_format:H:i',
            'radius_meters'     => 'required|integer|min:10',
            'late_tolerance'    => 'required|date_format:H:i',
            'office_address'    => 'required|string|max:255',
            'office_latitude'   => 'required|numeric',
            'office_longitude'  => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
    
        $settings = AbsensiSetting::first();
    
        if (!$settings) {
            $settings = AbsensiSetting::create($request->all());
        } else {
            $settings->update($request->all());
        }
    
        return response()->json([
            'success'  => true,
            'message'  => 'Pengaturan absensi berhasil diperbarui.',
            'settings' => $settings
        ]);
    }
}
