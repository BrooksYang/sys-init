<?php

/*
|--------------------------------------------------------------------------
| 钱包服务
|--------------------------------------------------------------------------
|
| 钱包服务相关配置
|
*/

return [

    // 系统提币地址
    'sys_withdraw_addr'  =>env('SYS_WITHDRAW_ADDR','0x68cee7e8dddadfd72626f64ff8e54b076f5c8ba3'),

    // ETH API URL
    'eth_api_url'         => 'https://api.etherscan.io/api',

    // API KEY TOKEN
    'eth_api_key_token'   => env('ETHTERSCAN_API_KEY_TOKEN', 'KPIV3HWTX3U4GY4WBNBAB3MYB6WT3ASQK5'),

    // BTC API URL
    'btc_api_url'         => 'https://blockchain.info/',

    // ETH 服务地址
    'eth_server'          => env('ETH_SERVER', '13.70.5.71:9988'),

    // eth钱包key前缀
    'eth_key_prefix'      => env('ETH_KEY_PREFIX', 'ex_eth_'),

    // BTC 服务地址
    'btc_server'          => env('BTC_SERVER', '13.70.5.71:8332'),

    // BTC用户名密码
    'btc_user'            => [
        'user'     => env('BTC_USER', 'bitcoincorerpc'),
        'password' => env('BTC_PASSWORD', 'fjdlaidsFJDKLSAfjdsl'),
    ],

    // btc账户前缀
    'btc_account_prefix'  => 'ex_btc',

    // btc 账户
    'btc_account'        => env('BTC_MASTER_ACCOUNT', 'ex_btc_account_master_abn4vtU5irmgkzlhNLMj'),

    // 云钱包地址（测试服）
    //'infura_address'      => env('INFURE_ADDRESS', 'https://rinkeby.infura.io/v3/52fcb8d1f8ee4f1db9d1b09db89162a9')

    // 云钱包地址（正式服）
    'infura_address'      => env('INFURE_ADDRESS', 'https://mainnet.infura.io/v3/52fcb8d1f8ee4f1db9d1b09db89162a9'),

    // 获取ETH交易记录
    'etherscan_url'       => env('ETHERSCAN_URL', 'https://api.etherscan.io/api?module=account&action=txlist&sort=desc&apikey=VT16MB6ZPSRGMIATQ7WZXWSQUNZNCQIFCX&address='),

    // 获取token交易记录（非eth，其他ERC20 - Token）
    'etherscan_token'     => env('ETHERSCAN_TOKEN', 'https://api.etherscan.io/api?module=account&action=tokentx&sort=desc&apikey=VT16MB6ZPSRGMIATQ7WZXWSQUNZNCQIFCX&address='),

    // LG 智能合约地址，或者其他智能合约地址，这些其实可以存在库里
    'lg_address'          => env('LG_ADDRESS', '0xc520f3ac303a107d8f4b08b326b6ea66a4f961cd'),

    // erc20 usdt 智能合约地址
    'usdt'              => [
        'name'      => 'Tether USD',
        'precision' => 6,
        'contract'  => '0xdac17f958d2ee523a2206206994597c13d831ec7',
    ],

];
