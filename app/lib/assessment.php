<?php
/** Free Assessment (spec C16): questions, scoring, submission. */

function assessment_questions(string $lang): array
{
    $ar = $lang === 'ar';
    // [id, label EN, label AR, multi?, [value => [EN, AR, points]]]
    $q = [
        ['industry', 'Industry', 'القطاع', false, [
            'distribution' => ['Distribution & trading', 'التوزيع والتجارة', 10],
            'manufacturing' => ['Manufacturing', 'التصنيع', 12],
            'retail' => ['Retail', 'التجزئة', 8],
            'contracting' => ['Contracting & services', 'المقاولات والخدمات', 8],
            'other' => ['Other', 'أخرى', 4],
        ]],
        ['employees', 'Employees', 'عدد الموظفين', false, [
            '1-20' => ['1-20', '1-20', 2], '21-50' => ['21-50', '21-50', 8], '51-200' => ['51-200', '51-200', 12],
            '201-500' => ['201-500', '201-500', 12], '500+' => ['500+', '500+', 8],
        ]],
        ['current_system', 'Current system', 'النظام الحالي', false, [
            'excel' => ['Excel', 'Excel', 6], 'accounting' => ['Accounting software', 'برنامج محاسبي', 10],
            'odoo' => ['Odoo', 'Odoo', 6], 'other_erp' => ['Other ERP', 'نظام ERP آخر', 8],
            'sap' => ['SAP Business One', 'SAP Business One', 10],
        ]],
        ['branches', 'Branches', 'عدد الفروع', false, [
            '1' => ['1', '1', 2], '2-5' => ['2-5', '2-5', 10], '6+' => ['6+', '6+', 14],
        ]],
        ['stock', 'Do you hold stock?', 'هل لديكم مخزون؟', false, [
            'one' => ['Yes, one warehouse', 'نعم، مستودع واحد', 8], 'several' => ['Yes, several', 'نعم، عدة مستودعات', 14], 'no' => ['No', 'لا', 0],
        ]],
        ['manufacture', 'Do you manufacture?', 'هل لديكم تصنيع؟', false, [
            'yes' => ['Yes', 'نعم', 10], 'no' => ['No', 'لا', 0],
        ]],
        ['zatca', 'ZATCA Phase 2 status', 'وضع المرحلة الثانية', false, [
            'integrated' => ['Already integrated', 'تم الربط بالفعل', 2], 'notified' => ['Wave notified', 'تم إبلاغنا بموجتنا', 8],
            'not_yet' => ['Not yet', 'ليس بعد', 6], 'unknown' => ["Don't know", 'لا أعرف', 5],
        ]],
        ['pains', 'Biggest pain', 'أكبر التحديات', true, [
            'month_end' => ['Slow month-end', 'بطء الإغلاق الشهري', 3], 'stock' => ['Stock accuracy', 'دقة المخزون', 3],
            'reporting' => ['Reporting', 'التقارير', 3], 'compliance' => ['Compliance', 'الامتثال', 3],
            'growth' => ['Growth/branches', 'النمو والفروع', 4],
        ]],
        ['timeline', 'Timeline', 'الإطار الزمني', false, [
            '3m' => ['Within 3 months', 'خلال 3 أشهر', 6], '6m' => ['3-6 months', '3-6 أشهر', 5],
            '12m' => ['6-12 months', '6-12 شهراً', 3], 'research' => ['Just researching', 'مجرد استكشاف', 1],
        ]],
        ['decision', 'Decision maker', 'صاحب القرار', false, [
            'me' => ['Me', 'أنا', 4], 'finance' => ['Finance manager', 'المدير المالي', 3], 'ceo' => ['CEO/owner', 'الرئيس التنفيذي/المالك', 4],
            'it' => ['IT', 'تقنية المعلومات', 2], 'committee' => ['Committee', 'لجنة', 3],
        ]],
    ];
    $out = [];
    foreach ($q as [$id, $en, $arLabel, $multi, $opts]) {
        $o = [];
        foreach ($opts as $v => [$oen, $oar, $pts]) $o[$v] = ['label' => $ar ? $oar : $oen, 'points' => $pts];
        $out[] = ['id' => $id, 'label' => $ar ? $arLabel : $en, 'multi' => $multi, 'options' => $o];
    }
    return $out;
}

/** Score 0–100. Higher = more stock, branches, manufacturing and pain = stronger SAP Business One fit. */
function assessment_score(array $answers): int
{
    $total = 0;
    $max = 0;
    foreach (assessment_questions('en') as $q) {
        $pts = array_column($q['options'], 'points');
        $max += $q['multi'] ? array_sum($pts) : max($pts);
        $a = $answers[$q['id']] ?? ($q['multi'] ? [] : '');
        foreach ((array) $a as $v) $total += $q['options'][$v]['points'] ?? 0;
    }
    return (int) round(min(100, $total / $max * 100));
}

function assessment_band(int $score): string
{
    return $score >= 60 ? 'strong' : ($score >= 40 ? 'good' : 'odoo');
}

function assessment_band_content(string $band, string $lang): array
{
    $c = [
        'strong' => [
            'en' => ['Strong fit for SAP Business One', 'Your size, stock and growth plans match what SAP Business One is built for.', [
                'Book a demo on a Saudi company scenario like yours.',
                'Get a scoped implementation plan and timeline.',
                'Check your ZATCA Phase 2 readiness with our team.',
            ]],
            'ar' => ['ملاءمة قوية لـ SAP Business One', '[AR-REVIEW]حجم شركتك ومخزونها وخطط نموها تتوافق مع ما صُمم SAP Business One من أجله.[/AR-REVIEW]', [
                '[AR-REVIEW]احجز عرضاً توضيحياً على سيناريو شركة سعودية مشابهة لشركتك.[/AR-REVIEW]',
                '[AR-REVIEW]احصل على خطة تطبيق محددة النطاق وجدول زمني.[/AR-REVIEW]',
                '[AR-REVIEW]تحقق من جاهزيتك للمرحلة الثانية من الفوترة الإلكترونية مع فريقنا.[/AR-REVIEW]',
            ]],
        ],
        'good' => [
            'en' => ["Good fit, let's check the details", 'SAP Business One can fit your company well. A short call will confirm scope and priorities.', [
                'Talk to a consultant about your main processes.',
                'Book a demo focused on your biggest pain points.',
                'Compare SAP Business One and Odoo for your case.',
            ]],
            'ar' => ['ملاءمة جيدة، لنراجع التفاصيل', '[AR-REVIEW]يمكن أن يناسب SAP Business One شركتك بشكل جيد، ومكالمة قصيرة تؤكد النطاق والأولويات.[/AR-REVIEW]', [
                '[AR-REVIEW]تحدث مع مستشار عن عملياتك الرئيسية.[/AR-REVIEW]',
                '[AR-REVIEW]احجز عرضاً توضيحياً يركز على أكبر تحدياتك.[/AR-REVIEW]',
                '[AR-REVIEW]قارن بين SAP Business One وOdoo لحالتك.[/AR-REVIEW]',
            ]],
        ],
        'odoo' => [
            'en' => ['Odoo may suit you better right now', 'For your current size and processes, a lighter ERP may give faster value. We implement both and will advise honestly.', [
                'Read our SAP Business One vs Odoo comparison.',
                'Talk to us about an Odoo setup for your team.',
                'Revisit SAP Business One when you add branches or stock.',
            ]],
            'ar' => ['قد يناسبك Odoo أكثر حالياً', '[AR-REVIEW]بالنظر إلى حجم شركتك وعملياتها الحالية، قد يحقق نظام أخف قيمة أسرع. نطبق النظامين وننصحك بصدق.[/AR-REVIEW]', [
                '[AR-REVIEW]اقرأ مقارنتنا بين SAP Business One وOdoo.[/AR-REVIEW]',
                '[AR-REVIEW]تحدث معنا عن تطبيق Odoo لفريقك.[/AR-REVIEW]',
                '[AR-REVIEW]أعد النظر في SAP Business One عند إضافة فروع أو مخزون.[/AR-REVIEW]',
            ]],
        ],
    ];
    [$title, $text, $steps] = $c[$band][$lang];
    return ['title' => $title, 'text' => $text, 'steps' => $steps];
}

/** Handle POST /api/assessment/ */
function handle_assessment_post(): void
{
    $in = $_POST;
    $GLOBALS['lang'] = ($in['page_language'] ?? 'en') === 'ar' ? 'ar' : 'en';
    if (!passes_spam_checks($in)) {
        form_failed(['_form' => t('form.errors.spam')], $in);
        return;
    }
    [$d, $err] = validate_contact($in, ['full_name', 'company', 'email', 'mobile']);

    $answers = [];
    foreach (assessment_questions('en') as $q) {
        $raw = $in['q_' . $q['id']] ?? ($q['multi'] ? [] : '');
        if ($q['multi']) {
            $answers[$q['id']] = array_values(array_intersect((array) $raw, array_keys($q['options'])));
        } else {
            $answers[$q['id']] = array_key_exists((string) $raw, $q['options']) ? (string) $raw : '';
            if ($answers[$q['id']] === '') $err['q_' . $q['id']] = t('form.errors.required');
        }
    }
    if ($err) {
        form_failed($err, $in);
        return;
    }
    $score = assessment_score($answers);
    $band = assessment_band($score);
    $token = bin2hex(random_bytes(16));
    $row = $d + tracking_data($in) + [
        'token' => $token, 'lang' => lang(), 'answers_json' => json_encode($answers, JSON_UNESCAPED_UNICODE),
        'score' => $score, 'band' => $band, 'consent' => 1, 'ip' => client_ip(),
    ];
    $id = 0;
    try {
        $id = db_insert('assessments', $row);
    } catch (Throwable $e) {
        log_line('assessments.log', $e->getMessage() . ' ' . json_encode($row, JSON_UNESCAPED_UNICODE));
    }

    // Show the result now; emails and CRM delivery continue in the background.
    redirect_and_continue(url('/sap-business-one/free-assessment/result/') . '?t=' . $token . '&new=1');

    $full = $row;
    $bc = assessment_band_content($band, lang());
    if (cfg('sales_inbox')) {
        send_mail(cfg('sales_inbox'), "[DWP Arabia] Free Assessment: {$d['company']}, score $score ($band)",
            "Assessment #$id\nScore: $score / 100 ($band)\n\n" . implode("\n", lead_summary_lines($full + ['form_type' => 'assessment'])) . "\n\nAnswers:\n" . json_encode($answers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $d['email']);
    }
    // Result email to the visitor.
    $resultUrl = url('/sap-business-one/free-assessment/result/', null, true) . '?t=' . $token;
    send_mail($d['email'], plain($bc['title']) . ' | DWP Arabia',
        plain($bc['title']) . "\n\n" . plain($bc['text']) . "\n\n" . t('assessment.score') . ": $score / 100\n\n" . t('assessment.next_steps') . ":\n- " . implode("\n- ", array_map('plain', $bc['steps'])) . "\n\n" . $resultUrl . "\n\n" . t('book_free_demo') . ': ' . url('/book-a-demo/', null, true) . "\n");

    $crm = push_to_crm(['type' => 'assessment', 'id' => $id, 'score' => $score, 'band' => $band, 'answers' => $answers] + $full);
    if ($id) db_update('assessments', $id, ['crm_status' => $crm === null ? 'skipped' : ($crm ? 'sent' : 'failed')]);
    exit;
}
