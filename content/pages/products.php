<?php
// Products overview, /products/ (not in the spec: written during the build, listed in CONFIRM.md)
// High level only: one card per product; details live on each product's own page.
// Images are real product screens: SAP B1 Web Client (sap.com), Odoo Accounting (odoo.com), CRAS/PMS/IoT (DWP's own site).
return [
    'en' => [
        'name' => 'Products',
        'title' => 'ERP Systems and DWP Platforms | DWP Arabia',
        'meta' => 'SAP Business One, Odoo and DWP platforms for consolidation, performance management and industrial IoT, delivered by our team in Riyadh.',
        'h1' => 'Our Products',
        'sections' => [
            ['type' => 'hero', 'sub' => 'ERP systems we implement and platforms built by DWP. Choose a product to learn more.'],
            ['type' => 'product_grid', 'groups' => [
                ['title' => 'ERP systems', 'items' => [
                    ['featured' => true, 'logo' => 'sap', 'title' => 'SAP Business One', 'href' => '/sap-business-one/', 'cta' => 'Explore SAP Business One',
                        'text' => 'One system for finance, sales, inventory and production, localised for Saudi Arabia.',
                        'image' => ['src' => 'img/products/sap-b1-webclient-home.webp', 'alt' => 'SAP Business One Web Client home page with tiles for sales quotations, sales orders, purchase orders, deliveries and A/R invoices', 'w' => 1600, 'h' => 796]],
                    ['title' => 'Odoo', 'href' => '/erp/odoo/', 'cta' => 'Explore Odoo',
                        'text' => 'A flexible, modular ERP for smaller teams.',
                        'image' => ['src' => 'img/products/odoo-accounting.webp', 'alt' => 'Odoo Accounting dashboard with customer invoices, vendor bills, bank and cash journals', 'w' => 1440, 'h' => 900]],
                ]],
                ['title' => 'DWP platforms', 'items' => [
                    ['title' => 'CRAS', 'href' => '/cras/', 'cta' => 'Explore CRAS',
                        'text' => 'Consolidation and reconciliation for groups of companies.',
                        'fit' => 'contain', 'bg' => '#102d4c', 'image' => ['src' => 'img/pages/real-time-dashboards.webp', 'alt' => 'CRAS financial consolidation dashboard showing entities, balances and reconciliation status', 'w' => 824, 'h' => 897]],
                    ['title' => 'PMS', 'href' => '/pms/', 'cta' => 'Explore PMS',
                        'text' => 'Projects and KPIs across every department.',
                        'fit' => 'contain', 'bg' => '#000', 'image' => ['src' => 'img/products/pms-executive-reporting.webp', 'alt' => 'PMS analytics dashboard with performance charts and executive reporting widgets', 'w' => 824, 'h' => 897]],
                    ['title' => 'Industrial IoT Platform', 'href' => '/industrial-iot-platform/', 'cta' => 'Explore the platform',
                        'text' => 'Live machine data and OEE from the production floor.',
                        'fit' => 'contain', 'bg' => '#131a2d', 'image' => ['src' => 'img/products/iot-machine-monitoring.webp', 'alt' => 'Industrial IoT machine monitoring screen with machine status, gauges, trend charts and alarms', 'w' => 824, 'h' => 897]],
                ]],
            ]],
            ['type' => 'cta_band'],
        ],
    ],
    'ar' => [
        'name' => 'منتجاتنا',
        'title' => 'أنظمة ERP ومنصات DWP | DWP Arabia',
        'meta' => '[AR-REVIEW]SAP Business One وOdoo ومنصات DWP لتوحيد البيانات المالية وإدارة الأداء وإنترنت الأشياء الصناعي، يقدمها فريقنا في الرياض.[/AR-REVIEW]',
        'h1' => 'منتجاتنا',
        'sections' => [
            ['type' => 'hero', 'sub' => '[AR-REVIEW]أنظمة ERP نطبقها ومنصات من تطوير DWP. اختر منتجاً لمعرفة المزيد.[/AR-REVIEW]'],
            ['type' => 'product_grid', 'groups' => [
                ['title' => 'أنظمة ERP', 'items' => [
                    ['featured' => true, 'logo' => 'sap', 'title' => 'SAP Business One', 'href' => '/sap-business-one/', 'cta' => 'اكتشف SAP Business One',
                        'text' => '[AR-REVIEW]نظام واحد للمالية والمبيعات والمخزون والإنتاج، مهيأ للسوق السعودي.[/AR-REVIEW]',
                        'image' => ['src' => 'img/products/sap-b1-webclient-home.webp', 'alt' => 'الصفحة الرئيسية لواجهة SAP Business One على الويب مع مربعات عروض الأسعار وأوامر البيع وأوامر الشراء والتسليمات وفواتير العملاء', 'w' => 1600, 'h' => 796]],
                    ['title' => 'Odoo', 'href' => '/erp/odoo/', 'cta' => '[AR-REVIEW]اكتشف Odoo[/AR-REVIEW]',
                        'text' => '[AR-REVIEW]نظام ERP مرن ومعياري للفرق الأصغر.[/AR-REVIEW]',
                        'image' => ['src' => 'img/products/odoo-accounting.webp', 'alt' => 'لوحة المحاسبة في Odoo مع فواتير العملاء وفواتير الموردين ودفاتر البنك والنقد', 'w' => 1440, 'h' => 900]],
                ]],
                ['title' => 'منصات DWP', 'items' => [
                    ['title' => 'CRAS', 'href' => '/cras/', 'cta' => '[AR-REVIEW]اكتشف CRAS[/AR-REVIEW]',
                        'text' => '[AR-REVIEW]توحيد ومطابقة حسابات مجموعات الشركات.[/AR-REVIEW]',
                        'fit' => 'contain', 'bg' => '#102d4c', 'image' => ['src' => 'img/pages/real-time-dashboards.webp', 'alt' => 'لوحة منصة CRAS لتوحيد البيانات المالية تعرض الكيانات والأرصدة وحالة التسويات', 'w' => 824, 'h' => 897]],
                    ['title' => 'PMS', 'href' => '/pms/', 'cta' => '[AR-REVIEW]اكتشف PMS[/AR-REVIEW]',
                        'text' => '[AR-REVIEW]المشاريع ومؤشرات الأداء في جميع الإدارات.[/AR-REVIEW]',
                        'fit' => 'contain', 'bg' => '#000', 'image' => ['src' => 'img/products/pms-executive-reporting.webp', 'alt' => 'لوحة تحليلات منصة PMS مع مخططات الأداء وتقارير الإدارة العليا', 'w' => 824, 'h' => 897]],
                    ['title' => 'منصة إنترنت الأشياء الصناعية', 'href' => '/industrial-iot-platform/', 'cta' => '[AR-REVIEW]اكتشف المنصة[/AR-REVIEW]',
                        'text' => '[AR-REVIEW]بيانات الآلات وكفاءة المعدات لحظياً من خطوط الإنتاج.[/AR-REVIEW]',
                        'fit' => 'contain', 'bg' => '#131a2d', 'image' => ['src' => 'img/products/iot-machine-monitoring.webp', 'alt' => 'شاشة مراقبة الآلات في منصة إنترنت الأشياء الصناعية مع حالة الآلات والمؤشرات والمخططات والتنبيهات', 'w' => 824, 'h' => 897]],
                ]],
            ]],
            ['type' => 'cta_band'],
        ],
    ],
];
