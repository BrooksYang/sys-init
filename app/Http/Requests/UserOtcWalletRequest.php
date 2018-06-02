<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;


class UserOtcWalletRequest extends FormRequest
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
            'currency_id' =>
                'required|numeric|min:1|unique:otc_balances,currency_id,'.$request->wallet,
            'available' =>
                'required|unumeric|min:1',
            'frozen' =>
                'nullable|unumeric|min:1',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            'currency_id' => '该币种的 OTC 记账钱包已存在', //交易用户
        ];
    }
}
