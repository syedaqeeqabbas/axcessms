<?php

namespace SyedAqeeqAbbas\Axcessms\Modules\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SyedAqeeqAbbas\Axcessms\Config\AxcessmsConfig;

/**
 * Class WebhookHandler
 *
 * Handles incoming webhook notifications from Axcess Merchant Services (AxcessMS).
 * This class supports both JSON-wrapped and raw encrypted payloads, decrypting them
 * using the AES-256-GCM algorithm as required by Axcess.
 *
 * Example usage:
 *
 * ```php
 * Route::post('/webhook/axcess', [WebhookController::class, 'handle']);
 * ```
 *
 * @package SyedAqeeqAbbas\Axcessms\Modules\Webhooks
 */
class WebhookHandler
{
    /**
     * The Axcess configuration instance.
     *
     * @var AxcessmsConfig
     */
    protected AxcessmsConfig $config;

    /**
     * Create a new WebhookHandler instance.
     *
     * @param AxcessmsConfig $config
     */
    public function __construct(AxcessmsConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Handle an incoming webhook from Axcess Merchant Services.
     *
     * This method automatically detects whether the webhook payload
     * is JSON-wrapped or raw and decrypts it using the configured
     * encryption key and AES-256-GCM.
     *
     * @param  Request  $request
     * @param  callable|null  $callback   Optional callback to handle the decrypted payload.
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, ?callable $callback = null)
    {
        try {
            $encryptedBody = $request->input('encryptedBody')
                ?: trim($request->getContent());

            if (empty($encryptedBody)) {
                return response()->json(['status' => 1, 'message' => 'Empty body received']);
            }

            $ivHeader  = $request->header('x_initialization_vector');
            $authTag   = $request->header('x_authentication_tag');

            if (!$ivHeader || !$authTag) {
                return response()->json(['status' => 1, 'message' => 'Missing encryption headers']);
            }

            $encryptionKey = $this->config->getEncryptionKey();

            if (empty($encryptionKey)) {
                return response()->json(['status' => 1, 'message' => 'Missing encryption key']);
            }

            $decrypted = sodium_crypto_aead_aes256gcm_decrypt(
                hex2bin($encryptedBody . $authTag),
                null,
                hex2bin($ivHeader),
                hex2bin($encryptionKey)
            );

            if (!$decrypted) {
                return response()->json(['status' => 1, 'message' => 'Decryption failed']);
            }

            $params = json_decode($decrypted, true);

            if (!isset($params['payload']) || empty($params['payload'])) {
                return response()->json(['status' => 1, 'message' => 'Invalid or empty payload']);
            }

            if ($callback) {
                call_user_func($callback, $params['payload']);
            }

            return response()->json(['status' => 1, 'message' => 'Webhook processed']);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 1,
                'message' => 'Error processing webhook',
            ]);
        }
    }
}
