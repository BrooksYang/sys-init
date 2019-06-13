<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class SysCryptoWalletReequest extends FormRequest
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
            'sys_crypto_wallet_currency_id' =>
                'required|numeric|max:255|unique:wallets_system,sys_crypto_wallet_currency_id,'.$request->cryptoWallet,
            'sys_crypto_wallet_title' =>
                'required|max:255|unique:wallets_system,sys_crypto_wallet_title,'.$request->cryptoWallet,
            'sys_crypto_wallet_address' =>
                'required|max:255|unique:wallets_system,sys_crypto_wallet_address,'.$request->cryptoWallet,
            'sys_crypto_wallet_description' => 'nullable|max:255'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            'sys_crypto_wallet_currency_id.unique' => '该币种的数字钱包已存在', //运营方
            'sys_crypto_wallet_title.unique' => '该币种数字钱包的名称或标题已存在',
            'sys_crypto_wallet_address.unique' => '该币种数字钱包的钱包地址已存在',
        ];
    }
}
