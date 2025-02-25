<?php

namespace AdamNatad\LaravelNTPayments\Services;

use AdamNatad\LaravelNTPayments\Helpers\PaymentHelper;
use Exception;

/**
 * Class TestService
 *
 * Provides a simulated environment for testing payment transactions
 * without connecting to real payment gateways.
 */
class TestService
{
    /**
     * Simulate a payment charge request.
     *
     * @param float $amount The amount to be charged.
     * @param string $currency The currency code (e.g., USD, PHP).
     * @param string $gateway The payment gateway name (mocked response).
     * @return array Mock response data.
     * @throws Exception If an unsupported gateway is provided.
     */
    public function charge(float $amount, string $currency, string $gateway): array
    {
        if (!PaymentHelper::isValidCurrency($currency)) {
            throw new Exception("Invalid currency: $currency.");
        }

        $transactionId = PaymentHelper::generateTransactionId(strtoupper($gateway));

        return [
            'status' => 'success',
            'message' => "Payment successfully processed on {$gateway} (Test Mode).",
            'transaction_id' => $transactionId,
            'amount' => PaymentHelper::formatCurrency($amount, $currency),
            'currency' => strtoupper($currency),
            'gateway' => strtoupper($gateway),
        ];
    }

    /**
     * Simulate retrieving payment details.
     *
     * @param string $transactionId The simulated transaction ID.
     * @return array Mock payment details.
     */
    public function getPaymentDetails(string $transactionId): array
    {
        return [
            'status' => 'success',
            'transaction_id' => $transactionId,
            'amount' => '100.00 PHP',
            'currency' => 'PHP',
            'gateway' => 'TEST',
            'message' => 'Mock transaction retrieved successfully.',
        ];
    }
}
