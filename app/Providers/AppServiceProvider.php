<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // validate phone
        Validator::extend('phone', function($attribute, $value, $parameters, $validator) {
            $mobileRule = '/^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/';
            $telephoneRule = '/^([0-9]{3,4}-)?[0-9]{7,8}$/';
            if(preg_match($mobileRule,$value) || preg_match($telephoneRule, $value)){
                return true;
            };
            return false;
        }, '手机或固定电话号码格式不正确');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
