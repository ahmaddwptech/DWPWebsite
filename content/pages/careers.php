<?php
// C39. Careers, /careers/ (open roles come from the `careers` table; no demo jobs)
return [
    'en' => [
        'name' => 'Careers',
        'title' => 'Careers at DWP Arabia | SAP & AI Jobs in Riyadh',
        'meta' => 'Join our SAP Business One and AI team in Riyadh. See open roles.',
        'h1' => 'Join DWP Arabia',
        'sections' => [
            ['type' => 'hero', 'bg_image' => 'img/gen/careers-team.webp', 'sub' => 'Build SAP Business One and AI solutions for growing Saudi companies, with a team based in Riyadh.'],
            ['type' => 'cards',
                'h2' => 'Why join us',
                'cols' => 3,
                'items' => [
                    ['icon' => 'shield', 'title' => 'Grow your SAP expertise', 'text' => 'Work on real SAP Business One projects and build towards SAP certification.'],
                    ['icon' => 'spark', 'title' => 'Work on practical AI', 'text' => 'Apply AI to ERP data for Saudi companies, forecasting, assistants and automation.'],
                    ['icon' => 'users', 'title' => 'Backed by a large group', 'text' => 'A Riyadh team that is part of the DWP group, founded in 1999.'],
                ]],
            ['type' => 'careers_list'],
            ['type' => 'cv_form'],
        ],
    ],
    'ar' => [
        'name' => 'الوظائف',
        'title' => 'الوظائف في DWP Arabia | وظائف SAP والذكاء الاصطناعي',
        'meta' => 'انضم إلى فريق SAP Business One والذكاء الاصطناعي في الرياض. اطلع على الوظائف المتاحة.',
        'h1' => 'انضم إلى DWP Arabia',
        'sections' => [
            ['type' => 'hero', 'bg_image' => 'img/gen/careers-team.webp', 'sub' => '[AR-REVIEW]ابنِ حلول SAP Business One والذكاء الاصطناعي للشركات السعودية النامية مع فريق في الرياض.[/AR-REVIEW]'],
            ['type' => 'cards',
                'h2' => '[AR-REVIEW]لماذا تنضم إلينا[/AR-REVIEW]',
                'cols' => 3,
                'items' => [
                    ['icon' => 'shield', 'title' => '[AR-REVIEW]طوّر خبرتك في SAP[/AR-REVIEW]', 'text' => '[AR-REVIEW]اعمل على مشاريع SAP Business One حقيقية واستعد للحصول على شهادة SAP.[/AR-REVIEW]'],
                    ['icon' => 'spark', 'title' => '[AR-REVIEW]اعمل على ذكاء اصطناعي عملي[/AR-REVIEW]', 'text' => '[AR-REVIEW]طبّق الذكاء الاصطناعي على بيانات أنظمة ERP للشركات السعودية، التنبؤ والمساعدات الذكية والأتمتة.[/AR-REVIEW]'],
                    ['icon' => 'users', 'title' => '[AR-REVIEW]مدعومون بمجموعة كبيرة[/AR-REVIEW]', 'text' => '[AR-REVIEW]فريق في الرياض ضمن مجموعة DWP التي تأسست عام 1999.[/AR-REVIEW]'],
                ]],
            ['type' => 'careers_list'],
            ['type' => 'cv_form'],
        ],
    ],
];
