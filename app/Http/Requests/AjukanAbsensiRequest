<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AjukanAbsensiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tanggal'  => 'required|date|before_or_equal:today',
            'status'   => 'required|in:Izin,Sakit,Cuti',
            'alasan'   => 'required|string|max:500',
            'lampiran' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ];
    }
}
