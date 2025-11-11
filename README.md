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

Publish the configuration:

```bash
php artisan vendor:publish --tag=axcessms
```

Add credentials in your `.env` file:

```bash
AXCESSMS_ENVIRONMENT=production // use sandbox for development or testing
AXCESSMS_ENTITY_ID=YOUR_ENTITY_ID
AXCESSMS_ACCESS_TOKEN=YOUR_ACCESS_TOKEN==
AXCESSMS_ENCRYPTION_KEY=YOUR_ENCRYPTION_KEY_FOR_WEBHOOK
```