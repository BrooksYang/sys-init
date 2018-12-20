<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class AdsRequest extends FormRequest
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
            'title' => [
                'nullable',
                //'unique:portal_ads,title,'.$request->ad,
                'max:255'
            ],
            'cover' => 'required|max:255',
            'url' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            //'title.unique' => '该标题已存在'
        ];
    }
}
