<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PegawaiRequest;
use App\Services\PegawaiService;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class PegawaiController extends Controller
{
    private PegawaiService $pegawaiService;

    public function __construct(PegawaiService $pegawaiService)
    {
        $this->pegawaiService = $pegawaiService;
    }

    public function index(): JsonResponse
    {
        $pegawai = $this->pegawaiService->list();
        return $this->successResponse($pegawai);
    }

    public function show(int $id): JsonResponse
    {
        $pegawai = $this->pegawaiService->find($id);

        if (!$pegawai) {
            return $this->notFoundResponse('Pegawai');
        }

        return $this->successResponse($pegawai);
    }

    public function store(PegawaiRequest $request): JsonResponse
    {
        if (!$this->pegawaiService->isAllowedToManage($request->input('role'))) {
            return $this->unauthorizedResponse();
        }

        $pegawai = $this->pegawaiService->create($request->validated());
        return $this->successResponse($pegawai, 'Pegawai berhasil ditambahkan', 201);
    }

    public function update(PegawaiRequest $request, int $id): JsonResponse
    {
        $pegawai = $this->pegawaiService->find($id);

        if (!$pegawai) {
            return $this->notFoundResponse('Pegawai');
        }

        if ($request->filled('role') && !$this->pegawaiService->isAllowedToManage($request->input('role'))) {
            return $this->unauthorizedResponse();
        }

        $pegawai = $this->pegawaiService->update($pegawai, $request->validated());
        return $this->successResponse($pegawai, 'Pegawai berhasil diperbarui');
    }

    public function destroy(int $id): JsonResponse
    {
        $pegawai = $this->pegawaiService->find($id);

        if (!$pegawai) {
            return $this->notFoundResponse('Pegawai');
        }

        if (!$this->pegawaiService->isAllowedToManage($pegawai->role)) {
            return $this->unauthorizedResponse();
        }

        $this->pegawaiService->delete($pegawai);
        return $this->successResponse(null, 'Pegawai berhasil dihapus');
    }

    public function search(PegawaiRequest $request): JsonResponse
    {
        $pegawai = $this->pegawaiService->search($request->input('query'));
        return $this->successResponse($pegawai);
    }

    public function filterByRole(PegawaiRequest $request): JsonResponse
    {
        $pegawai = $this->pegawaiService->filterByRole($request->input('role'));
        return $this->successResponse($pegawai);
    }

    // Helper Responses
    private function successResponse($data, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    private function notFoundResponse(string $entity): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => "$entity tidak ditemukan."
        ], 404);
    }

    private function unauthorizedResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
        ], 403);
    }
}
