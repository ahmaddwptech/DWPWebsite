<?php
/**
 * Cortex Project Management portfolio screen drawn in HTML with sample data, in the dark control room look
 * (reuses the .ca / .pdx dashboard styles). Shown inside the product_hero laptop. In Arabic and right to left on
 * Arabic pages, with the Hijri date next to the Gregorian one.
 */
$ar = is_ar();
$T = fn(string $en, string $a) => $ar ? $a : $en;
$logo = 'img/cortex/cortex-project-management.webp';
$projects = [[$T('Tower B', 'البرج ب'), 68, 64, 'On track'], [$T('Logistics warehouse', 'المستودع اللوجستي'), 55, 52, 'On track'], [$T('Clinic fit out', 'تجهيز العيادة'), 82, 91, 'Over budget'], [$T('School extension', 'توسعة المدرسة'), 40, 46, 'At risk'], [$T('Mall fit out', 'تجهيز المول'), 61, 58, 'On track']];
$cls = ['On track' => 'run', 'At risk' => 'idle', 'Over budget' => 'stop'];
$stl = ['On track' => $T('On track', 'ضمن الخطة'), 'At risk' => $T('At risk', 'معرض للخطر'), 'Over budget' => $T('Over budget', 'تجاوز الميزانية')];
$hijri = hijri('2026-10-12', $ar ? 'ar' : 'en');
?>
<div class="ca pdx" dir="<?= $ar ? 'rtl' : 'ltr' ?>" lang="<?= $ar ? 'ar' : 'en' ?>" role="img" aria-label="<?= e($T('Cortex Project Management portfolio: budget against actual, project health, milestones and cost by type', 'محفظة Cortex Project Management: الميزانية مقابل الفعلي وحالة المشاريع والمعالم والتكلفة حسب النوع')) ?>">
  <aside class="ca-side">
    <?php if (asset_exists($logo)): ?><img src="<?= e(asset($logo)) ?>" alt="Cortex Project Management" width="1228" height="270" loading="lazy"><?php endif; ?>
    <ul><?php foreach ([['chart', $T('Portfolio', 'المحفظة')], ['doc', $T('Projects', 'المشاريع')], ['calendar', $T('Schedule', 'الجدول الزمني')], ['coins', $T('Budgets', 'الميزانيات')], ['cart', $T('Procurement', 'المشتريات')], ['clock', $T('Timesheets', 'سجلات الوقت')], ['receipt', $T('Billing', 'الفوترة')]] as $i => [$ic, $l]): ?><li<?= $i ? '' : ' class="is-on"' ?>><?= icon($ic) ?><?= e($l) ?></li><?php endforeach; ?></ul>
    <p class="ca-co"><?= icon('briefcase') ?><?= e($T('OEC Contracting', 'أو إي سي للمقاولات')) ?></p>
  </aside>
  <div class="ca-main pdx-main">
    <div class="ca-top"><strong><?= e($T('Project Portfolio', 'محفظة المشاريع')) ?></strong><span class="pdx-live"><i></i><?= e($T('Live', 'مباشر')) ?></span><span class="ca-period"><?= icon('calendar') ?><?= e($T('October 2026', 'أكتوبر 2026')) ?><?php if ($hijri): ?><em class="ca-hijri"><?= e($hijri) ?></em><?php endif; ?></span><b class="ca-user">JB</b></div>
    <div class="pdx-kpis">
      <div><span><?= e($T('Active projects', 'مشاريع قائمة')) ?></span><strong>18</strong><em><?= e($T('3 starting this month', '3 تبدأ هذا الشهر')) ?></em></div>
      <div><span><?= e($T('Contract value', 'قيمة العقود')) ?></span><strong><?= e($T('SAR 41.5M', '41.5 مليون ريال')) ?></strong><em><?= e($T('2 variations approved', 'اعتماد أمري تغيير')) ?></em></div>
      <div><span><?= e($T('Budget used', 'الميزانية المستهلكة')) ?></span><strong>62%</strong><em><?= e($T('Progress 64%', 'الإنجاز 64%')) ?></em><i class="pdx-prog" style="--p:62%"></i></div>
      <div><span><?= e($T('Billed this month', 'المفوتر هذا الشهر')) ?></span><strong><?= e($T('SAR 3.8M', '3.8 مليون ريال')) ?></strong><em><?= e($T('Retention held 10%', 'محتجزات 10%')) ?></em></div>
      <div><span><?= e($T('Forecast margin', 'الهامش المتوقع')) ?></span><strong class="is-warn">19.6%</strong><em><?= e($T('Plan 21%', 'المخطط 21%')) ?></em></div>
    </div>
    <div class="pdx-r2">
      <div class="ca-card">
        <p class="ca-card-t"><?= e($T('Budget against actual cost (SAR M)', 'الميزانية مقابل التكلفة الفعلية (مليون ريال)')) ?> <small><b class="pdx-k1"></b><?= e($T('Budget', 'الميزانية')) ?> <b class="pdx-k2"></b><?= e($T('Actual', 'الفعلي')) ?></small></p>
        <div class="pmd-bars"><?php foreach ([[$T('Tower B', 'البرج ب'), 14.2, 9.1], [$T('Warehouse', 'المستودع'), 8.6, 4.5], [$T('Clinic', 'العيادة'), 4.1, 3.7], [$T('School', 'المدرسة'), 6.3, 2.9], [$T('Mall', 'المول'), 8.3, 4.8]] as [$n, $b, $a]): ?><div><span class="pmd-pair"><i style="--h:<?= round($b / 14.2 * 100) ?>%"></i><i style="--h:<?= round($a / 14.2 * 100) ?>%"></i></span><em><?= e($n) ?></em></div><?php endforeach; ?></div>
      </div>
      <div class="ca-card">
        <p class="ca-card-t"><?= e($T('Project health', 'حالة المشاريع')) ?></p>
        <ul class="pdx-lines"><?php foreach ($projects as [$n, $prog, $bud, $s]): ?><li><b><?= e($n) ?></b><span class="pdx-st pdx-<?= $cls[$s] ?>"><i></i><?= e($stl[$s]) ?></span><span class="pdx-bar"><i style="--p:<?= $prog ?>%"></i></span><em><?= $prog ?>%</em><small><?= e($T('Budget used', 'الميزانية المستهلكة')) ?> <?= $bud ?>%</small></li><?php endforeach; ?></ul>
      </div>
    </div>
    <div class="pdx-r3">
      <div class="ca-card">
        <p class="ca-card-t"><?= e($T('Milestones this week', 'معالم هذا الأسبوع')) ?></p>
        <table class="ca-table pdx-wo">
          <thead><tr><th><?= e($T('Project', 'المشروع')) ?></th><th><?= e($T('Milestone', 'المعلم')) ?></th><th><?= e($T('Due', 'الموعد')) ?></th><th><?= e($T('Billing', 'الفوترة')) ?></th></tr></thead>
          <tbody><?php foreach ([[$T('Tower B', 'البرج ب'), $T('Slab level 14 poured', 'صب سقف الدور 14'), $T('14 Oct', '3 ربيع الآخر'), $T('SAR 620K', '620 ألف ريال'), false], [$T('Warehouse', 'المستودع'), $T('Steel structure done', 'اكتمال الهيكل المعدني'), $T('15 Oct', '4 ربيع الآخر'), $T('SAR 410K', '410 آلاف ريال'), false], [$T('Clinic', 'العيادة'), $T('MEP first fix', 'التمديدات الأولى'), $T('16 Oct', '5 ربيع الآخر'), $T('SAR 180K', '180 ألف ريال'), false], [$T('School', 'المدرسة'), $T('Foundations handed over', 'تسليم الأساسات'), $T('Late', 'متأخر'), $T('SAR 260K', '260 ألف ريال'), true]] as [$p, $m, $d, $b, $late]): ?><tr><td><?= e($p) ?></td><td><?= e($m) ?></td><td<?= $late ? ' class="is-warn"' : '' ?>><?= e($d) ?></td><td><?= e($b) ?></td></tr><?php endforeach; ?></tbody>
        </table>
      </div>
      <div class="ca-card">
        <p class="ca-card-t"><?= e($T('Cost to date by type (SAR M)', 'التكلفة حتى الآن حسب النوع (مليون ريال)')) ?></p>
        <ul class="pdx-hbars"><?php foreach ([[$T('Subcontractors', 'مقاولو الباطن'), 9.8], [$T('Materials', 'المواد'), 8.1], [$T('Labour', 'العمالة'), 4.6], [$T('Equipment', 'المعدات'), 2.5]] as [$r, $m]): ?><li><span><?= e($r) ?></span><i style="--p:<?= round($m / 9.8 * 100) ?>%"></i><b><?= $m ?></b></li><?php endforeach; ?></ul>
      </div>
    </div>
  </div>
</div>
