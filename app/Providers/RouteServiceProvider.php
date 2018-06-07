<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => ['web'],
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
            require base_path('routes/Issue/issue.php');
            require base_path('routes/CryptoWallet/cryptoWallet.php');
            require base_path('routes/Wallet/wallet.php');
            require base_path('routes/Order/order.php');
            require base_path('routes/Cms/announcement.php');
            require base_path('routes/User/user.php');
            require base_path('routes/Log/log.php');
            require base_path('routes/OtcConfig/config.php');
            require base_path('routes/OtcAd/otcAd.php');
            require base_path('routes/OtcTicket/ticket.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
