<div class="cookie-banner" role="dialog" aria-live="polite" aria-labelledby="cookie-title" data-cookie-banner>
  <div class="cookie-inner">
    <span class="cookie-icon" aria-hidden="true"><?= icon('cookie') ?></span>
    <div class="cookie-copy">
      <p class="cookie-title" id="cookie-title"><?= e(t('cookie.title')) ?></p>
      <p><?= e(t('cookie.text')) ?> <a href="<?= e(url('/privacy-policy/')) ?>"><?= e(t('cookie.more')) ?></a></p>
    </div>
    <div class="cookie-actions">
      <button type="button" class="btn btn-sm cookie-reject" data-cookie="reject"><?= e(t('cookie.reject')) ?></button>
      <button type="button" class="btn btn-primary btn-sm" data-cookie="accept"><?= e(t('cookie.accept')) ?></button>
    </div>
  </div>
</div>
