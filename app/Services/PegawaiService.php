<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

class PegawaiService
{
    private const ALLOWED_ROLES = ['pegawai', 'manager'];

    public function isAllowedToManage(?string $targetRole): bool
    {
        if (!$targetRole) {
            return false;
        }

        $userRole = auth()->user()->role ?? null;

        return $userRole === 'admin' || in_array($targetRole, self::ALLOWED_ROLES);
    }

    public function list(): Collection
    {
        return User::nonAdmin()->get();
    }

    public function find(int $id): ?User
    {
        return User::nonAdmin()->find($id);
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function update(User $pegawai, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $pegawai->update($data);

        return $pegawai;
    }

    public function delete(User $pegawai): void
    {
        $pegawai->delete();
    }

    public function search(string $query): Collection
    {
        return User::nonAdmin()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('email', 'like', "%$query%");
            })
            ->get();
    }

    public function filterByRole(string $role): Collection
    {
        return User::nonAdmin()
            ->where('role', $role)
            ->get();
    }
}
