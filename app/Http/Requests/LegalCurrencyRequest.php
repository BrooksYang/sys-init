<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class LegalCurrencyRequest extends FormRequest
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
                'max:50',
                'unique:legal_currencies,name,'.$request->legalCurrency,
            ],
            'abbr' =>[
                'nullable',
                'max:50',
                'regex:/^[a-zA-Z]+$/',    //允许英文字符
            ],
            'rate' => 'required|numeric|min:0.001',
            'symbol' => 'nullable|max:10',
            'is_default_cn' => [
                'required',
                'numeric',
                'in:1,2',
                function($attribute, $value, $fail) {
                    $defaultCn = \DB::table('legal_currencies')->where('is_default_cn',1)->first();
                    if ($defaultCn && $value==1) {
                        return $fail('中文版默认法币已经存在请修改');
                    }
                },
            ],
            'is_default_en' => [
                'required',
                'numeric',
                'in:1,2',
                function($attribute, $value, $fail) {
                    $defaultCn = \DB::table('legal_currencies')->where('is_default_en',1)->first();
                    if ($defaultCn && $value==1) {
                        return $fail('英文版默认法币已经存在请修改');
                    }
                },
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
            'name.unique' => '该配置项已经存在',
        ];
    }
}
