<?php

namespace SyedAqeeqAbbas\Axcessms;

use Illuminate\Support\ServiceProvider;
use SyedAqeeqAbbas\Axcessms\Config\AxcessmsConfig;

/**
 * Class AxcessmsServiceProvider
 *
 * Registers and boots the Axcess Merchant Services (AxcessMS) package
 * within a Laravel application.
 *
 * This service provider is responsible for:
 *  - Publishing the package configuration file.
 *  - Merging default configuration values.
 *  - Registering the `axcessms` singleton instance in the service container.
 *
 * Once registered, developers can interact with the API through:
 *   - `app('axcessms')` via dependency injection, or
 *   - `Axcessms` Facade for static access.
 *
 * @package SyedAqeeqAbbas\Axcessms
 */
class AxcessmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * Publishes the package's configuration file to the application's
     * `config/` directory so developers can override default settings.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/Config/axcessms.php' => config_path('axcessms.php'),
        ], 'axcessms');
    }

    /**
     * Register any application services.
     *
     * Merges the default configuration file and binds the AxcessmsClient
     * as a singleton within the Laravel service container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/axcessms.php',
            'axcessms'
        );

        if (file_exists(__DIR__ . '/Support/helpers.php'))
        {
            require_once __DIR__ . '/Support/helpers.php';
        }

        $this->app->singleton('axcessms', function () {
            $config = new AxcessmsConfig(
                config('axcessms.entity_id'),
                config('axcessms.access_token'),
                config('axcessms.environment'),
                config('axcessms.encryption_key')
            );

            return new AxcessmsClient($config);
        });

        $this->app->singleton(AxcessmsClient::class, function ($app) {

            $config = new AxcessmsConfig(
                config('axcessms.entity_id'),
                config('axcessms.access_token'),
                config('axcessms.environment'),
                config('axcessms.encryption_key')
            );
            
            return new AxcessmsClient($config);
        });
    }
}
