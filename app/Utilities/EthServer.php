<?php

namespace App\Utilities;

use App\Exceptions\ErrorCode;
use App\Exceptions\EthException;
use App\Traits\ApiHelper;

class EthServer
{
    use ApiHelper;

    /**
     * 以太坊钱包服务地址
     *
     * @var
     */
    private $apiUrl;

    /**
     * 确认真实交易块数
     *
     * @var int
     */
    private $blocks = 12;

    /**
     * Gas
     *
     * @var float
     */
    public $gas = 0.00005;

    /**
     * EtherScan constructor.
     */
    public function __construct()
    {
        $this->apiUrl = config('blockChain.eth_server');
    }

    /**
     * 获取当前块
     *
     * @return mixed
     * @throws EthException
     */
    public function getBlockNumber()
    {
        $response = $this->postEthServer('eth_blockNumber');

        return $response['result'];
    }

    /**
     * 获取账户列表
     *
     * @return mixed
     * @throws EthException
     */
    public function getAccounts()
    {
        $response = $this->postEthServer('eth_accounts');

        return $response['result'];
    }

    /**
     * 创建账号
     *
     * @param $password
     * @return mixed
     * @throws EthException
     */
    public function newAccount($password)
    {
        $response = $this->postEthServer('personal_newAccount', [$password]);

        return $response['result'];
    }

    /**
     * 查询账户余额
     *
     * @param $address
     * @return mixed
     * @throws EthException
     */
    public function getBalance($address)
    {
        // 默认12块之后认为是真实交易
        $latest = $this->getBlockNumber();
        $block = hexdec($latest) - $this->blocks;
        $block = '0x' . dechex($block);

        // 获取余额
        $response = $this->postEthServer('eth_getBalance', [$address, $block]);

        // 获取余额
        $balance = hexdec($response['result']) / pow(10, 18);

        return $balance;
    }

    /**
     * 转账
     *
     * @param $from
     * @param $to
     * @param $value
     * @param $password
     * @return mixed
     * @throws EthException
     */
    public function sendTransaction($from, $to, $value, $password)
    {
        $value = $value * pow(10, 18);

        // 10进制转为16进制
        $value = '0x' . dechex($value);

        // 交易信息
        $tx = compact('from', 'to', 'value');

        $response = $this->postEthServer('personal_sendTransaction', [$tx, $password]);

        return $response['result'];
    }

    /**
     * 获取某一区块交易数量
     *
     * @param $blockNumber
     * @return mixed
     * @throws EthException
     */
    public function getBlockTransactionCountByNumber($blockNumber)
    {
        $response = $this->postEthServer('eth_getBlockTransactionCountByNumber', [$blockNumber]);

        return $response['result'];
    }

    /**
     * 根据区块，及区块索引，获取交易详情
     *
     * @param $blockNumber
     * @param $index
     * @return mixed
     * @throws EthException
     */
    public function getTransactionByBlockNumberAndIndex($blockNumber, $index)
    {
        $response = $this->postEthServer('eth_getTransactionByBlockNumberAndIndex', [$blockNumber, $index]);

        return $response['result'];
    }

    /**
     * 根据交易hash获取交易详情
     *
     * @param $hash
     * @return mixed
     * @throws EthException
     */
    public function getTransactionByHash($hash)
    {
        $response = $this->postEthServer('eth_getTransactionByHash', [$hash]);

        return $response['result'];
    }

    /**
     * 根据交易hash获取收据信息
     *
     * @param $hash
     * @return mixed
     * @throws EthException
     */
    public function getTransactionReceipt($hash)
    {
        $response = $this->postEthServer('eth_getTransactionReceipt', [$hash]);

        return $response['result'];
    }

    /**
     * 根据区块hash获取区块信息，以及区块下的所有交易
     *
     * @param $blockHash
     * @param $bool
     * @return mixed
     * @throws EthException
     */
    public function getBlockByHash($blockHash, $bool = true)
    {
        $response = $this->postEthServer('eth_getBlockByHash', [$blockHash, $bool]);

        return $response['result'];
    }

    /**
     * 根据区块号获取区块信息，以及区块下的所有交易
     *
     * @param $blockNumber
     * @param $bool
     * @return mixed
     * @throws EthException
     */
    public function getBlockByNumber($blockNumber, $bool = true)
    {
        $response = $this->postEthServer('eth_getBlockByNumber', [$blockNumber, $bool]);

        return $response['result'];
    }

    /**
     * 检查同步状态
     *
     * @return mixed
     * @throws EthException
     */
    public function syncing()
    {
        $response = $this->postEthServer('eth_syncing');

        return $response['result'];
    }

    /**
     * 获取所有 parity 用户
     *
     * @return mixed
     * @throws EthException
     */
    public function parityAllAccountsInfo()
    {
        $response = $this->postEthServer('parity_allAccountsInfo');

        return $response['result'];
    }

    /**
     * 请求 Eth Server
     *
     * @param $method
     * @param $params
     * @return mixed
     * @throws EthException
     */
    private function postEthServer($method, $params = [])
    {
        $timestamp = time();

        $params = [
            'jsonrpc' => '2.0',
            'method'  => $method,
            'params'  => $params,
            'id'      => $timestamp,
        ];

        // 发送请求
        $response = $this->curlPost($this->apiUrl, $params);

        // 检查返回数据
        $this->checkResponse($response, $timestamp);

        return $response;
    }

    /**
     * 检查返回数据
     *
     * @param $response
     * @param $timestamp
     * @throws EthException
     */
    private function checkResponse($response, $timestamp)
    {
        // 检查是否查询错误
        if (isset($response['error'])) {
            throw new EthException(['code' => @$response['error']['code'], 'msg' => '服务异常，请稍后再试！']);
        }

        // 检查查询id是否正确
        if (@$response['id'] != $timestamp) {
            throw new EthException(ErrorCode::SERVICE_UNREACHABLE);
        }
    }
}
