<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueInitRequest extends FormRequest
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
            'name_cn' =>[
                'required',
                'unique:dcuex_issuer_account,name_cn',
                'regex:/^[\x7f-\xff]+$/'
            ] ,
            'name_en' =>[
                'required',
                'regex:/^[a-z\sA-Z]+$/'   //允许英文字符和空格
            ] ,
            'abbr_en' => [
                'required',
                'unique:dcuex_issuer_account,abbr_en',
                'regex:/^[a-zA-Z]+$/'    //允许英文字符
            ],
            //账户信息暂不编辑
            'issuer' => 'required_with:edit_flag|email|unique:dcuex_issuer_account,issuer',
            'password' => 'required_with:edit_flag|required|alpha_dash',
            'repeat_pwd' => 'required_with:edit_flag|same:password',
            'addr' => 'required',
            'phone' => 'required|phone',
        ];
    }
}
