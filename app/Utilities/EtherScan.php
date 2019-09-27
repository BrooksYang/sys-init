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
     * 请求 etherscan
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendEtherscanRequest($params)
    {
        $apikey = $this->apiKeyToken;

        $params += compact('apikey');

        $response = $this->post($this->apiUrl, $params);

        if (isset($response['error'])) {
            \Log::warning('EtherScan: ' . json_encode($response));
        }

        return $response['result'] ?? 0;
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
     * 根据地址获取账户余额 - ether
     *
     * @param $address
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthBalance($address)
    {
        $response = $this->sendRequest('GET', $this->apiUrl, [
            'module'  => 'account',
            'action'  => 'balance',
            'address' => $address,
            'tag'     => 'latest',
            'apikey'  => $this->apiKeyToken,
        ]);

        return $response;
    }

    /**
     * 根据地址获取账户余额 - Erc20-token-(USDT)
     *
     * @param $address
     * @param $contractAddress
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTokenBalance($address, $contractAddress)
    {
        $response = $this->sendEtherscanRequest([
            'module'          => 'account',
            'action'          => 'tokenbalance',
            'contractaddress' => $contractAddress,
            'address'         => $address,
            'tag'             => 'latest',
        ]);

        return $response;
    }


}
