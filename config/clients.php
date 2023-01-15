<?php

return [
    /*
     |--------------------------------------------------------------------------
     | All front-end client related configurations
     |--------------------------------------------------------------------------
     | Here are all Slack configurations (Just webhooks right now)
     |
     */
    'web' => [

        /*
        |--------------------------------------------------------------------------
        | Slack Incoming Webhooks
        |--------------------------------------------------------------------------
        | Listed here are all slack Incoming webhook integrated. We currently use
        | them with System Alert notifications.
        |
        */
        'url' => [
            'base' => env('FRONT_END_URL'),
            'reset-password' => env('FRONT_END_RESET_PASSWORD_URL'),
            'verify-email' => env('FRONT_END_VERIFY_EMAIL_URL')
        ],
    ]
];
