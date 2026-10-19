<?php
/** assessment_result: result screen loaded by token. */
$r = $s['row'];
$bc = assessment_band_content($r['band'], lang());
?>
<section class="section page-hero">
  <div class="container narrow" <?= !empty($_GET['new']) ? 'data-events="assessment_complete,generate_lead"' : '' ?>>
    <p class="eyebrow"><?= e(t('assessment.result_heading')) ?></p>
    <h1><?= rich($bc['title']) ?></h1>
    <div class="score" style="--score: <?= (int) $r['score'] ?>">
      <span class="score-value"><?= (int) $r['score'] ?></span><span class="score-max">/100</span>
      <span class="score-label"><?= e(t('assessment.score')) ?></span>
    </div>
    <p class="lead"><?= rich($bc['text']) ?></p>
    <h2><?= e(t('assessment.next_steps')) ?></h2>
    <ol class="steps steps-3">
      <?php foreach ($bc['steps'] as $n => $st): ?><li class="step"><div><p><?= rich($st) ?></p></div></li><?php endforeach; ?>
    </ol>
    <div class="btn-row">
      <?= button([t('book_free_demo'), '/book-a-demo/', 'primary']) ?>
      <?php if ($r['band'] === 'odoo'): ?><?= button([is_ar() ? 'نظام Odoo' : 'Odoo ERP', '/erp/odoo/', 'secondary']) ?><?php else: ?><?= button([is_ar() ? 'اكتشف SAP Business One' : 'Explore SAP Business One', '/sap-business-one/', 'secondary']) ?><?php endif; ?>
    </div>
  </div>
</section>
