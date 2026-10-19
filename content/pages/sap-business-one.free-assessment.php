<?php
// C16. Free Assessment, /sap-business-one/free-assessment/ (lead magnet; questions live in app/lib/assessment.php)
return [
    'en' => [
        'name' => 'Free Assessment',
        'title' => 'Free ERP Readiness Assessment | DWP Arabia',
        'meta' => 'Answer 10 questions and get a free report on whether SAP Business One fits your company, plus next steps.',
        'h1' => 'Is Your Company Ready for SAP Business One?',
        'sections' => [
            ['type' => 'hero', 'image' => ['src' => 'img/gen/free-assessment.webp', 'alt' => 'Company owner answering assessment questions on a tablet with a consultant', 'w' => 1600, 'h' => 1067],
                'sub' => 'Answer 10 quick questions and get a free report on whether SAP Business One fits your company, plus clear next steps.',
                'buttons' => [['Start the assessment', '#assessment']]],
            ['type' => 'assessment',
                'h2' => 'Your free ERP readiness check',
                'intro' => 'It takes about 3 minutes. Your result appears on screen and is sent to your email.'],
            ['type' => 'cta_band'],
        ],
    ],
    'ar' => [
        'name' => 'تقييم مجاني',
        'title' => 'تقييم مجاني لجاهزية ERP',
        'meta' => 'أجب عن 10 أسئلة واحصل على تقرير مجاني يوضح مدى ملاءمة SAP Business One لشركتك والخطوات التالية.',
        'h1' => 'هل شركتك جاهزة لـ SAP Business One؟',
        'sections' => [
            ['type' => 'hero', 'image' => ['src' => 'img/gen/free-assessment.webp', 'alt' => 'صاحب شركة يجيب عن أسئلة التقييم على جهاز لوحي مع مستشار', 'w' => 1600, 'h' => 1067],
                'sub' => 'أجب عن 10 أسئلة واحصل على تقرير مجاني يوضح مدى ملاءمة SAP Business One لشركتك والخطوات التالية.',
                'buttons' => [['ابدأ التقييم', '#assessment']]],
            ['type' => 'assessment',
                'h2' => '[AR-REVIEW]فحص جاهزية ERP المجاني[/AR-REVIEW]',
                'intro' => '[AR-REVIEW]يستغرق نحو 3 دقائق، وتظهر نتيجتك على الشاشة وتُرسل إلى بريدك الإلكتروني.[/AR-REVIEW]'],
            ['type' => 'cta_band'],
        ],
    ],
];
