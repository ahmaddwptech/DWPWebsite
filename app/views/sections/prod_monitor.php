<?php
/**
 * prod_monitor: eyebrow, h2, intro, bg, tabs[[view, icon, label]] ('floor' | 'oee' | 'quality' | 'cost'), points[[icon, title, text]]
 * Large production monitoring dashboard in an application window, with tabs switching between the live shop floor,
 * OEE, quality and costing views (partials/prod-dash.php). Without JavaScript the first view shows.
 */
$tabs = $s['tabs'] ?? [];
if (!$tabs) return;
?>
<section class="<?= section_class($s, 'pm-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <div class="pm" data-pm>
      <div class="demo-tabs pm-tabs" role="tablist">
        <?php foreach ($tabs as $n => [$v, $ic, $label]): ?>
        <button type="button" class="demo-tab" role="tab" aria-selected="<?= $n ? 'false' : 'true' ?>" data-pm-tab="<?= $n ?>"><?= icon($ic) ?><span><?= rich($label) ?></span></button>
        <?php endforeach; ?>
      </div>
      <div class="pm-window">
        <div class="pd-screen-bar pm-bar" aria-hidden="true"><span class="pd-dots"><i></i><i></i><i></i></span><span class="pd-screen-title">Cortex Production</span></div>
        <div class="pm-screen">
          <?php foreach ($tabs as $n => [$v]): ?>
          <div class="pm-view<?= $n ? '' : ' is-on' ?>" data-pm-view="<?= $n ?>"><?= view('partials/prod-dash', ['view' => $v]) ?></div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php if (!empty($s['points'])): ?>
      <ul class="pm-points"><?php foreach ($s['points'] as [$ic, $t, $x]): ?><li><span class="pm-icon"><?= icon($ic) ?></span><div><h3><?= rich($t) ?></h3><p><?= rich($x) ?></p></div></li><?php endforeach; ?></ul>
      <?php endif; ?>
    </div>
  </div>
</section>
