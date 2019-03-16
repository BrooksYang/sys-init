<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class KycLevelRequest extends FormRequest
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
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'name' =>[
                'required',
                'unique:kyc_levels,name,'.$request->manage,
                'max:255',
            ],
            'level' =>[
                'required',
                'unique:kyc_levels,level,'.$request->manage,
                'numeric',
                'min:1',
            ],
            'withdraw_amount_daily' => 'nullable|numeric|min:0',
            'description' => 'nullable|max:255',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            'name.unique' => '该 KYC 认证等级名称已存在',
            'level.unique' => '该 KYC 认证等级值已存在',
        ];
    }
}
