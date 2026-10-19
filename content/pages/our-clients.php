<?php
// C4. Clients & Success Stories, /our-clients/
return [
    'en' => [
        'name' => 'Clients & Success Stories',
        'title' => 'Clients & Success Stories in Saudi Arabia | DWP Arabia',
        'meta' => 'How Saudi companies run finance, inventory and production on ERP with DWP Arabia. Read the case studies.',
        'h1' => 'Trusted by Companies Across the Kingdom',
        'sections' => [
            ['type' => 'hero', 'image' => ['src' => 'img/gen/clients-golive.webp', 'alt' => 'Client team celebrating a successful system go-live', 'w' => 1600, 'h' => 1067],
                'sub' => 'How Saudi companies run finance, inventory and production on ERP with DWP Arabia.',
                'buttons' => [['Book a Free Demo', '/book-a-demo/'], ['Free Assessment', '/sap-business-one/free-assessment/']]],
            ['type' => 'text',
                'h2' => 'Our clients',
                'body' => '[CONFIRM: approved Saudi client list with written permission to show logos]'],
            ['type' => 'case_studies', 'h2' => 'Success stories', 'placeholder' => true],
            ['type' => 'testimonials'],
            ['type' => 'cta_band'],
        ],
    ],
    'ar' => [
        'name' => 'عملاؤنا وقصص النجاح',
        'title' => 'عملاؤنا وقصص النجاح | DWP Arabia',
        'meta' => 'كيف تدير الشركات السعودية المالية والمخزون والإنتاج مع DWP Arabia. اقرأ قصص النجاح.',
        'h1' => 'موثوقون لدى الشركات في جميع أنحاء المملكة',
        'sections' => [
            ['type' => 'hero', 'image' => ['src' => 'img/gen/clients-golive.webp', 'alt' => 'فريق العميل يحتفل بنجاح إطلاق النظام', 'w' => 1600, 'h' => 1067],
                'sub' => '[AR-REVIEW]كيف تدير الشركات السعودية المالية والمخزون والإنتاج على نظام ERP مع DWP Arabia.[/AR-REVIEW]',
                'buttons' => [['احجز عرضاً مجانياً', '/book-a-demo/'], ['تقييم مجاني', '/sap-business-one/free-assessment/']]],
            ['type' => 'text',
                'h2' => 'عملاؤنا',
                'body' => '[CONFIRM: approved Saudi client list with written permission to show logos]'],
            ['type' => 'case_studies', 'h2' => 'قصص النجاح', 'placeholder' => true],
            ['type' => 'testimonials'],
            ['type' => 'cta_band'],
        ],
    ],
];
