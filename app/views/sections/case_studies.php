<?php
/** case_studies: published case studies from MySQL; hidden when none (spec C1 §10, C4). */
$rows = db_all("SELECT slug, client, industry, title, summary FROM case_studies WHERE lang = ? AND status = 'published' ORDER BY sort, id LIMIT " . (int) ($s['limit'] ?? 6), [lang()]);
if (!$rows) {
    if (show_markers() && !empty($s['placeholder'])) echo '<div class="container center staging-note"><p>' . rich('[CONFIRM: real case studies; section hidden until available]') . '</p></div>';
    return;
}
?>
<section class="section">
  <div class="container">
    <h2><?= rich($s['h2'] ?? t('case_studies')) ?></h2>
    <ul class="cards cols-<?= min(3, count($rows)) ?>">
      <?php foreach ($rows as $r): ?>
      <li class="card"><a class="card-inner" href="<?= e(url('/our-clients/' . $r['slug'] . '/')) ?>">
        <span class="card-badge"><?= e($r['industry']) ?></span>
        <h3><?= e($r['title']) ?></h3>
        <p><?= e($r['summary']) ?></p>
        <span class="card-link"><?= e(t('read_more')) ?> <?= icon('arrow') ?></span>
      </a></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
