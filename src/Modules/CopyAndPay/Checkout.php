<?php

namespace SyedAqeeqAbbas\Axcessms\Modules\CopyAndPay;

use Illuminate\Support\Facades\Http;
use SyedAqeeqAbbas\Axcessms\Config\AxcessmsConfig;
use SyedAqeeqAbbas\Axcessms\Core\StatusHandler;

/**
 * Class Checkout
 *
 * Handles the creation and management of **Copy & Pay** checkout sessions
 * using the Axcess Merchant Services (AxcessMS) API.
 *
 * This class provides methods for:
 *  - Creating new checkout sessions (single or recurring)
 *  - Checking the status of existing checkouts
 *
 * @package SyedAqeeqAbbas\Axcessms\Modules\CopyAndPay
 */
class Checkout
{
    /**
     * Create a new Checkout module instance.
     *
     * @param AxcessmsConfig $config        The configuration instance containing API credentials.
     * @param StatusHandler  $statusHandler The handler for validating API response codes.
     */
    public function __construct(
        protected AxcessmsConfig $config,
        protected StatusHandler $statusHandler
    ) {
    }

    /**
     * Retrieve the status of a checkout session.
     *
     * This method checks whether a given checkout or payment resource
     * was successfully processed or is still pending.
     *
     * @param  string  $resourcePath  The resource path returned by AxcessMS (e.g., `/v1/checkouts/{id}/payment`).
     * @return array                  Returns an associative array containing:
     *                                - `status` (bool): Whether the request was successful.
     *                                - `data` (array):  The response data if successful.
     *                                - `message` (string): Error message if failed.
     */
    public function status(string $resourcePath): array
    {
        try {
            $response = Http::get($this->config->baseUrl() . $resourcePath);

            if ($response->successful() && $this->statusHandler->validate($response->json()['result']['code'])) {
                return [
                    'status' => true,
                    'data'   => $response->json(),
                ];
            }

            return [
                'status'  => false,
                'message' => $response->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a new checkout session.
     *
     * This is the core method used to initiate Copy & Pay transactions.
     * You can customize parameters such as `amount`, `currency`, and `paymentType`.
     *
     * @param  array  $params  The parameters required to create a checkout session.
     * @return array           Returns an associative array containing:
     *                         - `status` (bool): Whether creation was successful.
     *                         - `data` (array):  Checkout response data.
     *                         - `message` (string): Error message if failed.
     */
    public function create(array $params): array
    {
        $params = array_merge([
            'entityId' => $this->config->getEntityId(),
        ], $params);

        try {
            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->config->getAccessToken(),
                ])
                ->post($this->config->baseUrl() . '/v1/checkouts', $params);

            if ($response->successful()) {
                return [
                    'status' => true,
                    'data'   => $response->json(),
                ];
            }

            return [
                'status'  => false,
                'message' => $response->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a single payment checkout session.
     *
     * Used for one-time transactions that charge the card immediately.
     * Equivalent to setting `paymentType = DB`.
     *
     * @param  array  $params  The parameters for the transaction (amount, currency, etc.).
     * @return array           Returns the API response structure.
     */
    public function singlePaymentCheckout(array $params): array
    {
        $params = array_merge(['paymentType' => 'DB'], $params);

        return $this->create($params);
    }

    /**
     * Create a recurring or scheduled payment checkout session.
     *
     * This initializes a registration-based transaction for recurring billing.
     * The amount is typically set to 0, and `recurringType` is set to `INITIAL`.
     *
     * @param  array  $params  The parameters for setting up a recurring payment session.
     * @return array           Returns the API response structure.
     */
    public function schedulePaymentCheckout(array $params): array
    {
        $params = array_merge([
            'paymentType'       => 'PA',
            'recurringType'     => 'INITIAL',
            'createRegistration'=> 'true',
        ], $params);

        $params['amount'] = 0;

        return $this->create($params);
    }
}
