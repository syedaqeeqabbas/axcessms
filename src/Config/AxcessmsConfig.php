<?php

namespace SyedAqeeqAbbas\Axcessms\Config;

/**
 * Class AxcessmsConfig
 *
 * Handles all configuration values for the Axcess Merchant Services integration,
 * including environment, authentication credentials, and base URL resolution.
 *
 * @package SyedAqeeqAbbas\Axcessms\Config
 */
class AxcessmsConfig
{
    /**
     * Create a new Axcessms configuration instance.
     *
     * @param string $entityId       The unique Entity ID provided by Axcess Merchant Services.
     * @param string $accessToken    The API access token used for authentication.
     * @param string $environment    The environment name — either 'sandbox' or 'production'.
     * @param string $encryptionKey  The optional encryption key for verifying/decrypting webhooks.
     */
    public function __construct(
        protected string $entityId,
        protected string $accessToken,
        protected string $environment,
        protected string $encryptionKey = ''
    ) {}

    /**
     * Get the Entity ID used for identifying the merchant account.
     *
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * Get the access token used to authorize API requests.
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Get the current environment setting.
     *
     * Possible values: `sandbox` or `production`.
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Get the encryption key used to verify webhook payloads.
     *
     * @return string
     */
    public function getEncryptionKey(): string
    {
        return $this->encryptionKey;
    }

    /**
     * Get the base API URL depending on the selected environment.
     *
     * - Returns `https://eu-test.oppwa.com` for sandbox.
     * - Returns `https://eu-prod.oppwa.com` for production.
     *
     * @return string
     */
    public function baseUrl(): string
    {
        return strtolower($this->environment) === 'production'
            ? 'https://eu-prod.oppwa.com'
            : 'https://eu-test.oppwa.com';
    }
}
