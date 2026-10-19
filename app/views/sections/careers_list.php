<?php
/** careers_list: open roles from MySQL. Hidden when there are none (only the CV form shows). */
$rows = db_all("SELECT slug, title, location, employment_type, valid_through FROM careers WHERE lang = ? AND status = 'open' AND (valid_through IS NULL OR valid_through >= CURDATE()) ORDER BY date_posted DESC", [lang()]);
if (!$rows) return;
?>
<section class="section" id="open-roles">
  <div class="container narrow">
    <h2><?= e(t('open_roles')) ?></h2>
    <ul class="role-list">
      <?php foreach ($rows as $r): ?>
      <li><a href="<?= e(url('/careers/' . $r['slug'] . '/')) ?>">
        <strong><?= e($r['title']) ?></strong>
        <span><?= e($r['location']) ?> · <?= e(employment_label($r['employment_type'])) ?></span>
        <?= icon('arrow') ?>
      </a></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
