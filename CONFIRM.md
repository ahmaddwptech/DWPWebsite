# CONFIRM.md — items DWP must supply before launch

In staging (`env` ≠ `production`) every open item shows on the page as a yellow **[CONFIRM]** tag, and Arabic that needs review is shaded blue (**AR-REVIEW**). In production, the tags are hidden. Numeric placeholders such as `[CONFIRM: 4]` print the number; all other placeholders print nothing. **Close every item below before switching to production.**

To find every open marker in the content:

```bash
grep -rn "\[CONFIRM" content app/views app/lang
grep -rc "\[AR-REVIEW\]" content/pages | grep -v ":0"
```

## 1. Settings (`config.local.php`)

| Setting | What's needed |
|---|---|
| `sales_inbox`, `mail_from`, `smtp` | Sales inbox for leads; sender address on dwptech.com; SMTP details (otherwise PHP `mail()` is used) |
| `crm_webhook_url` (+ `crm_webhook_secret`) | CRM name and the webhook URL that receives lead JSON |
| `turnstile_site_key` / `turnstile_secret_key` | Cloudflare Turnstile keys (optional; honeypot, time trap and rate limit are always on) |
| `gtm_id` | GTM container ID. GA4, Google Ads, LinkedIn Insight and Meta Pixel are configured **inside GTM** (see README → Tracking) |
| `whatsapp` | WhatsApp number (digits only). The floating button appears only once this is set |
| `email` | Public contact email (plain `mailto:`) |
| `instagram`, `facebook`, `x` | Social URLs (LinkedIn is already set) |
| `legal_name`, `cr_number` | Legal entity name and CR number for the footer |
| `certified_consultants` | Number of SAP Business One certified consultants (shown as "[CONFIRM: 4]") |
| `geo.lat`, `geo.lng` | Hala Center coordinates for LocalBusiness schema |
| `hrms_enabled` | Build or drop the HRMS page (currently **off**: the page, menu item and sitemap entry are hidden) |
| Working days | Confirm Saturday–Thursday (footer and contact page) |

## 2. Brand and assets (`public/assets/img/`)

- **Logo**: the old site has only a white logo (`DWP-Arabia-Site-Logo.png`, now `img/logo-light.png`). The header and footer are dark navy, so it is used as-is. A high-resolution SVG version would look sharper.
- **AI-generated photos** (`img/gen/`, 29 natural documentary-style images: every SAP, AI, industry, solution and company page has its own photo). They were generated for this build, so the people shown are not DWP staff or clients and alt text describes the scene only. DWP should approve them, or replace them with real photography, before launch.
- **Home hero photo and video** (`img/hero/home-skyline*.webp`, `video/hero-skyline(-hd).mp4/.webm`, upscaled to 2K): AI-generated photo of the Riyadh skyline at dusk (Kingdom Centre, King Fahd Road), animated on desktop. No people. On Arabic pages the image is mirrored so the text sits over open sky. DWP can replace it with licensed real footage later (16:9, landmark on the right third).
- **Hero video** (`video/hero-aurora.webm` / `.mp4`): the aurora background from the current live site, re-encoded (140 KB / 350 KB). It plays on desktop only; mobile, reduced-motion and data-saver visitors see the still poster.
- **Brand film** (`video/dwp-film.mp4`, About page, click to play): the existing `Feature_Video_1_iPhone.mp4`, compressed to 3.5 MB. It shows the *old* website and stock footage, and part of the logo text reads "Aroosi". Confirm it is still wanted, or supply an updated film.
- **Hero screenshots**: SAP pages show a coded SAP Business One–style dashboard mockup until real screenshots are added.
- **Brand colours**: placeholders in `public/assets/css/site.css` (`--c-primary`, `--c-accent`). Confirm the colours or keep these.
- ~~SAP Partner badge~~ ✅ supplied by DWP (`img/badges/sap-partner.png/.webp`), with the **SAP Business One logo**. Shown plainly in SAP's colours: header (next to the DWP logo), white partner band on Home, white partner row in the footer, Products menu, and above the H1 on SAP Business One pages. For dark backgrounds I made a transparent version of the SAP Business One logo with white "Business One" text (`img/brand/sap-business-one-white.webp`) — replace it with SAP's official reversed logo if DWP has one, and confirm sizes and clear space follow SAP's partner logo guidelines.
- **Huawei partner badge** (`img/badges/huawei-partner.png`).
- **Hero screenshots**: `img/hero/sap-b1-dashboard-en.webp` and `-ar.webp` (SAP Business One dashboard, Arabic UI for the AR page).
- **Not found on the old site**: Schneider Electric logo, ZATCA, Mudad, GOSI, Qiwa, Salla integration logos, `CRAS.jpg`. Save them as `img/partners/schneider-electric.webp`, `img/integrations/{zatca,mudad,gosi,qiwa,salla}.webp`, `img/pages/cras.webp`.
- **Default social share image**: `img/og-default.jpg` (1200×630).
- **Client logos**: add them in Admin → Client logos, only with written permission. Each name becomes the alt text.
- **Leadership**: names, titles, photos and LinkedIn links (About page, `people` section in `content/pages/about.php`).

## 3. Facts in the page content

| Topic | Where |
|---|---|
| Group facts (1999, 4,500+ people, countries) | Home, About |
| Certified consultant count | Home, trust strip, About |
| Which partners are formal (Microsoft, Cisco, Schneider, Fortinet); Huawei scope | Partners |
| Approved Saudi client list; 2–3 real case studies | Clients (Admin → Case studies) |
| Typical go-live timeline (8–16 weeks?) | Implementation |
| User range for "right for you" (10–500?) | SAP Business One |
| Hijri dates on documents; HR & payroll add-on for GOSI/Mudad | SAP Business One |
| ZATCA Phase 2 wording, reviewed by the ZATCA consultant; support for older SAP B1 versions | ZATCA page (the FAQ answer says this is checked during the readiness step) |
| Support plans: names, hours, response times (do not publish numbers until approved) | Support (the FAQ answer says times depend on the plan) |
| Subscription vs perpetual options | Pricing |
| vs-Odoo comparison wording (presales review) | SAP B1 vs Odoo |
| AI hosting options in-Kingdom; whether data trains public models, per solution (FAQ currently answers "No") | AI pillar |
| Saudi dialect support | Arabic AI Assistants |
| Odoo apps implemented; Phase 2 scope for Odoo | Odoo |
| Industry add-ons: Beas, mobile sales, POS, e-commerce (Salla/Zid/Shopify), progress billing/retention | Industry pages |
| HRMS features (GOSI, Mudad, WPS) | HRMS (if built) |
| Privacy Policy text aligned with PDPL: legal basis, retention periods, processors (CRM, email host), rights, privacy contact | Privacy Policy (draft structure only) |
| Insights authors (name, photo, LinkedIn) and full article bodies | Admin → Insights (6 drafts seeded) |
| Real open roles | Admin → Careers (none seeded; only the CV form shows) |
| Real, approved testimonials | Admin → Testimonials (the section stays hidden until one is approved) |

- **New overview pages not in the spec:** `/products/` and `/solutions/` (linked from "View all" in the menu). Their title, meta description and H1 were written during the build (`content/pages/products.php`, `solutions.php`); please approve or replace them. Product images on `/products/`: the SAP Business One Web Client and Odoo Accounting screens are SAP's and Odoo's official product images (confirm DWP's partner terms allow using them, or replace with screenshots from DWP's own demo systems); CRAS, PMS and IoT screens come from DWP's current site. The ISO 9001:2015 badge is a designed placeholder: supply the certification body's mark and the certificate PDF (`public/assets/docs/iso-9001-2015-certificate.pdf`).

- **Cortex add-ons (`/sap-business-one/`):** Production, Project Management, HR & Payroll, POS, AI, Property Management, Assets Management and Intercompany. Each card shows a full product logo (`public/assets/img/cortex/cortex-{name}.webp`: the circuit-symbol "C" + blue "ortex" + the application name in teal; `cortex-logo.webp` is the master logotype). All were AI-generated during the build — replace with DWP's official Cortex logos if they exist. Links go to Contact (Cortex AI to AI for SAP Business One) until each add-on's own page exists; send the page URLs or ask to build them.

- **Compliance section (`/sap-business-one/`):** confirm the new items. Local cloud powered by Huawei: hosting region and certifications. NCA cybersecurity controls: which controls (ECC, CCC) DWP's hosting meets. PDPL, data residency, Zakat/SOCPA reporting and audit trail wording.

- **Cortex AI page (`/cortex/ai/`):** title, meta and H1 written during the build; confirm the capability list (chat in Arabic/English, reports on request, forecasting, alerts, document automation, scheduled reports), the security claims (hosting on the Huawei-powered local cloud, read-only access, encrypted connection, permissions, request logging), how often Cortex AI refreshes SAP Business One data, and the product screen (`img/products/cortex-ai-screen.webp` is an AI-generated mock-up of the standalone app with fictional customers; replace with a real Cortex AI screenshot when available). Cortex AI is a dedicated application connected to SAP Business One (read-only), not something running inside it. The page also presents a Cortex AI mobile app (iPhone and Android) for quick questions, push alerts and reports on the go; confirm its features and release date.
- **Cortex Intercompany page (`/cortex/intercompany/`):** title, meta and H1 written during the build. Features shown: head office company, automatic mirroring of sales and purchase documents, master data sync, central payments on behalf, intercompany reconciliation, eliminations and share equity. Confirm the full list of documents that mirror, whether mirrored documents can wait for approval in the receiving company, multi-currency support between companies, and the IFRS consolidation wording (each marked `[CONFIRM]` on the page). The group screen in the hero and the document demo are drawn in HTML with fictional companies (Head office, Trading Co., Logistics Co., Factory) and amounts.
- **Cortex Production, Project Management, HR & Payroll, POS, Property Management and Assets Management pages (`/cortex/<name>/`):** built from the product names; title, meta, H1, the eight features and the FAQ on each page need DWP confirmation. Items marked `[CONFIRM]` on the pages: GOSI and WPS salary file, self service (HR & Payroll); ZATCA integration, payment terminals, loyalty, offline mode (POS); Ejar integration (Property Management); depreciation books and mobile counting (Assets Management). The app screens in the first section are drawn in HTML with sample data (company OEC Computers, fictional customers, tenants and employees); replace them with real screenshots when available. Content lives in `content/cortex-addons.php`. Every add-on page also has a results block (typical figures, `content/cortex-addons-extra.php`, marked `[CONFIRM: outcome figures]`) and a "Works with" block listing local systems (ZATCA, GOSI, Mudad, Qiwa, Muqeem, Ejar, SADAD, Etimad, mada, Apple Pay, STC Pay, Tabby, Tamara, Saudi banks and others, marked `[CONFIRM: integration list and scope]`); confirm which integrations each add-on really has before launch. All six (Production, Project Management, HR & Payroll, POS, Property Management, Assets Management) have a clickable process flow and a live, clickable control room with sample data (HR & Payroll, POS, Property and Assets are configured in `content/cortex-addons-live.php`); Project Management functions confirmed by DWP: budgets and cost control, tasks, schedule and timesheets, billing and retention, subcontractors and purchasing.
- **Live layout on 15 more pages (`content/live/*.php`):** Odoo, CRAS, PMS, Industrial IoT platform, Data and BI, AI, Industrial IoT intelligence, Consulting, SAP Business One implementation, support and ZATCA, and the manufacturing, distribution, retail and contracting pages now have a laptop screen, results figures, a clickable process, a live control room and a works with list. All figures, names and screens are sample data; results are marked `[CONFIRM: outcome figures]` and integration lists `[CONFIRM: integration list and scope]`, with a few specific items marked (Odoo ZATCA scope and payment providers, Power BI connector, supported PLC protocols, Vision 2030 wording, Teams, BI tool, loyalty scope, Future Factories wording). The pages keep their spec title, meta, H1, body content and FAQ.

## 4. Arabic to review (AR-REVIEW)

Wherever the spec gave English only, the Arabic was written natively and wrapped in `[AR-REVIEW]…[/AR-REVIEW]`. Count per file:

`about` 7 · `ai` 13 · `ai.ai-for-sap-business-one` 10 · `ai.arabic-ai-assistants` 9 · `ai.document-automation` 12 · `ai.predictive-analytics` 12 · `ai.vision-ai` 11 · `book-a-demo` 1 · `built-for-saudi-arabia` 5 · `careers` 8 · `consulting` 2 · `cras` 11 · `data-and-bi` 5 · `erp` 9 · `erp.odoo` 25 · `home` 2 · `hrms` 27 · `industrial-iot-platform` 11 · `industries` 6 · `industries.contracting` 25 · `industries.distribution` 20 · `industries.manufacturing` 29 · `industries.retail` 28 · `iot-industrial-intelligence` 2 · `our-clients` 1 · `pms` 13 · `privacy-policy` 16 · `sap-business-one` 13 · `sap-business-one.free-assessment` 2 · `sap-business-one.implementation` 9 · `sap-business-one.pricing` 17 · `sap-business-one.support` 12 · `sap-business-one.switch` 35 · `sap-business-one.vs-odoo` 32 · `sap-business-one.zatca` 14

These Arabic strings can't carry markers, so review them separately:

- **UI strings** in `app/lang/ar.php` that are not in the spec: form errors, cookie banner, assessment buttons, 404 page, demo date/time/format options.
- **Assessment result texts and next steps** in `app/lib/assessment.php` (they are marked).
- **The 6 seeded Insights articles (AR rows)**: meta descriptions, excerpts, outline headings and FAQ answers.
- **About page link text** "تعرّف على جميع شركائنا ←" (inside a link, so it can't hold a marker).

When Arabic is approved, delete the `[AR-REVIEW]` and `[/AR-REVIEW]` tags around it.

## 5. After launch (DWP team)

- Submit `https://dwptech.com/arabia/sitemap-index.xml` in Google Search Console and Bing Webmaster Tools.
- Set up a Google Business Profile for the Riyadh office with the same name, address and phone.
- Make sure the domain-level `https://dwptech.com/robots.txt` lists `Sitemap: https://dwptech.com/arabia/sitemap-index.xml`. Crawlers only read `robots.txt` at the domain root; `/arabia/robots.txt` is provided for reference.

## Cortex AI and AI for Your ERP: no SAP wording (Oct 13)
- Cortex AI (`/cortex/ai/`) and AI for Your ERP (`/ai/ai-for-sap-business-one/`) now say "your ERP" instead of SAP Business One. Please confirm which ERP systems Cortex AI connects to today. The FAQ says it works with ERP systems that offer a secure API, checked in a short technical review.
- The URL `/ai/ai-for-sap-business-one/` still contains "sap". Changing it needs a 301 redirect; tell us if you want it.
- The industry pages, footer demo line and Company menu demo card no longer name SAP Business One.
