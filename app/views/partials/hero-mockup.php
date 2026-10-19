<?php
/** Decorative SAP Business One–style dashboard, drawn in HTML/CSS (used when no hero screenshot is available). */
$ar = is_ar();
$L = $ar
    ? ['title' => 'لوحة المتابعة', 'rev' => 'المبيعات (الشهر)', 'stock' => 'قيمة المخزون', 'orders' => 'أوامر مفتوحة', 'zatca' => 'فواتير معتمدة من زاتكا', 'chart' => 'المبيعات حسب الفرع', 'ai' => 'تنبيه ذكي', 'ai_text' => 'من المتوقع نفاد 3 أصناف خلال 10 أيام، واقتراح أمر شراء جاهز.', 'menu' => ['المالية', 'المبيعات', 'المشتريات', 'المخزون', 'الإنتاج', 'التقارير'], 'branches' => ['الرياض', 'جدة', 'الدمام', 'مكة', 'المدينة', 'أبها']]
    : ['title' => 'Dashboard', 'rev' => 'Sales (month)', 'stock' => 'Stock value', 'orders' => 'Open orders', 'zatca' => 'ZATCA-cleared invoices', 'chart' => 'Sales by branch', 'ai' => 'AI alert', 'ai_text' => '3 items will run out within 10 days. A purchase order draft is ready.', 'menu' => ['Finance', 'Sales', 'Purchasing', 'Inventory', 'Production', 'Reports'], 'branches' => ['Riyadh', 'Jeddah', 'Dammam', 'Makkah', 'Madinah', 'Abha']];
$bars = [92, 68, 74, 51, 43, 30];
?>
<div class="mockup" aria-hidden="true">
  <div class="mockup-bar"><span></span><span></span><span></span><em>SAP Business One</em></div>
  <div class="mockup-body">
    <ul class="mockup-side">
      <?php foreach ($L['menu'] as $i => $m): ?><li<?= $i === 0 ? ' class="on"' : '' ?>><?= e($m) ?></li><?php endforeach; ?>
    </ul>
    <div class="mockup-main">
      <p class="mockup-title"><?= e($L['title']) ?></p>
      <div class="mockup-kpis">
        <div><small><?= e($L['rev']) ?></small><b dir="ltr">SAR 4.2M</b><i class="up">▲ 12%</i></div>
        <div><small><?= e($L['stock']) ?></small><b dir="ltr">SAR 1.8M</b><i>▬ 0%</i></div>
        <div><small><?= e($L['orders']) ?></small><b dir="ltr">146</b><i class="up">▲ 8</i></div>
        <div><small><?= e($L['zatca']) ?></small><b dir="ltr">99.8%</b><i class="up">✓</i></div>
      </div>
      <div class="mockup-chart">
        <small><?= e($L['chart']) ?></small>
        <div class="bars">
          <?php foreach ($bars as $i => $h): ?><div class="bar"><span style="height:<?= $h ?>%"></span><em><?= e($L['branches'][$i]) ?></em></div><?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="mockup-ai"><strong><?= e($L['ai']) ?></strong><span><?= e($L['ai_text']) ?></span></div>
</div>
