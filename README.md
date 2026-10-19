# DWP Arabia website — PHP + MySQL

A bilingual (English / Arabic, full RTL) marketing site for DWP Arabia, built from `refdoc/DWP-Arabia-Website-Build-Spec.md`. It replaces the spec's Next.js stack with **plain PHP 8.1+ and MySQL 8**: no framework, no build step, no Composer dependencies. It is served from `https://dwptech.com/arabia/`.

Open items DWP must supply are listed in **[CONFIRM.md](CONFIRM.md)**. Requirement-by-requirement status is in **[REQUIREMENTS.md](REQUIREMENTS.md)**.

## What's where

| Path | Purpose |
|---|---|
| `public/` | **The only web root.** `index.php` (front controller), `.htaccess`, `assets/` (CSS, JS, images) |
| `content/pages/*.php` | **Page copy**: one file per page, with `en` and `ar` arrays holding title, meta, H1 and sections. Marketing edits text here |
| `app/lib/routes.php` | Page registry (URL, breadcrumb parent, schema types, form pre-selection) and the 301/410 redirect map |
| `app/views/sections/` | Reusable section templates (`hero`, `cards`, `list`, `steps`, `table`, `faq`, `cta_box`, `lead_form`, `trust_strip`, `logos`, `assessment` …). Each file's header comment lists its keys |
| `app/views/partials/` | Layout parts: header, footer, lead form, breadcrumbs, cookie banner, article, role, case study |
| `app/lang/{en,ar}.php` | UI strings (menu, footer, forms, errors, cookie banner) |
| `app/lib/` | SEO head, JSON-LD, forms and lead delivery, SMTP mailer, assessment scoring, sitemaps |
| `app/admin/` | Admin area at `/arabia/admin/` |
| `database/schema.sql`, `seed.sql` | MySQL schema, plus the 6 launch articles (EN + AR, drafts) |
| `tests/run.php` | Launch checks from spec Part D |
| `scripts/` | Dev router, asset fetcher, admin-user creator |
| `storage/` | CV uploads and logs (not web-accessible) |

### What lives in MySQL

- **Leads** from every form (lead, demo, quote and short forms), with UTM/click IDs and email/CRM delivery status.
- **Assessments**: answers, score and band.
- **Applications**: CVs. The PDF files are stored in `storage/uploads/`.
- Editable content: **Insights** (blog), **Careers**, **Case studies**, **Testimonials** and **Client logos**, all managed in the admin area.

Page copy stays in version-controlled PHP files, as the spec intended for MDX. Testimonials, case studies and client logos render only when approved rows exist; otherwise the sections are hidden (spec B7).

## Run locally

```bash
mysql -uroot -e "CREATE DATABASE dwp_arabia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -uroot dwp_arabia < database/schema.sql
mysql -uroot dwp_arabia < database/seed.sql
cp config.local.example.php config.local.php        # edit DB credentials etc.
php scripts/create-admin.php you@dwptech.com        # admin login
bash scripts/fetch-assets.sh                         # images from the old site (needs cwebp)
php -S 127.0.0.1:8080 -t public scripts/dev-router.php
```

Open http://127.0.0.1:8080/arabia/ (Arabic: `/arabia/ar/`, admin: `/arabia/admin/`).

## Tests (spec Part D launch checklist)

```bash
php tests/run.php http://127.0.0.1:8080
```

The tests fetch every page over HTTP, in EN and AR, and check:

- **SEO tags:** title, meta description and exactly one H1 match the content file and appear **verbatim in the spec**. Canonical is self-referencing, hreflang has `en-SA`/`ar-SA`/`x-default`, `og:locale` is set and JSON-LD parses.
- **Page structure:** required schema types, breadcrumbs (visible + schema), FAQ schema matching the visible FAQ, `lang`/`dir`, a language switch to the same page, and image alt text and dimensions.
- **Content rules:** no English UI text on Arabic pages, and no reseller wording or demo filler.
- **Redirects:** every A5 redirect returns 301 and the dead pages return 410. Uppercase and missing-slash URLs 301, and the 404 page is noindex.
- **Sitemaps:** they list only live, indexable pages.
- **Source scan:** no Arabic presentation-form characters (U+FB50–U+FEFF) anywhere.
- **Forms:** hidden tracking fields are present and honeypot submissions are rejected.

The exit code is 1 on any failure, so the suite can gate a deploy.

Tracking checks (consent gate, the six dataLayer events, UTM capture) run in a real browser: `npm i puppeteer-core && node tests/tracking.mjs` (set any `gtm_id` in `config.local.php` first).

Regenerate the default social-share image with `php scripts/make-og-image.php`.

## Deploy

1. PHP 8.1+ with `pdo_mysql`, `curl`, `fileinfo`, `mbstring`. MySQL 8 (or MariaDB 10.5+).
2. Upload the project **outside** the web root and expose only `public/` at `/arabia/`:
   - **Apache:** `Alias /arabia /path/to/DWP_Website/public` with `AllowOverride All`. `public/.htaccess` handles rewrites, HTTPS, caching and security headers.
   - **nginx:** see `nginx.conf.example`.
3. Create the database, import `schema.sql` (+ `seed.sql`), create `config.local.php` with a random `app_secret`, and create an admin user.
4. Make `storage/` writable by PHP.
5. While on staging, keep `env = staging`: `robots.txt` disallows everything and markers are visible. At launch, set `env = production`.
6. Run `php tests/run.php https://staging-host` before switching DNS or the WordPress paths.

The old WordPress URLs are redirected by the PHP router (spec A5): `-ar` slugs, `erp-programs`, `solutions-ai`, `career/*`, the 12 demo posts (→ `/insights/`), and `hello-world`/`test-page` (410).

## Forms and lead delivery

Every form posts to `/arabia/api/lead/`, `/api/assessment/` or `/api/cv/`. On submit, the server:

1. Checks spam: honeypot, a signed time-trap token (no sessions, so pages stay cacheable), Cloudflare Turnstile if keys are set, and a limit of 5 submissions per IP per 10 minutes.
2. Validates the input. Saudi mobiles are normalised to `+9665XXXXXXXX`.
3. Saves the submission to MySQL.
4. Emails the sales inbox (SMTP or `mail()`) and POSTs the JSON to the CRM webhook. Failures are logged in `storage/logs/` and flagged in the admin dashboard.
5. Redirects to `/thank-you/` (noindex), which fires `generate_lead` (plus `book_demo` for the demo form).

The assessment scores 0–100 server-side. It shows a result page (noindex, fires `assessment_complete`), emails the visitor their result, and sends answers + score to the CRM.

## Tracking

- `dataLayer` events: `generate_lead`, `book_demo`, `assessment_complete`, `whatsapp_click`, `phone_click`, `email_click`. Each carries `page_language` and `page_path`.
- **GTM loads only after the visitor accepts cookies** (PDPL banner in both languages; "Cookie settings" in the footer reopens it).
- Configure GA4, Google Ads conversions, LinkedIn Insight and Meta Pixel as tags **inside GTM**, triggered by these events.
- UTM parameters, `gclid`, `li_fat_id`, `fbclid` and the landing page are captured on first touch and sent as hidden form fields.

## Editing content

- **Page text:** edit `content/pages/<page>.php`. Sections are arrays, for example `['type' => 'cards', 'h2' => '…', 'items' => [['title' => '…', 'text' => '…', 'href' => '/sap-business-one/']]]`.
  - Inline formatting: `**bold**` and `[link text](/path/)` (links become language-aware automatically).
  - Keep titles, metas and H1s identical to the spec: the test suite checks this.
- **Blog, careers, case studies, testimonials, client logos:** use the admin area. Article `<h2>` headings build the table of contents automatically, and FAQ JSON feeds the FAQPage schema.
- **Never paste Arabic copied from a PDF or the old site.** The admin rejects presentation-form characters, and the tests fail on them.
