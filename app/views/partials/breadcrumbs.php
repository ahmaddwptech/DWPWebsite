<nav class="breadcrumbs container" aria-label="<?= e(t('breadcrumb')) ?>">
  <ol>
    <?php $last = count($trail) - 1; foreach ($trail as $i => [$name, $path]): ?>
      <?php if ($i < $last): ?>
        <li><a href="<?= e(url($path)) ?>"><?= e(plain($name)) ?></a></li>
      <?php else: ?>
        <li aria-current="page"><?= e(plain($name)) ?></li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ol>
</nav>
