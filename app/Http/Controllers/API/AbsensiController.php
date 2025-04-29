<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AjukanAbsensiRequest;
use App\Http\Requests\ScanQrRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Services\AbsensiService;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    protected $absensiService;

    public function __construct(AbsensiService $absensiService)
    {
        $this->absensiService = $absensiService;
    }

    public function getLatestQr()
    {
        $result = $this->absensiService->getLatestQr();
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    public function scanQr(ScanQrRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        
        $result = $this->absensiService->processQrScan($data);
        return response()->json($result, $result['success'] ? 200 : ($result['message'] === 'QR Code tidak valid atau telah kedaluwarsa' ? 403 : 409));
    }

    public function ajukanAbsensi(AjukanAbsensiRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['lampiran'] = $request->file('lampiran');
        
        $result = $this->absensiService->ajukanAbsensi($data);
        return response()->json($result, $result['success'] ? 200 : 409);
    }

    public function riwayatAbsensi()
    {
        $riwayat = $this->absensiService->getRiwayatAbsensi(auth()->id());
        return response()->json([
            'success' => true,
            'message' => 'Riwayat absensi ditemukan',
            'riwayat' => $riwayat
        ]);
    }

    public function riwayatPengajuan(Request $request)
    {
        $pengajuan = $this->absensiService->getRiwayatPengajuan(
            auth()->id(),
            auth()->user()->role,
            $request->input('filter')
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Riwayat pengajuan absensi ditemukan.',
            'pengajuan' => $pengajuan
        ]);
    }

    public function approveAbsensi($id)
    {
        $result = $this->absensiService->approveAbsensi($id, auth()->id());
        return response()->json($result, isset($result['success']) ? 200 : 404);
    }

    public function rejectAbsensi(Request $request, $id)
    {
        $result = $this->absensiService->rejectAbsensi(
            $id, 
            auth()->id(), 
            $request->input('alasan_ditolak')
        );
        return response()->json($result, isset($result['success']) ? 200 : 404);
    }

    public function listAbsensi()
    {
        $result = $this->absensiService->getListAbsensi(
            auth()->id(),
            auth()->user()->role
        );
        
        return $result['success'] 
            ? response()->json($result)
            : response()->json($result, 403);
    }

    public function generateAlphaForAll()
    {
        $result = $this->absensiService->generateAlphaForAll();
        return response()->json($result);
    }

    public function getSettings()
    {
        $result = $this->absensiService->getSettings();
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    public function updateSettings(UpdateSettingsRequest $request)
    {
        $result = $this->absensiService->updateSettings($request->validated());
        return response()->json($result);
    }
}