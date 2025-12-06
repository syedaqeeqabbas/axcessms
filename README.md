# AxcessMS Laravel SDK

A Laravel package for **Axcess Merchant Services** (AxcessMS) — enabling seamless integration with Copy & Pay, Server-to-Server, and Scheduling APIs.

## Features

- ✅ Modular architecture (`CopyAndPay`, `ServerToServer`, `Scheduling`, `Webhook`)
- 🔐 Secure webhook encryption support
- ⚙️ Sandbox & production environments
- 🧩 Service provider, config file & facade for Laravel
- 💳 Easy checkout, payment, and subscription scheduling APIs

## Installation

Use composer to manage your dependencies.

```bash
composer require syedaqeeqabbas/axcessms
```

Publish the configuration (Optional):

```bash
php artisan vendor:publish --tag=axcessms
```

Add credentials in your `.env` file:

```bash
AXCESSMS_ENVIRONMENT=production // use 'sandbox' for development or testing
AXCESSMS_ENTITY_ID=YOUR_ENTITY_ID
AXCESSMS_ACCESS_TOKEN=YOUR_ACCESS_TOKEN
AXCESSMS_ENCRYPTION_KEY=YOUR_ENCRYPTION_KEY_FOR_WEBHOOK
```

## Example Usage

### 1️⃣ Copy & Pay Checkout

This feature allows you to initialize a checkout session for either a single payment or a scheduled (recurring) payment.

Once the checkout session is created, the API response provides a unique `checkoutId`.

This `checkoutId` is then passed to the view, where it is used to render the Copy and Pay payment widget (e.g., for VISA, MasterCard, or other supported brands).

Facade vs Helper (Quick Reference)

Both styles resolve to the same underlying service.

```php
use Axcessms;

Axcessms::copyAndPay();   // Facade
copyAndPay();             // Helper

Axcessms::serverToServer();   // Facade
serverToServer();             // Helper
```

- ✅ Use Facade if you prefer explicit imports
- ✅ Use Helper for cleaner controllers & routes

#### For single or one time payment checkout:

```php
$checkout = copyAndPay()->singlePaymentCheckout([
	            'amount' => 19.99,
	            'currency' => 'GBP',
	            'merchantTransactionId' => rand(10000, 99999), // Replace it with real unique Order ID or Checkout ID
	        ]);

return view('checkout')->with(['checkoutId' => $checkout['id']]);
```

#### For schedule or recurring payment checkout:

```php
$checkout = copyAndPay()->schedulePaymentCheckout([
	            'amount' => 19.99,
	            'currency' => 'GBP',
	            'merchantTransactionId' => rand(10000, 99999), // Replace it with real unique Order ID or Checkout ID
	        ]); 

return view('checkout')->with(['checkoutId' => $checkout['id']]);
```

#### Frontend:

```html
<form action="/payment/result" class="paymentWidgets" data-brands="VISA MASTER AMEX"></form>

<script src="{{ Axcessms::config()->baseUrl() }}/v1/paymentWidgets.js?checkoutId={{ $checkoutId }}" crossorigin="anonymous"></script>

<script>
    var wpwlOptions = {
        billingAddress: {},
        mandatoryBillingFields:{}, 
        style: "card"
    }
</script>
```

#### Check Payment Status:

```php
// Inside of your Controller on /payment/result route

$response = copyAndPay()->status();

if ($response['status'])
{
	// Payment successfull
}
else
{
	// Payment failed
}
```

### 2️⃣ Server-to-Server

Use this mode when you want full control on your backend without using the Copy & Pay widget, or for operations like capturing, voiding, or refunding payments.

Exact parameters depend on your AxcessMS account and API documentation. The SDK sends them through as you provide.

#### Create Pre-authorize payment:

```php

$payment = serverToServer()->preAuthorizePayment([
    'amount'          => 92,
    'currency'        => 'GBP',
    'paymentBrand'    => 'VISA',
    'card.number'     => '4200000000000000',
    'card.holder'     => 'Jane Jones',
    'card.expiryMonth'=> '05',
    'card.expiryYear' => '2034',
    'card.cvv'        => '123',
    'customer.givenName'     => 'Jane',
    'customer.surname'     => 'Jones',
    'customer.email'     => 'info@example.com',
    'customer.phone'     => '0123456789',
    'customer.browser.acceptHeader' => '3D Secure v2',
    'customer.browser.language' => 'english',
    'customer.browser.screenHeight' => '700',
    'customer.browser.screenWidth' => '1028',
    'customer.browser.userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',
    'customer.browser.javaEnabled' => 'true',
]);
```

#### Perform debit payment:

```php

$payment = serverToServer()->debitPayment([
    'amount'          => 92,
    'currency'        => 'GBP',
    'paymentBrand'    => 'VISA',
    'card.number'     => '4200000000000000',
    'card.holder'     => 'Jane Jones',
    'card.expiryMonth'=> '05',
    'card.expiryYear' => '2034',
    'card.cvv'        => '123',
    'customer.givenName'     => 'Jane',
    'customer.surname'     => 'Jones',
    'customer.email'     => 'info@example.com',
    'customer.phone'     => '0123456789',
    'customer.ip' => '59.103.124.217',
]);
```

#### Manage the payment:

```php

$response = serverToServer()->status('PAYMENT_ID_FROM_AXCESSMS');

if ($response['status'])
{
	// Payment successfull
}
else
{
	// Payment failed
}
```

#### Manage bank receipt confirmations:

```php

$params = [
    'amount'   => 92,
    'currency' => 'GBP',
];

$response = serverToServer()->receipt($params, 'PAYMENT_ID_FROM_AXCESSMS');
```

#### Capture the payment:

```php

$params = [
    'amount'   => 92,
    'currency' => 'GBP',
];

$response = serverToServer()->capture($params, 'PAYMENT_ID_FROM_AXCESSMS');
```

#### Refund either the full captured amount or a part of the captured amount:

```php

$params = [
    'amount'   => 90.50,
    'currency' => 'GBP',
];

$response = serverToServer()->refund($params, 'PAYMENT_ID_FROM_AXCESSMS');
```

#### Rebill the processed order for additional products:

```php

$params = [
    'amount'   => 90.50,
    'currency' => 'GBP',
];

$response = serverToServer()->rebill($params, 'PAYMENT_ID_FROM_AXCESSMS');
```

#### Reflect the chargeback processed by the bank:

```php

$params = [
    'amount'   => 90.50,
    'currency' => 'GBP',
];

$response = serverToServer()->chargeBack($params, 'PAYMENT_ID_FROM_AXCESSMS');
```

#### Reflect the chargeback reversal processed by the bank:

```php

$params = [
    'amount'   => 90.50,
    'currency' => 'GBP',
];

$response = serverToServer()->chargeBackReversal($params, 'PAYMENT_ID_FROM_AXCESSMS');
```

### 3️⃣ Scheduling API

While `schedulePaymentCheckout()` creates a schedule via Copy & Pay, the Scheduling module is for managing schedules entirely from backend.

```php

use Axcessms;

$schedule = Axcessms::scheduler()->schedule([
    'amount' => 65,
    'currency' => 'GBP',
    'registrationId' => '8ac7a***********************',
    'job.startDate' => date('Y-m-d', strtotime('+1 days')) . ' 00:01:00',
    'job.dayOfWeek' => '2,3,4,5,6',
    'job.hour' => 18,
    'job.minute' => 10,
    'job.endDate' => date('Y-m-d', strtotime('+10 days')) . ' 00:01:00',
]);

// Handle $schedule['id'] etc.
```

#### Cancel a Schedule:

```php

use Axcessms;

$response = Axcessms::scheduler()->cancel('SCHEDULE_ID_FROM_AXCESSMS');
```

### 4️⃣ Webhooks

AxcessMS can send asynchronous notifications (e.g. payment result, chargebacks, schedule events) to your application.

This package provides a helper to decrypt and validate webhook payloads using `AXCESSMS_ENCRYPTION_KEY`.

The SDK automatically:
- Validates headers
- Decrypts payload (AES-256-GCM)
- Parses JSON
- Exposes clean payload data

#### Defining the Route:

In `routes/web.php` or `routes/api.php`:

```php

use App\Http\Controllers\AxcessmsWebhookController;

Route::post('/axcessms/webhook', [AxcessmsWebhookController::class, 'handle'])
    ->name('axcessms.webhook');
```

#### Controller Example:

```php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Axcessms;

class AxcessmsWebhookController extends Controller
{
    public function handle(Request $request)
    {
        return Axcessms::webhook()->handle($request, function ($payload) {
            // $payload is already decrypted & verified
            \Log::info('AxcessMS Webhook', $payload);
        });
    }
}
```