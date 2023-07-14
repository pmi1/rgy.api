<?php

namespace App\Providers;

use App\Order;
use App\OrderPayment;
use App\User;
use App\UserCashback;
use App\Observers\UserObserver;
use App\Observers\OrderPaymentObserver;
use App\Observers\UserCashbackObserver;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        UserCashback::observe(UserCashbackObserver::class);
        Order::observe(OrderObserver::class);
        User::observe(UserObserver::class);
        OrderPayment::observe(OrderPaymentObserver::class);

        $siteId = 1;


        if (isset($_SERVER['HTTP_REFERER'])) {

            $site = DB::table('cmf_site')
                    ->whereRaw('status and UPPER(system_name) = ?', mb_strtoupper(explode('.', parse_url($_SERVER['HTTP_REFERER'])['host'])[0]))
                    ->first();

            if (!$site) {

                $site = DB::table('cmf_site')->where('status', 1)->where('is_default', 1)->first();
            }

            if ($site) {

                $siteId = $site->cmf_site_id;
            }
        }

        config(['common.siteId' => $siteId]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
