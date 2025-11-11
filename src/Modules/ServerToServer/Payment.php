<?php

namespace SyedAqeeqAbbas\Axcessms\Modules\ServerToServer;

use Illuminate\Support\Facades\Http;
use SyedAqeeqAbbas\Axcessms\Config\AxcessmsConfig;
use SyedAqeeqAbbas\Axcessms\Core\StatusHandler;

/**
 * Class Payment
 *
 * Handles all **Server-to-Server (S2S)** payment operations for the
 * Axcess Merchant Services (AxcessMS) API.
 *
 * This module supports:
 *  - Creating new payments
 *  - Checking payment status
 *  - Performing pre-authorizations, debits, captures, refunds, etc.
 *
 * @package SyedAqeeqAbbas\Axcessms\Modules\ServerToServer
 */
class Payment
{
    /**
     * Create a new Payment instance.
     *
     * @param AxcessmsConfig $config        The configuration instance containing API credentials.
     * @param StatusHandler  $statusHandler The status handler for validating API result codes.
     */
    public function __construct(
        protected AxcessmsConfig $config,
        protected StatusHandler $statusHandler
    ) {}

    /**
     * Retrieve the status of a specific payment.
     *
     * @param  string  $paymentId  The unique identifier of the payment.
     * @return array               The API response containing payment details or error message.
     */
    public function status(string $paymentId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config->getAccessToken(),
            ])->get(
                $this->config->baseUrl() . "/v1/payments/{$paymentId}",
                ['entityId' => $this->config->getEntityId()]
            );

            if ($response->successful() && $this->statusHandler->validate($response->json()['result']['code'])) {
                return ['status' => true, 'data' => $response->json()];
            }

            return ['status' => false, 'message' => $response->json()];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Create a new payment.
     *
     * This is the base method used by all other payment operations (debit, pre-auth, etc.).
     *
     * @param  array  $params  The payment parameters (amount, currency, card details, etc.).
     * @return array           The API response containing transaction details or error message.
     */
    public function create(array $params): array
    {
        $params = array_merge(['entityId' => $this->config->getEntityId()], $params);

        try {
            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->config->getAccessToken(),
                ])
                ->post($this->config->baseUrl() . '/v1/payments', $params);

            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json()];
            }

            return ['status' => false, 'message' => $response->json()];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Perform a secondary payment action (e.g., capture, refund, rebill).
     *
     * @param  array   $params     Additional payment parameters.
     * @param  string  $paymentId  The original payment identifier.
     * @return array               The API response or error details.
     */
    public function payment(array $params, string $paymentId): array
    {
        try {
            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->config->getAccessToken(),
                ])
                ->post(
                    $this->config->baseUrl() . "/v1/payments/{$paymentId}",
                    array_merge(['entityId' => $this->config->getEntityId()], $params)
                );

            if ($response->successful() && $this->statusHandler->validate($response->json()['result']['code'])) {
                return ['status' => true, 'data' => $response->json()];
            }

            return ['status' => false, 'message' => $response->json()];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Pre-authorize a payment.
     * Reserves the funds without charging the customer immediately.
     *
     * @param  array  $params
     * @return array
     */
    public function preAuthorizePayment(array $params): array
    {
        return $this->create(array_merge(['paymentType' => 'PA'], $params));
    }

    /**
     * Perform a direct debit (immediate charge) payment.
     *
     * @param  array  $params
     * @return array
     */
    public function debitPayment(array $params): array
    {
        return $this->create(array_merge(['paymentType' => 'DB'], $params));
    }

    /**
     * Retrieve the receipt for a completed payment.
     *
     * @param  array   $params
     * @param  string  $paymentId
     * @return array
     */
    public function receipt(array $params, string $paymentId): array
    {
        return $this->payment(array_merge(['paymentType' => 'RC'], $params), $paymentId);
    }

    /**
     * Capture a pre-authorized payment.
     *
     * @param  array   $params
     * @param  string  $paymentId
     * @return array
     */
    public function capture(array $params, string $paymentId): array
    {
        return $this->payment(array_merge(['paymentType' => 'CP'], $params), $paymentId);
    }

    /**
     * Refund a completed payment.
     *
     * @param  array   $params
     * @param  string  $paymentId
     * @return array
     */
    public function refund(array $params, string $paymentId): array
    {
        return $this->payment(array_merge(['paymentType' => 'RF'], $params), $paymentId);
    }

    /**
     * Rebill a previously authorized payment.
     * Useful for subscription or stored-payment scenarios.
     *
     * @param  array   $params
     * @param  string  $paymentId
     * @return array
     */
    public function rebill(array $params, string $paymentId): array
    {
        return $this->payment(array_merge(['paymentType' => 'RB'], $params), $paymentId);
    }

    /**
     * Record a chargeback against a payment.
     * Used to report a reversal initiated by the issuing bank.
     *
     * @param  array   $params
     * @param  string  $paymentId
     * @return array
     */
    public function chargeBack(array $params, string $paymentId): array
    {
        return $this->payment(array_merge(['paymentType' => 'CB'], $params), $paymentId);
    }

    /**
     * Reverse a previously reported chargeback.
     *
     * @param  array   $params
     * @param  string  $paymentId
     * @return array
     */
    public function chargeBackReversal(array $params, string $paymentId): array
    {
        return $this->payment(array_merge(['paymentType' => 'CR'], $params), $paymentId);
    }
}
