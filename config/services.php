<?php

return [
    'erp' => [
        'url' => env('ERP_URL', 'https://erp.i-deb.com.mx/home'),
    ],

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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    // Carpetas de cursos en Google Drive (App\Services\DriveCarpetas).
    // Basta una de las dos formas de acceso: cuenta de servicio (archivo JSON) u OAuth con refresh token.
    'google_drive' => [
        'service_account' => env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON') ? base_path(env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON')) : null,
        'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
        // Respaldo si no se captura la URL de la carpeta base en Configuraciones
        'carpeta_id' => env('GOOGLE_DRIVE_FOLDER_ID'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
