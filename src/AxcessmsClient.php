<?php

namespace SyedAqeeqAbbas\Axcessms;

use SyedAqeeqAbbas\Axcessms\Config\AxcessmsConfig;
use SyedAqeeqAbbas\Axcessms\Core\StatusHandler;
use SyedAqeeqAbbas\Axcessms\Modules\CopyAndPay\Checkout;
use SyedAqeeqAbbas\Axcessms\Modules\ServerToServer\Payment;
use SyedAqeeqAbbas\Axcessms\Modules\Scheduling\Scheduler;
use SyedAqeeqAbbas\Axcessms\Modules\Scheduling\Canceller;
use SyedAqeeqAbbas\Axcessms\Modules\Webhooks\WebhookHandler;

/**
 * Class AxcessmsClient
 *
 * Acts as the primary interface for communicating with the
 * Axcess Merchant Services (AxcessMS) API.
 *
 * This class encapsulates the configuration, environment setup,
 * and provides access to all functional modules, including:
 *  - Copy & Pay Checkout
 *  - Scheduling (Scheduled Payments)
 *  - Cancelling Scheduled Payments
 *
 * @package SyedAqeeqAbbas\Axcessms
 */
class AxcessmsClient
{
    /**
     * The configuration object containing credentials and environment settings.
     *
     * @var AxcessmsConfig
     */
    protected AxcessmsConfig $config;

    /**
     * The handler responsible for validating AxcessMS response codes.
     *
     * @var StatusHandler
     */
    protected StatusHandler $statusHandler;

    /**
     * Create a new Axcessms client instance.
     *
     * @param AxcessmsConfig $config  The configuration instance for the API.
     */
    public function __construct(AxcessmsConfig $config)
    {
        $this->config = $config;
        $this->statusHandler = new StatusHandler($config);
    }

    /**
     * Get the configuration instance associated with this client.
     *
     * @return AxcessmsConfig
     */
    public function getConfig(): AxcessmsConfig
    {
        return $this->config;
    }

    /**
     * Get the StatusHandler instance for validating API response codes.
     *
     * @return StatusHandler
     */
    public function status(): StatusHandler
    {
        return $this->statusHandler;
    }

    /**
     * Access the Copy & Pay Checkout module.
     *
     * Used to create checkout sessions for one-time or recurring payments.
     *
     * @return Checkout
     */
    public function copyAndPay(): Checkout
    {
        return new Checkout($this->config, $this->statusHandler);
    }

    /**
     * Access the Server to Server Payment module.
     *
     * Used to process payments directly or verify completed transactions.
     *
     * @return Payment
     */
    public function serverToServer(): Payment
    {
        return new Payment($this->config, $this->statusHandler);
    }

    /**
     * Access the Scheduling module.
     *
     * Used to create and manage recurring or scheduled payments.
     *
     * @return Scheduler
     */
    public function scheduler(): Scheduler
    {
        return new Scheduler($this->config, $this->statusHandler);
    }

    /**
     * Access the Canceller module.
     *
     * Used to cancel existing scheduled payments.
     *
     * @return Canceller
     */
    public function canceller(): Canceller
    {
        return new Canceller($this->config, $this->statusHandler);
    }

    /**
     * Access the Webhook Handler module.
     *
     * Provides an interface for handling and decrypting incoming webhook
     * notifications from Axcess Merchant Services (AxcessMS).
     *
     * The handler automatically detects whether the payload is JSON-wrapped
     * or a raw encrypted body and uses AES-256-GCM decryption with the
     * configured encryption key to securely process webhook data.
     *
     * @return \SyedAqeeqAbbas\Axcessms\Modules\Webhooks\WebhookHandler
     */
    public function webhook(): WebhookHandler
    {
        return new WebhookHandler($this->config);
    }

}
