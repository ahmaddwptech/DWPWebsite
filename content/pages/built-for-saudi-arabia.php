<?php
// C37. Built for Saudi Arabia, /built-for-saudi-arabia/
$hr = cfg('hrms_enabled') ? '/hrms/' : null;
$gov = function (bool $ar) use ($hr) {
    $R = fn($t) => $ar ? '[AR-REVIEW]' . $t . '[/AR-REVIEW]' : $t;
    $items = [
        ['receipt', 'ZATCA Phase 2 e-invoicing', 'Invoices cleared and reported through Fatoora, straight from your ERP.', 'الفوترة الإلكترونية: المرحلة الثانية (زاتكا)', 'فواتير تُعتمد وتُبلَّغ عبر منصة فاتورة مباشرة من نظامك.', '/sap-business-one/zatca-e-invoicing/'],
        ['coins', 'VAT', 'Saudi VAT codes on every document and reports ready for your return.', 'ضريبة القيمة المضافة', 'رموز الضريبة السعودية على كل مستند وتقارير جاهزة للإقرار.', null],
        ['shield', 'GOSI', 'Social insurance contributions calculated with payroll. [CONFIRM: HR add-on]', 'التأمينات الاجتماعية', 'احتساب اشتراكات التأمينات مع الرواتب. [CONFIRM: HR add-on]', $hr],
        ['users', 'Mudad', 'Payroll files prepared for the Wage Protection System. [CONFIRM: HR add-on]', 'مدد', 'ملفات رواتب جاهزة لنظام حماية الأجور. [CONFIRM: HR add-on]', $hr],
        ['briefcase', 'Qiwa', 'Employee and contract data kept consistent with Qiwa. [CONFIRM: HR add-on]', 'قوى', 'بيانات الموظفين والعقود متسقة مع منصة قوى. [CONFIRM: HR add-on]', $hr],
        ['doc', 'Arabic documents', 'Invoices, statements and reports in Arabic and English, with Hijri dates.', 'مستندات عربية', 'فواتير وكشوف وتقارير بالعربية والإنجليزية مع التاريخ الهجري.', null],
    ];
    return array_map(fn($i) => array_filter(['icon' => $i[0], 'title' => $R($ar ? $i[3] : $i[1]), 'text' => $R($ar ? $i[4] : $i[2]), 'href' => $i[5]]), $items);
};
$commerce = fn(bool $ar) => array_map(fn($n) => ['name' => $n], $ar
    ? ['سلة', 'زد', 'فودكس', 'مدى', 'STC Pay', 'Apple Pay', 'تابي', 'تمارا']
    : ['Salla', 'Zid', 'Foodics', 'mada', 'STC Pay', 'Apple Pay', 'Tabby', 'Tamara']);
return [
    'en' => [
        'name' => 'Built for Saudi Arabia',
        'title' => 'ZATCA, GOSI & Mudad Ready ERP | DWP Arabia',
        'meta' => 'ERP set up for Saudi rules: ZATCA e-invoicing, VAT, GOSI, Mudad and Qiwa, with Arabic documents and local support.',
        'h1' => 'Built for Saudi Arabia',
        'sections' => [
            ['type' => 'hero', 'image' => ['src' => 'img/gen/built-for-saudi.webp', 'alt' => 'Café owner checking sales on a point-of-sale tablet as a customer pays by phone', 'w' => 1600, 'h' => 1067],
                'sub' => 'ERP set up for Saudi rules: ZATCA e-invoicing, VAT, GOSI, Mudad and Qiwa, with Arabic documents and local support from Riyadh.',
                'buttons' => [['Book a Free Demo', '/book-a-demo/'], ['Free Assessment', '/sap-business-one/free-assessment/']]],
            ['type' => 'cards',
                'h2' => 'Saudi government platforms',
                'intro' => 'Your ERP is set up for the rules and platforms every Saudi company deals with.',
                'cols' => 3,
                'items' => $gov(false)],
            ['type' => 'logos',
                'h2' => 'E-commerce, POS and payments',
                'intro' => 'We connect your ERP to the Saudi sales and payment platforms your business already uses.',
                'bg' => 'muted',
                'items' => $commerce(false),
                'closing' => 'Supporting the digital transformation goals of Saudi Vision 2030.'],
            ['type' => 'lead_form', 'interest' => 'zatca'],
            ['type' => 'cta_band'],
        ],
    ],
    'ar' => [
        'name' => 'مصمم للسعودية',
        'title' => 'نظام ERP متوافق مع زاتكا والتأمينات ومدد',
        'meta' => 'نظام ERP مهيأ للأنظمة السعودية: الفوترة الإلكترونية والضريبة والتأمينات ومدد وقوى، مع مستندات عربية ودعم محلي.',
        'h1' => 'مصمم للمملكة العربية السعودية',
        'sections' => [
            ['type' => 'hero', 'image' => ['src' => 'img/gen/built-for-saudi.webp', 'alt' => 'صاحب مقهى يراجع المبيعات على جهاز نقطة البيع بينما يدفع عميل بهاتفه', 'w' => 1600, 'h' => 1067],
                'sub' => '[AR-REVIEW]نظام ERP مهيأ للأنظمة السعودية: الفوترة الإلكترونية والضريبة والتأمينات ومدد وقوى، مع مستندات عربية ودعم محلي من الرياض.[/AR-REVIEW]',
                'buttons' => [['احجز عرضاً مجانياً', '/book-a-demo/'], ['تقييم مجاني', '/sap-business-one/free-assessment/']]],
            ['type' => 'cards',
                'h2' => '[AR-REVIEW]المنصات الحكومية السعودية[/AR-REVIEW]',
                'intro' => '[AR-REVIEW]نُهيّئ نظامك للأنظمة والمنصات التي تتعامل معها كل شركة سعودية.[/AR-REVIEW]',
                'cols' => 3,
                'items' => $gov(true)],
            ['type' => 'logos',
                'h2' => '[AR-REVIEW]التجارة الإلكترونية ونقاط البيع والدفع[/AR-REVIEW]',
                'intro' => '[AR-REVIEW]نربط نظامك بمنصات البيع والدفع السعودية التي تستخدمها شركتك.[/AR-REVIEW]',
                'bg' => 'muted',
                'items' => $commerce(true),
                'closing' => '[AR-REVIEW]دعماً لمستهدفات التحول الرقمي في رؤية المملكة 2030.[/AR-REVIEW]'],
            ['type' => 'lead_form', 'interest' => 'zatca'],
            ['type' => 'cta_band'],
        ],
    ],
];
