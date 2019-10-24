<?php

namespace App\Http\Requests;

use App\Models\OTC\UserAppKey;
use App\Rules\VerifyIpFormat;
use App\Rules\VerifyTimePeriod;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserAppKeyRequest extends FormRequest
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
        $userTable = User::getModel()->getTable();
        $emailRule = $request->phone ? 'nullable' : 'required';
        $phoneRule = $request->email ? 'nullable' : 'required';
        $typeRole = implode(',', array_keys(UserAppKey::TYPE));

        return [
            'country_id' => "required",
            'username' => [
                "sometimes",
                "unique:$userTable,username,".$request->user
            ],
            'email' => [
                $emailRule,
                "email",
                "max:255",
                "unique:$userTable,email,".$request->user
            ],
            'phone' => [
                $phoneRule,
                //"phone",
                "unique:$userTable,phone,".$request->user
            ],
            'id_number' => "sometimes|max:255",

            'ip' => [
                'sometimes',
                 new VerifyIpFormat($request->ip),
            ],
            'remark' => 'sometimes|max:255',

            'type'   => 'required|in:'.$typeRole,

            'is_enabled' => "required|in:0,1",
            'start_time' => "sometimes|required_with:end_time",
            'end_time' => [
                "sometimes",
                "required_with:start_time",
                new VerifyTimePeriod($request->start_time)
            ]
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'country_id.required' => '请选择国家',
            'username.unique'     => '用户名已存在',
            'email.required_without'  => '请填写邮箱或电话',
            'phone.required_without'  => '请填写邮箱或电话',
            'email.unique'  => '邮箱已存在',
            'phone.unique'  => '电话已存在',
            'remark.max'    => '备注最多包含255个字符',
        ];
    }
}
