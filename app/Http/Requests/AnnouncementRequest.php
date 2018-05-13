<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class AnnouncementRequest extends FormRequest
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
            'anno_title' => [
                'required',
                'unique:dcuex_cms_announcement,anno_title,'.$request->announcement,
                'max:255'
                ],
            'anno_summary' => 'nullable|max:255',
            'anno_draft' => 'required',
            'anno_top' => 'required',
            'anno_content' => 'required',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
          'anno_title.unique' => '该公告标题已存在'
        ];
    }
}
