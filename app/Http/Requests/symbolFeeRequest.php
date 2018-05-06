<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class symbolFeeRequest extends FormRequest
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
    public function rules()
    {
        return [
            'symbolFee.*.maker_fee' => 'required|numeric|min:0.000000001',
            'symbolFee.*.taker_fee' => 'required|numeric|min:0.000000001',
        ];
    }
}
