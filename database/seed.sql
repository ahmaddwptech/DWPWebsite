-- DWP Arabia — seed data: 6 launch articles (C38) in EN + AR, status draft.
-- Re-runnable. Usage: mysql -u <user> -p <database> < database/seed.sql
SET NAMES utf8mb4;

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'zatca-phase-2-sap-business-one',
  'en',
  'ZATCA Phase 2: What SAP Business One Users Must Do',
  'ZATCA Phase 2 explained for SAP Business One users: what integration with Fatoora requires, how waves work and the steps to go live on time.',
  'ZATCA Phase 2: What SAP Business One Users Must Do',
  'What ZATCA Phase 2 requires and the practical steps SAP Business One users should take before their wave deadline.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> Phase 2 (Integration) requires connecting your invoicing system to ZATCA''s Fatoora platform. This guide explains what that means for SAP Business One users.</p>
<h2>What Phase 2 (Integration) is</h2>
<p>Explain the move from Phase 1 generation to Phase 2 integration with Fatoora.</p>
<h2>How the waves work</h2>
<p>How ZATCA notifies taxpayers and the compliance window after notification.</p>
<h2>Technical requirements</h2>
<p>XML format, cryptographic stamp, QR code, clearance vs reporting, device onboarding.</p>
<h2>How SAP Business One connects to Fatoora</h2>
<p>Direct integration, no double entry; what changes for users.</p>
<h2>Your readiness checklist</h2>
<p>Step-by-step: readiness check, onboarding, sandbox testing, go-live, monitoring.</p>',
  '[["What is ZATCA Phase 2?","Phase 2 (Integration) requires taxpayers to connect their e-invoicing system to ZATCA''s Fatoora platform, in waves."],["How do I know my wave?","ZATCA notifies taxpayers directly. Check the ZATCA website and your notifications."]]',
  '/sap-business-one/zatca-e-invoicing/',
  'ZATCA E-Invoicing for SAP Business One',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'zatca-phase-2-sap-business-one',
  'ar',
  'المرحلة الثانية من الفوترة الإلكترونية: ما يجب على مستخدمي SAP Business One فعله',
  'شرح المرحلة الثانية من الفوترة الإلكترونية لمستخدمي SAP Business One: متطلبات الربط مع منصة فاتورة، والموجات، وخطوات التشغيل في الوقت المحدد.',
  'المرحلة الثانية من الفوترة الإلكترونية: ما يجب على مستخدمي SAP Business One فعله',
  'متطلبات المرحلة الثانية والخطوات العملية التي يجب على مستخدمي SAP Business One اتخاذها قبل موعد موجتهم.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> تتطلب المرحلة الثانية (الربط والتكامل) ربط نظام الفوترة بمنصة فاتورة. يوضح هذا الدليل ما يعنيه ذلك لمستخدمي SAP Business One.</p>
<h2>ما هي المرحلة الثانية (الربط والتكامل)</h2>
<p>شرح الانتقال من مرحلة الإصدار إلى مرحلة الربط مع منصة فاتورة.</p>
<h2>كيف تعمل الموجات</h2>
<p>كيف تبلغ الهيئة المكلفين والمهلة المحددة بعد الإبلاغ.</p>
<h2>المتطلبات التقنية</h2>
<p>صيغة XML والختم التشفيري ورمز QR والاعتماد والإبلاغ وتسجيل الأجهزة.</p>
<h2>كيف يرتبط SAP Business One بمنصة فاتورة</h2>
<p>ربط مباشر دون إدخال مزدوج، وما الذي يتغير على المستخدمين.</p>
<h2>قائمة التحقق من الجاهزية</h2>
<p>فحص الجاهزية ثم التسجيل ثم الاختبار ثم التشغيل ثم المتابعة.</p>',
  '[["ما هي المرحلة الثانية من الفوترة الإلكترونية؟","تتطلب المرحلة الثانية ربط نظام الفوترة الإلكترونية بمنصة فاتورة التابعة لهيئة الزكاة والضريبة والجمارك على شكل موجات."],["كيف أعرف موعد موجتي؟","تبلغ الهيئة المكلفين مباشرة، ويمكنك متابعة موقع الهيئة وإشعاراتك."]]',
  '/sap-business-one/zatca-e-invoicing/',
  'الفوترة الإلكترونية مع SAP Business One',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'signs-outgrown-accounting-software',
  'en',
  '7 Signs Your Company Has Outgrown Its Accounting Software',
  'Slow month-end, wrong stock numbers, Excel reports? Seven signs your Saudi company has outgrown basic accounting software and needs an ERP.',
  '7 Signs Your Company Has Outgrown Its Accounting Software',
  'Seven clear signs it is time to move from basic accounting software to an ERP like SAP Business One.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> Accounting software works well for a start, but growing companies reach a point where it holds them back.</p>
<h2>Month-end takes too long</h2>
<p>Why closing drags on and what it costs.</p>
<h2>Stock and sales data never match</h2>
<p>Disconnected systems and manual reconciliation.</p>
<h2>Reports need Excel work</h2>
<p>Time lost re-keying data for management reports.</p>
<h2>New branches, warehouses and ZATCA Phase 2</h2>
<p>Growth and compliance pressures that basic tools cannot handle.</p>
<h2>What to do next</h2>
<p>How to plan a move to SAP Business One with no data loss.</p>',
  '[["When should we move to an ERP?","When several of these signs apply, especially if you are adding branches, warehouses or facing ZATCA Phase 2."],["Will we lose our data?","No. Opening balances, customers, suppliers, items and open documents are migrated and tested before go-live."]]',
  '/sap-business-one/switch/',
  'Switch to SAP Business One',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'signs-outgrown-accounting-software',
  'ar',
  '7 علامات أن شركتك تجاوزت برنامجها المحاسبي',
  'إغلاق شهري بطيء وأرقام مخزون غير دقيقة وتقارير على Excel؟ سبع علامات تدل على أن شركتك تحتاج إلى نظام ERP.',
  '7 علامات أن شركتك تجاوزت برنامجها المحاسبي',
  'سبع علامات واضحة على أن الوقت حان للانتقال من البرنامج المحاسبي إلى نظام ERP مثل SAP Business One.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> يناسب البرنامج المحاسبي البدايات، لكن الشركات النامية تصل إلى مرحلة يصبح فيها عائقاً أمام نموها.</p>
<h2>الإغلاق الشهري يطول</h2>
<p>أسباب تأخر الإغلاق وتكلفته.</p>
<h2>بيانات المخزون والمبيعات غير متطابقة</h2>
<p>الأنظمة غير المترابطة والمطابقة اليدوية.</p>
<h2>التقارير تحتاج عملاً على Excel</h2>
<p>الوقت الضائع في إعادة إدخال البيانات لتقارير الإدارة.</p>
<h2>فروع ومستودعات جديدة والمرحلة الثانية من الفوترة</h2>
<p>ضغوط النمو والامتثال التي لا تتحملها الأدوات البسيطة.</p>
<h2>الخطوة التالية</h2>
<p>كيف تخطط للانتقال إلى SAP Business One دون فقدان بياناتك.</p>',
  '[["متى ننتقل إلى نظام ERP؟","عندما تنطبق عدة علامات من هذه، خاصة عند إضافة فروع أو مستودعات أو مع اقتراب المرحلة الثانية من الفوترة."],["هل سنفقد بياناتنا؟","لا، يتم نقل الأرصدة الافتتاحية والعملاء والموردين والأصناف والمستندات المفتوحة واختبارها قبل الإطلاق."]]',
  '/sap-business-one/switch/',
  'الانتقال إلى SAP Business One',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'sap-business-one-vs-odoo-saudi',
  'en',
  'SAP Business One vs Odoo for Saudi Companies',
  'SAP Business One or Odoo? An honest comparison for Saudi companies by size, industry, budget and growth plans, from a partner for both.',
  'SAP Business One vs Odoo for Saudi Companies',
  'An honest comparison of SAP Business One and Odoo for Saudi companies, from a partner that implements both.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> As a partner for both systems, we compare SAP Business One and Odoo by what matters to Saudi companies.</p>
<h2>Who each system is best for</h2>
<p>Company size, process complexity and growth plans.</p>
<h2>Inventory, production and finance depth</h2>
<p>Where each system is stronger.</p>
<h2>Customisation and budget</h2>
<p>Flexibility versus structure; typical cost drivers.</p>
<h2>Saudi compliance and support</h2>
<p>ZATCA, Arabic and local support for both.</p>
<h2>How to decide</h2>
<p>Our free assessment and written recommendation.</p>',
  '[["Which is cheaper?","Odoo often has a lower entry cost; total cost depends on users, scope and support for both."],["Can you help us choose?","Yes. After a free assessment we recommend one system in writing, with reasons."]]',
  '/sap-business-one/vs-odoo/',
  'SAP Business One vs Odoo',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'sap-business-one-vs-odoo-saudi',
  'ar',
  'مقارنة SAP Business One وOdoo للشركات السعودية',
  'SAP Business One أم Odoo؟ مقارنة واضحة للشركات السعودية حسب الحجم والقطاع والميزانية وخطط النمو من شريك للنظامين.',
  'مقارنة SAP Business One وOdoo للشركات السعودية',
  'مقارنة واضحة بين SAP Business One وOdoo للشركات السعودية من شريك يطبق النظامين.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> بصفتنا شريكاً للنظامين، نقارن SAP Business One وOdoo وفق ما يهم الشركات السعودية.</p>
<h2>لمن يناسب كل نظام</h2>
<p>حجم الشركة وتعقيد العمليات وخطط النمو.</p>
<h2>عمق المخزون والإنتاج والمالية</h2>
<p>أين يتفوق كل نظام.</p>
<h2>التخصيص والميزانية</h2>
<p>المرونة مقابل الهيكلة وعوامل التكلفة.</p>
<h2>الامتثال السعودي والدعم</h2>
<p>الفوترة الإلكترونية والعربية والدعم المحلي للنظامين.</p>
<h2>كيف تقرر</h2>
<p>التقييم المجاني والتوصية المكتوبة.</p>',
  '[["أيهما أقل تكلفة؟","غالباً ما تكون تكلفة البداية في Odoo أقل، والتكلفة الإجمالية تعتمد على المستخدمين والنطاق والدعم في النظامين."],["هل تساعدوننا في الاختيار؟","نعم، بعد تقييم مجاني نوصي بنظام واحد كتابياً مع الأسباب."]]',
  '/sap-business-one/vs-odoo/',
  'مقارنة SAP Business One مع Odoo',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'sap-business-one-cost-saudi-arabia',
  'en',
  'How Much Does SAP Business One Cost in Saudi Arabia?',
  'What drives SAP Business One cost in Saudi Arabia: users, licence type, database, hosting and implementation scope. Plan your budget with confidence.',
  'How Much Does SAP Business One Cost in Saudi Arabia?',
  'The five factors that drive SAP Business One cost, so you can budget with confidence.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> There is no single price for SAP Business One. The cost depends on a few clear factors.</p>
<h2>Number and type of users</h2>
<p>How user types affect cost.</p>
<h2>Database and hosting</h2>
<p>SAP HANA or SQL Server; cloud or on-premise.</p>
<h2>Implementation scope</h2>
<p>Modules, reports, integrations and data migration.</p>
<h2>Add-ons and annual support</h2>
<p>Ongoing costs to plan for.</p>
<h2>Getting a tailored quote</h2>
<p>What we need from you to give an accurate quote.</p>',
  '[["Is there a monthly option?","Subscription options may be available depending on hosting; we explain the choices in your quote."],["Are there hidden costs?","We list implementation, add-ons and support clearly in the quote so you can compare."]]',
  '/sap-business-one/pricing/',
  'SAP Business One pricing',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'sap-business-one-cost-saudi-arabia',
  'ar',
  'كم تكلفة SAP Business One في السعودية؟',
  'ما الذي يحدد تكلفة SAP Business One في السعودية: عدد المستخدمين ونوع الترخيص وقاعدة البيانات والاستضافة ونطاق التطبيق.',
  'كم تكلفة SAP Business One في السعودية؟',
  'العوامل الخمسة التي تحدد تكلفة SAP Business One لتخطط لميزانيتك بثقة.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> لا يوجد سعر واحد لنظام SAP Business One، فالتكلفة تعتمد على عوامل واضحة.</p>
<h2>عدد المستخدمين ونوعهم</h2>
<p>كيف يؤثر نوع المستخدم في التكلفة.</p>
<h2>قاعدة البيانات والاستضافة</h2>
<p>SAP HANA أو SQL Server، سحابي أو داخل الشركة.</p>
<h2>نطاق التطبيق</h2>
<p>الوحدات والتقارير والربط ونقل البيانات.</p>
<h2>الإضافات والدعم السنوي</h2>
<p>التكاليف المستمرة التي يجب التخطيط لها.</p>
<h2>الحصول على عرض سعر مخصص</h2>
<p>ما نحتاجه منك لتقديم عرض دقيق.</p>',
  '[["هل يوجد خيار شهري؟","قد تتوفر خيارات اشتراك حسب نوع الاستضافة، ونوضح الخيارات في عرض السعر."],["هل توجد تكاليف خفية؟","نوضح التطبيق والإضافات والدعم بالتفصيل في عرض السعر لتتمكن من المقارنة."]]',
  '/sap-business-one/pricing/',
  'أسعار SAP Business One',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'ai-use-cases-erp-data',
  'en',
  '5 Practical AI Uses on Top of Your ERP Data',
  'Five practical ways Saudi companies use AI on ERP data: forecasting, Arabic assistants, anomaly alerts, cash-flow outlook and invoice capture.',
  '5 Practical AI Uses on Top of Your ERP Data',
  'Five practical AI use cases that turn your ERP history into answers and alerts.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> Your ERP already holds years of sales, stock and finance history. Here are five practical ways AI turns it into value.</p>
<h2>Ask your ERP in Arabic or English</h2>
<p>Natural-language questions answered from ERP data.</p>
<h2>Demand and stock forecasting</h2>
<p>Knowing what to reorder and when.</p>
<h2>Cash-flow outlook</h2>
<p>Expected collections and payments.</p>
<h2>Anomaly alerts</h2>
<p>Unusual prices, discounts or postings flagged automatically.</p>
<h2>Supplier invoice capture</h2>
<p>AI reads invoices and creates drafts for review.</p>',
  '[["Do we need a lot of data?","Most use cases work with the history already in your ERP; we check this in a short workshop."],["Does AI post to our ERP automatically?","No. Connections are read-only by default and any posting needs your approval."]]',
  '/ai/ai-for-sap-business-one/',
  'AI for SAP Business One',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'ai-use-cases-erp-data',
  'ar',
  '5 استخدامات عملية للذكاء الاصطناعي على بيانات نظامك',
  'خمس طرق عملية تستخدم بها الشركات السعودية الذكاء الاصطناعي على بيانات ERP: التنبؤ والمساعدات بالعربية والتنبيهات والتدفق النقدي والفواتير.',
  '5 استخدامات عملية للذكاء الاصطناعي على بيانات نظامك',
  'خمس حالات استخدام عملية تحوّل بيانات نظامك إلى إجابات وتنبيهات.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> يحتفظ نظامك بسنوات من بيانات المبيعات والمخزون والمالية. إليك خمس طرق عملية يحوّلها بها الذكاء الاصطناعي إلى قيمة.</p>
<h2>اسأل نظامك بالعربية أو الإنجليزية</h2>
<p>أسئلة بلغة طبيعية تُجاب من بيانات النظام.</p>
<h2>التنبؤ بالطلب والمخزون</h2>
<p>معرفة ماذا تطلب ومتى.</p>
<h2>توقعات التدفق النقدي</h2>
<p>التحصيلات والمدفوعات المتوقعة.</p>
<h2>تنبيهات الحالات غير الطبيعية</h2>
<p>أسعار أو خصومات أو قيود غير معتادة تُكتشف تلقائياً.</p>
<h2>قراءة فواتير الموردين</h2>
<p>الذكاء الاصطناعي يقرأ الفواتير وينشئ مسودات للمراجعة.</p>',
  '[["هل نحتاج إلى بيانات كثيرة؟","معظم الحالات تعمل بالبيانات الموجودة في نظامك، ونتحقق من ذلك في ورشة قصيرة."],["هل يرحّل الذكاء الاصطناعي إلى نظامنا تلقائياً؟","لا، الربط بصلاحية قراءة افتراضياً وأي ترحيل يتم بموافقتك."]]',
  '/ai/ai-for-sap-business-one/',
  'الذكاء الاصطناعي مع SAP Business One',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'choosing-sap-business-one-partner-ksa',
  'en',
  'Choosing an SAP Business One Partner in KSA: 10 Questions',
  'Ten questions to ask before choosing or changing your SAP Business One partner in Saudi Arabia: team, method, ZATCA, support and more.',
  'Choosing an SAP Business One Partner in KSA: 10 Questions',
  'Ten questions that help you choose, or change, your SAP Business One partner in Saudi Arabia.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> The partner you choose matters as much as the software. Ask these ten questions before you decide.</p>
<h2>Team and certification</h2>
<p>Who will work on your project and their SAP Business One certifications.</p>
<h2>Implementation method</h2>
<p>Phases, sign-offs and how scope is controlled.</p>
<h2>Saudi compliance experience</h2>
<p>ZATCA Phase 2, VAT and local requirements.</p>
<h2>Support after go-live</h2>
<p>Response times, language and local presence.</p>
<h2>Changing partners</h2>
<p>How a handover works and what happens to your licences.</p>',
  '[["Can we change our SAP partner?","Yes. A health check and handover plan make the switch simple."],["Do we keep our licences?","Yes. Licences stay with your company."]]',
  '/sap-business-one/support/',
  'SAP Business One support',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

INSERT INTO insights (slug, lang, title, meta_description, h1, excerpt, published_at, reading_minutes, body_html, faq_json, related_url, related_label, status) VALUES (
  'choosing-sap-business-one-partner-ksa',
  'ar',
  'اختيار شريك SAP Business One في السعودية: 10 أسئلة',
  'عشرة أسئلة قبل اختيار شريك SAP Business One أو تغييره في السعودية: الفريق والمنهجية والفوترة الإلكترونية والدعم.',
  'اختيار شريك SAP Business One في السعودية: 10 أسئلة',
  'عشرة أسئلة تساعدك في اختيار شريك SAP Business One أو تغييره في السعودية.',
  NULL,
  5,
  '<p><mark class="confirm">[DRAFT]</mark> الشريك الذي تختاره لا يقل أهمية عن النظام نفسه. اطرح هذه الأسئلة العشرة قبل أن تقرر.</p>
<h2>الفريق والشهادات</h2>
<p>من سيعمل على مشروعك وشهاداته في SAP Business One.</p>
<h2>منهجية التطبيق</h2>
<p>المراحل والاعتمادات وكيفية ضبط النطاق.</p>
<h2>الخبرة في الامتثال السعودي</h2>
<p>المرحلة الثانية من الفوترة والضريبة والمتطلبات المحلية.</p>
<h2>الدعم بعد الإطلاق</h2>
<p>سرعة الاستجابة واللغة والوجود المحلي.</p>
<h2>تغيير الشريك</h2>
<p>كيف يتم الانتقال وماذا يحدث لتراخيصك.</p>',
  '[["هل يمكننا تغيير شريك SAP؟","نعم، فحص النظام وخطة الانتقال يجعلان التغيير سهلاً."],["هل نحتفظ بتراخيصنا؟","نعم، التراخيص تبقى مع شركتك."]]',
  '/sap-business-one/support/',
  'دعم SAP Business One',
  'draft'
) ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description), h1 = VALUES(h1), excerpt = VALUES(excerpt), reading_minutes = VALUES(reading_minutes), body_html = VALUES(body_html), faq_json = VALUES(faq_json), related_url = VALUES(related_url), related_label = VALUES(related_label);

