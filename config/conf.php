<?php

return [

    // 系统页面布局全屏
    'layout_full' => env('LAYOUT_FULL', true),

    // 客服人员角色
    'supervisor_role' => env('SUPERVISOR_ROLE', 3),

    // 商户默认密码
    'merchant_pwd' => env('MERCHANT_PWD', 'otc@merchant2019'),

    // 系統显示币种
    'currency_usdt' => 'USDT',

    // 是否开启OTC运营方提币
    'enable_sys_withdraw' => env('ENABLE_SYS_WITHDRAW', false),

    // 领导人保证金 - USDT
    'leader_margin' => env('LEADER_MARGIN', 10000),

    // 系统环境
    'app_debug' => env('APP_DEBUG', true),

];
