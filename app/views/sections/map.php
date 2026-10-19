<?php /** map: Google Maps embed of the Riyadh office (loads on click to protect performance and privacy). */
$q = rawurlencode('Hala Center, Anas Bin Malik Road, Riyadh, Saudi Arabia');
?>
<section class="section map-section">
  <div class="container">
    <div class="map-embed" data-map-src="https://www.google.com/maps?q=<?= $q ?>&output=embed&hl=<?= lang() ?>">
      <button type="button" class="btn btn-secondary" data-map-load><?= icon('map') ?><span><?= is_ar() ? 'عرض الخريطة' : 'Show map' ?></span></button>
      <p><a href="https://www.google.com/maps/search/?api=1&query=<?= $q ?>" target="_blank" rel="noopener"><?= e(t('footer.address')) ?></a></p>
    </div>
  </div>
</section>
