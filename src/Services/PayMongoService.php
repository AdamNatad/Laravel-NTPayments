<?php

namespace AdamNatad\LaravelNTPayments\Services;

use Illuminate\Support\Facades\Http;
use Exception;
use AdamNatad\LaravelNTPayments\Helpers\PaymentHelper;

/**
 * Class PayMongoService
 *
 * Handles API interactions with the PayMongo payment gateway, including transactions
 * such as charging a payment and retrieving payment details.
 */
class PayMongoService
{
    protected string $apiKey;
    protected string $apiUrl;

    /**
     * PayMongoService constructor.
     * Initializes API credentials and base URL from configuration.
     */
    public function __construct()
    {
        $this->apiKey = config('ntpayments.paymongo_secret', '');
        $this->apiUrl = config('ntpayments.api_urls.paymongo', 'https://api.paymongo.com/v1');
    }

    /**
     * Get the available payment methods supported by PayMongo.
     *
     * @return array List of supported payment methods.
     */
    public function getAvailableMethods(): array
    {
        return [
            'credit_card',
            'ewallet'
        ];
    }

    /**
     * Get the available currencies supported by PayMongo.
     *
     * @return array List of supported currencies.
     */
    public function getAvailableCurrencies(): array
    {
        return ['PHP']; // PayMongo only supports PHP
    }

    /**
     * Charge a payment through PayMongo.
     *
     * @param float $amount The amount to be charged.
     * @param string $currency The currency code (e.g., USD, PHP).
     * @param string $paymentType The payment type (credit_card, ewallet).
     * @return array Response data from PayMongo API.
     * @throws Exception If the request fails or currency is unsupported.
     */
    public function charge(float $amount, string $currency, string $paymentType = 'credit_card'): array
    {
        // Validate if currency is supported
        if (!in_array($currency, $this->getAvailableCurrencies())) {
            throw new Exception("Currency '$currency' is not supported by PayMongo.");
        }

        // Validate if payment type is supported
        if (!in_array($paymentType, $this->getAvailableMethods())) {
            throw new Exception("Payment method '$paymentType' is not supported by PayMongo.");
        }

        $formattedAmount = intval($amount * 100); // Convert amount to cents

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
            'Content-Type' => 'application/json',
        ])->post("{$this->apiUrl}/payment_intents", [
            'data' => [
                'attributes' => [
                    'amount' => $formattedAmount,
                    'currency' => strtoupper($currency),
                    'payment_method_allowed' => $this->getAvailableMethods(),
                    'capture_type' => 'automatic',
                ],
            ],
        ]);

        if ($response->failed()) {
            throw new Exception('PayMongo Charge Failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Retrieve payment details from PayMongo.
     *
     * @param string $paymentId The unique payment ID.
     * @return array Payment details from PayMongo.
     * @throws Exception If the request fails.
     */
    public function getPaymentDetails(string $paymentId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
        ])->get("{$this->apiUrl}/payments/{$paymentId}");

        if ($response->failed()) {
            throw new Exception('Failed to retrieve payment details: ' . $response->body());
        }

        return $response->json();
    }
}
