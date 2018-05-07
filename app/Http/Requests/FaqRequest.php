<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class FaqRequest extends FormRequest
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
            'faq_title' => [
                'required',
                'unique:dcuex_faq,faq_title,'.$request->manage,
                'max:255'
            ],
            'faq_key_words' => 'nullable|max:255',
            'is_draft' => 'required',
            'type_id' => 'required',
            'faq_content' => 'required',
        ];
    }
}
