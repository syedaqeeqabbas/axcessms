<?php

namespace SyedAqeeqAbbas\Axcessms\Core;

use SyedAqeeqAbbas\Axcessms\Config\AxcessmsConfig;

/**
 * Class StatusHandler
 *
 * Handles and validates response status codes returned by Axcess Merchant Services.
 * This class determines whether a payment, checkout, or scheduling request was successful
 * based on the environment-specific result codes provided by the AxcessMS API.
 *
 * @package SyedAqeeqAbbas\Axcessms\Core
 */
class StatusHandler
{
    /**
     * Create a new StatusHandler instance.
     *
     * @param AxcessmsConfig $config The configuration instance containing environment details.
     */
    public function __construct(protected AxcessmsConfig $config) {}

    /**
     * Validate a given API response code.
     *
     * This method checks whether the provided result code indicates a successful
     * transaction, based on the Axcess Merchant Services documentation.  
     * 
     * - In **production**, only `"000.000.000"` is considered successful.  
     * - In **sandbox**, multiple codes are treated as valid to allow for test success scenarios.
     *
     * @param  string  $code   The result code returned by the AxcessMS API.
     * @return bool            Returns `true` if the code is valid (successful); otherwise `false`.
     */
    public function validate(string $code): bool
    {
        $validCodes = strtolower($this->config->getEnvironment()) === 'production'
            ? ['000.000.000']
            : [
                '000.000.000', '000.100.112', '000.100.110', '000.400.110',
                '000.000.100', '000.100.105', '000.100.106', '000.100.111',
                '000.300.000', '000.300.100', '000.300.101', '000.300.102',
                '000.300.103', '000.310.100', '000.310.101', '000.310.110',
                '000.400.120', '000.600.000',
            ];

        return in_array($code, $validCodes, true);
    }
}
