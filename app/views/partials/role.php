<section class="section page-hero">
  <div class="container narrow">
    <h1><?= e($r['title']) ?></h1>
    <ul class="role-facts">
      <li><?= icon('pin') ?><span><?= e(t('location')) ?>: <?= e($r['location']) ?></span></li>
      <li><?= icon('briefcase') ?><span><?= e(t('employment_type')) ?>: <?= e(employment_label($r['employment_type'])) ?></span></li>
      <?php if ($r['valid_through']): ?><li><?= icon('calendar') ?><span><?= e(t('apply_by')) ?>: <?= e(format_date($r['valid_through'])) ?></span></li><?php endif; ?>
    </ul>
  </div>
</section>
<section class="section"><div class="container narrow prose"><?= $r['body_html'] ?></div></section>
