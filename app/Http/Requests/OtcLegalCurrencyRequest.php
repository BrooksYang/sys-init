<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class OtcLegalCurrencyRequest extends FormRequest
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
            'name' => [
                'required',
                'unique:legal_currencies,name,'.$request->legalCurrency ,
                'max:255'
            ],
            'abbr' => 'required|alpha|max:255',
            'country' => 'required|max:255',
            'country_en' =>  [
                'required',
                'regex:/^[a-z\sA-Z]+$/',   //允许英文字符和空格
                'max:255'
            ],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            'name.unique' => '该法定币种已存在',
        ];
    }

}
