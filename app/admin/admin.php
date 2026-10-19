<?php
/**
 * Admin area (/arabia/admin/): leads, assessments, CVs, and content tables in MySQL.
 * Create the first user with: php scripts/create-admin.php you@dwptech.com
 */

function admin_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_name('dwp_admin');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => rtrim(cfg('base_path'), '/') . '/admin/',
        'secure' => !empty($_SERVER['HTTPS']) || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https',
        'httponly' => true, 'samesite' => 'Strict',
    ]);
    session_start();
}

function admin_url(string $p = ''): string
{
    return rtrim(cfg('base_path'), '/') . '/admin/' . ltrim($p, '/');
}

function admin_csrf(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function admin_check_csrf(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', (string) ($_POST['_csrf'] ?? ''))) {
        http_response_code(400);
        exit('Invalid form token. Go back and try again.');
    }
}

/** Editable content tables: field => [label, type, options]. */
function admin_tables(): array
{
    $lang = ['lang', 'Language', 'select', ['en' => 'English', 'ar' => 'Arabic']];
    return [
        'insights' => ['Insights (blog)', 'title', [
            ['slug', 'Slug (same for EN and AR)', 'text'], $lang,
            ['title', 'Title (SEO title, without "| DWP Arabia")', 'text'],
            ['meta_description', 'Meta description (≤155 chars)', 'text'],
            ['h1', 'H1 (defaults to title)', 'text'],
            ['excerpt', 'Excerpt for listing', 'textarea'],
            ['author_name', 'Author name', 'text'], ['author_title', 'Author job title', 'text'],
            ['author_photo', 'Author photo (path under assets, e.g. img/team/name.webp)', 'text'],
            ['author_linkedin', 'Author LinkedIn URL', 'text'],
            ['published_at', 'Published date', 'date'], ['updated_at', 'Updated date', 'date'],
            ['reading_minutes', 'Reading time (minutes)', 'number'],
            ['body_html', 'Body (HTML — use <h2> for sections; they build the table of contents)', 'html'],
            ['faq_json', 'FAQ as JSON: [["Question","Answer"], …]', 'json'],
            ['related_url', 'Related service page path, e.g. /sap-business-one/zatca-e-invoicing/', 'text'],
            ['related_label', 'Related service CTA text', 'text'],
            ['status', 'Status', 'select', ['draft' => 'Draft', 'published' => 'Published']],
        ]],
        'careers' => ['Careers', 'title', [
            ['slug', 'Slug', 'text'], $lang, ['title', 'Job title', 'text'],
            ['meta_description', 'Meta description', 'text'], ['location', 'Location', 'text'],
            ['employment_type', 'Employment type', 'select', ['FULL_TIME' => 'Full-time', 'PART_TIME' => 'Part-time', 'CONTRACTOR' => 'Contract', 'INTERN' => 'Internship']],
            ['date_posted', 'Date posted', 'date'], ['valid_through', 'Valid through', 'date'],
            ['body_html', 'Description (HTML)', 'html'],
            ['status', 'Status', 'select', ['open' => 'Open', 'closed' => 'Closed']],
        ]],
        'case_studies' => ['Case studies', 'title', [
            ['slug', 'Slug', 'text'], $lang, ['client', 'Client', 'text'], ['industry', 'Industry', 'text'],
            ['title', 'Title', 'text'], ['meta_description', 'Meta description', 'text'], ['summary', 'Summary', 'textarea'],
            ['challenge_html', 'Challenge (HTML)', 'html'], ['solution_html', 'Solution (HTML)', 'html'], ['results_html', 'Results with numbers (HTML)', 'html'],
            ['quote', 'Client quote (approved)', 'textarea'], ['quote_by', 'Quote by', 'text'],
            ['sort', 'Sort order', 'number'],
            ['status', 'Status', 'select', ['draft' => 'Draft', 'published' => 'Published']],
        ]],
        'testimonials' => ['Testimonials', 'person', [
            $lang, ['quote', 'Quote', 'textarea'], ['person', 'Person', 'text'], ['role', 'Role', 'text'], ['company', 'Company', 'text'],
            ['approved', 'Approved by the client in writing', 'select', ['0' => 'No', '1' => 'Yes']], ['sort', 'Sort order', 'number'],
        ]],
        'client_logos' => ['Client logos', 'name', [
            ['name', 'Company name (used as alt text)', 'text'], ['logo', 'Logo path under assets, e.g. img/clients/name.webp', 'text'],
            ['approved', 'Written permission received', 'select', ['0' => 'No', '1' => 'Yes']], ['sort', 'Sort order', 'number'],
        ]],
    ];
}

function admin_dispatch(string $path, string $method): void
{
    admin_session();
    header('X-Robots-Tag: noindex, nofollow');
    header('Cache-Control: no-store');
    $path = '/' . trim($path, '/');

    if ($path === '/login') { admin_login($method); return; }
    if ($path === '/logout') { session_destroy(); redirect(admin_url('login')); }
    if (empty($_SESSION['admin_id'])) redirect(admin_url('login'));
    if ($method === 'POST') admin_check_csrf();

    if ($path === '/') { admin_dashboard(); return; }
    if ($path === '/leads') { admin_leads(); return; }
    if ($path === '/leads/export') { admin_export('leads'); return; }
    if (preg_match('~^/leads/(\d+)$~', $path, $m)) { admin_lead((int) $m[1], $method); return; }
    if ($path === '/assessments') { admin_assessments(); return; }
    if ($path === '/assessments/export') { admin_export('assessments'); return; }
    if ($path === '/applications') { admin_applications(); return; }
    if (preg_match('~^/applications/(\d+)/cv$~', $path, $m)) { admin_cv((int) $m[1]); return; }
    if (preg_match('~^/(insights|careers|case_studies|testimonials|client_logos)$~', $path, $m)) { admin_list($m[1]); return; }
    if (preg_match('~^/(insights|careers|case_studies|testimonials|client_logos)/(new|\d+)$~', $path, $m)) { admin_edit($m[1], $m[2], $method); return; }
    if (preg_match('~^/(insights|careers|case_studies|testimonials|client_logos)/(\d+)/delete$~', $path, $m) && $method === 'POST') {
        db()->prepare("DELETE FROM `{$m[1]}` WHERE id = ?")->execute([(int) $m[2]]);
        redirect(admin_url($m[1]));
    }
    http_response_code(404);
    admin_layout('Not found', '<p>Not found.</p>');
}

function admin_layout(string $title, string $body): void
{
    $nav = [
        '' => 'Dashboard', 'leads' => 'Leads', 'assessments' => 'Assessments', 'applications' => 'CVs',
        'insights' => 'Insights', 'careers' => 'Careers', 'case_studies' => 'Case studies', 'testimonials' => 'Testimonials', 'client_logos' => 'Client logos',
    ];
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">';
    echo '<title>' . e($title) . ' · DWP Arabia admin</title><style>' . admin_css() . '</style></head><body>';
    if (!empty($_SESSION['admin_id'])) {
        echo '<header><strong>DWP Arabia admin</strong><nav>';
        foreach ($nav as $p => $l) echo '<a href="' . e(admin_url($p)) . '">' . e($l) . '</a>';
        echo '<a href="' . e(url('/', 'en')) . '" target="_blank">View site ↗</a><a href="' . e(admin_url('logout')) . '">Log out</a></nav></header>';
    }
    echo '<main><h1>' . e($title) . '</h1>' . $body . '</main></body></html>';
}

function admin_css(): string
{
    return 'body{font:15px/1.5 system-ui,sans-serif;margin:0;color:#1d2733;background:#f5f7fa}header{background:#0c1a2a;color:#fff;padding:10px 20px;display:flex;flex-wrap:wrap;gap:16px;align-items:center}header nav{display:flex;flex-wrap:wrap;gap:4px 14px}header a{color:#cfe3ff;text-decoration:none}main{max-width:1200px;margin:0 auto;padding:20px}table{width:100%;border-collapse:collapse;background:#fff;font-size:14px}th,td{padding:8px 10px;border-bottom:1px solid #e3e8ef;text-align:left;vertical-align:top}th{background:#eef2f7}a{color:#0b4f8a}.btn,button{display:inline-block;background:#0b4f8a;color:#fff;border:0;border-radius:6px;padding:8px 14px;cursor:pointer;text-decoration:none;font:inherit}.btn.danger{background:#b42318}form.edit label{display:block;font-weight:600;margin:14px 0 4px}input,select,textarea{width:100%;box-sizing:border-box;padding:8px;border:1px solid #c4ced9;border-radius:6px;font:inherit}textarea{min-height:90px}textarea.html{min-height:320px;font-family:ui-monospace,monospace;font-size:13px}.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px}.card{background:#fff;border:1px solid #e3e8ef;border-radius:8px;padding:16px}.card b{font-size:28px;display:block}.muted{color:#637282}.err{background:#fef3f2;color:#b42318;padding:10px;border-radius:6px}.tools{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:12px}.tools input,.tools select{width:auto}dl{display:grid;grid-template-columns:200px 1fr;gap:6px 16px;background:#fff;padding:16px;border-radius:8px}dt{font-weight:600}dd{margin:0;white-space:pre-wrap}[dir=rtl]{text-align:right}';
}

function admin_login(string $method): void
{
    $err = '';
    if ($method === 'POST') {
        $ip = client_ip();
        $n = db_one('SELECT COUNT(*) n FROM admin_login_attempts WHERE ip = ? AND created_at > NOW() - INTERVAL 15 MINUTE', [$ip]);
        if ($n && (int) $n['n'] >= 8) {
            $err = 'Too many attempts. Try again in 15 minutes.';
        } else {
            $u = db_one('SELECT * FROM admin_users WHERE email = ?', [strtolower(trim((string) ($_POST['email'] ?? '')))]);
            if ($u && password_verify((string) ($_POST['password'] ?? ''), $u['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = (int) $u['id'];
                $_SESSION['admin_email'] = $u['email'];
                db_update('admin_users', (int) $u['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
                redirect(admin_url());
            }
            db_insert('admin_login_attempts', ['ip' => $ip]);
            $err = 'Wrong email or password.';
        }
    }
    admin_layout('Log in', ($err ? '<p class="err">' . e($err) . '</p>' : '') .
        '<form method="post" class="edit" style="max-width:360px"><label>Email<input type="email" name="email" required autocomplete="username"></label>' .
        '<label>Password<input type="password" name="password" required autocomplete="current-password"></label><p><button>Log in</button></p></form>');
}

function admin_dashboard(): void
{
    $c = fn($sql) => (int) (db_one($sql)['n'] ?? 0);
    $cards = [
        ['New leads (7 days)', $c("SELECT COUNT(*) n FROM leads WHERE created_at > NOW() - INTERVAL 7 DAY"), 'leads'],
        ['Leads not yet contacted', $c("SELECT COUNT(*) n FROM leads WHERE status = 'new'"), 'leads?status=new'],
        ['Assessments (7 days)', $c("SELECT COUNT(*) n FROM assessments WHERE created_at > NOW() - INTERVAL 7 DAY"), 'assessments'],
        ['CVs (30 days)', $c("SELECT COUNT(*) n FROM applications WHERE created_at > NOW() - INTERVAL 30 DAY"), 'applications'],
        ['Failed email/CRM deliveries', $c("SELECT COUNT(*) n FROM leads WHERE email_status = 'failed' OR crm_status = 'failed'"), 'leads?failed=1'],
        ['Draft articles', $c("SELECT COUNT(*) n FROM insights WHERE status = 'draft'"), 'insights'],
    ];
    $h = '<div class="cards">';
    foreach ($cards as [$l, $n, $p]) $h .= '<a class="card" href="' . e(admin_url($p)) . '"><b>' . $n . '</b>' . e($l) . '</a>';
    admin_layout('Dashboard', $h . '</div>');
}

function admin_leads(): void
{
    $where = ['1=1'];
    $params = [];
    if (!empty($_GET['q'])) { $where[] = '(full_name LIKE ? OR company LIKE ? OR email LIKE ? OR mobile LIKE ?)'; $q = '%' . $_GET['q'] . '%'; array_push($params, $q, $q, $q, $q); }
    if (!empty($_GET['status'])) { $where[] = 'status = ?'; $params[] = $_GET['status']; }
    if (!empty($_GET['type'])) { $where[] = 'form_type = ?'; $params[] = $_GET['type']; }
    if (!empty($_GET['failed'])) { $where[] = "(email_status = 'failed' OR crm_status = 'failed')"; }
    $rows = db_all('SELECT id, created_at, form_type, lang, full_name, company, email, mobile, interest, utm_source, utm_campaign, status, email_status, crm_status FROM leads WHERE ' . implode(' AND ', $where) . ' ORDER BY id DESC LIMIT 500', $params);
    $h = '<form class="tools"><input name="q" placeholder="Search name, company, email, mobile" value="' . e($_GET['q'] ?? '') . '">'
        . '<select name="status"><option value="">Any status</option>' . admin_opts(['new', 'contacted', 'qualified', 'closed'], $_GET['status'] ?? '') . '</select>'
        . '<select name="type"><option value="">Any form</option>' . admin_opts(['lead', 'demo', 'quote', 'short'], $_GET['type'] ?? '') . '</select>'
        . '<button>Filter</button> <a class="btn" href="' . e(admin_url('leads/export') . '?' . http_build_query($_GET)) . '">Export CSV</a></form>';
    $h .= '<table><tr><th>#</th><th>Date</th><th>Form</th><th>Name</th><th>Company</th><th>Email / Mobile</th><th>Interest</th><th>Source</th><th>Status</th><th>Delivery</th></tr>';
    foreach ($rows as $r) {
        $h .= '<tr><td><a href="' . e(admin_url('leads/' . $r['id'])) . '">' . $r['id'] . '</a></td><td>' . e($r['created_at']) . '</td><td>' . e($r['form_type']) . ' · ' . e(strtoupper($r['lang'])) . '</td>'
            . '<td>' . e($r['full_name']) . '</td><td>' . e($r['company']) . '</td><td>' . e($r['email']) . '<br><span class="muted">' . e($r['mobile']) . '</span></td>'
            . '<td>' . e($r['interest']) . '</td><td>' . e(trim($r['utm_source'] . ' / ' . $r['utm_campaign'], ' /')) . '</td><td>' . e($r['status']) . '</td><td class="muted">mail: ' . e($r['email_status']) . '<br>crm: ' . e($r['crm_status']) . '</td></tr>';
    }
    admin_layout('Leads', $h . '</table>' . (!$rows ? '<p class="muted">No leads yet.</p>' : ''));
}

function admin_opts(array $vals, string $sel): string
{
    return implode('', array_map(fn($v) => '<option' . ($v === $sel ? ' selected' : '') . '>' . e($v) . '</option>', $vals));
}

function admin_lead(int $id, string $method): void
{
    if ($method === 'POST' && in_array($_POST['status'] ?? '', ['new', 'contacted', 'qualified', 'closed'], true)) {
        db_update('leads', $id, ['status' => $_POST['status']]);
        redirect(admin_url('leads/' . $id));
    }
    $r = db_one('SELECT * FROM leads WHERE id = ?', [$id]);
    if (!$r) { admin_layout('Not found', ''); return; }
    $h = '<form method="post" class="tools"><input type="hidden" name="_csrf" value="' . e(admin_csrf()) . '"><select name="status">' . admin_opts(['new', 'contacted', 'qualified', 'closed'], $r['status']) . '</select><button>Update status</button></form>';
    $h .= '<dl>';
    foreach ($r as $k => $v) if ($v !== null && $v !== '') $h .= '<dt>' . e($k) . '</dt><dd dir="auto">' . e($v) . '</dd>';
    admin_layout('Lead #' . $id, $h . '</dl>');
}

function admin_assessments(): void
{
    $rows = db_all('SELECT id, token, created_at, lang, full_name, company, email, mobile, score, band, answers_json FROM assessments ORDER BY id DESC LIMIT 500');
    $h = '<p><a class="btn" href="' . e(admin_url('assessments/export')) . '">Export CSV</a></p><table><tr><th>#</th><th>Date</th><th>Name / Company</th><th>Contact</th><th>Score</th><th>Answers</th></tr>';
    foreach ($rows as $r) {
        $h .= '<tr><td>' . $r['id'] . '</td><td>' . e($r['created_at']) . ' · ' . e(strtoupper($r['lang'])) . '</td><td>' . e($r['full_name']) . '<br><span class="muted">' . e($r['company']) . '</span></td>'
            . '<td>' . e($r['email']) . '<br>' . e($r['mobile']) . '</td><td><b>' . (int) $r['score'] . '</b> ' . e($r['band']) . '</td><td class="muted" style="font-size:12px">' . e($r['answers_json']) . '</td></tr>';
    }
    admin_layout('Assessments', $h . '</table>');
}

function admin_applications(): void
{
    $rows = db_all('SELECT * FROM applications ORDER BY id DESC LIMIT 500');
    $h = '<table><tr><th>#</th><th>Date</th><th>Name</th><th>Contact</th><th>Role</th><th>Message</th><th>CV</th></tr>';
    foreach ($rows as $r) {
        $h .= '<tr><td>' . $r['id'] . '</td><td>' . e($r['created_at']) . '</td><td>' . e($r['full_name']) . '</td><td>' . e($r['email']) . '<br>' . e($r['mobile']) . '</td><td>' . e($r['role_slug'] ?: 'General') . '</td><td dir="auto">' . e(mb_strimwidth((string) $r['message'], 0, 200, '…')) . '</td><td><a href="' . e(admin_url('applications/' . $r['id'] . '/cv')) . '">Download PDF</a></td></tr>';
    }
    admin_layout('CVs', $h . '</table>');
}

function admin_cv(int $id): void
{
    $r = db_one('SELECT cv_path, full_name FROM applications WHERE id = ?', [$id]);
    $file = $r ? realpath(cfg('uploads_dir') . '/' . $r['cv_path']) : false;
    $root = realpath(cfg('uploads_dir'));
    if (!$file || !$root || !str_starts_with($file, $root) || !is_file($file)) { http_response_code(404); exit('Not found'); }
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="CV-' . preg_replace('~[^A-Za-z0-9]+~', '-', $r['full_name']) . '-' . $id . '.pdf"');
    header('X-Content-Type-Options: nosniff');
    readfile($file);
    exit;
}

function admin_export(string $table): void
{
    $rows = db_all("SELECT * FROM `$table` ORDER BY id DESC");
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $table . '-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows Arabic correctly
    if ($rows) fputcsv($out, array_keys($rows[0]), ',', '"', '\\');
    foreach ($rows as $r) {
        // Neutralise spreadsheet formula injection.
        fputcsv($out, array_map(fn($v) => is_string($v) && preg_match('~^[=+\-@]~', $v) ? "'" . $v : $v, $r), ',', '"', '\\');
    }
    exit;
}

function admin_list(string $table): void
{
    [$label, $titleCol] = admin_tables()[$table];
    $hasLang = in_array('lang', array_column(admin_tables()[$table][2], 0), true);
    $hasStatus = in_array('status', array_column(admin_tables()[$table][2], 0), true);
    $rows = db_all("SELECT * FROM `$table` ORDER BY id DESC");
    $h = '<p><a class="btn" href="' . e(admin_url($table . '/new')) . '">+ New</a></p><table><tr><th>#</th><th>Title</th>' . ($hasLang ? '<th>Lang</th>' : '') . ($hasStatus ? '<th>Status</th>' : '') . '<th></th></tr>';
    foreach ($rows as $r) {
        $view = isset($r['slug']) ? ['insights' => '/insights/', 'careers' => '/careers/', 'case_studies' => '/our-clients/'][$table] . $r['slug'] . '/' : null;
        $h .= '<tr><td>' . $r['id'] . '</td><td dir="auto"><a href="' . e(admin_url($table . '/' . $r['id'])) . '">' . e($r[$titleCol]) . '</a></td>'
            . ($hasLang ? '<td>' . e($r['lang']) . '</td>' : '') . ($hasStatus ? '<td>' . e($r['status']) . '</td>' : '')
            . '<td>' . ($view ? '<a href="' . e(url($view, $r['lang'])) . '" target="_blank">View ↗</a>' : '') . '</td></tr>';
    }
    admin_layout($label, $h . '</table>');
}

function admin_edit(string $table, string $id, string $method): void
{
    [$label, , $fields] = admin_tables()[$table];
    $row = $id === 'new' ? [] : (db_one("SELECT * FROM `$table` WHERE id = ?", [(int) $id]) ?? []);
    $err = '';
    if ($method === 'POST') {
        $data = [];
        foreach ($fields as $f) {
            $v = trim((string) ($_POST[$f[0]] ?? ''));
            if ($f[2] === 'json' && $v !== '') {
                json_decode($v);
                if (json_last_error()) $err = 'FAQ JSON is not valid: ' . json_last_error_msg();
            }
            if ($f[0] === 'slug') $v = strtolower(preg_replace('~[^a-z0-9\-]+~i', '-', $v));
            if (preg_match('~[\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]~u', $v)) $err = 'Field "' . $f[1] . '" contains Arabic presentation-form characters (usually copied from a PDF). Retype the Arabic text.';
            $data[$f[0]] = $v === '' && in_array($f[2], ['date', 'json', 'number'], true) ? null : $v;
        }
        // Published articles need a date (Article datePublished for Google rich results).
        if ($table === 'insights' && ($data['status'] ?? '') === 'published' && empty($data['published_at'])) $data['published_at'] = date('Y-m-d');
        if (!$err) {
            try {
                if ($id === 'new') { $newId = db_insert($table, $data); redirect(admin_url($table . '/' . $newId)); }
                db_update($table, (int) $id, $data);
                redirect(admin_url($table . '/' . $id) . '?saved=1');
            } catch (PDOException $e) {
                $err = str_contains($e->getMessage(), 'Duplicate') ? 'That slug already exists for this language.' : 'Could not save: ' . $e->getMessage();
            }
        }
        $row = $data + $row;
    }
    $h = ($err ? '<p class="err">' . e($err) . '</p>' : '') . (!empty($_GET['saved']) ? '<p class="card">Saved.</p>' : '');
    $h .= '<form method="post" class="edit"><input type="hidden" name="_csrf" value="' . e(admin_csrf()) . '">';
    foreach ($fields as $f) {
        [$name, $lbl, $type] = $f;
        $v = (string) ($row[$name] ?? '');
        $h .= '<label for="f-' . $name . '">' . e($lbl) . '</label>';
        $h .= match ($type) {
            'select' => '<select id="f-' . $name . '" name="' . $name . '">' . implode('', array_map(fn($k, $l) => '<option value="' . e($k) . '"' . ((string) $k === $v ? ' selected' : '') . '>' . e($l) . '</option>', array_keys($f[3]), $f[3])) . '</select>',
            'textarea', 'json' => '<textarea id="f-' . $name . '" name="' . $name . '" dir="auto">' . e($v) . '</textarea>',
            'html' => '<textarea class="html" id="f-' . $name . '" name="' . $name . '" dir="auto">' . e($v) . '</textarea>',
            default => '<input id="f-' . $name . '" type="' . ($type === 'number' ? 'number' : ($type === 'date' ? 'date' : 'text')) . '" name="' . $name . '" value="' . e($v) . '" dir="auto">',
        };
    }
    $h .= '<p><button>Save</button> <a href="' . e(admin_url($table)) . '">Cancel</a></p></form>';
    if ($id !== 'new') {
        $h .= '<form method="post" action="' . e(admin_url($table . '/' . $id . '/delete')) . '" onsubmit="return confirm(\'Delete permanently?\')"><input type="hidden" name="_csrf" value="' . e(admin_csrf()) . '"><button class="btn danger">Delete</button></form>';
    }
    admin_layout(($id === 'new' ? 'New' : 'Edit') . ' · ' . $label, $h);
}

