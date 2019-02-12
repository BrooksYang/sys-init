<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CurrencyTypeRequest extends FormRequest
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
            'title' =>[
                'required',
                'unique:dcuex_currency_type,title,'.$request->currencyTypeMg,
                'alpha_dash',
            ],
            'subtitled' => 'nullable|max:255',
            'intro' => 'required|max:2000',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            'title.unique' => '该币种类型名称已存在',
        ];
    }
}
