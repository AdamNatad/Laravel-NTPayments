<?php

namespace AdamNatad\LaravelNTPayments\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class NTPayments
 *
 * Provides a static interface for accessing the LaravelNTPayments service.
 *
 * @method static array charge(float $amount, string $currency, string $gateway, array $options = [])
 */
class NTPayments extends Facade
{
    /**
     * Get the registered name of the component in Laravel's service container.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'ntpayments';
    }
}
