<?php
// Copy to config.local.php (never commit it) and fill in. Overrides app/config.php.
return [
    'env' => 'staging',                 // 'production' hides drafts and opens robots.txt
    'show_markers' => false,            // true (staging only) shows the [CONFIRM] tags, AR-REVIEW underlines and staging bar for content review
    'base_url' => 'https://dwptech.com',
    'app_secret' => 'CHANGE-ME-to-a-long-random-string', // php -r 'echo bin2hex(random_bytes(32));'

    'db' => ['host' => '127.0.0.1', 'name' => 'dwp_arabia', 'user' => 'dwp_web', 'pass' => ''],

    'sales_inbox' => '',
    'mail_from' => '',
    'smtp' => ['host' => '', 'port' => 587, 'user' => '', 'pass' => '', 'secure' => 'tls'],
    'crm_webhook_url' => '',
    'crm_webhook_secret' => '',

    'turnstile_site_key' => '',
    'turnstile_secret_key' => '',
    'gtm_id' => '',

    'whatsapp' => '',
    'email' => '',
    'instagram' => '', 'facebook' => '', 'x' => '',
    'geo' => ['lat' => null, 'lng' => null],
    'legal_name' => 'DWP IT Arabia',
    'cr_number' => '',
    'certified_consultants' => '',
    'hrms_enabled' => false,
];
