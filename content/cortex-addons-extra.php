<?php
/**
 * Outcomes (big result numbers) and local integrations for the eight Cortex add-on pages, in English and Arabic.
 * Used by cortex_addon_page() and by the Cortex AI and Intercompany page files. Figures are typical results and the
 * integrations list what each add-on works with; both are marked for DWP confirmation.
 * Outcome: [value, prefix, suffix, label, [before label, before value], [after label, after value]]
 * Integration: [name, text, icon, logo (optional path under assets)]
 */
$R = fn(string $t) => '[AR-REVIEW]' . $t . '[/AR-REVIEW]';
$o = fn($v, $pre, $suf, $label, $before = null, $after = null) => ['value' => $v, 'prefix' => $pre, 'suffix' => $suf, 'label' => $label, 'before' => $before, 'after' => $after];
$g = fn($name, $text, $icon = 'link', $logo = null) => ['name' => $name, 'text' => $text, 'icon' => $icon, 'logo' => $logo];
$zatca = 'img/partners/zatca.svg';
$heads = [
    'en' => ['oc' => ['Results', 'What changes when you switch it on'], 'ig' => ['Works with', 'Connected to the systems you already use'], 'oc_note' => 'Typical results from DWP projects. [CONFIRM: outcome figures]', 'ig_note' => '[CONFIRM: integration list and scope]'],
    'ar' => ['oc' => [$R('النتائج'), $R('ما الذي يتغير عند تشغيله')], 'ig' => [$R('يعمل مع'), $R('متصل بالأنظمة التي تستخدمها اليوم')], 'oc_note' => $R('نتائج معتادة من مشاريع DWP.') . ' [CONFIRM: outcome figures]', 'ig_note' => '[CONFIRM: integration list and scope]'],
];
$data = [
'production' => [
    'en' => ['oc' => [
        $o(1, '', ' day', 'To cost every production order', ['Before: 6 days in spreadsheets', 6], ['With Cortex Production: 1 day', 1]),
        $o(25, '', '%', 'Less scrap on the lines', ['Scrap rate before: 2.4%', 2.4], ['Scrap rate now: 1.8%', 1.8]),
        $o(94, '', '%', 'Orders finished on time', ['Before: 78%', 78], ['With live tracking: 94%', 94]),
        $o(15, '', '%', 'More output from the same lines', ['OEE before: 68%', 68], ['OEE now: 78%', 78]),
    ], 'ig' => [
        $g('Machines and PLCs', 'Live counts and status from the line through the DWP IoT platform.', 'factory'),
        $g('Barcode and labels', 'Scanners and label printers for materials, lots and finished goods.', 'doc'),
        $g('Scales and weighbridges', 'Weights captured straight into receipts and production.', 'box'),
        $g('ZATCA', 'E-invoices for every finished goods sale, through SAP Business One.', 'receipt', $zatca),
        $g('Tablets on the floor', 'Operators report output, scrap and downtime from the line.', 'chat'),
        $g('Microsoft Excel', 'Production plans and BOMs imported from Excel.', 'doc'),
    ]],
    'ar' => ['oc' => [
        $o(1, '', $R(' يوم'), $R('لحساب تكلفة كل أمر إنتاج'), [$R('قبل: 6 أيام في جداول البيانات'), 6], [$R('مع Cortex Production: يوم واحد'), 1]),
        $o(25, '', '%', $R('هالك أقل على خطوط الإنتاج'), [$R('نسبة الهالك قبل: 2.4%'), 2.4], [$R('نسبة الهالك الآن: 1.8%'), 1.8]),
        $o(94, '', '%', $R('أوامر تُنجز في موعدها'), [$R('قبل: 78%'), 78], [$R('مع المتابعة المباشرة: 94%'), 94]),
        $o(15, '', '%', $R('إنتاج أكثر من الخطوط نفسها'), [$R('الكفاءة الشاملة قبل: 68%'), 68], [$R('الكفاءة الشاملة الآن: 78%'), 78]),
    ], 'ig' => [
        $g($R('الآلات وأنظمة التحكم'), $R('أعداد وحالة مباشرة من الخط عبر منصة DWP لإنترنت الأشياء.'), 'factory'),
        $g($R('الباركود والملصقات'), $R('قارئات وطابعات ملصقات للمواد والدفعات والمنتج النهائي.'), 'doc'),
        $g($R('الموازين'), $R('تسجيل الأوزان مباشرة في الاستلام والإنتاج.'), 'box'),
        $g('ZATCA', $R('فواتير إلكترونية لكل بيع للمنتج النهائي عبر SAP Business One.'), 'receipt', $zatca),
        $g($R('أجهزة لوحية في المصنع'), $R('يسجّل المشغلون الإنتاج والهالك والتوقف من الخط.'), 'chat'),
        $g('Microsoft Excel', $R('استيراد خطط الإنتاج وقوائم المواد من Excel.'), 'doc'),
    ]],
],
'project-management' => [
    'en' => ['oc' => [
        $o(3, '', ' days', 'From progress to client invoice', ['Before: 12 days', 12], ['With Cortex Project Management: 3 days', 3]),
        $o(1, '', ' hour', 'To turn a ready BOQ into a budget', ['Before: 3 working days', 24], ['Now: 1 hour', 1]),
        $o(4, '', ' weeks', 'Earlier warning of projects going over budget', ['Found at month end', 1], ['Seen as costs post', 4]),
        $o(100, '', '%', 'Retention and advances tracked', ['In spreadsheets: 60%', 60], ['In the system: 100%', 100]),
    ], 'ig' => [
        $g('Excel BOQ', 'Ready BOQs from clients and consultants imported and analysed.', 'doc'),
        $g('ZATCA', 'Progress invoices issued as e-invoices through SAP Business One.', 'receipt', $zatca),
        $g('Etimad', 'Government tenders and contracts recorded against projects.', 'briefcase'),
        $g('Saudi banks', 'Bank guarantees, advance payments and supplier payments.', 'coins'),
        $g('Primavera and MS Project', 'Schedules imported to the project Gantt chart.', 'calendar'),
        $g('Mobile site app', 'Site engineers report progress, hours and materials from site.', 'chat'),
    ]],
    'ar' => ['oc' => [
        $o(3, '', $R(' أيام'), $R('من الإنجاز إلى فاتورة العميل'), [$R('قبل: 12 يوماً'), 12], [$R('مع Cortex Project Management: 3 أيام'), 3]),
        $o(1, '', $R(' ساعة'), $R('لتحويل جدول كميات جاهز إلى ميزانية'), [$R('قبل: 3 أيام عمل'), 24], [$R('الآن: ساعة واحدة'), 1]),
        $o(4, '', $R(' أسابيع'), $R('إنذار أبكر بتجاوز ميزانية المشروع'), [$R('يُكتشف في نهاية الشهر'), 1], [$R('يظهر مع ترحيل التكاليف'), 4]),
        $o(100, '', '%', $R('متابعة المحتجزات والدفعات المقدمة'), [$R('في جداول البيانات: 60%'), 60], [$R('في النظام: 100%'), 100]),
    ], 'ig' => [
        $g($R('جداول كميات Excel'), $R('استيراد جداول الكميات الجاهزة من العملاء والاستشاريين وتحليلها.'), 'doc'),
        $g('ZATCA', $R('إصدار فواتير الإنجاز كفواتير إلكترونية عبر SAP Business One.'), 'receipt', $zatca),
        $g($R('منصة اعتماد'), $R('تسجيل المناقصات والعقود الحكومية على المشاريع.'), 'briefcase'),
        $g($R('البنوك السعودية'), $R('الضمانات البنكية والدفعات المقدمة ومدفوعات الموردين.'), 'coins'),
        $g('Primavera / MS Project', $R('استيراد الجداول الزمنية إلى مخطط جانت للمشروع.'), 'calendar'),
        $g($R('تطبيق الموقع'), $R('يسجّل مهندسو الموقع الإنجاز والساعات والمواد من الموقع.'), 'chat'),
    ]],
],
'hr-payroll' => [
    'en' => ['oc' => [
        $o(1, '', ' day', 'To run and post the monthly payroll', ['Before: 5 days', 5], ['With Cortex HR & Payroll: 1 day', 1]),
        $o(100, '', '%', 'Iqamas and contracts renewed on time', ['Before: 82%', 82], ['With expiry alerts: 100%', 100]),
        $o(60, '', '%', 'Less HR time on routine requests', ['Paper and email requests', 100], ['Self service and approvals', 40]),
        $o(0, '', '', 'Manual payroll journal entries', ['Before: 40 entries a month', 40], ['Now: posted automatically', 0]),
    ], 'ig' => [
        $g('GOSI', 'Contributions calculated on every payroll run.', 'shield'),
        $g('Mudad', 'Salary files for the Wage Protection System.', 'coins'),
        $g('Qiwa', 'Contracts and workforce data kept in step.', 'users'),
        $g('Muqeem', 'Iqama and visa details for expiry alerts.', 'doc'),
        $g('Saudi banks', 'Salary transfer files for your bank.', 'coins'),
        $g('Attendance devices', 'Check ins from fingerprint, face and mobile.', 'clock'),
    ]],
    'ar' => ['oc' => [
        $o(1, '', $R(' يوم'), $R('لتشغيل مسير الرواتب الشهري وترحيله'), [$R('قبل: 5 أيام'), 5], [$R('مع Cortex HR & Payroll: يوم واحد'), 1]),
        $o(100, '', '%', $R('تجديد الإقامات والعقود في موعدها'), [$R('قبل: 82%'), 82], [$R('مع تنبيهات الانتهاء: 100%'), 100]),
        $o(60, '', '%', $R('وقت أقل للموارد البشرية على الطلبات المعتادة'), [$R('طلبات ورقية وبالبريد'), 100], [$R('خدمة ذاتية واعتمادات'), 40]),
        $o(0, '', '', $R('قيود رواتب يدوية'), [$R('قبل: 40 قيداً شهرياً'), 40], [$R('الآن: ترحيل تلقائي'), 0]),
    ], 'ig' => [
        $g($R('التأمينات الاجتماعية'), $R('حساب الاشتراكات في كل مسير رواتب.'), 'shield'),
        $g($R('مدد'), $R('ملفات الرواتب لنظام حماية الأجور.'), 'coins'),
        $g($R('قوى'), $R('العقود وبيانات القوى العاملة متوافقة.'), 'users'),
        $g($R('مقيم'), $R('بيانات الإقامات والتأشيرات لتنبيهات الانتهاء.'), 'doc'),
        $g($R('البنوك السعودية'), $R('ملفات تحويل الرواتب لبنكك.'), 'coins'),
        $g($R('أجهزة الحضور'), $R('تسجيل الحضور بالبصمة والوجه والجوال.'), 'clock'),
    ]],
],
'pos' => [
    'en' => ['oc' => [
        $o(18, '', ' sec', 'Average checkout', ['Before: 45 seconds', 45], ['With Cortex POS: 18 seconds', 18]),
        $o(98, '', '%', 'Stock accuracy in every branch', ['Before: 85%', 85], ['With live stock: 98%', 98]),
        $o(10, '', ' min', 'To close the day in a branch', ['Before: 60 minutes', 60], ['Now: 10 minutes', 10]),
        $o(100, '', '%', 'Receipts issued as e-invoices', ['Paper receipts', 0], ['ZATCA e-invoices', 100]),
    ], 'ig' => [
        $g('ZATCA', 'Simplified tax invoices with QR codes for every receipt.', 'receipt', $zatca),
        $g('mada', 'Card payments on the counter terminal.', 'coins', 'img/integrations/trim/mada.webp'),
        $g('Apple Pay', 'Tap to pay with phones and watches.', 'coins', 'img/integrations/trim/apple-pay.webp'),
        $g('STC Pay', 'Wallet payments at the counter.', 'coins', 'img/integrations/trim/stc-pay.webp'),
        $g('Tabby', 'Buy now, pay later in four payments.', 'coins', 'img/integrations/trim/tabby.webp'),
        $g('Tamara', 'Split payments for larger baskets.', 'coins', 'img/integrations/trim/tamara.webp'),
    ]],
    'ar' => ['oc' => [
        $o(18, '', $R(' ثانية'), $R('متوسط زمن الدفع'), [$R('قبل: 45 ثانية'), 45], [$R('مع Cortex POS: 18 ثانية'), 18]),
        $o(98, '', '%', $R('دقة المخزون في كل فرع'), [$R('قبل: 85%'), 85], [$R('مع المخزون اللحظي: 98%'), 98]),
        $o(10, '', $R(' دقائق'), $R('لإقفال اليوم في الفرع'), [$R('قبل: 60 دقيقة'), 60], [$R('الآن: 10 دقائق'), 10]),
        $o(100, '', '%', $R('إيصالات تصدر كفواتير إلكترونية'), [$R('إيصالات ورقية'), 0], [$R('فواتير إلكترونية'), 100]),
    ], 'ig' => [
        $g('ZATCA', $R('فواتير ضريبية مبسطة برمز QR لكل إيصال.'), 'receipt', $zatca),
        $g('مدى', $R('الدفع بالبطاقة على جهاز نقطة البيع.'), 'coins', 'img/integrations/trim/mada.webp'),
        $g('Apple Pay', $R('الدفع باللمس بالجوال والساعة.'), 'coins', 'img/integrations/trim/apple-pay.webp'),
        $g('STC Pay', $R('الدفع بالمحفظة عند الكاشير.'), 'coins', 'img/integrations/trim/stc-pay.webp'),
        $g('Tabby', $R('اشترِ الآن وادفع لاحقاً على أربع دفعات.'), 'coins', 'img/integrations/trim/tabby.webp'),
        $g('Tamara', $R('تقسيط المشتريات الأكبر.'), 'coins', 'img/integrations/trim/tamara.webp'),
    ]],
],
'property-management' => [
    'en' => ['oc' => [
        $o(96, '', '%', 'Rent collected on time', ['Before: 82%', 82], ['With reminders and matching: 96%', 96]),
        $o(40, '', '%', 'Less overdue rent', ['Overdue before', 100], ['Overdue now', 60]),
        $o(5, '', ' min', 'To issue the monthly rent invoices', ['Before: 2 days', 48], ['Now: 5 minutes', 1]),
        $o(18, '', ' days', 'Average time a unit stays vacant', ['Before: 45 days', 45], ['With renewal alerts: 18 days', 18]),
    ], 'ig' => [
        $g('Ejar', 'Lease contracts registered and kept in step.', 'doc'),
        $g('SADAD', 'Tenants pay rent bills through their bank.', 'coins'),
        $g('ZATCA', 'E-invoices for commercial rent with VAT.', 'receipt', $zatca),
        $g('mada', 'Card payments at the leasing office.', 'coins', 'img/integrations/trim/mada.webp'),
        $g('Saudi banks', 'Transfers and cheques matched to leases.', 'coins'),
        $g('SMS and email', 'Rent reminders and receipts sent to tenants.', 'chat'),
    ]],
    'ar' => ['oc' => [
        $o(96, '', '%', $R('إيجارات محصلة في موعدها'), [$R('قبل: 82%'), 82], [$R('مع التذكيرات والمطابقة: 96%'), 96]),
        $o(40, '', '%', $R('إيجارات متأخرة أقل'), [$R('المتأخرات قبل'), 100], [$R('المتأخرات الآن'), 60]),
        $o(5, '', $R(' دقائق'), $R('لإصدار فواتير الإيجار الشهرية'), [$R('قبل: يومان'), 48], [$R('الآن: 5 دقائق'), 1]),
        $o(18, '', $R(' يوماً'), $R('متوسط بقاء الوحدة شاغرة'), [$R('قبل: 45 يوماً'), 45], [$R('مع تنبيهات التجديد: 18 يوماً'), 18]),
    ], 'ig' => [
        $g($R('إيجار'), $R('توثيق عقود الإيجار ومتابعتها.'), 'doc'),
        $g($R('سداد'), $R('يدفع المستأجرون فواتير الإيجار عبر بنوكهم.'), 'coins'),
        $g('ZATCA', $R('فواتير إلكترونية للإيجارات التجارية مع الضريبة.'), 'receipt', $zatca),
        $g('مدى', $R('الدفع بالبطاقة في مكتب التأجير.'), 'coins', 'img/integrations/trim/mada.webp'),
        $g($R('البنوك السعودية'), $R('مطابقة التحويلات والشيكات مع العقود.'), 'coins'),
        $g($R('الرسائل والبريد'), $R('إرسال تذكيرات الإيجار والإيصالات للمستأجرين.'), 'chat'),
    ]],
],
'assets-management' => [
    'en' => ['oc' => [
        $o(3, '', ' days', 'For the annual asset count', ['Before: 15 days', 15], ['Scanning with phones: 3 days', 3]),
        $o(98, '', '%', 'Assets found at their location', ['Before: 86%', 86], ['Now: 98%', 98]),
        $o(95, '', '%', 'Maintenance done on time', ['Before: 70%', 70], ['With plans and alerts: 95%', 95]),
        $o(0, '', '', 'Depreciation spreadsheets', ['Before: one per asset class', 9], ['Now: one run in the system', 0]),
    ], 'ig' => [
        $g('Barcode and QR tags', 'Every asset labelled and scanned.', 'doc'),
        $g('Mobile counting app', 'Custodians and counters scan with their phones.', 'chat'),
        $g('ZATCA', 'Fixed asset schedules ready for the Zakat return.', 'receipt', $zatca),
        $g('Insurance companies', 'Policies and claims tracked per asset.', 'shield'),
        $g('Fleet tracking', 'Vehicle hours and kilometres for maintenance plans.', 'map'),
        $g('SAP Business One purchasing', 'Assets capitalised straight from purchase invoices.', 'cart'),
    ]],
    'ar' => ['oc' => [
        $o(3, '', $R(' أيام'), $R('للجرد السنوي للأصول'), [$R('قبل: 15 يوماً'), 15], [$R('بالمسح بالجوال: 3 أيام'), 3]),
        $o(98, '', '%', $R('أصول موجودة في مواقعها'), [$R('قبل: 86%'), 86], [$R('الآن: 98%'), 98]),
        $o(95, '', '%', $R('صيانة تتم في موعدها'), [$R('قبل: 70%'), 70], [$R('مع الخطط والتنبيهات: 95%'), 95]),
        $o(0, '', '', $R('جداول إهلاك يدوية'), [$R('قبل: جدول لكل فئة أصول'), 9], [$R('الآن: احتساب واحد في النظام'), 0]),
    ], 'ig' => [
        $g($R('ملصقات باركود وQR'), $R('كل أصل مُرقّم ويُمسح.'), 'doc'),
        $g($R('تطبيق الجرد بالجوال'), $R('يمسح أصحاب العهد والجرادون بجوالاتهم.'), 'chat'),
        $g('ZATCA', $R('جداول الأصول الثابتة جاهزة للإقرار الزكوي.'), 'receipt', $zatca),
        $g($R('شركات التأمين'), $R('متابعة الوثائق والمطالبات لكل أصل.'), 'shield'),
        $g($R('تتبع الأسطول'), $R('ساعات وكيلومترات المركبات لخطط الصيانة.'), 'map'),
        $g($R('مشتريات SAP Business One'), $R('رسملة الأصول مباشرة من فواتير الشراء.'), 'cart'),
    ]],
],
'ai' => [
    'en' => ['oc' => [
        $o(10, '', ' sec', 'For a report you used to wait days for', ['Before: 2 days from IT', 48], ['Ask Cortex AI: seconds', 1]),
        $o(2, '', '', 'Languages, Arabic and English', null, null),
        $o(24, '', '/7', 'Answers on the web and mobile', ['Office hours only', 8], ['Any time, anywhere', 24]),
        $o(30, '', '%', 'Fewer stock outs with early alerts', ['Stock outs before', 100], ['With alerts', 70]),
    ], 'ig' => [
        $g('Your ERP', 'Reads your live ERP data, read only.', 'link'),
        $g('iPhone and Android', 'Quick questions and alerts on the mobile app.', 'chat'),
        $g('Microsoft Excel', 'Every answer and report exported to Excel.', 'doc'),
        $g('Email', 'Scheduled reports sent to your inbox.', 'chat'),
        $g('DWP Cloud', 'Hosted in Saudi Arabia on our local cloud.', 'cloud'),
        $g('Your documents', 'Supplier invoices and orders read for posting.', 'receipt'),
    ]],
    'ar' => ['oc' => [
        $o(10, '', $R(' ثوانٍ'), $R('لتقرير كنت تنتظره أياماً'), [$R('قبل: يومان من تقنية المعلومات'), 48], [$R('اسأل Cortex AI: ثوانٍ'), 1]),
        $o(2, '', '', $R('لغتان: العربية والإنجليزية'), null, null),
        $o(24, '', '/7', $R('إجابات على الويب والجوال'), [$R('ساعات الدوام فقط'), 8], [$R('في أي وقت ومن أي مكان'), 24]),
        $o(30, '', '%', $R('نفاد مخزون أقل بالتنبيهات المبكرة'), [$R('النفاد قبل'), 100], [$R('مع التنبيهات'), 70]),
    ], 'ig' => [
        $g($R('نظامك'), $R('يقرأ بيانات نظامك المباشرة بصلاحية قراءة فقط.'), 'link'),
        $g('iPhone / Android', $R('أسئلة سريعة وتنبيهات على تطبيق الجوال.'), 'chat'),
        $g('Microsoft Excel', $R('تصدير كل إجابة وتقرير إلى Excel.'), 'doc'),
        $g($R('البريد الإلكتروني'), $R('تقارير مجدولة تصل إلى بريدك.'), 'chat'),
        $g($R('سحابة DWP'), $R('مستضاف في المملكة على سحابتنا المحلية.'), 'cloud'),
        $g($R('مستنداتك'), $R('قراءة فواتير وطلبات الموردين للترحيل.'), 'receipt'),
    ]],
],
'intercompany' => [
    'en' => ['oc' => [
        $o(3, '', ' days', 'To close the group month', ['Before: 10 days', 10], ['With Cortex Intercompany: 3 days', 3]),
        $o(0, '', '', 'Documents typed twice', ['Before: every intercompany sale', 100], ['Now: mirrored automatically', 0]),
        $o(100, '', '%', 'Intercompany balances matched', ['Before: 74% at month end', 74], ['Now: 100%', 100]),
        $o(1, '', '', 'Place to create master data for the group', ['Before: once per company', 4], ['Now: once at head office', 1]),
    ], 'ig' => [
        $g('SAP Business One', 'Every group company in its own SAP Business One company.', 'link'),
        $g('ZATCA', 'Each company issues its own e-invoices.', 'receipt', $zatca),
        $g('Saudi banks', 'Payments on behalf and settlements between companies.', 'coins'),
        $g('Consolidation reports', 'Eliminations ready for the group statements.', 'chart'),
    ]],
    'ar' => ['oc' => [
        $o(3, '', $R(' أيام'), $R('لإقفال شهر المجموعة'), [$R('قبل: 10 أيام'), 10], [$R('مع Cortex Intercompany: 3 أيام'), 3]),
        $o(0, '', '', $R('مستندات تُدخل مرتين'), [$R('قبل: كل بيع بين الشركات'), 100], [$R('الآن: تنعكس تلقائياً'), 0]),
        $o(100, '', '%', $R('أرصدة متطابقة بين الشركات'), [$R('قبل: 74% في نهاية الشهر'), 74], [$R('الآن: 100%'), 100]),
        $o(1, '', '', $R('مكان واحد لإنشاء البيانات الأساسية'), [$R('قبل: في كل شركة'), 4], [$R('الآن: مرة في المركز الرئيسي'), 1]),
    ], 'ig' => [
        $g('SAP Business One', $R('كل شركة في المجموعة لها شركتها في SAP Business One.'), 'link'),
        $g('ZATCA', $R('تصدر كل شركة فواتيرها الإلكترونية.'), 'receipt', $zatca),
        $g($R('البنوك السعودية'), $R('المدفوعات نيابة عن الشركات والتسويات بينها.'), 'coins'),
        $g($R('تقارير التوحيد'), $R('الاستبعادات جاهزة للقوائم الموحدة.'), 'chart'),
    ]],
],
];
$out = [];
foreach ($data as $slug => $langs) {
    foreach ($langs as $lang => $d) {
        $h = $heads[$lang];
        $out[$slug][$lang] = [
            'outcomes' => ['eyebrow' => $h['oc'][0], 'h2' => $h['oc'][1], 'note' => $h['oc_note'], 'items' => $d['oc']],
            'integrations' => ['eyebrow' => $h['ig'][0], 'h2' => $h['ig'][1], 'note' => $h['ig_note'], 'items' => $d['ig']],
        ];
    }
}
return $out;
