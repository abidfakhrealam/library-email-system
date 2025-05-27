<?php

return [
    'default' => env('IMAP_CONNECTION', 'office365'),

    'connections' => [
        'office365' => [
            'host' => 'outlook.office365.com',
            'port' => 993,
            'encryption' => 'ssl',
            'validate_cert' => true,
            'username' => env('IMAP_USERNAME'),
            'password' => env('IMAP_PASSWORD'),
            'protocol' => 'imap'
        ],
        'gmail' => [
            'host' => 'imap.gmail.com',
            'port' => 993,
            'encryption' => 'ssl',
            'validate_cert' => true,
            'username' => env('GMAIL_IMAP_USERNAME'),
            'password' => env('GMAIL_IMAP_PASSWORD'),
            'protocol' => 'imap'
        ]
    ]
];
