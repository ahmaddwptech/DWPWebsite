<?php /** Facts bar along the bottom of every first section: plain, verifiable facts about DWP Arabia */ ?>
<div class="ed-facts">
  <ul class="container">
    <?php foreach ((array) t('hero_facts') as $f): ?><li><?= e($f) ?></li><?php endforeach; ?>
  </ul>
</div>
