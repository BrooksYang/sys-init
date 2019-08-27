<?php

namespace App\Http\Resources;

use App\Models\OTC\OtcOrder;

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
            'currency'       => $order->currency->abbr,
            'legal_currency' => $order->legalCurrency->abbr,
            'type'           => $order->type,
            'type_text'      => OtcOrder::$typeText[$order->type],
            'from'           => (@$order->tradeOwner->username ?:'--').' | '.(@$order->tradeOwner->email?:'--').' | '.(@$order->tradeOwner->phone ?:'--'),
            'from_user_id'   => $order->from_user_id,
            'to'             => (@$order->user->username ?:'--').' | '.(@$order->user->email?:'--').' | '.(@$order->user->phone ?:'--'),
            'remark'         => $order->remark,
            'user_id'        => $order->user_id,
            'field_amount'   => $order->field_amount,
            'cash_amount'    => $order->cash_amount,
            'price'          => $order->price,
            'status'         => $order->status,
            'status_text'    => OtcOrder::$statusTexts[$order->status],
            'appeal_status'  => $order->appeal_status,
            'appeal_text'    => OtcOrder::$appealText[$order->appeal_status ?: 0],
            'appeal_content' => $order->content,
            'created_at'     => $order->created_at->toDateTimeString(),
        ];
    }
}
