<?php

return [

    // 客服人员角色
    'supervisor_role' => env('SUPERVISOR_ROLE', 3),

    // 商户默认密码
    'merchant_pwd' => env('MERCHANT_PWD', 'otc@merchant2019'),

    // 系統显示币种
    'currency_usdt' => 'USDT',

    // 是否开启OTC运营方提币
    'enable_sys_withdraw' => env('ENABLE_SYS_WITHDRAW', false),
];
