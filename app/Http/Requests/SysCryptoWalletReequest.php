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
                'required|numeric|unique:dcuex_sys_crypto_wallet,sys_crypto_wallet_currency_id,'.$request->cryptoWallet,
            'sys_crypto_wallet_title' =>
                'required|unique:dcuex_sys_crypto_wallet,sys_crypto_wallet_title,'.$request->cryptoWallet,
            'sys_crypto_wallet_address' =>
                'required|unique:dcuex_sys_crypto_wallet,sys_crypto_wallet_address,'.$request->cryptoWallet,
        ];
    }
}
