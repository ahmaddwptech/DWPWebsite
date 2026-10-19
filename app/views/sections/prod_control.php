<?php
/**
 * prod_control: eyebrow, h2, intro, bg, hint (short line telling visitors they can click)
 * Dark production control room that runs like a live system (site.js): output counters rise, items move along the
 * lines, machine statuses change and events appear in the log. Visitors can open a machine (pause, resume, report
 * output, call maintenance), start or complete work orders and approve or reject a quality hold.
 * On Arabic pages the whole room is in Arabic and right to left; script messages come from the word list below. Sample data only.
 */
$ar = is_ar();
$T = fn(string $en, string $a) => $ar ? $a : $en;
// Arabic names for the sample machines, products, operators and lines
$W = $ar ? ['Press P 01' => 'مكبس P 01', 'Press P 02' => 'مكبس P 02', 'Laser L 01' => 'ليزر L 01', 'Welder W 03' => 'لحام W 03', 'Paint line PL 1' => 'خط الدهان PL 1', 'Bender B 02' => 'ثني B 02', 'Robot R 01' => 'روبوت R 01', 'Packing PK 1' => 'التغليف PK 1',
    'Steel shelving 2 m' => 'أرفف معدنية 2 م', 'Wire mesh deck' => 'أرضيات شبكية', 'Workbench' => 'منضدة عمل', 'Storage cabinet' => 'خزانة تخزين', 'Pallet racking' => 'رفوف تخزين', 'Tool cabinet' => 'خزانة أدوات',
    'Faisal A.' => 'فيصل أ.', 'Majed S.' => 'ماجد س.', 'Ravi K.' => 'رافي ك.', 'Omar F.' => 'عمر ف.', 'Saleh H.' => 'صالح ح.', 'Ali M.' => 'علي م.', 'Auto' => 'آلي', 'Hassan N.' => 'حسن ن.',
    'A' => 'أ', 'B' => 'ب', 'C' => 'ج', 'D' => 'د', 'In progress' => 'قيد التنفيذ', 'Scheduled' => 'مجدول'] : [];
$w = fn(string $x) => $W[$x] ?? $x;
$machines = [
    // id, name, line, status, rate (units per second while running), count, order, product, operator, progress
    ['p01', 'Press P 01', 'A', 'run', 0.7, 2140, 'WO 2291', 'Steel shelving 2 m', 'Faisal A.', 72],
    ['p02', 'Press P 02', 'A', 'run', 0.65, 1985, 'WO 2291', 'Steel shelving 2 m', 'Majed S.', 72],
    ['l01', 'Laser L 01', 'B', 'run', 0.3, 640, 'WO 2297', 'Wire mesh deck', 'Ravi K.', 48],
    ['w03', 'Welder W 03', 'B', 'chg', 0.4, 410, 'WO 2298', 'Workbench', 'Omar F.', 0],
    ['pl1', 'Paint line PL 1', 'C', 'run', 0.5, 880, 'WO 2295', 'Storage cabinet', 'Saleh H.', 35],
    ['b02', 'Bender B 02', 'C', 'stop', 0.45, 520, 'WO 2294', 'Pallet racking', 'Ali M.', 20],
    ['r01', 'Robot R 01', 'D', 'run', 0.55, 1210, 'WO 2297', 'Wire mesh deck', 'Auto', 48],
    ['pk1', 'Packing PK 1', 'D', 'idle', 0.35, 760, 'WO 2291', 'Steel shelving 2 m', 'Hassan N.', 60],
];
$label = ['run' => $T('Running', 'تعمل'), 'idle' => $T('Idle', 'خاملة'), 'stop' => $T('Stopped', 'متوقفة'), 'chg' => $T('Changeover', 'تبديل')];
$i18n = $ar ? [
    'Running' => 'تعمل', 'Idle' => 'خاملة', 'Stopped' => 'متوقفة', 'Changeover' => 'تبديل', '{0} stopped' => '{0} متوقفة', 'None stopped' => 'لا توجد آلة متوقفة',
    '{0}, line {1}' => '{0}، الخط {1}', '{0} units/min' => '{0} وحدة/دقيقة', 'Pause' => 'إيقاف مؤقت', 'Maintenance in progress' => 'الصيانة جارية', 'Resume' => 'استئناف',
    '{0} paused by supervisor' => 'أوقف المشرف {0} مؤقتاً', '{0} resumed on {1}' => 'استُؤنف {0} على {1}', '25 units of {0} reported on {1}' => 'سُجلت 25 وحدة من {0} على {1}',
    'Maintenance called for {0}' => 'طُلبت الصيانة لـ {0}', '{0} back running after maintenance' => 'عاد {0} للعمل بعد الصيانة',
    'In progress' => 'قيد التنفيذ', 'Complete' => 'إكمال', 'Done' => 'مكتمل', 'Received' => 'تم الاستلام', '{0} released and started' => 'أُصدر {0} وبدأ التنفيذ',
    '{0} completed, 240 units received to finished goods' => 'اكتمل {0} واستُلمت 240 وحدة في المنتج النهائي',
    'Lot L 8840 released to finished goods by the quality supervisor.' => 'أفرج مشرف الجودة عن الدفعة L 8840 إلى المنتج النهائي.',
    'Lot L 8840 sent to rework on the paint line. A new inspection is planned.' => 'أُعيدت الدفعة L 8840 للتصنيع على خط الدهان وخُطط لفحص جديد.',
    'Lot L 8840 approved and released' => 'اعتُمدت الدفعة L 8840 وأُفرج عنها', 'Lot L 8840 sent to rework' => 'أُعيدت الدفعة L 8840 للتصنيع',
    'Welder W 03 changeover finished, WO 2298 running' => 'انتهى تبديل لحام W 03 وبدأ أمر 2298', 'Steel coil 2 mm issued to line A, 3.2 t' => 'صُرف لفيف حديد 2 مم للخط أ، 3.2 طن',
    'Cartons arrived, Packing PK 1 running' => 'وصلت الكراتين وعاد التغليف PK 1 للعمل', 'Lot L 8842 passed in process inspection' => 'اجتازت الدفعة L 8842 فحص أثناء الإنتاج',
    'Laser L 01 waiting for material: sheet 1.5 mm' => 'ليزر L 01 ينتظر المواد: صاج 1.5 مم', 'Sheets delivered, Laser L 01 running' => 'وصل الصاج وعاد ليزر L 01 للعمل',
    'Planned maintenance for Press P 02 at 13:30' => 'صيانة مخططة للمكبس P 02 الساعة 13:30',
] : [];
$orders = [['WO 2291', 'Steel shelving 2 m', 'A', 'In progress', 72], ['WO 2297', 'Wire mesh deck', 'B', 'In progress', 48], ['WO 2295', 'Storage cabinet', 'C', 'In progress', 35], ['WO 2298', 'Workbench', 'B', 'Scheduled', 0], ['WO 2299', 'Tool cabinet', 'D', 'Scheduled', 0]];
$total = array_sum(array_column($machines, 5));
?>
<section class="<?= section_class($s, 'pcr-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <?php if (!empty($s['hint'])): ?><p class="pcr-hint"><?= icon('check') ?><?= rich($s['hint']) ?></p><?php endif; ?>
    <div class="pcr-wrap"><div class="pcr-frame">
      <div class="pcr" data-pcr dir="<?= $ar ? 'rtl' : 'ltr' ?>" lang="<?= $ar ? 'ar' : 'en' ?>">
        <script type="application/json" data-pcr-i18n><?= json_encode($i18n, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?></script>
        <div class="pcr-top">
          <div class="pcr-title"><strong><?= $T('Plant 1 Control Room', 'غرفة التحكم، المصنع 1') ?></strong><span class="pcr-live"><i></i><?= $T('Live', 'مباشر') ?></span></div>
          <span class="pcr-clock" data-pcr-clock>10:42:18</span>
          <span class="pcr-date"><?= e($T('Mon 12 Oct 2026', 'الإثنين 12 أكتوبر 2026')) ?><?php if ($hj = hijri('2026-10-12', $ar ? 'ar' : 'en')): ?><em><?= e($hj) ?></em><?php endif; ?></span>
          <span class="pcr-shift"><?= $T('Shift A, 06:00 to 14:00', 'الوردية أ، 06:00 إلى 14:00') ?></span>
        </div>
        <div class="pcr-kpis">
          <div><span><?= $T('Output this shift', 'إنتاج الوردية') ?></span><strong data-pcr-total><?= number_format($total) ?></strong><em><?= $T('Plan 9,400 units', 'المخطط 9,400 وحدة') ?></em></div>
          <div><span><?= $T('OEE', 'الكفاءة الشاملة') ?></span><strong data-pcr-oee>78.4%</strong><em><?= $T('Target 85%', 'المستهدف 85%') ?></em></div>
          <div><span><?= $T('Machines running', 'آلات تعمل') ?></span><strong data-pcr-running>5 / 8</strong><em data-pcr-down><?= $T('1 stopped', '1 متوقفة') ?></em></div>
          <div><span><?= $T('Finished goods', 'المنتج النهائي') ?></span><strong data-pcr-fg>1,120</strong><em><?= $T('Received to stock', 'مستلم في المخزون') ?></em></div>
          <div><span><?= $T('Open alerts', 'تنبيهات مفتوحة') ?></span><strong class="is-warn" data-pcr-alerts>3</strong><em><?= $T('Needs attention', 'تحتاج متابعة') ?></em></div>
        </div>
        <div class="pcr-main">
          <div class="pcr-floor">
            <p class="pcr-h"><?= $T('Shop floor', 'أرضية المصنع') ?> <small><?= $T('Click a machine', 'اضغط على آلة') ?></small></p>
            <?php foreach (['A', 'B', 'C', 'D'] as $line): ?>
            <div class="pcr-line">
              <span class="pcr-line-name"><?= $T('Line', 'الخط') ?> <?= $w($line) ?></span>
              <span class="pcr-belt<?= in_array('run', array_column(array_filter($machines, fn($m) => $m[2] === $line), 3), true) ? ' is-moving' : '' ?>" data-pcr-belt="<?= $line ?>"><i></i><i></i><i></i><i></i></span>
              <?php foreach ($machines as [$id, $name, $l, $status, $rate, $count, $wo, $prod, $op, $pr]): if ($l !== $line) continue; ?>
              <button type="button" class="pcr-m is-<?= $status ?>" data-pcr-m="<?= $id ?>" data-name="<?= e($w($name)) ?>" data-line="<?= $l ?>" data-line-name="<?= e($w($l)) ?>" data-status="<?= $status ?>" data-rate="<?= $rate ?>" data-count="<?= $count ?>" data-wo="<?= e($wo) ?>" data-product="<?= e($w($prod)) ?>" data-op="<?= e($w($op)) ?>" data-progress="<?= $pr ?>">
                <span class="pcr-m-top"><b><?= e($w($name)) ?></b><span class="pcr-st"><i></i><span data-st><?= $label[$status] ?></span></span></span>
                <span class="pcr-m-wo"><?= e($wo) ?> · <?= e($w($prod)) ?></span>
                <span class="pcr-m-count"><strong data-count><?= number_format($count) ?></strong> <?= $T('units', 'وحدة') ?></span>
                <span class="pcr-bar"><i data-bar style="--p:<?= $pr ?>%"></i></span>
              </button>
              <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="pcr-side">
            <div class="pcr-card">
              <p class="pcr-h"><?= $T('Work orders', 'أوامر العمل') ?></p>
              <ul class="pcr-wo">
                <?php foreach ($orders as [$o, $p, $l, $st, $pr]): ?>
                <li data-pcr-wo="<?= e($o) ?>" data-status="<?= e($st) ?>">
                  <div><b><?= e($o) ?></b><span><?= e($w($p)) ?><?= $T(', line', '، الخط') ?> <?= $w($l) ?></span></div>
                  <span class="pcr-wo-st" data-wo-st><?= e($w($st)) ?></span>
                  <button type="button" class="pcr-btn" data-wo-action><?= $st === 'Scheduled' ? $T('Start', 'بدء') : $T('Complete', 'إكمال') ?></button>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
            <div class="pcr-card pcr-hold" data-pcr-hold>
              <p class="pcr-h"><?= $T('Quality hold', 'حجز الجودة') ?> <span class="pcr-tag"><?= $T('Lot L 8840', 'الدفعة L 8840') ?></span></p>
              <p class="pcr-hold-text" data-hold-text><?= $T('Storage cabinet, paint finish below standard on 6 of 40 units checked.', 'خزانة تخزين، الدهان دون المستوى في 6 من 40 وحدة مفحوصة.') ?></p>
              <div class="pcr-hold-actions"><button type="button" class="pcr-btn is-ok" data-hold="approve"><?= $T('Approve and release', 'اعتماد وإفراج') ?></button><button type="button" class="pcr-btn is-bad" data-hold="reject"><?= $T('Send to rework', 'إعادة تصنيع') ?></button></div>
            </div>
          </div>
        </div>
        <div class="pcr-bottom">
          <div class="pcr-card pcr-chart">
            <p class="pcr-h"><?= $T('Output per minute', 'الإنتاج في الدقيقة') ?> <small><?= $T('last 30 minutes', 'آخر 30 دقيقة') ?></small></p>
            <div class="pcr-bars" data-pcr-bars><?php foreach ([32, 35, 31, 36, 38, 34, 29, 33, 37, 39, 36, 34, 30, 35, 38, 40, 37, 35, 33, 36, 38, 41, 39, 36, 34, 37, 39, 42, 40, 38] as $v): ?><i style="--h:<?= round($v / 50 * 100) ?>%"></i><?php endforeach; ?></div>
          </div>
          <div class="pcr-card pcr-log">
            <p class="pcr-h"><?= $T('Events', 'الأحداث') ?></p>
            <ul data-pcr-log>
              <li class="is-warn"><time>10:41</time><?= $T('Bender B 02 stopped: maintenance, 18 min', 'توقف ثني B 02: صيانة، 18 دقيقة') ?></li>
              <li><time>10:38</time><?= $T('WO 2297 started on Laser L 01', 'بدأ أمر WO 2297 على ليزر L 01') ?></li>
              <li class="is-ok"><time>10:35</time><?= $T('Lot L 8839 passed final inspection', 'اجتازت الدفعة L 8839 الفحص النهائي') ?></li>
              <li class="is-warn"><time>10:31</time><?= $T('Low material at Packing PK 1: carton 120 x 60', 'نقص مواد في التغليف PK 1: كرتون 120 × 60') ?></li>
            </ul>
          </div>
        </div>
        <aside class="pcr-drawer" data-pcr-drawer aria-hidden="true">
          <button type="button" class="pcr-close" data-pcr-close aria-label="<?= $T('Close', 'إغلاق') ?>">×</button>
          <p class="pcr-d-name" data-d-name></p>
          <p class="pcr-st pcr-d-st" data-d-st><i></i><span></span></p>
          <dl class="pcr-d-facts">
            <div><dt><?= $T('Work order', 'أمر العمل') ?></dt><dd data-d-wo></dd></div>
            <div><dt><?= $T('Product', 'المنتج') ?></dt><dd data-d-product></dd></div>
            <div><dt><?= $T('Operator', 'المشغل') ?></dt><dd data-d-op></dd></div>
            <div><dt><?= $T('Units this shift', 'وحدات الوردية') ?></dt><dd data-d-count></dd></div>
            <div><dt><?= $T('Speed', 'السرعة') ?></dt><dd data-d-speed></dd></div>
          </dl>
          <div class="pcr-d-actions">
            <button type="button" class="pcr-btn" data-d-action="toggle"><?= $T('Pause', 'إيقاف مؤقت') ?></button>
            <button type="button" class="pcr-btn" data-d-action="report"><?= $T('Report 25 units', 'تسجيل 25 وحدة') ?></button>
            <button type="button" class="pcr-btn is-bad" data-d-action="maint"><?= $T('Call maintenance', 'طلب الصيانة') ?></button>
          </div>
        </aside>
      </div>
    </div></div>
  </div>
</section>
