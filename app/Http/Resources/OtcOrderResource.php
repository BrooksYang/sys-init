<?php

namespace App\Http\Resources;

use App\Models\OTC\OtcOrder;
use App\Models\OTC\OtcOrderQuick;
use App\User;

/**
 * 订单属性转换
 *
 * Class OtcOrderResource
 * @package App\Http\Resources
 */
class OtcOrderResource
{
    /**
     * @param $order
     * @return array
     */
    public static function attribute($order)
    {
        return [
            'id'             => $order->id,
            'merchant'       => @$order->merchant->username ?: @$order->merchant->phone,
            'currency'       => $order->currency->abbr,
            'legal_currency' => $order->legalCurrency->abbr,
            'type'           => $order->type,
            'type_text'      => OtcOrder::$typeText[$order->type],
            'from'           => (@$order->tradeOwner->username ?:'--').' | '.(@$order->tradeOwner->email?:'--').' | '.(@$order->tradeOwner->phone ?:'--'),
            'from_user_id'   => $order->from_user_id,
            'from_user_type' => User::find($order->from_user_id)->account_type,
            'to'             => (@$order->user->username ?:'--').' | '.(@$order->user->email?:'--').' | '.(@$order->user->phone ?:'--'),
            'remark'         => $order->remark,
            'card_number'    => $order->card_number,
            'user_id'        => $order->user_id,
            'field_amount'   => $order->field_amount,
            'cash_amount'    => $order->cash_amount,
            'price'          => $order->price,
            'status'         => $order->status,
            'status_text'    => OtcOrder::$statusTexts[$order->status],
            'appeal_status'  => $order->appeal_status,
            'appeal_text'    => OtcOrder::$appealText[$order->appeal_status ?: 0],
            'appeal_content' => $order->content,
            'merchant_order' => $order->merchant_order_id,
            'created_at'     => $order->created_at->toDateTimeString(),
        ];
    }

    /**
     * OTC 快捷抢单
     *
     * @param $order
     * @return array
     */
    public static function otcQuick($order)
    {
        return [
            'id'                    => $order->id,
            'merchant'              => @$order->merchant->username ?: @$order->merchant->phone,
            'user_id'               => $order->user_id,
            'from_user_type'        => User::find($order->user_id)->account_type,
            'merchant_id'           => $order->merchant_id,
            'user'                  => (@$order->user->username ?:'--').' | '.(@$order->user->email?:'--').' | '.(@$order->user->phone ?:'--'),
            'owner_phone'           => $order->owner_phone,
            'merchant_amount'       => number_format($order->merchant_amount,8),
            'merchant_final_amount' => $order->merchant_final_amount,
            'merchant_rate'         => number_format($order->merchant_rate,8),
            'cash_amount'           => number_format($order->cash_amount,8),
            'field_amount'          => number_format($order->field_amount, 8),
            'price'                 => number_format($order->price, 8),
            'rate'                  => number_format($order->rate, 8),
            'rate_sys'              => number_format($order->rate_sys, 8),
            'income_total'          => number_format($order->income_total, 8),
            'income_sys'            => number_format($order->income_sys, 8),
            'income_merchant'       => number_format($order->income_merchant, 8),
            'income_user'           => number_format($order->income_user, 8),
            'status'                => $order->status,
            'status_text'           => OtcOrderQuick::STATUS[$order->status]['name'],
            'appeal_status'         => $order->appeal_status,
            'appeal_text'           => OtcOrderQuick::APPEAL_STATUS[$order->appeal_status ?: 0]['name'],
            'appeal_content'        => $order->content,
            'remark'                => $order->remark,
            'card_number'           => $order->card_number,
            'payment_url'           => $order->payment_url,
            'merchant_order_id'     => $order->merchant_order_id,
            'created_at'            => $order->created_at->toDateTimeString(),
        ];
    }
}
