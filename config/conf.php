<?php

return [

    // 系统页面布局全屏
    'layout_full' => env('LAYOUT_FULL', true),

    // 客服人员角色
    'supervisor_role' => env('SUPERVISOR_ROLE', 3),

    // 商户默认密码
    'merchant_pwd' => env('MERCHANT_PWD', 'otc@merchant2019'),

    // 默认登录及支付密码
    'def_user_pwd' => env('DEFAULT_PWD', 'otc@2020'),
    'def_user_pay_pwd' => env('DEFAULT_PAY_PWD', 'otcpay@2020'),

    // 系統显示币种
    'currency_usdt' => 'USDT',
    'currency_ttk'  => 'TTK',

    // 是否开启OTC运营方提币
    'enable_sys_withdraw' => env('ENABLE_SYS_WITHDRAW', false),

    // 领导人保证金 - USDT
    'leader_margin' => env('LEADER_MARGIN', 10000),

    // otc最小提币额 - USDT
    'withdraw_min' => env('WITHDRAW_MIN', 300),

    // otc最大提币额 - USDT
    'withdraw_max' => env('WITHDRAW_MAX', 30000),

    // 出金-快捷抢单白名单
    'otc_quick_order_white_list' => env('OTC_QUICK_ORDER_WHITE_LIST', '121,122,133,277,361,369,617,1715'),

    // 系统环境
    'app_debug' => env('APP_DEBUG', true),

];
