<?php

namespace SyedAqeeqAbbas\Axcessms\Modules\Scheduling;

use Illuminate\Support\Facades\Http;
use SyedAqeeqAbbas\Axcessms\Config\AxcessmsConfig;
use SyedAqeeqAbbas\Axcessms\Core\StatusHandler;

/**
 * Class Scheduler
 *
 * Provides functionality for creating and managing **scheduled or recurring payments**
 * via the Axcess Merchant Services (AxcessMS) API.
 *
 * This module allows merchants to:
 *  - Create new scheduled payment instructions.
 *  - Cancel existing scheduled payments.
 *
 * @package SyedAqeeqAbbas\Axcessms\Modules\Scheduling
 */
class Scheduler
{
    /**
     * Create a new Scheduler module instance.
     *
     * @param AxcessmsConfig $config        The configuration instance containing API credentials.
     * @param StatusHandler  $statusHandler The handler used to validate API response codes.
     */
    public function __construct(
        protected AxcessmsConfig $config,
        protected StatusHandler $statusHandler
    ) {}

    /**
     * Create a scheduled or recurring payment.
     *
     * Sends a request to the AxcessMS Scheduling API to create a scheduled
     * transaction for a registered customer or card.
     *
     * The `entityId` and `paymentType` are automatically appended to the request payload.
     *
     * @param  array  $params  The parameters required for scheduling a payment.
     *                         Example keys:
     *                         - `amount` (float)
     *                         - `currency` (string)
     *                         - `registrationId` (string)
     *                         - `merchantTransactionId` (string)
     * @return array            Returns an associative array with:
     *                         - `status` (bool): Whether the operation was successful.
     *                         - `data` (array):  API response payload on success.
     *                         - `message` (string): Error message on failure.
     */
    public function schedule(array $params): array
    {
        $params = array_merge([
            'entityId'    => $this->config->getEntityId(),
            'paymentType' => 'DB',
        ], $params);

        try {
            $response = Http::asForm()
                        ->withHeaders([
                            'Authorization' => 'Bearer ' . $this->config->getAccessToken(),
                        ])
                        ->post($this->config->baseUrl() . '/scheduling/v1/schedules', $params);

            if ($response->successful() && $this->statusHandler->validate($response->json()['result']['code'])) {
                return [
                    'status' => true,
                    'data'   => $response->json(),
                ];
            }

            return [
                'status'  => false,
                'message' => $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel an existing scheduled payment.
     *
     * Sends a DELETE request to the AxcessMS Scheduling API to cancel
     * a previously created schedule using its unique schedule ID.
     *
     * @param  string  $scheduleId  The unique schedule identifier (UUID) provided by AxcessMS.
     * @return array                Returns an associative array with:
     *                              - `status` (bool): Whether the operation was successful.
     *                              - `data` (array):  API response payload on success.
     *                              - `message` (string): Error message on failure.
     */
    public function cancel(string $scheduleId): array
    {
        try {
            $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $this->config->getAccessToken(),
                        ])->delete($this->config->baseUrl() . "/scheduling/v1/schedules/{$scheduleId}?entityId={$this->config->getEntityId()}");

            if ($response->successful() && $this->statusHandler->validate($response->json()['result']['code'])) {
                return [
                    'status' => true,
                    'data'   => $response->json(),
                ];
            }

            return [
                'status'  => false,
                'message' => $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
