<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Transaction ID Customization
    |--------------------------------------------------------------------------
    |
    | Define a custom prefix for transaction IDs. This prefix will be used
    | before the payment gateway and unique identifier.
    |
    */
    'transaction_prefix' => 'NTP', // Default prefix for all transactions

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    */
    'preferred_currency' => 'USD',
    'secondary_currency' => 'PHP',
    'conversion_rate' => 54.30,
    
    /*
    |--------------------------------------------------------------------------
    | Test Mode
    |--------------------------------------------------------------------------
    |
    | Enable test mode to use mock payment transactions instead of calling
    | real PayMongo or Xendit APIs.
    |
    | Options:
    | - true: Payments are simulated using TestService.
    | - false: Payments are processed via real gateways.
    |
    */
    'test_mode' => env('NTPAYMENTS_TEST_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway API Credentials
    |--------------------------------------------------------------------------
    |
    | The secret keys required for authenticating API requests to payment
    | gateways. Ensure that these keys are securely stored in your .env file.
    |
    */
    'xendit_secret'   => env('XENDIT_SECRET_KEY'),
    'paymongo_secret' => env('PAYMONGO_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API Base URLs
    |--------------------------------------------------------------------------
    |
    | The base API URLs for Xendit and PayMongo. These URLs are used to send
    | payment requests and may be updated as gateways evolve.
    |
    */
    'api_urls' => [
        'xendit'   => env('XENDIT_API_URL', 'https://api.xendit.co'),
        'paymongo' => env('PAYMONGO_API_URL', 'https://api.paymongo.com/v1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Security Keys
    |--------------------------------------------------------------------------
    |
    | Webhook secret keys are used to validate incoming webhook requests from
    | payment gateways. These should be set in the environment file to enhance
    | security and prevent unauthorized calls.
    |
    */
    'webhooks' => [
        'xendit'   => env('XENDIT_WEBHOOK_SECRET'),
        'paymongo' => env('PAYMONGO_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | The default payment gateway to be used for transactions. Developers can
    | override this setting by specifying a gateway dynamically.
    | Supported options: "xendit", "paymongo".
    |
    */
    'default_gateway' => env('PAYMENT_GATEWAY', 'xendit'),

    /*
    |--------------------------------------------------------------------------
    | Currency Configuration
    |--------------------------------------------------------------------------
    |
    | The default currency to be used for transactions. The available currency
    | options are based on the supported currencies of Xendit and PayMongo.
    | 
    | If a currency is not supported, the transaction may be declined.
    |
    */
    'currency' => env('PAYMENT_CURRENCY', 'USD'), // Default: USD

    /*
    |--------------------------------------------------------------------------
    | Supported Currencies
    |--------------------------------------------------------------------------
    |
    | This array defines the currencies supported by the payment gateways.
    | Xendit and PayMongo have specific currency limitations, and any
    | currency outside of this list may not be processed successfully.
    |
    | Reference:
    | - Xendit Supported Currencies: https://docs.xendit.co
    | - PayMongo Supported Currencies: https://developers.paymongo.com
    |
    */
    'allowed_currencies' => [
        'USD', 'PHP', 'IDR', 'SGD', // Xendit Supported Currencies
        'USD', 'PHP' // PayMongo Supported Currencies
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Enables or disables logging for API requests and responses. This is
    | useful for debugging and tracking transactions. The log channel is
    | defined in the Laravel logging configuration.
    |
    */
    'logging' => [
        'enabled' => env('NTPAYMENTS_LOGGING', false),
        'channel' => env('NTPAYMENTS_LOG_CHANNEL', 'stack'),
    ],
];
