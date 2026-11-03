<?php

return [
    'notification_email' => env('SALES_NOTIFICATION_EMAIL', 'sales@dawnsolutions.com'),
    'from_email'         => env('SALES_FROM_EMAIL', 'no-reply@dawnsolutions.com'),

    'spam' => [
        // Reject quotes with more than N URLs in the details
        'max_urls_in_details' => 3,

        // Reject if same IP submits more than N quotes in 1 hour
        'max_per_ip_per_hour' => 3,

        // Common disposable domains to block
        'blocked_domains' => [
            'mailinator.com', 'guerrillamail.com', '10minutemail.com',
            'temp-mail.org', 'throwawaymail.com',
        ],
    ],
];