<?php
/**
 * services_showcase: eyebrow, h2, intro, link[label, href], bg,
 *   items[{icon, title, text, list[], href, image{src, alt, w, h}}]
 * Full-bleed photo tiles with the title over the image. On hover/focus the text and capabilities rise;
 * on touch screens they are always shown.
 */
$items = $s['items'] ?? [];
if (!$items) return;
?>
<section class="<?= section_class($s, 'services-showcase') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <div class="services-top">
      <?= section_head($s) ?>
      <?php if (!empty($s['link'])): ?><div class="services-top-link"><?= button([$s['link'][0], $s['link'][1], 'secondary']) ?></div><?php endif; ?>
    </div>
    <ul class="svc-grid">
      <?php foreach ($items as $n => $it): $tag = !empty($it['href']) ? 'a' : 'div'; ?>
      <li>
        <<?= $tag ?> class="svc-tile"<?= $tag === 'a' ? ' href="' . e(url($it['href'])) . '"' : '' ?>>
          <?= img($it['image'] + ['sizes' => '(min-width: 1000px) 33vw, (min-width: 640px) 50vw, 100vw']) ?>
          <span class="svc-top"><span class="svc-icon"><?= icon($it['icon']) ?></span></span>
          <span class="svc-content">
            <h3><?= rich($it['title']) ?></h3>
            <span class="svc-reveal"><span class="svc-reveal-inner">
              <?php if (!empty($it['text'])): ?><span class="svc-text"><?= rich($it['text']) ?></span><?php endif; ?>
              <?php if (!empty($it['list'])): ?><span class="svc-list"><?php foreach ($it['list'] as $li): ?><span><?= icon('check') ?><?= rich($li) ?></span><?php endforeach; ?></span><?php endif; ?>
              <?php if ($tag === 'a'): ?><span class="svc-link"><?= e($it['link_label'] ?? t('learn_more')) ?> <?= icon('arrow') ?></span><?php endif; ?>
            </span></span>
          </span>
        </<?= $tag ?>>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
