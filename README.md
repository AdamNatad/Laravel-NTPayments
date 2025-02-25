# Laravel-NTPayments
[![Unit Tests](https://github.com/adamnatad/laravel-ntpayments/actions/workflows/run-tests.yml/badge.svg)](https://github.com/adamnatad/laravel-ntpayments/actions)
[![Latest Stable Version](https://poser.pugx.org/adamnatad/laravel-ntpayments/version)](https://packagist.org/packages/adamnatad/laravel-ntpayments)
[![Total Downloads](https://poser.pugx.org/adamnatad/laravel-ntpayments/d/total)](https://packagist.org/packages/adamnatad/laravel-ntpayments)
[![License](https://img.shields.io/github/license/adamnatad/laravel-ntpayments.svg)](LICENSE)

A **modular and scalable** payment gateway integration for Laravel, supporting **Xendit** and **PayMongo** with **automatic currency conversion**, **dynamic payment method validation** and **configurable transaction IDs**.

---

## **🚀 Features**
✔ **Supports Multiple Payment Gateways** → Xendit & PayMongo (More coming soon!)  
✔ **Automatic Currency Conversion** → Converts between preferred and secondary currencies.  
✔ **Dynamic Payment Method Validation** → Ensures only supported methods are used.  
✔ **Configurable Transaction ID Format** → `{PREFIX}_{GATEWAY}_{UNIQUEID}_{TIMESTAMP}`  
✔ **Programmatic Conversion Rate Override** → Developers can adjust conversion rates dynamically per request.  
✔ **Retrieve Unique Transaction IDs** → Ensures traceable and globally unique transaction tracking.  
✔ **Modular & Extensible** → Easily add new gateways without modifying core logic.  

---

## **📦 Installation**
Install the package via Composer:
```sh
composer require adamnatad/laravel-ntpayments
```

Then, publish the configuration file:
```sh
php artisan vendor:publish --tag=ntpayments-config
```

This will create:
```sh
config/ntpayments.php
```

---

## **⚙️ Configuration**
### **1️⃣ Set API Credentials**
Update your `.env` file with your payment gateway credentials:
```ini
XENDIT_SECRET_KEY=your_xendit_api_key
PAYMONGO_SECRET_KEY=your_paymongo_api_key
```

### **2️⃣ Define Default Payment Settings**
Modify `config/ntpayments.php` to set:
```php
return [
    'default_gateway' => env('PAYMENT_GATEWAY', 'xendit'),

    'preferred_currency' => 'USD',
    'secondary_currency' => 'PHP',
    'conversion_rate' => 54.30,

    'transaction_prefix' => 'NTP', // Custom transaction prefix
];
```

---

## **🛠 Usage**
### **✅ Processing a Payment**
```php
use AdamNatad\LaravelNTPayments\Facades\NTPayments;

$response = NTPayments::createPayment([
    'amount' => 1000,
    'currency' => 'USD',
    'gateway' => 'xendit',
    'payment_type' => 'credit_card',
]);

dd($response);
```

### **✅ Fetch Available Payment Methods**
```php
$methods = NTPayments::getMethods('xendit');
dd($methods);
```

### **✅ Get Available Currencies**
```php
$currencies = NTPayments::getCurrencies('paymongo');
dd($currencies);
```

### **✅ Retrieve Conversion Rate**
```php
$rate = NTPayments::getConversionRate();
dd($rate);
```

### **✅ Override Conversion Rate (For Current Request)**
```php
$ntp = new NTPayments();
$ntp->setConversionRate(55.00);
$rate = $ntp->getConversionRate();
dd($rate);
```

### **✅ Generate a Unique Transaction ID**

The transaction ID is generated using the following format:
```
{PREFIX}_{GATEWAY}_{UNIQUEID}_{TIMESTAMP}
```
- **PREFIX** → Defined in `config/ntpayments.php` (`transaction_prefix`, default: `NTP`)
- **GATEWAY** → The selected payment gateway (e.g., `XENDIT`, `PAYMONGO`)
- **UNIQUEID** → A secure 10-character unique identifier generated via `bin2hex(random_bytes(5))`
- **TIMESTAMP** → The Unix timestamp at the time of transaction creation
```php
$transactionId = NTPayments::generateTransactionId('xendit');
dd($transactionId);
```
**Example Output:**
```sh
NTP_XENDIT_65AB2C7F12D_1719214023
```

### **✅ Charge a Payment**
```php
$chargeResponse = NTPayments::charge(1000, 'USD', 'credit_card', 'xendit');
dd($chargeResponse);
```

### **✅ Retrieve Payment Details**

> **Note:** The payment ID used here (`invoice_12345`) is the unique invoice ID **returned by Xendit** when processing a payment. This is **not** the same as the internally generated transaction ID (`NTP_XENDIT_65AB2C7F12D_1719214023`). When retrieving payment details, always use the invoice ID provided by Xendit.
```php
$paymentDetails = NTPayments::getPaymentDetails('xendit', 'invoice_12345');
dd($paymentDetails);
```

### **✅ Retrieve Payment Status**

> **Note:** Similar to retrieving payment details, the payment ID used here (`invoice_12345`) is the unique invoice ID **returned by Xendit** when processing a payment. This is **not** the same as the internally generated transaction ID (`NTP_XENDIT_65AB2C7F12D_1719214023`). When retrieving payment status, always use the invoice ID provided by Xendit.
```php
$paymentStatus = NTPayments::getPaymentStatus('xendit', 'invoice_12345');
dd($paymentStatus);
```

---

## **📜 License**
This package is open-source and licensed under the **MIT License**.

---

## **📞 Support & Contributions**
Pull requests and feature suggestions are welcome! If you encounter any issues, feel free to open an issue on GitHub.
