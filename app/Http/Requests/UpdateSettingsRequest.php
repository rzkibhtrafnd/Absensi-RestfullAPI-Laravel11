<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'check_in_start'    => 'required|date_format:H:i',
            'check_in_end'      => 'required|date_format:H:i',
            'check_out_time'    => 'required|date_format:H:i',
            'radius_meters'     => 'required|integer|min:10',
            'late_tolerance'    => 'required|date_format:H:i',
            'office_address'    => 'required|string|max:255',
            'office_latitude'   => 'required|numeric',
            'office_longitude'  => 'required|numeric',
        ];
    }
}