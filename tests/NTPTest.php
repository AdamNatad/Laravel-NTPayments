<?php

namespace Tests;

use AdamNatad\LaravelNTPayments\Facades\NTPayments;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NTPTest extends TestCase
{   
    // Test use command php artisan test --filter NTPTest
    
    // Ensure the database is refreshed for each test (if using DB features)
    use RefreshDatabase;

    /**
     * Test if payment can be created in test mode
     *
     * @return void
     */
    public function testCreatePaymentInTestMode()
    {
        // Mock data for creating a payment
        $paymentData = [
            'amount' => 1000,
            'currency' => 'USD',
            'gateway' => 'xendit', // Set to 'paymongo' if you want to test PayMongo
            'reference_id' => 'ORDER-123',
            'email' => 'customer@example.com',
        ];

        // Create the payment
        $response = NTPayments::createPayment($paymentData);

        // Assert that the response is an array and contains expected keys
        $this->assertIsArray($response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('transaction_id', $response);
    }

    /**
     * Test if we can retrieve payment details
     *
     * @return void
     */
    public function testGetPaymentDetails()
    {
        // For this example, assume we are using a test mode transaction
        $transactionId = 'TEST-123456789'; // This would come from your mock service or real transaction ID

        // Retrieve the payment details
        $response = NTPayments::getPaymentDetails('test', $transactionId);

        // Assert that the response is an array and contains expected keys
        $this->assertIsArray($response);
        $this->assertArrayHasKey('transaction_id', $response);
        $this->assertArrayHasKey('amount', $response);
        $this->assertArrayHasKey('currency', $response);
    }

    /**
     * Test the payment status retrieval
     *
     * @return void
     */
    public function testGetPaymentStatus()
    {
        $transactionId = 'TEST-123456789'; // This would be a valid transaction ID

        // Retrieve payment status
        $status = NTPayments::getPaymentStatus($transactionId);

        // Assert the status is an array
        $this->assertIsArray($status);
        $this->assertArrayHasKey('status', $status);
    }
}
