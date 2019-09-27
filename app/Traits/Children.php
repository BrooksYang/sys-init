<?php

namespace App\Traits;

use App\User;

/**
 * 获取子节点
 *
 * Trait GetWeek
 * @package App\Traits
 */
trait Children {

    /**
     * 递归获取子节点
     * @param $uid
     * @param $uidS
     * @return mixed
     */
    public static function readChild($uid, &$uidS = [])
    {
        $childrenIds = User::where('pid', $uid)->pluck('id')->toArray();

        //if (!$uidS) { array_unshift($uidS, $childrenIds); }
        if ($childrenIds) {
            $uidS[$uid] = $childrenIds;
        }

       foreach ($childrenIds as $key => $child) {
            self::readChild($child, $uidS);
        }

        return $uidS;
    }

    /**
     * 递归获取非管理级别的子节点
     * @param $uid
     * @param $uidS
     * @return mixed
     */
    public function readGeneralChild($uid, &$uidS = [])
    {
        $childrenIds = User::where('pid', $uid)
            ->whereIn('settle_level', [User::SINGLE])->pluck('id')->toArray();

        if ($childrenIds) { array_unshift($uidS, $childrenIds); }

        foreach ($childrenIds as $key => $child) {
            $this->readGeneralChild($child, $uidS);
        }

        return array_flatten($uidS);
    }


} 