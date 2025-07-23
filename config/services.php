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
'servers' => [
    'openvpn' => [
        'ip'            => env('OPENVPN_SERVER_IP'),           // Actual target (EC2)
        'controller_ip' => env('CONTROLLER_IP'), // Ubuntu box running Ansible
    ],
    'ftp' => [
        'ip'            => env('FTP_SERVER_IP'),
        'controller_ip' => env('CONTROLLER_IP'),
    ],
    'haproxy' => [
        'ip'            => env('HAPROXY_SERVER_IP'),
        'controller_ip' => env('CONTROLLER_IP'),
    ],
],
    'ubuntu' => [
        'key_path' => env('CONTROLLER_KEY_PATH'),
        'user'     => env('UBUNTU_USER', 'ubuntu'),
    ],

    'ec2' => [
        'key_path' => env('EC2_KEY_PATH'),
        'user'     => env('EC2_USER', 'ec2-user'),
    ],

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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
