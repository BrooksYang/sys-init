<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserCryptoWalletReequest extends FormRequest
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
            'withdraw_currency_id' =>
                'required|numeric|min:1|unique:dcuex_user_crypto_wallet,withdraw_currency_id,'.$request->cryptoWallet,
            'crypto_wallet_title' =>
                'required|unique:dcuex_user_crypto_wallet,crypto_wallet_title,'.$request->cryptoWallet,
            'crypto_wallet_address' =>
                'required|unique:dcuex_user_crypto_wallet,crypto_wallet_address,'.$request->cryptoWallet,
        ];
    }
}
