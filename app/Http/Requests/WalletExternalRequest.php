<?php

namespace App\Http\Requests;

use App\Models\Wallet\WalletExternal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class WalletExternalRequest extends FormRequest
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
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        $table = (new WalletExternal())->getTable();

        return [
            'address' => [
                'required',
                "unique:$table,address,".$request->withdrawAddr,
                'max:255'
            ],
            'desc' => 'nullable|max:255'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'address.unique' => '地址已存在',
        ];
    }
}
