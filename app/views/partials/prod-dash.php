<?php
/**
 * Cortex Production dashboards drawn in HTML with sample data (sharp at any size, sized in container units).
 * $view: 'overview' (first section laptop: KPIs, output vs plan, line status, work orders, downtime),
 *        'floor' (machine board), 'oee' (OEE by line), 'quality' (defects and inspections), 'cost' (actual vs standard).
 * The screens stay in English on both language versions, like the real application.
 */
$view = $view ?? ($w['view'] ?? 'overview');
$logo = 'img/cortex/cortex-production.webp';
$st = ['Running' => 'run', 'Idle' => 'idle', 'Stopped' => 'stop', 'Changeover' => 'chg'];
$lines = [['Line A', 'Running', 86, 'WO 2291 Steel shelving'], ['Line B', 'Running', 79, 'WO 2297 Wire mesh deck'], ['Line C', 'Changeover', 64, 'Next: WO 2298'], ['Line D', 'Stopped', 41, 'Maintenance, 18 min'], ['Line E', 'Running', 83, 'WO 2295 Storage cabinet']];
$hours = ['06', '07', '08', '09', '10', '11', '12', '13'];
$actual = [780, 860, 905, 640, 820, 910, 880, 700];
$plan = 850;
$pts = function (array $vals, int $max) { $n = count($vals) - 1; $o = []; foreach ($vals as $i => $v) $o[] = round($i * 300 / $n, 1) . ',' . round(100 - $v * 100 / $max, 1); return implode(' ', $o); };
?>
<?php if ($view === 'overview'):
  $ar = is_ar(); $T = fn(string $en, string $a) => $ar ? $a : $en; $hj = hijri('2026-10-12', $ar ? 'ar' : 'en');
  $stA = ['Running' => $T('Running', 'تعمل'), 'Changeover' => $T('Changeover', 'تبديل'), 'Stopped' => $T('Stopped', 'متوقفة'), 'Idle' => $T('Idle', 'خاملة')];
  $linesT = [[$T('Line A', 'الخط أ'), 'Running', 86, $T('WO 2291 Steel shelving', 'أمر 2291 أرفف معدنية')], [$T('Line B', 'الخط ب'), 'Running', 79, $T('WO 2297 Wire mesh deck', 'أمر 2297 أرضيات شبكية')], [$T('Line C', 'الخط ج'), 'Changeover', 64, $T('Next: WO 2298', 'التالي: أمر 2298')], [$T('Line D', 'الخط د'), 'Stopped', 41, $T('Maintenance, 18 min', 'صيانة، 18 دقيقة')], [$T('Line E', 'الخط هـ'), 'Running', 83, $T('WO 2295 Storage cabinet', 'أمر 2295 خزانة تخزين')]];
?>
<div class="ca pdx" dir="<?= $ar ? 'rtl' : 'ltr' ?>" lang="<?= $ar ? 'ar' : 'en' ?>" role="img" aria-label="<?= e($T('Cortex Production monitor: OEE, output against plan, line status, work orders and downtime', 'مراقبة Cortex Production: الكفاءة الشاملة والإنتاج مقابل الخطة وحالة الخطوط وأوامر العمل والتوقف')) ?>">
  <aside class="ca-side">
    <?php if (asset_exists($logo)): ?><img src="<?= e(asset($logo)) ?>" alt="Cortex Production" width="1228" height="270" loading="lazy"><?php endif; ?>
    <ul><?php foreach ([['chart', $T('Monitor', 'المراقبة')], ['factory', $T('Work Orders', 'أوامر العمل')], ['calendar', $T('Planning', 'التخطيط')], ['doc', $T('Bills of Materials', 'قوائم المواد')], ['check', $T('Quality', 'الجودة')], ['gear', $T('Maintenance', 'الصيانة')], ['coins', $T('Costing', 'التكاليف')]] as $i => [$ic, $l]): ?><li<?= $i ? '' : ' class="is-on"' ?>><?= icon($ic) ?><?= e($l) ?></li><?php endforeach; ?></ul>
    <p class="ca-co"><?= icon('briefcase') ?><?= e($T('OEC Computers', 'شركة أو إي سي للحاسبات')) ?></p>
  </aside>
  <div class="ca-main pdx-main">
    <div class="ca-top"><strong><?= e($T('Production Monitor', 'مراقبة الإنتاج')) ?></strong><span class="pdx-live"><i></i><?= e($T('Live', 'مباشر')) ?></span><span class="ca-period"><?= icon('clock') ?><?= e($T('Shift A, 06:00 to 14:00', 'الوردية أ، 06:00 إلى 14:00')) ?><?php if ($hj): ?><em class="ca-hijri"><?= e($hj) ?></em><?php endif; ?></span><b class="ca-user">JB</b></div>
    <div class="pdx-kpis">
      <div><span><?= e($T('OEE', 'الكفاءة الشاملة')) ?></span><strong>78%</strong><em><?= e($T('A 91% · P 89% · Q 96%', 'جاهزية 91% · أداء 89% · جودة 96%')) ?></em></div>
      <div><span><?= e($T('Output today', 'إنتاج اليوم')) ?></span><strong>6,495</strong><em><?= e($T('Plan 6,800 units', 'المخطط 6,800 وحدة')) ?></em><i class="pdx-prog" style="--p:95%"></i></div>
      <div><span><?= e($T('Open work orders', 'أوامر عمل مفتوحة')) ?></span><strong>46</strong><em><?= e($T('12 due this week', '12 مستحقة هذا الأسبوع')) ?></em></div>
      <div><span><?= e($T('Downtime today', 'التوقف اليوم')) ?></span><strong class="is-warn"><?= e($T('52 min', '52 دقيقة')) ?></strong><em><?= e($T('Line D maintenance', 'صيانة الخط د')) ?></em></div>
      <div><span><?= e($T('Scrap rate', 'نسبة الهالك')) ?></span><strong>1.8%</strong><em><?= e($T('Down from 2.4%', 'انخفضت من 2.4%')) ?></em></div>
    </div>
    <div class="pdx-r2">
      <div class="ca-card">
        <p class="ca-card-t"><?= e($T('Output against plan by hour', 'الإنتاج مقابل الخطة لكل ساعة')) ?> <small><b class="pdx-k1"></b><?= e($T('Actual', 'الفعلي')) ?> <b class="pdx-k2"></b><?= e($T('Plan', 'المخطط')) ?></small></p>
        <div class="pdx-chart">
          <svg viewBox="0 0 300 100" preserveAspectRatio="none" aria-hidden="true"<?= $ar ? ' style="transform:scaleX(-1)"' : '' ?>>
            <defs><linearGradient id="pdx-fill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3b82f6" stop-opacity=".35"/><stop offset="1" stop-color="#3b82f6" stop-opacity="0"/></linearGradient></defs>
            <polygon points="0,100 <?= $pts($actual, 1000) ?> 300,100" fill="url(#pdx-fill)"/>
            <polyline points="0,<?= 100 - $plan / 10 ?> 300,<?= 100 - $plan / 10 ?>" fill="none" stroke="#94a3b8" stroke-width="1.5" vector-effect="non-scaling-stroke"/>
            <polyline points="<?= $pts($actual, 1000) ?>" fill="none" stroke="#1d5fd1" stroke-width="2.5" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
          </svg>
          <div class="pdx-axis"><?php foreach ($hours as $h): ?><span><?= $h ?>:00</span><?php endforeach; ?></div>
        </div>
      </div>
      <div class="ca-card">
        <p class="ca-card-t"><?= e($T('Lines', 'الخطوط')) ?></p>
        <ul class="pdx-lines"><?php foreach ($linesT as [$n, $s, $oee, $note]): ?><li><b><?= e($n) ?></b><span class="pdx-st pdx-<?= $st[$s] ?>"><i></i><?= e($stA[$s]) ?></span><span class="pdx-bar"><i style="--p:<?= $oee ?>%"></i></span><em><?= $oee ?>%</em><small><?= e($note) ?></small></li><?php endforeach; ?></ul>
      </div>
    </div>
    <div class="pdx-r3">
      <div class="ca-card">
        <p class="ca-card-t"><?= e($T('Work orders in progress', 'أوامر عمل قيد التنفيذ')) ?></p>
        <table class="ca-table pdx-wo">
          <thead><tr><th><?= e($T('Order', 'الأمر')) ?></th><th><?= e($T('Product', 'المنتج')) ?></th><th><?= e($T('Line', 'الخط')) ?></th><th><?= e($T('Progress', 'الإنجاز')) ?></th><th><?= e($T('Due', 'الموعد')) ?></th></tr></thead>
          <tbody><?php foreach ([['WO 2291', $T('Steel shelving 2 m', 'أرفف معدنية 2 م'), $T('A', 'أ'), 72, $T('Today', 'اليوم'), false], ['WO 2297', $T('Wire mesh deck', 'أرضيات شبكية'), $T('B', 'ب'), 48, $T('Tomorrow', 'غداً'), false], ['WO 2295', $T('Storage cabinet', 'خزانة تخزين'), $T('E', 'هـ'), 35, $T('15 Oct', '4 ربيع الآخر'), false], ['WO 2294', $T('Pallet racking', 'رفوف تخزين'), $T('D', 'د'), 20, $T('Late', 'متأخر'), true]] as [$o, $p, $l, $pr, $d, $late]): ?><tr><td><?= e($o) ?></td><td><?= e($p) ?></td><td><?= e($l) ?></td><td><span class="pdx-bar"><i style="--p:<?= $pr ?>%"></i></span> <?= $pr ?>%</td><td<?= $late ? ' class="is-warn"' : '' ?>><?= e($d) ?></td></tr><?php endforeach; ?></tbody>
        </table>
      </div>
      <div class="ca-card">
        <p class="ca-card-t"><?= e($T('Downtime reasons today (min)', 'أسباب التوقف اليوم (دقيقة)')) ?></p>
        <ul class="pdx-hbars"><?php foreach ([[$T('Maintenance', 'الصيانة'), 18], [$T('Changeover', 'التبديل'), 14], [$T('Waiting for material', 'انتظار المواد'), 12], [$T('Quality hold', 'حجز الجودة'), 8]] as [$r, $m]): ?><li><span><?= e($r) ?></span><i style="--p:<?= round($m / 18 * 100) ?>%"></i><b><?= $m ?></b></li><?php endforeach; ?></ul>
      </div>
    </div>
  </div>
</div>

<?php elseif ($view === 'floor'): ?>
<div class="pdx-panel" dir="ltr" lang="en">
  <div class="pdx-head"><strong>Shop floor</strong><span class="pdx-live"><i></i>Live, updated 5 seconds ago</span><span class="pdx-legend"><span class="pdx-st pdx-run"><i></i>Running 9</span><span class="pdx-st pdx-chg"><i></i>Changeover 1</span><span class="pdx-st pdx-idle"><i></i>Idle 1</span><span class="pdx-st pdx-stop"><i></i>Stopped 1</span></span></div>
  <div class="pdx-machines">
    <?php foreach ([
      ['Press P 01', 'Running', 'WO 2291 Steel shelving', 42, 'pcs/min', 72, 'Faisal A.'], ['Press P 02', 'Running', 'WO 2291 Steel shelving', 40, 'pcs/min', 72, 'Majed S.'],
      ['Laser L 01', 'Running', 'WO 2297 Wire mesh deck', 18, 'sheets/h', 48, 'Ravi K.'], ['Welder W 03', 'Changeover', 'Next: WO 2298 Workbench', 0, 'pcs/min', 0, 'Omar F.'],
      ['Paint line PL 1', 'Running', 'WO 2295 Storage cabinet', 6, 'm/min', 35, 'Saleh H.'], ['Bender B 02', 'Stopped', 'Maintenance, 18 min', 0, 'pcs/min', 20, 'Ali M.'],
      ['Robot R 01', 'Running', 'WO 2297 Wire mesh deck', 26, 'pcs/min', 48, 'Auto'], ['Packing PK 1', 'Idle', 'Waiting for material', 0, 'boxes/h', 0, 'Hassan N.'],
    ] as [$m, $s, $wo, $spd, $u, $pr, $op]): ?>
    <div class="pdx-m pdx-m-<?= $st[$s] ?>">
      <div class="pdx-m-top"><b><?= e($m) ?></b><span class="pdx-st pdx-<?= $st[$s] ?>"><i></i><?= e($s) ?></span></div>
      <p class="pdx-m-wo"><?= e($wo) ?></p>
      <p class="pdx-m-spd"><strong><?= $spd ?></strong> <?= e($u) ?></p>
      <span class="pdx-bar"><i style="--p:<?= $pr ?>%"></i></span>
      <p class="pdx-m-foot"><span><?= $pr ?>% of order</span><span><?= icon('users') ?><?= e($op) ?></span></p>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php elseif ($view === 'oee'): ?>
<div class="pdx-panel" dir="ltr" lang="en">
  <div class="pdx-head"><strong>OEE by line</strong><span class="pdx-note">Availability × Performance × Quality, this shift</span></div>
  <div class="pdx-gauges">
    <?php foreach ([['Line A', 86, 94, 93, 98], ['Line B', 79, 90, 91, 97], ['Line C', 64, 72, 92, 96], ['Line D', 41, 48, 88, 97], ['Line E', 83, 93, 92, 97]] as [$n, $o, $a, $p, $q]): $c = 2 * M_PI * 40; ?>
    <div class="pdx-g">
      <svg viewBox="0 0 100 100" aria-hidden="true"><circle cx="50" cy="50" r="40" class="pdx-g-bg"/><circle cx="50" cy="50" r="40" class="pdx-g-fg<?= $o < 60 ? ' is-low' : ($o < 75 ? ' is-mid' : '') ?>" style="stroke-dasharray:<?= round($c * $o / 100, 1) ?> <?= round($c, 1) ?>"/></svg>
      <strong><?= $o ?>%</strong><b><?= e($n) ?></b>
      <ul><li><span>Availability</span><i style="--p:<?= $a ?>%"></i><em><?= $a ?>%</em></li><li><span>Performance</span><i style="--p:<?= $p ?>%"></i><em><?= $p ?>%</em></li><li><span>Quality</span><i style="--p:<?= $q ?>%"></i><em><?= $q ?>%</em></li></ul>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="pdx-trend"><p>Plant OEE, last 7 days</p><div><?php foreach ([['Mon', 72], ['Tue', 75], ['Wed', 74], ['Thu', 77], ['Fri', 79], ['Sat', 76], ['Sun', 78]] as [$d, $v]): ?><span style="--h:<?= ($v - 60) * 4 ?>%"><i></i><b><?= $v ?>%</b><em><?= $d ?></em></span><?php endforeach; ?></div></div>
</div>

<?php elseif ($view === 'quality'): ?>
<div class="pdx-panel" dir="ltr" lang="en">
  <div class="pdx-head"><strong>Quality</strong><span class="pdx-note">Inspections and defects, this week</span></div>
  <div class="pdx-qkpis"><div><span>First pass yield</span><strong>96.4%</strong></div><div><span>Inspections</span><strong>312</strong></div><div><span>Defects found</span><strong class="is-warn">37</strong></div><div><span>Open holds</span><strong>2</strong></div></div>
  <div class="pdx-qrow">
    <div class="pdx-box"><p>Defects by type</p><ul class="pdx-hbars"><?php foreach ([['Paint finish', 14], ['Weld porosity', 9], ['Dimension out of tolerance', 7], ['Scratches', 5], ['Missing parts', 2]] as [$r, $m]): ?><li><span><?= e($r) ?></span><i style="--p:<?= round($m / 14 * 100) ?>%"></i><b><?= $m ?></b></li><?php endforeach; ?></ul></div>
    <div class="pdx-box"><p>Latest inspections</p><table class="ca-table"><thead><tr><th>Lot</th><th>Product</th><th>Stage</th><th>Result</th></tr></thead><tbody><?php foreach ([['L 8841', 'Steel shelving', 'Final', 'Passed'], ['L 8840', 'Storage cabinet', 'In process', 'On hold'], ['L 8838', 'Wire mesh deck', 'Final', 'Passed'], ['L 8837', 'Pallet racking', 'Receipt', 'Passed'], ['L 8835', 'Workbench', 'Final', 'Rework']] as [$l, $p, $s, $r]): ?><tr><td><?= e($l) ?></td><td><?= e($p) ?></td><td><?= e($s) ?></td><td><span class="ca-st <?= $r === 'Passed' ? 'ca-st-done' : 'ca-st-late' ?>"><?= e($r) ?></span></td></tr><?php endforeach; ?></tbody></table></div>
  </div>
</div>

<?php else: ?>
<div class="pdx-panel" dir="ltr" lang="en">
  <div class="pdx-head"><strong>Production costing</strong><span class="pdx-note">Actual against standard cost, closed work orders this month</span></div>
  <div class="pdx-qkpis"><div><span>Standard cost</span><strong>SAR 1.92M</strong></div><div><span>Actual cost</span><strong>SAR 1.97M</strong></div><div><span>Variance</span><strong class="is-warn">+2.6%</strong></div><div><span>Orders closed</span><strong>38</strong></div></div>
  <div class="pdx-qrow">
    <div class="pdx-box"><p>Variance by cost type</p><ul class="pdx-var"><?php foreach ([['Material', 1.4], ['Labour', 4.8], ['Machine time', 2.1], ['Overhead', -0.6]] as [$t, $v]): ?><li><span><?= e($t) ?></span><i class="<?= $v < 0 ? 'is-good' : '' ?>" style="--p:<?= round(abs($v) / 4.8 * 100) ?>%"></i><b><?= ($v > 0 ? '+' : '') . $v ?>%</b></li><?php endforeach; ?></ul></div>
    <div class="pdx-box"><p>Work orders</p><table class="ca-table"><thead><tr><th>Order</th><th>Standard</th><th>Actual</th><th>Variance</th></tr></thead><tbody><?php foreach ([['WO 2280', '58,400', '57,900', -0.9], ['WO 2283', '112,000', '118,300', 5.6], ['WO 2285', '46,200', '46,900', 1.5], ['WO 2286', '73,800', '75,100', 1.8], ['WO 2288', '39,500', '38,700', -2.0]] as [$o, $s, $a, $v]): ?><tr><td><?= e($o) ?></td><td>SAR <?= e($s) ?></td><td>SAR <?= e($a) ?></td><td class="<?= $v > 3 ? 'is-warn' : ($v < 0 ? 'is-good' : '') ?>"><?= ($v > 0 ? '+' : '') . $v ?>%</td></tr><?php endforeach; ?></tbody></table></div>
  </div>
</div>
<?php endif; ?>
