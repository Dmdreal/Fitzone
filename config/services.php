<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'mpesa' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE', '174379'),
        'passkey' => env('MPESA_PASSKEY'),
        'cafe_shortcode' => env('MPESA_CAFE_SHORTCODE', env('MPESA_SHORTCODE', '174379')),
        'cafe_passkey' => env('MPESA_CAFE_PASSKEY', env('MPESA_PASSKEY')),
        'cafe_transaction_type' => env('MPESA_CAFE_TRANSACTION_TYPE', 'CustomerBuyGoodsOnline'),
        'env' => env('MPESA_ENV', 'sandbox'),
        'callback_url' => env('MPESA_CALLBACK_URL'),
    ], 

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'),
    ],

    'card' => [
        'provider' => env('CARD_PROVIDER', 'manual'),
        'public_key' => env('CARD_PUBLIC_KEY'),
        'secret_key' => env('CARD_SECRET_KEY'),
    ],

    'bank' => [
        'name' => env('BANK_NAME', 'Fitzone Gym'),
        'account_name' => env('BANK_ACCOUNT_NAME', 'Fitzone Gym'),
        'account_number' => env('BANK_ACCOUNT_NUMBER'),
        'paybill' => env('BANK_PAYBILL'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'firebase' => [
        'api_key' => env('FIREBASE_API_KEY', 'AIzaSyCYUQjlgerfGRyYpMMZbX0AOw3zosVGvbQ'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN', 'fitzonewebsite-a8b4b.firebaseapp.com'),
        'project_id' => env('FIREBASE_PROJECT_ID', 'fitzonewebsite-a8b4b'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'fitzonewebsite-a8b4b.firebasestorage.app'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', '812695645097'),
        'app_id' => env('FIREBASE_APP_ID', '1:812695645097:web:fdc98084386cc8e25af2b8'),
        'measurement_id' => env('FIREBASE_MEASUREMENT_ID', 'G-SKCBEJ1HMT'),
        'client_id' => env('FIREBASE_CLIENT_ID'),
    ],

];
