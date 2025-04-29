<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Gunakan method khusus, tergantung route mana yang pakai
        return match ($this->route()->getActionMethod()) {
            'store' => $this->storeRules(),
            'update' => $this->updateRules(),
            'search' => $this->searchRules(),
            'filterByRole' => $this->filterByRoleRules(),
            default => [],
        };
    }

    private function storeRules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => ['required', 'string', Rule::in(['pegawai', 'manager', 'admin'])],
            'divisi'   => 'required_unless:role,admin|string|max:255',
            'posisi'   => 'required_unless:role,admin|string|max:255',
        ];
    }

    private function updateRules(): array
    {
        return [
            'name'     => 'sometimes|string|max:255',
            'email'    => [
                'sometimes', 'string', 'email', 'max:255',
                Rule::unique('users')->ignore($this->route('id'))
            ],
            'password' => 'sometimes|string|min:8|confirmed',
            'role'     => ['sometimes', 'string', Rule::in(['pegawai', 'manager', 'admin'])],
            'divisi'   => 'required_unless:role,admin|string|max:255',
            'posisi'   => 'required_unless:role,admin|string|max:255',
        ];
    }

    private function searchRules(): array
    {
        return [
            'query' => 'required|string|min:2',
        ];
    }

    private function filterByRoleRules(): array
    {
        return [
            'role' => ['required', 'string', Rule::in(['pegawai', 'manager'])],
        ];
    }
}
