<?php /** logos: h2, intro, items[{name, src, href, text}] — partner/integration logo grid */ ?>
<section class="<?= section_class($s) ?>">
  <div class="container">
    <?= section_head($s) ?>
    <ul class="logo-grid">
      <?php foreach ($s['items'] as $it): $tag = !empty($it['href']) ? 'a' : 'div'; ?>
      <li><<?= $tag ?> class="logo-tile"<?= $tag === 'a' ? ' href="' . e(url($it['href'])) . '"' : '' ?>>
        <?php if (!empty($it['src']) && asset_exists($it['src'])): ?>
          <img src="<?= e(asset($it['src'])) ?>" alt="<?= e(plain($it['name'])) ?>" width="120" height="60" loading="lazy">
        <?php else: ?>
          <span class="logo-name"><?= rich($it['name']) ?></span>
        <?php endif; ?>
        <?php if (!empty($it['text'])): ?><span class="logo-caption"><?= rich($it['text']) ?></span><?php endif; ?>
      </<?= $tag ?>></li>
      <?php endforeach; ?>
    </ul>
    <?php if (!empty($s['closing'])): ?><p class="closing"><?= rich($s['closing']) ?></p><?php endif; ?>
  </div>
</section>
