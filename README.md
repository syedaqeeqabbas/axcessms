# axcessms
Laravel package for seamless integration with Axcess Merchant Services Payment Gateway.

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
AXCESSMS_ACCESS_TOKEN=YOUR_ACCESS_TOKEN==
AXCESSMS_ENCRYPTION_KEY=YOUR_ENCRYPTION_KEY_FOR_WEBHOOK
```

## Example Usage

### 1️⃣ Copy & Pay Checkout

This feature allows you to initialize a checkout session for either a single payment or a scheduled (recurring) payment.

Once the checkout session is created, the API response provides a unique `checkoutId`.

This `checkoutId` is then passed to the view, where it is used to render the Copy and Pay payment widget (e.g., for VISA, MasterCard, or other supported brands).


```php
use Axcessms;

// For single or one time payment using Facade

$checkout = Axcessms::copyAndPay()->singlePaymentCheckout([
	            'amount' => 19.99,
	            'currency' => 'GBP',
	            'merchantTransactionId' => rand(10000, 99999), // Replace it with real unique Order ID or Checkout ID
	        ]); 

// For single or one time payment using helper

$checkout = copyAndPay()->singlePaymentCheckout([
	            'amount' => 19.99,
	            'currency' => 'GBP',
	            'merchantTransactionId' => rand(10000, 99999), // Replace it with real unique Order ID or Checkout ID
	        ]);

// For schedule or recurring payment using Facade

$checkout = Axcessms::copyAndPay()->schedulePaymentCheckout([
	            'amount' => 19.99,
	            'currency' => 'GBP',
	            'merchantTransactionId' => rand(10000, 99999), // Replace it with real unique Order ID or Checkout ID
	        ]); 

// For schedule or recurring payment using helper

$checkout = copyAndPay()->schedulePaymentCheckout([
	            'amount' => 19.99,
	            'currency' => 'GBP',
	            'merchantTransactionId' => rand(10000, 99999), // Replace it with real unique Order ID or Checkout ID
	        ]); 

return view('checkout')->with(['checkoutId' => $checkout['id']]);
```

Frontend:

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

Check Payment Status:

```php
// Inside of your Controller on /payment/result route

$response = Axcessms::copyAndPay()->status(request()->resourcePath);

if ($response['status'])
{
	// Payment successfull
}
else
{
	// Payment failed
}
```