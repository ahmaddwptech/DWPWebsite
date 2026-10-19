<?php
/**
 * Site configuration.
 * Override any value in config.local.php (not committed) or with environment variables.
 * Items marked CONFIRM must be supplied by DWP before launch (see CONFIRM.md).
 */

$config = [
    // 'staging' shows [CONFIRM] / [AR-REVIEW] markers and draft articles; 'production' hides them.
    'env'        => 'staging',
    'base_url'   => 'https://dwptech.com',   // scheme + host, no trailing slash
    'base_path'  => '/arabia',               // site is served from dwptech.com/arabia/
    'app_secret' => '',                      // long random string; signs form tokens (set in config.local.php)

    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'dwp_arabia',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    // Lead delivery
    'sales_inbox'   => '',      // CONFIRM: sales inbox, e.g. sales@...
    'careers_inbox' => '',      // optional; CVs go to sales_inbox if empty
    'mail_from'     => '',      // CONFIRM: sender address on dwptech.com
    'smtp' => [                 // leave host empty to use PHP mail()
        'host' => '', 'port' => 587, 'user' => '', 'pass' => '', 'secure' => 'tls',
    ],
    'crm_webhook_url'    => '', // CONFIRM: CRM + webhook URL
    'crm_webhook_secret' => '', // optional, sent as X-Webhook-Secret


    // Spam protection (Cloudflare Turnstile). Leave empty to rely on honeypot + time trap + rate limit.
    'turnstile_site_key'   => '',
    'turnstile_secret_key' => '',

    // Tracking (loaded through GTM only after cookie consent)
    'gtm_id' => '',             // CONFIRM: GTM-XXXXXXX

    // Contact facts
    'phone'         => '+966 11 254 6591',
    'phone_e164'    => '+966112546591',
    'whatsapp'      => '',      // CONFIRM: digits only, e.g. 9665XXXXXXXX
    'email'         => '',      // CONFIRM: e.g. info@...
    'linkedin'      => 'https://www.linkedin.com/company/dwp-arabia',
    'instagram'     => '',      // CONFIRM
    'facebook'      => '',      // CONFIRM
    'x'             => '',      // CONFIRM
    'geo'           => ['lat' => null, 'lng' => null], // CONFIRM: Hala Center coordinates
    'legal_name'    => 'DWP IT Arabia', // CONFIRM: legal entity name
    'cr_number'     => '',      // CONFIRM: CR number to show in footer
    'certified_consultants' => '', // CONFIRM: e.g. 4

    // Optional pages
    'hrms_enabled'  => false,   // CONFIRM: build or drop the HRMS page

    // Admin area (/arabia/admin/)
    'admin_enabled' => true,

    'uploads_dir' => __DIR__ . '/../storage/uploads',
    'log_dir'     => __DIR__ . '/../storage/logs',
];

$local = __DIR__ . '/../config.local.php';
if (is_file($local)) {
    $config = array_replace_recursive($config, require $local);
}

foreach (['DB_HOST' => ['db', 'host'], 'DB_NAME' => ['db', 'name'], 'DB_USER' => ['db', 'user'], 'DB_PASS' => ['db', 'pass'], 'APP_ENV' => ['env']] as $var => $path) {
    $v = getenv($var);
    if ($v === false) continue;
    if (count($path) === 2) $config[$path[0]][$path[1]] = $v; else $config[$path[0]] = $v;
}

return $config;
