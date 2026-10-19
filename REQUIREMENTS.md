# Spec compliance — DWP Arabia website

Maps every requirement in `refdoc/DWP-Arabia-Website-Build-Spec.md` to its implementation and how it was verified.
Stack: **PHP 8 + MySQL** (replaces the spec's Next.js at DWP's request). Last verified: 9 Oct 2026.

**Legend:** ✅ done and verified · ⏳ built, waiting on a DWP fact or asset (listed in `CONFIRM.md`)

## Part A — Build

| # | Requirement | Status | Implementation / evidence |
|---|---|---|---|
| A1 | Bilingual lead-gen site: SAP Business One first, AI second, Odoo | ✅ | 38 pages EN + AR, `content/pages/`. SAP partnership block (`sections/sap_partner.php`) on Home, About and Partners; Odoo shown as a secondary option in menus, footer and Partners |
| A2 | Content editable without code | ✅ | Page copy in `content/pages/*.php`; blog, careers, case studies and testimonials in MySQL with an admin at `/arabia/admin/` |
| A2 | Forms → email to sales + CRM webhook + spam protection | ⏳ | `app/lib/forms.php`: MySQL + SMTP/mail() + JSON webhook; honeypot, signed time token, rate limit, optional Turnstile. **Inbox and webhook URL to supply** |
| A2 | Served from `dwptech.com/arabia/` | ✅ | `base_path` config; `.htaccess` / `nginx.conf.example` |
| A3 | `/arabia/<slug>/` and `/arabia/ar/<slug>/` with the same slug, lowercase, trailing slash | ✅ | Router 301-normalises; tested |
| A3 | `lang` / `dir`, full RTL mirroring | ✅ | CSS logical properties; tested on every page |
| A3 | Language switch to the same page | ✅ | Tested on every page |
| A3 | hreflang `en-SA` / `ar-SA` / `x-default` + self canonical | ✅ | Tested on every page |
| A4 | Exact title, meta and H1 per language | ✅ | Test checks each one **verbatim against the spec file** |
| A4 | One H1 per page | ✅ | Tested |
| A4 | OG + Twitter tags, 1200×630 image, `og:locale` + alternate | ✅ | `img/og-default.jpg` (`scripts/make-og-image.php`); tested |
| A4 | JSON-LD per page; FAQ schema = visible FAQ | ✅ | `app/lib/schema.php`. Test checks Google's required fields (Organization, LocalBusiness, FAQPage, Article, BreadcrumbList, JobPosting, HowTo, Service) |
| A4 | Visible breadcrumbs + BreadcrumbList on inner pages | ✅ | Tested |
| A4 | Images: alt in page language, WebP, width/height, lazy below the fold, responsive | ✅ | `img()` helper with `srcset` (800 px mobile variants); tested |
| A4 | EN + AR sitemaps + index, robots, noindex on thank-you / 404 | ✅ | `/sitemap-index.xml`, `/sitemap.xml`, `/ar/sitemap.xml`; tested |
| A4 | Core Web Vitals (mobile): LCP < 2.5 s, CLS < 0.1, INP < 200 ms; no hero video on mobile | ✅ | Lighthouse mobile: LCP 1.7–2.4 s, CLS 0, TBT 0 ms. Home hero video (230 KB) loads ≥ 768 px only; phones get a 23 KB photo |
| A5 | All redirects (301 / 410) | ✅ | `redirect_map()`; every rule tested |
| A6 | GTM only after consent; GA4, Ads, LinkedIn and Meta tags inside GTM | ⏳ | Tested in `tests/tracking.mjs`. **GTM ID to supply** |
| A6 | Events `generate_lead`, `book_demo`, `assessment_complete`, `whatsapp_click`, `phone_click`, `email_click` with `page_language` + `page_path` | ✅ | Tested in `tests/tracking.mjs` |
| A6 | Hidden fields: UTM ×5, gclid, li_fat_id, fbclid, landing_page, page_language | ✅ | Tested (capture into the form) |
| A6 | PDPL cookie banner EN + AR | ✅ | Accept / Reject, re-openable from the footer; tested |
| A7 | Logo from the live site | ✅ | White logo used on dark header and footer (a dark version is a CONFIRM item) |
| A7 | Fonts: English Inter, Arabic IBM Plex Sans Arabic | ✅ | Self-hosted (no Google requests before consent) |
| A7 | "SAP Business One" written in full in headings | ✅ | Every rendered heading scanned |
| A7 | Never "reseller / licence sales / authorized distributor", no prices | ✅ | Tested across rendered pages and source |
| A7 | Mobile-first, sticky header, ~1200 px max width | ✅ | No horizontal overflow at 375 px (checked) |

## Part B — Shared components

| # | Requirement | Status | Notes |
|---|---|---|---|
| B1 | Header menu, language switch, Book a Demo | ✅ | Changed at DWP's request from the spec's 6 dropdowns to 4: **Products** (ERP: SAP Business One as a single flagship card, then Odoo; Platforms: CRAS, PMS, IIoT Platform). SAP sub-pages are reached from the SAP Business One page and the footer, **Solutions** (AI, Data & BI, Consulting, Industrial IoT), **Industries**, **Company**. "SAP B1 vs Odoo" removed from menus at DWP's request (page kept for search). Full-width mega menu on desktop; drawer with accordions on mobile; hover, click and keyboard support; RTL mirrored. HRMS hidden until `hrms_enabled` |
| B2 | Footer columns, contact block, social links, badges, © line | ⏳ | Footer v3: dark CTA panel (Riyadh skyline, page CTA text, Book a Demo, WhatsApp, phone), light body with brand + contact and SAP Business One / Products / Solutions / Company columns, partner logos in their own colours, legal bar with social icons. WhatsApp, email, social URLs, Huawei badge and CR number to supply |
| B3 | Floating WhatsApp with per-page message, bottom-left on Arabic | ⏳ | Staging shows a placeholder until the number is set |
| B4 | Lead form: 10 fields, pre-selected interest, consent, thank-you | ✅ | Company required as specified |
| B5 | CTA band | ✅ | Rendered once, as the CTA panel at the top of the footer, using each page's `cta_band` text (no duplicate band) |
| B6 | Trust strip | ⏳ | Consultant count and client logos to supply |
| B7 | Testimonials only when real and approved | ✅ | Hidden when empty, never "No Post Found" |

## Part C — Pages

All pages C1–C39 exist in EN and AR with the spec's SEO and sections. Special features:
- **C6 Book a Demo:** date, time, language and format fields; fires `book_demo`.
- **C16 Free ERP Assessment:** 10 steps with a progress bar, 0–100 score with 3 bands, result page, email to the visitor, score sent to the CRM.
- **C38 Insights:** article template with author, date, reading time, table of contents, FAQ schema, related CTA and related articles. The 6 launch articles are seeded as drafts.
- **C39 Careers:** JobPosting schema; CV upload (PDF ≤ 5 MB, type checked); no demo jobs.

## Part D — Launch checklist

| Check | Status | Evidence |
|---|---|---|
| All pages EN + AR with exact title / meta / H1 (automated) | ✅ | `php tests/run.php` |
| hreflang + canonical (automated) | ✅ | `php tests/run.php` |
| RTL; no English UI on Arabic pages | ✅ | Tests + full Latin-word scan of every AR page (only brand / product names remain) |
| Redirect status codes | ✅ | `php tests/run.php` |
| Sitemaps list only live, indexable pages | ✅ | `php tests/run.php` fetches every URL |
| No demo / filler content | ✅ | `php tests/run.php` |
| No presentation-form Arabic (U+FB50–U+FEFF) (automated) | ✅ | Source scan in tests; the admin also rejects it |
| Every [CONFIRM] in `CONFIRM.md`, highlighted on staging | ✅ | Markers render as yellow tags in staging and are hidden in production |
| Forms: validation, spam, email + CRM, UTM, thank-you, events | ✅ / ⏳ | Tested end to end; real delivery needs the inbox and webhook URL |
| Lighthouse mobile ≥ 90 (Home, SAP pillar, industry page) | ✅ | See below |
| JSON-LD valid (Home, SAP FAQ, Contact, article) | ✅ | Required fields tested offline. Run Google's Rich Results Test once the site is on a public URL |
| No reseller / licence-price wording | ✅ | Tested |
| Search Console, Bing, Google Business Profile | ⏳ | DWP team, after launch |

### Lighthouse — mobile, 9 Oct 2026

| Page | Performance | Accessibility | Best practices | SEO | LCP | CLS |
|---|---|---|---|---|---|---|
| `/` | 99 | 100 | 100 | 100 | 2.0 s | 0 |
| `/ar/` | 98 | 100 | 100 | 100 | 2.4 s | 0 |
| `/sap-business-one/` | 100 | 100 | 100 | 100 | 1.7 s | 0 |
| `/ar/sap-business-one/` | 98 | 100 | 100 | 100 | 2.3 s | 0 |
| `/industries/distribution/` | 100 | 100 | 100 | 100 | 1.7 s | 0 |
| `/ar/industries/distribution/` | 98 | 100 | 100 | 100 | 2.3 s | 0 |

These were measured on the local PHP server with gzip. Re-run them on the production server, since hosting and CDN affect the final numbers.
