<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Axcess Merchant Services Environment
    |--------------------------------------------------------------------------
    |
    | Defines which Axcess Merchant Services API environment the package will
    | communicate with.
    | Supported values: "sandbox" for testing or "production" for live
    | transactions. The default is "sandbox".
    |
    */

    'environment' => env('AXCESSMS_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Axcess Merchant Services Entity ID
    |--------------------------------------------------------------------------
    |
    | Your unique Entity ID provided by Axcess Merchant Services.
    | This identifier associates API requests and transactions
    | with your specific merchant account.
    |
    */

    'entity_id' => env('AXCESSMS_ENTITY_ID'),

    /*
    |--------------------------------------------------------------------------
    | Axcess Merchant Services Access Token
    |--------------------------------------------------------------------------
    |
    | The access token used to authenticate requests to the Axcess
    | Merchant Services API. Keep this value secure and never expose
    | it publicly.
    |
    */

    'access_token' => env('AXCESSMS_ACCESS_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Axcess Merchant Services Webhook Encryption Key
    |--------------------------------------------------------------------------
    |
    | The encryption key provided by Axcess Merchant Services used to
    | decrypt incoming webhook event payloads. This ensures that all
    | webhook notifications are securely verified and have not been
    | tampered with in transit.
    |
    */

    'encryption_key' => env('AXCESSMS_ENCRYPTION_KEY'),

];
