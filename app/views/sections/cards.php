<?php
/**
 * cards: h2, intro, eyebrow, items[{title, text, href, icon, image{}, featured, link_label, list, badge}],
 *        cols (2|3|4), closing, variant ('numbered'), bg ('' | 'muted' | 'dark'), link[label,href]
 * Items without text render as compact link tiles. 'aside_image' => {src,alt,w,h} puts a photo beside the cards.
 */
$items = $s['items'] ?? [];
$cols = (int) ($s['cols'] ?? min(4, max(2, count($items))));
$numbered = ($s['variant'] ?? '') === 'numbered';
$withImages = (bool) array_filter($items, fn($it) => !empty($it['image']));
$overlay = $withImages && !array_filter($items, fn($it) => empty($it['image']) || !empty($it['text']) || !empty($it['list']));
$tiles = !$numbered && $items && !array_filter($items, fn($it) => !empty($it['text']) || !empty($it['list']) || !empty($it['image']));
?>
<section class="<?= section_class($s) ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?php if (!empty($s['aside_image'])): ?><div class="cards-aside"><div class="cards-aside-media framed"><?= img($s['aside_image']) ?></div><div><?php endif; ?>
    <?= section_head($s) ?>
    <ul class="cards cols-<?= $cols ?><?= $numbered ? ' cards-numbered' : '' ?><?= $tiles ? ' cards-tiles' : '' ?><?= $withImages ? ' cards-image' : '' ?><?= $overlay ? ' cards-overlay' : '' ?>">
      <?php foreach ($items as $n => $it): $tag = !empty($it['href']) ? 'a' : 'div'; ?>
      <li class="card<?= !empty($it['featured']) ? ' card-featured' : '' ?>">
        <<?= $tag ?> class="card-inner"<?= $tag === 'a' ? ' href="' . e(url($it['href'])) . '"' : '' ?>>
          <?php if (!empty($it['image'])): ?><div class="card-media"><?= img($it['image'] + ['sizes' => $cols >= 4 ? '(min-width: 1000px) 25vw, (min-width: 640px) 50vw, 100vw' : '(min-width: 1000px) 33vw, (min-width: 640px) 50vw, 100vw']) ?></div><?php endif; ?>
          <?php if (!empty($it['icon'])): ?><span class="card-icon"><?= icon($it['icon']) ?></span><?php endif; ?>
          <?php if (!empty($it['badge'])): ?><span class="card-badge"><?= rich($it['badge']) ?></span><?php endif; ?>
          <h3><?= rich($it['title']) ?></h3>
          <?php if (!empty($it['text'])): ?><?= paragraphs($it['text']) ?><?php endif; ?>
          <?php if (!empty($it['list'])): ?><ul class="checklist small"><?php foreach ($it['list'] as $li): ?><li><?= icon('check') ?><span><?= rich($li) ?></span></li><?php endforeach; ?></ul><?php endif; ?>
          <?php if ($tag === 'a'): ?>
            <?php if ($tiles): ?><span class="tile-arrow"><?= icon('arrow') ?></span>
            <?php else: ?><span class="card-link"><?= e($it['link_label'] ?? t('learn_more')) ?> <?= icon('arrow') ?></span><?php endif; ?>
          <?php endif; ?>
        </<?= $tag ?>>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php if (!empty($s['aside_image'])): ?></div></div><?php endif; ?>
    <?php if (!empty($s['closing'])): ?><p class="closing"><?= rich($s['closing']) ?></p><?php endif; ?>
    <?php if (!empty($s['link'])): ?><p class="section-link"><a href="<?= e(url($s['link'][1])) ?>"><?= rich($s['link'][0]) ?> <?= icon('arrow') ?></a></p><?php endif; ?>
  </div>
</section>
