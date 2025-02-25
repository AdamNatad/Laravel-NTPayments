<?php

namespace AdamNatad\LaravelNTPayments\Providers;

use Illuminate\Support\ServiceProvider;
use AdamNatad\LaravelNTPayments\NTPayments;

/**
 * Class NTPProvider
 *
 * Registers the LaravelNTPayments service, ensuring that the package
 * is correctly integrated within the Laravel application.
 */
class NTPProvider extends ServiceProvider
{
    /**
     * Register services within Laravel's service container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ntpayments', function () {
            return new NTPayments();
        });

        // Merge the package's config with the application's config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/ntpayments.php',
            'ntpayments'
        );
    }

    /**
     * Bootstrap any necessary configurations or files when the package is loaded.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/ntpayments.php' => config_path('ntpayments.php'),
        ], 'ntpayments-config');
    }
}
