<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CurrencyContractToUserRequest extends FormRequest
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
            'currency_id' =>'required|numeric|min:1|unique:dcuex_user_currency_contract,currency_id,'.$request->userCurrencyContract,
            'symbol' => 'required',
            'user_withdraw_daily_amount_limit' =>'required|numeric|min:1',
            'user_withdraw_daily_count_limit' =>'required|numeric|min:1',
            'user_withdraw_fee_rate' =>'required|numeric|min:0.000000001',
            'user_deposit_minimum_amount' =>'required|numeric|min:1',
            'user_sell_daily_limit' =>'required|numeric|min:1',
            'user_deposit_warning' =>'required|max:255',
            'user_withdraw_warning' =>'required|max:255',

        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            'currency_id.unique' => '该币种交易合约已经存在'
        ];
    }
}
