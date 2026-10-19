<section class="section page-hero">
  <div class="container narrow">
    <p class="eyebrow"><?= e($cs['industry']) ?></p>
    <h1><?= e($cs['title']) ?></h1>
    <p class="lead"><?= e($cs['summary']) ?></p>
  </div>
</section>
<section class="section">
  <div class="container narrow prose">
    <dl class="case-facts">
      <div><dt><?= e(t('case.client')) ?></dt><dd><?= e($cs['client']) ?></dd></div>
      <div><dt><?= e(t('case.industry')) ?></dt><dd><?= e($cs['industry']) ?></dd></div>
    </dl>
    <h2><?= e(t('case.challenge')) ?></h2><?= $cs['challenge_html'] ?>
    <h2><?= e(t('case.solution')) ?></h2><?= $cs['solution_html'] ?>
    <h2><?= e(t('case.results')) ?></h2><?= $cs['results_html'] ?>
    <?php if ($cs['quote']): ?>
    <figure class="pull-quote"><blockquote><p><?= e($cs['quote']) ?></p></blockquote><?php if ($cs['quote_by']): ?><figcaption><?= e($cs['quote_by']) ?></figcaption><?php endif; ?></figure>
    <?php endif; ?>
  </div>
</section>
