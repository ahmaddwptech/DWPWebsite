<?php
/** Form tokens, validation and lead delivery (MySQL + email + CRM webhook). */

function app_secret(): string
{
    $s = (string) cfg('app_secret');
    return $s !== '' ? $s : hash('sha256', ROOT_DIR . php_uname());
}

/** Signed, stateless token: timestamp + HMAC. Also used as a time trap. */
function form_token(): string
{
    $ts = (string) time();
    return $ts . '.' . hash_hmac('sha256', $ts, app_secret());
}

function check_form_token(string $token, int $minSeconds = 3, int $maxSeconds = 86400): bool
{
    if (!preg_match('~^(\d+)\.([a-f0-9]{64})$~', $token, $m)) return false;
    if (!hash_equals(hash_hmac('sha256', $m[1], app_secret()), $m[2])) return false;
    $age = time() - (int) $m[1];
    return $age >= $minSeconds && $age <= $maxSeconds;
}

const TRACKING_FIELDS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'li_fat_id', 'fbclid', 'landing_page'];

/** Hidden fields shared by every form. */
function form_hidden_fields(string $formType): string
{
    $h = '<input type="hidden" name="_token" value="' . e(form_token()) . '">';
    $h .= '<input type="hidden" name="form_type" value="' . e($formType) . '">';
    $h .= '<input type="hidden" name="page_language" value="' . e(lang()) . '">';
    $h .= '<input type="hidden" name="page_path" value="' . e(strtok($_SERVER['REQUEST_URI'] ?? '/', '?')) . '">';
    foreach (TRACKING_FIELDS as $f) $h .= '<input type="hidden" name="' . $f . '" value="" data-track-field="' . $f . '">';
    // Honeypot: real users never see or fill this field.
    $h .= '<div class="hp" aria-hidden="true"><label>' . (is_ar() ? 'الموقع' : 'Website') . '<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>';
    if (cfg('turnstile_site_key')) {
        $h .= '<div class="cf-turnstile" data-sitekey="' . e(cfg('turnstile_site_key')) . '" data-language="' . e(lang()) . '"></div>';
    }
    return $h;
}

function field_error(array $errors, string $name): string
{
    return isset($errors[$name]) ? '<p class="field-error" id="err-' . e($name) . '">' . e($errors[$name]) . '</p>' : '';
}

function input_attrs(array $errors, string $name): string
{
    return isset($errors[$name]) ? ' aria-invalid="true" aria-describedby="err-' . e($name) . '"' : '';
}

function old(string $name, string $default = ''): string
{
    return e($GLOBALS['form_old'][$name] ?? $default);
}

function select_options(array $options, string $selected = '', bool $placeholder = true): string
{
    $h = $placeholder ? '<option value="">' . e(t('form.select')) . '</option>' : '';
    foreach ($options as $v => $label) {
        $h .= '<option value="' . e($v) . '"' . ((string) $v === $selected ? ' selected' : '') . '>' . e($label) . '</option>';
    }
    return $h;
}

function verify_turnstile(string $response): bool
{
    $secret = cfg('turnstile_secret_key');
    if (!$secret) return true;
    if ($response === '') return false;
    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(['secret' => $secret, 'response' => $response, 'remoteip' => client_ip()]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
    ]);
    $res = json_decode((string) curl_exec($ch), true);
    return !empty($res['success']);
}

/** Spam checks shared by all forms. Returns true if the submission looks human. */
function passes_spam_checks(array $in): bool
{
    if (!empty($in['website'])) return false;
    if (!check_form_token((string) ($in['_token'] ?? ''))) return false;
    if (!verify_turnstile((string) ($in['cf-turnstile-response'] ?? ''))) return false;
    // Rate limit: max 5 submissions per IP per 10 minutes.
    $row = db_one('SELECT COUNT(*) AS n FROM leads WHERE ip = ? AND created_at > (NOW() - INTERVAL 10 MINUTE)', [client_ip()]);
    if ($row && (int) $row['n'] >= 5) return false;
    return true;
}

function normalize_mobile(string $m): ?string
{
    $d = preg_replace('~\D~', '', $m);
    if (str_starts_with($d, '00')) $d = substr($d, 2);
    if (str_starts_with($d, '05') && strlen($d) === 10) $d = '966' . substr($d, 1);
    if (str_starts_with($d, '5') && strlen($d) === 9) $d = '966' . $d;
    if (preg_match('~^9665\d{8}$~', $d)) return '+' . $d;
    // Accept other international numbers (7–15 digits) for non-Saudi visitors.
    if (strlen($d) >= 7 && strlen($d) <= 15 && !str_starts_with($d, '966')) return '+' . $d;
    return null;
}

function clean(array $in, string $k, int $max = 255): string
{
    return mb_substr(trim(strip_tags((string) ($in[$k] ?? ''))), 0, $max);
}

/** Validate the B4 contact fields. $required: list of field names. Returns [data, errors]. */
function validate_contact(array $in, array $required): array
{
    $err = [];
    $d = [
        'full_name' => clean($in, 'full_name', 120),
        'company' => clean($in, 'company', 160),
        'job_title' => clean($in, 'job_title', 120),
        'email' => strtolower(clean($in, 'email', 190)),
        'mobile' => clean($in, 'mobile', 40),
    ];
    foreach ($required as $f) {
        if (isset($d[$f]) && $d[$f] === '') $err[$f] = t('form.errors.required');
    }
    if ($d['email'] !== '' && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $err['email'] = t('form.errors.email');
    if ($d['mobile'] !== '') {
        $n = normalize_mobile($d['mobile']);
        if ($n === null) $err['mobile'] = t('form.errors.mobile'); else $d['mobile'] = $n;
    }
    if (empty($in['consent'])) $err['consent'] = t('form.errors.consent');
    return [$d, $err];
}

function tracking_data(array $in): array
{
    $d = [];
    foreach (TRACKING_FIELDS as $f) $d[$f] = clean($in, $f, $f === 'landing_page' ? 500 : 255);
    $d['page_path'] = clean($in, 'page_path', 500);
    return $d;
}

/**
 * Handle POST /api/lead/ (lead, demo, quote and short forms).
 * On success: store in MySQL, email sales, push to CRM, redirect to thank-you.
 * On error: re-render the page the form was on with messages.
 */
function handle_lead_post(): void
{
    $in = $_POST;
    $GLOBALS['lang'] = ($in['page_language'] ?? 'en') === 'ar' ? 'ar' : 'en';
    $type = in_array($in['form_type'] ?? '', ['lead', 'demo', 'quote', 'short'], true) ? $in['form_type'] : 'lead';

    if (!passes_spam_checks($in)) {
        log_line('spam.log', client_ip() . ' ' . ($in['page_path'] ?? ''));
        form_failed(['_form' => t('form.errors.spam')], $in);
        return;
    }

    $required = ['full_name', 'company', 'email', 'mobile'];
    [$d, $err] = validate_contact($in, $required);

    $interestOpts = array_keys(t('form.interest_options'));
    $d['interest'] = in_array($in['interest'] ?? '', $interestOpts, true) ? $in['interest'] : '';
    if ($type !== 'short' && $d['interest'] === '') $err['interest'] = t('form.errors.required');
    $d['employees'] = array_key_exists($in['employees'] ?? '', t('form.employee_options')) ? $in['employees'] : '';
    $d['current_system'] = array_key_exists($in['current_system'] ?? '', t('form.system_options')) ? $in['current_system'] : '';
    $d['message'] = mb_substr(trim(strip_tags((string) ($in['message'] ?? ''))), 0, 4000);
    $d['users_count'] = isset($in['users_count']) && $in['users_count'] !== '' ? max(0, min(100000, (int) $in['users_count'])) : null;
    $d['demo_date'] = preg_match('~^\d{4}-\d{2}-\d{2}$~', (string) ($in['demo_date'] ?? '')) ? $in['demo_date'] : null;
    $d['demo_time'] = array_key_exists($in['demo_time'] ?? '', t('form.demo_time_options')) ? $in['demo_time'] : '';
    $d['demo_language'] = array_key_exists($in['demo_language'] ?? '', t('form.demo_language_options')) ? $in['demo_language'] : '';
    $d['demo_format'] = array_key_exists($in['demo_format'] ?? '', t('form.demo_format_options')) ? $in['demo_format'] : '';

    if ($err) {
        form_failed($err, $in);
        return;
    }

    $row = $d + tracking_data($in) + [
        'form_type' => $type,
        'lang' => lang(),
        'consent' => 1,
        'ip' => client_ip(),
        'user_agent' => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    ];
    $id = 0;
    try {
        $id = db_insert('leads', $row);
    } catch (Throwable $e) {
        log_line('leads.log', 'DB insert failed: ' . $e->getMessage() . ' ' . json_encode($row, JSON_UNESCAPED_UNICODE));
    }

    // Send the visitor to the thank-you page now; email and CRM delivery continue in the background.
    redirect_and_continue(url('/thank-you/') . '?f=' . ($type === 'demo' ? 'demo' : 'lead') . '&i=' . rawurlencode($d['interest']));

    $emailOk = send_lead_email($row, $id);
    $crmOk = push_to_crm(['type' => $type, 'id' => $id] + $row);
    if ($id) db_update('leads', $id, ['email_status' => $emailOk ? 'sent' : 'failed', 'crm_status' => $crmOk === null ? 'skipped' : ($crmOk ? 'sent' : 'failed')]);
    exit;
}

/**
 * Send a 303 redirect and close the connection, then let the script keep running
 * (email, CRM webhook) so the visitor never waits for third-party services.
 */
function redirect_and_continue(string $to): void
{
    ignore_user_abort(true);
    @set_time_limit(90);
    header('Location: ' . $to, true, 303);
    header('Content-Length: 0');
    header('Connection: close');
    if (function_exists('fastcgi_finish_request')) { fastcgi_finish_request(); return; }
    if (function_exists('litespeed_finish_request')) { litespeed_finish_request(); return; }
    while (ob_get_level() > 0) @ob_end_flush();
    flush();
}

/** Re-render the originating page with errors (no redirect, keeps the visitor's input). */
function form_failed(array $errors, array $in): void
{
    $GLOBALS['form_errors'] = $errors;
    $GLOBALS['form_old'] = array_map(fn($v) => is_string($v) ? $v : '', $in);
    http_response_code(422);
    $path = (string) ($in['page_path'] ?? '/');
    $base = rtrim(cfg('base_path'), '/');
    if ($base !== '' && str_starts_with($path, $base)) $path = substr($path, strlen($base));
    if (str_starts_with($path, '/ar/') || $path === '/ar') $path = substr($path, 3) ?: '/';
    route_page($path === '' ? '/' : $path, lang());
}

function lead_summary_lines(array $row): array
{
    $labels = [
        'form_type' => 'Form', 'lang' => 'Language', 'full_name' => 'Full name', 'company' => 'Company', 'job_title' => 'Job title',
        'email' => 'Email', 'mobile' => 'Mobile', 'employees' => 'Employees', 'interest' => 'Interested in',
        'current_system' => 'Current system', 'users_count' => 'Users', 'demo_date' => 'Preferred date', 'demo_time' => 'Preferred time',
        'demo_language' => 'Demo language', 'demo_format' => 'Format', 'message' => 'Message',
        'utm_source' => 'utm_source', 'utm_medium' => 'utm_medium', 'utm_campaign' => 'utm_campaign', 'utm_term' => 'utm_term',
        'utm_content' => 'utm_content', 'gclid' => 'gclid', 'li_fat_id' => 'li_fat_id', 'fbclid' => 'fbclid',
        'landing_page' => 'Landing page', 'page_path' => 'Submitted on',
    ];
    $lines = [];
    foreach ($labels as $k => $label) {
        if (isset($row[$k]) && $row[$k] !== '' && $row[$k] !== null) $lines[] = str_pad($label . ':', 16) . $row[$k];
    }
    return $lines;
}

function send_lead_email(array $row, int $id): bool
{
    $to = cfg('sales_inbox');
    if (!$to) return false;
    $subject = sprintf('[DWP Arabia] New %s lead: %s (%s)', $row['form_type'], $row['company'] ?? '', strtoupper($row['lang']));
    $body = "New website lead #{$id}\n\n" . implode("\n", lead_summary_lines($row)) . "\n";
    return send_mail($to, $subject, $body, $row['email'] ?? null);
}

/** POST JSON to the CRM webhook. Returns null if not configured. */
function push_to_crm(array $payload): ?bool
{
    $urlHook = cfg('crm_webhook_url');
    if (!$urlHook) return null;
    $ch = curl_init($urlHook);
    $headers = ['Content-Type: application/json'];
    if (cfg('crm_webhook_secret')) $headers[] = 'X-Webhook-Secret: ' . cfg('crm_webhook_secret');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errno = curl_errno($ch);
    $ok = !$errno && $code >= 200 && $code < 300;
    if (!$ok) log_line('crm.log', "Webhook failed ($code/$errno): " . json_encode($payload, JSON_UNESCAPED_UNICODE));
    return $ok;
}

/** Handle POST /api/cv/ (careers CV upload). */
function handle_cv_post(): void
{
    $in = $_POST;
    $GLOBALS['lang'] = ($in['page_language'] ?? 'en') === 'ar' ? 'ar' : 'en';
    if (!passes_spam_checks($in)) {
        form_failed(['_form' => t('form.errors.spam')], $in);
        return;
    }
    [$d, $err] = validate_contact($in, ['full_name', 'email', 'mobile']);
    $f = $_FILES['cv'] ?? null;
    $okFile = $f && $f['error'] === UPLOAD_ERR_OK && $f['size'] > 0 && $f['size'] <= 5 * 1024 * 1024;
    if ($okFile) {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        $head = (string) file_get_contents($f['tmp_name'], false, null, 0, 5);
        $okFile = $mime === 'application/pdf' && $head === '%PDF-';
    }
    if (!$okFile) $err['cv'] = t('form.errors.file');
    if ($err) {
        form_failed($err, $in);
        return;
    }
    $dir = cfg('uploads_dir') . '/cv/' . date('Y/m');
    if (!is_dir($dir)) mkdir($dir, 0770, true);
    $name = bin2hex(random_bytes(16)) . '.pdf';
    move_uploaded_file($f['tmp_name'], "$dir/$name");
    $role = preg_replace('~[^a-z0-9\-]~', '', (string) ($in['role'] ?? ''));
    $row = [
        'lang' => lang(), 'full_name' => $d['full_name'], 'email' => $d['email'], 'mobile' => $d['mobile'],
        'role_slug' => $role, 'message' => mb_substr(trim(strip_tags((string) ($in['message'] ?? ''))), 0, 4000),
        'cv_path' => 'cv/' . date('Y/m') . '/' . $name, 'consent' => 1, 'ip' => client_ip(),
    ];
    try {
        $id = db_insert('applications', $row);
        if (cfg('sales_inbox')) {
            send_mail(cfg('careers_inbox') ?: cfg('sales_inbox'), '[DWP Arabia] New CV: ' . $d['full_name'], "New application #{$id}\nRole: " . ($role ?: 'General') . "\nName: {$d['full_name']}\nEmail: {$d['email']}\nMobile: {$d['mobile']}\n\nDownload from the admin area.", $d['email']);
        }
    } catch (Throwable $e) {
        log_line('applications.log', $e->getMessage());
    }
    redirect(url('/thank-you/') . '?f=cv', 303);
}
