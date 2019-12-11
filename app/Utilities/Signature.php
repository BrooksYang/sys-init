<?php

namespace App\Utilities;

/**
 * 签名工具
 *
 * @package App\Utilities
 */
class Signature
{
    /**
     * 表单提交字符集编码
     *
     * @var string
     */
    public $postCharset = "UTF-8";

    /**
     * @var string
     */
    private $fileCharset = "UTF-8";

    /**
     * 签名
     *
     * @param $data
     * @param $secretKey
     * @return string
     */
    public function sign($data, $secretKey)
    {
        // 处理签名内容
        $data = $this->getSignContent($data);

        // 拼接私钥
        $data .= "&secret_key=$secretKey";

        // 默认 md5 签名
        return md5($data);
    }

    /**
     * 处理签名内容
     *
     * @param $params
     * @return string
     */
    public function getSignContent($params)
    {
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;

        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->charset($v, $this->postCharset);
                $v = urlencode($v);

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i ++;
            }
        }

        unset ($k, $v);

        return $stringToBeSigned;
    }

    /**
     * 校验$value是否非空
     *
     * @param $value
     * @return bool
     */
    protected function checkEmpty($value)
    {
        if (!isset($value) || is_null($value) || trim($value) === '') {
            return true;
        }

        return false;
    }

    /**
     * 转换字符集编码
     *
     * @param $data
     * @param $targetCharset
     * @return string
     */
    public function charset($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = $this->fileCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }

        return $data;
    }
}
