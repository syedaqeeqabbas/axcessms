<?php

namespace SyedAqeeqAbbas\Axcessms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Axcessms
 *
 * The Laravel Facade for accessing the Axcess Merchant Services (AxcessMS) client.
 * This provides a static, expressive interface to interact with the underlying
 * AxcessmsClient instance registered in the service container.
 *
 * @see \SyedAqeeqAbbas\Axcessms\AxcessmsClient
 * @package SyedAqeeqAbbas\Axcessms\Facades
 */
class Axcessms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * This value maps to the binding name registered in the
     * service container by AxcessmsServiceProvider.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'axcessms';
    }
}
