<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class TraderIncomeRequest extends FormRequest
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
        $sumPercentage = $request->sys_percentage + $request->sys_percentage;

        return [
            'is_leader' => 'required|in:0,1',
            'deposit_fee' => 'required|numeric|min:0',
            'self_percentage' => 'sometimes|numeric|min:0',
            'sys_percentage' => 'sometimes|numeric|min:0',
        ];
    }
}
