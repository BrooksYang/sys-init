<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

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
    public function rules(Request $request)
    {
        //TODO 重置账户和密码
        $issuerAccountRule = 'required|unique:dcuex_issuer_account,issuer_account,'.$request->issurerInit;
        $password = 'required|alpha_dash';
        $repeatPwd = 'required|same:password';

        if ($request->editFlag) {
            $issuerAccountRule = $password = $repeatPwd = 'nullable';
        }

        return [
            'issuer_title_cn' =>[
                'required',
                'unique:dcuex_issuer_account,issuer_title_cn,'.$request->issurerInit,
                'regex:/^[\x7f-\xff]+$/'
            ] ,
            'issuer_title_en' =>[
                'required',
                'unique:dcuex_issuer_account,issuer_title_en,'.$request->issurerInit,
                'regex:/^[a-z\sA-Z]+$/'   //允许英文字符和空格
            ] ,
            'issuer_title_en_abbr' => [
                'required',
                'unique:dcuex_issuer_account,issuer_title_en_abbr,'.$request->issurerInit,
            ],
            //账户信息暂不编辑
            'issuer_account' => $issuerAccountRule,
            'password' => $password,
            'repeat_pwd' => $repeatPwd,
            'issuer_address' => 'required',
            'issuer_phone' => 'required|phone',
        ];
    }
}
