<?php

namespace App\Console\Commands;

use App\Http\Controllers\Order\UserOtcOrderController;
use App\Models\Currency;
use App\Models\LegalCurrency;
use App\Models\OTC\OtcOrder;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OtcOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:otc-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print the OTC orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        bcscale(config('app.bcmath_scale'));
        $currency = config('conf.currency_usdt');
        $rate = LegalCurrency::rmbRate();

        $otcBuyTotal = Cache::get('otcBuyTotal');

        $otcOrders = OtcOrder::with(['currency','legalCurrency'])
            ->type(OtcOrder::BUY)
            ->currency(Currency::USDT)
            ->status(OtcOrder::RECEIVED)
            ->latest()
            ->take(100)
            ->get();

        $otcOrders = $otcOrders->sortBy('created_at');
        $otcOrders = $otcOrders->values()->all();;

        $this->info('Trade in Amount: ￥'.bcmul(@$otcBuyTotal->field_amount, $rate,8));

        foreach ($otcOrders as $key=>$order) {
            $this->line(
                '订单id: #'.$order->id.'|'.
                '商户: '.$order->merchant_id.'|'.
                '广告: '.$order->merchant_id.'|'.
                'UID: '.$order->user_id.'|'.
                '广告商: '.$order->from_user_id.'|'.
                '币种: '.$currency.'|'.
                '法币: '.@$order->legalCurrency->abbr.'|'.
                '单价: '.@$order->price.'|'.
                '交易量: '.$order->field_amount.$currency.'|'.
                '总价: '.$order->cash_amount.'|'.
                '手续费: '.$order->fee.'|'.
                //'团队红利: '.$order->team_bonus.'|'.
                '到账: '.$order->final_amount.'|'.
                '状态: 成功|'.
                '备注: '.str_limit($order->remark,4).'|'.
                'Card: '.$order->card_number.'|'.
                '商户订单: '.$order->merchant_order_id.'|'.
                '时间: '.$order->created_at.'|'
            );

            sleep(rand(1,3));
        }

    }
}
