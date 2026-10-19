<?php
/** insights_list: articles from MySQL (published; drafts too in staging). */
$statuses = is_staging() ? ['published', 'draft'] : ['published'];
$in = implode(',', array_fill(0, count($statuses), '?'));
$rows = db_all("SELECT slug, title, excerpt, published_at, status, reading_minutes FROM insights WHERE lang = ? AND status IN ($in) ORDER BY published_at DESC, id DESC", [lang(), ...$statuses]);
?>
<section class="section">
  <div class="container">
    <?php if (!$rows): ?>
      <p class="center lead"><?= e(t('no_articles')) ?></p>
    <?php else: ?>
    <ul class="cards cols-3">
      <?php foreach ($rows as $r): ?>
      <li class="card"><a class="card-inner" href="<?= e(url('/insights/' . $r['slug'] . '/')) ?>">
        <?php if ($r['status'] === 'draft' && show_markers()): ?><span class="card-badge draft-badge"><?= e(t('draft')) ?></span><?php endif; ?>
        <h2 class="h3"><?= e($r['title']) ?></h2>
        <?php if ($r['excerpt']): ?><p><?= e($r['excerpt']) ?></p><?php endif; ?>
        <p class="meta-line"><?php if ($r['published_at']): ?><time datetime="<?= e($r['published_at']) ?>"><?= e(format_date($r['published_at'])) ?></time> · <?php endif; ?><?= e(sprintf(t('min_read'), max(1, (int) $r['reading_minutes']))) ?></p>
        <span class="card-link"><?= e(t('read_more')) ?> <?= icon('arrow') ?></span>
      </a></li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>
</section>
