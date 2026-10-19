<?php
/** Insights article (C38). Body HTML comes from the admin (trusted editors). TOC is built from its <h2> tags. */
$body = $a['body_html'] ?? '';
$toc = [];
$body = preg_replace_callback('~<h2([^>]*)>(.*?)</h2>~is', function ($m) use (&$toc) {
    $text = trim(strip_tags($m[2]));
    $id = 'h-' . (count($toc) + 1);
    $toc[] = [$id, $text];
    return '<h2 id="' . $id . '"' . $m[1] . '>' . $m[2] . '</h2>';
}, $body);
?>
<article class="article">
  <header class="section page-hero article-head">
    <div class="container narrow">
      <?php if ($a['status'] === 'draft'): ?><p><span class="card-badge draft-badge"><?= e(t('draft')) ?></span></p><?php endif; ?>
      <h1><?= e($a['h1'] ?: $a['title']) ?></h1>
      <div class="byline">
        <?php if ($a['author_photo'] && asset_exists($a['author_photo'])): ?><img src="<?= e(asset($a['author_photo'])) ?>" alt="<?= e($a['author_name']) ?>" width="48" height="48" class="avatar"><?php endif; ?>
        <div>
          <?php if ($a['author_name']): ?>
          <p class="author"><?= e(t('by')) ?> <strong><?= e($a['author_name']) ?></strong><?= $a['author_title'] ? ', ' . e($a['author_title']) : '' ?>
            <?php if ($a['author_linkedin']): ?><a href="<?= e($a['author_linkedin']) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><?= icon('linkedin') ?></a><?php endif; ?></p>
          <?php elseif (show_markers()): ?><p><?= rich('[CONFIRM: author name, photo, LinkedIn]') ?></p><?php endif; ?>
          <p class="meta-line">
            <?php if ($a['published_at']): ?><?= e(t('published')) ?>: <time datetime="<?= e($a['published_at']) ?>"><?= e(format_date($a['published_at'])) ?></time> · <?php endif; ?>
            <?= e(sprintf(t('min_read'), max(1, (int) $a['reading_minutes']))) ?>
          </p>
        </div>
      </div>
    </div>
  </header>
  <div class="container article-layout">
    <?php if ($toc): ?>
    <nav class="toc" aria-label="<?= e(t('toc')) ?>">
      <p class="toc-title"><?= e(t('toc')) ?></p>
      <ol><?php foreach ($toc as [$id, $text]): ?><li><a href="#<?= $id ?>"><?= e($text) ?></a></li><?php endforeach; ?></ol>
    </nav>
    <?php endif; ?>
    <div class="prose article-body"><?= $body ?></div>
  </div>
</article>
<?php if ($faq): ?>
<?= render_sections([['type' => 'faq', 'items' => $faq]], ['key' => 'insights', 'interest' => 'sap']) ?>
<?php endif; ?>
<?php if ($a['related_url']): ?>
<?= render_sections([['type' => 'cta_box', 'h2' => $a['related_label'] ?: t('learn_more'), 'button' => [t('learn_more'), $a['related_url'], 'light']]], ['key' => 'insights', 'interest' => 'sap']) ?>
<?php endif; ?>
<?php if ($related): ?>
<section class="section section-muted">
  <div class="container">
    <h2><?= e(t('related_articles')) ?></h2>
    <ul class="cards cols-3">
      <?php foreach ($related as $r): ?>
      <li class="card"><a class="card-inner" href="<?= e(url('/insights/' . $r['slug'] . '/')) ?>"><h3><?= e($r['title']) ?></h3><span class="card-link"><?= e(t('read_more')) ?> <?= icon('arrow') ?></span></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>
<?= render_sections([['type' => 'cta_band']], ['key' => 'insights', 'interest' => 'sap']) ?>
