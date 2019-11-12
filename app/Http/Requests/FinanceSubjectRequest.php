<?php

namespace App\Http\Requests;

use App\Models\Wallet\FinanceSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class FinanceSubjectRequest extends FormRequest
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
     * @param $request
     * @return array
     */
    public function rules(Request $request)
    {
        $table = FinanceSubject::getModel()->getTable();

        return [
            'title' => [
                'required',
                "unique:$table,title,".$request->subject,
                'max:255'
            ],
            'desc' => 'nullable|max:255',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            'title.unique' => '该科目已存在'
        ];
    }
}
