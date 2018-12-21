<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PortalConfRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'logo' => 'required|max:255',
            'phone' => 'nullable|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'required|url|max:255',
            'copyright' => 'required|max:255',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            'logo.required' => '请上传logo'
        ];
    }
}
