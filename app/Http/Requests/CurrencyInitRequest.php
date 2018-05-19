<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CurrencyInitRequest extends FormRequest
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
            'currency_title_cn' =>[
                'required',
                'unique:dcuex_crypto_currency,currency_title_cn,'.$request->currencyTypeInit,
                'regex:/^[\x7f-\xff]+$/',
                'max:100'
            ] ,
            'currency_title_en' =>[
                'required',
                'unique:dcuex_crypto_currency,currency_title_en,'.$request->currencyTypeInit,
                'regex:/^[a-z\sA-Z]+$/',   //允许英文字符和空格
                'max:100'
            ] ,
            'currency_title_en_abbr' => [
                'required',
                'unique:dcuex_crypto_currency,currency_title_en_abbr,'.$request->currencyTypeInit,
                'regex:/^[a-zA-Z]+$/',    //允许英文字符
                'max:50'
            ],
            'currency_type_id' => 'required|min:1',
            'currency_issue_date' => 'required|date',
            'currency_issue_amount' => 'required|numeric',
            'currency_issue_circulation' => 'required|numeric|max:'.$request->currency_issue_amount,
            'currency_issuer_website' => 'required|url',
            'white_paper_url' => 'required|url',
            'wallet_download_url' => 'nullable|url',
            'block_chain_record_url' => 'required|url',
            'currency_summary' => 'nullable|max:500',
            'currency_intro' => 'nullable',
            //TODO 图片类-型尺寸限定-UploadHandle
            //'currency_icon' => 'mimes:jpeg,png,bmp|dimensions:min_width=80,min_height=80,ratio=1/1',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     * @return array
     */
    public function messages()
    {
        return [
            'currency_title_cn.unique' => '该币种的中文名称已存在',
            'currency_title_cn.regex' => '仅允许中文字符',
            'currency_title_en.unique' => '该币种的英文名称已存在',
            'currency_title_en.regex' => '仅允许英文字符和空格',
            'currency_title_en_abbr.unique' => '该币种的英文简称已存在',
            'currency_title_en_abbr.regex' => '仅允许英文字符',
            'currency_issue_circulation.max' => '流通数量不得大于发行数量',
        ];
    }
}
