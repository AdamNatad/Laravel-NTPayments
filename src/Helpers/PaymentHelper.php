<?php

namespace AdamNatad\LaravelNTPayments\Helpers;

use Exception;

/**
 * Class PaymentHelper
 *
 * Provides utility functions for payment processing, including currency validation,
 * formatting, unique transaction ID generation, and currency conversion.
 */
class PaymentHelper
{
    /**
     * Format an amount into a readable currency format.
     *
     * @param float $amount The amount to format.
     * @param string $currency The currency code (e.g., USD, PHP).
     * @return string Formatted currency string.
     */
    public static function formatCurrency(float $amount, string $currency = 'USD'): string
    {
        return number_format($amount, 2) . ' ' . strtoupper($currency);
    }

    /**
     * Validate if a given currency is supported.
     *
     * @param string $currency The currency code.
     * @return bool True if valid, false otherwise.
     */
    public static function isValidCurrency(string $currency): bool
    {
        $supportedCurrencies = array_merge(
            config('ntpayments.conversion_rates', []),
            [
                config('ntpayments.preferred_currency'),
                config('ntpayments.secondary_currency')
            ]
        );

        return in_array(strtoupper($currency), $supportedCurrencies);
    }

    /**
     * Generate a unique transaction ID.
     *
     * Format: {PREFIX}_{GATEWAY}_{UNIQUEID}_{TIMESTAMP}
     * Example: NTP_XENDIT_65AB2C7F12D_1719214023
     *
     * @param string $gateway The payment gateway identifier (e.g., Xendit, PayMongo).
     * @return string Unique transaction reference.
     */
    public static function generateTransactionId(string $gateway): string
    {
        $prefix = strtoupper(config('ntpayments.transaction_prefix', 'NTP'));
        $gateway = strtoupper($gateway);

        do {
            $uniqueId = strtoupper(bin2hex(random_bytes(5))); // Secure 10-character unique ID
            $timestamp = time(); // Current Unix timestamp
            $transactionId = "{$prefix}_{$gateway}_{$uniqueId}_{$timestamp}";
        } while (self::transactionIdExists($transactionId)); // Ensure uniqueness

        return $transactionId;
    }

    /**
     * Check if a transaction ID already exists in the database.
     *
     * @param string $transactionId The generated transaction ID.
     * @return bool True if exists, false otherwise.
     */
    private static function transactionIdExists(string $transactionId): bool
    {
        return \DB::table('transactions')->where('transaction_id', $transactionId)->exists();
    }

    /**
     * Convert an amount from the preferred currency to the secondary currency.
     *
     * @param float $amount The amount to be converted.
     * @return float The converted amount.
     * @throws Exception If no valid conversion rate is defined.
     */
    public static function convertCurrency(float $amount): float
    {
        $conversionRate = config('ntpayments.conversion_rate');

        if (!$conversionRate || $conversionRate <= 0) {
            throw new Exception("Invalid conversion rate. Please check your configuration.");
        }

        return $amount * $conversionRate;
    }
}
