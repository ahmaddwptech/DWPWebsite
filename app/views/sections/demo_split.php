<?php /** demo_split (C6): h2, items[] what you'll see — left; demo form — right */ ?>
<section class="section">
  <div class="container form-container">
    <div class="form-intro">
      <h2><?= rich($s['h2']) ?></h2>
      <ul class="checklist"><?php foreach ($s['items'] as $it): ?><li><?= icon('check') ?><span><?= rich($it) ?></span></li><?php endforeach; ?></ul>
      <?php if (!empty($s['note'])): ?><p class="note"><?= rich($s['note']) ?></p><?php endif; ?>
    </div>
    <div class="card form-card" id="lets-talk">
      <?= view('partials/lead-form', ['variant' => 'demo', 'interest' => $page['interest'], 'heading' => null, 'id' => 'demo-form']) ?>
    </div>
  </div>
</section>
