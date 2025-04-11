<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    private const ALLOWED_ROLES = ['pegawai', 'manager'];

    private function isAllowedToManage(?string $targetRole): bool
    {
        if (!$targetRole) return false;

        $userRole = auth()->user()->role;

        // Admin bisa kelola semua
        if ($userRole === 'admin') {
            return true;
        }

        // HR hanya boleh kelola role tertentu
        return in_array($targetRole, self::ALLOWED_ROLES);
    }

    public function index(): JsonResponse
    {
        $pegawai = User::nonAdmin()->get();
        return $this->successResponse($pegawai);
    }

    public function show($id): JsonResponse
    {
        $pegawai = User::nonAdmin()->find($id);
        return $pegawai
            ? $this->successResponse($pegawai)
            : $this->notFoundResponse('Pegawai');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => ['required', 'string', Rule::in(array_merge(self::ALLOWED_ROLES, ['admin']))],
            'divisi'   => 'required_unless:role,admin|string|max:255',
            'posisi'   => 'required_unless:role,admin|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        if (!$this->isAllowedToManage($request->role)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk membuat user dengan role ini.'
            ], 403);
        }

        $pegawai = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'divisi'   => $request->divisi,
            'posisi'   => $request->posisi,
        ]);

        return $this->successResponse($pegawai, 'Pegawai berhasil ditambahkan', 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $pegawai = User::nonAdmin()->find($id);
        if (!$pegawai) {
            return $this->notFoundResponse('Pegawai');
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:255',
            'email'    => [
                'sometimes', 'string', 'email', 'max:255',
                Rule::unique('users')->ignore($pegawai->id)
            ],
            'password' => 'sometimes|string|min:8|confirmed',
            'role'     => ['sometimes', 'string', Rule::in(array_merge(self::ALLOWED_ROLES, ['admin']))],
            'divisi'   => 'required_unless:role,admin|string|max:255',
            'posisi'   => 'required_unless:role,admin|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        // Hanya validasi role jika dikirim
        if ($request->has('role') && !$this->isAllowedToManage($request->role)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengubah user dengan role ini.'
            ], 403);
        }

        $data = $request->all();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $pegawai->update($data);

        return $this->successResponse($pegawai, 'Pegawai berhasil diperbarui');
    }

    public function destroy($id): JsonResponse
    {
        $pegawai = User::nonAdmin()->find($id);
        if (!$pegawai) {
            return $this->notFoundResponse('Pegawai');
        }

        if (!$this->isAllowedToManage($pegawai->role)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus user dengan role ini.'
            ], 403);
        }

        $pegawai->delete();
        return $this->successResponse(null, 'Pegawai berhasil dihapus');
    }

    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $pegawai = User::nonAdmin()
            ->search($request->query)
            ->get();

        return $this->successResponse($pegawai);
    }

    public function filterByRole(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role' => ['required', 'string', Rule::in(self::ALLOWED_ROLES)]
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $pegawai = User::where('role', $request->role)->get();
        return $this->successResponse($pegawai);
    }

    // Helper Methods
    private function successResponse($data, $message = '', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    private function notFoundResponse($entity): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => "$entity tidak ditemukan"
        ], 404);
    }

    private function validationErrorResponse($errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors'  => $errors
        ], 422);
    }
}
