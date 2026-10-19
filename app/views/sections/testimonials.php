<?php
/** testimonials (B7): renders only approved testimonials from MySQL; hidden when empty. */
$rows = db_all('SELECT quote, person, role, company FROM testimonials WHERE approved = 1 AND lang = ? ORDER BY sort, id LIMIT 6', [lang()]);
if (!$rows) return;
?>
<section class="section section-muted">
  <div class="container">
    <h2 class="center"><?= e(t('testimonials')) ?></h2>
    <ul class="cards cols-<?= min(3, count($rows)) ?>">
      <?php foreach ($rows as $r): ?>
      <li class="card"><figure class="card-inner testimonial">
        <blockquote><p><?= e($r['quote']) ?></p></blockquote>
        <figcaption><strong><?= e($r['person']) ?></strong><?= $r['role'] ? ', ' . e($r['role']) : '' ?><?= $r['company'] ? ', ' . e($r['company']) : '' ?></figcaption>
      </figure></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
