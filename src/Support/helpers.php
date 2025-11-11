<?php

use SyedAqeeqAbbas\Axcessms\AxcessmsClient;

if (!function_exists('copyAndPay')) {
    /**
     * Shortcut helper for AxcessmsClient Copy & Pay integration.
     *
     * @return SyedAqeeqAbbas\Axcessms\Modules\CopyAndPay\Checkout
     */
    function copyAndPay()
    {
        return app(AxcessmsClient::class)->copyAndPay();
    }
}

if (!function_exists('serverToServer')) {
    /**
     * Shortcut helper for AxcessmsClient Server-to-Server integration.
     *
     * @return SyedAqeeqAbbas\Axcessms\Modules\ServerToServer\Payment
     */
    function serverToServer()
    {
        return app(AxcessmsClient::class)->serverToServer();
    }
}

if (!function_exists('scheduler')) {
    /**
     * Shortcut helper for AxcessmsClient Scheduler integration.
     *
     * @return SyedAqeeqAbbas\Axcessms\Modules\Scheduling\Scheduler
     */
    function scheduler()
    {
        return app(AxcessmsClient::class)->scheduler();
    }
}

if (!function_exists('canceller')) {
    /**
     * Shortcut helper for AxcessmsClient Canceller integration.
     *
     * @return SyedAqeeqAbbas\Axcessms\Modules\Scheduling\Canceller
     */
    function canceller()
    {
        return app(AxcessmsClient::class)->canceller();
    }
}

if (!function_exists('webhook')) {
    /**
     * Shortcut helper for AxcessmsClient Webhook handler.
     *
     * @return SyedAqeeqAbbas\Axcessms\Modules\Webhooks\WebhookHandler
     */
    function webhook()
    {
        return app(AxcessmsClient::class)->webhook();
    }
}
