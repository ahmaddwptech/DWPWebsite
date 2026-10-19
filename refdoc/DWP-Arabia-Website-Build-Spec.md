# DWP Arabia — Website Build Spec (for Claude Code)

> **How to use this file:** Give this whole file to Claude Code and say:
> "Build this website exactly as specified. Follow Part A for the tech, Part B for shared parts, Part C for every page's content and SEO."
>
> Version: 6 Oct 2026 · Owner: Ahmad Al Saadi, Senior Presales Manager – SAP Products, DWP Arabia
> The **Appendix** at the end holds the full structure and every word of the current site (EN + clean Arabic), the assets to reuse, and the errors to fix.
> Items marked **[CONFIRM: …]** are facts DWP must supply before launch. Do not invent them. Render them as visible placeholders in staging so nobody misses them.

---

## PART A — Build instructions

### A1. Goal
A fast, bilingual (Arabic + English) marketing website for **DWP Arabia** that generates leads for **SAP Business One** (primary), **AI** (second pillar) and Odoo (secondary). Every page has its own SEO for Saudi Arabia.

### A2. Stack (default — change only if DWP says so)
- **Next.js (App Router) + TypeScript + Tailwind CSS**, static generation (SSG) for all pages.
- Content in `/content/{en,ar}/*.mdx` (or JSON) so marketing can edit text without touching code.
- Blog ("Insights") from MDX files in `/content/{en,ar}/insights/`.
- Forms post to an API route that (1) sends an email to [CONFIRM: sales inbox] and (2) pushes the lead to the CRM via webhook [CONFIRM: CRM + webhook URL]. Include Google reCAPTCHA v3 or Cloudflare Turnstile.
- Deploy target: [CONFIRM: Vercel / own server / Coolify]. Must serve from `dwptech.com/arabia/` (use Next.js `basePath: '/arabia'`) so existing links keep working.
- If DWP decides to stay on WordPress instead: use the same content, URLs and SEO from this file.

### A3. URL & language structure
- English: `https://dwptech.com/arabia/<slug>/`
- Arabic: `https://dwptech.com/arabia/ar/<slug>/` — **same slug as English**.
- Trailing slash on all URLs. Lowercase. No `-ar` suffix.
- Arabic pages: `<html lang="ar" dir="rtl">`; English: `<html lang="en" dir="ltr">`. Full RTL mirroring (nav, icons with direction, breadcrumbs, form layout).
- Language switcher links to the **same page** in the other language.
- Every page outputs `hreflang` tags: `en-SA` → EN URL, `ar-SA` → AR URL, `x-default` → EN URL. Self-referencing canonical.

### A4. SEO requirements (every page)
- `<title>` and `<meta name="description">` exactly as given in Part C (per language).
- One `<h1>` per page = the H1 given in Part C.
- Open Graph + Twitter card tags (title, description, image 1200×630, `og:locale` = `en_SA` / `ar_SA`, `og:locale:alternate`).
- JSON-LD schema as listed per page (Organization + WebSite on home; LocalBusiness on home + contact; Service / Article / FAQPage / BreadcrumbList / SoftwareApplication / JobPosting as listed). FAQ schema must match the visible FAQ text exactly.
- Breadcrumbs on all inner pages (visible + BreadcrumbList schema).
- All images: descriptive `alt` in the page language; WebP/AVIF; width/height set; lazy-load below the fold.
- Generate `sitemap.xml` (EN) and `ar/sitemap.xml` (AR) + a sitemap index; `robots.txt` pointing to it. `noindex` on: thank-you pages, 404.
- Core Web Vitals target (mobile): LCP < 2.5 s, CLS < 0.1, INP < 200 ms. No autoplay hero video on mobile.

### A5. Redirects (configure in `next.config.js` or the server)
| From | To | Code |
|---|---|---|
| `/arabia/erp-programs/` | `/arabia/erp/` | 301 |
| `/arabia/solutions-ai/` | `/arabia/ai/` | 301 |
| `/arabia/ar/<slug>-ar/` (all 13 Arabic pages) | `/arabia/ar/<slug>/` | 301 |
| `/arabia/ar/erp-programs-ar/` | `/arabia/ar/erp/` | 301 |
| `/arabia/ar/solutions-ai-ar/` | `/arabia/ar/ai/` | 301 |
| `/arabia/career/*` | `/arabia/careers/` | 301 |
| The 12 old help-desk demo posts (`/arabia/5-common-help-desk-mistakes/`, `/arabia/future-of-help-desks-2025-and-beyond/`, `/arabia/future-of-help-desks-2026-and-beyond/`, `/arabia/mastering-customer-queries-like-a-pro/`, `/arabia/transform-your-customer-journey-in-30-days/`, `/arabia/5-signs-its-time-to-upgrade-your-support-system/`, `/arabia/how-we-doubled-customer-happiness-in-6-months/`, `/arabia/how-we-doubled-customer-happiness-in-6-months-2/`, `/arabia/how-top-support-teams-stay-motivated-and-fast/`, `/arabia/how-top-support-teams-stay-motivated-and-fast-2/`, `/arabia/tell-me-you-want-it-without-telling-me/`, `/arabia/how-to-build-customer-loyalty-in-a-competitive-market/`) | `/arabia/insights/` | 301 |
| `/arabia/hello-world/`, `/arabia/test-page/` | — | 410 Gone |

### A6. Tracking (load through Google Tag Manager, only after cookie consent)
- GTM container [CONFIRM: GTM-ID] with GA4 [CONFIRM: G-ID], Google Ads conversion tag, LinkedIn Insight Tag, Meta Pixel.
- Fire these events (with `page_language` and `page_path` params): `generate_lead` (any form submit), `book_demo`, `assessment_complete`, `whatsapp_click`, `phone_click`, `email_click`.
- Forms capture hidden fields: `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `gclid`, `li_fat_id`, `fbclid`, `landing_page`, `page_language`.
- Cookie consent banner (Arabic + English) aligned with the Saudi Personal Data Protection Law (PDPL).

### A7. Brand & design
- Take the current logo files from the live site: `/arabia/wp-content/uploads/DWP-Arabia-Site-Logo.png` (dark) and the light variant. [CONFIRM: brand colours, or keep current site colours.]
- Clean, corporate, lots of white space. Primary colour for buttons/links, one accent colour for the SAP/AI highlights.
- Fonts: English **Inter**; Arabic **IBM Plex Sans Arabic** (or Tajawal). Load via `next/font`.
- SAP branding: use the official **"SAP Partner"** badge only as provided by SAP, following SAP's logo guidelines. Always write "SAP Business One" in full in headings. **Never** write "reseller", "licence sales", "authorized distributor" or show licence prices (DWP is not yet a SAP PartnerEdge Sell partner).
- Mobile-first. Sticky header. Max content width ~1200 px.

---

## PART B — Shared components

### B1. Header (all pages)
- Logo (links to home).
- Main menu — EN / AR:
  1. **SAP Business One** / **SAP Business One** — dropdown: Overview · Implementation · ZATCA E-Invoicing · Support & Upgrades · Switch to SAP B1 · Pricing · SAP B1 vs Odoo · Free ERP Assessment
     AR: نظرة عامة · التطبيق · الفوترة الإلكترونية (زاتكا) · الدعم والتحديثات · الانتقال إلى SAP Business One · الأسعار · مقارنة SAP مع Odoo · تقييم ERP مجاني
  2. **AI** / **الذكاء الاصطناعي** — dropdown: Overview · AI for Your ERP · Predictive Analytics · Arabic AI Assistants · Document Automation · Vision AI
     AR: نظرة عامة · الذكاء الاصطناعي لنظامك · التحليلات التنبؤية · المساعدات الذكية بالعربية · أتمتة المستندات · الرؤية الحاسوبية
  3. **Solutions** / **الحلول** — ERP Overview · Odoo · Data & BI · Consulting · Industrial IoT
     AR: أنظمة ERP · Odoo · البيانات وذكاء الأعمال · الاستشارات · إنترنت الأشياء الصناعي
  4. **Industries** / **القطاعات** — Distribution & Trading · Manufacturing · Retail · Contracting & Services · Built for Saudi Arabia
     AR: التوزيع والتجارة · التصنيع · التجزئة · المقاولات والخدمات · مصمم للسعودية
  5. **Products** / **منتجاتنا** — CRAS · PMS · Industrial IoT Platform · [HRMS — only if the page is approved]
  6. **Company** / **الشركة** — About · Partners · Clients & Success Stories · Insights · Careers · Contact
     AR: من نحن · شركاؤنا · عملاؤنا وقصص النجاح · مقالات · الوظائف · تواصل معنا
- Right side: language switch (EN | عربي) + button **Book a Demo** / **احجز عرضاً توضيحياً** → `/book-a-demo/`.
- **Remove** the old Sign in / Log in / Sign up widgets.

### B2. Footer (all pages)
- Column 1: logo + one line — EN: "SAP Business One and AI for growing Saudi companies." AR: "SAP Business One والذكاء الاصطناعي للشركات السعودية النامية."
- Column 2: SAP Business One links. Column 3: AI + Solutions links. Column 4: Company links + Privacy Policy.
- Contact block:
  - Address EN: Hala Center Offices, 3310 Anas bin Malik Road, Riyadh, Saudi Arabia · AR: مكاتب هلا سنتر، 3310 طريق أنس بن مالك، الرياض، المملكة العربية السعودية
  - Phone: +966 11 254 6591 (`tel:` link)
  - WhatsApp: [CONFIRM: number] (`https://wa.me/<number>`)
  - Email: [CONFIRM: e.g. info@…] as a plain `mailto:` link (no obfuscation script)
  - Hours EN: Saturday–Thursday, 9:00 AM – 5:00 PM (Saudi time) · AR: من السبت إلى الخميس، 9:00 صباحاً – 5:00 مساءً (بتوقيت السعودية) [CONFIRM working days]
- Social icons: LinkedIn `https://www.linkedin.com/company/dwp-arabia` + [CONFIRM: Instagram, Facebook, X URLs].
- Badges row: SAP Partner badge · Odoo partner badge · Huawei partner badge (official assets only) [CONFIRM: assets].
- Bottom: "© 2026 DWP IT Arabia. All rights reserved." / "© 2026 DWP IT Arabia. جميع الحقوق محفوظة." [CONFIRM: legal entity name + CR number to show]

### B3. Floating WhatsApp button (all pages)
- Bottom-right (bottom-left on Arabic). Pre-filled message per page:
  EN: "Hi DWP, I'm interested in {page name}." · AR: "مرحباً DWP، أنا مهتم بـ {اسم الصفحة}."

### B4. Lead form (reused on most pages, "Let's talk" block)
Heading EN: **Talk to an SAP Business One expert** · AR: **تحدث مع خبير SAP Business One**
Sub EN: "Tell us about your company. We reply within one business day." · AR: "أخبرنا عن شركتك وسنرد عليك خلال يوم عمل واحد."
Fields (EN / AR):
1. Full name* / الاسم الكامل*
2. Company* / اسم الشركة*
3. Job title / المسمى الوظيفي
4. Work email* / البريد الإلكتروني للعمل*
5. Mobile (+966)* / رقم الجوال (+966)*
6. Number of employees / عدد الموظفين — 1–20 · 21–50 · 51–200 · 201–500 · 500+
7. Interested in* / مجال الاهتمام* — SAP Business One · ZATCA E-Invoicing · AI Solutions · Odoo · Data & BI · Industrial IoT · Other
   AR: SAP Business One · الفوترة الإلكترونية (زاتكا) · حلول الذكاء الاصطناعي · Odoo · البيانات وذكاء الأعمال · إنترنت الأشياء الصناعي · أخرى
   (Pre-select based on the page the form is on.)
8. Current system / النظام الحالي — Excel · Accounting software · Odoo · SAP Business One · Other ERP · None
   AR: Excel · برنامج محاسبي · Odoo · SAP Business One · نظام ERP آخر · لا يوجد
9. Message / رسالتك
10. Consent checkbox* — EN: "I agree to DWP Arabia contacting me and processing my data as described in the Privacy Policy." AR: "أوافق على تواصل DWP Arabia معي ومعالجة بياناتي وفق سياسة الخصوصية."
Button: **Send** / **إرسال** → redirect to `/thank-you/` (noindex) which fires `generate_lead` and shows: EN "Thank you — our team will contact you within one business day." AR "شكراً لك — سيتواصل معك فريقنا خلال يوم عمل واحد." + WhatsApp button.

### B5. CTA band (end of most pages)
EN: **See SAP Business One running on a Saudi company scenario.** [Book a Free Demo] [WhatsApp Us]
AR: **شاهد SAP Business One يعمل على سيناريو شركة سعودية.** [احجز عرضاً مجانياً] [تواصل عبر واتساب]

### B6. Trust strip (home + SAP pages)
- SAP Partner badge · Odoo Silver Partner badge · Future Factories Program Approved Service Provider badge · "[CONFIRM: 4] SAP Business One certified consultants" / "[CONFIRM: 4] مستشارين معتمدين في SAP Business One" · "Part of the DWP group since 1999" / "جزء من مجموعة DWP منذ 1999" · "Riyadh-based team" / "فريق في الرياض"
- Client logos row [CONFIRM: logos with written permission + alt text = company name].

### B7. Testimonials block
Only render when real, approved testimonials exist. **Never** show "No Post Found". Hide the block if empty.

---

## PART C — Pages (content + SEO)

Format for every page: **SEO** (title, meta, H1, keywords, schema) → **Sections** in order, English then Arabic. Use the shared components from Part B where named. Write Arabic exactly as given (native copy, not machine translation). Where a section gives English only, write the Arabic natively in the same tone and wrap it in an `[AR-REVIEW]` marker (listed in `CONFIRM.md`) so DWP can check it.

---

### C1. Home — `/` · `/ar/`

**SEO**
- Title EN: `SAP Business One Partner in Saudi Arabia | DWP Arabia`
- Title AR: `شريك SAP Business One في السعودية | DWP Arabia`
- Meta EN: `SAP Business One implementation with AI for Saudi companies. ZATCA-ready ERP, local team in Riyadh. Book a free demo.`
- Meta AR: `تطبيق نظام SAP Business One مع الذكاء الاصطناعي للشركات السعودية. متوافق مع زاتكا وفريق محلي في الرياض. احجز عرضاً مجانياً.`
- Keywords: SAP Business One Saudi Arabia, ERP Saudi Arabia, SAP partner Riyadh / ساب بزنس ون السعودية، نظام ERP، شريك SAP الرياض
- Schema: Organization, WebSite, LocalBusiness

**1. Hero**
- H1 EN: **SAP Business One & AI for Growing Saudi Companies**
- H1 AR: **SAP Business One والذكاء الاصطناعي للشركات السعودية النامية**
- Sub EN: One system for finance, sales, inventory and production — implemented by a certified team in Riyadh, ready for ZATCA, and powered by AI.
- Sub AR: نظام واحد للمالية والمبيعات والمخزون والإنتاج — يطبقه فريق معتمد في الرياض، جاهز لمتطلبات زاتكا، ومدعوم بالذكاء الاصطناعي.
- Buttons: [Book a Free Demo / احجز عرضاً مجانياً] [Free ERP Assessment / تقييم ERP مجاني]
- Visual: SAP Business One dashboard screenshot on a laptop (Arabic UI on the AR page). Badge: SAP Partner.

**2. Trust strip** → component B6.

**3. Problem → solution**
- H2 EN: **Still running your business on Excel and disconnected systems?**
- H2 AR: **هل ما زالت شركتك تعمل على Excel وأنظمة غير مترابطة؟**
- Three cards (EN / AR):
  1. **Late, manual reports** — Month-end takes weeks and numbers don't match. / **تقارير متأخرة ويدوية** — إغلاق الشهر يستغرق أسابيع والأرقام غير متطابقة.
  2. **No stock visibility** — You don't know what's in the warehouse until someone counts. / **لا رؤية للمخزون** — لا تعرف ما في المستودع حتى يتم الجرد.
  3. **Compliance pressure** — ZATCA e-invoicing, VAT and audits need clean, connected data. / **ضغط الامتثال** — الفوترة الإلكترونية والضريبة والتدقيق تتطلب بيانات دقيقة ومترابطة.
- Closing line EN: SAP Business One puts it all in one system — with one version of the truth. AR: SAP Business One يجمع كل ذلك في نظام واحد — بنسخة واحدة من الحقيقة.

**4. What SAP Business One covers** (icon grid, 8 tiles, each links to `/sap-business-one/`)
EN: Finance & Accounting · Sales & CRM · Purchasing · Inventory & Warehouses · Production & MRP · Projects & Service · Reporting & Dashboards · ZATCA E-Invoicing
AR: المالية والمحاسبة · المبيعات وإدارة العملاء · المشتريات · المخزون والمستودعات · الإنتاج وتخطيط المواد · المشاريع والخدمات · التقارير ولوحات المتابعة · الفوترة الإلكترونية (زاتكا)

**5. Why DWP Arabia**
- H2 EN: **Why Saudi companies choose DWP Arabia** · AR: **لماذا تختار الشركات السعودية DWP Arabia**
- Four points:
  1. **Certified SAP Business One team** — [CONFIRM: 4] certified consultants in Riyadh. / **فريق معتمد في SAP Business One** — [CONFIRM: 4] مستشارين معتمدين في الرياض.
  2. **Built for Saudi rules** — ZATCA, VAT, GOSI and Mudad-ready processes. / **مصمم للأنظمة السعودية** — عمليات جاهزة لزاتكا والضريبة والتأمينات ومدد.
  3. **AI on top of your ERP** — forecasting, Arabic assistants, document automation. / **ذكاء اصطناعي فوق نظامك** — التنبؤ والمساعدات الذكية بالعربية وأتمتة المستندات.
  4. **Backed by a large group** — part of the DWP group since 1999, 4,500+ people across the region. / **مدعومون بمجموعة كبيرة** — جزء من مجموعة DWP منذ 1999 بأكثر من 4,500 موظف في المنطقة. [CONFIRM group facts]

**6. ZATCA block**
- H2 EN: **ZATCA Phase 2 e-invoicing, built in** · AR: **الفوترة الإلكترونية المرحلة الثانية، جاهزة ضمن النظام**
- Text EN: Connect SAP Business One to ZATCA's Fatoora platform and issue compliant e-invoices from day one. AR: اربط SAP Business One بمنصة فاتورة وأصدر فواتير إلكترونية متوافقة من اليوم الأول.
- Link: [Check your ZATCA readiness → / تحقق من جاهزيتك لزاتكا ←] `/sap-business-one/zatca-e-invoicing/`

**7. AI teaser**
- H2 EN: **Make your ERP data work harder with AI** · AR: **اجعل بيانات نظامك تعمل أكثر بالذكاء الاصطناعي**
- 3 cards linking to AI pages: AI for Your ERP · Predictive Analytics · Arabic AI Assistants (AR: الذكاء الاصطناعي لنظامك · التحليلات التنبؤية · المساعدات الذكية بالعربية)

**8. Industries** — 4 cards → industry pages: Distribution & Trading · Manufacturing · Retail · Contracting & Services (AR: التوزيع والتجارة · التصنيع · التجزئة · المقاولات والخدمات)

**9. How we work** (4 steps)
EN: 1 Discover — free assessment of your processes · 2 Design — SAP Business One fit to your business · 3 Deploy — data migration, training, go-live · 4 Support — local Arabic support after go-live
AR: 1 الاستكشاف — تقييم مجاني لعملياتك · 2 التصميم — مواءمة SAP Business One مع أعمالك · 3 التشغيل — نقل البيانات والتدريب والإطلاق · 4 الدعم — دعم محلي بالعربية بعد الإطلاق

**10. Case studies** (2 cards) [CONFIRM: real projects] — hide the section until available.

**11. Testimonials** → B7.

**12. FAQ** (visible + FAQPage schema)
1. EN Q: What is SAP Business One? A: SAP Business One is an ERP system from SAP designed for small and mid-sized companies. It connects finance, sales, purchasing, inventory and production in one system.
   AR س: ما هو SAP Business One؟ ج: هو نظام تخطيط موارد مؤسسات (ERP) من SAP مصمم للشركات الصغيرة والمتوسطة، يربط المالية والمبيعات والمشتريات والمخزون والإنتاج في نظام واحد.
2. EN Q: Is SAP Business One compliant with ZATCA e-invoicing? A: Yes. DWP Arabia connects SAP Business One to ZATCA's Fatoora platform for Phase 2 integration.
   AR س: هل SAP Business One متوافق مع الفوترة الإلكترونية؟ ج: نعم، تقوم DWP Arabia بربط SAP Business One بمنصة فاتورة لتلبية متطلبات المرحلة الثانية.
3. EN Q: Does SAP Business One work in Arabic? A: Yes. It supports Arabic printouts and documents, and our team delivers training and support in Arabic and English.
   AR س: هل يدعم SAP Business One اللغة العربية؟ ج: نعم، يدعم المستندات والمطبوعات بالعربية، ويقدم فريقنا التدريب والدعم بالعربية والإنجليزية.
4. EN Q: How long does an implementation take? A: It depends on scope. We give you a clear timeline after a free assessment of your processes.
   AR س: كم يستغرق تطبيق النظام؟ ج: يعتمد على نطاق المشروع، ونقدم لك جدولاً زمنياً واضحاً بعد تقييم مجاني لعملياتك.
5. EN Q: Do you also implement Odoo? A: Yes. We implement both and recommend the system that best fits your size, industry and budget.
   AR س: هل تطبقون نظام Odoo أيضاً؟ ج: نعم، نطبق النظامين ونوصي بالأنسب لحجم شركتك وقطاعها وميزانيتها.

**13. Lead form** → B4. **CTA band** → B5.

---

### C2. About — `/about/`

**SEO**
- Title EN: `About DWP Arabia | SAP & AI Partner in Riyadh` · AR: `من نحن | DWP Arabia شريك SAP في الرياض`
- Meta EN: `Part of the DWP group since 1999, 4,500+ people across KSA, UAE, Qatar and Pakistan. Certified SAP Business One team in Riyadh.`
- Meta AR: `جزء من مجموعة DWP منذ 1999 بأكثر من 4,500 موظف. فريق معتمد في SAP Business One بالرياض.`
- H1 EN: **About DWP Arabia** · AR: **من نحن**
- Schema: Organization, AboutPage, BreadcrumbList

**Sections**
1. **Intro** — EN: DWP Arabia helps Saudi companies run better with SAP Business One, AI and modern data platforms. We are part of the DWP group, founded in 1999, with 4,500+ people across Saudi Arabia, the UAE, Qatar and Pakistan. AR: تساعد DWP Arabia الشركات السعودية على إدارة أعمالها بكفاءة أعلى عبر SAP Business One والذكاء الاصطناعي ومنصات البيانات الحديثة. نحن جزء من مجموعة DWP التي تأسست عام 1999 ويعمل بها أكثر من 4,500 موظف في السعودية والإمارات وقطر وباكستان. [CONFIRM facts]
2. **Our focus** (3 cards) — SAP Business One / AI / Saudi compliance. AR: SAP Business One / الذكاء الاصطناعي / الامتثال للأنظمة السعودية.
3. **Mission** — EN: Give growing Saudi companies the same quality of systems as large enterprises — delivered locally, in Arabic and English. AR: أن نمنح الشركات السعودية النامية أنظمة بجودة الشركات الكبرى — بتنفيذ محلي وباللغتين العربية والإنجليزية.
4. **Leadership** — cards with photo, name, title, LinkedIn link. [CONFIRM: names, titles, photos]. Replace the old text "Leadership information will be shared separately".
5. **Our certified team** — EN: [CONFIRM: 4] SAP Business One certified consultants, presales and sales specialists based in Riyadh. AR: [CONFIRM: 4] مستشارين معتمدين في SAP Business One مع فريق ما قبل البيع والمبيعات في الرياض.
6. **Partners strip** → link to `/partners/`. 7. CTA band B5.

---

### C3. Partners — `/partners/`

**SEO**
- Title EN: `Technology Partners: SAP, Odoo, Huawei | DWP Arabia` · AR: `شركاؤنا التقنيون: SAP وOdoo وHuawei | DWP Arabia`
- Meta EN: `DWP Arabia is an SAP Partner for SAP Business One in Saudi Arabia, an Odoo partner and a Huawei partner.`
- Meta AR: `DWP Arabia شريك SAP لنظام SAP Business One في السعودية، وشريك Odoo وHuawei.`
- H1 EN: **Our Technology Partners** · AR: **شركاؤنا التقنيون**
- Schema: Organization, BreadcrumbList

**Sections** — one card per partner, in this order. Each card: official badge, 2-line text, link.
1. **SAP** — EN: SAP Partner delivering SAP Business One implementation, support and add-ons in Saudi Arabia. → `/sap-business-one/` AR: شريك SAP لتطبيق ودعم نظام SAP Business One وإضافاته في السعودية.
2. **Odoo** — EN: Odoo partner for companies that need a flexible, cost-effective ERP. → `/erp/odoo/` AR: شريك Odoo للشركات التي تحتاج نظام ERP مرناً وبتكلفة مناسبة.
3. **Huawei** — EN: Huawei partner for cloud and infrastructure. [CONFIRM partner scope] AR: شريك Huawei في الحوسبة السحابية والبنية التحتية.
4. Microsoft · Cisco · Schneider Electric · Fortinet — listed on the current site (Appendix 2). [CONFIRM which are formal partnerships]
5. Odoo card shows the **Odoo Silver Partner** badge. Every logo needs `alt` = partner name.

---

### C4. Clients & Success Stories — `/our-clients/`

**SEO**
- Title EN: `Clients & Success Stories in Saudi Arabia | DWP Arabia` · AR: `عملاؤنا وقصص النجاح | DWP Arabia`
- Meta EN: `How Saudi companies run finance, inventory and production on ERP with DWP Arabia. Read the case studies.`
- Meta AR: `كيف تدير الشركات السعودية المالية والمخزون والإنتاج مع DWP Arabia. اقرأ قصص النجاح.`
- H1 EN: **Trusted by Companies Across the Kingdom** · AR: **موثوقون لدى الشركات في جميع أنحاء المملكة**
- Schema: CollectionPage, BreadcrumbList (Review schema only for real, approved quotes)

**Sections**
1. Logo wall with names (alt text) [CONFIRM: approved client list]. Do NOT reuse the current list as-is: it mixes vendors (SAP, Oracle, Microsoft…) and group clients from other countries (see Appendix 4, issue 3).
2. Case study cards — template per case (EN/AR): Client · Industry · Challenge · Solution · Results (numbers) · Quote. Each case gets its own page `/our-clients/<client-slug>/` with Article schema. [CONFIRM: 2–3 cases; Odoo projects are fine until SAP projects exist]
3. CTA band B5.

---

### C5. Contact — `/contact/`

**SEO**
- Title EN: `Contact DWP Arabia, Riyadh | Book an SAP Demo` · AR: `تواصل مع DWP Arabia في الرياض`
- Meta EN: `Talk to our SAP Business One team in Riyadh. Call, WhatsApp or send a message today.`
- Meta AR: `تحدث مع فريق SAP Business One في الرياض عبر الهاتف أو واتساب أو أرسل رسالتك اليوم.`
- H1 EN: **Contact Us** · AR: **تواصل معنا**
- Schema: LocalBusiness (name, address, geo, telephone, openingHours), BreadcrumbList

**Sections**
1. Intro EN: We're in Riyadh and reply within one business day. AR: نحن في الرياض ونرد خلال يوم عمل واحد.
2. Contact cards: Phone · WhatsApp · Email · Address · Hours (from B2 — Saudi time, not PST).
3. Lead form B4. 4. Google Map embed (Hala Center, Anas bin Malik Rd).

---

### C6. Book a Demo — `/book-a-demo/`

**SEO**
- Title EN: `Book a Free SAP Business One Demo | DWP Arabia` · AR: `احجز عرضاً مجانياً لـ SAP Business One`
- Meta EN: `See SAP Business One running on a Saudi company scenario, in Arabic or English. 45 minutes, no cost.`
- Meta AR: `شاهد SAP Business One يعمل على سيناريو شركة سعودية بالعربية أو الإنجليزية. 45 دقيقة مجاناً.`
- H1 EN: **Book Your Free SAP Business One Demo** · AR: **احجز عرضك التوضيحي المجاني لـ SAP Business One**
- Schema: WebPage, BreadcrumbList

**Sections**
1. Left: what you'll see (EN/AR):
   - Your daily processes in SAP Business One — sales, purchasing, inventory, finance / عملياتك اليومية في SAP Business One — المبيعات والمشتريات والمخزون والمالية
   - A ZATCA-compliant e-invoice issued live / إصدار فاتورة إلكترونية متوافقة مع زاتكا مباشرة
   - Real-time dashboards and reports / لوحات متابعة وتقارير لحظية
   - AI examples on ERP data / أمثلة على الذكاء الاصطناعي على بيانات النظام
   - Q&A with a certified consultant / أسئلة وأجوبة مع مستشار معتمد
2. Right: lead form B4 + preferred date/time + language (Arabic / English) + format (Online / At your office). Fires `book_demo`.

---

### C7. Thank you — `/thank-you/` (noindex)
Text from B4. Add: "Meanwhile, read: ZATCA Phase 2 guide →" / "في الأثناء، اقرأ: دليل المرحلة الثانية من الفوترة الإلكترونية ←".

---

### C8. Privacy Policy — `/privacy-policy/`

**SEO** — Title EN `Privacy Policy | DWP Arabia` · AR `سياسة الخصوصية | DWP Arabia` · H1 Privacy Policy / سياسة الخصوصية
**Content** — [CONFIRM: DWP legal to provide text aligned with the Saudi PDPL]. Must cover: what data the forms collect, why, how long it's kept, cookies and tracking tools used, who receives it (CRM, email), user rights, contact for privacy requests. Link from every form and from the cookie banner.

---

### C9. SAP Business One (pillar) — `/sap-business-one/`

**SEO**
- Title EN: `SAP Business One in Saudi Arabia | ERP for SMEs` · AR: `نظام SAP Business One في السعودية`
- Meta EN: `One ERP for finance, sales, inventory and production. Implemented in Arabic and English by a certified Riyadh team.`
- Meta AR: `نظام ERP واحد للمالية والمبيعات والمخزون والإنتاج، يطبقه فريق معتمد في الرياض بالعربية والإنجليزية.`
- H1 EN: **SAP Business One for Saudi Companies** · AR: **SAP Business One للشركات السعودية**
- Keywords: SAP Business One Saudi Arabia, SAP B1 KSA, ERP for SMEs / ساب بزنس ون، نظام SAP للشركات الصغيرة والمتوسطة
- Schema: Service (provider DWP Arabia, areaServed SA), FAQPage, BreadcrumbList

**Sections**
1. **Hero** — Sub EN: The ERP trusted by small and mid-sized companies worldwide — set up for Saudi rules, in Arabic and English. AR: نظام ERP تعتمد عليه الشركات الصغيرة والمتوسطة حول العالم — مهيأ للأنظمة السعودية وباللغتين العربية والإنجليزية. Buttons: Book a Demo · Free Assessment.
2. **What is SAP Business One?** — EN: SAP Business One is an ERP from SAP built for growing companies. It replaces spreadsheets and separate tools with one system, so every department works from the same real-time data. It runs on SAP HANA or Microsoft SQL Server, on-premise or in the cloud. AR: SAP Business One هو نظام ERP من SAP مصمم للشركات النامية، يستبدل الجداول والأدوات المتفرقة بنظام واحد، فتعمل جميع الإدارات على نفس البيانات اللحظية. يعمل على SAP HANA أو Microsoft SQL Server، داخل الشركة أو على السحابة.
3. **Modules** (8 cards, 2 lines each):
   - Finance & Accounting — general ledger, banking, VAT, budgets, multi-currency. / المالية والمحاسبة — الأستاذ العام والبنوك والضريبة والموازنات وتعدد العملات.
   - Sales & CRM — quotations to invoices, customer history, opportunities. / المبيعات وإدارة العملاء — من عرض السعر إلى الفاتورة وسجل العملاء والفرص.
   - Purchasing — requests, POs, goods receipt, supplier invoices. / المشتريات — الطلبات وأوامر الشراء والاستلام وفواتير الموردين.
   - Inventory & Warehouses — multiple warehouses, batches, serials, bin locations. / المخزون والمستودعات — مستودعات متعددة والدفعات والأرقام التسلسلية والمواقع.
   - Production & MRP — bills of materials, production orders, material planning. / الإنتاج وتخطيط المواد — قوائم المواد وأوامر الإنتاج وتخطيط الاحتياجات.
   - Projects & Service — project costing, service contracts and calls. / المشاريع والخدمات — تكاليف المشاريع وعقود ونداءات الخدمة.
   - Reporting & Analytics — real-time dashboards, Crystal Reports, Excel. / التقارير والتحليلات — لوحات لحظية وتقارير Crystal وExcel.
   - Mobile & Cloud — access from anywhere, on phone or browser. / الجوال والسحابة — وصول من أي مكان عبر الجوال أو المتصفح.
4. **Built for Saudi Arabia** — bullets EN/AR: ZATCA Phase 2 e-invoicing / الفوترة الإلكترونية المرحلة الثانية · VAT reporting / تقارير ضريبة القيمة المضافة · Arabic documents and printouts / مستندات ومطبوعات بالعربية · Hijri and Gregorian dates on documents [CONFIRM] / التاريخ الهجري والميلادي على المستندات · HR & payroll add-ons for GOSI and Mudad [CONFIRM which add-on] / إضافات الموارد البشرية والرواتب للتأمينات ومدد. → link `/built-for-saudi-arabia/`
5. **Is SAP Business One right for you?** — EN: Best for companies with roughly 10 to 500 users [CONFIRM range] that manage stock, have several branches or plan to grow fast. AR: مناسب للشركات التي لديها مخزون أو عدة فروع أو تخطط لنمو سريع. Button: Take the free assessment.
6. **Our services** (cards linking to cluster pages): Implementation · ZATCA E-Invoicing · Support & Upgrades · Switch from accounting software · AI for Your ERP · Pricing.
7. **Industries** (4 cards). 8. Trust strip B6. 9. Testimonials B7.
10. **FAQ** (+schema):
   - EN Q: Is SAP Business One only for small companies? A: No. It's built for small and mid-sized companies and scales with multiple branches, warehouses and companies. / AR س: هل SAP Business One للشركات الصغيرة فقط؟ ج: لا، هو مصمم للشركات الصغيرة والمتوسطة ويتوسع مع تعدد الفروع والمستودعات والشركات.
   - EN Q: Cloud or on-premise? A: Both. We help you choose based on cost, security and IT team. / AR س: سحابي أم داخل الشركة؟ ج: الخياران متاحان، ونساعدك في الاختيار حسب التكلفة والأمان وفريق تقنية المعلومات.
   - EN Q: Can it connect to our other systems? A: Yes, through the SAP Business One Service Layer and standard integration tools — for example e-commerce, POS and banks. / AR س: هل يمكن ربطه بأنظمتنا الأخرى؟ ج: نعم، عبر Service Layer وأدوات الربط القياسية، مثل المتاجر الإلكترونية ونقاط البيع والبنوك.
   - EN Q: Who supports us after go-live? A: Our Riyadh team, in Arabic and English. / AR س: من يدعمنا بعد الإطلاق؟ ج: فريقنا في الرياض بالعربية والإنجليزية.
11. Lead form B4 (pre-select SAP Business One) + CTA band B5.

---

### C10. Implementation — `/sap-business-one/implementation/`

**SEO**
- Title EN: `SAP Business One Implementation in KSA | DWP Arabia` · AR: `تطبيق SAP Business One في السعودية`
- Meta EN: `Our step-by-step method takes you live on SAP Business One with data migration, training and Arabic support.`
- Meta AR: `منهجيتنا خطوة بخطوة تنقلك إلى SAP Business One مع نقل البيانات والتدريب والدعم بالعربية.`
- H1 EN: **SAP Business One Implementation** · AR: **تطبيق نظام SAP Business One**
- Schema: Service, HowTo (the 6 phases), BreadcrumbList

**Sections**
1. Intro EN: A successful ERP project is 20% software and 80% method. We follow a proven phased approach, with sign-off at every phase. AR: نجاح مشروع ERP يعتمد على المنهجية أكثر من البرنامج. نتبع منهجية مرحلية مجربة مع اعتماد رسمي في نهاية كل مرحلة.
2. **Our 6 phases** (timeline graphic):
   1. Discover — workshops to map your processes / الاستكشاف — ورش عمل لفهم عملياتك
   2. Design — solution blueprint and sign-off / التصميم — مخطط الحل واعتماده
   3. Build — configuration, reports, add-ons / البناء — الإعداد والتقارير والإضافات
   4. Migrate & test — data migration and user testing / نقل البيانات والاختبار — نقل البيانات واختبار المستخدمين
   5. Train & go-live — key-user and end-user training, cut-over / التدريب والإطلاق — تدريب المستخدمين والانتقال للنظام
   6. Support — hypercare, then ongoing support / الدعم — متابعة مكثفة ثم دعم مستمر
3. **What's included** — project manager, certified consultants, data migration templates, Arabic/English training, documentation, phase sign-offs. / مدير مشروع، مستشارون معتمدون، قوالب نقل البيانات، تدريب بالعربية والإنجليزية، توثيق، واعتماد كل مرحلة.
4. **Typical timeline** — EN: Most projects go live in [CONFIRM: 8–16] weeks depending on scope. AR: تنطلق معظم المشاريع خلال [CONFIRM] أسابيع حسب النطاق.
5. FAQ (3): data migration from old system / who we need from your side / training language. Write answers in the same style as C1.
6. Lead form B4 + CTA B5.

---

### C11. ZATCA E-Invoicing — `/sap-business-one/zatca-e-invoicing/`

**SEO**
- Title EN: `ZATCA Phase 2 E-Invoicing for SAP Business One` · AR: `الفوترة الإلكترونية المرحلة الثانية مع SAP`
- Meta EN: `Connect SAP Business One to ZATCA Fatoora for Phase 2 integration. Check your wave and get ready on time.`
- Meta AR: `اربط SAP Business One بمنصة فاتورة لمتطلبات المرحلة الثانية. اعرف موعد موجتك واستعد في الوقت المناسب.`
- H1 EN: **ZATCA Phase 2 E-Invoicing with SAP Business One** · AR: **الفوترة الإلكترونية (المرحلة الثانية) مع SAP Business One**
- Keywords: ZATCA phase 2 SAP, Fatoora integration, e-invoicing Saudi / الفوترة الإلكترونية زاتكا، الربط مع فاتورة، المرحلة الثانية
- Schema: Service, FAQPage, BreadcrumbList

**Sections**
1. Intro EN: ZATCA's Phase 2 (Integration) requires taxpayers to connect their invoicing system to the Fatoora platform, in waves. When your wave is announced, you have a fixed window to comply. AR: تتطلب المرحلة الثانية (الربط والتكامل) من هيئة الزكاة والضريبة والجمارك ربط نظام الفوترة بمنصة فاتورة على شكل موجات، ولكل موجة مهلة محددة للامتثال.
2. **What Phase 2 requires** — bullets: XML invoices in the approved format · cryptographic stamp and QR code · real-time clearance for B2B tax invoices and reporting for simplified invoices · device onboarding with ZATCA. / فواتير بصيغة XML المعتمدة · ختم تشفيري ورمز QR · اعتماد فوري لفواتير المنشآت والإبلاغ عن الفواتير المبسطة · تسجيل الأجهزة لدى الهيئة. [Have the ZATCA consultant review this wording before launch]
3. **How we do it in SAP Business One** — EN: We connect SAP Business One directly to Fatoora, so invoices are cleared or reported automatically when you post them — no double entry. AR: نربط SAP Business One مباشرة بمنصة فاتورة، فتُعتمد الفواتير أو يُبلَّغ عنها تلقائياً عند ترحيلها دون إدخال مزدوج.
4. **Steps** — readiness check → setup and onboarding → testing in sandbox → go-live → monitoring. / فحص الجاهزية ← الإعداد والتسجيل ← الاختبار في البيئة التجريبية ← التشغيل ← المتابعة.
5. **Already received your wave notice?** CTA box — EN: Talk to us now — we'll plan your go-live before the deadline. AR: تواصل معنا الآن لنخطط للتشغيل قبل الموعد النهائي.
6. FAQ (4): What is Phase 2? · How do I know my wave? (ZATCA notifies taxpayers; check the ZATCA website and your notifications) · Does it work with older SAP Business One versions? [CONFIRM] · What if we're not on SAP yet? (we can implement SAP Business One with e-invoicing ready from day one).
7. Lead form B4 (pre-select ZATCA E-Invoicing) + CTA B5.

---

### C12. Support & Upgrades — `/sap-business-one/support/`

**SEO**
- Title EN: `SAP Business One Support & Upgrades in KSA` · AR: `دعم وتحديث SAP Business One`
- Meta EN: `Already on SAP Business One? Get local Arabic support, version upgrades and fixes from a Riyadh team.`
- Meta AR: `تستخدم SAP Business One؟ احصل على دعم محلي بالعربية وتحديثات الإصدارات من فريق في الرياض.`
- H1 EN: **SAP Business One Support** · AR: **دعم SAP Business One**
- Keywords: SAP Business One support Riyadh, change SAP partner, SAP B1 upgrade / دعم SAP Business One، تغيير شريك SAP
- Schema: Service, FAQPage, BreadcrumbList

**Sections**
1. Hero sub EN: Slow responses? Old version? Reports that never got finished? Our Riyadh team takes over and gets your system back on track. AR: استجابة بطيئة؟ إصدار قديم؟ تقارير لم تكتمل؟ يتولى فريقنا في الرياض نظامك ويعيده إلى المسار الصحيح.
2. **Services**: helpdesk support · version upgrades (incl. move to SAP HANA) · new reports and dashboards · user training · health check · ZATCA Phase 2 for existing systems. / الدعم الفني · ترقية الإصدار (بما في ذلك الانتقال إلى SAP HANA) · تقارير ولوحات جديدة · تدريب المستخدمين · فحص صحة النظام · الفوترة الإلكترونية للأنظمة القائمة.
3. **Free system health check** offer box — EN: We review your SAP Business One setup and send you a short report of issues and quick wins. AR: نراجع إعدادات SAP Business One لديك ونرسل لك تقريراً مختصراً بالمشكلات والتحسينات السريعة. Button → form.
4. **Switching partners is simple** — 3 steps: health check → handover plan → support starts. / فحص النظام ← خطة الانتقال ← بدء الدعم.
5. Support plans [CONFIRM: plan names, hours, response times — do not publish numbers until approved].
6. FAQ (3): Can we change our SAP partner? · Do we keep our licences? (licences stay with your company) · How fast do you respond? [CONFIRM].
7. Lead form B4 + CTA B5.

---

### C13. Switch to SAP Business One — `/sap-business-one/switch/`

**SEO**
- Title EN: `Move from Excel or Accounting Software to SAP B1` · AR: `الانتقال إلى SAP Business One`
- Meta EN: `Outgrown Excel or basic accounting software? Here's how Saudi companies move to SAP Business One with no data loss.`
- Meta AR: `هل تجاوزت شركتك Excel أو البرنامج المحاسبي؟ تعرّف كيف تنتقل إلى SAP Business One دون فقدان بياناتك.`
- H1 EN: **Outgrown Your Accounting Software?** · AR: **هل تجاوزت شركتك برنامجها المحاسبي؟**
- Schema: Article, FAQPage, BreadcrumbList

**Sections**
1. **7 signs you've outgrown your system** (checklist): month-end takes too long · stock numbers are never right · sales and accounts use different data · reports need Excel work · you're adding branches or warehouses · ZATCA Phase 2 is coming · you can't see profit per product, customer or project. / الإغلاق الشهري يطول · أرقام المخزون غير دقيقة · المبيعات والحسابات تعمل ببيانات مختلفة · التقارير تحتاج عملاً على Excel · تضيف فروعاً أو مستودعات · المرحلة الثانية من الفوترة قادمة · لا ترى الربحية حسب المنتج أو العميل أو المشروع.
2. **What changes with SAP Business One** — before/after table (EN/AR).
3. **How we move your data** — opening balances, customers, suppliers, items, open documents; tested before go-live. / الأرصدة الافتتاحية والعملاء والموردون والأصناف والمستندات المفتوحة، مع الاختبار قبل الإطلاق.
4. CTA: Take the free assessment. FAQ (3). Lead form B4.

---

### C14. Pricing — `/sap-business-one/pricing/`

**SEO**
- Title EN: `SAP Business One Price in Saudi Arabia | Get a Quote` · AR: `سعر نظام SAP Business One في السعودية`
- Meta EN: `What drives SAP Business One cost: users, licence type, hosting and implementation scope. Get a tailored quote.`
- Meta AR: `ما الذي يحدد تكلفة SAP Business One: عدد المستخدمين ونوع الترخيص والاستضافة ونطاق التطبيق. احصل على عرض سعر.`
- H1 EN: **How Much Does SAP Business One Cost?** · AR: **كم تكلفة نظام SAP Business One؟**
- Schema: Article, FAQPage, BreadcrumbList

**Sections** (no price list — rule A7)
1. Intro EN: There's no single price — the cost depends on a few clear factors. Here's what they are, so you can budget with confidence. AR: لا يوجد سعر واحد، فالتكلفة تعتمد على عوامل واضحة. إليك هذه العوامل لتخطط لميزانيتك بثقة.
2. **The 5 cost drivers**: number and type of users (e.g. Professional, Limited, Starter) · database (SAP HANA or SQL Server) · cloud or on-premise hosting · implementation scope (modules, reports, integrations) · add-ons and annual support. / عدد المستخدمين ونوعهم · قاعدة البيانات · الاستضافة السحابية أو الداخلية · نطاق التطبيق · الإضافات والدعم السنوي.
3. **Subscription vs perpetual** — short neutral explanation [CONFIRM options available].
4. **Get your tailored quote** form (B4 + users count field).
5. FAQ (3): Is there a monthly option? · What's included in implementation? · Are there hidden costs?

---

### C15. SAP Business One vs Odoo — `/sap-business-one/vs-odoo/`

**SEO**
- Title EN: `SAP Business One vs Odoo: Which ERP Fits You?` · AR: `مقارنة SAP Business One مع Odoo`
- Meta EN: `We implement both. An honest comparison by company size, industry, budget and growth plans.`
- Meta AR: `نطبق النظامين. مقارنة واضحة حسب حجم الشركة والقطاع والميزانية وخطط النمو.`
- H1 EN: **SAP Business One vs Odoo** · AR: **SAP Business One أم Odoo؟**
- Schema: Article, BreadcrumbList

**Sections**
1. Intro EN: As a partner for both, we don't need to push one. Here's how they compare. AR: بصفتنا شريكاً للنظامين، لا نحتاج لترجيح أحدهما. إليك المقارنة.
2. **Comparison table** (rows): Best for · Strengths · Inventory & production depth · Customisation · Typical budget · Brand & audit perception · Support model. Content guidance: SAP Business One = structured, audit-friendly, strong inventory/production/finance, backed by SAP — best for companies planning growth, multiple branches, or that need enterprise credibility. Odoo = modular, flexible, often lower entry cost — best for smaller teams and simpler processes. [Presales to review final wording]
3. **Our recommendation process** — free assessment → we recommend one in writing with reasons.
4. CTA: Get a recommendation for your company. Lead form B4.

---

### C16. Free ERP Assessment — `/sap-business-one/free-assessment/` (lead magnet)

**SEO**
- Title EN: `Free ERP Readiness Assessment | DWP Arabia` · AR: `تقييم مجاني لجاهزية ERP`
- Meta EN: `Answer 10 questions and get a free report on whether SAP Business One fits your company, plus next steps.`
- Meta AR: `أجب عن 10 أسئلة واحصل على تقرير مجاني يوضح مدى ملاءمة SAP Business One لشركتك والخطوات التالية.`
- H1 EN: **Is Your Company Ready for SAP Business One?** · AR: **هل شركتك جاهزة لـ SAP Business One؟**
- Schema: WebPage, BreadcrumbList

**Build:** a multi-step form (one question per screen, progress bar), then contact fields (B4 fields 1–5 + consent), then a result screen and email. Fires `assessment_complete`.
**Questions (EN / AR)** — single choice unless noted:
1. Industry / القطاع — Distribution & trading · Manufacturing · Retail · Contracting & services · Other
2. Employees / عدد الموظفين — 1–20 · 21–50 · 51–200 · 201–500 · 500+
3. Current system / النظام الحالي — Excel · Accounting software · Odoo · Other ERP · SAP Business One
4. Branches / عدد الفروع — 1 · 2–5 · 6+
5. Do you hold stock? / هل لديكم مخزون؟ — Yes, one warehouse · Yes, several · No
6. Do you manufacture? / هل لديكم تصنيع؟ — Yes · No
7. ZATCA Phase 2 status / وضع المرحلة الثانية — Already integrated · Wave notified · Not yet · Don't know
8. Biggest pain (multi) / أكبر التحديات — Slow month-end · Stock accuracy · Reporting · Compliance · Growth/branches
9. Timeline / الإطار الزمني — Within 3 months · 3–6 months · 6–12 months · Just researching
10. Decision maker / صاحب القرار — Me · Finance manager · CEO/owner · IT · Committee
**Result logic:** score 0–100 (more stock/branches/manufacturing/pain = higher fit). Result bands: "Strong fit for SAP Business One" / "Good fit — let's check details" / "Odoo may suit you better right now" (the Odoo fallback). Each band shows 3 next steps + Book a Demo button. Send the answers to the CRM with the score.

---

### C17. AI (pillar) — `/ai/`

**SEO**
- Title EN: `AI Solutions for Saudi Businesses | DWP Arabia` · AR: `حلول الذكاء الاصطناعي للشركات السعودية`
- Meta EN: `Practical AI that works on your ERP data: forecasting, Arabic assistants and document automation, hosted in line with Saudi data rules.`
- Meta AR: `ذكاء اصطناعي عملي يعمل على بيانات نظامك: التنبؤ والمساعدات الذكية بالعربية وأتمتة المستندات، وفق أنظمة البيانات السعودية.`
- H1 EN: **Practical AI for Saudi Businesses** · AR: **ذكاء اصطناعي عملي للشركات السعودية**
- Schema: Service, BreadcrumbList

**Sections**
1. Hero sub EN: No hype — AI that saves hours, cuts errors and helps you decide faster, built on the data you already have in your ERP. AR: بلا مبالغة — ذكاء اصطناعي يوفر الساعات ويقلل الأخطاء ويسرّع قراراتك، مبني على البيانات الموجودة في نظامك.
2. **5 solution cards** → AI for Your ERP · Predictive Analytics · Arabic AI Assistants · Document Automation · Vision AI (one line each, EN/AR, as in their page subs below).
3. **How we start** — 1 Use-case workshop · 2 Proof of value in weeks · 3 Roll out and measure. / ورشة تحديد حالات الاستخدام · إثبات القيمة خلال أسابيع · التوسع والقياس.
4. **Your data stays in Saudi Arabia** — EN: We design AI solutions with data residency and PDPL in mind, and can host in-Kingdom. [CONFIRM hosting options] AR: نصمم حلول الذكاء الاصطناعي مع مراعاة إقامة البيانات ونظام حماية البيانات الشخصية، ويمكن الاستضافة داخل المملكة.
5. FAQ (3): Do we need a lot of data? · Does it work in Arabic? · Is our data used to train public models? (No — [CONFIRM per solution]).
6. Lead form B4 (pre-select AI Solutions) + CTA B5.

---

### C18. AI for Your ERP — `/ai/ai-for-sap-business-one/`

**SEO**
- Title EN: `AI for Your ERP | Smarter ERP in KSA` · AR: `الذكاء الاصطناعي لنظامك`
- Meta EN: `Ask your ERP questions in Arabic or English, forecast demand and spot issues early — AI built on your ERP data.`
- Meta AR: `اسأل نظامك بالعربية أو الإنجليزية، وتنبأ بالطلب واكتشف المشكلات مبكراً — ذكاء اصطناعي مبني على بيانات نظامك.`
- H1 EN: **AI on Top of Your ERP** · AR: **ذكاء اصطناعي فوق نظامك**
- Schema: Service, FAQPage, BreadcrumbList

**Sections**
1. Intro EN: Your SAP Business One already holds years of sales, stock and finance history. AI turns that history into answers and alerts. AR: يحتفظ SAP Business One بسنوات من بيانات المبيعات والمخزون والمالية، والذكاء الاصطناعي يحوّلها إلى إجابات وتنبيهات.
2. **Use cases** (cards, EN/AR):
   - Ask your ERP — "What were our top 10 customers last quarter?" answered in seconds, in Arabic or English. / اسأل نظامك — "من أفضل 10 عملاء في الربع الماضي؟" إجابة خلال ثوانٍ بالعربية أو الإنجليزية.
   - Demand & stock forecasting — know what to reorder and when. / التنبؤ بالطلب والمخزون — اعرف ماذا تطلب ومتى.
   - Cash-flow outlook — expected collections and payments. / توقعات التدفق النقدي — التحصيلات والمدفوعات المتوقعة.
   - Anomaly alerts — unusual prices, discounts or postings flagged automatically. / تنبيهات الحالات غير الطبيعية — أسعار أو خصومات أو قيود غير معتادة.
   - Supplier invoice capture — AI reads invoices and creates drafts in SAP Business One. / قراءة فواتير الموردين — الذكاء الاصطناعي يقرأ الفواتير وينشئ مسودات في النظام.
3. **How it connects** — through the secure API of your ERP; read-only by default; you approve any posting. / الربط عبر واجهة API آمنة في نظامك، بصلاحية قراءة افتراضياً، وأي ترحيل يتم بموافقتك.
4. FAQ (3) + Lead form B4 + CTA B5.

---

### C19. Predictive Analytics — `/ai/predictive-analytics/`
- Title EN: `Sales & Demand Forecasting with AI | DWP Arabia` · AR: `التنبؤ بالمبيعات والطلب بالذكاء الاصطناعي`
- Meta EN: `Forecast sales, stock and cash flow from your ERP history so you buy and plan with confidence.` · AR: `تنبأ بالمبيعات والمخزون والتدفق النقدي من بيانات نظامك لتشتري وتخطط بثقة.`
- H1 EN: **AI Forecasting for Sales, Stock and Cash** · AR: **التنبؤ بالمبيعات والمخزون والنقد بالذكاء الاصطناعي**
- Schema: Service, BreadcrumbList
- Sections: problem (overstock and stock-outs / فائض المخزون ونفاده) · what we forecast (sales by item/branch, reorder points, seasonality incl. Ramadan and seasons / المبيعات حسب الصنف والفرع، نقاط إعادة الطلب، المواسم بما فيها رمضان) · how it shows up (dashboards + weekly email) · Lead form B4.

### C20. Arabic AI Assistants — `/ai/arabic-ai-assistants/`
- Title EN: `Arabic AI Assistants for Business | DWP Arabia` · AR: `مساعد ذكي باللغة العربية للشركات`
- Meta EN: `AI assistants that answer staff and customers in Arabic and English, using your own documents and ERP data.` · AR: `مساعدات ذكية تجيب الموظفين والعملاء بالعربية والإنجليزية من مستنداتك وبيانات نظامك.`
- H1 EN: **Arabic AI Assistants for Your Team and Customers** · AR: **مساعدات ذكية بالعربية لفريقك وعملائك**
- Schema: Service, BreadcrumbList
- Sections: internal assistant (policies, procedures, ERP questions / السياسات والإجراءات وأسئلة النظام) · customer assistant on website/WhatsApp (order status, FAQs, lead capture / حالة الطلب والأسئلة الشائعة وجمع العملاء المحتملين) · understands Saudi dialect [CONFIRM] / يفهم اللهجة السعودية · hand-over to a human · Lead form B4.

### C21. Document Automation — `/ai/document-automation/`
- Title EN: `AI Invoice & Document Automation | DWP Arabia` · AR: `أتمتة الفواتير والمستندات بالذكاء الاصطناعي`
- Meta EN: `Read supplier invoices and documents with AI and post them to SAP Business One — less typing, fewer errors.` · AR: `اقرأ فواتير الموردين والمستندات بالذكاء الاصطناعي ورحّلها إلى SAP Business One — إدخال أقل وأخطاء أقل.`
- H1 EN: **AI Document Automation into Your ERP** · AR: **أتمتة المستندات بالذكاء الاصطناعي داخل نظامك**
- Schema: Service, BreadcrumbList
- Sections: documents handled (supplier invoices in Arabic/English, delivery notes, POs, expense receipts / فواتير الموردين، مذكرات التسليم، أوامر الشراء، إيصالات المصروفات) · flow (upload/email → AI extracts → staff reviews → draft in SAP Business One) · Lead form B4.

### C22. Vision AI — `/ai/vision-ai/`
- Title EN: `Vision AI for Factories in Saudi Arabia` · AR: `الرؤية الحاسوبية للمصانع`
- Meta EN: `Camera-based quality checks and safety monitoring that feed results into your ERP and dashboards.` · AR: `فحص الجودة ومراقبة السلامة بالكاميرات مع ربط النتائج بنظامك ولوحات المتابعة.`
- H1 EN: **Vision AI for Quality and Safety** · AR: **الرؤية الحاسوبية للجودة والسلامة**
- Schema: Service, BreadcrumbList
- Sections: quality inspection · PPE/safety detection · counting and tracking · link to Industrial IoT Platform · Lead form B4.

---

### C23. ERP hub — `/erp/` (was `/erp-programs/`)
- Title EN: `ERP Systems in Saudi Arabia: SAP & Odoo | DWP Arabia` · AR: `أنظمة ERP في السعودية: SAP وOdoo`
- Meta EN: `Choose the right ERP with a partner for both SAP Business One and Odoo. Implementation, support and ZATCA compliance in KSA.` · AR: `اختر نظام ERP المناسب مع شريك لـ SAP Business One وOdoo. تطبيق ودعم وامتثال لزاتكا في السعودية.`
- H1 EN: **ERP Systems That Keep Your Business in Control** · AR: **أنظمة ERP تمنحك السيطرة على أعمالك**
- Schema: Service, BreadcrumbList
- Sections: 1) What ERP does (finance, operations, reporting in one source of truth — keep current site copy, improved). 2) **Two options** — big card SAP Business One (recommended for growing companies → `/sap-business-one/`), smaller card Odoo (→ `/erp/odoo/`). 3) Link "Not sure? Compare SAP Business One vs Odoo". 4) Business outcomes (faster close, visibility, less manual work). 5) Lead form B4.

### C24. Odoo — `/erp/odoo/`
- Title EN: `Odoo ERP Implementation in Saudi Arabia | DWP Arabia` · AR: `تطبيق نظام Odoo في السعودية`
- Meta EN: `Flexible, cost-effective ERP for smaller teams. Odoo implementation and support from DWP Arabia in Riyadh.` · AR: `نظام ERP مرن وبتكلفة مناسبة للفرق الصغيرة. تطبيق ودعم Odoo من DWP Arabia في الرياض.`
- H1 EN: **Odoo ERP for Growing Teams** · AR: **نظام Odoo للفرق النامية**
- Schema: Service, BreadcrumbList
- Sections: who it's for · apps we implement [CONFIRM] · Saudi localisation & ZATCA · factory modernisation (reuse the home "Empowering Saudi Factories" copy from Appendix 2 + Future Factories Program Approved Service Provider badge) · Odoo Silver Partner badge · "Thinking about growth? See SAP Business One" link · Lead form B4 (pre-select Odoo).

### C25. Data & BI — `/data-and-bi/`
- Title EN: `Data & BI Dashboards for SAP and ERP | DWP Arabia` · AR: `لوحات البيانات وذكاء الأعمال`
- Meta EN: `Data engineering, warehousing and executive dashboards on top of SAP Business One and your other systems.` · AR: `هندسة البيانات ومستودعات البيانات ولوحات القيادة فوق SAP Business One وأنظمتك الأخرى.`
- H1 EN: **Turn Your Data into Faster Decisions** · AR: **حوّل بياناتك إلى قرارات أسرع**
- Schema: Service, BreadcrumbList
- Sections: reuse current copy from Appendix 2 (Data & BI) + "Power BI on SAP Business One" example + Lead form B4.

### C26. Consulting — `/consulting/`
- Title EN: `Digital Transformation Consulting in KSA | DWP Arabia` · AR: `استشارات التحول الرقمي في السعودية`
- Meta EN: `Roadmaps, ERP selection, IT audits and legacy modernisation — structured advice from strategy to delivery.` · AR: `خرائط طريق واختيار أنظمة ERP وتدقيق تقني وتحديث الأنظمة القديمة — من الاستراتيجية إلى التنفيذ.`
- H1 EN: **Structured Digital Transformation** · AR: **تحول رقمي منظم**
- Sections: reuse current copy from Appendix 2 (Consulting); add "ERP selection" as first service linking to the assessment. Lead form B4.

### C27. Industrial IoT — `/iot-industrial-intelligence/`
- Title EN: `Industrial IoT for Saudi Factories | DWP Arabia` · AR: `إنترنت الأشياء الصناعي للمصانع`
- Meta EN: `Real-time visibility across operations: machine data, monitoring, predictive maintenance and OEE, linked to your ERP.` · AR: `رؤية لحظية للعمليات: بيانات الآلات والمراقبة والصيانة التنبؤية وكفاءة المعدات، مربوطة بنظامك.`
- H1 EN: **Real-Time Visibility Across Operations** · AR: **رؤية لحظية لجميع العمليات**
- Sections: reuse current copy from Appendix 2 (IoT); add "connects to SAP Business One production orders" + Lead form B4.

---

### C28–C31. Products (reuse the current copy in Appendix 2 — EN + clean AR — apply SEO; add screenshots, 3 use cases, demo CTA)

| Page | URL | Title EN | Title AR | Meta EN | H1 EN / AR | Schema |
|---|---|---|---|---|---|---|
| CRAS | `/cras/` | CRAS: Financial Consolidation Software \| DWP Arabia | برنامج التوحيد والمطابقة المالية CRAS | Automate reconciliation and multi-entity consolidation with real-time dashboards and full audit trails. | Close Faster with Automated Consolidation / إغلاق أسرع بالتوحيد الآلي | SoftwareApplication |
| PMS | `/pms/` | PMS: Performance & KPI Management \| DWP Arabia | نظام إدارة الأداء ومؤشرات KPI | Track projects and KPIs across departments with live dashboards and executive reporting. | Measure What Matters in Real Time / قِس ما يهم لحظياً | SoftwareApplication |
| Industrial IoT Platform | `/industrial-iot-platform/` | Industrial IoT Platform: Real-Time OEE \| DWP Arabia | منصة إنترنت الأشياء الصناعية | Connect machines for real-time production visibility, OEE monitoring and AI-driven optimisation. | Operational Intelligence in Real Time / ذكاء تشغيلي لحظي | SoftwareApplication |
| HRMS [CONFIRM: build or drop] | `/hrms/` | HRMS & Payroll for Saudi Companies \| DWP Arabia | نظام الموارد البشرية والرواتب | Workforce management and payroll built for Saudi rules: GOSI, Mudad and WPS. [CONFIRM features] | HR & Payroll Built for Saudi Arabia / موارد بشرية ورواتب مصممة للسعودية | SoftwareApplication |

Arabic meta descriptions:
- CRAS: `أتمتة المطابقة وتوحيد القوائم المالية لعدة كيانات مع لوحات لحظية وسجل تدقيق كامل.`
- PMS: `تابع المشاريع ومؤشرات الأداء في جميع الإدارات عبر لوحات لحظية وتقارير للإدارة العليا.`
- Industrial IoT Platform: `اربط الآلات لرؤية الإنتاج لحظياً ومراقبة كفاءة المعدات والتحسين بالذكاء الاصطناعي.`
- HRMS: `إدارة الموظفين والرواتب وفق الأنظمة السعودية: التأمينات الاجتماعية ومدد وحماية الأجور.`

---

### C32. Industries hub — `/industries/`
- Title EN: `ERP by Industry in Saudi Arabia | DWP Arabia` · AR: `حلول ERP حسب القطاع`
- Meta EN: `ERP set up for distribution, manufacturing, retail and contracting companies in Saudi Arabia.` · AR: `أنظمة ERP مهيأة لشركات التوزيع والتصنيع والتجزئة والمقاولات في السعودية.`
- H1 EN: **Built for Your Industry** · AR: **مصمم لقطاعك**
- Schema: CollectionPage, BreadcrumbList
- Sections: 4 industry cards + "Built for Saudi Arabia" card + CTA B5.

**Industry page template (C33–C36)** — these are the Google/LinkedIn ad landing pages. Each page, in order:
1. Hero: H1 + sub + [Book a Demo] [Free Assessment] — **form visible above the fold on desktop** (short form: name, company, mobile, email, consent).
2. "Sound familiar?" — 4 industry pains.
3. "How our ERP solves it" — 4–6 features mapped to the pains.
4. AI add-on for this industry (1 card).
5. ZATCA block (short). 6. Case study [CONFIRM] or hide. 7. FAQ (3, + schema). 8. Lead form B4 + CTA B5.
Schema: Service (+ FAQPage), BreadcrumbList.

### C33. Distribution & Trading — `/industries/distribution/`
- Title EN: `ERP for Distributors in KSA | DWP Arabia` · AR: `نظام ERP لشركات التوزيع والتجارة`
- Meta EN: `Control stock across warehouses, price lists, van sales and margins in one ERP. Built for Saudi distributors.` · AR: `تحكم في المخزون بين المستودعات وقوائم الأسعار والمبيعات والهوامش بنظام واحد لموزعي السعودية.`
- H1 EN: **ERP for Distribution & Trading** · AR: **نظام ERP لشركات التوزيع والتجارة**
- Keywords: ERP for distribution, inventory management system Saudi / نظام مخزون وتوزيع، برنامج إدارة مستودعات
- Pains (EN/AR): stock differences between system and warehouse / فروقات المخزون بين النظام والمستودع · many price lists and discounts / قوائم أسعار وخصومات متعددة · slow collections / بطء التحصيل · no margin per customer or item / لا رؤية للهامش حسب العميل أو الصنف.
- Features: multi-warehouse & bin locations · batch/serial & expiry · price lists and discount rules · sales/purchase cycle with approvals · customer credit limits · mobile sales [CONFIRM add-on] · margin reports. / تعدد المستودعات والمواقع · الدفعات والأرقام التسلسلية والصلاحية · قوائم الأسعار وقواعد الخصم · دورة المبيعات والمشتريات مع الاعتمادات · حدود ائتمان العملاء · المبيعات عبر الجوال · تقارير الهامش.
- AI add-on: demand forecasting and reorder suggestions / التنبؤ بالطلب واقتراحات إعادة الطلب.

### C34. Manufacturing — `/industries/manufacturing/`
- Title EN: `ERP for Manufacturers in KSA | DWP Arabia` · AR: `نظام ERP للمصانع في السعودية`
- Meta EN: `Plan production, control materials and know your true product cost with an ERP built for Saudi factories.` · AR: `خطط للإنتاج وتحكم في المواد واعرف التكلفة الحقيقية لمنتجك بنظام ERP للمصانع السعودية.`
- H1 EN: **ERP for Manufacturing** · AR: **نظام ERP للتصنيع**
- Keywords: manufacturing ERP Saudi, production costing / نظام إدارة المصانع، تكاليف الإنتاج
- Pains: unknown real product cost / عدم معرفة التكلفة الحقيقية للمنتج · material shortages stop production / نقص المواد يوقف الإنتاج · manual production tracking / تتبع الإنتاج يدوياً · scrap and waste not measured / الهالك غير مقاس.
- Features: bills of materials · production orders · MRP · costing (standard/actual) · quality checks · advanced production add-on [CONFIRM e.g. Beas] · link to Industrial IoT Platform for machine data.
- AI add-on: Vision AI quality inspection + predictive maintenance.

### C35. Retail — `/industries/retail/`
- Title EN: `ERP for Retail in Saudi Arabia | DWP Arabia` · AR: `نظام ERP لقطاع التجزئة`
- Meta EN: `Connect branches, POS and e-commerce to one ERP with live stock and ZATCA-ready invoicing.` · AR: `اربط الفروع ونقاط البيع والمتجر الإلكتروني بنظام واحد مع مخزون لحظي وفوترة متوافقة.`
- H1 EN: **ERP for Retail** · AR: **نظام ERP لقطاع التجزئة**
- Pains: branch stock not visible / مخزون الفروع غير ظاهر · POS and accounts don't match / عدم تطابق نقاط البيع مع الحسابات · promotions hard to control / صعوبة التحكم بالعروض · e-commerce orders handled manually / طلبات المتجر الإلكتروني يدوية.
- Features: multi-branch stock · POS integration [CONFIRM POS] · e-commerce integration (e.g. Salla, Zid, Shopify) [CONFIRM] · promotions · replenishment · daily sales dashboards.
- AI add-on: sales forecasting by branch and season.

### C36. Contracting & Services — `/industries/contracting/`
- Title EN: `ERP for Contracting & Service Companies KSA | DWP Arabia` · AR: `ERP لشركات المقاولات والخدمات`
- Meta EN: `Track every project's budget, cost and profit in one ERP — from tender to final invoice.` · AR: `تابع ميزانية وتكلفة وربح كل مشروع في نظام واحد — من المناقصة حتى الفاتورة النهائية.`
- H1 EN: **ERP for Contracting & Services** · AR: **نظام ERP للمقاولات والخدمات**
- Pains: profit per project unknown until the end / ربح المشروع غير معروف حتى النهاية · costs posted late / تأخر تسجيل التكاليف · progress billing done in Excel / المستخلصات على Excel · service contracts tracked manually / متابعة عقود الخدمة يدوياً.
- Features: project costing and budgets · purchase per project · progress billing / retention [CONFIRM add-on] · service contracts and calls · project profitability reports.
- AI add-on: cost-overrun alerts.

### C37. Built for Saudi Arabia — `/built-for-saudi-arabia/` (add the missing AR version)
- Title EN: `ZATCA, GOSI & Mudad Ready ERP | DWP Arabia` · AR: `نظام ERP متوافق مع زاتكا والتأمينات ومدد`
- Meta EN: `ERP set up for Saudi rules: ZATCA e-invoicing, VAT, GOSI, Mudad and Qiwa, with Arabic documents and local support.` · AR: `نظام ERP مهيأ للأنظمة السعودية: الفوترة الإلكترونية والضريبة والتأمينات ومدد وقوى، مع مستندات عربية ودعم محلي.`
- H1 EN: **Built for Saudi Arabia** · AR: **مصمم للمملكة العربية السعودية**
- Schema: Service, BreadcrumbList
- Sections: integration grid from the current site (Appendix 2): ZATCA e-invoicing · Mudad · GOSI · Qiwa · Salla · Zid · Foodics · Mada · STC Pay · Apple Pay · Tabby · Tamara, with their logos; add Vision 2030 digital transformation line; link each item to the related page (ZATCA → `/sap-business-one/zatca-e-invoicing/`, HR → `/hrms/` if built). Lead form B4.

---

### C38. Insights (blog) — `/insights/` + `/insights/<slug>/`
- Listing Title EN: `ERP & AI Insights for Saudi Businesses | DWP Arabia` · AR: `مقالات ERP والذكاء الاصطناعي | DWP Arabia`
- Listing Meta EN: `Practical guides on SAP Business One, ZATCA e-invoicing and AI for Saudi companies.` · AR: `أدلة عملية حول SAP Business One والفوترة الإلكترونية والذكاء الاصطناعي للشركات السعودية.`
- H1 EN: **Insights** · AR: **مقالات ورؤى**
- Article template: H1, author (with photo + LinkedIn), date, reading time, table of contents, body, FAQ (+schema), related service CTA, related articles. Schema: Article + FAQPage + BreadcrumbList. Both languages.
- **Launch articles** (create the 6 MDX files with title, slug, meta and an outline of H2s; full text to be written by DWP — mark body as [DRAFT]):

| # | Slug | Title EN | Title AR | Links to |
|---|---|---|---|---|
| 1 | `zatca-phase-2-sap-business-one` | ZATCA Phase 2: What SAP Business One Users Must Do | المرحلة الثانية من الفوترة الإلكترونية: ما يجب على مستخدمي SAP Business One فعله | ZATCA page |
| 2 | `signs-outgrown-accounting-software` | 7 Signs Your Company Has Outgrown Its Accounting Software | 7 علامات أن شركتك تجاوزت برنامجها المحاسبي | Switch page |
| 3 | `sap-business-one-vs-odoo-saudi` | SAP Business One vs Odoo for Saudi Companies | مقارنة SAP Business One وOdoo للشركات السعودية | vs-Odoo page |
| 4 | `sap-business-one-cost-saudi-arabia` | How Much Does SAP Business One Cost in Saudi Arabia? | كم تكلفة SAP Business One في السعودية؟ | Pricing page |
| 5 | `ai-use-cases-erp-data` | 5 Practical AI Uses on Top of Your ERP Data | 5 استخدامات عملية للذكاء الاصطناعي على بيانات نظامك | AI for SAP B1 |
| 6 | `choosing-sap-business-one-partner-ksa` | Choosing an SAP Business One Partner in KSA: 10 Questions | اختيار شريك SAP Business One في السعودية: 10 أسئلة | Support page |

### C39. Careers — `/careers/` + `/careers/<role>/`
- Title EN: `Careers at DWP Arabia | SAP & AI Jobs in Riyadh` · AR: `الوظائف في DWP Arabia | وظائف SAP والذكاء الاصطناعي`
- Meta EN: `Join our SAP Business One and AI team in Riyadh. See open roles.` · AR: `انضم إلى فريق SAP Business One والذكاء الاصطناعي في الرياض. اطلع على الوظائف المتاحة.`
- H1 EN: **Join DWP Arabia** · AR: **انضم إلى DWP Arabia**
- Content: why join (3 points), open roles list (from MDX, real roles only [CONFIRM]), "No open role? Send your CV" form (CV upload, PDF ≤ 5 MB). Each role page: JobPosting schema (title, location Riyadh, employmentType, datePosted, validThrough). **Delete all demo jobs.** If no roles are open, show only the CV form.

---

## PART D — Launch checklist (Claude Code must verify before handing over)

- [ ] All 38 pages exist in EN and AR with the exact titles, metas and H1s above; build fails if any page lacks title/meta/H1 (add a test).
- [ ] `hreflang` pairs and canonicals correct on every page (add an automated check).
- [ ] Arabic pages fully RTL; no English UI text left on Arabic pages (except brand/product names).
- [ ] All redirects in A5 return the right status codes.
- [ ] Sitemaps list only live, indexable pages; old demo URLs absent.
- [ ] No "No Post Found", no lorem ipsum, no demo content anywhere.
- [ ] Every issue in Appendix 4 is fixed; no Arabic copied from the live site (presentation-form characters U+FB50–U+FEFF must not appear anywhere — add a test).
- [ ] Every **[CONFIRM]** item is listed in `CONFIRM.md` for DWP to fill; staging shows them highlighted.
- [ ] Forms: validation, spam protection, email + CRM delivery, UTM capture, thank-you page, GTM events fire.
- [ ] Lighthouse mobile ≥ 90 for Performance, SEO, Accessibility, Best Practices on Home, SAP pillar and one industry page.
- [ ] JSON-LD validates in Google Rich Results Test for Home, SAP pillar (FAQ), Contact (LocalBusiness), one article.
- [ ] No "reseller" / licence-price wording anywhere (search the codebase).
- [ ] After launch (DWP team): submit sitemaps in Google Search Console + Bing; set up Google Business Profile for the Riyadh office with the same name/address/phone.
---

## APPENDIX — Current website: full structure and content (captured 8 Oct 2026)

> Source: every live page of `dwptech.com/arabia/` (English + Arabic), read directly from the page HTML.
> Purpose: Claude Code must **reuse this copy** for the pages marked "keep current copy" in Part C, apply the fixes in Appendix 4, and use the image file names in Appendix 3 to find assets on the old site (`/arabia/wp-content/uploads/…`).
> Arabic below is retyped in **standard Arabic characters**. The live site uses PDF-copied "presentation form" characters (see Appendix 4, issue 1) — never copy Arabic from the live site directly.

### Appendix 1 — Current site structure

**Platform:** WordPress + Elementor, theme "BrightHub" (Case-Themes), designed by Digital Dart. Page titles are "<Page> – DWP". **No page has a meta description.**

**Main menu (mega menu) — EN / AR**

- Home / الرئيسية
- Solutions / الحلول
  - ERP → ERP Implementation · Customization & Extensions · Integration Architecture · Support & Optimization
    AR: تنفيذ أنظمة تخطيط موارد المؤسسات · التخصيص والتطوير · هندسة التكامل · الدعم والتحسين
  - Consulting → Digital Transformation Roadmaps · IT Advisory & Governance · IT Audit & Compliance · Legacy Modernization
    AR: خارطة طريق التحول الرقمي · استشارات تقنية المعلومات والحوكمة · تدقيق تقنية المعلومات والامتثال · تحديث الأنظمة القائمة
  - Data & BI → Data Engineering · Data Warehousing · Business Intelligence · Executive Dashboards
    AR: هندسة البيانات · مستودعات البيانات · ذكاء الأعمال · لوحات المعلومات التنفيذية
  - AI → Enterprise AI Solutions · Predictive Optimization · Vision AI & Monitoring · Knowledge AI Systems
    AR: حلول الذكاء الاصطناعي للمؤسسات · التحسين التنبؤي · الرؤية الحاسوبية والمراقبة الذكية · أنظمة الذكاء المعرفي
  - IoT → Industrial IoT · Edge Processing · Production Intelligence · OEE & Performance Monitoring
    AR: إنترنت الأشياء الصناعية · المعالجة الطرفية · ذكاء الإنتاج · مراقبة الكفاءة الشاملة للمعدات (OEE) والأداء
- Products / المنتجات
  - CRAS → Reconciliation · Consolidation · Financial Reporting · Multi-Entity Scaling
    AR: التسويات المالية · توحيد البيانات المالية · التقارير المالية · إدارة المؤسسات متعددة الكيانات
  - HRMS → Workforce Management · Payroll Integration · Attendance & Compliance
    AR (live site — WRONG, see issue 4): heading "الدورة الشهرية" with sub-items copied from PMS
  - PMS → Portfolio Monitoring · KPI Tracking · Performance Visibility
    AR: مراقبة المحافظ · متابعة مؤشرات الأداء الرئيسية · رؤية شاملة للأداء
  - Industrial IoT Platform → Real-Time Monitoring · AI Optimization · Operational Dashboards
    AR: المراقبة اللحظية · التحسين باستخدام الذكاء الاصطناعي · لوحات المعلومات التشغيلية
- Company / الشركة → About · Clients · Partners / من نحن · عملاؤنا · شركاؤنا
- Header right: phone +966 11 254 6591 · [Book a Demo → /contact/] · language switch English / العربية

**Footer**
- Logo + EN: "We deliver a broad spectrum of global IT solutions, catering to diverse sectors spanning banking, oil & gas and more." AR: "نقدم مجموعة متكاملة من حلول تقنية المعلومات العالمية، تلبي احتياجات قطاعات متنوعة تشمل البنوك، والنفط والغاز، وغيرها من القطاعات."
- Phone +966 11 254 6591
- Company: About · Clients · Partners · Contact
- Solutions: ERP · Consulting · Data & BI · AI · IoT
- Products: CRAS · PMS · Industrial IoT Platform
- "© 2026 DWP Arabia. All rights reserved." (AR shows "© 2026 DWP العربية" — wrong, keep the brand name in Latin) · "Designed by Digital Dart"

**Page list**

| Page | EN URL | AR URL | Status |
|---|---|---|---|
| Home | `/arabia/` | `/arabia/ar/` | Live |
| About | `/about/` | `/ar/about-ar/` | Live — has internal placeholder text |
| Partners | `/partners/` | `/ar/partners-ar/` | Live — AR shows only 2 logos |
| Our Clients | `/our-clients/` | `/ar/our-clients-ar/` | Live — AR shows only 2 logos |
| Contact | `/contact/` | `/ar/contact-ar/` | Live |
| ERP Programs | `/erp-programs/` | `/ar/erp-programs-ar/` | Live |
| Consulting | `/consulting/` | `/ar/consulting-ar/` | Live |
| Data & BI | `/data-and-bi/` | `/ar/data-and-bi-ar/` | Live |
| AI | `/solutions-ai/` | `/ar/solutions-ai-ar/` | Live |
| IoT / Industrial Intelligence | `/iot-industrial-intelligence/` | `/ar/iot-industrial-intelligence-ar/` | Live |
| Built for Saudi Arabia | `/built-for-saudi-arabia/` | — | EN only, not in menu, duplicate of home hero |
| CRAS | `/cras/` | `/ar/cras-ar/` | Live |
| PMS | `/pms/` | `/ar/pms-ar/` | Live |
| Industrial IoT Platform | `/industrial-iot-platform/` | `/ar/industrial-iot-platform-ar/` | Live |
| HRMS | `/hrms/` | — | In menu, **404** |
| Test page | `/test-page/` | — | Copy of AI page — delete |
| 13 blog posts, 5 careers | various | — | Theme demo content — delete |

**Shared "Let's Talk" block (on most pages)**
- EN heading: "Let's Talk. We're All Ears." · AR (fixed): "لنتحدث، نحن هنا للاستماع إليكم"
- Fields EN: Full Name · Company · Email ("Enter your email") · Phone (optional) (placeholder shows the company number +966 11 254 6591 — wrong) · Area of Interest [ERP | Consulting | Data & BI | AI | IoT | Products] · Message ("Leave us a message...") · [Submit Inquiry]
- Fields AR (fixed): الاسم الكامل · الشركة · البريد الإلكتروني · رقم الهاتف (اختياري) · مجال الاهتمام [تخطيط موارد المؤسسات | الاستشارات | البيانات وذكاء الأعمال | الذكاء الاصطناعي | إنترنت الأشياء | المنتجات] · الرسالة ("اكتب رسالتك هنا...") · [إرسال الاستفسار]
- Home page also contains a second, hidden mini form (Name · Organization · Contact Email · Area of Interest) — remove.

---

### Appendix 2 — Page content (English + Arabic)

#### Home — `/arabia/`
1. **Hero** — H1: "Engineering Enterprise Intelligence" / "هندسة ذكاء المؤسسات"
   Sub: "ERP, Data, AI, and Industrial Systems built to help organizations operate with greater visibility, control, and confidence" / "حلول متكاملة في تخطيط موارد المؤسسات (ERP)، وإدارة البيانات، والذكاء الاصطناعي، والأنظمة الصناعية، صُممت لتمكين المؤسسات من العمل برؤية أوضح، وتحكم أكبر، وثقة أعلى."
   Buttons: [Speak With Us → /contact/] [Explore Solutions → # (dead link)] / [تواصل معنا] [استكشف الحلول] (AR buttons point to the wrong pages)
   Background: video (`Feature_Video_1_iPhone.mp4`).
2. **H2** "From Strategy to Systems. Without Fragmentation." / "من الاستراتيجية إلى الأنظمة. بمنهجية متكاملة دون تعقيد"
   "DWP IT Arabia helps organizations modernize operations through ERP, Data, AI, and Industrial Intelligence solutions. From strategy and architecture to implementation and support, we deliver technology programs that create measurable business outcomes and long term value" / "تساعد DWP IT Arabia المؤسسات على تحديث عملياتها من خلال حلول تخطيط موارد المؤسسات (ERP)، والبيانات، والذكاء الاصطناعي، والذكاء الصناعي. ومن وضع الاستراتيجيات وتصميم البنية التقنية إلى التنفيذ والدعم، نقدم برامج تقنية متكاملة تحقق نتائج أعمال قابلة للقياس وتُسهم في خلق قيمة مستدامة على المدى الطويل."
   [Explore More → /about/] / [اكتشف المزيد]
3. **H2** "Technology & Ecosystem Partnerships" / "شراكات تقنية ومنظومات رقمية متكاملة"
   "We work with leading enterprise platforms to deliver integrated, secure, and future ready solutions" / "نتعاون مع أبرز منصات الأعمال العالمية لتقديم حلول متكاملة وآمنة وجاهزة لمواكبة متطلبات المستقبل"
   Logos: Microsoft, Odoo. Tagline: "Integrated. Secure. Enterprise Grade" / "حلول متكاملة، آمنة، وبمستوى المؤسسات"
4. **H2** "Proprietary Platforms" / "منصاتنا المطورة"
   - CRAS — "Automated financial consolidation, reconciliation, and reporting designed for multi entity organizations." / "منصة متخصصة لأتمتة عمليات توحيد البيانات المالية، والتسويات، وإعداد التقارير، ومصممة خصيصًا للمؤسسات متعددة الكيانات."
   - HRMS — "A unified platform for workforce management, payroll processes, compliance, and employee operations." (EN only; missing on AR home)
   - PMS — "Performance and portfolio management solutions that provide visibility, accountability, and informed decision making." / "حلول متقدمة لإدارة الأداء والمحافظ، توفر رؤية شاملة، وتعزز المساءلة، وتدعم اتخاذ القرارات المبنية على البيانات."
   - Industrial IoT Platform — "Real time operational intelligence powered by connected systems, analytics, and AI." / "منصة توفر ذكاءً تشغيليًا لحظيًا بالاعتماد على الأنظمة المتصلة، وتحليلات البيانات، وتقنيات الذكاء الاصطناعي."
   [View Products → # (dead link)] / [استعرض المنتجات]
5. **H2** "Trusted by Enterprise Leaders" / "موثوق من قبل كبرى المؤسسات الرائدة" — shows **"No Post Found"** — "Supporting organizations that operate in complex, regulated, and performance driven environments" / "ندعم المؤسسات التي تعمل في بيئات تشغيلية معقدة، وخاضعة للأنظمة، وترتكز على تحقيق أعلى مستويات الأداء والكفاءة"
6. **H2** "Built for Saudi Arabia" / "مصمم خصيصًا للمملكة العربية السعودية" — logo grid: ZATCA e-invoicing compliance · Mudad integration capability · GOSI integration capability · Qiwa integration capability · Salla integration capability · Zid integration · Foodics integration · Mada · STC Pay · Apple Pay · Tabby · Tamara
7. **H2** "Empowering Saudi Factories Through Digital Transformation" / "تمكين المصانع السعودية عبر التحول الرقمي"
   "Helping manufacturers and enterprises modernize operations through Odoo ERP, AI-driven automation, analytics, and enterprise technology solutions aligned with Saudi Vision 2030" / "نساعد المصانع والمؤسسات على تطوير عملياتها التشغيلية من خلال نظام Odoo ERP، وأتمتة الأعمال المدعومة بالذكاء الاصطناعي، وتحليلات البيانات، وحلول تقنية متكاملة، بما يتوافق مع مستهدفات رؤية المملكة 2030."
   Badge: "Future Factories Program Approved Service Provider" (`Future-Factories_N.png`)
8. **H2** "Solutions Built for Enterprise Growth" / "حلول مصممة لدعم نمو المؤسسات" — [Explore All Solutions → # (dead)]
   - 01 ERP Programs — "Implementation, integration, optimization, and governance for modern enterprise operations." / "تنفيذ وتكامل وتحسين وإدارة حلول تخطيط موارد المؤسسات بما يدعم كفاءة العمليات التشغيلية الحديثة."
   - 02 Digital Transformation & Consulting — "Technology roadmaps, modernization initiatives, governance frameworks, and advisory services." / "استراتيجيات تقنية، ومبادرات للتحول والتحديث، وأطر حوكمة، وخدمات استشارية تدعم تحقيق الأهداف المؤسسية."
   - 03 Data & BI — "Connected data ecosystems, executive dashboards, predictive analytics, and reporting." / "منظومات بيانات مترابطة، ولوحات معلومات تنفيذية، وتحليلات تنبؤية، وتقارير متقدمة تدعم اتخاذ القرار."
   - 04 Applied AI — "AI solutions that improve decision making, operational efficiency, and business performance." / "حلول ذكاء اصطناعي تعزز جودة اتخاذ القرار، وترفع الكفاءة التشغيلية، وتحسن أداء الأعمال."
   - 05 Industrial Intelligence — "IoT and AI powered solutions that provide real time visibility and operational control." / "حلول مدعومة بإنترنت الأشياء والذكاء الاصطناعي توفر رؤية تشغيلية لحظية وتحكمًا أكثر كفاءة في العمليات."
   - Theme filler line (EN only — delete): "We've built something that matters. Thousands of companies trust us to find their next software partner. The numbers speak for themselves."
9. **H2** "Frequently Asked Questions" — "FAQs built to give you the answers you need" / "الأسئلة الشائعة" — "إجابات واضحة على أكثر الأسئلة شيوعًا لمساعدتك في العثور على ما تحتاج إليه"
   1. What types of organizations do you work with — "We work with government entities, financial institutions, manufacturing organizations, and enterprise groups operating in complex environments." / ما أنواع المؤسسات التي تتعاونون معها؟ — "نعمل مع الجهات الحكومية، والمؤسسات المالية، والمنشآت الصناعية، والمجموعات المؤسسية التي تعمل في بيئات تشغيلية معقدة."
   2. Do you provide both strategy and implementation — "Yes. We support the complete journey from planning and design through deployment and optimization." / هل تقدمون خدمات وضع الاستراتيجيات والتنفيذ معًا؟ — "نعم. ندعم جميع مراحل المشروع، بدءًا من التخطيط والتصميم، مرورًا بالتنفيذ، وصولاً إلى التشغيل والتحسين المستمر."
   3. Are you tied to specific technology vendors — "No. We recommend and implement solutions based on business requirements and long term objectives." / هل تقتصر حلولكم على مزودي تقنيات محددين؟ — "لا. نوصي بالحلول المناسبة وننفذها بناءً على متطلبات الأعمال والأهداف الاستراتيجية طويلة المدى."
   4. Do you provide post implementation support — "Yes. Our services include optimization, monitoring, managed support, and structured service agreements." / هل تقدمون خدمات الدعم بعد التنفيذ؟ — "نعم. تشمل خدماتنا التحسين المستمر، والمراقبة، والدعم المُدار، واتفاقيات مستوى الخدمة المنظمة لضمان استمرارية الأداء."
   Badges near FAQ: Odoo Silver Partner (`Odoo-Silver-Partner-Logo.png`), The Factory (`The-Factory-Logo.png`)
10. **H2** "Start a Structured Conversation" / "ابدأ حوارًا يحقق أهداف أعمالك"
    "Whether you are modernizing systems, building a data foundation, deploying AI, or improving operations, we are ready to help" / "سواء كنتم تسعون إلى تحديث الأنظمة، أو بناء بنية متكاملة للبيانات، أو توظيف حلول الذكاء الاصطناعي، أو تطوير كفاءة العمليات، فنحن على أتم الاستعداد لدعمكم."
    → Let's Talk form.

#### About — `/about/`
- H2 "Who We Are" / "من نحن": "DWP IT Arabia delivers enterprise software, AI, cybersecurity, and digital transformation solutions that help organizations modernize operations and accelerate growth." / "تقدم DWP IT Arabia حلولاً متكاملة تشمل برمجيات المؤسسات، والذكاء الاصطناعي، والأمن السيبراني، والتحول الرقمي، لتمكين المؤسسات من تحديث عملياتها التشغيلية وتسريع نمو أعمالها."
- Internal note published by mistake (delete): "Leadership information will be shared separately. In the meantime, the following company facts may be used with relevant icons:" / "سيتم مشاركة معلومات فريق القيادة لاحقًا…"
- Facts: Established in 1999 / تأسست عام 1999 · 4,500+ Employees / أكثر من 4,500 موظف · Presence across Pakistan, Saudi Arabia, UAE and Qatar / حضور في باكستان، والمملكة العربية السعودية، والإمارات العربية المتحدة، وقطر
- H2 "What We Do" / "ماذا نقدم": Implement and optimize ERP ecosystems / تنفيذ وتحسين منظومات تخطيط موارد المؤسسات (ERP) · Engineer unified data platforms / تصميم وتطوير منصات بيانات موحدة · Deploy applied AI solutions / تطبيق حلول الذكاء الاصطناعي العملية · Connect industrial systems through IoT / ربط الأنظمة الصناعية عبر إنترنت الأشياء · Deliver structured digital transformation programs / تنفيذ برامج تحول رقمي بمنهجية منظمة
- H2 "Why Clients Work With Us" / "لماذا يختارنا عملاؤنا؟": Saudi presence and local understanding / حضور محلي وفهم عميق للسوق السعودي · Enterprise-scale system capability / خبرة في تنفيذ حلول المؤسسات واسعة النطاق · Vendor-neutral platform strategy / استراتيجية تقنية محايدة تجاه مزودي الحلول · Clear accountability from design to deployment / مسؤولية واضحة تمتد من التصميم وحتى التنفيذ · Long-term operational support / دعم تشغيلي مستمر على المدى الطويل
- H2 "Technology. Structured. Delivered" / "تقنيات متقدمة. بمنهجية واضحة. ونتائج موثوقة." — [Start a Conversation → /contact/] / [ابدأ التواصل معنا] (AR links to the English contact page)
- No H1 on this page.

#### Partners — `/partners/`
- H1 "Strategic Technology Partnerships" / "شراكات تقنية استراتيجية"
- "We collaborate with leading enterprise platforms to deliver integrated, future-ready systems" / "نتعاون مع أبرز منصات تقنية المؤسسات لتقديم حلول متكاملة وجاهزة لمواكبة متطلبات المستقبل"
- H2 "Technology & Platform Ecosystem" / "منظومة التقنيات والمنصات"
- Logos EN: Microsoft · Odoo · Cisco · Huawei · Schneider Electric · Fortinet. AR: Microsoft · Odoo only. **No SAP.**

#### Our Clients — `/our-clients/`
- H1 "Trusted by Leading Organizations Across the Kingdom" / "موثوق من قبل كبرى المؤسسات في المملكة"
- "We deliver enterprise systems for regulated, operationally complex environments" / "نقدم حلولاً مؤسسية متكاملة للمؤسسات العاملة في بيئات تشغيلية معقدة وخاضعة للأنظمة واللوائح"
- H2 "Our Clients" / "عملاؤنا"
- Logos EN (24): SAP · Oracle · Microsoft · Odoo · Cisco · Huawei (these 6 are vendors, not clients) · Unilever · Citibank · Pepsi · Deutsche Bank · LBDO Dr. Mohamed Al Amri · Grant Thornton · Dubai Islamic Bank (image file is "Bank-Islami") · KfW · Ajlan & Bros · Standard Chartered · Compass · Telenor · Azizi Bank · JazzCash · HEC · NTDC · Feroze 1888 Mills Limited · UBL. AR: Microsoft · Odoo only.

#### Contact — `/contact/`
- H1 "Let's Start the Conversation" / "لنبدأ الحوار"
- "Tell us what you're building, modernizing, or optimizing — and we'll respond with clarity" / "أخبرنا بما تعمل على تطويره أو تحديثه أو تحسينه، وسنقدم لك الرؤية والحلول المناسبة بكل وضوح"
- Head Office / المكتب الرئيسي: Hala Center Offices, 3310 Anas bin Malik Road, Riyadh, Saudi Arabia / هلا سنتر، 3310 طريق أنس بن مالك، الرياض، المملكة العربية السعودية
- Phone +966 11 254 6591 · Email: hidden by a Cloudflare protection script [CONFIRM real address] · Social Media (no links shown)
- Working Hours: "Saturday to Thursday 9:00 AM – 5:00 PM (PST)" / "من السبت إلى الخميس، 9:00 صباحًا إلى 5:00 مساءً"
- Let's Talk form + Google Map. AR page title is "احجز عرضًا توضيحيًا" (Book a demo), not "Contact".

#### Solution pages — same template: H1 · sub · button → `#lets_talk` · "Key Capabilities" · "Business Outcomes" · "Platforms We Work With" · form

**ERP Programs — `/erp-programs/`**
- H1 "Modern ERP Systems That Keep Your Business in Control" / "أنظمة ERP حديثة تمنح أعمالك تحكمًا أكبر"
- "We implement, integrate, and optimize ERP platforms that connect finance, operations, and reporting into a single source of truth" / "نقوم بتنفيذ وتكامل وتحسين أنظمة تخطيط موارد المؤسسات (ERP) لربط العمليات المالية والتشغيلية والتقارير ضمن منصة موحدة توفر مصدرًا موثوقًا وموحدًا للبيانات."
- [Talk About ERP] / [تحدث معنا حول حلول ERP]
- Key Capabilities / أبرز القدرات: ERP implementation and deployment / تنفيذ ونشر أنظمة تخطيط موارد المؤسسات (ERP) · Integration with existing business systems / التكامل مع أنظمة الأعمال الحالية · Workflow customization and automation / تخصيص سير العمل وأتمتته · Regulatory and compliance alignment / الامتثال للأنظمة والمتطلبات التنظيمية · Ongoing support and optimization / الدعم المستمر والتحسين المتواصل
- Business Outcomes / نتائج الأعمال: Faster financial closing / تسريع عمليات الإقفال المالي · Improved operational visibility / تعزيز الرؤية التشغيلية · Reduced manual effort / تقليل الأعمال اليدوية · Stronger governance and control / تعزيز الحوكمة والرقابة
- Platforms EN: SAP · Oracle · Microsoft · Odoo. AR: Microsoft · Odoo.

**Consulting — `/consulting/`**
- H1 "Structured Digital Transformation Clear Business Outcomes" / "تحول رقمي بمنهجية واضحة ونتائج أعمال ملموسة"
- "We help organizations modernize technology, improve processes, and execute transformation programs with confidence" / "نساعد المؤسسات على تحديث بنيتها التقنية، وتحسين عملياتها، وتنفيذ برامج التحول الرقمي بكفاءة وثقة"
- [Start a Transformation Discussion] / [ابدأ رحلة التحول الرقمي معنا]
- Capabilities: Digital transformation roadmaps / خارطة طريق للتحول الرقمي · IT advisory and governance / استشارات تقنية المعلومات والحوكمة · Technology audits and assessments / تدقيق وتقييم البنية التقنية · Legacy modernization / تحديث الأنظمة القائمة · Compliance readiness / الجاهزية للامتثال للأنظمة والمتطلبات التنظيمية
- Outcomes: Reduced execution risk / تقليل مخاطر التنفيذ · Clear strategic direction / رؤية استراتيجية واضحة · Improved governance / تعزيز الحوكمة · Greater operational efficiency / رفع الكفاءة التشغيلية
- Platforms EN: SAP · Oracle · Microsoft · Odoo (AR: none)

**Data & BI — `/data-and-bi/`**
- H1 "Turn Data Into Better Decisions" / "حوّل البيانات إلى قرارات أكثر ذكاءً"
- "We bring data together across systems and transform it into meaningful insights that support faster and smarter decision making" / "نوحّد البيانات من مختلف الأنظمة ونحوّلها إلى رؤى عملية تدعم اتخاذ قرارات أسرع وأكثر دقة وذكاءً"
- [Build Your Data Foundation] / [ابدأ ببناء منظومة بياناتك]
- Capabilities: Data engineering and integration / هندسة البيانات وتكاملها · Data warehousing / مستودعات البيانات · Executive dashboards / لوحات المعلومات التنفيذية · Automated reporting / التقارير المؤتمتة · Predictive analytics / التحليلات التنبؤية
- Outcomes: Real time visibility / رؤية لحظية للبيانات · More accurate forecasting / توقعات أكثر دقة · Faster reporting / إعداد التقارير بوتيرة أسرع · Better business decisions / اتخاذ قرارات أعمال أكثر فاعلية
- Platforms: Microsoft · Odoo

**AI — `/solutions-ai/`**
- H1 "Practical AI for Real Business Challenges" / "ذكاء اصطناعي عملي لمواجهة تحديات الأعمال الواقعية"
- "We deploy AI solutions that improve efficiency, reduce risk, and help organizations make more informed decisions" / "نوفر حلولاً للذكاء الاصطناعي تعزز الكفاءة التشغيلية، وتحد من المخاطر، وتمكّن المؤسسات من اتخاذ قرارات أكثر دقة واستنارة"
- [Explore AI Solutions] / [استكشف حلول الذكاء الاصطناعي]
- Capabilities: Knowledge assistants / مساعدو الذكاء المعرفي · Predictive analytics / التحليلات التنبؤية · Monitoring and anomaly detection / المراقبة واكتشاف الحالات غير الطبيعية · Risk identification / تحديد المخاطر · Intelligent maintenance planning / التخطيط الذكي لأعمال الصيانة
- Outcomes: Reduced downtime / تقليل فترات التوقف · Improved planning / تحسين التخطيط · Faster issue detection / اكتشاف المشكلات بسرعة أكبر · Enhanced service quality / الارتقاء بجودة الخدمات
- Platforms EN: SAP · Oracle · Microsoft · Odoo

**IoT / Industrial Intelligence — `/iot-industrial-intelligence/`**
- H1 "Real Time Visibility Across Operations" / "رؤية لحظية شاملة عبر جميع العمليات التشغيلية"
- "We connect machines, systems, and operational data to help organizations improve performance, efficiency, and control." / "نربط الآلات والأنظمة والبيانات التشغيلية لتمكين المؤسسات من تعزيز الأداء، ورفع الكفاءة، وتحقيق تحكم أكبر في العمليات"
- [Improve Operational Performance] / [ارتقِ بالأداء التشغيلي]
- Capabilities: Industrial IoT integration / تكامل إنترنت الأشياء الصناعية · Real time monitoring / المراقبة اللحظية · Performance tracking / متابعة الأداء · Predictive maintenance / الصيانة التنبؤية · Operational analytics / تحليلات العمليات التشغيلية
- Outcomes: Increased productivity / زيادة الإنتاجية · Reduced downtime / تقليل فترات التوقف · Better quality control / تحسين مراقبة الجودة · Improved resource utilization / رفع كفاءة استغلال الموارد
- Platforms EN: SAP · Oracle · Microsoft · Odoo. AR page title wrong: "إنترنت الأشياء / منصة المؤسسات".

**Built for Saudi Arabia — `/built-for-saudi-arabia/`** (EN only, not linked)
- Copy of the home hero (same H1 "Engineering Enterprise Intelligence", same buttons) + video
- H5 "Technology & Platform Ecosystem" + placeholder logo (`Logo-Placeholder.png`) + list: ZATCA e-invoicing compliance · Mudad integration capability · GOSI integration capability · Qiwa integration capability · Salla integration capability · Zid integration · Foodics integration · Mada · STC Pay · Apple Pay · Tabby · Tamara
- Partner logos: Microsoft · Odoo · Cisco · Huawei · Schneider Electric · Fortinet

#### Product pages — same template: H1 · sub · [Request a Demo] · "What X Solves" · "Key Capabilities" (numbered) · "Business Impact" · form

**CRAS — `/cras/`**
- H1 "Automate Reconciliation Simplify Consolidation" / "أتمت التسويات المالية وبسّط توحيد البيانات المالية"
- "CRAS eliminates manual financial consolidation and delivers real-time multi-entity reporting" / "تعمل منصة CRAS على أتمتة عمليات توحيد البيانات المالية، والاستغناء عن الإجراءات اليدوية، مع توفير تقارير لحظية للمؤسسات متعددة الكيانات."
- What CRAS Solves / التحديات التي تعالجها منصة CRAS — "Many enterprises struggle with:" / "تواجه العديد من المؤسسات تحديات تشمل:" Manual reconciliation / التسويات المالية اليدوية · Delayed financial closing / تأخر عمليات الإقفال المالي · Multi-entity reporting complexity / تعقيد إعداد التقارير للمؤسسات متعددة الكيانات · Limited audit transparency / ضعف الشفافية في عمليات التدقيق — "CRAS centralizes financial data and automates consolidation across entities and currencies." / "تقوم منصة CRAS بتوحيد البيانات المالية وأتمتة عمليات الدمج والتوحيد عبر مختلف الكيانات والعملات."
- Key Capabilities / أبرز القدرات: 01 Automatic generation of financial statements / الإنشاء الآلي للقوائم المالية · 02 Multi-entity consolidation / توحيد البيانات المالية للمؤسسات متعددة الكيانات · 03 Real-time dashboards / لوحات معلومات لحظية · 04 Full audit trails / سجلات تدقيق شاملة · 05 ERP integration / التكامل مع أنظمة تخطيط موارد المؤسسات (ERP)
- Business Impact / الأثر على الأعمال: Faster month-end close / تسريع الإقفال المالي بنهاية الشهر · Reduced reconciliation errors / تقليل أخطاء التسويات المالية · Stronger compliance tracking / تعزيز متابعة الامتثال · Improved financial control / تعزيز الرقابة المالية

**PMS — `/pms/`**
- H1 "Measure What Matters In Real Time" / "قِس ما يهم في الوقت الفعلي"
- "PMS tracks projects, KPIs, and operational performance across departments" / "تتيح منصة PMS متابعة المشاريع، ومؤشرات الأداء الرئيسية (KPIs)، والأداء التشغيلي عبر مختلف الإدارات، من خلال رؤية موحدة وآنية."
- What PMS Solves: Limited performance visibility / ضعف وضوح الأداء · Disconnected KPI tracking / تشتت متابعة مؤشرات الأداء الرئيسية · Manual reporting / إعداد التقارير يدويًا
- Capabilities: 01 Project monitoring / متابعة المشاريع · 02 KPI dashboards / لوحات معلومات مؤشرات الأداء الرئيسية (KPIs) · 03 Executive reporting / التقارير التنفيذية · 04 Performance analytics / تحليلات الأداء
- Business Impact: Clear accountability / تعزيز المساءلة · Faster decision-making / تسريع اتخاذ القرارات · Strategic alignment / مواءمة الأهداف الاستراتيجية · Improved operational transparency / تعزيز الشفافية التشغيلية

**Industrial IoT Platform — `/industrial-iot-platform/`**
- H1 "Operational Intelligence In Real Time" / "ذكاء تشغيلي في الوقت الفعلي"
- "Our Industrial IoT Platform connects machines and systems to deliver production visibility and performance optimization" / "تربط منصة إنترنت الأشياء الصناعية لدينا بين الآلات والأنظمة لتوفير رؤية شاملة للإنتاج، وتحسين الأداء التشغيلي، وتعزيز كفاءة العمليات."
- "What IT Solves" (typo — should be "What It Solves") / التحديات التي تعالجها المنصة: Production downtime / توقف الإنتاج · Limited machine visibility / ضعف الرؤية التشغيلية للآلات · Reactive maintenance / الصيانة التفاعلية · Performance inefficiencies / انخفاض كفاءة الأداء
- Capabilities: 01 Real-time machine data capture / جمع بيانات الآلات في الوقت الفعلي · 02 Edge processing / المعالجة الطرفية · 03 OEE monitoring / مراقبة الكفاءة الشاملة للمعدات (OEE) · 04 Performance alerts / تنبيهات الأداء · 05 AI-driven optimization / التحسين المدعوم بالذكاء الاصطناعي
- Business Impact: Reduced downtime / تقليل فترات التوقف · Higher production efficiency / رفع كفاءة الإنتاج · Improved quality tracking / تحسين متابعة الجودة · Data-driven decision-making / اتخاذ قرارات تستند إلى البيانات

**HRMS** — no page. Only the home card text and menu items (Workforce Management · Payroll Integration · Attendance & Compliance).

---

### Appendix 3 — Assets to reuse (from `/arabia/wp-content/uploads/`)

- Logos: `DWP-Arabia-Site-Logo.png`, `logo-light.png`, `cropped-favicon-270x270.png`
- Badges: `Odoo-Silver-Partner-Logo.png`, `Future-Factories_N.png` (Future Factories Program Approved Service Provider), `The-Factory-Logo.png`
- Partner logos: Microsoft, `Odoo-Logo-DWP-250x250.png`, `Cisco-Logo-DWP-250x250.png`, `Huawei-Logo-DWP-250x250.png`, Schneider Electric, `Fortinet-Logo-1-250x250.png`, `Sap-Logo-DWP.png`, `Oracle-Logo-DWP.png`
- Saudi integrations: ZATCA, Mudad, GOSI, Qiwa, Salla, `Zid-integration.png`, `Foodics-integration.png`, `Mada.png`, `STC-Pay.png`, `Apple-Pay.png`, `Tabby.png`, `Tamara.png`
- Client logos (24 files, `*-250x250.png`) — reuse only those DWP confirms
- Page images: `Who-We-Are-About.jpg`, `What-We-Do-About.jpg`, `ERP-Programs.jpg`, `Data-and-Bi.jpg`, `Applied-Ai.jpg`, `CRAS.jpg`/`What-CRAS-Solves.jpg`, `PMS.jpg`/`What-PMS-solves.jpg`, `Industrial-IoT-Platform.jpg`, `HRMS.jpg`, `Data-and-bi-Solutions.png`, `solutions-ai.png`, `Real-time-dashboards.jpg`, `Full-audit-trails.jpg`, `ERP-integration.jpg`, `Lets-Talk.-Were-All-Ears.png`
- Video: `Feature_Video_1_iPhone.mp4`

---

### Appendix 4 — Content issues found (fix in the new site)

1. **Arabic text is unreadable to Google.** Most Arabic on the site was copied from a PDF and uses "presentation form" characters (look-alike glyphs instead of normal letters), with broken word order and broken lam-alef (لالستماع, اإللكتروني, األعمال, عمالؤنا, التحليالت). Google and on-site search can't match these words. Use the clean Arabic in Appendix 2 only.
2. **SAP is missing** from Partners and the Arabic site. It appears only as a logo in "Platforms We Work With" and, wrongly, as a "client".
3. **Clients page lists vendors as clients** (SAP, Oracle, Microsoft, Odoo, Cisco, Huawei), and most client logos are from other DWP group countries (e.g. Telenor, JazzCash, HEC, NTDC, UBL, Azizi Bank) under the heading "Across the Kingdom". Show only Saudi clients DWP can confirm, or retitle as "Group clients".
4. **HRMS mistranslated in the Arabic menu** as "الدورة الشهرية" (reads as "menstrual cycle"), with PMS sub-items under it. Correct: "نظام الموارد البشرية والرواتب".
5. **"AI" translated as "منظمة العفو الدولية"** (Amnesty International) in the Arabic form dropdown and footer. Correct: "الذكاء الاصطناعي".
6. Other Arabic errors: "حن هنا" → "نحن هنا"; hours "9:0" → "9:00"; "DWP العربية" → "DWP Arabia"; "عمالؤنا" → "عملاؤنا"; "لألداء" → "للأداء"; AR contact page titled "Book a demo".
7. Dead links: "Explore Solutions", "View Products", "Explore All Solutions" all point to `#`. AR hero buttons are swapped and link to the English contact page; AR About button links to the English contact page.
8. Internal notes and theme filler published: "Leadership information will be shared separately…", "Thousands of companies trust us to find their next software partner…", "No Post Found".
9. "Built for Saudi Arabia" page duplicates the home hero and isn't in the menu; it has the best Saudi integration content — rebuild it (C37).
10. Typos: "What IT Solves" → "What It Solves"; H1s missing spaces ("Structured Digital Transformation Clear Business Outcomes", "Automate Reconciliation Simplify Consolidation") — add a line break or dash.
11. Phone field placeholder shows the company's own number — use "+966 5X XXX XXXX".
12. About page has no H1; several pages use H5 for section headings.
13. Arabic pages show fewer partner/client logos than English (2 vs 6 / 2 vs 24).
