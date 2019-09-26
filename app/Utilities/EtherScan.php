<?php

namespace App\Utilities;

use App\Traits\ApiHelper;

class EtherScan
{
    use ApiHelper;

    /**
     * @var
     */
    private $apiUrl;

    /**
     * @var
     */
    private $apiKeyToken;

    /**
     * EtherScan constructor.
     */
    public function __construct()
    {
        $this->apiKeyToken = config('blockChain.eth_api_key_token');
        $this->apiUrl = config('blockChain.eth_api_url');
    }

    /**
     * 根据Hex获取交易信息
     *
     * @param $transactionHash
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTransactionByHash($transactionHash)
    {
        $response = $this->sendRequest('POST', $this->apiUrl, [
            'module' => 'proxy',
            'action' => 'eth_getTransactionByHash',
            'txhash' => $transactionHash,
            'apikey' => $this->apiKeyToken,
        ]);

        return $response;
    }

    /**
     * 根据地址获取账户余额
     *
     * @param $address
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccountBalance($address)
    {
        $response = $this->sendRequest('GET', $this->apiUrl, [
            'module'  => 'account',
            'action'  => 'balance',
            'address' => $address,
            'tag'     => 'latest',
            'apikey'  => $this->apiKeyToken,
        ]);

        // module=account&action=balance&address=0x68cee7e8dddadfd72626f64ff8e54b076f5c8ba3&tag=latest&apikey=KPIV3HWTX3U4GY4WBNBAB3MYB6WT3ASQK5
        /*$response = $this->curlGet($this->apiUrl, [
            '?module=account',
            'action=balance',
            "address=$address",
            'tag=latest',
            "apikey=$this->apiKeyToken"
        ]);*/

        return $response;
    }


}
