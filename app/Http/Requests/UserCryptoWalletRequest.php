<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserCryptoWalletRequest extends FormRequest
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
            'crypto_wallet_currency_id' =>
                'required|numeric|min:1|unique:dcuex_user_crypto_wallet,withdraw_currency_id,'.$request->cryptoWallet,
            'crypto_wallet_title' =>
                'required|max:255|unique:dcuex_user_crypto_wallet,crypto_wallet_title,'.$request->cryptoWallet,
            'crypto_wallet_address' =>
                'required|max:255|unique:dcuex_user_crypto_wallet,crypto_wallet_address,'.$request->cryptoWallet,
            'crypto_wallet_description' => 'nullable|max:255'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            'crypto_wallet_currency_id.unique' => '该币种的真实钱包已存在',  //交易用户
            'crypto_wallet_title.unique' => '该币种钱包地址名称或标题已存在',
            'crypto_wallet_address.unique' => '该币种真实钱包的钱包地址已存在',
        ];
    }
}
