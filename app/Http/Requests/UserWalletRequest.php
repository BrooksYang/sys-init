<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserWalletRequest extends FormRequest
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
            'user_wallet_currency_id' =>
                'required|numeric|min:1|unique:dcuex_user_wallet,user_wallet_currency_id,'.$request->wallet,
            'user_wallet_balance' =>
                'required|unumeric|min:1',
            'user_wallet_balance_freeze_amount' =>
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
            'user_wallet_currency_id' => '该币种的记账钱包已存在', //交易用户
        ];
    }
}
