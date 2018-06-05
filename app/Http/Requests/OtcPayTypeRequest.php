<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class OtcPayTypeRequest extends FormRequest
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
            'name' =>[
                'required',
                'unique:otc_pay_types,name,'.$request->payType,
                'max:255',
            ],
            'name_en' => 'alpha_dash|max:255',
            //'name_en' => ['regex:/^[a-zA-Z]+$/' ],    //允许英文字符
            'icon' => 'nullable|max:255',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            'name.unique' => '该 OTC 支付类型名称已存在',
        ];
    }
}
