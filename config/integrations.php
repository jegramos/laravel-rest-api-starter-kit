<?php

return [
    /*
     |--------------------------------------------------------------------------
     | All Slack-related configurations
     |--------------------------------------------------------------------------
     | Here are all Slack configurations (Just webhooks right now)
     |
     */
    'slack' => [

        /*
        |--------------------------------------------------------------------------
        | Slack Incoming Webhooks
        |--------------------------------------------------------------------------
        | Listed here are all slack Incoming webhook integrated. We currently use
        | them with System Alert notifications.
        |
        */
        'webhooks' => [
            'dev-alerts' => env('SLACK_INCOMING_WEBHOOK_DEV_ALERTS')
        ]
    ]
];