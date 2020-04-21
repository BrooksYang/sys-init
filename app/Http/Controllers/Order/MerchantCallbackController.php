<?php

namespace App\Http\Controllers\Order;

use App\Models\OTC\OtcOrder;
use App\Traits\ApiHelper;
use App\User;
use App\Utilities\Signature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 商户入金手动回调
 *
 * Class MerchantCallbackController
 * @package App\Http\Controllers\Order
 */
class MerchantCallbackController extends Controller
{
    use ApiHelper;

    private $order;

    /**
     * 商户入金回调
     *
     * @param $order
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function callback($order)
    {
        $order = OtcOrder::where('id', $order)
            ->status(OtcOrder::RECEIVED)
            ->whereNotNull('merchant_callback')
            ->where('is_callback', OtcOrder::NOT_CALLBACK)
            ->first();

        $this->order = $order;

        $this->handle();

        return response()->json(['code'=>0, 'msg'=>'已回调']);
    }

    /**
     * Execute the job.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        // 商户回调（对应商户下单v2接口）
        if (empty($this->order->merchant_callback)) {
            return;
        }

        // 获取商户及 app key
        $merchant = User::find($this->order->merchant_id);
        $appKey = $merchant->appKey;

        // 待签名数据
        $timestamp = time();
        $nonce = rand(10000, 99999);
        $params = [
            'order_id'          => $this->order->id,
            'merchant_order_id' => $this->order->merchant_order_id,
            'status'            => 'success',
            'access_key'        => $appKey->access_key,
            'timestamp'         => $timestamp,
            'nonce'             => $nonce,
        ];

        // 签名
        $sign = (new Signature())->sign($params, $appKey->secret_key);

        // 入金回调
        $url = "{$this->order->merchant_callback}?order_id={$this->order->id}&merchant_order_id={$this->order->merchant_order_id}&status=success&access_key={$appKey->access_key}&timestamp=$timestamp&nonce=$nonce&sign=$sign";

        try {
            $response = $this->post($url);
            $this->order->is_callback = OtcOrder::CALLBACK;
            $this->order->callback_response = json_encode($response, JSON_UNESCAPED_UNICODE);
            $this->order->save();
        } catch (\Exception $exception) {
            $this->order->is_callback = OtcOrder::CALLBACK;
            $this->order->callback_response = json_encode($exception->getCode() . '|' . $exception->getMessage());
            $this->order->save();
        }

    }
}