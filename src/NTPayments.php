<?php

namespace AdamNatad\LaravelNTPayments;

use AdamNatad\LaravelNTPayments\Services\PayMongoService;
use AdamNatad\LaravelNTPayments\Services\XenditService;
use AdamNatad\LaravelNTPayments\Services\TestService;
use AdamNatad\LaravelNTPayments\Helpers\PaymentHelper;
use Exception;

/**
 * Class NTPayments
 *
 * Handles real and test mode transactions, including payment processing,
 * currency validation, and conversion handling.
 */
class NTPayments
{
    protected PayMongoService $payMongoService;
    protected XenditService $xenditService;
    protected TestService $testService;
    protected ?float $customConversionRate = null;
    protected bool $testMode;

    /**
     * NTPayments constructor.
     * Initializes payment services and checks test mode.
     */
    public function __construct()
    {
        $this->testMode = $this->loadTestMode();
        $this->payMongoService = new PayMongoService();
        $this->xenditService = new XenditService();
        $this->testService = new TestService();
    }

    /**
     * Load test mode setting from configuration.
     *
     * @return bool True if test mode is enabled, false otherwise.
     */
    private function loadTestMode(): bool
    {
        return function_exists('config') ? config('ntpayments.test_mode', false) : false;
    }

    /**
     * Create a payment through the selected gateway with automatic currency validation and conversion.
     *
     * @param array $paymentData The payment details.
     * @return array Payment response.
     * @throws Exception If validation fails or the gateway is unsupported.
     */
    public function createPayment(array $paymentData): array
    {
        $amount = $paymentData['amount'] ?? 0;
        $currency = $paymentData['currency'] ?? config('ntpayments.preferred_currency');
        $gateway = $paymentData['gateway'] ?? config('ntpayments.default_gateway', 'xendit');
        $paymentType = $paymentData['payment_type'] ?? 'credit_card';

        // Validate currency against supported currencies
        $availableCurrencies = $this->getCurrencies($gateway);
        if (!in_array($currency, $availableCurrencies)) {
            $secondaryCurrency = config('ntpayments.secondary_currency');
            if (!in_array($secondaryCurrency, $availableCurrencies)) {
                throw new Exception("Neither {$currency} nor {$secondaryCurrency} is supported by {$gateway}.");
            }
            // Convert amount to secondary currency
            $convertedAmount = PaymentHelper::convertCurrency($amount);
            $currency = $secondaryCurrency;
            $amount = $convertedAmount;
        }

        // Validate payment method
        $availableMethods = $this->getMethods($gateway);
        if (!in_array($paymentType, $availableMethods)) {
            throw new Exception("Payment method '$paymentType' is not supported by $gateway.");
        }

        return match ($gateway) {
            'xendit' => $this->xenditService->charge($amount, $currency, $paymentType),
            'paymongo' => $this->payMongoService->charge($amount, $currency, $paymentType),
            default => throw new Exception("Unsupported gateway: $gateway"),
        };
    }

    /**
     * Retrieve available payment methods for a specific gateway.
     *
     * @param string $gateway The payment gateway.
     * @return array List of supported payment methods.
     */
    public function getMethods(string $gateway): array
    {
        return match ($gateway) {
            'xendit' => $this->xenditService->getAvailableMethods(),
            'paymongo' => $this->payMongoService->getAvailableMethods(),
            default => throw new Exception("Unsupported gateway: $gateway"),
        };
    }

    /**
     * Retrieve available currencies for a specific gateway.
     *
     * @param string $gateway The payment gateway.
     * @return array List of supported currencies.
     */
    public function getCurrencies(string $gateway): array
    {
        return match ($gateway) {
            'xendit' => $this->xenditService->getAvailableCurrencies(),
            'paymongo' => $this->payMongoService->getAvailableCurrencies(),
            default => throw new Exception("Unsupported gateway: $gateway"),
        };
    }

    /**
     * Get the current conversion rate.
     *
     * @return array The preferred currency, secondary currency, and rate.
     */
    public function getConversionRate(): array
    {
        return [
            'from' => config('ntpayments.preferred_currency'),
            'to' => config('ntpayments.secondary_currency'),
            'rate' => $this->customConversionRate ?? config('ntpayments.conversion_rate'),
        ];
    }

    /**
     * Set a custom conversion rate programmatically for the current request only.
     *
     * @param float $rate The new conversion rate.
     * @throws Exception If the rate is invalid.
     */
    public function setConversionRate(float $rate): void
    {
        if ($rate <= 0) {
            throw new Exception("Conversion rate must be a positive number.");
        }
        $this->customConversionRate = $rate;
    }
}
