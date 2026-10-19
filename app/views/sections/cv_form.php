<?php
/** cv_form (C39): "No open role? Send your CV" — PDF ≤ 5 MB → POST /api/cv/ */
$errors = (($GLOBALS['form_old']['form_id'] ?? '') === 'cv') ? ($GLOBALS['form_errors'] ?? []) : [];
$req = '<span class="req" aria-hidden="true">*</span>';
$role = $s['role'] ?? '';
?>
<section class="section section-muted" id="apply">
  <div class="container form-container">
    <div class="form-intro">
      <h2><?= rich($s['heading'] ?? t('form.cv_heading')) ?></h2>
      <p><?= rich($s['sub'] ?? t('form.cv_sub')) ?></p>
    </div>
    <div class="card form-card">
      <form class="lead-form" method="post" action="<?= e(url('/api/cv/')) ?>" enctype="multipart/form-data" novalidate data-lead-form data-form-type="cv">
        <?php if ($errors): ?><div class="form-alert" role="alert"><?= e($errors['_form'] ?? t('form.errors.generic')) ?></div><?php endif; ?>
        <?= form_hidden_fields('cv') ?>
        <input type="hidden" name="form_id" value="cv">
        <input type="hidden" name="role" value="<?= e($role) ?>">
        <div class="form-grid">
          <?php foreach ([['full_name', 'text', 'name'], ['email', 'email', 'email'], ['mobile', 'tel', 'tel']] as [$k, $type, $ac]): ?>
          <div class="field<?= $k === 'full_name' ? ' field-full' : '' ?>">
            <label for="cv-<?= $k ?>"><?= e(t("form.$k")) ?><?= $req ?></label>
            <input id="cv-<?= $k ?>" name="<?= $k ?>" type="<?= $type ?>" autocomplete="<?= $ac ?>" required<?= $k !== 'full_name' ? ' dir="ltr"' : '' ?><?= $k === 'mobile' ? ' placeholder="' . e(t('form.mobile_ph')) . '"' : '' ?> value="<?= old($k) ?>"<?= input_attrs($errors, $k) ?>>
            <?= field_error($errors, $k) ?>
          </div>
          <?php endforeach; ?>
          <div class="field field-full">
            <label for="cv-file"><?= e(t('form.cv')) ?><?= $req ?></label>
            <input id="cv-file" name="cv" type="file" accept="application/pdf,.pdf" required data-max-size="5242880"<?= input_attrs($errors, 'cv') ?>>
            <?= field_error($errors, 'cv') ?>
          </div>
          <div class="field field-full">
            <label for="cv-message"><?= e(t('form.message')) ?></label>
            <textarea id="cv-message" name="message" rows="3" maxlength="4000"><?= old('message') ?></textarea>
          </div>
          <div class="field field-full">
            <label class="consent"><input type="checkbox" name="consent" value="1" required> <span><?= rich(t('form.consent')) ?><?= $req ?></span></label>
            <?= field_error($errors, 'consent') ?>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block"><?= e(t('form.send')) ?></button>
      </form>
    </div>
  </div>
</section>
