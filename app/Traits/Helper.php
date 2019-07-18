<?php

namespace App\Traits;

trait Helper
{
    /**
     * id 格式化（逗号隔开的id集合）
     *
     * @param $ids
     * @return array
     */
    public function idFormat($ids)
    {
        $ids = explode(',', $ids);
        $ids = array_unique($ids);
        $ids = array_filter($ids, function ($item) {
            return !empty($item);
        });
        return $ids;
    }
    /**
     * 检查ip格式
     *
     * @param $ip
     * @return bool
     */
    public function isIp($ip)
    {
        $preg = "/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
        if (preg_match($preg, $ip)) {
            return true;
        }
        return false;
    }
}