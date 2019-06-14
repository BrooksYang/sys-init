<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class SysWalletRequest extends FormRequest
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
            'sys_wallet_currency_id' =>
                'required|numeric|min:1|unique:wallet_balances_system,sys_wallet_currency_id,'.$request->wallet,
            'sys_wallet_balance' =>
                'required|unumeric|min:1',
            'sys_wallet_balance_freeze_amount' =>
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
            'sys_wallet_currency_id.unique' => '该币种的记账钱包已存在', //运营方
        ];
    }
}
