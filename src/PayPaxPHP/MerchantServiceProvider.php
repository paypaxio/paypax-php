<?php

namespace PayPaxPHP;

use Illuminate\Support\ServiceProvider;

/**
 * Class MerchantServiceProvider
 */
class MerchantServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('paypaxMerchant', Merchant::class);
    }
}