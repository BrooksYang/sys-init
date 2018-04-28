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
                'unique:dcuex_crypto_currency,currency_title_cn,'.$request->get('currencyTypeInit'),
                'regex:/^[\x7f-\xff]+$/'
            ] ,
            'currency_title_en' =>[
                'required',
                'regex:/^[a-z\sA-Z]+$/'   //允许英文字符和空格
            ] ,
            'currency_title_en_abbr' => [
                'required',
                'unique:dcuex_crypto_currency,currency_title_en_abbr,'.$request->get('currencyTypeInit'),
                'regex:/^[a-zA-Z]+$/'    //允许英文字符
            ],
            'currency_type_id' => 'required|min:1',
            'currency_issue_date' => 'required|date',
            'currency_issue_amount' => 'required|numeric',
            'currency_issue_circulation' => 'required|numeric|max:'.$request->get('currency_issue_amount'),
            'currency_issuer_website' => 'required|url',
            'white_paper_url' => 'required|url',
            'block_chain_record_url' => 'required|url',
            //TODO 图片类-型尺寸限定
            //'currency_icon' => 'mimes:jpeg,png,bmp|dimensions:min_width=80,min_height=80',

        ];
    }
}
