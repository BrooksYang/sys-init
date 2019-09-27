<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait ApiHelper
{
    /**
     * GET
     *
     * @param       $url
     * @param array $params
     * @param       $token
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($url, $params = [], $token = '')
    {

        return $this->sendRequest('GET', $url, $params, $token);
    }

    /**
     * POST
     *
     * @param       $url
     * @param array $params
     * @param       $token
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($url, $params = [], $token = '')
    {
        return $this->sendRequest('POST', $url, $params, $token);
    }

    /**
     * PATCH
     *
     * @param       $url
     * @param array $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patch($url, $params = [])
    {
        return $this->sendRequest('PATCH', $url, $params);
    }


    /**
     * PUT
     *
     * @param       $url
     * @param array $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put($url, $params = [])
    {
        return $this->sendRequest('PUT', $url, $params);
    }

    /**
     * 发送 HTTP 请求
     *
     * @param       $method
     * @param       $url
     * @param array $params
     * @param       $token
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendRequest($method, $url, $params = [], $token = '')
    {
        // 获取token
        $token = $token ?: session('token');

        // 发送请求
        $client = new Client(['headers' => ['Authorization' => "Bearer $token"]]);
        if (strtolower($method) == 'get') {
            $response = $client->request($method, $url, ['query' => $params]);
        } else {
            $response = $client->request($method, $url, ['json' => $params, 'verify' => false]);
        }

        // 请求状态异常处理
        $code = $response->getStatusCode();
        if ($code != 200) abort($code);

        $responseString = (string) $response->getBody();

        // 返回数据
        $data = json_decode($responseString, true);

        return $data;
    }

    /**
     * 发送 POST 请求
     *
     * @param $url
     * @param $data
     * @return mixed
     */
    public function curlPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }

    /**
     * 发送 GET 请求
     *
     * @param $url
     * @param $params
     * @return mixed
     */
    public function curlGet($url, $params)
    {
        if ($params) {
            $url .= implode('&', $params);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }

}
