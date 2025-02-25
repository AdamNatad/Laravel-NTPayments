<?php

namespace AdamNatad\LaravelNTPayments\Services;

use Illuminate\Support\Facades\Http;
use Exception;
use AdamNatad\LaravelNTPayments\Helpers\PaymentHelper;

/**
 * Class XenditService
 *
 * Handles API interactions with the Xendit payment gateway, enabling transactions
 * such as charging a payment and retrieving payment details.
 */
class XenditService
{
    protected string $apiKey;
    protected string $apiUrl;

    /**
     * XenditService constructor.
     * Initializes API credentials and base URL from configuration.
     */
    public function __construct()
    {
        $this->apiKey = config('ntpayments.xendit_secret', '');
        $this->apiUrl = config('ntpayments.api_urls.xendit', 'https://api.xendit.co');
    }

    /**
     * Get the available payment methods supported by Xendit.
     *
     * @return array List of supported payment methods.
     */
    public function getAvailableMethods(): array
    {
        return [
            'credit_card',
            'debit_card',
            'ewallet',
            'bank_transfer'
        ];
    }

    /**
     * Get the available currencies supported by Xendit.
     *
     * @return array List of supported currencies.
     */
    public function getAvailableCurrencies(): array
    {
        return ['USD', 'PHP', 'IDR'];
    }

    /**
     * Charge a payment through Xendit.
     *
     * @param float $amount The amount to be charged.
     * @param string $currency The currency code (e.g., USD, PHP, IDR).
     * @param string $paymentType The payment type (credit_card, debit_card, ewallet, bank_transfer).
     * @return array Response data from Xendit API.
     * @throws Exception If the request fails or currency/method is unsupported.
     */
    public function charge(float $amount, string $currency, string $paymentType): array
    {
        // Validate if currency is supported
        if (!in_array($currency, $this->getAvailableCurrencies())) {
            throw new Exception("Currency '$currency' is not supported by Xendit.");
        }

        // Validate if payment type is supported
        if (!in_array($paymentType, $this->getAvailableMethods())) {
            throw new Exception("Payment method '$paymentType' is not supported by Xendit.");
        }

        $externalId = PaymentHelper::generateTransactionId('XENDIT');

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
            'Content-Type' => 'application/json',
        ])->post("{$this->apiUrl}/v2/invoices", [
            'external_id' => $externalId,
            'amount' => intval($amount),
            'currency' => strtoupper($currency),
            'payer_email' => 'customer@example.com',
            'description' => "Payment for order {$externalId}",
        ]);

        if ($response->failed()) {
            throw new Exception('Xendit Charge Failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Retrieve payment details from Xendit.
     *
     * @param string $invoiceId The unique invoice ID.
     * @return array Payment details from Xendit.
     * @throws Exception If the request fails.
     */
    public function getPaymentDetails(string $invoiceId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
        ])->get("{$this->apiUrl}/v2/invoices/{$invoiceId}");

        if ($response->failed()) {
            throw new Exception('Failed to retrieve payment details: ' . $response->body());
        }

        return $response->json();
    }
}
